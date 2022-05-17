<?php

header('Access-Control-Allow-Origin: *');
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
// header('Access-Control-Max-Age: 86400');

$postdata = file_get_contents("php://input");
if (isset($postdata))
  $request = json_decode($postdata);


// error_log("toto");
// error_log($postdata);

include "../config/common_cfg.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $bdd);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$arr=array();	

$sql = "SELECT a.nom, a.prix, a.visible, c.nom FROM article a,categorie c WHERE c.customid = '" . $request->customid . "' AND c.customid = a.customid"; 

error_log($sql);



  if ($result = $conn->query($sql))
  {
    while ($row = $result->fetch_row())
    {
      $arm = array('nom' => $row[0], 'prix' => $row[1], 'visible' => $row[2], 'categorie' => $row[3] );
    		  	
   		array_push( $arr, $arm);
 		}
  }
  $result->close();
  
  $conn->close();
  
  $output = $arr;

  //error_log(json_encode($output));	

  echo json_encode($output);
?>
