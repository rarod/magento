<?php

namespace JRComercio\PaymentMethods\Model\Payment\Method;

class Payment415 extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_CODE = 'payment_415';

    // /**
    //  * XML Paths for configuration constants
    //  */
    // const XML_PATH_PAYMENT_PAYMENT_23_ACTIVE = 'payment/payment_23/active';

    // const XML_PATH_PAYMENT_PAYMENT_23_ORDER_STATUS = 'payment/payment_23/order_status';

    // const XML_PATH_PAYMENT_PAYMENT_23_PAYMENT_ACTION = 'payment/payment_23/payment_action';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CODE;
}
