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

require '../vendor/autoload.php';

function calculateOrderAmount(array $items, $conn, $customid, $model, $fraislivr): int 
{
  $arrlength = count($items);
  $price = 0.0;
  if ($arrlength === 0)
    throw new Exception("Panier Vide");
  else 
  {
  	// Calcul du cout des articles obligatoires
    $query = 'SELECT artid FROM article WHERE customid = ' . $customid . ' AND obligatoire = 1';
    
		if ($result = $conn->query($query)) {
      while ($row = $result->fetch_row()) {
        $find = 0;
     		for ($j=0; $j<$arrlength ; $j++)
        { 
          $id1 = $items[$j]->id;
          $id2 = $row[0];
          if ($id1 == $id2)
          {
            $find = 1;          
          }
     		}
     		if($find == 0) {
     		  throw new Exception("Article obligatoire manquant");
     		}
     	}
   		$result->close();
   	}
    
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
  		
  		$price = $price + $surcout;
    }
  }
  // Envoi du cout de la commande
  return (round($price * 100));
}


header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);

  $customer = htmlspecialchars($json_obj->boutic);	
  
	include "config/common_cfg.php";
  include "param.php";

	
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
	
	$skey = GetValeurParam("SecretKey", $conn, $customid);
	
	// This is your real test secret API key.
	\Stripe\Stripe::setApiKey($skey);
	
	error_log($json_obj->fraislivr);
	
  $paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => calculateOrderAmount($json_obj->items, $conn, $customid, $json_obj->model, $json_obj->fraislivr),
    'currency' => 'eur',
  ]);

  $output = [
    'clientSecret' => $paymentIntent->client_secret,
  ];

  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

