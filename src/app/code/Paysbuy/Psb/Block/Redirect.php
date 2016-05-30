<?php


class Paysbuy_Psb_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $psb = Mage::getModel('psb/psb');

        $form = new Varien_Data_Form();
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
