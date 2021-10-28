<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../../bootstrap.php';

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$searchCriteria = $objectManager->create('Magento\Framework\Api\SearchCriteriaInterface');
$categoryRepository = $objectManager->create('\Magento\Catalog\Api\CategoryListInterface');
$resourceConnection = $objectManager->create('\Magento\Framework\App\ResourceConnection');
$brandSource = $objectManager->create('\JRComercio\AddProductFields\Model\Source\Brand');

$categories = $categoryRepository->getList($searchCriteria);

$productsToImport = json_decode(file_get_contents(__DIR__ . '/products.json'), true);

$connection = $resourceConnection->getConnection();

/**
 * Catalog Product Entity Table and Columns
 */
$catalogProductEntityTableName = $connection->getTableName('catalog_product_entity');

$catalogProductEntityColumns = [
    'attribute_set_id',
    'type_id',
    'sku',
    'has_options',
    'required_options'
];

$catalogProductEntityData = [];

/**
 * Catalog Product Entity Decimal Table and Columns
 */
$catalogProductEntityDecimalTableName = $connection->getTableName('catalog_product_entity_decimal');

$catalogProductEntityDecimalColumns = [
    'attribute_id',
    'store_id',
    'entity_id',
    'value'
];

$catalogProductEntityDecimalData = [];

/**
 * Catalog Product Entity Int Table and Columns
 */
$catalogProductEntityIntTableName = $connection->getTableName('catalog_product_entity_int');

$catalogProductEntityIntColumns = [
    'attribute_id',
    'store_id',
    'entity_id',
    'value'
];

$catalogProductEntityIntData = [];

/**
 * Catalog Product Entity Text Table and Columns
 */
$catalogProductEntityTextTableName = $connection->getTableName('catalog_product_entity_text');

$catalogProductEntityTextColumns = [
    'attribute_id',
    'store_id',
    'entity_id',
    'value'
];

$catalogProductEntityTextData = [];

/**
 * Catalog Product Entity Varchar Table and Columns
 */
$catalogProductEntityVarcharTableName = $connection->getTableName('catalog_product_entity_varchar');

$catalogProductEntityVarcharColumns = [
    'attribute_id',
    'store_id',
    'entity_id',
    'value'
];

$catalogProductEntityVarcharData = [];

/**
 * Catalog Category Product Table and Columns
 */
$catalogCategoryProductTableName = $connection->getTableName('catalog_category_product');

$catalogCategoryProductColumns = [
    'category_id',
    'product_id',
    'position'
];

$catalogCategoryProductData = [];

/**
 * Catalog Inventory Stock Item Table and Columns
 */
$cataloginventoryStockItemTableName = $connection->getTableName('cataloginventory_stock_item');

$cataloginventoryStockItemColumns = [
    'product_id',
    'stock_id',
    'qty',
    'min_qty',
    'use_config_min_qty',
    'is_qty_decimal',
    'backorders',
    'use_config_backorders',
    'min_sale_qty',
    'use_config_min_sale_qty',
    'max_sale_qty',
    'use_config_max_sale_qty',
    'is_in_stock',
    'notify_stock_qty',
    'use_config_notify_stock_qty',
    'manage_stock',
    'use_config_manage_stock',
    'stock_status_changed_auto',
    'use_config_qty_increments',
    'qty_increments',
    'use_config_enable_qty_inc',
    'enable_qty_increments',
    'is_decimal_divided',
    'website_id'
];

$cataloginventoryStockItemData = [];

/**
 * Inventory Source Item Table and Columns
 */
$inventorySourceItemTableName = $connection->getTableName('inventory_source_item');

$inventorySourceItemColumns = [
    'source_code',
    'sku',
    'quantity',
    'status'
];

$inventorySourceItemData = [];

/**
 * Catalog Product Website Table and Columns
 */
$catalogProductWebsiteTableName = $connection->getTableName('catalog_product_website');

$catalogProductWebsiteColumns = [
    'product_id',
    'website_id',
];

$catalogProductWebsiteData = [];

$products = fetchProducts($connection, $catalogProductEntityTableName);

createCatalogProductEntityData($productsToImport, $products, $catalogProductEntityData);

if (count($catalogProductEntityData) > 0) {
    $connection->insertArray($catalogProductEntityTableName, $catalogProductEntityColumns, $catalogProductEntityData);
}

$brands = $brandSource->getAllOptions();
$productAttributes = fetchProductAttributes($connection);
$products = fetchProducts($connection, $catalogProductEntityTableName);

createCatalogProductEntitiesData($productsToImport, $products, $productAttributes, $brands, $catalogProductEntityDecimalData, $catalogProductEntityIntData, $catalogProductEntityTextData, $catalogProductEntityVarcharData);

if (count($catalogProductEntityDecimalData) > 0) {
    $connection->insertArray($catalogProductEntityDecimalTableName, $catalogProductEntityDecimalColumns, $catalogProductEntityDecimalData);
}

