<?php

session_start();

if (empty($_SESSION['boutic']) == TRUE)
 	  header("LOCATION: index.php");
else	
  $boutic = $_SESSION['boutic'];

if (empty($_SESSION[$boutic . '_auth']) == TRUE)
{
 	header("LOCATION: index.php");
 	exit();
}	

if (strcmp($_SESSION[$boutic . '_auth'],'oui') != 0)
{
 	header("LOCATION: index.php");
 	exit();
}
  
require '../../vendor/autoload.php';

include "../config/common_cfg.php";
include "../param.php";

$conn = new mysqli($servername, $username, $password, $bdd);
if ($conn->connect_error) 
  die("Connection failed: " . $conn->connect_error); 


header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);
  
  //error_log($json_obj->customer);

  
  $reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
  $reqci->bind_param("s", $json_obj->customer);
  $reqci->execute();
  $reqci->bind_result($customid);
  $resultatci = $reqci->fetch();
  $reqci->close();

  //error_log($customid);
	
	$validsms = GetValeurParam("VALIDATION_SMS", $conn, $customid, "0");
	
	$rcvnom = GetValeurParam("Receivernom_mail", $conn, $customid,"Ma PraticBoutic");  
  
  //error_log($validsms);
	if (strcmp($validsms,"1") == 0)
	{
		$content = $json_obj->message;

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer ' . $tokensms));
    $response = curl_exec($ch);
    curl_close($ch);
	   
	}
  $conn->close();
  
	$output = "SMS OK";  
  
  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

