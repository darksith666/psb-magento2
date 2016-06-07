<?php
/**
 * Attribution Notice: Based on the Paypal payment module included with Magento 2.
 *
 * @copyright  Copyright (c) 2015 Magento
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Paysbuy\PsbGateway\Controller\Checkout;

class Start extends \Magento\Framework\App\Action\Action
{
    /**
    * @var \Magento\Checkout\Model\Session
    */
    protected $_checkoutSession;

    /**
    * @var \Paysbuy\PsbGateway\Model\PaymentMethod
    */
    protected $_paymentMethod;

    /**
    * @param \Magento\Framework\App\Action\Context $context
    * @param \Magento\Checkout\Model\Session $checkoutSession
    * @param \Paysbuy\PsbGateway\Model\PaymentMethod $paymentMethod
    */
    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Paysbuy\PsbGateway\Model\PaymentMethod $paymentMethod
    ) {
        $this->_paymentMethod = $paymentMethod;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
    * Start checkout by requesting checkout code and dispatching customer to PAYSBUY.
    */
    public function execute()
    {
        die("REDIRECT TO PAYSBUY HERE!!!");
        $this->getResponse()->setRedirect(
            $this->_paymentMethod->getCheckoutUrl($this->getOrder())
        );
    }

    /**
    * Get order object.
    *
    * @return \Magento\Sales\Model\Order
    */
    protected function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
}
