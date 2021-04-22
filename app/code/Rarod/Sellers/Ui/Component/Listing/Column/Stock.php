<?php


namespace Rarod\Sellers\Ui\Component\Listing\Column;


use Rarod\Sellers\Model\SellerFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\InventorySales\Model\ResourceModel\StockIdResolver;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;

class Stock extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    private $websiteFactory;
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var SellerFactory
     */
    private $sellerFactory;
    /**
     * @var StockIdResolver
     */
    private $stockIdResolver;
    /**
     * @var GetProductSalableQtyInterface
     */
    private $getProductSalableQty;


    public function __construct(
        SellerFactory $sellerFactory,
        StockIdResolver $stockIdResolver,
        GetProductSalableQtyInterface $getProductSalableQty,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        ProductFactory $productFactory,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->websiteFactory = $websiteFactory;
        $this->productFactory = $productFactory;
        $this->sellerFactory = $sellerFactory;
        $this->stockIdResolver = $stockIdResolver;
        $this->getProductSalableQty = $getProductSalableQty;
    }

    public function prepareDataSource(array $dataSource)
    {
        $sellerModel = $this->sellerFactory->create();
        if (isset($dataSource['data']['items'])) {

            $websiteModel = $this->websiteFactory->create();

            foreach ($dataSource['data']['items'] as &$item) {
                try {
                    $columnName = $this->getName();
                    $seller = $sellerModel->load($columnName, 'name');
                    $websiteId = $seller->getWebsiteId();
                    $websiteCode = $websiteModel->load($websiteId)->getCode();
                    $stockId = $this->stockIdResolver->resolve('website', $websiteCode);
                    $qty = $this->getProductSalableQty->execute($item['sku'], $stockId);

                    $item[$this->getName()] = $qty;
                } catch (\Exception $e) {
                    $item[$this->getName()] = '-';
                }

            }
        }
        return $dataSource;
    }
}