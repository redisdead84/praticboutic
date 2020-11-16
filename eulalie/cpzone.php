<?php

require '../vendor/autoload.php';

include "config/config.php";
include "param.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $bdd);
// Check connection
if ($conn->connect_error) 
  die("Connection failed: " . $conn->connect_error);    

header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $cp = json_decode($json_str);
  
  $query = 'SELECT cpzoneid FROM cpzone WHERE codepostal = "' . $cp . '" AND actif = 1';

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