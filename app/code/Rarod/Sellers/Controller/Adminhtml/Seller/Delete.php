<?php


namespace Rarod\Sellers\Controller\Adminhtml\Seller;


class Delete extends \Magento\Backend\App\Action
{
    private $sellerFactory;
    protected $messageManager;
    protected $resultRedirect;

    public function __construct
    (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Rarod\Sellers\Model\SellerFactory $sellerFactory
    )
    {
        parent::__construct($context);
        $this->sellerFactory = $sellerFactory;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function execute()
    {
        $result = $this->resultRedirectFactory->create();

        try {
            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $seller = $this->sellerFactory->create();
                $seller->load($id);
                $seller->delete();
                $this->messageManager->addSuccess(__('Vendedor removido com sucesso'));
            }


        } catch (\Exception $e) {

            $this->messageManager->addError(__('Não foi possível salvar os dados. Por favor, tente novamente mais tarde.'));

        }

        $result->setPath('*/*/index');
        return $result;
    }
}