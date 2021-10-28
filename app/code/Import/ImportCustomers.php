<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../../bootstrap.php';

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$searchCriteria = $objectManager->create('Magento\Framework\Api\SearchCriteriaInterface');
$resourceConnection = $objectManager->create('\Magento\Framework\App\ResourceConnection');
$customerGroupRepository = $objectManager->create('\Magento\Customer\Api\GroupRepositoryInterface');
$customerGroupFactory = $objectManager->create('\Magento\Customer\Model\Data\GroupFactory');
$regionFactory = $objectManager->create('\Magento\Directory\Model\RegionFactory');

$customersToImport = json_decode(file_get_contents(__DIR__ . '/customers.json'), true);

$connection = $resourceConnection->getConnection();

createCustomerGroups($customersToImport, $customerGroupFactory, $customerGroupRepository);

$customerGroups = $customerGroupRepository->getList($searchCriteria);

/**
 * Customer Entity Table and Columns
 */
$customerEntityTableName = $connection->getTableName('customer_entity');

$customerEntityColumns = [
    'website_id',
    'email',
    'group_id',
    'store_id',
    'is_active',
    'disable_auto_group_change',
    'created_in',
    'firstname',
    'lastname',
    'taxvat'
];

$customerEntityData = [];

/**
 * Customer Entity Int Table and Columns
 */
$customerEntityIntTableName = $connection->getTableName('customer_entity_int');

$customerEntityIntColumns = [
    'attribute_id',
    'entity_id',
    'value'
];

$customerEntityIntData = [];

/**
 * Customer Entity Varchar Table and Columns
 */
$customerEntityVarcharTableName = $connection->getTableName('customer_entity_varchar');

$customerEntityVarcharColumns = [
    'attribute_id',
    'entity_id',
    'value'
];

$customerEntityVarcharData = [];

/**
 * Customer Address Entity Table and Columns
 */
$customerAddressEntityTableName = $connection->getTableName('customer_address_entity');

$customerAddressEntityColumns = [
    'parent_id',
    'is_active',
    'city',
    'country_id',
    'firstname',
    'lastname',
    'postcode',
    'region',
    'region_id',
    'street',
    'telephone'
];

$customerAddressEntityData = [];

$customers = fetchCustomers($connection, $customerEntityTableName);

createCustomerEntityData($customersToImport, $customers, $customerGroups, $customerEntityData);

if (count($customerEntityData) > 0) {
    $connection->insertArray($customerEntityTableName, $customerEntityColumns, $customerEntityData);
}

$customers = fetchCustomers($connection, $customerEntityTableName);

$customerAttributes = fetchCustomerAttributes($connection);

createCustomerEntityIntVarcharData($customersToImport, $customers, $customerAttributes, $customerEntityIntData, $customerEntityVarcharData);

if (count($customerEntityIntData) > 0) {
    $connection->insertArray($customerEntityIntTableName, $customerEntityIntColumns, $customerEntityIntData);
}

if (count($customerEntityVarcharData) > 0) {
    $connection->insertArray($customerEntityVarcharTableName, $customerEntityVarcharColumns, $customerEntityVarcharData);
}

createCustomerAddressEntityData($customersToImport, $customers, $regionFactory, $customerAddressEntityData);

if (count($customerAddressEntityData) > 0) {
    $connection->insertArray($customerAddressEntityTableName, $customerAddressEntityColumns, $customerAddressEntityData);
}

$customerAddresses = fetchCustomerAddressess($connection, $customerAddressEntityTableName);

foreach ($customerAddresses as $customerAddress) {
    $addressId = $customerAddress['entity_id'];
    $customerId = $customerAddress['parent_id'];
    $connection->update($customerEntityTableName, ['default_shipping' => $addressId, 'default_billing' => $addressId], "entity_id = $customerId");
}

function createCustomerEntityData($customersToImport, $customers, $customerGroups, &$customerEntityData) {
    foreach ($customersToImport as $customerToImport) {

        $customerId = getCustomerId($customers, $customerToImport['Email']);

        if ($customerId) {
            continue;
        }

        if (trim($customerToImport['Email']) === "") {
            continue;
        }

        if (customerAlreadyExists($customerEntityData, $customerToImport['Email'])) {
            continue;
        }

        $groupId = getCustomerGroupByCode($customerToImport['ClienteClassificacao'], $customerGroups->getItems());

        $customerEntityData[] = [
            'website_id' => 1,
            'email' => $customerToImport['Email'],
            'group_id' => $groupId,
            'store_id' => 1,
            'is_active' => 1,
            'disable_auto_group_change' => 0,
            'created_in' => 'Default Store View',
            'firstname' => $customerToImport['Nome'],
            'lastname' => $customerToImport['Nome'],
            'taxvat' => $customerToImport['CgcCPF']
        ];
    }
}

