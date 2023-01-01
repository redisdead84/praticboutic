<?php

session_start();

if (empty($_SESSION['customer']) != 0)
{
  header('LOCATION: 404.html');
  exit();
}

$customer = $_SESSION['customer'];
$method = $_SESSION['method'];
$table = $_SESSION['table'];

if (empty($_SESSION[$customer . '_mail']) == TRUE)
{
  header('LOCATION: index.php?customer=' . $customer . '');
  exit();
}

if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
{
  header('LOCATION: index.php?customer=' . $customer . '');
  exit();
}

require "../vendor/autoload.php";
include "config/common_cfg.php";
include "param.php";
  
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function calculateOrderAmount(array $items, $conn, $customid, $model, $fraislivr, $codepromo): int 
{
  $arrlength = count($items);
  $price = 0.0;
  if ($arrlength === 0)
    throw new Exception("Panier Vide");
  else 
  {
    // Calcul du cout des lignes de commandes
 		for ($i=0; $i<$arrlength ; $i++)
    { 
      // recuperre l'id
      $id = $items[$i]->id;

      $type = $items[$i]->type;
      if ($type == "article")
        $req = $conn->prepare('SELECT prix FROM article WHERE customid = ' . $customid . ' AND artid = ?');
      else if ($type == "option")
        $req = $conn->prepare('SELECT surcout FROM `option` WHERE customid = ' . $customid . ' AND optid = ?');

      $req->bind_param("s", $id);
   	  $req->execute();
     	$req->bind_result($prix_serveur);
     	$resultat = $req->fetch();
      $req->close(); 
      
      if ($prix_serveur == $items[$i]->prix)
        $price = $price + $items[$i]->prix * $items[$i]->qt;
      else
        throw new Exception("Prix invalide");
    }
    
    $surcout = 0;
    
    // Calcul du cout de livraison
		if(strcmp($model, "LIVRER") == 0) 
		{
    	$query = 'SELECT surcout FROM barlivr WHERE customid = ' . $customid . ' AND valminin <= ' . $price . ' AND (valmaxex > ' . $price . ' OR valminin >= valmaxex) AND actif = 1';
		
			if ($result = $conn->query($query)) 
			{
				if ($result->num_rows > 0)
				{
			  	if ($row = $result->fetch_row()) 
		  			$surcout = $row[0];
		  	}
		  	else
		  		$surcout = 0; 
  		}
  		
			if($surcout != $fraislivr) {
        throw new Exception("erreur Frais de livraison");
  		}
  		
    }
    
    $query = 'SELECT taux FROM promotion WHERE customid = ' . $customid . ' AND code = "' . $codepromo . '" AND actif = 1';
    
		if ($result = $conn->query($query)) 
		{
			if ($result->num_rows > 0)
			{
		  	if ($row = $result->fetch_row()) 
	  			$taux = $row[0];
	  	}
	  	else
	  		$taux = 0; 
		}
		
		$remise = $price * -($taux/100);
		
		$total = $price + $remise + $surcout;
		
  }
  // Envoi du cout de la commande
  return (round($total * 100));
}


header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);

  $customer = htmlspecialchars($json_obj->boutic);	

	// Create connection
	$conn = new mysqli($servername, $username, $password, $bdd);
	// Check connection
	if ($conn->connect_error) 
	  die("Connection failed: " . $conn->connect_error);    
	  
	$reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
	$reqci->bind_param("s", $customer);
	$reqci->execute();
	$reqci->bind_result($customid);
	$resultatci = $reqci->fetch();
	$reqci->close();
	
	$skey = $_ENV['STRIPE_SECRET_KEY'];
	
	$stripe_connected_account = GetValeurParam("STRIPE_ACCOUNT_ID", $conn, $customid);
	
	// This is your real test secret API key.
	\Stripe\Stripe::setApiKey($skey);
	
  $paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => calculateOrderAmount($json_obj->items, $conn, $customid, $json_obj->model, $json_obj->fraislivr, $json_obj->codepromo),
    'currency' => 'eur',
    'automatic_payment_methods' => ['enabled' => 'true']
  ], ['stripe_account' => $stripe_connected_account,
  ]);

  $output = [
    'clientSecret' => $paymentIntent->client_secret,
  ];

  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

