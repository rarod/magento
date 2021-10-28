<?php

namespace JRComercio\MagentoSales\Model\Order;

class OrderCreatedPublisher
{
    const TOPIC_NAME = 'order.created';

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     */
    public function __construct(
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->publisher = $publisher;
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function execute(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        try {
            if ($order->getCustomerId()) {
                /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                $customer = $this->customerRepository->getById($order->getCustomerId());

                if ($customer) {
                    $erpCustomerId = $customer->getCustomAttribute('erp_customer_id');
                    $representativeId = $customer->getCustomAttribute('representative_id');

                    $group = $this->groupRepository->getById($customer->getGroupId());
                }

                $extensionAttributes = $order->getExtensionAttributes();

                $extensionAttributes->setErpCustomerId($erpCustomerId->getValue());
                $extensionAttributes->setRepresentativeId($representativeId->getValue());
                $extensionAttributes->setCustomerPriceTable($group->getCode());

                $order->setExtensionAttributes($extensionAttributes);

                $this->publisher->publish(self::TOPIC_NAME, $order);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
