<?php
date_default_timezone_set('Etc/UTC');

require 'PHPMailer/PHPMailerAutoload.php';
$SECURE_SECRET = "ABC87485B6826BAAAA49B7ACF2294578";
$servername = "localhost";
$username = "tickets1_root";
$password = "#megatrek55";
$dbname = "tickets1_ticketsokodb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorExists = false;

function getResponseDescription($responseCode) {

    switch ($responseCode) {
        case "0" : $result = "Transaction Successful"; break;
        case "?" : $result = "Transaction status is unknown"; break;
        case "1" : $result = "Unknown Error"; break;
        case "2" : $result = "Bank Declined Transaction"; break;
        case "3" : $result = "No Reply from Bank"; break;
        case "4" : $result = "Expired Card"; break;
        case "5" : $result = "Insufficient funds"; break;
        case "6" : $result = "Error Communicating with Bank"; break;
        case "7" : $result = "Payment Server System Error"; break;
        case "8" : $result = "Transaction Type Not Supported"; break;
        case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
        case "A" : $result = "Transaction Aborted"; break;
        case "C" : $result = "Transaction Cancelled"; break;
        case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
        case "F" : $result = "3D Secure Authentication failed"; break;
        case "I" : $result = "Card Security Code verification failed"; break;
        case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
        case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
        case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
        case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
        case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
        case "T" : $result = "Address Verification Failed"; break;
        case "U" : $result = "Card Security Code Failed"; break;
        case "V" : $result = "Address Verification and Card Security Code Failed"; break;
        default  : $result = "Unable to be determined"; 
    }
    return $result;
}


function getStatusDescription($statusResponse) {
    if ($statusResponse == "" || $statusResponse == "No Value Returned") {
        $result = "3DS not supported or there was no 3DS data provided";
    } else {
        switch ($statusResponse) {
            Case "Y"  : $result = "The cardholder was successfully authenticated."; break;
            Case "E"  : $result = "The cardholder is not enrolled."; break;
            Case "N"  : $result = "The cardholder was not verified."; break;
            Case "U"  : $result = "The cardholder's Issuer was unable to authenticate due to some system error at the Issuer."; break;
            Case "F"  : $result = "There was an error in the format of the request from the merchant."; break;
            Case "A"  : $result = "Authentication of your Merchant ID and Password to the ACS Directory Failed."; break;
            Case "D"  : $result = "Error communicating with the Directory Server."; break;
            Case "C"  : $result = "The card type is not supported for authentication."; break;
            Case "S"  : $result = "The signature on the response received from the Issuer could not be validated."; break;
            Case "P"  : $result = "Error parsing input from Issuer."; break;
            Case "I"  : $result = "Internal Payment Server system error."; break;
            default   : $result = "Unable to be determined"; break;
        }
    }
    return $result;
}

//  -----------------------------------------------------------------------------


function addDigitalOrderField($field, $value) {
  
  if (strlen($value) == 0) return false;      // Exit the function if no $value data is provided
  if (strlen($field) == 0) return false;      // Exit the function if no $value data is provided
  
  // Add the digital order information to the data to be posted to the Payment Server
  $postData .= (($postData=="") ? "" : "&") . urlencode($field) . "=" . urlencode($value);
  
  // Add the key's value to the MD5 hash input (only used for 3 party)
  $hashinput .= $field . "=" . $value . "&";
  
  return $hashinput;
  
 }

   
// If input is null, returns string "No Value Returned", else returns input
function null2unknown($data) {
    if ($data == "") {
        return "No Value Returned";
    } else {
        return $data;
    }
} 
    


function hashAllFields($hashinput,$SECURE_SECRET) {
		$hashinput=rtrim($hashInput,"&");
		 $hashinput = strtoupper(hash_hmac('SHA256',$hashinput, pack("H*",$SECURE_SECRET)));
		return $hashinput;
	}


