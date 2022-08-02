<?php
session_id("customerarea");
session_start();

require '../../vendor/autoload.php';
include "../config/common_cfg.php";
include "../param.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $bdd);
// Check connection
if ($conn->connect_error) 
  die("Connection failed: " . $conn->connect_error);
  
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Access-Control-Allow-Origin: *');
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json');

try
{

  $json_str = file_get_contents('php://input');
  $input = json_decode($json_str);
  $output ="";

  //error_log($json_str);

  /* Initialize the Stripe client */
  // For sample support and debugging. Not required for production:
  \Stripe\Stripe::setAppInfo(
    "pratic-boutic/registration  ",
    "0.0.2",
    "https://praticboutic.fr"
  );

  $stripe = new \Stripe\StripeClient([
  // TODO replace hardcoded apikey by env variable
    'api_key' => $_ENV['STRIPE_SECRET_KEY'],
    'stripe_version' => '2020-08-27',
  ]);
  
    //  Récupération de l'utilisateur et de son pass hashé
  $req = $conn->prepare('SELECT client.cltid, client.pass, customer.customid, client.stripe_customer_id FROM client, customer WHERE client.email = ? AND client.actif = 1 AND client.cltid = customer.cltid LIMIT 1');
  $req->bind_param("s", $input->courriel);
  $req->execute();
  $req->bind_result($id, $pass_hache, $bouticid, $stripe_customer_id);
  $resultat = $req->fetch();
  $req->close();
  if ($resultat)
  {
    $_SESSION['bo_stripe_customer_id'] = $stripe_customer_id;
    $_SESSION['bo_id'] = $bouticid;
    $_SESSION['bo_email'] = $input->courriel;
    $_SESSION['bo_auth'] = 'oui';
    $_SESSION['bo_init'] = 'non';
    echo json_encode($output);
    header("LOCATION: admin.php");
  }
  else
  {
    $_SESSION['verify_email'] = $input->courriel;
    echo json_encode($output);
    header("LOCATION: register.php");
    
  }
}
catch (Error $e)
{
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>