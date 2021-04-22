<?php


namespace Rarod\Sellers\Ui\Component\Listing\Column;


class Website extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    private $websiteFactory;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->websiteFactory = $websiteFactory;
    }

    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {

            $website = $this->websiteFactory->create();

            foreach ($dataSource['data']['items'] as &$item) {
                $websiteName = $website->load($item['website_id'])->getName();
                $item['website_id'] = $websiteName;
            }
        }
        return $dataSource;
    }
}