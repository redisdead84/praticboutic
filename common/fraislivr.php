<?php

require '../vendor/autoload.php';
include "config/common_cfg.php";
include "param.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $bdd);
// Check connection
if ($conn->connect_error) 
  die("Connection failed: " . $conn->connect_error); 
  
header('Access-Control-Allow-Origin: *');
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $obj = json_decode($json_str);
  if (isset($obj->sessionid))
    session_id($obj->sessionid);

  session_start();

  if (empty($_SESSION['customer']) != 0)
  {
    header('LOCATION: error.php?code=nocustomer');
    exit();
  }

  $customer = $_SESSION['customer'];
  $method = $_SESSION['method'];
  $table = $_SESSION['table'];

  if (empty($_SESSION[$customer . '_mail']) == TRUE)
  {
    header('LOCATION: error.php?code=noemail');
    exit();
  }

  if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
  {
    header('LOCATION: error.php?code=alreadysent');
    exit();
  }
  
  $customer = htmlspecialchars($obj->customer);

  $val = $obj->sstotal;
  
  $surcout = 0;
	
	$reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
	$reqci->bind_param("s", $customer);
	$reqci->execute();
	$reqci->bind_result($customid);
	$resultatci = $reqci->fetch();
	$reqci->close();  
  
  $query = 'SELECT surcout FROM barlivr WHERE customid = ' . $customid . ' AND valminin <= ' . $val . ' AND (valmaxex > ' . $val . ' OR valminin >= valmaxex) AND actif = 1';

	if ($result = $conn->query($query)) 
	{
		if ($result->num_rows > 0)
		{
	  	if ($row = $result->fetch_row()) 
  			$surcout = $row[0];
  	}
  	else
  		$surcout = 0; 
  }   
  $output = $surcout;
  
  $conn->close();

  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}



?>