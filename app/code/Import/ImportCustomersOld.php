<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../../bootstrap.php';

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$searchCriteria = $objectManager->create('Magento\Framework\Api\SearchCriteriaInterface');
$customerAccountManagement = $objectManager->create('\Magento\Customer\Api\AccountManagementInterface');
$customerRepository = $objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface');
$customerGroupRepository = $objectManager->create('\Magento\Customer\Api\GroupRepositoryInterface');
$customerGroupFactory = $objectManager->create('\Magento\Customer\Model\Data\GroupFactory');
$regionFactory = $objectManager->create('Magento\Directory\Model\RegionFactory');
$addressRepository = $objectManager->create('\Magento\Customer\Api\AddressRepositoryInterface');
$customerDataFactory = $objectManager->create('\Magento\Customer\Api\Data\CustomerInterfaceFactory');
$addressDataFactory = $objectManager->create('\Magento\Customer\Api\Data\AddressInterfaceFactory');
$dataObjectHelper = $objectManager->create('\Magento\Framework\Api\DataObjectHelper');

$customers = json_decode(file_get_contents(__DIR__ . '/customers.json'), true);

createCustomerGroups($customers, $customerGroupFactory, $customerGroupRepository);

$allCustomers = $customerRepository->getList($searchCriteria);
$customerGroups = $customerGroupRepository->getList($searchCriteria);

foreach ($customers as $customer) {
    if (customerAlreadyExists($allCustomers->getItems(), $customer['Email'])) {
        continue;
    }

    $customerData = [
        'firstname' => $customer['Nome'],
        'lastname' => $customer['Nome'],
        'taxvat' => $customer['CgcCPF'],
        'email' => $customer['Email'],
        'erp_customer_id' => (string) $customer['Codigo'],
        'is_legal' => $customer['Pessoa'] === "J" ? true : false,
        'company_name' => $customer['RazaoSocial'],
        'fantasy_name' => $customer['NomeFantasia'],
        'state_registration' => $customer['IeCI'],
        'representative_id' => $customer['Vendedor']
    ];

    $newCustomer = $customerDataFactory->create();
    $dataObjectHelper->populateWithArray(
        $newCustomer,
        $customerData,
        \Magento\Customer\Api\Data\CustomerInterface::class
    );

    try {
        $newCustomer = $customerAccountManagement->createAccount($newCustomer);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        continue;
    }

    $street = explode(', ', $customer['Endereco']);
    $street[] = 'N/A';
    $street[] = $customer['Bairro'];

    $region = $regionFactory->create();
    $regionId = $region->loadByCode($customer['UF'], "BR")->getId();

    $addressData = [
        'firstname' => $customer['Nome'],
        'lastname' => $customer['Nome'],
        'street' => $street,
        'city' => $customer['Cidade'],
        'telephone' => $customer['Telefone'],
        'postcode' => $customer['Cep'],
        'region_id' => $regionId,
        'country_id' => 'BR',
        'is_default_billing' => true,
        'is_default_shipping' => true,
    ];

    $newAddress = $addressDataFactory->create();
    $dataObjectHelper->populateWithArray(
        $newAddress,
        $addressData,
        \Magento\Customer\Api\Data\AddressInterface::class
    );

    $newAddress->setCustomerId($newCustomer->getId());

    try {
        $addressRepository->save($newAddress);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }

    // $street = explode(', ', $customer['Endereco']);
    // $street[] = 'N/A';
    // $street[] = $customer['Bairro'];

    // $region = $regionFactory->create();
    // $regionId = $region->loadByCode($customer['UF'], "BR")->getId();

    // $newAddress->setCustomerId($newCustomer->getId());
    // $newAddress->setStreet($street);
    // $newAddress->setCity($customer['Cidade']);
    // $newAddress->setPhone($customer['Telefone']);
    // $newAddress->setPostcode($customer['Cep']);
    // $newAddress->setRegionId($regionId);
    // $newAddress->setIsDefaultBilling(true);
    // $newAddress->setIsDefaultShipping(true);

    // try {
    //     $customerAddressRepository->save($newAddress);
    // } catch (\Exception $e) {
    //     echo $newAddress->getId() . PHP_EOL;
    // }
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
            echo $newCustomerGroup->getCode() . PHP_EOL;
        }
    }
}

function getCustomerGroupByCode($customerGroupCode, $customerGroups) {
    foreach ($customerGroups as $customerGroup) {
        if ($customerGroup->getCode() === $customerGroupCode) {
            return $customerGroup;
        }
    }

    return false;
}

function customerAlreadyExists($allCustomers, $customerEmail) {
    foreach ($allCustomers as $customer) {
        if ($customer->getEmail() === $customerEmail) {
            return true;
        }
    }

    return false;
}