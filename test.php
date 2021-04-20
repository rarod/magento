<?php
use Magento\Framework\App\Bootstrap;
include('app/bootstrap.php');
$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');


$_product = $objectManager->create('Magento\Catalog\Model\Product');
$_product->setName('Fifth Product');
$_product->setTypeId('simple');
$_product->setAttributeSetId(4);
$_product->setSku('fifth-SKU');
$_product->setWebsiteIds(array(1));
$_product->setVisibility(4);
$_product->setPrice(array(1));
$_product->setImage('/testimg/test4.jpg');
$_product->setSmallImage('/testimg/test4.jpg');
$_product->setThumbnail('/testimg/test4.jpg');
$_product->setStockData(array(
        'use_config_manage_stock' => 0, //'Use config settings' checkbox
        'manage_stock' => 1, //manage stock
        'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
        'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
        'is_in_stock' => 1, //Stock Availability
        'qty' => 100 //qty
    )
);

$_product->save();
?>
