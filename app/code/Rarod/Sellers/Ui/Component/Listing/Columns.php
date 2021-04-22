<?php

namespace Rarod\Sellers\Ui\Component\Listing;

use Rarod\Sellers\Model\Resource\Seller\Collection;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * @var Collection
     */
    private $sellerCollection;
    /**
     * @var UiComponentFactory
     */
    private $componentFactory;

    public function __construct(UiComponentFactory $componentFactory, Collection $sellerCollection, ContextInterface $context, array $components = [], array $data = [])
    {
        parent::__construct($context, $components, $data);
        $this->sellerCollection = $sellerCollection;
        $this->componentFactory = $componentFactory;
    }

    public function prepare()
    {
        $sellers = $this->sellerCollection->load();
        foreach ($sellers as $seller) {
            $column = $this->createColumn($seller, $this->getContext());
            $column->prepare();
            $this->addComponent($seller->getName(), $column);
        }
        parent::prepare();
    }

    private function createColumn($seller, $context)
    {
        $name = $seller->getName();
        $config = [
            'label' => $name ,
            'dataType' => 'text',
            'visible' => $seller->getActive(),
            'sortable' => false
        ];

        $arguments = [
            'config' => [
                'class' => '\Rarod\Sellers\Ui\Component\Listing\Column\Stock',
            ],
            'data' => [
                'config' => $config,
            ],
            'context' => $context,
        ];

        return $this->componentFactory->create($name, 'column', $arguments);
    }
}