<?php

namespace Rarod\Sellers\Controller\Adminhtml\Seller;

use Magento\Backend\App\Action;

class Stock extends \Magento\Backend\App\Action
{
    private $resultPageFactory;

    public function __construct
    (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}