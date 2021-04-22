<?php

namespace Rarod\Sellers\Plugin\Ui\Model\Export;

use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Store\Model\WebsiteFactory;
use Rarod\Sellers\Model\SellerFactory;
use Magento\InventorySales\Model\ResourceModel\StockIdResolver;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Api\ProductRepositoryInterface;

class MetadataProviderPlugin
{
    /** @var WebsiteFactory */
    protected $websiteFactory;

    /** @var SellerFactory */
    protected $sellerFactory;

    /** @var StockIdResolver */
    protected $stockIdResolver;

    /** @var GetProductSalableQtyInterface */
    protected $getProductSalableQty;

    /** @var Filter */
    protected $filter;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /**
     * @param WebsiteFactory $websiteFactory
     * @param SellerFactory $sellerFactory
     * @param StockIdResolver $stockIdResolver
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param Filter $filter
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        WebsiteFactory $websiteFactory,
        SellerFactory $sellerFactory,
        StockIdResolver $stockIdResolver,
        GetProductSalableQtyInterface $getProductSalableQty,
        Filter $filter,
        ProductRepositoryInterface $productRepository
    ) {
        $this->websiteFactory = $websiteFactory;
        $this->sellerFactory = $sellerFactory;
        $this->stockIdResolver = $stockIdResolver;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->filter = $filter;
        $this->productRepository = $productRepository;
    }

    /**
     * @param MetadataProvider $subject
     * @param DocumentInterface $document
     * @param array $fields
     * @param array $options
     * @param array $row
     * @return array
     */
    public function afterGetRowData(
        MetadataProvider $subject,
        array $row,
        DocumentInterface $document, 
        $fields, 
        $options
    ) {
        $component = $this->filter->getComponent();
        if ($component && $component->getName() === 'rarod_sellers_stock_listing') {
            $columns = array_slice($fields, 2);
            foreach ($columns as $column) {
                $columnIndex = array_search($column, $fields);
                if ($column === "name") {
                    $product = $this->productRepository->get($document->getSku());
                    $row[$columnIndex] = $product->getName();
                    continue;
                }
                $sellerModel = $this->sellerFactory->create();
                $seller = $sellerModel->load($column, 'name');
                $websiteId = $seller->getWebsiteId();

                $websiteModel = $this->websiteFactory->create();
                $websiteCode = $websiteModel->load($websiteId)->getCode();
                $stockId = $this->stockIdResolver->resolve('website', $websiteCode);
                $qty = $this->getProductSalableQty->execute($document->getSku(), $stockId);                
                $row[$columnIndex] = $qty;
            }
        }
        return $row;
    }
}
