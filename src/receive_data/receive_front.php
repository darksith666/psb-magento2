<?PHP

//Receive POSTED variables from the gateway
$result = $_POST["result"];
$status = substr($result, 0, 2);
$ref = trim(substr($_POST["result"],2));
$apCode = $_POST["apCode"];
$amt = $_POST["amt"];
$fee = $_POST["fee"];
$method = $_POST["method"];
$confirm_cs = strtolower(trim($_POST["confirm_cs"]));
$curl = curPageURL();
$url = str_replace("/receive_data/receive_front.php","",$curl);

if($result == '')
{
	header( 'Location: '.$url ) ;
}

if ($status == '00'){
	$textheader = "Your order has been received.";
	$text_result = "Thank you for your purchase!";
			
}
else if ($status == '02'){
	$textheader = "Your order has been received.";
	$text_result = "Thank you for your order!";
}
else if ($status == '99'){
	$textheader = "Order Canceled!";
	$text_result = "An error occurred in the process of payment";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Payment Result</title>
</head>
<body>
<div align="center">
<h1><?=$textheader?></h1>
<h2><?=$text_result?></h2>
<p>Your order # is: <?=$ref?>.</p>
<p><button type="button" class="button" title="Continue Shopping" onclick="window.location='<?=$url?>'"><span><span>Continue Shopping</span></span></button></p>
</div>
</body>
</html>

<script type="text/javascript">
<?php
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
?>
</script>