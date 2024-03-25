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


$query = "SELECT customer.customid, customer.customer, customer.nom, customer.logo, client.stripe_customer_id FROM customer, client WHERE customer.cltid = client.cltid";

if ($result = $conn->query($query)) 
{
  while ($row = $result->fetch_row())
  {
    $bouticid = $row[0];
    try
    {   

        //check active subscription
        $subscriptions = $stripe->subscriptions->all(['customer' => $row[4],
          'status' => 'active'
        ]);
        //error_log($subscriptions);
        if ($subscriptions->count() > 0)
          $query = "UPDATE customer SET actif = 1 WHERE customid = " . $bouticid;
        else 
          $query = "UPDATE customer SET actif = 0 WHERE customid = " . $bouticid;

        if ($conn->query($query) === FALSE)
        {
           throw new Error($conn->error);
        }
    }
    catch (Exception $e)
    {
        //error_log($e);
        $query = "UPDATE customer SET actif = 0 WHERE customid = " . $bouticid;

        if ($conn->query($query) === FALSE)
        {
           throw new Error($conn->error);
        }
    }



  } 

  $result->close();
}


?>
