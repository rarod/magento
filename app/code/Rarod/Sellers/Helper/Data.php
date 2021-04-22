<?php

namespace Rarod\Sellers\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Rarod\Sellers\Model\SellerFactory
     */
    private $sellerFactory;

    public function __construct(
        Context $context,
        \Rarod\Sellers\Model\SellerFactory $sellerFactory
    )
    {
        parent::__construct($context);
        $this->sellerFactory = $sellerFactory;
    }


    public function getSellerById($id)
    {
        return $this->sellerFactory->create()
            ->getCollection()
            ->addFieldToFilter('id', $id)
            ->getFirstItem();
    }

    public function getSellersByWebSiteId($websiteId)
    {
        return $this->sellerFactory->create()
            ->getCollection()
            ->addFieldToFilter('website_id', $websiteId)
            ->getFirstItem();
    }

    public function setSellersByWebSiteId($websiteId)
    {
        return $this->sellerFactory->create()
            ->getCollection()
            ->addFieldToFilter('website_id', $websiteId)
            ->getFirstItem();
    }



}