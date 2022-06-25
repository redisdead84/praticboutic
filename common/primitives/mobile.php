<?php
  session_id("customerarea");
  session_start();

  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  header('Access-Control-Max-Age: 86400');

  include "../config/common_cfg.php";

  function incTentative($conn, $ip)
  {
    $q1 = "INSERT INTO connexion (ip, ts) VALUES ('$ip', CURRENT_TIMESTAMP)";
    if (!$conn->query($q1))
    {
      throw new Error("Erreur connexion: " . $conn->error);
    }
    else
    {
      throw new Error('Mauvais identifiant ou mot de passe !');
    }
  }

  try
  {
    $postdata = file_get_contents("php://input");
    if (isset($postdata))
    {
      $request = json_decode($postdata);
    }

    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);
    // Check connection
    if ($conn->connect_error) {
      throw new Error("La connexion a échoué: " . $conn->connect_error);
    }

    $ip = $_SERVER["REMOTE_ADDR"];
    $q2 = "SELECT COUNT(*) FROM `connexion` WHERE `ip` LIKE '$ip' AND `ts` > (now() - interval $interval)";
    if ($r2 = $conn->query($q2))
    {
      if ($row2 = $r2->fetch_row()) 
      {
        $count2 = $row2[0];
      }
    }

    if ($count2 >= $maxretry)
    {
      throw new Error("Vous êtes autorisé à " . $maxretry . " tentatives en " . $interval);
    }
    else 
    { 
      $sql = "SELECT c.pass, cu.customid, cu.customer, c.stripe_customer_id FROM client c, customer cu WHERE c.email = '" . $request->email . "' AND c.cltid = cu.cltid LIMIT 1";
      // error_log($sql);
      $result = $conn->query($sql);
      // output data of each row
      if($row = $result->fetch_row())
      {
        if (password_verify($request->password, $row[0]) )
        {
          $_SESSION['bo_stripe_customer_id'] = $row[3];
          $_SESSION['bo_id'] = $row[1];
          $_SESSION['bo_email'] = $request->email;
          $_SESSION['bo_auth'] = 'oui';
          $_SESSION['bo_init'] = 'non';
          $arr=array();
          array_push($arr, $row[1]);
          array_push($arr, $row[2]);
          array_push($arr, $row[3]);
          $output = $arr;
        }
        else
        {
          incTentative($conn, $ip);
        }
      }
      else
      {
        incTentative($conn, $ip);
      }
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
