<?php

session_start();

require '../vendor/autoload.php';

header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $obj = json_decode($json_str);
  
  $customer = $obj->customer;
  
  include "../" . $customer . "/config/custom_cfg.php";
  include "config/common_cfg.php";
  include "param.php";

  $val = $obj->sstotal;
  
  $surcout = 0;
  
		// Create connection
	$conn = new mysqli($servername, $username, $password, $bdd);
	// Check connection
	if ($conn->connect_error) 
	  die("Connection failed: " . $conn->connect_error); 
	
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