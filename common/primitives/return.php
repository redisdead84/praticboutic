<?php

  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  
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
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);
    // Check connection
    if ($conn->connect_error)
    {
      throw new Error("Connection failed: " . $conn->connect_error);
    }
    
    $sca = GetValeurParam("STRIPE_ACCOUNT_ID", $conn, $_SESSION['bo_id']);
    
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
    
    if (strcmp($sca,'') != 0)
    {
      $loginlink = $stripe->accounts->createLoginLink($sca);
      header('Location: ' .  $loginlink->url);
    }
    else 
    {
      throw new Error('Il n\'y a pas de compte Stripe défini pour cette boutic');
    }
  }
  catch (Error $e)
  {
    echo $e->getMessage();
  }
?>
