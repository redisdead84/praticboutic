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

header('Content-Type: application/json');
try
{

  $json_str = file_get_contents('php://input');
  $input = json_decode($json_str);
  $output ="";

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

  if (strcmp($input->action,"lienscreationboutic") == 0)
  {
    # Simulates an authenticated user. In practice, you'll
    # use the Stripe Customer ID of the authenticated user.
    $req = $conn->prepare('SELECT stripe_customer_id, cltid FROM client WHERE email = ? AND actif = 1 ');
    $req->bind_param("s", $input->login);
    $req->execute();
    $req->bind_result($stripe_customer_id, $cltid);
    $resultat = $req->fetch();
    $req->close();
    if (strcmp($stripe_customer_id, "") == 0 )
    {
      throw new Error("Erreur ! Client non trouvé");
    }
    
    $lienscreation = array();
    $query = 'SELECT aboid, creationboutic, bouticid, stripe_subscription_id FROM abonnement WHERE cltid = ' . $cltid;
    //error_log($query);
    if ($result = $conn->query($query)) 
    {
      while ($row = $result->fetch_row()) 
      {
        $subscription = $stripe->subscriptions->retrieve($row[3]);
        $arm = array("aboid" => $row[0], "creationboutic" => $row[1], "bouticid" => $row[2], "stripe_subscription" => $subscription );
        array_push($lienscreation, $arm);
      }
      $result->close();
    }
    //error_log(print_r($lienscreation, true));
    $output = $lienscreation;

  }
  if (strcmp($input->action,"configuration") == 0)
  {
    $pub_key = $_ENV['STRIPE_PUBLISHABLE_KEY'];
    
    $prices = $stripe->prices->all(['lookup_keys' => ['pb_fixe','pb_conso']]);
    
    $output = array('publishableKey' => $pub_key,
                    'prices' => $prices->data);
    //error_log(print_r($output, true));

  }
  
  if (strcmp($input->action,"creationabonnement") == 0)
  {
    //error_log($input->priceid);
    $_SESSION['creationabonnement_priceid'] = $input->priceid;
    
    $stripe_customer_id = $_SESSION['registration_stripe_customer_id'];
    
    //error_log($stripe_customer_id);
    
    // Create the subscription.
    $subscription = $stripe->subscriptions->create([
        'customer' => $stripe_customer_id,
        'items' => [[
            'price' => $input->priceid,
        ]],
        'payment_behavior' => 'default_incomplete',
        'expand' => ['latest_invoice.payment_intent'],
    ]);
    
    //error_log(print_r($subscription, TRUE));
    
    $_SESSION['creationabonnement_stripe_subscription_id'] = $subscription->id;
    
    //error_log($subscription->latest_invoice->payment_intent->client_secret);

    $output = array(
      'subscriptionId' => $subscription->id,
      'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret
    );
  }
  
  if (strcmp($input->action,"bocreationabonnement") == 0)
  {

    $_SESSION['bocreationabonnement_priceid'] = $input->priceid;
    
    $stripe_customer_id = $_SESSION['bo_stripe_customer_id'];
    
    $req = $conn->prepare('SELECT cltid FROM client WHERE stripe_customer_id = ? AND actif = 1 ');
    $req->bind_param("s", $stripe_customer_id);
    $req->execute();
    $req->bind_result($cltid);
    $resultat = $req->fetch();
    $req->close();
    if (strcmp($stripe_customer_id, "") == 0 )
    {
      throw new Error("Erreur ! Client non trouvé");
    }
    
    // Create the subscription.
    $subscription = $stripe->subscriptions->create([
        'customer' => $stripe_customer_id,
        'items' => [[
            'price' => $input->priceid,
        ]],
        'payment_behavior' => 'default_incomplete',
        'expand' => ['latest_invoice.payment_intent'],
    ]);
    
    $query = "INSERT INTO abonnement(cltid, creationboutic, bouticid, stripe_subscription_id, actif) VALUES ";
    $query = $query . "('$cltid', '0', " . $_SESSION['bo_id'] . ", '" . $subscription->id . "', '1')";

    //error_log($query);

    if ($conn->query($query) === FALSE)
    {
      throw new Error($conn->error);
    }
                
    $_SESSION['bocreationabonnement_stripe_subscription_id'] = $subscription->id;
    
    $output = array(
      'subscriptionId' => $subscription->id,
      'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret
    );
  }
  
  if (strcmp($input->action,"conso") == 0)
  {

    //error_log($input->priceid);
    $_SESSION['creationabonnement_priceid'] = $input->priceid;
    
    $stripe_customer_id = $_SESSION['registration_stripe_customer_id'];
    
    //error_log($stripe_customer_id);
    
    $output = array(
      'customerId' => $stripe_customer_id,
      'priceId' => $input->priceid
    );
  }  
  
  if (strcmp($input->action,"boconso") == 0)
  {

    //error_log($input->priceid);
    $_SESSION['bocreationabonnement_priceid'] = $input->priceid;
    
    $stripe_customer_id = $_SESSION['bo_stripe_customer_id'];
    
    //error_log($stripe_customer_id);
    
    $output = array(
      'customerId' => $stripe_customer_id,
      'priceId' => $input->priceid
    );
  }    
  
  if (strcmp($input->action,"consocreationabonnement") == 0)
  {
    
    $stripe_customer_id = $input->customerId;
    
    $req = $conn->prepare('SELECT cltid FROM client WHERE stripe_customer_id = ? AND actif = 1 ');
    $req->bind_param("s", $stripe_customer_id);
    $req->execute();
    $req->bind_result($cltid);
    $resultat = $req->fetch();
    $req->close();
    if (strcmp($stripe_customer_id, "") == 0 )
    {
      throw new Error("Erreur ! Client non trouvé");
    }
    
    $_SESSION['creationabonnement_priceid'] = $input->priceId;
    
    try {
      $payment_method = $stripe->paymentMethods->retrieve(
        $input->paymentMethodId
      );
      $payment_method->attach([
        'customer' => $stripe_customer_id,
      ]);
    } catch (Exception $e) {
      throw new Error($e->getMessage());
    }
    
    // Set the default payment method on the customer
    $stripe->customers->update($input->customerId, [
      'invoice_settings' => [
        'default_payment_method' => $input->paymentMethodId
      ]
    ]);
    
    // Create the subscription
    $subscription = $stripe->subscriptions->create([
      'customer' => $stripe_customer_id,
      'items' => [
        [
          'price' => $input->priceId,
        ],
      ],
      'expand' => ['latest_invoice.payment_intent'],
    ]);
    
    $_SESSION['creationabonnement_stripe_subscription_id'] = $subscription->id;
    
    $output = $subscription;
  }
  
  if (strcmp($input->action,"boconsocreationabonnement") == 0)
  {
    
    $stripe_customer_id = $input->customerId;
    
    $req = $conn->prepare('SELECT cltid FROM client WHERE stripe_customer_id = ? AND actif = 1 ');
    $req->bind_param("s", $stripe_customer_id);
    $req->execute();
    $req->bind_result($cltid);
    $resultat = $req->fetch();
    $req->close();
    if (strcmp($stripe_customer_id, "") == 0 )
    {
      throw new Error("Erreur ! Client non trouvé");
    }
    
    $_SESSION['bocreationabonnement_priceid'] = $input->priceId;
    
    try {
      $payment_method = $stripe->paymentMethods->retrieve(
        $input->paymentMethodId
      );
      $payment_method->attach([
        'customer' => $stripe_customer_id,
      ]);
    } catch (Exception $e) {
      throw new Error($e->getMessage());
    }
    
    // Set the default payment method on the customer
    $stripe->customers->update($input->customerId, [
      'invoice_settings' => [
        'default_payment_method' => $input->paymentMethodId
      ]
    ]);
    
    // Create the subscription
    $subscription = $stripe->subscriptions->create([
      'customer' => $stripe_customer_id,
      'items' => [
        [
          'price' => $input->priceId,
        ],
      ],
      'expand' => ['latest_invoice.payment_intent'],
    ]);
    
    $query = "INSERT INTO abonnement(cltid, creationboutic, bouticid, stripe_subscription_id, actif) VALUES ";
    $query = $query . "('$cltid', '0', " . $_SESSION['bo_id'] . ", '" . $subscription->id . "', '1')";

    //error_log($query);

    if ($conn->query($query) === FALSE)
    {
      throw new Error($conn->error);
    }
    $_SESSION['bocreationabonnement_stripe_subscription_id'] = $subscription->id;
    
    $output = $subscription;
  }
  
  if (strcmp($input->action,"boannulerabonnement") == 0)
  {

    $subscription = $stripe->subscriptions->cancel($input->subscriptionid);
    
    $req = $conn->prepare("SELECT aboid FROM abonnement WHERE stripe_subscription_id = ? ");
    $req->bind_param("s", $input->subscriptionid);
    $req->execute();
    $req->bind_result($aboid);
    $resultat = $req->fetch();
    $req->close();
    
    //error_log($aboid);
    
    $query = "UPDATE abonnement SET actif = 0 WHERE aboid = $aboid";

    if ($conn->query($query) === FALSE)
    {
      throw new Error($conn->error);
    }

    $output = array('subscription' => $subscription);
  }
  
  if (strcmp($input->action,"boactivationabonnement") == 0)
  {
    $req = $conn->prepare("SELECT aboid FROM abonnement WHERE stripe_subscription_id = ? ");
    $req->bind_param("s", $input->subscriptionId);
    $req->execute();
    $req->bind_result($aboid);
    $resultat = $req->fetch();
    $req->close();
    
    $query = "UPDATE abonnement SET actif = 1 WHERE aboid = $aboid";

    if ($conn->query($query) === FALSE)
    {
      throw new Error($conn->error);
    }

    $subscription = $stripe->subscriptions->retrieve($input->subscriptionId);

    $output = array('subscription' => $subscription);
  }
  
  echo json_encode($output);
}
catch (Error $e)
{
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
