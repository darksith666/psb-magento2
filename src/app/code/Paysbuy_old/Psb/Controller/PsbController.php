<?php
namespace Paysbuy\Psb\Controller;

/*
*Paysbuy Psb Controller
*By: Paysbuy
*/

class Psb extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->salesOrderFactory = $salesOrderFactory;
        parent::__construct(
            $context
        );
    }


	public function redirectAction() {
        $session = $this->checkoutSession;
		$session->setPsbQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('psb/redirect')->toHtml());
        $session->unsQuoteId();

	}
	
	public function cancelAction()
    {
        $session = $this->checkoutSession;
        $session->setQuoteId($session->getPsbQuoteId(true));
        
        if ($session->getLastRealOrderId()) {
            $order = $this->salesOrderFactory->create()->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
        }
        $this->_redirect('checkout/cart');
     }

    public function successAction()
    {
        $session = $this->checkoutSession;
        $session->setQuoteId($session->getPsbQuoteId(true));
        
        $this->checkoutSession->getQuote()->setIsActive(false)->save();
		
        $order = $this->salesOrderFactory->create();
        $order->load($this->checkoutSession->getLastOrderId());
    
	    $order->save();
        
        if($order->getId()){
            $order->sendNewOrderEmail();
        }

        $this->checkoutSession->unsQuoteId();
		
    	
        $this->_redirect('checkout/onepage/success');
    }
    
    public function failureAction()
    {
    	$session = $this->checkoutSession;
        $session->setQuoteId($session->getPsbQuoteId(true));
        
        if ($session->getLastRealOrderId()) {
            $order = $this->salesOrderFactory->create()->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
        }
        $this->_redirect('checkout/onepage/failure');
    }
}