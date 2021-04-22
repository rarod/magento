<?php

namespace Rarod\Sellers\Helper;

use Magento\Framework\App\Response\RedirectInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\Framework\App\ResourceConnection;

class SellerWebsite extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var int
     */
    protected $websiteId;
    /**
     * @var WebsiteFactory
     */
    protected $websiteFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var RedirectInterface
     */
    protected $redirect;
    /**
     * @var GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    protected $sourcesAssignedToStock;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param RedirectInterface $redirect
     * @param WebsiteFactory $websiteFactory
     * @param StoreManagerInterface $storeManager
     * @param GetSourcesAssignedToStockOrderedByPriorityInterface $sourcesAssignedToStock
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        RedirectInterface $redirect,
        WebsiteFactory $websiteFactory,
        StoreManagerInterface $storeManager,
        GetSourcesAssignedToStockOrderedByPriorityInterface $sourcesAssignedToStock,
        ResourceConnection $resourceConnection
    ) {
        $this->redirect = $redirect;
        $this->websiteFactory = $websiteFactory;
        $this->storeManager = $storeManager;
        $this->sourcesAssignedToStock = $sourcesAssignedToStock;
        $this->resourceConnection = $resourceConnection;
    }

    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    public function getCurrentStoreUrl()
    {
        return $this->redirect->getRefererUrl();
    }

    public function getCurrentStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    public function getSellerWebsite()
    {
        return $this->websiteFactory->create()->load($this->websiteId);
    }

    public function getSellerDefaultStore()
    {
        $website = $this->getSellerWebsite();
        return $website->getDefaultStore();
    }

    public function getSellerStoreUrl($preserveCurrentUrl = true)
    {
        $currentStore = $this->getCurrentStoreCode();
        $currentUrl   = $this->getCurrentStoreUrl();

        $sellerDefaultStore = $this->getSellerDefaultStore();
        $newStoreCode       = $sellerDefaultStore->getCode();
        $newStoreBaseUrl    = $sellerDefaultStore->getBaseUrl();

        if(!$preserveCurrentUrl){
            return $newStoreBaseUrl;
        }

        $baseUrl = $this->parseBaseUrl($this->storeManager->getStore()->getBaseUrl());

        $url = str_replace($baseUrl,$newStoreBaseUrl,$currentUrl);
        if(strpos($currentUrl,$currentStore)){
            $pos = strpos($currentUrl, $currentStore);
            if ($pos !== false) {
                $url = substr_replace($currentUrl, $newStoreCode, $pos, strlen($currentStore));
            }
        }

        return $url;
    }

    protected function parseBaseUrl($baseUrl){

        $url = rtrim($baseUrl,'/');
        $baseUrlArray = explode('/',$url);
        array_pop($baseUrlArray);
        $baseUrlParsed = implode('/', $baseUrlArray);

        return $baseUrlParsed . '/';
    }

    public function getWebsiteSourceCode()
    {
        $website = $this->getSellerWebsite();
        $websiteCode = $website->getCode();

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(
            'inventory_stock_sales_channel'
        );

        $sourceCode = "";
        $sql = "SELECT stock_id FROM {$tableName} WHERE code = '{$websiteCode}'";
        $result = $connection->fetchAll($sql);
        if (array_key_exists(0, $result)) {
            $sourceArray = $this->sourcesAssignedToStock->execute($result[0]['stock_id']);
            $sourceCode = array_key_exists(0,  $sourceArray) ? $sourceArray[0]->getSourceCode() : "";
        }

        return $sourceCode;
    }
}