<?php

  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";

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
  $json_obj = json_decode($json_str);
  if (isset($json_obj->sessionid))
    session_id($json_obj->sessionid);

  session_start();

  if (empty($_SESSION['customer']) != 0)
  {
    throw new Error("Pas de boutic");
  }

  $customer = $_SESSION['customer'];
  $method = $_SESSION['method'];
  $table = $_SESSION['table'];

  if (empty($_SESSION[$customer . '_mail']) == TRUE)
  {
    throw new Error("Pas de courriel");
  }

  if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
  {
    throw new Error("Courriel déjà envoyé");
  }

  $customer = htmlspecialchars($json_obj->customer);
	
	$reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
	$reqci->bind_param("s", $customer);
	$reqci->execute();
	$reqci->bind_result($customid);
	$resultatci = $reqci->fetch();
	$reqci->close();

  $query = 'SELECT cpzoneid FROM cpzone WHERE customid = ' . $customid . ' AND codepostal = "' . $json_obj->cp . '" AND actif = 1';

  $output = "ko";

	if ($result = $conn->query($query)) {
    if ($result->num_rows > 0)
      $output = "ok";		
  }   
  
  $result->free_result();
  
  $conn->close();

  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}



?>