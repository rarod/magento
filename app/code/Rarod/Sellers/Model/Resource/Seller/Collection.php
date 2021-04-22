<?php


namespace Rarod\Sellers\Model\Resource\Seller;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'rarod_sellers_collection';
    protected $_eventObject = 'sellers_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Rarod\Sellers\Model\Seller', 'Rarod\Sellers\Model\Resource\Seller');
    }
}