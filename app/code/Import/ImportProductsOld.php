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
$categoryLinkManagement = $objectManager->create('\Magento\Catalog\Api\CategoryLinkManagementInterface');
$productRepository = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
$productFactory = $objectManager->create('\Magento\Catalog\Api\Data\ProductInterfaceFactory');

$brandSource = $objectManager->create('\JRComercio\AddProductFields\Model\Source\Brand');

$products = json_decode(file_get_contents(__DIR__ . '/products.json'), true);

$categories = $categoryRepository->getList($searchCriteria);
$brands = $brandSource->getAllOptions();

foreach ($products as $product) {
    $categoryId = getCategoryIdByErpCategoryId($categories, $product['Categoria']);
    $childCategoryId = getCategoryIdByErpCategoryId($categories, $product['SubCategoria']); 

    $categoryIds = [2, $categoryId];
    if ($categoryId !== $childCategoryId) {
        $categoryIds[] = $childCategoryId;
    }

    $brandId = getBrandIdByErpBrandName($brands, $product['Marca']);

    $newProduct = $productFactory->create();

    $newProduct->setName($product['Nome']);
    $newProduct->setSku((string) $product['codigo']);
    $newProduct->setTypeId('simple');
    $newProduct->setTaxClassId(0);
    $newProduct->setVisibility(4);
    $newProduct->setStatus(1);
    $newProduct->setAttributeSetId(4);
    $newProduct->setPrice(rand(10, 10000));
    $newProduct->setStockData([
        'use_config_manage_stock' => 0,
        'manage_stock' => 1,
        'is_in_stock' => 1,
        'qty' => rand(10, 1000)
    ]);
    $newProduct->setErpProductId($product['codigo']);
    $newProduct->setBrand($brandId);
    $newProduct->setEan($product['CodBarrasUnidade']);
    $newProduct->setPackage($product['Embalagem']);
    $newProduct->setPackagingUnit($product['UnidadeEmbalagem']);
    $newProduct->setNcm($product['classificacaofiscal']);

    try {
        $productRepository->save($newProduct);

        $categoryLinkManagement->assignProductToCategories($newProduct->getSku(), $categoryIds);
    } catch (\Exception $e) {
        echo $e->getMessage();
        echo $newProduct->getSku() . PHP_EOL;
    }

    
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

    return false;
}
