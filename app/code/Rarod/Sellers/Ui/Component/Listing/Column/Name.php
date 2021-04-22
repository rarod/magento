<?php


namespace Rarod\Sellers\Ui\Component\Listing\Column;


use Magento\Catalog\Model\ProductFactory;

class Name extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    public function __construct(
        ProductFactory $productFactory,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productFactory = $productFactory;
    }

    public function prepareDataSource(array $dataSource)
    {
        $productModel = $this->productFactory->create();
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as &$item) {
                $product = $productModel->load($item['entity_id']);
                $item['name'] = $product->getName();
            }
        }
        return $dataSource;
    }
}