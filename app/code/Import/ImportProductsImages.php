<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../../bootstrap.php';

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$searchCriteria = $objectManager->create('Magento\Framework\Api\SearchCriteriaInterface');
$productRepository = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
$resourceConnection = $objectManager->create('\Magento\Framework\App\ResourceConnection');

// $productsToImport = json_decode(file_get_contents(__DIR__ . '/products.json'), true);

$connection = $resourceConnection->getConnection();

/**
 * Catalog Product Entity Media Gallery Table and Columns
 */
$catalogProductEntityMediaGalleryTableName = $connection->getTableName('catalog_product_entity_media_gallery');

$catalogProductEntityMediaGalleryColumns = [
    'attribute_id',
    'value',
    'media_type',
    'disabled'
];

$catalogProductEntityMediaGalleryData = [];

/**
 * Catalog Product Entity Media Gallery Value Table and Columns
 */
$catalogProductEntityMediaGalleryValueTableName = $connection->getTableName('catalog_product_entity_media_gallery_value');

$catalogProductEntityMediaGalleryValueColumns = [
    'value_id',
    'store_id',
    'entity_id',
    'label',
    'position',
    'disabled',
    'record_id'
];

$catalogProductEntityMediaGalleryValueData = [];

/**
 * Catalog Product Entity Media Gallery Value To Entity Table and Columns
 */
$catalogProductEntityMediaGalleryValueToEntityTableName = $connection->getTableName('catalog_product_entity_media_gallery_value_to_entity');

$catalogProductEntityMediaGalleryValueToEntityColumns = [
    'value_id',
    'entity_id'
];

$catalogProductEntityMediaGalleryValueToEntityData = [];

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

$images = json_decode(file_get_contents(__DIR__ . '/images.json'), true);

$products = $productRepository->getList($searchCriteria)->getItems();
$productAttributes = fetchProductAttributes($connection);

createCatalogProductMediaGallery($images, $catalogProductEntityMediaGalleryData);

if (count($catalogProductEntityMediaGalleryData) > 0) {
    $connection->insertArray($catalogProductEntityMediaGalleryTableName, $catalogProductEntityMediaGalleryColumns, $catalogProductEntityMediaGalleryData);
}

$mediaGalleries = fetchMediaGalleries($connection, $catalogProductEntityMediaGalleryTableName);

createCatalogProductMediaGalleryLinks($products, $images, $mediaGalleries, $productAttributes, $catalogProductEntityVarcharData, $catalogProductEntityMediaGalleryValueData, $catalogProductEntityMediaGalleryValueToEntityData);

if (count($catalogProductEntityVarcharData) > 0) {
    $connection->insertArray($catalogProductEntityVarcharTableName, $catalogProductEntityVarcharColumns, $catalogProductEntityVarcharData);
}

if (count($catalogProductEntityMediaGalleryValueData) > 0) {
    $connection->insertArray($catalogProductEntityMediaGalleryValueTableName, $catalogProductEntityMediaGalleryValueColumns, $catalogProductEntityMediaGalleryValueData);
} 

if (count($catalogProductEntityMediaGalleryValueToEntityData) > 0) {
    $connection->insertArray($catalogProductEntityMediaGalleryValueToEntityTableName, $catalogProductEntityMediaGalleryValueToEntityColumns, $catalogProductEntityMediaGalleryValueToEntityData);
}

function createCatalogProductMediaGallery($images, &$catalogProductEntityMediaGalleryData) {
    foreach ($images as $image) {
        $catalogProductEntityMediaGalleryData[] = [
            'attribute_id' => 90,
            'value' => $image['path'],
            'media_type' => 'image',
            'disabled' => 0
        ];
    }
}

function fetchMediaGalleries($connection, $catalogProductEntityMediaGalleryTableName) {
    $query = $connection->select()->from($catalogProductEntityMediaGalleryTableName);
    return $connection->fetchAll($query);
}

function createCatalogProductMediaGalleryLinks($products, $images, $mediaGalleries, $productAttributes, &$catalogProductEntityVarcharData, &$catalogProductEntityMediaGalleryValueData, &$catalogProductEntityMediaGalleryValueToEntityData) {
    foreach ($images as $image) {
        $productId = getProductId($products, $image['product_id']);

        if (!$productId) {
            continue;
        }

        $mediaGalleryId = getMediaGalleryId($mediaGalleries, $image['path']);

        if (!$mediaGalleryId) {
            continue;
        }

        if ($image['index'] === 1) {
            $catalogProductEntityVarcharData[] = [
                'attribute_id' => getProductAttributeId($productAttributes, 'image'),
                'store_id' => 0,
                'entity_id' => $productId,
                'value' => $image['path']
            ];

            $catalogProductEntityVarcharData[] = [
                'attribute_id' => getProductAttributeId($productAttributes, 'small_image'),
                'store_id' => 0,
                'entity_id' => $productId,
                'value' => $image['path']
            ];

            $catalogProductEntityVarcharData[] = [
                'attribute_id' => getProductAttributeId($productAttributes, 'thumbnail'),
                'store_id' => 0,
                'entity_id' => $productId,
                'value' => $image['path']
            ];

            $catalogProductEntityVarcharData[] = [
                'attribute_id' => getProductAttributeId($productAttributes, 'swatch_image'),
                'store_id' => 0,
                'entity_id' => $productId,
                'value' => $image['path']
            ];
        }

        $catalogProductEntityMediaGalleryValueData[] = [
            'value_id' => $mediaGalleryId,
            'store_id' => 0,
            'entity_id' => $productId,
            'label' => '',
            'position' => $image['index'],
            'disabled' => 0,
            'record_id' => 0
        ];

        $catalogProductEntityMediaGalleryValueToEntityData[] = [
            'value_id' => $mediaGalleryId,
            'entity_id' => $productId
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

function getMediaGalleryId($mediaGalleries, $path) {
    foreach ($mediaGalleries as $mediaGallery) {
        if ($mediaGallery['value'] === $path) {
            return $mediaGallery['value_id'];
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

function fetchProductAttributes($connection) {
    $eavAttributeTableName = $connection->getTableName('eav_attribute');
    $query = $connection->select()->from($eavAttributeTableName, ['attribute_id', 'attribute_code'])->where('entity_type_id = ?', 4)->where('attribute_code in (?)', ['status', 'visibility', 'tax_class_id', 'brand', 'price', 'weight', 'media_gallery', 'meta_keyword', 'name', 'meta_title', 'meta_description', 'image', 'small_image', 'thumbnail', 'options_container', 'msrp_display_actual_price_type', 'url_key', 'gift_message_available', 'swatch_image', 'erp_product_id', 'ean', 'package', 'packaging_unit', 'ncm']);
    return  $connection->fetchAll($query);
}