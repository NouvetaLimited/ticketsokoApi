<?php

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

function get_card_type($card_number)
	{
		$regVisa = "/^4[0-9]{12}(?:[0-9]{3})?$/";
		$regMaster = "/^5[1-5][0-9]{14}$/";
		$regExpress = "/^3[47][0-9]{13}$/";
		$regDiners = "/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/";
		$regDiscover = "/^6(?:011|5[0-9]{2})[0-9]{12}$/";
		$regJSB = "/^(?:2131|1800|35\\d{3})\\d{11}$/";

		if (preg_match($regVisa,$card_number))
		{
			return "Visa";				
		}
		else if (preg_match($regMaster,$card_number))
		{
			return "Mastercard";
		}
		else if (preg_match($regExpress ,$card_number))
		{
			return "a Card";
		}
		else if (preg_match($regDiners,$card_number))
		{
			return "a Card";
		}
		else if (preg_match($regDiscover,$card_number))
		{
			return "a Card";
		}
		else if (preg_match($regJSB ,$card_number))
		{
			return "a Card";
		}
		else
		{
			return "Visa";
		}
	}

$vpc_SecureHash= 'ABC87485B6826BAAAA49B7ACF2294578  ';
$vpc_AccessCode= 'AE7AC063';
$vpc_Merchant= '61051002';

//$cardExp= $_POST['CVV'];
//$orderInfo = $_POST['orderInfo'];
//$cardNumber = $_POST['cardNO'];
//$cardExp = $_POST['EXP'];
//$amount = $_POST['amount'];

$cardCvv= $_GET['cvv'];
$orderInfo = mt_rand(1,2000);
$cardNumber = $_GET['cardno'];
$cardExp = $_GET['ccexpmonth'].substr($_GET['ccexpyear'], -2);
$amount=$_GET['amount'];;
$emailadd=$_GET['email'];
$fullname=$_GET['names'];

if (strpos($amount, '.') !== FALSE)
{ 
$amount=test_input($amount);
$vpc_Amount = $amount;
$vpc_Amount = str_replace(".","",$amount);;
}
else
{
$vpc_Amount = $amount."00";
}
    


$cardExp=test_input($cardExp);
$orderInfo=test_input($orderInfo);
$cardNumber=test_input($cardNumber);
$cardCvv=test_input($cardCvv);
$cardExpiryYear= substr("$cardExp", -2); 
$cardExpiryMonth= substr("$cardExp",0,2); 
$cardExp = "$cardExpiryYear$cardExpiryMonth";
$vpc_card = get_card_type($cardNumber) ;  
$orderinf = "$orderInfo";
$testarr = array(
"vpc_AccessCode" => $vpc_AccessCode,
"vpc_Amount" => $vpc_Amount,
"vpc_card" => $vpc_card,
"vpc_CardExp" =>$cardExp,
"vpc_CardNum" => $cardNumber ,
"vpc_CardSecurityCode" =>$cardCvv,
"vpc_Command"=> 'pay',
"vpc_gateway" => 'ssl',
"vpc_Locale"=> 'en',
"vpc_MerchTxnRef"=> $orderinf, 
"vpc_Merchant"=> $vpc_Merchant,
"vpc_OrderInfo"=> $orderInfo,
"vpc_ReturnURL"=> 'http://ticketsoko.nouveta.co.ke/api/cardpayments/nouvetaReturn.php',
"vpc_Version"=> '1');

// *********************
// START OF MAIN PROGRAM
// *********************

$SECURE_SECRET = $vpc_SecureHash;
$vpcURL = 'https://migs.mastercard.com.au/vpcpay';
$md5HashData = $SECURE_SECRET;
ksort ($testarr);
$appendAmp = 0;


$servername = "localhost";
$username = "tickets1_root";
$password = "#megatrek55";
$dbname = "tickets1_ticketsokodb";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$sql = "INSERT INTO cardtrans (fullname,cemail, orderinfo)
VALUES ('".$fullname."','".$emailadd."', '".$orderinf."')";

