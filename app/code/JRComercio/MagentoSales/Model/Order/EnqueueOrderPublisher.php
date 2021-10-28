<?php

namespace JRComercio\MagentoSales\Model\Order;

class EnqueueOrderPublisher
{
    const TOPIC_NAME = 'enqueue.created.order';

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;

    /**
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     */
    public function __construct(\Magento\Framework\MessageQueue\PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }
    
    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function execute(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->publisher->publish(self::TOPIC_NAME, $order);
    }
}