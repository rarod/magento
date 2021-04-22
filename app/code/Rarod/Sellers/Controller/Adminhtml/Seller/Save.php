<?php


namespace Rarod\Sellers\Controller\Adminhtml\Seller;


class Save extends \Magento\Backend\App\Action
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
            $post = $this->_request->getPostValue();

            $seller = $this->sellerFactory->create();

            if (isset($post['seller']['id'])) {
                $seller->load($post['seller']['id']);
            }

            $seller->setName($post['seller']['name']);
            $seller->setWebsiteId($post['seller']['website_id']);
            $seller->setPhone($post['seller']['phone']);
            $seller->setActive($post['seller']['active']);

            $seller->save();

            $this->messageManager->addSuccess(__('Vendedor cadastrados com sucesso'));

        } catch (\Exception $e) {

            $this->messageManager->addError(__('Não foi possível salvar os dados. Por favor, tente novamente mais tarde.'));

        }

        $result->setPath('*/*/index');
        return $result;
    }
}