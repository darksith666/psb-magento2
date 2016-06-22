<?php
/**
 * Attribution Notice: Based on the Paypal payment module included with Magento 2.
 *
 * @copyright  Copyright (c) 2015 Magento
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Paysbuy\PsbGateway\Controller\Ipn;

use Magento\Framework\App\Action\Action as AppAction;

class Callback extends AppAction
{
	
	const STATUS_RECEIVED = '00';
	const STATUS_WAITCSP = '02';
	const STATUS_FAILED = '99';

	/**
	* @var \Paysbuy\PsbGateway\Model\PaymentMethod
	*/
	protected $_paymentMethod;

	/**
	* @var \Magento\Sales\Model\Order
	*/
	protected $_order;

	/**
	* @var \Magento\Sales\Model\OrderFactory
	*/
	protected $_orderFactory;

	/**
	* @var Magento\Sales\Model\Order\Email\Sender\OrderSender
	*/
	protected $_orderSender;

	/**
	* @var \Psr\Log\LoggerInterface
	*/
	protected $_logger;

	protected $_postbackDets;

	/**
	* @param \Magento\Framework\App\Action\Context $context
	* @param \Magento\Sales\Model\OrderFactory $orderFactory
	* @param \Paysbuy\PsbGateway\Model\PaymentMethod $paymentMethod
	* @param Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
	* @param  \Psr\Log\LoggerInterface $logger
	*/
	public function __construct(
	\Magento\Framework\App\Action\Context $context,
	\Magento\Sales\Model\OrderFactory $orderFactory,
	\Paysbuy\PsbGateway\Model\PaymentMethod $paymentMethod,
	\Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
	\Psr\Log\LoggerInterface $logger
	) {
		$this->_paymentMethod = $paymentMethod;
		$this->_orderFactory = $orderFactory;
		$this->_orderSender = $orderSender;
		$this->_logger = $logger;
		$this->_postbackDets = _getPostbackDetails();
		parent::__construct($context);
	}

	/**
	* Handle POST request to PAYSBUY callback endpoint.
	*/
	public function execute()
	{
		try {
			$this->_order = $this->_loadOrder($this->_postbackDets['ref']);

			switch ($this->_postbackDets['status']) {
				case self::STATUS_RECEIVED:
					_handlePaymentRecieved();
					break;
				case self::STATUS_WAITCSP:
					_handlePaymentWaitingCSP();
					break;

				case self::STATUS_FAILED:
					_handlePaymentFailed();
					break;
				default:
					$this->_handleUnknownCallback($this->_postbackDets['status']);
					break;
			}

			$this->_success();

		} catch (\Exception $e) {
			$this->_logger->addError("PAYSBUY: error processing callback");
			$this->_logger->addError($e->getMessage());
			return $this->_failure();
		}
	}


	protected function _handlePaymentReceived() {
		$currCode = $this->_order->getBaseCurrencyCode();
		$orderAmount = $this->_order->getGrandTotal()/1;
		$receivedAmount = $this->_postbackDets['amt']/1;
		$fmtReceivedAmount = $currCode . sprintf('%.2f', $receivedAmount);
		if ($orderAmount == $receivedAmount) {
			$msg = $this->_makeComment("Received through Paysbuy payment: ", $fmtReceivedAmount);
			$this->_changeOrderState(\Magento\Sales\Model\Order::STATE_PROCESSING, $msg);
		} else {
			$msg = $this->_makeComment("Incorrect amount received through Paysbuy payment: ", $fmtReceivedAmount);
			$fmtOrderAmount = $currCode . sprintf('%.2f', $orderAmount);
			$msg .= " - " . $this->_makeComment("Expected: ", $fmtOrderAmount);
			$this->_changeOrderState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW, $msg);
		}
	}

	protected function _handlePaymentWaitingCSP() {
		$msg = $this->_makeComment("Awaiting counter service payment");
		$this->_changeOrderState(\Magento\Sales\Model\Order::STATE_HOLDED, $msg);
	}

	protected function _handlePaymentFailed() {
		$msg = $this->_makeComment("Payment failed");
		$this->_changeOrderState(\Magento\Sales\Model\Order::STATE_CLOSED, $msg);
	}

	protected function _handleUnknownCallback($status)
	{
		$this->_logger->addNotice("PAYSBUY: Received callback of unknown type $status");
		return;
	}

	protected function _getPostbackDetails() {
		$p = $this->request->getPost();
		$result = $p['result'];
		return [
			'status'    => substr($result, 0, 2),
			'ref'       => trim(substr($result, 2)),
			'apCode'    => $p['apCode'],
			'amt'       => $p['amt'],
			'fee'       => $p['fee'],
			'method'    => $p['method'],
			'confirm_cs'=> strtolower(trim($p['confirm_cs']))
		];
	}

	protected function _changeOrderState($state, $message) {
		$this->_order->setState($state, true, $message, 1)->save();
		$this->_order->setState($state);
		$hist = $this->_order->addStatusHistoryComment($message);
		$hist->setIsCustomerNotified(true);
		$this->_order->save();

		$this->_orderSender->send($this->_order);
		/// $this->_order->sendOrderUpdateEmail(true, $message);   /// FIX!
	}

	protected function _loadOrder($ref)
	{
		$order = $this->_orderFactory->create()->loadByIncrementId($ref);

		if (!($this->_order && $this->_order->getId())) {
			throw new Exception('Could not find Magento order with id $order_id');
		}

		return $order;

	}

	protected function _success()
	{
		$this->getResponse()
			 ->setStatusHeader(200);
	}

	protected function _failure()
	{
		$this->getResponse()
			 ->setStatusHeader(400);
	}

	protected function _makeComment($comment, $uffix = '')
	{
		$fullComment = __($comment) . $suffix;
		return $fullComment;
	}
}
