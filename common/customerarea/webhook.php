<?php

header('Content-Type: application/json');

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

$json_str = file_get_contents('php://input');
$input = json_decode($json_str);
$output ="";

/* Initialize the Stripe client */
// For sample support and debugging. Not required for production:
\Stripe\Stripe::setAppInfo(
  "pratic-boutic/subscription/fixed-price",
  "0.0.2",
  "https://praticboutic.fr"
);

$stripe = new \Stripe\StripeClient([
// TODO replace hardcoded apikey by env variable
  'api_key' => $_ENV['STRIPE_SECRET_KEY'],
  'stripe_version' => '2020-08-27',
]);

$event = $input;
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

// Parse the message body (and check the signature if possible)
$webhookSecret = "whsec_8D2NVgd5GehcmXjnJERYzUbQeWiENL7a";
if ($webhookSecret) 
{
  try 
  {
    $event = \Stripe\Webhook::constructEvent($json_str, $sig_header, $webhookSecret);
  } 
  catch (Exception $e) 
  {
    http_response_code(403);
    echo json_encode(['error' => $e->getMessage()]);
  }
} 
else 
{
  $event = $input;
}

$type = $event['type'];
$object = $event['data']['object'];

// Handle the event
// Review important events for Billing webhooks
// https://stripe.com/docs/billing/webhooks
switch ($type) {
  case 'invoice.paid':
    if ($object['billing_reason'] == 'subscription_create') {
      // The subscription automatically activates after successful payment
      // Set the payment method used to pay the first invoice
      // as the default payment method for that subscription
      $subscription_id = $object['subscription'];
      $payment_intent_id = $object['payment_intent'];

      # Retrieve the payment intent used to pay the subscription
      $payment_intent = $stripe->paymentIntents->retrieve(
        $payment_intent_id,
        []
      );
      $stripe->subscriptions->update(
        $subscription_id,
        ['default_payment_method' => $payment_intent->payment_method],
      );

      error_log('Default payment method set for subscription:' + $payment_intent->payment_method);
      
      $query = "UPDATE abonnement SET creationboutic = 1, actif = 1 ";
      $query = $query . "WHERE stripe_subscription_id = '$subscription_id'";

      error_log($query);

      if ($conn->query($query) === FALSE)
      {
        throw new Error($conn->error);
      }
    };

    // database to reference when a user accesses your service to avoid hitting rate
    // limits.
    echo 'Invoice paid: ' . $event->id;
    break;
  case 'invoice.payment_failed':
      // If the payment fails or the customer does not have a valid payment method,
      // an invoice.payment_failed event is sent, the subscription becomes past_due.
      // Use this webhook to notify your user that their payment has
      // failed and to retrieve new card details.
      echo 'Invoice payment failed: ' . $event->id;
      break;
  case 'invoice.finalized':
      // If you want to manually send out invoices to your customers
      // or store them locally to reference to avoid hitting Stripe rate limits.
      echo 'Invoice finalized: ' . $event->id;
      break;
  case 'customer.subscription.deleted':
      // handle subscription cancelled automatically based
      // upon your subscription settings. Or if the user
      // cancels it.
      echo 'Subscription canceled: ' . $event->id;
      break;
  // ... handle other event types
  default:
  // Unhandled event type
}

http_response_code(200);
echo json_encode(['status' => 'success']);

$conn->close();
?>
