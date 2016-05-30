<?php
namespace Paysbuy\Psb\Block;



class Redirect extends \Magento\Framework\View\Element\AbstractBlock
{

    /**
     * @var \Paysbuy\Psb\Model\PsbFactory
     */
    protected $psbPsbFactory;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Paysbuy\Psb\Model\PsbFactory $psbPsbFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->psbPsbFactory = $psbPsbFactory;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _toHtml()
    {
        $psb = $this->psbPsbFactory->create();

        $form = $this->formFactory->create();
        $form->setAction($psb->getUrl())
            ->setId('psb_checkout')
            ->setName('psb_checkout')
            ->setMethod('post')
            ->setUseContainer(true);
        foreach ($psb->getCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Paysbuy in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("psb_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }//end function _toHtml
}//end class Paysbuy_Psb_Block_Redirect
