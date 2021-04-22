<?php


namespace Rarod\Sellers\Controller\Adminhtml\Seller;


class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $sellerFactory;
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Rarod\Sellers\Model\SellerFactory $sellerFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->sellerFactory = $sellerFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $seller = $this->sellerFactory->create()->load($id);
            if (!$seller->getId()) {
                $this->messageManager->addErrorMessage(__('Registro não encontrado'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('rarod_seller', $seller);

        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}