if (count($catalogProductEntityIntData) > 0) {
    $connection->insertArray($catalogProductEntityIntTableName, $catalogProductEntityIntColumns, $catalogProductEntityIntData);
}

if (count($catalogProductEntityTextData) > 0) {
    $connection->insertArray($catalogProductEntityTextTableName, $catalogProductEntityTextColumns, $catalogProductEntityTextData);
}

if (count($catalogProductEntityVarcharData) > 0) {
    $connection->insertArray($catalogProductEntityVarcharTableName, $catalogProductEntityVarcharColumns, $catalogProductEntityVarcharData);
}

createCatalogCategoryProduct($productsToImport, $products, $categories, $catalogCategoryProductData);

if (count($catalogCategoryProductData) > 0) {
    $connection->insertArray($catalogCategoryProductTableName, $catalogCategoryProductColumns, $catalogCategoryProductData);
}

createProductStock($products, $cataloginventoryStockItemData, $inventorySourceItemData);

if (count($cataloginventoryStockItemData) > 0) {
    $connection->insertArray($cataloginventoryStockItemTableName, $cataloginventoryStockItemColumns, $cataloginventoryStockItemData);
}

if (count($inventorySourceItemData) > 0) {
    $connection->insertArray($inventorySourceItemTableName, $inventorySourceItemColumns, $inventorySourceItemData);
}

createCatalogProductWebsite($products, $catalogProductWebsiteData);

if (count($catalogProductWebsiteData) > 0) {
    $connection->insertArray($catalogProductWebsiteTableName, $catalogProductWebsiteColumns, $catalogProductWebsiteData);
}

function createCatalogProductEntityData($productsToImport, $products, &$catalogProductEntityData) {
    foreach ($productsToImport as $productToImport) {
        $sku = (string) $productToImport['codigo'];

        if ($sku === "") {
            continue;
        }

        $productId = getProductId($products, $sku);

        if ($productId) {
            continue;
        }

        if (productAlreadyExists($catalogProductEntityData, $sku)) {
            continue;
        }

        $catalogProductEntityData[] = [
            'attribute_set_id' => 4,
            'type_id' => 'simple',
            'sku' => $sku,
            'has_options' => 0,
            'required_options' => 0
        ];
    }
}

function createCatalogProductEntitiesData($productsToImport, $products, $productAttributes, $brands, &$catalogProductEntityDecimalData, &$catalogProductEntityIntData, &$catalogProductEntityTextData, &$catalogProductEntityVarcharData) {
    $productIds = [];

    foreach ($productsToImport as $productToImport) {
        $sku = (string) $productToImport['codigo'];

        if ($sku === "") {
            continue;
        }

        $productId = getProductId($products, $sku);

        if (in_array($productId, $productIds)) {
            continue;
        }

        $productIds[] = $productId;

        // Price
        $catalogProductEntityDecimalData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'price'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => (float) rand(10, 10000)
        ];

        // Weight
        $catalogProductEntityDecimalData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'weight'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => (float) rand(1, 50)
        ];

        // Status
        $catalogProductEntityIntData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'status'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => 1
        ];

        // Visibility
        $catalogProductEntityIntData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'visibility'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => 4
        ];

        // Tax Class Id
        $catalogProductEntityIntData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'tax_class_id'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => 0
        ];

        // Brand
        $catalogProductEntityIntData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'brand'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => getBrandIdByErpBrandName($brands, $productToImport['Marca'])
        ];

        // Meta Keyword
        $catalogProductEntityTextData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'meta_keyword'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $productToImport['Nome']
        ];

        // Name
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'name'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $productToImport['Nome']
        ];

        // Meta Title
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'meta_title'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $productToImport['Nome']
        ];

        // Meta Description
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'meta_description'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $productToImport['Nome']
        ];

        $urlKey = $sku . '-' . kebabCase(removeAccents($productToImport['Nome']));

        // Url Key
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'url_key'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $urlKey
        ];

        // Erp Product Id
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'erp_product_id'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $sku
        ];

        // Ean
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'ean'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $productToImport['CodBarrasUnidade']
        ];

        // Package
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'package'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $productToImport['Embalagem']
        ];

        // Packaging Unit
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'packaging_unit'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $productToImport['UnidadeEmbalagem']
        ];

        // Ncm
        $catalogProductEntityVarcharData[] = [
            'attribute_id' => getProductAttributeId($productAttributes, 'ncm'),
            'store_id' => 0,
            'entity_id' => $productId,
            'value' => $productToImport['classificacaofiscal']
        ];
    }
}