if(array_key_exists("vpc_SecureHash", $_GET)) {

    //$md5HashData = $SECURE_SECRET;

  foreach($_GET as $key => $value) {
if (($key!="vpc_SecureHash") && ($key != "vpc_SecureHashType") && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_")))  
{
$hashinput = addDigitalOrderField($key, $value);
//echo "$hashinput";

        }
  
	
}
//ksort($hashinput);
$secureHash = hashAllFields($hashinput,$SECURE_SECRET);

     
$hashValidated = "Valid";
     
$amount          = null2unknown($_GET["vpc_Amount"]);
$locale          = null2unknown($_GET["vpc_Locale"]);
$batchNo         = null2unknown($_GET["vpc_BatchNo"]);
$command         = null2unknown($_GET["vpc_Command"]);
$message         = null2unknown($_GET["vpc_Message"]);
$version         = null2unknown($_GET["vpc_Version"]);
$cardType        = null2unknown($_GET["vpc_Card"]);
$orderInfo       = null2unknown($_GET["vpc_OrderInfo"]);
$receiptNo       = null2unknown($_GET["vpc_ReceiptNo"]);
$merchantID      = null2unknown($_GET["vpc_Merchant"]);
$authorizeID     = null2unknown($_GET["vpc_AuthorizeId"]);
$merchTxnRef     = null2unknown($_GET["vpc_MerchTxnRef"]);
$transactionNo   = null2unknown($_GET["vpc_TransactionNo"]);
$acqResponseCode = null2unknown($_GET["vpc_AcqResponseCode"]);
$txnResponseCode = null2unknown($_GET["vpc_TxnResponseCode"]);
$amt=substr("$amount",0,-2); 

// 3-D Secure Data
/*$verType         = array_key_exists("vpc_VerType", $_GET)          ? $_GET["vpc_VerType"]          : "No Value Returned";
$verStatus       = array_key_exists("vpc_VerStatus", $_GET)        ? $_GET["vpc_VerStatus"]        : "No Value Returned";
$token           = array_key_exists("vpc_VerToken", $_GET)         ? $_GET["vpc_VerToken"]         : "No Value Returned";
$verSecurLevel   = array_key_exists("vpc_VerSecurityLevel", $_GET) ? $_GET["vpc_VerSecurityLevel"] : "No Value Returned";
$enrolled        = array_key_exists("vpc_3DSenrolled", $_GET)      ? $_GET["vpc_3DSenrolled"]      : "No Value Returned";
$xid             = array_key_exists("vpc_3DSXID", $_GET)           ? $_GET["vpc_3DSXID"]           : "No Value Returned";
$acqECI          = array_key_exists("vpc_3DSECI", $_GET)           ? $_GET["vpc_3DSECI"]           : "No Value Returned";
$authStatus      = array_key_exists("vpc_3DSstatus", $_GET)        ? $_GET["vpc_3DSstatus"]        : "No Value Returned";

*/
        
$trxResp= getResponseDescription($txnResponseCode);

 $sql1 = "SELECT fullname, cemail FROM cardtrans where orderinfo='".$orderInfo."'";
$result1 = $conn->query($sql1);
$row1 = $result1->fetch_assoc();


if (($txnResponseCode == 0) && ($message=='Approved'))

{
$status ='successful';  
$msg="Dear ".$row1['fullname'].", \n Your order ".$orderInfo." for value USD ".$amt." has successfuly been processed. Your Transaction reference number is ".$receiptNo." \n This charge will appear on your credit/debit card statement as TicketSoko Purchase \n Thank you ";
}

else 
{
  //echo "$trxResp";
$status ='failed';  
$msg="Dear ".$row1['fullname'].",\n Your order ".$orderinfo." for value USD ".$amt." has Failed. Your Transaction number is ".$transactionNo." Message from Bank is ".$message.". \n Thank you";

}

$upddate1=date('YmdHis');

$upddate=date('Y-m-d H:i:s');
$sql = "UPDATE cardtrans SET amount = '".$amt."', locale = '".$locale."', batchNo = '".$batchNo."', command = '".$command."', message = '".$message."', version = '".$version."', cardType = '".$cardType."', receiptNo = '".$receiptNo."', merchantID = '".$merchantID."', authorizeID = '".$authorizeID."', merchTxnRef = '".$merchTxnRef."', transactionNo = '".$transactionNo."', acqResponseCode = '".$acqResponseCode."', txnResponseCode = '".$txnResponseCode."', verType = '".$verType."', verStatus = '".$verStatus."', token = '".$token."', verSecurLevel = '".$verSecurLevel."', enrolled = '".$enrolled."', xid = '".$xid."', acqECI = '".$acqECI."', authStatus = '".$authStatus."', finstatus = '".$status."', upddate = '".$upddate."' WHERE cardtrans.orderinfo = '".$orderInfo."'";

if($status='successfull'){
$mystatus=="Y";
$sql2 = "INSERT INTO successfull_card_transactions (phone_number,amount,riffle_number)
VALUES ('cardpayment','".$amt."', '".$orderInfo."')";

$conn->query($sql2);

if ($conn->query($sq2) === TRUE) {
    //echo "New record created successfully";
} else {
   // echo "Error: " . $sql . "<br>" . $conn->error;
}
}
if($status='failed'){
$mystatus=="F";
}
 $sql1 = "INSERT INTO cardtransactions (tid,TransType,TransID, TransTime, TransAmount,BillRefNumber,fname,status)
VALUES ('','Card','".$transactionNo."', '".$upddate1."', '".$amt."', '".$orderInfo."', '".$fullname."', '".$mystatus."')";

$conn->query($sql1);

if ($conn->query($sql) === TRUE) {
    //echo "New record created successfully";
} else {
   // echo "Error: " . $sql . "<br>" . $conn->error;
}

$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPSecure = 'tls';
$mail->SMTPAuth = true;
$mail->Username = "admin@nouveta.tech";
$mail->Password = "!Login123.!@#";
$mail->setFrom('admin@nouveta.tech', 'Nouveta');
$mail->addReplyTo('noreply@nouveta.tech', 'Nouveta');
$mail->addAddress(''.$row1['cemail'].'', ''.$row1['fullname'].'');
$mail->Subject = 'Laibon Raffle Payment';
$mail->Body = $msg;

if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    //echo $message;
// echo "You Payment is Successful. Transaction/Receipt No ".$transactionNo." \nThis charge will appear on your credit/debit card statement as Laibon Raffle Payment \n\n Thanks You";
 header("location: index.html");
}


$conn->close();
   
} 



else 
{
    // Secure Hash was not validated, add a data field to be displayed later.
    $hashValidated = "Invalid";
    echo"All parameteres---- Negative";
}






?>
