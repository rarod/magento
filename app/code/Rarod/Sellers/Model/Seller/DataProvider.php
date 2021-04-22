<?php

namespace Rarod\Sellers\Model\Seller;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $storeManager;

    public function __construct(

        $name,
        $primaryFieldName,
        $requestFieldName,
        \Rarod\Sellers\Model\Resource\Seller\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }


        $items = $this->collection->getItems();
        $this->loadedData = array();

        foreach ($items as $contact) {

            $this->loadedData[$contact->getId()]['seller'] = $contact->getData();
        }
        return $this->loadedData;
    }

}