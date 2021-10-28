<?php

namespace JRComercio\MagentoSales\Plugin\Model\Service;

class OrderService
{
    /**
     * @var \JRComercio\MagentoSales\Model\Order\OrderCreatedPublisher
     */
    protected $orderCreatedPublisher;

    /**
 	* Order Service constructor.
 	*
 	* @param \JRComercio\MagentoSales\Model\Order\OrderCreatedPublisher $enqueueOrderPublisher
 	*/
    public function __construct(
        \JRComercio\MagentoSales\Model\Order\OrderCreatedPublisher $orderCreatedPublisher
    ) {
        $this->orderCreatedPublisher = $orderCreatedPublisher;
    }

    public function afterPlace(\Magento\Sales\Api\OrderManagementInterface $subject, \Magento\Sales\Api\Data\OrderInterface $result)
    {
        $this->orderCreatedPublisher->execute($result);
        return $result;
    }
}