if ($conn->query($sql) === TRUE) {
    //echo "New record created successfully";
} else {
   // echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="no-store, no-cache, must-revalidate">
<META HTTP-EQUIV="PRAGMA" CONTENT="no-store, no-cache, must-revalidate">
<style>
body{font: 14px "Open Sans", serif; background-color: #fdfdfd; float: left; height: 100%; position: relative; width: 100%; background-color: #e3e3e4;}
*{margin: 0; padding: 0; box-sizing: border-box;}
:focus{outline: none;}
a{text-decoration: none;}
li{list-style: none;}
h1{font-size: 24px; font-weight: 600; margin: 20px 0 10px; text-align: center;}
.container{margin: 0px 0px; max-width: 100%;}

/*= wizard css =*/
.payment-wizard{float: left; width: 100%;}
.payment-wizard li.active{position: relative; z-index: 1;}
.wizard-heading{float: left; width: 100%; padding: 10px 15px; background-color: #fafafa; margin-bottom: 1px; box-sizing: border-box; font-size: 18px; color: #4c4c4c; text-transform: uppercase; transition: 0.3s;}
.wizard-content{display: none; float: left; width: 100%; background-color: #fff; box-shadow: 0 8px 8px #d2d2d2; padding: 15px; box-sizing: border-box;}
li:first-child .wizard-content{display: block;}
.wizard-content p{margin-bottom: 15px; font-size: 15px; line-height: 26px; color: #4c4c4c;}
.btn-green{color: #fff; float: right; border: 0; padding: 7px 10px; min-width: 92px; z-index: 1; cursor: pointer; font-size: 14px; text-transform: uppercase; background-color: #5fba57; border-radius: 3px; border-bottom: 3px solid #289422; position: relative; transition: 0.3s;}
.btn-green:before{content: ""; width: 100%; height: 0; border-radius: 3px; z-index: -1; position: absolute; left: 0; bottom: 0; background-color: #289422; transition: 0.3s;}
.btn-green:hover:before{height: 100%;}
.wizard-heading span{float: right; background-image: url(wizard-icons.png); background-repeat: no-repeat;}
.icon-user{width: 20px; height: 18px; background-position: 0 -40px; margin-top: 4px;}
.icon-location{width: 15px; height: 20px; background-position: -22px -42px; margin-top: 4px;}
.icon-summary{width: 20px; height: 20px; background-position: -39px -42px; margin-top: 4px;}
.icon-mode{width: 20px; height: 16px; background-position: -61px -34px; margin-top: 6px;}
.active .wizard-heading{
	background-color: #FF6600;
	color: #fff;
	margin-bottom: 0;
}
.active .icon-user{background-position: 0 0;}
.active .icon-location{background-position: -22px 0;}
.active .icon-summary{background-position: -39px 0;}
.active .icon-mode{background-position: -61px 0;}
.completed .wizard-heading{color: #447294; position: relative; padding: 10px 15px 10px 36px; cursor: pointer; transition: 0.3s;}
.completed .wizard-heading:before{content: "✓"; color: #fff; text-align: center; font-size: 15px; font-weight: bold; position: absolute; left: -7px; top: 8px; width: 32px; padding: 4px 0; background-color: #447294; z-index: 99;}
.completed .wizard-heading:after{content: ""; position: absolute; top: 38px; left: -7px; border-left: 7px solid transparent; border-top: 5px solid #001e34;}
.completed .icon-user{background-position: 0 -20px;}
.completed .icon-location{background-position: -22px -21px;}
.completed .icon-summary{background-position: -39px -21px;}
.completed .icon-mode{background-position: -61px -17px;}
/*= wizard end =*/
</style>

<link href="css/style.css" rel="stylesheet" type="text/css" media="screen">
<!-- -----------------------------------------------------------------------------------------
# Currently the body tag was set so that form will be sent automatically.
# 
# Merchants are welcome to NOT have this page being sent automatically. They may use this page
# to display a confirmation message, with a submit/confirm button for the cardholders to press.
# Beware that you will need to include the submit button to your hash also.
------------------------------------------------------------------------------------------- -->
<body onload="document.order.submit()">
<!--body-->
	<form name="order" action="<?=$vpcURL?>" method="post">
	<p> <h2>Please wait your payment is processed...</></p>
<?php	
	foreach($testarr
as $key => $value) {
if (strlen($value) > 0) {
?>
<input type="hidden" name="<?php echo($key); ?>" value="<?php echo($value); ?>"/><br>
<?php
if ((strlen($value) > 0)
&& ((substr($key, 0,4)=="vpc_") || (substr($key,0,5)
=="user_"))) {
$hashinput .= $key ."=" . $value . "&";
}
}
}
$hashinput = rtrim($hashinput, "&");
?>

<input type="hidden" name="vpc_SecureHash" value="<?php echo(strtoupper(hash_hmac('SHA256', $hashinput, pack('H*',$SECURE_SECRET))));
?>"/>
<input
type="hidden" name="vpc_SecureHashType"
value="SHA256">
        <input type="submit" value="Submit"/>
	</form>
</body>
</html>

