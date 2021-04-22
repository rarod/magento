<?php


namespace Rarod\Sellers\Model;


class Seller extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'rarod_sellers';
    protected $_cacheTag = 'rarod_sellers';
    protected $_eventPrefix = 'rarod_sellers';

    protected function _construct()
    {
        $this->_init('Rarod\Sellers\Model\Resource\Seller');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}