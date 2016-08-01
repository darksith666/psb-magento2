<?php
/**
 * Attribution Notice: Based on the Paypal payment module included with Magento 2.
 *
 * @copyright  Copyright (c) 2015 Magento
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Paysbuy\PsbGateway\Model;

use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;

class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod {

		// const CGI_URL = 'https://www.paysbuy.com/paynow.aspx';
		// const CGI_URL_TEST = 'https://demo.paysbuy.com/paynow.aspx';

		// const DEFAULT_CURRENCY = 'THB';
		const DEFAULT_CURRENCY_TYPE = 'TH';

		const URL_SUCCESS = 'psb/checkout/success';
		const URL_CALLBACK = 'psb/ipn/callback';

		protected $_code = 'psb';

		// // Currency code conversions
		// static protected $_currCodes = [
		// 	'THB' => 764,
		// 	'AUD' => 036,		
		// 	'GBP' => 826,	
		// 	'EUR' => 978,		
		// 	'HKD' => 344,		
		// 	'JPY' => 392,		
		// 	'NZD' => 554,
		// 	'SGD' => 702,	
		// 	'CHF' => 756,	
		// 	'USD' => 840	
		// ];

		// Currency code conversions
		static protected $_currTypes = [
			'THB' => 'TH',
			'AUD' => 'AU',		
			'GBP' => 'GB',	
			'EUR' => 'EU',		
			'HKD' => 'HK',		
			'JPY' => 'JP',		
			'NZD' => 'NZ',
			'SGD' => 'SG',	
			'CHF' => 'CH',	
			'USD' => 'US'	
		];


		/**
		* @var \Magento\Framework\Exception\LocalizedExceptionFactory
		*/
		protected $_exception;

		/**
		* @var \Magento\Sales\Api\TransactionRepositoryInterface
		*/
		protected $_transactionRepository;

		/**
		* @var Transaction\BuilderInterface
		*/
		protected $_transactionBuilder;

		/**
		* @var \Magento\Framework\UrlInterface
		*/
		protected $_urlBuilder;

		/**
		* @var \Magento\Sales\Model\OrderFactory
		*/
		protected $_orderFactory;

		/**
		* @var \Magento\Store\Model\StoreManagerInterface
		*/
		protected $_storeManager;

		/**
		* @param \Magento\Framework\UrlInterface $urlBuilder
		* @param \Magento\Framework\Exception\LocalizedExceptionFactory $exception
		* @param \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository
		* @param Transaction\BuilderInterface $transactionBuilder
		* @param \Magento\Sales\Model\OrderFactory $orderFactory
		* @param \Magento\Store\Model\StoreManagerInterface $storeManager
		* @param \Magento\Framework\Model\Context $context
		* @param \Magento\Framework\Registry $registry
		* @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
		* @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
		* @param \Magento\Payment\Helper\Data $paymentData
		* @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
		* @param \Magento\Payment\Model\Method\Logger $logger
		* @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
		* @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
		* @param array $data
		*/
		public function __construct(
			\Magento\Framework\UrlInterface $urlBuilder,
			\Magento\Framework\Exception\LocalizedExceptionFactory $exception,
			\Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
			\Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
			\Magento\Sales\Model\OrderFactory $orderFactory,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Magento\Framework\Model\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
			\Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
			\Magento\Payment\Helper\Data $paymentData,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Payment\Model\Method\Logger $logger,
			\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
			\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
			array $data = []
		) {
			$this->_urlBuilder = $urlBuilder;
			$this->_exception = $exception;
			$this->_transactionRepository = $transactionRepository;
			$this->_transactionBuilder = $transactionBuilder;
			$this->_orderFactory = $orderFactory;
			$this->_storeManager = $storeManager;

			parent::__construct(
					$context,
					$registry,
					$extensionFactory,
					$customAttributeFactory,
					$paymentData,
					$scopeConfig,
					$logger,
					$resource,
					$resourceCollection,
					$data
			);
		}

		/**
		 * Instantiate state and set it to state object.
		 *
		 * @param string                        $paymentAction
		 * @param \Magento\Framework\DataObject $stateObject
		 */
		public function initialize($paymentAction, $stateObject)
		{
				$payment = $this->getInfoInstance();
				$order = $payment->getOrder();
				$order->setCanSendNewEmailFlag(false);

				$stateObject->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
				$stateObject->setStatus('pending_payment');
				$stateObject->setIsNotified(false);
		}

		// public static function getCurrencyCode($alpha) {
		// 	return self::$_currCodes[isset(self::$_currCodes[$alpha]) ? $alpha : self::DEFAULT_CURRENCY]; 
		// }

		public static function getCurrencyType($alpha) {
			return self::$_currTypes[isset(self::$_currTypes[$alpha]) ? $alpha : self::DEFAULT_CURRENCY_TYPE]; 
		}

		/**
		 * Get return URL.
		 *
		 * @param int|null $storeId
		 *
		 * @return string
		 */
		public function getSuccessUrl($storeId = null)
		{
			return $this->_getUrl(self::URL_SUCCESS, $storeId);
		}

		/**
		 * Get notify (IPN) URL.
		 *
		 * @param int|null $storeId
		 *
		 * @return string
		 */
		public function getNotifyUrl($storeId = null)
		{
			return $this->_getUrl(self::URL_CALLBACK, $storeId, false);
		}

		/**
		 * Build URL for store.
		 *
		 * @param string    $path
		 * @param int       $storeId
		 * @param bool|null $secure
		 *
		 * @return string
		 */
		protected function _getUrl($path, $storeId, $secure = null)
		{
			$store = $this->_storeManager->getStore($storeId);

			return $this->_urlBuilder->getUrl(
				$path,
				['_store' => $store, '_secure' => $secure === null ? $store->isCurrentlySecure() : $secure]
			);
		}

		// /**
		//  * Get main URL for PAYSBUY gateway
		//  *
		//  * @return string
		//  */
		// public function getGatewayUrl() {

		// 	$test_mode = $this->getConfigData('test_mode');
		// 	if ($test_mode == '0') {
		// 		$url = self::CGI_URL;
		// 	} else {
		// 		$url = self::CGI_URL_TEST;
		// 	}
			
		// 	return $url;
		// }

		/**
		 * Get full URL for payment
		 *
		 * @param order		$order
		 *
		 * @return string
		 */
		public function getPaymentUrl($order) {
			$cur = self::getCurrencyType($order->getBaseCurrencyCode());
			$grandTotalAmount = sprintf('%.2f', $order->getBaseGrandTotal());
			$orderId = $order->getIncrementId();

			$item_names = [];
			$items = $order->getItemsCollection();
			foreach ($items as $item){
				$item_name = $item->getName();
				$qty = number_format($item->getQtyOrdered(), 0, '.', ' ');
				$item_names[] = $item_name . ' x ' . $qty;
			}
			$itemName = sprintf( __('Order %s '), $orderId ) . " - " . implode(', ', $item_names);


			\PaysbuyService::setup([
				'psbID' => $this->getConfigData('merchant_psbid'),
				'username' => $this->getConfigData('merchant_id'),
				'secureCode' => $this->getConfigData('merchant_securecode')
			]);
			\PaysbuyService::$testMode = $this->getConfigData('test_mode') != '0';


			$payUrl = \PaysbuyPaynow::authenticate([
				'method' => '1',
				'language' => 'E',
				'inv' => $orderId,
				'itm' => $itemName,
				'amt' => $grandTotalAmount,
				'curr_type' => $cur,
				'resp_front_url' => $this->getSuccessUrl(),
				'resp_back_url' => $this->getNotifyUrl(),
				'opt_fix_redirect' => '0'
			]);

			return $payUrl;

		}

		// /**
		//  * Get field key=>value list for the checkout form
		//  *
		//  * @return array
		//  */
		// public function getCheckoutFormFields($order) {
			
		// 	$cur = self::getCurrencyCode($order->getBaseCurrencyCode());
			
		// 	$grandTotalAmount = sprintf('%.2f', $order->getBaseGrandTotal());
						
		// 	$orderId = $order->getIncrementId();
		// 	$item_names = [];
		// 	$items = $order->getItemsCollection();
		// 	foreach ($items as $item){
		// 		$item_name = $item->getName();
		// 		$qty = number_format($item->getQtyOrdered(), 0, '.', ' ');
		// 		$item_names[] = $item_name . ' x ' . $qty;
		// 	}	
		// 	$paysbuy_args['item_name'] 	= sprintf( __('Order %s '), $orderId ) . " - " . implode(', ', $item_names);
		// 	$orderReferenceValue = $orderId; // TODO - check if this is right - used to be $this->getCheckout()->getLastRealOrderId();
		// 	$merchantId = $this->getConfigData('merchant_id');
		// 	$postback_url = $this->getNotifyUrl();
		// 	$url = $this->getSuccessUrl();
		// 	$psb = 'psb';
			
		// 	$fields = [
		// 		'psb'								=> $psb,
		// 		'biz'								=> $merchantId,
		// 		'amt'								=> $grandTotalAmount, 
		// 		'currencyCode'			=> $cur,
		// 		'itm'								=> $paysbuy_args['item_name'],
		// 		'inv'								=> $orderReferenceValue,
		// 		'opt_fix_redirect'	=> '1',
		// 		'postURL'						=> $url,
		// 		'reqURL'						=> $postback_url,
		// 	];

		// 	$filtered_fields = [];
		// 	foreach ($fields as $k=>$v) {
		// 		$value = str_replace("&","and",$v);
		// 		$filtered_fields[$k] =  $value;
		// 	}
			
		// 	return $filtered_fields;

		// }

}
