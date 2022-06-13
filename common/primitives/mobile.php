<?php

header('Access-Control-Allow-Origin: *');
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');

$postdata = file_get_contents("php://input");
if (isset($postdata))
  $request = json_decode($postdata);

include "../config/common_cfg.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $bdd);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT c.pass, cu.customid, cu.customer FROM client c, customer cu WHERE c.email = '" . $request->email . "' AND c.cltid = cu.cltid LIMIT 1";

// error_log($sql);
$result = $conn->query($sql);

  // output data of each row
  if($row = $result->fetch_row())
  {

    if (password_verify($request->password, $row[0]) )
    {
      $arr=array();
      array_push($arr, $row[1]);
      array_push($arr, $row[2]);
      $output = $arr;
      //error_log(json_encode($output));
      echo json_encode($output);
    }
    else
    {
      echo ("-1");
    }
  }
  else
  {
    echo ("-1");
  }
  $result->close();
  $conn->close();

?>
