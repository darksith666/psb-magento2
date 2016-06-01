<?php
namespace Paysbuy\Psb\Block;



class Form extends \Magento\Payment\Block\Form
{
    protected function _construct()
    {
        $this->setTemplate('psb/form.phtml');
        parent::_construct();
    }//end function _construct
}//end class Paysbuy_Psb_Block_Form
