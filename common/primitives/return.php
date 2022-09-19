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

  if (empty($_SESSION['bo_auth']) == TRUE)
  {
    exit();
  }

  if (strcmp($_SESSION['bo_auth'],'oui') != 0)
  {
    exit();
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

  $json_str = file_get_contents('php://input');
  $input = json_decode($json_str);
  $output ="";
  
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
    if ($stripe->accounts->retrieve($sca, [])->details_submitted == true)
    {
      $loginlink = $stripe->accounts->createLoginLink($sca);
      error_log($loginlink->url);
      header('Location: ' .  $loginlink->url);
    }
  }
?>
