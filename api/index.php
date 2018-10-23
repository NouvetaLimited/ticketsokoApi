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
include "helpers/PHPMailer.php";

date_default_timezone_set("Africa/Nairobi");
header("Access-Control-Allow-Origin: *");

if(!empty($_POST['function']))
    $function = $_POST['function'];

if(!empty($_GET['function']))
    $function = $_GET['function'];

if(!empty($_POST['TransactionType']))
    $function = $_POST['TransactionType'];




function accountBalance($order_number) {

    $sqlTotal = "SELECT SUM(amount_paid)
                       FROM `transactions`
                       WHERE order_number = '$order_number'";

    $results = DB::instance()->executeSQL($sqlTotal);

    if ($results)
        return $results->fetch_array()[0];

}

function getTotalDayUnValidatedTickets($event_company,$id)
{
    $new_array = null;

    $sql ="SELECT day, COUNT(*) c FROM options WHERE `event_id`='$id'  HAVING c > 1 ;";
    $res = DB::instance()->executeSQL($sql);
    while ($row = $res->fetch_assoc()) {
        $new_array[] = $row['day']." : " .getTotalDayUnValidatedTicket($event_company,$row['day']);
    }

    return $new_array;
}
function getTotalValidatedTickets($event_company,$id) {


    $new_array = null;

    $sql ="SELECT day, COUNT(*) c FROM options WHERE `event_id`='$id'  HAVING c > 1 ;";
    $res = DB::instance()->executeSQL($sql);
    while ($row = $res->fetch_assoc()) {
        $new_array[] = $row['day']." : " .getTotalDayValidatedTickets($event_company,$row['day']);
    }

    return $new_array;


}
function sendEmail($replyTo,$to,$subject,$message ){

    $headers = 'From: ' . "\r\n" .
        'Reply-To: '.$replyTo . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);
}

function totalTicketsSalesAmount($event_company) {


    if (!empty($_POST['dateTo']) && !empty($_POST['dateFrom'])) {
        $dateTo = date('Y-m-d 23:59:59', strtotime($_POST['dateTo']));
        $dateFrom = date('Y-m-d 00:00:00 ', strtotime($_POST['dateFrom']));

        $sqlTotal = "SELECT SUM(amount)
                       FROM `ticket_sales`
                       WHERE event_company = '$event_company'  AND `date` BETWEEN '$dateFrom' AND  '$dateTo'";

        $sqlTotalPaid = "SELECT SUM(amount)
                       FROM `ticket_sales`
                       WHERE event_company = '$event_company' AND `paid`='1'  AND `date` BETWEEN '$dateFrom' AND  '$dateTo'";

        $sqlTotalUnPaid = "SELECT SUM(amount)
                       FROM `ticket_sales`
                       WHERE event_company = '$event_company' AND `paid`='0'  AND `date` BETWEEN '$dateFrom' AND  '$dateTo'";

    } else {

        $sqlTotal = "SELECT SUM(amount)
                       FROM `ticket_sales`
                       WHERE event_company = '$event_company'";

        $sqlTotalPaid = "SELECT SUM(amount)
                       FROM `ticket_sales`
                       WHERE event_company = '$event_company' AND `paid`='1'";

        $sqlTotalUnPaid = "SELECT SUM(amount)
                       FROM `ticket_sales`
                       WHERE event_company = '$event_company' AND `paid`='0'";

    }



    $total = DB::instance()->executeSQL($sqlTotal)->fetch_array()[0];

    $totalPaid = DB::instance()->executeSQL($sqlTotalPaid)->fetch_array()[0];

    $totalUnPaid = DB::instance()->executeSQL($sqlTotalUnPaid)->fetch_array()[0];

    $new_array = array("total"=>$total,"paid"=>$totalPaid,"unpaid"=>$totalUnPaid);
    return  $new_array;

}


if($function=="getTotalSalesTicketsValidated"){

    $event_company = $_POST['keyword'];

    $sql = "SELECT * FROM `events` WHERE `event_company`='$event_company'";
    $result = DB::instance()->executeSQL($sql);

    if($result){
        $id = $result->fetch_assoc()['id'];//Event id
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "totalTicketsSalesAmount : totalTicketsQuantity : getTotalValidatedTickets";
        $response->data =  array("totalTicketsSalesAmount"=> totalTicketsSalesAmount($event_company),"getTotalUnValidatedTickets"=>getTotalDayUnValidatedTickets($event_company,$id),"getTotalValidatedTickets"=>getTotalValidatedTickets($event_company,$id),'getTotalPerDay'=>getEventsDays($id)); ;
        $response->success = true;
        echo json_encode($response);
        exit();
    }

}

