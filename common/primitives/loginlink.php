<?php

  session_id("customerarea");
  session_start();
  
  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  
  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";
  try
  {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);
    // Check connection
    if ($conn->connect_error)
    {
      throw new Error("Connection failed: " . $conn->connect_error);
    }

    $json_str = file_get_contents('php://input');
    $input = json_decode($json_str);
    $output ="";
    
    $sca = GetValeurParam("STRIPE_ACCOUNT_ID", $conn, $input->bouticid);
    
    // For sample support and debugging. Not required for production:
    \Stripe\Stripe::setAppInfo(
      "pratic-boutic/registration  ",
      "0.0.2",
      "https://praticboutic.fr"
    );

    $stripe = new \Stripe\StripeClient([
      'api_key' => $_ENV['STRIPE_SECRET_KEY'],
      'stripe_version' => '2020-08-27',
    ]);

    if ($stripe->accounts->retrieve($sca, [])->details_submitted == true)
    {
      $loglink = $stripe->accounts->createLoginLink($sca);
    }
    else
    {
      
      if (isset($_SERVER['HTTPS']))
      {
        $protocole = 'https://';
      }
      else
      {
        $protocole = 'http://';
      }
      
      $server = $_SERVER['SERVER_NAME'];
      
      $accountlink = $stripe->accountLinks->create([
        'account' => $sca,
        'refresh_url' => $protocole . $server . '/common/404.php',
        'return_url' => $protocole . $server . '/common/primitives/close.php',
        'type' => 'account_onboarding',
      ]);

      $loglink = $stripe->accounts->createLoginLink($sca);

    }

    echo json_encode($loglink);

  }
  catch (Error $e)
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
?>

