<?php

// Include Magento application
require_once ( "../app/Mage.php" );
umask(0);

//load Magento application base "default" folder
$app = Mage::app("default");

//Receive POSTED variables from the gateway
$result = $_POST["result"];
$status = substr($result, 0, 2);
$ref = trim(substr($_POST["result"],2));
$apCode = $_POST["apCode"];
$amt = $_POST["amt"];
$fee = $_POST["fee"];
$method = $_POST["method"];
$confirm_cs = strtolower(trim($_POST["confirm_cs"]));

//confirmation sent to the gateway to explain that the variables have been sent

$order_object = Mage::getSingleton('sales/order');
$order_object->loadByIncrementId($ref);

$dbCur = $order_object->getBaseCurrencyCode();

$dbAmt = sprintf('%.2f', $order_object->getGrandTotal());

if ($status == '00' && $dbAmt == $amt){
			$comment = "Received through Paysbuy Payment: " . $dbCur . $dbAmt;
			$order_object->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $comment, 1)->save(); 		
			$order_object->sendOrderUpdateEmail(true, $comment);
}
else if ($status == '02'){
			$comment = "Awaiting Counter Service payment";
			$order_object->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, $comment, 1)->save(); 		
			$order_object->sendOrderUpdateEmail(true, $comment);	
}
else if ($status == '99'){
			$comment = "Payment Failed";
			$order_object->setState(Mage_Sales_Model_Order::STATE_CLOSED, true, $comment, 1)->save(); 		
			$order_object->sendOrderUpdateEmail(true, $comment);
}

?>