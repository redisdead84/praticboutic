<?php
  session_id("customerarea");
  session_start();

  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  header('Access-Control-Max-Age: 86400');

  include "../config/common_cfg.php";

  try
  {
    $json_str = file_get_contents('php://input');
    $input = json_decode($json_str);
    $output ="";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);
    // Check connection
    if ($conn->connect_error) {
      throw new Error("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT COUNT(*) FROM client c WHERE c.email = '" . $input->email . "' LIMIT 1";

    //error_log($sql);
    $result = $conn->query($sql);

    // output data of each row
    if($row = $result->fetch_row())
    {
      if ($row[0] == 0)
      {
        $_SESSION['verify_email'] = $input->email;
        $output = "OK";
      }
      else
      {
        $output = "KO";
      }
    }
    else
    {
      throw new Error('Erreur lors de la vérification du courriel');
    }
    $result->close();
    $conn->close();
    echo json_encode($output);
  }
  catch (Error $e)
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
?>
