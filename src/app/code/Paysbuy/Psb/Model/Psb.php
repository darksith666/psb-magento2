<?php
class Paysbuy_Psb_Model_Psb extends Mage_Payment_Model_Method_Abstract {
	
	const CGI_URL = 'https://www.paysbuy.com/paynow.aspx';
    const CGI_URL_TEST = 'https://demo.paysbuy.com/paynow.aspx';
	
	protected $_code = 'psb';
	protected $_formBlockType = 'psb/form';	
	protected $_allowCurrencyCode = array('THB','AUD','GBP','EUR','HKD','JPY','NZD','SGD','CHF','USD');
	
    public function getUrl()
    {
    	$url = $this->getConfigData('cgi_url');
    	
    	if($url == '0')
    	{
    		$url = self::CGI_URL;
    	}
		else if($url == '1')
		{
			$url = self::CGI_URL_TEST;
		}
    	
    	return $url;
    }//end function getUrl
	
	public function getSession()
    {
        return Mage::getSingleton('psb/psb_session');
    }//end function getSession
	
	public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }//end function getCheckout
	
	public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }//end function getQuote
	
	public function getCheckoutFormFields()
	{
		$order = Mage::getSingleton('sales/order');
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
		$postbackground = $this->getConfigData('postbackground');
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
			'reqURL'					=> $postbackground,
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
            Mage::throwException(Mage::helper('psb')->__('Selected currency code ('.$currency_code.') is not compatabile with Paysbuy'));
        }
        return $this;
    }//end function validate
	
	public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
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