function createCatalogCategoryProduct($productsToImport, $products, $categories, &$catalogCategoryProductData) {
    $productIds = [];
    foreach ($productsToImport as $productToImport) {
        $sku = (string) $productToImport['codigo'];

        if ($sku === "") {
            continue;
        }

        $productId = getProductId($products, $sku);

        if (in_array($productId, $productIds)) {
            continue;
        }

        $productIds[] = $productId;

        $catalogCategoryProductData[] = [
            'category_id' => 2,
            'product_id' => $productId,
            'position' => 0
        ];

        $categoryId = getCategoryIdByErpCategoryId($categories, $productToImport['Categoria']);

        if (!$categoryId) {
            continue;
        }

        $catalogCategoryProductData[] = [
            'category_id' => $categoryId,
            'product_id' => $productId,
            'position' => 0
        ];

        $childCategoryId = getCategoryIdByErpCategoryId($categories, $productToImport['SubCategoria']); 

        if (!$childCategoryId || $categoryId === $childCategoryId) {
            continue;
        }

        $catalogCategoryProductData[] = [
            'category_id' => $childCategoryId,
            'product_id' => $productId,
            'position' => 0
        ];
    }
}

function createProductStock($products, &$cataloginventoryStockItemData, &$inventorySourceItemData) {
    foreach ($products as $product) {
        $quantity = rand(1, 50);

        $cataloginventoryStockItemData[] = [
            'product_id' => $product['entity_id'],
            'stock_id' => 1,
            'qty' => $quantity,
            'min_qty' => 0,
            'use_config_min_qty' => 1,
            'is_qty_decimal' => 0,
            'backorders' => 0,
            'use_config_backorders' => 1,
            'min_sale_qty' => 1,
            'use_config_min_sale_qty' => 1,
            'max_sale_qty' => 10000,
            'use_config_max_sale_qty' => 1,
            'is_in_stock' => 1,
            'notify_stock_qty' => 1,
            'use_config_notify_stock_qty' => 1,
            'manage_stock' => 1,
            'use_config_manage_stock' => 1,
            'stock_status_changed_auto' => 0,
            'use_config_qty_increments' => 1,
            'qty_increments' => 1,
            'use_config_enable_qty_inc' => 1,
            'enable_qty_increments' => 0,
            'is_decimal_divided' => 0,
            'website_id' => 0
        ];

        $inventorySourceItemData[] = [
            'source_code' => 'default',
            'sku' => $product['sku'],
            'quantity' => $quantity,
            'status' => 1
        ];
    }
}

function createCatalogProductWebsite($products, &$catalogProductWebsiteData) {
    foreach ($products as $product) {
        $catalogProductWebsiteData[] = [
            'product_id' => $product['entity_id'],
            'website_id' => 1
        ];
    }
}

function getProductId($products, $sku) {
    foreach ($products as $product) {
        if ($product['sku'] === $sku) {
            return $product['entity_id'];
        }
    }

    return null;
}

function getProductAttributeId($attributes, $attributeCode) {
    foreach ($attributes as $attribute) {
        if ($attribute['attribute_code'] === $attributeCode) {
            return $attribute['attribute_id'];
        }
    }

    return null;
}

function productAlreadyExists($products, $sku) {
    foreach ($products as $product) {
        if ($product['sku'] === $sku) {
            return true;
        }
    }

    return false;
}

function fetchProducts($connection, $catalogProductEntityTableName) {
    $query = $connection->select()->from($catalogProductEntityTableName);
    return $connection->fetchAll($query);
}

function fetchProductAttributes($connection) {
    $eavAttributeTableName = $connection->getTableName('eav_attribute');
    $query = $connection->select()->from($eavAttributeTableName, ['attribute_id', 'attribute_code'])->where('entity_type_id = ?', 4)->where('attribute_code in (?)', ['status', 'visibility', 'tax_class_id', 'brand', 'price', 'weight', 'media_gallery', 'meta_keyword', 'name', 'meta_title', 'meta_description', 'image', 'small_image', 'thumbnail', 'options_container', 'msrp_display_actual_price_type', 'url_key', 'gift_message_available', 'swatch_image', 'erp_product_id', 'ean', 'package', 'packaging_unit', 'ncm']);
    return  $connection->fetchAll($query);
}

function getCategoryIdByErpCategoryId($categories, $erpCategoryId) {
    foreach ($categories->getItems() as $category) {
        if ($category->getErpCategoryId() === (string) $erpCategoryId) {
            return $category->getId();
        }
    }

    return false;
}

function getBrandIdByErpBrandName($brands, $erpBrandName) {
    foreach ($brands as $brand) {
        if ($brand['label']->getText() === $erpBrandName) {
            return $brand['value'];
        }
    }

    return null;
}

function removeAccents($string) {
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç|Ç)/"), explode(" ","a A e E i I o O u U n N c C"), $string);
}

function kebabCase($string) {
    $string = strtolower(str_replace(',', '-', $string));
    $string = strtolower(str_replace('/', '-', $string));
    $string = strtolower(str_replace(' ', '-', $string));
    $string = strtolower(str_replace('--', '-', $string));
    $string = strtolower(str_replace('---', '-', $string));

    return $string;
}