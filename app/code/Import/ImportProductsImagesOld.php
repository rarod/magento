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
$mediaGalleryProcessor = $objectManager->get('Magento\Catalog\Model\Product\Gallery\Processor');

$images = json_decode(file_get_contents(__DIR__ . '/images.json'), true);

$products = $productRepository->getList($searchCriteria)->getItems();

foreach ($products as $product) {

    $hasImage = false;
    foreach ($images as $image) {
        if ($image['product_id'] !== $product->getErpProductId()) {
            continue;
        }

        $hasImage = true;

        $imageType = [];
        if ($image['index'] === 1) {
            $imageType = ['image','thumbnail','small_image'];
        }
        try {
            $mediaGalleryProcessor->addImage($product, 'catalog/'. $image['path'], $imageType, false, false);
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            echo $image['product_id'];
        }
        
    }

    if ($hasImage) {
        try {
            $productRepository->save($product);
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}

