<?php


  header('Access-Control-Allow-Origin: * ');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  header('Access-Control-Max-Age: 86400');
  header('Content-Type: application/json');

  //Load composer's autoloader
  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";

  try 
  {
    $json_str = file_get_contents('php://input');
    $input = json_decode($json_str);
    
    if (isset($input->sessionid))
      session_id($input->sessionid);
    session_start();
    
    if (!isset($_SESSION))
    {
      throw new Error('Session expirée');
    }
    
    if (empty($_SESSION['bo_auth']) == TRUE)
    {
      throw new Error("Non authentifié");
    }
  
    if (strcmp($_SESSION['bo_auth'],'oui') != 0)
    {
      throw new Error("Non authentifié");
    }
    


    $bouticid = $input->bouticid;
    $conn = new mysqli($servername, $username, $password, $bdd);
    if ($conn->connect_error) 
    {
      throw new Error("Connection failed: " . $conn->connect_error);
    }
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
  
    $stripe = new \Stripe\StripeClient([
      'api_key' => $_ENV['STRIPE_SECRET_KEY'],
      'stripe_version' => '2020-08-27',
    ]);
    
    $sca = GetValeurParam("STRIPE_ACCOUNT_ID", $conn, $bouticid);
    if (strcmp($sca, "") == 0 )
      $output = "KO";
    else
      $output = $stripe->accounts->retrieve($sca, [])->charges_enabled ? "OK" : "KO";

    $conn->close();
    echo json_encode($output);
  }
  catch (Error $e) 
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
?>