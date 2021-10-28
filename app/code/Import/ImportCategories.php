<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../../bootstrap.php';

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

$categoryRepository = $objectManager->create('\Magento\Catalog\Api\CategoryRepositoryInterface');
$categoryFactory = $objectManager->create('\Magento\Catalog\Model\CategoryFactory');

$categories = json_decode(file_get_contents(__DIR__ . '/categories.json'), true);
$childrenCategories = json_decode(file_get_contents(__DIR__ . '/children_categories.json'), true);

foreach ($categories as $category) {

    $newCategory = $categoryFactory->create();

    $newCategory->setName($category['Nome']);
    $newCategory->setParentId(2);
    $newCategory->setIsActive(true);
    $newCategory->setErpCategoryId($category['Codigo']);

    try {
        $newCategory = $categoryRepository->save($newCategory);
    } catch (Exception $e) {
        echo "Category exists" . PHP_EOL;
    }

    foreach($childrenCategories as $childCategory) {
        if ($newCategory->getErpCategoryId() !== (string) $childCategory['Categoria']) {
            continue;
        }

        $newChildCategory = $categoryFactory->create();

        $newChildCategory->setName($childCategory['SubNome']);
        $newChildCategory->setParentId($newCategory->getId());
        $newChildCategory->setIsActive(true);
        $newChildCategory->setErpCategoryId($childCategory['SubCodigo']);

        $categoryRepository->save($newChildCategory);
    }
}
