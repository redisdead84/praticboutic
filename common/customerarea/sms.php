<?php

session_id("customerarea");
session_start();

require '../../vendor/autoload.php';

include "../config/common_cfg.php";
include "../param.php";

$conn = new mysqli($servername, $username, $password, $bdd);
if ($conn->connect_error) 
  die("Connection failed: " . $conn->connect_error); 

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
  
header('Access-Control-Allow-Origin: *');
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json');
try {

  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);
  
  //error_log($json_obj->customer);

  $customid = $json_obj->bouticid;
  
  /*$reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
  $reqci->bind_param("s", $json_obj->customer);
  $reqci->execute();
  $reqci->bind_result($customid);
  $resultatci = $reqci->fetch();
  $reqci->close();*/

  //error_log($customid);
	
	$validsms = GetValeurParam("VALIDATION_SMS", $conn, $customid, "0");
	
	$rcvnom = GetValeurParam("Receivernom_mail", $conn, $customid,"Ma PraticBoutic");  
  
  //error_log($validsms);
	if (strcmp($validsms,"1") == 0)
	{
		$content = $json_obj->message;
		
		//error_log($content);

    $numbers = array($json_obj->telephone);
    
    $recipients = array();
    foreach ($numbers as $n) {
      $recipients[] = array('value' => $n);
    }

    $postdata = array(
      'sms' => array(
       'message' => array(
        'text' => $content,
        'sender' => $sendersms
       ),
       'recipients' => array('gsm' => $recipients)
      )
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.smsfactor.com/send");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer ' . $_ENV['TOKEN_SMS']));
    $response = curl_exec($ch);
    curl_close($ch);
    //error_log($response);
	   
	}
  $conn->close();
  
	$output = "SMS OK";  
  //error_log($output);
  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

