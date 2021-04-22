<?php

namespace Rarod\Sellers\Block\Adminhtml;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Block\Template;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Inventory\Model\StockSourceLink;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventorySales\Model\ResourceModel\StockIdResolver;
use Magento\Store\Model\WebsiteFactory;

class Acl extends Template
{
    /**
     * @var AclRetriever
     */
    private $aclRetriever;
    /**
     * @var Session
     */
    private $authSession;
    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;
    /**
     * @var StockIdResolver
     */
    private $stockIdResolver;
    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;
    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;

    public function __construct(
        AclRetriever $aclRetriever,
        Session $authSession,
        Template\Context $context,
        WebsiteFactory $websiteFactory,
        StockIdResolver $stockIdResolver,
        SourceRepositoryInterface $sourceRepository,
        StockRepositoryInterface $stockRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->aclRetriever = $aclRetriever;
        $this->authSession = $authSession;
        $this->websiteFactory = $websiteFactory;
        $this->stockIdResolver = $stockIdResolver;
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->stockRepository = $stockRepository;
    }

    private function getAdminWebsites()
    {
        $user = $this->authSession->getUser();
        $role = $user->getRole();
        $userWebsites = $role->getGwsWebsites();
        return $userWebsites;
    }
    function getStockIdSources($stockId)
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();
        $stockSearchCriteria = $searchCriteria->addFilter('stock_id', $stockId)->create();
        $stock = $this->stockRepository->getList($stockSearchCriteria);
        $stockNames = [];
        foreach ($stock->getItems() as $item) {
            $stockName = $item->getName();
            $stockNames = $stockName;
        }
        return $stockNames;
    }
    public function getAdminSources()
    {
        $userWebsites = $this->getAdminWebsites();
        $sources = [];
        foreach ($userWebsites as $websiteId) {
            $websiteCode = $this->websiteFactory->create()->load($websiteId)->getCode();
            $stockId = $this->stockIdResolver->resolve('website', $websiteCode);
            $sources[] = $this->getStockIdSources($stockId);
        }
        return json_encode($sources);
    }
}