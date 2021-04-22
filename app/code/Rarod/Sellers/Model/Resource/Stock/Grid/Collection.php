<?php
namespace Rarod\Sellers\Model\Resource\Stock\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    public function __construct
    (
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = null,
        $resourceModel = null,
        $identifierName = null,
        $connectionName = null
    )
    {
        $mainTable = 'catalog_product_entity';
        $resourceModel = \Magento\Catalog\Model\ResourceModel\Product::class;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel, $identifierName, $connectionName);
    }

    public function _beforeLoad()
    {
        $this->addFieldToFilter('type_id', 'simple');
        return parent::_beforeLoad();
    }
}