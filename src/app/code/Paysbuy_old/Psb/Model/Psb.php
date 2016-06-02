<?php
namespace Paysbuy\Psb\Model;

class Psb extends \Magento\Payment\Model\Method\AbstractMethod {
	
	const CGI_URL = 'https://www.paysbuy.com/paynow.aspx';
    const CGI_URL_TEST = 'https://demo.paysbuy.com/paynow.aspx';
	
	protected $_code = 'psb';
	protected $_formBlockType = 'psb/form';	
	protected $_allowCurrencyCode = array('THB','AUD','GBP','EUR','HKD','JPY','NZD','SGD','CHF','USD');

    /**
     * @var \Paysbuy\Psb\Model\Psb\Session
     */
    protected $psbPsbSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $salesOrder;

    public function __construct(
        \Paysbuy\Psb\Model\Psb\Session $psbPsbSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $salesOrder
    ) {
        $this->psbPsbSession = $psbPsbSession;
        $this->checkoutSession = $checkoutSession;
        $this->salesOrder = $salesOrder;
    }
    public function getUrl()
    {
    	$test_mode = $this->getConfigData('test_mode');
    	
    	if($test == '0')
    	{
    		$url = self::CGI_URL;
    	}
		else if($test_mode == '1')
		{
			$url = self::CGI_URL_TEST;
		}
    	
    	return $url;
    }//end function getUrl
	
	public function getSession()
    {
        return $this->psbPsbSession;
    }//end function getSession
	
	public function getCheckout()
    {
        return $this->checkoutSession;
    }//end function getCheckout
	
	public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }//end function getQuote
	
	public function getCheckoutFormFields()
	{
		$order = $this->salesOrder;
		$order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
		
		$currency_code = $order->getBaseCurrencyCode();
		
		$grandTotalAmount = sprintf('%.2f', $order->getGrandTotal());
		
		switch($currency_code){
		case 'THB':
			$cur = 764;
			break;
		case 'AUD':
			$cur = 036;
			break;		
		case 'GBP':
			$cur = 826;
			break;	
		case 'EUR':
			$cur = 978;
			break;		
		case 'HKD':
			$cur = 344;
			break;		
		case 'JPY':
			$cur = 392;
			break;		
		case 'NZD':
			$cur = 554;
			break;
		case 'SGD':
			$cur = 702;
			break;	
		case 'CHF':
			$cur = 756;
			break;	
		case 'USD':
			$cur = 840;
			break;	
		default:
			$cur = 764;
		}	 
		
		$orderId = $order->getIncrementId();
		$item_names = array();
		$items = $order->getItemsCollection();
		foreach ($items as $item){
			$item_name = $item->getName();
 		  	$qty = number_format($item->getQtyOrdered(), 0, '.', ' ');
			$item_names[] = $item_name . ' x ' . $qty;
		}	
		$paysbuy_args['item_name'] 	= sprintf( __('Order %s '), $orderId ) . " - " . implode(', ', $item_names);
		$orderReferenceValue = $this->getCheckout()->getLastRealOrderId();
		$merchantId = $this->getConfigData('merchant_id');
		$postback_url = $this->getConfigData('postback_url');
		$url_r = Mage::getUrl('receive_data/receive_front.php');
		$url = str_replace("index.php/","",$url_r);
		$psb = 'psb';
		
		$fields = array(
			'psb'			            => $psb,
			'biz'						=> $merchantId,
			'amt'						=> $grandTotalAmount, 
			'currencyCode'				=> $cur,
			'itm'    				    => $paysbuy_args['item_name'],
			'inv'						=> $orderReferenceValue,
			'opt_fix_redirect'			=> '1',
			'postURL'					=> $url,
			'reqURL'					=> $postback_url,
		);

		$filtered_fields = array();
        foreach ($fields as $k=>$v) {
            $value = str_replace("&","and",$v);
            $filtered_fields[$k] =  $value;
        }
        
        return $filtered_fields;
	}//end function getCheckoutFormFields
	
	public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('psb/form', $name)
            ->setMethod('psb')
            ->setPayment($this->getPayment())
            ->setTemplate('psb/form.phtml');

        return $block;
    }//end function createFormBlock
	
	public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Selected currency code ('.$currency_code.') is not compatabile with Paysbuy'));
        }
        return $this;
    }//end function validate
	
	public function onOrderValidate(\Magento\Sales\Model\Order\Payment $payment)
    {
       return $this;
    }//end function onOrderValidate

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {
		
	}//end function onInvoiceCreate
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('psb/psb/redirect');
	}//end function getOrderPlaceRedirectUrl
}
?>