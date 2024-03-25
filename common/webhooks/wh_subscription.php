<?php

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

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

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

function maj_customer_subscription($conn, $stripe, $abo) 
{
  try
  {
    //check active subscription
    $subscriptions = $stripe->subscriptions->all(['customer' => $abo->customer,
      'status' => 'active'
    ]);

    $req = $conn->prepare('SELECT customer.customid FROM customer, client WHERE client.stripe_customer_id = ? AND customer.cltid = client.cltid');
    $req->bind_param("s", $abo->customer);
    $req->execute();
    $req->bind_result($bouticid);
    $resultat = $req->fetch();
    $req->close();
  
    if ($subscriptions->count() > 0)
      $query = "UPDATE customer SET actif = 1 WHERE customid = " . $bouticid;
    else 
      $query = "UPDATE customer SET actif = 0 WHERE customid = " . $bouticid;
  
    if ($conn->query($query) === FALSE)
    {
       throw new Error($conn->error);
    }
  }
  catch(Exception $e)
  {
    error_log("Error update" . $abo->id);
  }
}

try
{
  $body = @file_get_contents('php://input');
  $event_json = json_decode($body);
  $event_id = $event_json->id;
  $event = $stripe->events->retrieve($event_id);
  if (($event->type == 'customer.subscription.deleted') || ($event->type == 'customer.subscription.created'))
  {
    maj_customer_subscription($conn, $stripe, $event->data->object);
  }
}
catch (Error $e) 
{
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

?>
