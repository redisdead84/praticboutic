<?php

  require '../vendor/autoload.php';
  include "config/common_cfg.php";
  include "param.php";
  
  ini_set('session.gc_maxlifetime', $maxdureesessionclt);
  
  session_start();
	
	if (empty($_GET['customer']) != 0)
	{
    header('LOCATION: error.php?code=nocustomer');
    exit();
	}
	else
    $customer = htmlspecialchars($_GET['customer']);

	$conn = new mysqli($servername, $username, $password, $bdd);
  if ($conn->connect_error) 
 	  die("Connection failed: " . $conn->connect_error);
	
  $reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
 	$reqci->bind_param("s", $customer);
 	$reqci->execute();
 	$reqci->bind_result($customid);
 	$resultatci = $reqci->fetch();
 	$reqci->close();
 	  
 	if (strcmp($customid, "") == 0 )
 	{
   	header('LOCATION: error.php?code=bouticid');
   	exit;
	}
	
  $reqai = $conn->prepare('SELECT client.stripe_customer_id FROM abonnement, client WHERE abonnement.bouticid = ? AND abonnement.cltid = client.cltid LIMIT 1');
  
 	$reqai->bind_param("i", $customid);
 	$reqai->execute();
 	$reqai->bind_result($stripe_customer_id);
 	$resultataci = $reqai->fetch();
 	$reqai->close();
 	if (strcmp($stripe_customer_id, "") == 0 )
 	{
   	header('LOCATION: error.php?code=nostripeid');
   	exit;
	}
 	
 	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  
  $stripe = new \Stripe\StripeClient([
  // TODO replace hardcoded apikey by env variable
    'api_key' => $_ENV['STRIPE_SECRET_KEY'],
    'stripe_version' => '2020-08-27',
  ]);
  $subscriptions = $stripe->subscriptions->all(['customer' => $stripe_customer_id,
                               'status' => 'active'
  ]);
  if ($subscriptions->count() == 0)
  {
    header('LOCATION: error.php?code=noabo');
    exit();
  }
  
  session_write_close();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Initialisation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
		<script type="text/javascript">
      sessionStorage.clear();
      var customer = '<?php echo $_SESSION['customer'];?>';
      var method = '<?php echo $_SESSION['method'];?>';
      var table = '<?php echo $_SESSION['table'];?>';
      sessionStorage.setItem('customer', customer);
      sessionStorage.setItem(customer + '_mail', 'non');
      sessionStorage.setItem('method', method);
      sessionStorage.setItem('table', table);
      document.location.href = 'carte.php';
		</script>
  </body>
</html>