function createCustomerEntityIntVarcharData($customersToImport, $customers, $customerAttributes, &$customerEntityIntData, &$customerEntityVarcharData) {
    $customerIds = [];

    foreach ($customersToImport as $customerToImport) {
        if (trim($customerToImport['Email']) === "") {
            continue;
        }
    
        $customerId = getCustomerId($customers, $customerToImport['Email']);

        if (in_array($customerId, $customerIds)) {
            continue;
        }

        $customerIds[] = $customerId;

        // Erp Customer Id
        $customerEntityVarcharData[] = [
            'attribute_id' => getCustomerAttributeId($customerAttributes, 'erp_customer_id'),
            'entity_id' => $customerId,
            'value' => (string) $customerToImport['Codigo']
        ];

        // Company Name
        $customerEntityVarcharData[] = [
            'attribute_id' => getCustomerAttributeId($customerAttributes, 'company_name'),
            'entity_id' => $customerId,
            'value' => $customerToImport['RazaoSocial']
        ];

        // Fantasy Name
        $customerEntityVarcharData[] = [
            'attribute_id' => getCustomerAttributeId($customerAttributes, 'fantasy_name'),
            'entity_id' => $customerId,
            'value' => $customerToImport['NomeFantasia']
        ];

        // State Registration
        $customerEntityVarcharData[] = [
            'attribute_id' => getCustomerAttributeId($customerAttributes, 'state_registration'),
            'entity_id' => $customerId,
            'value' => $customerToImport['IeCI']
        ];
    
        // Is Legal 
        $customerEntityIntData[] = [
            'attribute_id' => getCustomerAttributeId($customerAttributes, 'is_legal'),
            'entity_id' => $customerId,
            'value' => $customerToImport['Pessoa'] === "J" ? true : false
        ];

        // Representative Id
        $customerEntityIntData[] = [
            'attribute_id' => getCustomerAttributeId($customerAttributes, 'representative_id'),
            'entity_id' => $customerId,
            'value' => $customerToImport['Vendedor']
        ];
    }
}

function createCustomerAddressEntityData($customersToImport, $customers, $regionFactory, &$customerAddressEntityData) {
    $customerIds = [];
    foreach ($customersToImport as $customerToImport) {
        if (trim($customerToImport['Email']) === "") {
            continue;
        }

        $customerId = getCustomerId($customers, $customerToImport['Email']);

        if (in_array($customerId, $customerIds)) {
            continue;
        }

        $customerIds[] = $customerId;

        $street = explode(', ', $customerToImport['Endereco']);
        $street[] = 'N/A';
        $street[] = $customerToImport['Bairro'];

        $region = $regionFactory->create()->loadByCode($customerToImport['UF'], "BR");

        $customerAddressEntityData[] = [
            'parent_id' => $customerId,
            'is_active' => 1,
            'city' => $customerToImport['Cidade'],
            'country_id' => 'BR',
            'firstname' => $customerToImport['Nome'],
            'lastname' => $customerToImport['Nome'],
            'postcode' => $customerToImport['Cep'],
            'region' => $region->getDefaultName(), 
            'region_id' => $region->getId(),
            'street' => implode(PHP_EOL, $street),
            'telephone' => $customerToImport['Telefone']
        ];
    }
}

function fetchCustomers($connection, $customerEntityTableName) {
    $customersQuery = $connection->select()->from($customerEntityTableName);
    return $connection->fetchAll($customersQuery);
}

function fetchCustomerAddressess($connection, $customerAddressEntityTableName) {
    $customerAddressessQuery = $connection->select()->from($customerAddressEntityTableName);
    return $connection->fetchAll($customerAddressessQuery);
}

function fetchCustomerAttributes($connection) {
    $eavAttributeTableName = $connection->getTableName('eav_attribute');
    $customerAttributesQuery = $connection->select()->from($eavAttributeTableName, ['attribute_id', 'attribute_code'])->where('attribute_code in (?)', ['company_name', 'erp_customer_id', 'fantasy_name', 'is_legal', 'state_registration', 'representative_id']);
    return  $connection->fetchAll($customerAttributesQuery);
}


function getCustomerGroupByCode($customerGroupCode, $customerGroups) {
    foreach ($customerGroups as $customerGroup) {
        if ($customerGroup->getCode() === $customerGroupCode) {
            return $customerGroup->getId();
        }
    }

    return false;
}

function createCustomerGroups($customers, $customerGroupFactory, $customerGroupRepository) {
    $customerGroups = [];

    foreach ($customers as $customer) {
        $customerGroup = $customer['ClienteClassificacao'];

        if (!in_array($customerGroup, $customerGroups)) {
            $customerGroups[] = $customerGroup;
        }
    }

    foreach ($customerGroups as $customerGroup) {
        $newCustomerGroup = $customerGroupFactory->create();

        $newCustomerGroup->setCode($customerGroup);
        $newCustomerGroup->setTaxClassId(3);

        try {
            $customerGroupRepository->save($newCustomerGroup);
        } catch (\Exception $e) {
            // echo $newCustomerGroup->getCode() . PHP_EOL;
        }
    }
}

function customerAlreadyExists($customers, $email) {
    foreach ($customers as $customer) {
        if (trim(strtolower($customer['email'])) === trim(strtolower($email))) {
            return true;
        }
    }

    return false;
}

function getCustomerId($customers, $email) {
    foreach ($customers as $customer) {
        if (trim(strtolower($customer['email'])) === trim(strtolower($email))) {
            return $customer['entity_id'];
        }
    }

    return null;
}

function getCustomerAttributeId($customerAttributes, $attributeCode) {
    foreach ($customerAttributes as $customerAttribute) {
        if ($customerAttribute['attribute_code'] === $attributeCode) {
            return $customerAttribute['attribute_id'];
        }
    }

    return null;
}