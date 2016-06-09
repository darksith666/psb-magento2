<?php
namespace Paysbuy\PsbGateway\Block;


class Redirect extends \Magento\Framework\View\Element\AbstractBlock
{

	/**
	 * @var \Paysbuy\PsbGateway\Model\PaymentMethodFactory
	 */
	protected $_paymentMethodFactory;

	/**
	 * @var \Magento\Framework\Data\FormFactory
	 */
	protected $_formFactory;

	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Paysbuy\PsbGateway\Model\PaymentMethodFactory $paymentMethodFactory,
		\Magento\Framework\Data\FormFactory $formFactory,
		array $data = []
	) {
		$this->_formFactory = $formFactory;
		$this->_paymentMethodFactory = $paymentMethodFactory;
		parent::__construct(
			$context,
			$data
		);
	}

	protected function _toHtml() {

		$order = $this->getData('order');
		$payMeth = $this->_paymentMethodFactory->create();

		$form = $this->_formFactory->create();
		$form->setAction($payMeth->getGatewayUrl())
			->setId('psb_checkout')
			->setName('psb_checkout')
			->setMethod('post')
			->setUseContainer(true);
		foreach ($payMeth->getCheckoutFormFields($order) as $field=>$value) {
			$form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
		}
		$html = '<html><body>';
		$html.= __('You will be redirected to Paysbuy in a few seconds.');
		$html.= $form->toHtml();
		// $html.= '<script type="text/javascript">document.getElementById("psb_checkout").submit();</script>';
		$html.= '<script type="text/javascript">alert("It worked!");</script>';
		$html.= '</body></html>';

		return $html;
	}

}
