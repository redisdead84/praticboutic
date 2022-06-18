<?php
  session_id("customerarea");
  session_start();

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

  $sql = "SELECT COUNT(*) FROM client c WHERE c.email = '" . $request->email . "' LIMIT 1";

   error_log($sql);
  $result = $conn->query($sql);

  // output data of each row
  if($row = $result->fetch_row())
  {
    echo ($row[0]);
    if ($row[0] == 0)
    {
      $_SESSION['verify_email'] = $request->email;

      //error_log('output : ' . $_SESSION['verify_email']);
    }
  }
  else
  {
    echo ("-1");
  }
  $result->close();
  $conn->close();

?>
