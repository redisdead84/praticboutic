<?php

header('Access-Control-Allow-Origin: *');
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');

$postdata = file_get_contents("php://input");
if (isset($postdata))
  $request = json_decode($postdata);

error_log("toto");
error_log($postdata);

$servername = "localhost";
$username = "qruser";
$password = "A1u4qr!=gg";
$dbname = "merge_test_fusion2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT c.pass, cu.customid FROM client c, customer cu WHERE c.email = '" . $request->email . "' AND c.cltid = cu.cltid LIMIT 1"; 
//AND pass = '" . password_hash($request->password, PASSWORD_DEFAULT ) . "'";
error_log($sql);
$result = $conn->query($sql);

  // output data of each row
  if($row = $result->fetch_row())
  {
    error_log($row[0]);
    error_log($request->password);
    if (password_verify($request->password, $row[0]) )
    {
      echo ($row[1]);
      error_log("1");
    }
    else 
    {
      echo ("-1");
      error_log("0");
    }
  }
  else 
  {
    echo ("-1");
    error_log("0");
  }
  $result->close();
  $conn->close();

?>
