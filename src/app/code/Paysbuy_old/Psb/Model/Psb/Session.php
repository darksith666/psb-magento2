<?php

class Paysbuy_Psb_Model_Psb_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('psb');
    }//end function __construct
}//end class Paysbuy_Psb_Model_Psb_Session
