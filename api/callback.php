<?php
/**
 * Created by PhpStorm.
 * User: AlexBoey
 * Date: 4/24/2017
 * Time: 5:55 PM
 */

include 'includes/DB.php';
include 'helpers/Response.php';
include 'helpers/ConfirmationCode.php';
include "helpers/AfricasTalkingGateway.php";
include 'helpers/fpdf/fpdf.php';

date_default_timezone_set("Africa/Nairobi");
header("Access-Control-Allow-Origin: *");

$order_number = $_POST['AccountReference'];
$phone_number = formatPhoneNumber( $_POST['PhoneNumber']);
$amount_paid = $_POST['Amount'];
$code = $_POST['MpesaReceiptNumber'];
$payment_method = "MPESA";

if(strlen($code)>11){
    exit();   
}else{
    if($code=="FAILED"){
        exit();
    }else{
        tickets($phone_number,$amount_paid,$code,$order_number,$payment_method);
    }
}
function sendEmail($replyTo,$to,$subject,$message ){

    $headers = 'From: ' . "\r\n" .
        'Reply-To: '.$replyTo . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);
}


function tickets($phone_number,$amount_paid,$code,$order_number,$payment_method){
    $sql ="INSERT INTO `transactions`(`phone_number`, `amount_paid`, `code`, `order_number`, `payment_method`)
                       VALUES ('$phone_number','$amount_paid','$code','$order_number','$payment_method')";

    $result = DB::instance()->executeSQL($sql);
    if($result){

        $sql ="SELECT * FROM `ticket_sales` WHERE `order_number`='$order_number'";
        $result = DB::instance()->executeSQL($sql);
        if($result){
            $paidValue =accountBalance($order_number);
            $expectedValue = $result->fetch_assoc()['amount'];
            if($paidValue>=$expectedValue){
                //update the status of the tickets sales
                $sql = "UPDATE `ticket_sales` SET `paid` ='1' WHERE `order_number`='$order_number'";
                $result = DB::instance()->executeSQL($sql);
                //update the status of the ticket
                $sql="UPDATE `tickets` SET `paid`='1' WHERE `order_number`='$order_number' ";
                $result = DB::instance()->executeSQL($sql);
                //send tickets
                $sql ="SELECT * FROM `tickets` WHERE `order_number` ='$order_number'";
                $result = DB::instance()->executeSQL($sql);
                while ($row =$result->fetch_assoc()){
                    $ticket_number = $row['ticket_number'];
                    $email=$row['email'];
                    $link = "Follow link to download your ticket https://ticketsold.ticketsoko.com/ticket.html?ticket_number=$ticket_number";
                    $subject='ticket';
                    sendSMS($phone_number,$link);
                    send($phone_number,$link);
                    sendEmail('noreply@ticketsoko.com',$email,$subject,$link);
                }

                $response = new Response();
                $response->status = Response::STATUS_SUCCESS;
                $response->message = "Tickets Sent";
                $response->success = true;
                echo json_encode($response);
                exit();


            }else{
                $balance = $expectedValue-$paidValue;
                $data="Please complete your payment in order to receive your ticket(s) Paid KES. $paidValue expected KES.$expectedValue . Balance KES.$balance";
                $subject="complete your payment";
               // sendSMS($phone_number,"Please complete your payment in order to receive your ticket(s) Paid KES. $paidValue expected KES.$expectedValue . Balance KES.$balance");
               send($phone,$data);
                sendEmail('noreply@ticketsoko.com',$email,$subject,$link);
                $response = new Response();
                $response->status = Response::STATUS_SUCCESS;
                $response->message = "Tickets  not Sent Pay full the amount";
                $response->success = false;
                echo json_encode($response);
                exit();
            }
        }
    }

}

function confirmatioCode(){
    $ConfCode = new ConfirmationCode;
    $code = $ConfCode->auto(4);
    return $code;
}


function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^\dxX]/', '', $phoneNumber);
    $phoneNumber = preg_replace('/^0/','254',$phoneNumber);

    $phoneNumber = $phone = preg_replace('/\D+/', '', $phoneNumber);

    return $phoneNumber;
}


function sendSMS($phoneNumber,$message){
    $username   = "Nouveta";
    $apikey     = "59dc7ed72f3ff0259259b13408a6a4c82ec02d4518ac23617cbfe4b7d11e2a6a";

    $gateway    = new AfricasTalkingGateway($username, $apikey);
    try
    {
        // Thats it, hit send and we'll take care of the rest.
        $results = $gateway->sendMessage($phoneNumber, $message,'NOUVETA');

        foreach($results as $result) {
            /* // status is either "Success" or "error message"
             echo " Number: " .$result->number;
             echo " Status: " .$result->status;
             echo " MessageId: " .$result->messageId;
             echo " Cost: "   .$result->cost."\n";*/
        }
    }
    catch ( AfricasTalkingGatewayException $e )
    {
        //  echo "Encountered an error while sending: ".$e->getMessage();
    }

}

function accountBalance($order_number) {

    $sqlTotal = "SELECT SUM(amount_paid)
                       FROM `transactions`
                       WHERE order_number = '$order_number'";

    $results = DB::instance()->executeSQL($sqlTotal);

    if ($results)
        return $results->fetch_array()[0];

}


function send($phone,$text){
$username = 'info@nouveta.tech';
$password = 'Tech.N0uv3t4.2018';


$header = "Basic " . base64_encode($username . ":" . $password);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.infobip.com/sms/1/text/single",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{ \"from\":\"NOUVETA\", \"to\":\"$phone\", \"text\":\"$text\" }",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "authorization: $header",
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
}
