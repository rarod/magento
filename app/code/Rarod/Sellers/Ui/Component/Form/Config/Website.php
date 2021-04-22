<?php


namespace Rarod\Sellers\Ui\Component\Form\Config;

use Magento\Framework\Data\OptionSourceInterface;

class Website implements OptionSourceInterface
{
    protected $options;

    protected $websiteFactory;

    public function __construct
    (
        \Magento\Store\Model\WebsiteFactory $websiteFactory
    )
    {
        $this->websiteFactory = $websiteFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->websiteFactory->create()->getCollection();

        foreach ($collection as $website) {
            $this->options[] = [
                'label' => $website->getName(),
                'value' => $website->getWebsiteId()
            ];
        }

        return $this->options;
    }
}