if ($function =="addEvents"){

    //GET IMAGE AND SAVE TO THE FOLDER UPLOADS
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message= "File is not an image.";
            $response->success = false;
            echo json_encode($response);
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Sorry, file already exists.";
        $response->success = false;
        echo json_encode($response);
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Sorry, your file is too large.";
        $response->success = false;
        echo json_encode($response);
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Sorry, your Images was not uploaded.";
        $response->success = false;
        echo json_encode($response);
        exit();
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {


            $event_name = $_POST['event_name'];
            $event_description = $_POST['event_description'];
            $event_date   = $_POST['event_date'];
            $event_venue = $_POST['event_venue'];
            $event_company = $_POST['event_company'];
            $event_image = "https://ticketsold.ticketsoko.com/api/uploads/".basename( $_FILES["fileToUpload"]["name"]);
            $event_ticket_no = $_POST['event_ticket_no'];
            $paybill = $_POST['paybill'];
            $status = '0';

            $sql ="INSERT INTO `events`(`event_name`, `event_description`, `event_date`, `event_venue`, `event_company`, `event_image`, `event_ticket_no`, `paybill`, `status`) 
                      VALUES ('$event_name', '$event_description', '$event_date', '$event_venue', '$event_company', '$event_image', '$event_ticket_no', '$paybill','$status')";

            $result = DB::instance()->executeSQL($sql);
            if($result){
                $response = new Response();
                $response->status = Response::STATUS_SUCCESS;
                $response->message = "Event added Successfully";
                $response->success = true;
                echo json_encode($response);
                exit();

            }else{
                $response = new Response();
                $response->status = Response::STATUS_SUCCESS;
                $response->message= "Fail to add events";
                $response->success = false;
                echo json_encode($response);
                exit();
            }

        } else {

            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message= "Sorry, there was an error uploading your file.";
            $response->success = false;
            echo json_encode($response);
            exit();
        }
    }

}
if($function=="deleteEvent"){
    $event_id = $_POST['event_id'];

    $sql ="DELETE FROM `events` WHERE `id` ='$event_id'";
    $result = DB::instance()->executeSQL($sql);

    if($result){
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Event deleted";
        $response->success = false;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Failed to delete";
        $response->success = false;
        echo json_encode($response);
        exit();
    }
}if($function=="login"){
    $user_name = $_POST['user_name'];
    $password= $_POST['password'];

    $sql ="SELECT * FROM `users` WHERE `user_name` ='$user_name'AND `password` ='$password'";
    $result = DB::instance()->executeSQL($sql);


    if($result->num_rows >0){
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Login Success";
        $response->data = $result->fetch_assoc();
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Invalid credentials ";
        $response->success = false;
        echo json_encode($response);
        exit();
    }
}
if ($function =="getEvents"){

    if(!empty($_REQUEST["keyword"])) {
        $keWord = $_REQUEST["keyword"];
        $sql ="SELECT * FROM `events` WHERE `status`='0' AND `id` LIKE '%$keWord%' OR `event_company` LIKE '%$keWord%' OR `event_name` LIKE '%$keWord%' OR `paybill` LIKE '%$keWord%'";
    }else{
        $sql ="SELECT * FROM `events` WHERE `status`='0' ORDER BY id DESC ";
    }

    $result = DB::instance()->executeSQL($sql);

    if($result->num_rows > 0){

        while( $row = $result->fetch_assoc()){
            $new_array[] = array("Events"=>$row, "Options" => getTicketOptions($row['id']) ,"Merchandise" => getMerchandise($row['id']),"totalTicketsSalesAmount" => totalTicketsSalesAmount($row['event_company']));
        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $new_array;
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if($function==="checkTicketsPayments"){

    $order_number = $_POST['order_number'];

    $sql ="SELECT * FROM `ticket_sales` WHERE `order_number` ='$order_number' and `paid`='1'";

    $result = DB::instance()->executeSQL($sql);

    if($result ->num_rows >0){

        $sql ="SELECT * FROM `tickets` WHERE `order_number` ='$order_number'";
        $result = DB::instance()->executeSQL($sql);
        $res = DB::instance()->executeSQL($sql);
        while ($row = $res->fetch_assoc()) {
            $new_array[] = "Follow link to download your ticket https://www.ticketsoko.com/ticket.html?ticket_number=".$row['ticket_number'];


        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "$order_number Payment success";
        $response->data =$new_array;
        $response->success = true;
        echo json_encode($response);



    }else{

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "$order_number Waiting for payments";
        $response->success = false;
        echo json_encode($response);

    }
}
if ($function =="getPastEvent"){
    updateDaysToSalesOrder();

    $result = DB::instance()->executeSQL("SELECT * FROM `events` WHERE  `status` ='1'");

    if($result->num_rows > 0){

        while( $row = $result->fetch_assoc()){
            $new_array[] = array("Events"=>$row, "Options" => getTicketOptions($row['id']) ,"Merchandise" => getMerchandise($row['id']));
        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $new_array;
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if($function =="getOptions"){

    if(empty($_POST['id'])){
        $sql ="SELECT * FROM `options` ";
    }else{
        $id = $_POST['id'];
        $sql ="SELECT * FROM `options` WHERE `id`='$id'";
    }

    $res = DB::instance()->executeSQL($sql);
    /* while ($row = $res->fetch_assoc()){
         $new_array[] =$row;
     }*/

    $response = new Response();
    $response->status = Response::STATUS_SUCCESS;
    $response->data=$res->fetch_assoc();
    $response->success = true;
    echo json_encode($response);
    exit();

}
function getTicketOptions($id){

    $new_array = null;

    $sql ="SELECT * FROM `ticket_options` WHERE `event_id`='$id'";
    $res = DB::instance()->executeSQL($sql);
    while ($row = $res->fetch_assoc()) {
        //$new_array[] = $row;
        $new_array[] = array("Choice"=>$row, "OptionChoice" => getOptions($id));
    }

    return $new_array;

}
function getOptions($id){

    $new_array = null;

    $sql ="SELECT * FROM `options` WHERE `event_id`='$id'";
    $res = DB::instance()->executeSQL($sql);
    while ($row = $res->fetch_assoc()) {
        $new_array[] = $row;
    }

    return $new_array;

}

if($function=="cashOptions"){

    $response = new Response();
    $response->status = Response::STATUS_SUCCESS;
    $response->message= "Found";
    $response->data = getOptions("12");
    $response->success = true;
    echo json_encode($response);
}

function getEventsDays($id){

    $new_array = null;

    $sql ="SELECT day, COUNT(*) c FROM options WHERE `event_id`='$id' GROUP BY day HAVING c > 1 ;";
    $res = DB::instance()->executeSQL($sql);
    while ($row = $res->fetch_assoc()) {
        $new_array[] = $row['day']." : " .getTotalDayTickets('NATIONAL YOUTH ORCHESTRA',$row['day']);
    }

    return $new_array;

}

function getSumPerDay($id){
    $new_array = null;
    $sql ="SELECT day, COUNT(*) c FROM options WHERE `event_id`='$id' GROUP BY day HAVING c > 1 ;";
    $res = DB::instance()->executeSQL($sql);
    while ($row = $res->fetch_assoc()) {
        $new_array[] = $row['day']." : " .getTotalDayTickets('NATIONAL YOUTH ORCHESTRA',$row['day']);
    }

    return $new_array;

}

function getSumPaymentTypeDay ($day,$payment){


    $sqlTotal = "SELECT SUM(amount)
                       FROM `ticket_sales`
                       WHERE event_company = 'NATIONAL YOUTH ORCHESTRA' AND `day`='$day' AND `payment`='$payment'";

    $res = DB::instance()->executeSQL($sqlTotal);

    return $res->fetch_array()[0];
}

function updateDaysToSalesOrder(){
    $sql = "SELECT * FROM `tickets` WHERE `event_company`='NATIONAL YOUTH ORCHESTRA'";
    $res = DB::instance()->executeSQL($sql);
    while ($row = $res->fetch_assoc()) {
        $order_number = $row['order_number'];
        $day = $row['day'];
        DB::instance()->executeSQL("UPDATE `ticket_sales` SET `day`='$day' WHERE `order_number`='$order_number'");
    }

}

function getTotalDayTickets($event_company,$day){

    $new_array = null;
    $sql ="SELECT * FROM `tickets` WHERE `event_company`='$event_company'  AND `paid`='1'";
    $res = DB::instance()->executeSQL($sql);
    return $res->num_rows;

}
function getTotalDayValidatedTickets($event_company,$day){

    $new_array = null;
    $sql ="SELECT * FROM `tickets` WHERE `event_company`='$event_company' AND `day`='$day' AND `paid`='1' AND `is_validated`='1'";
    $res = DB::instance()->executeSQL($sql);
    return $res->num_rows;

}function getTotalDayUnValidatedTicket($event_company,$day){

    $new_array = null;
    $sql ="SELECT * FROM `tickets` WHERE `event_company`='$event_company'  AND `paid`='1' AND `is_validated`='0'";
    $res = DB::instance()->executeSQL($sql);
    return $res->num_rows;

}
function getMerchandise($id){

    $new_array = null;

    $sql ="SELECT * FROM `merchandize` WHERE `event_id`='$id'";
    $res = DB::instance()->executeSQL($sql);
    while ($row = $res->fetch_assoc()) {
        $new_array[] = $row;
    }

    return $new_array;

}
if ($function =="getTicketSales"){
    $event_company = $_POST['event_company'];

    if(!empty($_POST["keyword"])) {
        $keWord = $_POST["keyword"];
        $sql ="SELECT * FROM `ticket_sales` WHERE `event_company` = '%$event_company%' AND `phone_number` LIKE '%$keWord%' OR `paybill` LIKE '%$keWord%' ORDER BY id DESC ";
    }else{
        $sql ="SELECT * FROM `ticket_sales` WHERE `event_company`='$event_company' ORDER BY id DESC ";
    }

    $result = DB::instance()->executeSQL($sql);

    if($result->num_rows > 0){

        while( $row = $result->fetch_assoc()){
            $new_array[] = $row; // Inside while loop
        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $new_array;
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if ($function =="getTickets"){
    $event_company = $_POST['event_company'];

    if(!empty($_POST["keyword"])) {
        $keWord = $_POST["keyword"];
        $sql ="SELECT * FROM `tickets` WHERE `event_company` = '%$event_company%'  AND `order_number` LIKE '%$keWord%' OR `ticket_number` LIKE '%$keWord%' OR `is_validated` LIKE '%$keWord%' OR `phone_number` LIKE '%$keWord%' ORDER BY id DESC";
    }else{
        $sql ="SELECT * FROM `tickets` WHERE `event_company`='$event_company' AND `paid`='1' ORDER BY id DESC";
    }

    $result = DB::instance()->executeSQL($sql);

    if($result->num_rows > 0){

        while( $row = $result->fetch_assoc()){
            $new_array[] = $row; // Inside while loop
        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $new_array;
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if ($function =="ticketDetails"){
    $ticket_number = $_POST['ticket_number'];

    $sql ="SELECT * FROM `tickets` WHERE `ticket_number`='$ticket_number' ";
    $result = DB::instance()->executeSQL($sql);

    if($result->num_rows > 0){

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $result->fetch_assoc();
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if ($function =="getTicketOption"){

    $sql ="SELECT * FROM `ticket_options` ";

    $result = DB::instance()->executeSQL($sql);

    if($result->num_rows > 0){

        while( $row = $result->fetch_assoc()){
            $new_array[] = $row; // Inside while loop
        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $new_array;
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if($function=="getTotalTicketSalesAmount"){
    $event_company = $_POST['keyword'];

    $response = new Response();
    $response->status = Response::STATUS_SUCCESS;
    $response->message= "Total Tickets sales Amount";
    $response->data = totalTicketsSalesAmount($event_company);
    $response->success = true;
    echo json_encode($response);
    exit();

}

if($function=="getTotalTicketSalesQuantity"){
    $event_company = $_POST['keyword'];

    $response = new Response();
    $response->status = Response::STATUS_SUCCESS;
    $response->message= "Total Tickets Sales Quantity";
    $response->data = totalTicketsQuantity($event_company);
    $response->success = true;
    echo json_encode($response);
    exit();

}
if ($function =="addTicketOptions"){
    $event_id = $_POST['event_id'];
    $ticket_options_id = $_POST['ticket_options_id'];
    $seasonal = $_POST['seasonal'];
    $amount = $_POST['amount'];

    //check event_id
    $sql ="SELECT * FROM `events` WHERE `id`='$event_id'";
    $result = DB::instance()->executeSQL($sql);
    if($result->num_rows >0){
        $sql ="INSERT INTO `ticket_options`(`event_id`, `ticket_options_id`, `seasonal`, `amount`) 
                            VALUES ('$event_id','$ticket_options_id','$seasonal','$amount')";
        $result = DB::instance()->executeSQL($sql);

        if($result){
            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message= "Ticket Option Added";
            $response->success = true;
            echo json_encode($response);
            exit();

        }else{
            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message= "Not Added";
            $response->success = false;
            echo json_encode($response);
            exit();
        }
    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Invalid Event Id";
        $response->success = false;
        echo json_encode($response);
        exit();
    }


}
if ($function =="CardCheckOut"){

    $event_id_sale = $_POST['event_id'];

    if(ticketsAvailable($event_id_sale)==1){
        //REGULAR
        $OptionChoiceSelectedRegular = $_POST['OptionChoiceSelectedRegularName'];//Options for ticket selected (Regular,VIP,VVIP)
        $valueRegular = $_POST['valueRegular'];//NO. of regular ticket
        $totalRegular = $_POST['totalRegular'];//Sum of total purchased regular tickets
        $day = $_POST['day'];

        //SEASONAL
        $OptionChoiceSelectedSeasonal = $_POST['OptionChoiceSelectedSeasonal'];// Seasonal Options for ticket selected (Regular,VIP,VVIP)
        $valueSeasonal = $_POST['valueSeasonal'];//NO. of Seasonal ticket
        $totalSeasonal = $_POST['totalSeasonal'];//Sum of total purchased seasonal tickets

        //MERCHANDISE
        $valueItemNo1 = $_POST['valueItemNo1']; //5
        $valueItemNo2 = $_POST['valueItemNo2'];
        $valueItemNo3 = $_POST['valueItemNo3'];
        $valueItemNo4 = $_POST['valueItemNo4'];
        $valueItemNo5 = $_POST['valueItemNo5'];
        $totalItemNo1 = $_POST['totalItemNo1'];//3000
        $totalItemNo2 = $_POST['totalItemNo2'];
        $totalItemNo3 = $_POST['totalItemNo3'];
        $totalItemNo4 = $_POST['totalItemNo4'];
        $totalItemNo5 = $_POST['totalItemNo5'];
        $descriptionItemNo1 = $_POST['descriptionItemNo1']; // 3 Quine Hoodie KES 4500
        $descriptionItemNo2 = $_POST['descriptionItemNo2'];
        $descriptionItemNo3 = $_POST['descriptionItemNo3'];
        $descriptionItemNo4 = $_POST['descriptionItemNo4'];
        $descriptionItemNo5 = $_POST['descriptionItemNo5'];


        $totalSum = $_POST['totalSum'];//500
        $payBill = '175555';
        $event_image = $_POST['event_image'];
        $transaction_desc = $_POST['transaction_desc'];


        $event_company_sale = $_POST['event_company'];
        $phone_number_sale =formatPhoneNumber($_POST['phone_number']);

        $amount = $totalSum;
        $email=$_REQUEST['email'];
        $api_key=$_REQUEST['api_key'];

        $quantity =$valueRegular+$valueSeasonal+$valueItemNo1+$valueItemNo2+$valueItemNo3+$valueItemNo4+$valueItemNo5;
        $today = date("d");
        $rand = strtoupper(substr(uniqid(sha1(time())),0,4));
        $order_number_sale = $today . $rand;

        $sql ="INSERT INTO `ticket_sales`(`order_number`, `event_id`, `quantity`, `amount`, `event_company`, `phone_number`, `paybill`,`payment`)
                      VALUES ('$order_number_sale','$event_id_sale','$quantity','$amount','$event_company_sale','$phone_number_sale','bank','card')";

        $result = DB::instance()->executeSQL($sql);
        if($result){
            /*$data ="To pay for your ticket, please send KES $amount to paybill number $payBill Account number $order_number_sale. Once payment is confirmed,
            you will receive a link to download your ticket. If you have already paid and received your ticket, ignore this SMS.";

                pushCardPayments($amount,$phone_number_sale,$order_number_sale,$transaction_desc);*/


            //generate tickets for regular
            createTickets($valueRegular,$OptionChoiceSelectedRegular,$day);
            //generate tickets for seasonal
            createTickets($valueSeasonal,$OptionChoiceSelectedSeasonal,$day);
            //generate tickets for Merchandise
            createTickets($valueItemNo1,$descriptionItemNo1,$day);
            createTickets($valueItemNo2,$descriptionItemNo2,$day);
            createTickets($valueItemNo3,$descriptionItemNo3,$day);
            createTickets($valueItemNo4,$descriptionItemNo4,$day);
            createTickets($valueItemNo5,$descriptionItemNo5,$day);

            //sendSMS($phone_number_sale,$data);
            $cardExpiryYear = $_REQUEST['cardexpiringyear'];
            $cardExpiryMonth = $_REQUEST['cardexpiringmonth'];
            $cardNumber = $_REQUEST['cardnumber'];
            $cardCvv = $_REQUEST['cardCvv'];
            $orderInfo =$order_number_sale;
            $returnUrl='https://processor.ticketsoko.com';
            $name=$_REQUEST['name'];

//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
//            //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//            set_time_limit(0);
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => "https://processor.ticketsoko.com",
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => "",
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 30,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => "POST",
//                CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"cvv\"\r\n\r\n$cardCvv\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"phone\"\r\n\r\n$PhoneNumber\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"names\"\r\n\r\n$name\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"orderNumber\"\r\n\r\n$orderInfo\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"cardno\"\r\n\r\n$cardNumber\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"ccexpmonth\"\r\n\r\n$cardExpiryMonth\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"ccexpyear\"\r\n\r\n$cardExpiryYear\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$amount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition:form-data; name=\"returnURL\"\r\n\r\n$returnUrl\r\n
//                ",
//                CURLOPT_HTTPHEADER => array(
//                    "cache-control: no-cache",
//
//                    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
//                    "postman-token: 4fe6b48a-5c0a-e9fa-7d45-172ce8b64722"
//                ),
//            ));
//
//            $resp = curl_exec($curl);
//
//            echo $resp;
            $url="https://processor.ticketsoko.com/index.php?Amount=$amount&PhoneNumber=$phone_number_sale&TransactionDesc=$orderInfo&email=$email&name=$name&paymnentMode=card&api_key='b438d90c22f71e3b5db1cfe0e78e2f8c2fd8b7f6'&cardexpiringyear=$cardExpiryYear&cardCvv=$cardCvv&returnURL=https://processor.ticketsoko.com/index.php&cardnumber=$cardNumber&cardexpiringmonth=$cardExpiryMonth";

            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message= "failed";
            $response->data=$url;
            $response->success = false;
            echo json_encode($response);
            exit();



        }else{
            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message= "failed";
            $response->success = false;
            echo json_encode($response);
            exit();
        }


    }else{

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Tickets Sold Out";
        $response->success = true;
        echo json_encode($response);
    }

}
if ($function =="checkOut"){

    $event_id_sale = $_POST['event_id'];

    if(ticketsAvailable($event_id_sale)==1){
        //REGULAR
        $OptionChoiceSelectedRegular = $_POST['OptionChoiceSelectedRegularName'];//Options for ticket selected (Regular,VIP,VVIP)
        $valueRegular = $_POST['valueRegular'];//NO. of regular ticket
        $totalRegular = $_POST['totalRegular'];//Sum of total purchased regular tickets
        $day = $_POST['day'];

        //SEASONAL
        $OptionChoiceSelectedSeasonal = $_POST['OptionChoiceSelectedSeasonal'];// Seasonal Options for ticket selected (Regular,VIP,VVIP)
        $valueSeasonal = $_POST['valueSeasonal'];//NO. of Seasonal ticket
        $totalSeasonal = $_POST['totalSeasonal'];//Sum of total purchased seasonal tickets

        //MERCHANDISE
        $valueItemNo1 = $_POST['valueItemNo1']; //5
        $valueItemNo2 = $_POST['valueItemNo2'];
        $valueItemNo3 = $_POST['valueItemNo3'];
        $valueItemNo4 = $_POST['valueItemNo4'];
        $valueItemNo5 = $_POST['valueItemNo5'];
        $totalItemNo1 = $_POST['totalItemNo1'];//3000
        $totalItemNo2 = $_POST['totalItemNo2'];
        $totalItemNo3 = $_POST['totalItemNo3'];
        $totalItemNo4 = $_POST['totalItemNo4'];
        $totalItemNo5 = $_POST['totalItemNo5'];
        $descriptionItemNo1 = $_POST['descriptionItemNo1']; // 3 Quine Hoodie KES 4500
        $descriptionItemNo2 = $_POST['descriptionItemNo2'];
        $descriptionItemNo3 = $_POST['descriptionItemNo3'];
        $descriptionItemNo4 = $_POST['descriptionItemNo4'];
        $descriptionItemNo5 = $_POST['descriptionItemNo5'];


        $totalSum = $_POST['totalSum'];//500
        $payBill = '175555';
        $event_image = $_POST['event_image'];
        $transaction_desc = 'TICKETSOKO';


        $event_company_sale = $_POST['event_company'];
        $phone_number_sale =formatPhoneNumber($_POST['phone_number']);

        $tentedsingle=$_POST['single'];
        $singlenight=$_POST['singleNight'];
        $tenteddouble=$_POST['double'];
        $doubleNight=$_POST['doubleNight'];
        $self=$_POST['self'];
        $selfNight=$_POST['selfNight'];
        $selfFood=$_POST['selfFood'];

        $amount = $totalSum;

        $quantity ='';
        $today = date("d");
        $rand = strtoupper(substr(uniqid(sha1(time())),0,4));
        $order_number_sale = $today . $rand;

        $sql ="INSERT INTO `ticket_sales`(`order_number`, `event_id`, `quantity`, `amount`, `event_company`, `phone_number`, `paybill`)
                      VALUES ('$order_number_sale','$event_id_sale','$quantity','$amount','$event_company_sale','$phone_number_sale','$payBill')";

        $result = DB::instance()->executeSQL($sql);
        if($result){
            /*$sql2="INSERT INTO `personalData`(`ID`,`orderNumber`) VALUES ('$order_number_sale','$order_number_sale')";
            $result2 = DB::instance()->executeSQL($sql2);*/
            $sql1="INSERT INTO `accomodation`(`telephoneNumber`, `tentedsingle`, `tenteddouble`, `selffood`, `selfcaterd`, `orderNumber`, `singleNight`, `DoubleNight`, `selfNight`)
                  VALUES ('$phone_number_sale','$tentedsingle','$tenteddouble','$selfFood','$self','$order_number_sale','$singlenight','$doubleNight','$selfNight')";
            $result1 = DB::instance()->executeSQL($sql1);

            $data ="To pay for your ticket, please send KES $amount to paybill number $payBill Account number $order_number_sale. Once payment is confirmed,you will receive a link to download your ticket. If you have already paid and received your ticket, ignore this email.";
            $email=$_REQUEST['email'];
            $subject="ORDER confirmation";
            $text="To pay for your ticket, please send KES $amount to paybill number $payBill Account number $order_number_sale. Once payment is confirmed,you will receive a link to download your ticket. If you have already paid and received your ticket, ignore this sms.";

            pushPayments($amount,$phone_number_sale,$order_number_sale,$transaction_desc,$payBill);

            //generate tickets for regular
            createTickets($valueRegular,$OptionChoiceSelectedRegular,$day,$email);
            //generate tickets for seasonal
            createTickets($valueSeasonal,$OptionChoiceSelectedSeasonal,$day,$email);
            //generate tickets for Merchandise
            createTickets($valueItemNo1,$descriptionItemNo1,$day,$email);
            createTickets($valueItemNo2,$descriptionItemNo2,$day,$email);
            createTickets($valueItemNo3,$descriptionItemNo3,$day,$email);
            createTickets($valueItemNo4,$descriptionItemNo4,$day,$email);
            createTickets($valueItemNo5,$descriptionItemNo5,$day,$email);
             send($phone_number_sale,$text);
            sendSMS($phone_number_sale,$data);
            sendEmail('noreply@ticketsoko.com',$email,$subject,$data);
            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message= "To pay for your ticket, please send KES $amount to paybill number $payBill Account number $order_number_sale. Once payment is confirmed,you will receive a link to download your ticket";
            $response->data =$order_number_sale;
            $response->success = true;
            echo json_encode($response);
            exit();



        }else{
            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message= "failed";
            $response->success = false;
            echo json_encode($response);
            exit();
        }


    }else{

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Tickets Sold Out";
        $response->success = true;
        echo json_encode($response);
    }

}

if($function=="validateTicket"){
    $ticket_number = $_POST['ticket_number'];
    validateTicket($ticket_number);
}

if($function =="deleteMerchandise"){
    $id = $_POST['id'];
    $result = DB::instance()->executeSQL("DELETE FROM `merchandize` WHERE `id`='$id'");
    if($result){
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Merchandise deleted";
        $response->success = true;
        echo json_encode($response);
    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Failed to delete";
        $response->success = false;
        echo json_encode($response);
    }

}

if($function =="addMerchandise"){
    $event_id = $_POST['event_id'];
    $price = $_POST['price'];
    $item_no = $_POST['item_no'];
    $product = $_POST['product'];

    $result = DB::instance()->executeSQL("INSERT INTO `merchandize`(`event_id`, `product`, `price`, `item_no`) VALUES ('$event_id','$product','$price','$item_no')");
    if($result){
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Merchandise added";
        $response->success = true;
        echo json_encode($response);
    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "Failed to add";
        $response->success = false;
        echo json_encode($response);
    }

}

function ticketsAvailable($event_id){

    $sql = "SELECT * FROM `events` WHERE `id` ='$event_id'";
    $result = DB::instance()->executeSQL($sql);
    $limit = $result->fetch_assoc()['event_ticket_no'];
    if($result){
        $sql = "SELECT * FROM `tickets` WHERE `event_id` ='$event_id'";

        $result = DB::instance()->executeSQL($sql);
        if($result->num_rows <=$limit){
            return 1;
        }else{
            DB::instance()->executeSQL("UPDATE `events` SET `status`='1'  WHERE `id` ='$event_id'");
            return 0;
        }
    }

}

function createTickets($value,$description,$day,$email){
    while ($value>0){

        $ticket_number = confirmatioCode();

        $order_number = $GLOBALS['order_number_sale'];
        $event_id =  $GLOBALS['event_id_sale'];
        $phone_number =  $GLOBALS['phone_number_sale'];
        $event_company =  $GLOBALS['event_company_sale'];
        $event_image =  $GLOBALS['event_image'];

        $sql ="INSERT INTO `tickets`(`order_number`, `event_id`, `ticket_number`, `is_validated`, `description`, `phone_number`, `event_company`,`event_image`,`day`,`email`) VALUES 
                          ('$order_number','$event_id','$ticket_number','0','$description','$phone_number','$event_company','$event_image','$day','$email')";
        DB::instance()->executeSQL($sql);

        $value--;
    }
}

if($function=="cashTicket"){

    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $day = $_POST['day'];
    $phone_number=$_POST['phoneNumber'];
    $email=$_POST['email'];

    $today = date("d");
    $rand = strtoupper(substr(uniqid(sha1(time())),0,4));
    $order_number_sale = $today . $rand;

    $sql ="INSERT INTO `ticket_sales`(`order_number`, `event_id`, `quantity`, `amount`, `event_company`, `phone_number`, `paybill`,`paid`,`email`)
                      VALUES ('$order_number_sale','15','1','0','Creative Business Summit','$phone_number','175555','1','$email')";

    $ticket_number = confirmatioCode();

    $result = DB::instance()->executeSQL($sql);
    if($result){
        $sql ="INSERT INTO `tickets`(`order_number`, `event_id`, `ticket_number`, `is_validated`, `description`, `phone_number`, `event_company`,`event_image`,`day`,`paid`,`email`) VALUES 
                          ('$order_number_sale','10','$ticket_number','0','complimentary','$phone_number','Creative Business Africa Summit 2018','https://ticketsoko.com/nairobisummit.png','all','1','$email')";
        $res=  DB::instance()->executeSQL($sql);
        
        $link = "Follow link to download your Creative Business Africa Summit 2018 ticket https://ticketsold.ticketsoko.com/ticket.html?ticket_number=$ticket_number";
        $subject='Creative Business Africa Summit 2018';
        send($phone_number,$link);

        if($res){
            $sql  = "SELECT * FROM `tickets` WHERE `ticket_number`='$ticket_number' ";
            sendEmail('noreply@ticketsoko.com',$email,$subject,$link);

            $result = DB::instance()->executeSQL($sql);

            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message = "CASH SUCCESS";
            $response->data = $result->fetch_assoc();
            $response->success = true;
            echo json_encode($response);
        }
    }

}

function validateTicket($ticket_number){
    $sql  = "SELECT * FROM `tickets` WHERE `ticket_number`='$ticket_number' AND `is_validated`='0'";

    $result = DB::instance()->executeSQL($sql);

    if($result->num_rows >0){
        $sql = "UPDATE `tickets` SET `is_validated` ='1' WHERE `ticket_number`='$ticket_number'";
        $res = DB::instance()->executeSQL($sql);
        if($res){
            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->data = $result->fetch_assoc();
            $response->message = "Ticket Validated Successfully";
            $response->success = true;
            echo json_encode($response);
        }else{
            $response = new Response();
            $response->status = Response::STATUS_SUCCESS;
            $response->message = "Failed to Validate";
            $response->success = false;
            echo json_encode($response);

        }

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message = "Invalid Ticket";
        $response->success = false;
        echo json_encode($response);

    }


}

if ($function =="paymentCallBack") {
    $order_number = $_POST['order_number'];
    $phone_number = $_POST['phone_number'];
    $amount_paid = $_POST['amount_paid'];
    $code = $_POST['code'];
    $payment_method = $_POST['payment_method'];



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
                //update the status of the tickets

                DB::instance()->executeSQL("UPDATE `ticket_sales` SET `paid` ='1' WHERE `order_number`='$order_number'");
                DB::instance()->executeSQL("UPDATE `tickets` SET `paid` ='1' WHERE `order_number`='$order_number'");
                //send tickets
                $sql ="SELECT * FROM `tickets` WHERE `order_number` ='$order_number'";
                $result = DB::instance()->executeSQL($sql);
                while ($row =$result->fetch_assoc()){
                    $ticket_number = $row['ticket_number'];
                    $link = "Follow link to download your  ticket https://ticketsoko.nouveta.co.ke/ticket.html?ticket_number=$ticket_number";
                    sendSMS($phone_number,$link);
                }

                $response = new Response();
                $response->status = Response::STATUS_SUCCESS;
                $response->message = "Tickets Sent";
                $response->success = true;
                echo json_encode($response);
                exit();


            }else{
                $balance = $expectedValue-$paidValue;
                sendSMS($phone_number,"Please complete your payment in order to receive your ticket(s) Paid KES. $paidValue expected KES.$expectedValue . Balance KES.$balance");
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

if ($function =="downloadTicket"){
    require 'pdfcrowd.php';
    if(empty($_POST['ticket_number'])){
        $ticket_number = $_GET['ticket_number'];
    }else{
        $ticket_number = $_POST['ticket_number'];
    }
    try
    {

        $client = new Pdfcrowd("alexboey", "56016f94b57b602d5c500837ce30e00c");


        // convert a web page and store the generated PDF into a $pdf variable
        $pdf = $client->convertURI('https://ticketsoko.nouveta.co.ke/ticket.html?ticket_number='.$ticket_number);

        // set HTTP response headers
        header("Content-Type: application/pdf");
        header("Cache-Control: max-age=0");
        header("Accept-Ranges: none");
        header("Content-Disposition: attachment; filename=\"$ticket_number.pdf\"");

        // send the generated PDF
        echo $pdf;
    }
    catch(PdfcrowdException $why)
    {
        echo "Pdfcrowd Error: " . $why;
    }
}

if($function=="uploadImage"){
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
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

function pushPayments($amount, $phoneNumber, $AccountReference,$TransactionDes,$payBill){


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://payme.ticketsoko.com/api/index.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"function\"\r\n\r\nCustomerPayBillOnline\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"PayBillNumber\"\r\n\r\n$payBill\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"Amount\"\r\n\r\n$amount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"PhoneNumber\"\r\n\r\n$phoneNumber\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"AccountReference\"\r\n\r\n$AccountReference\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"TransactionDesc\"\r\n\r\nTICKETSOKO\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
            "postman-token: 4fe6b48a-5c0a-e9fa-7d45-172ce8b64722"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

}
function pushCardPayments($amount,$phone_number_sale,$order_number_sale,$transaction_desc){

    $cvv= $_REQUEST['cvv'];
    $cardno = $_REQUEST['cardno'];
    $ccexpmonth = $_REQUEST['ccexpmonth'];
    $ccexpyear = $_REQUEST['ccexpyear'];
    $email=$_REQUEST['email'];
    $names=$_REQUEST['names'];
    $callback= 'https://ticketsoko.nouveta.co.ke/api/callback.php';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt_array($curl, array(
        CURLOPT_URL =>   "https://cardsapi.nouveta.co.ke/step1.php?cvv=".$cvv."&cardno=".$cardno."&ccexpmonth=".$ccexpmonth."&ccexpyear=".$ccexpyear."&amount=".$amount."&email=".$email."&names=".$names."&callback=".$callback,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
            "postman-token: 4fe6b48a-5c0a-e9fa-7d45-172ce8b64722"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

}

function confirmatioCode(){
    $ConfCode = new ConfirmationCode;
    $code = $ConfCode->auto(3);
    return $code;
}

function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^\dxX]/', '', $phoneNumber);
    $phoneNumber = preg_replace('/^0/','254',$phoneNumber);

    $phoneNumber = $phone = preg_replace('/\D+/', '', $phoneNumber);

    return $phoneNumber;
}

/*if($function =="getTotalSales"){
    echo totalSales();
}

function totalTicketsSold(){



}

function totalSales(){
    $sqlTotal = "SELECT SUM(amount) FROM `ticket_sales` WHERE event_Company = 'KUBAMBA'";

    $result = DB::instance()->executeSQL($sqlTotal);
    if($result){
        return $result->fetch_array()[0];
    }

}

function totalMerchandise(){
    $sqlTotal = "SELECT SUM(amount) FROM `ticket_sales` WHERE event_Company = 'KUBAMBA'";

    $result = DB::instance()->executeSQL($sqlTotal);
    if($result){
        return $result->fetch_array()[0];
    }

}

function totalValidatedTickets(){

}*/
if($function=='personaldata'){


    $sql="SELECT * FROM `personalData` ORDER BY `ID` DESC ";
    $result = DB::instance()->executeSQL($sql);
    if($result->num_rows > 0){

        while( $row = $result->fetch_assoc()){
            $new_array[] = $row; // Inside while loop
        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $new_array;
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if($function=='accomodationdata'){


    $sql="SELECT * FROM `accomodation` ORDER BY `ID` DESC ";
    $result = DB::instance()->executeSQL($sql);
    if($result->num_rows > 0){

        while( $row = $result->fetch_assoc()){
            $new_array[] = $row; // Inside while loop
        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $new_array;
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if($function=='insurance'){


    $sql="SELECT  `idno`,`personalData`.`firstName`, `medicalCondition`, `allergies`, `insuranceProvider`, `policyNumber`, `nameOnCard`, `tellNo`, `emergencyContactName`, `relationship`, `tellNoRelationship`, `emegency`.`email`, `countryOfResidence` 
           FROM `emegency` , `personalData` ";
    $result = DB::instance()->executeSQL($sql);
    if($result->num_rows > 0){

        while( $row = $result->fetch_assoc()){
            $new_array[] = $row; // Inside while loop
        }

        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->data= $new_array;
        $response->success = true;
        echo json_encode($response);
        exit();

    }else{
        $response = new Response();
        $response->status = Response::STATUS_SUCCESS;
        $response->message= "No Data found";
        $response->success = false;
        echo json_encode($response);
        exit();
    }

}
if($function=='return'){

    echo "<script type='text/javascript'>alert('payments confirmed') window.location.replace('https://ticketsoko.com')</script>";
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


}
// if($funtion='atTheDoor'){


//     $phone_number=formatPhoneNumber($_REQUEST['phone']);

//     $sql ="INSERT INTO `ticket_sales`(`order_number`, `event_id`, `quantity`, `amount`, `event_company`, `phone_number`, `paybill`,`paid`)
//                       VALUES ('cash','16','1','2500','Mercedes-Benz Club Kenya','test','cash','1')";
//     $result = DB::instance()->executeSQL($sql);
//     if($result) {

//         $ticket_number = confirmatioCode();



//         $sql ="INSERT INTO `tickets`(`order_number`, `event_id`, `ticket_number`, `is_validated`, `description`, `phone_number`, `event_company`,`event_image`,`day`,`email`,`paid`) VALUES 
//                           ('cash','16','$ticket_number','1','Member charges KES 2500','cash','Mercedes-Benz Club Kenya','https://ticketsoko.com/finalmerc.jpeg','amy','cash','1')";
//         DB::instance()->executeSQL($sql);
//         $link = "Follow link to download your ticket https://ticketsold.ticketsoko.com/ticket.html?ticket_number=$ticket_number";

//         send($phone_number,$link);
//          echo 'im done';
//     }

// }
// if($function=='paybill'){

//     $phone_number=$_REQUEST['phone'];

//     $sql ="INSERT INTO `ticket_sales`(`order_number`, `event_id`, `quantity`, `amount`, `event_company`, `phone_number`, `paybill`,`paid`)
//                       VALUES ('Mpesa','16','1','2500','Mercedes-Benz Club Kenya','test','mpesa','1')";
//     $result = DB::instance()->executeSQL($sql);
//     if($result) {

//         $ticket_number = confirmatioCode();



//         $sql ="INSERT INTO `tickets`(`order_number`, `event_id`, `ticket_number`, `is_validated`, `description`, `phone_number`, `event_company`,`event_image`,`day`,`email`,`paid`) VALUES 
//                           ('mpesa','16','$ticket_number','1','Member charges KES 2500','cash','Mercedes-Benz Club Kenya','https://ticketsoko.com/finalmerc.jpeg','amy','mpesa','1')";
//         DB::instance()->executeSQL($sql);
//         $link = "Follow link to download your ticket https://ticketsold.ticketsoko.com/ticket.html?ticket_number=$ticket_number";

//         send($phone_number,$link);

//     }
// }
class PDF_MySQL_Table extends FPDF
{
protected $ProcessingTable=false;
protected $aCols=array();
protected $TableX;
protected $HeaderColor;
protected $RowColors;
protected $ColorIndex;

function Header()
{
    // Print the table header if necessary
    if($this->ProcessingTable)
        $this->TableHeader();
}

function TableHeader()
{
    $this->SetFont('Arial','B',12);
    $this->SetX($this->TableX);
    $fill=!empty($this->HeaderColor);
    if($fill)
        $this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
    foreach($this->aCols as $col)
        $this->Cell($col['w'],6,$col['c'],1,0,'C',$fill);
    $this->Ln();
}

function Row($data)
{
    $this->SetX($this->TableX);
    $ci=$this->ColorIndex;
    $fill=!empty($this->RowColors[$ci]);
    if($fill)
        $this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
    foreach($this->aCols as $col)
        $this->Cell($col['w'],5,$data[$col['f']],1,0,$col['a'],$fill);
    $this->Ln();
    $this->ColorIndex=1-$ci;
}

function CalcWidths($width, $align)
{
    // Compute the widths of the columns
    $TableWidth=0;
    foreach($this->aCols as $i=>$col)
    {
        $w=$col['w'];
        if($w==-1)
            $w=$width/count($this->aCols);
        elseif(substr($w,-1)=='%')
            $w=$w/100*$width;
        $this->aCols[$i]['w']=$w;
        $TableWidth+=$w;
    }
    // Compute the abscissa of the table
    if($align=='C')
        $this->TableX=max(($this->w-$TableWidth)/2,0);
    elseif($align=='R')
        $this->TableX=max($this->w-$this->rMargin-$TableWidth,0);
    else
        $this->TableX=$this->lMargin;
}

function AddCol($field=-1, $width=-1, $caption='', $align='L')
{
    // Add a column to the table
    if($field==-1)
        $field=count($this->aCols);
    $this->aCols[]=array('f'=>$field,'c'=>$caption,'w'=>$width,'a'=>$align);
}

function Table($link, $query, $prop=array())
{
    // Execute query
    $res=mysqli_query($link,$query) or die('Error: '.mysqli_error($link)."<br>Query: $query");
    // Add all columns if none was specified
    if(count($this->aCols)==0)
    {
        $nb=mysqli_num_fields($res);
        for($i=0;$i<$nb;$i++)
            $this->AddCol();
    }
    // Retrieve column names when not specified
    foreach($this->aCols as $i=>$col)
    {
        if($col['c']=='')
        {
            if(is_string($col['f']))
                $this->aCols[$i]['c']=ucfirst($col['f']);
            else
                $this->aCols[$i]['c']=ucfirst(mysqli_fetch_field_direct($res,$col['f'])->name);
        }
    }
    // Handle properties
    if(!isset($prop['width']))
        $prop['width']=0;
    if($prop['width']==0)
        $prop['width']=$this->w-$this->lMargin-$this->rMargin;
    if(!isset($prop['align']))
        $prop['align']='C';
    if(!isset($prop['padding']))
        $prop['padding']=$this->cMargin;
    $cMargin=$this->cMargin;
    $this->cMargin=$prop['padding'];
    if(!isset($prop['HeaderColor']))
        $prop['HeaderColor']=array();
    $this->HeaderColor=$prop['HeaderColor'];
    if(!isset($prop['color1']))
        $prop['color1']=array();
    if(!isset($prop['color2']))
        $prop['color2']=array();
    $this->RowColors=array($prop['color1'],$prop['color2']);
    // Compute column widths
    $this->CalcWidths($prop['width'],$prop['align']);
    // Print header
    $this->TableHeader();
    // Print rows
    $this->SetFont('Arial','',11);
    $this->ColorIndex=0;
    $this->ProcessingTable=true;
    while($row=mysqli_fetch_array($res))
        $this->Row($row);
    $this->ProcessingTable=false;
    $this->cMargin=$cMargin;
    $this->aCols=array();
}
}
if ($function=='transaction'){class PDF extends PDF_MySQL_Table
{
    function Header()
    {
        // Title
        $this->SetFont('Arial','',18);
        $this->Cell(0,6,'MERCEDES BENZ CLUB KENYA',0,1,'C');
        $this->Ln(10);
        // Ensure table header is printed
        parent::Header();
    }
}

// Connect to database
    $link = mysqli_connect('localhost','ticketso_nouveta','Network97','ticketso_ticketsoko');

    $pdf = new PDF();
    $pdf->AddPage();
// First table: output all columns
    $pdf->Table($link,"SELECT `order_number`,`ticket_number`,`is_validated`,`phone_number`,`email` FROM tickets WHERE `event_id`=16 AND `paid` ='1'");
    $pdf->AddPage();
// Second table: specify 3 columns
    $pdf->AddCol('rank',20,'','C');
    $pdf->AddCol('name',40,'Country');
    $pdf->AddCol('pop',40,'Pop (2001)','R');
    $prop = array('HeaderColor'=>array(255,150,100),
        'color1'=>array(210,245,255),
        'color2'=>array(255,255,210),
        'padding'=>2);
//$pdf->Table($link,'select RevSource, format(pop,0) as pop, rank from revenue_sources order by rank limit 0,10',$prop);
    $pdf->Output();
}

