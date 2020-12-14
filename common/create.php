<?php
session_start();

$customer = $_GET['customer'];

require '../vendor/autoload.php';

include "../" . $customer . "/config/custom_cfg.php";
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

//\Stripe\Stripe::setApiKey('sk_test_51H8fNKHGzhgYgqhxXKxXLCKqGMGaHXXfQ3AedURHAd2BTaNjr07L7wLHVZP41UMNWnxRHt4R7XTdeydg0GWcUXL400QA2swxxl');

function calculateOrderAmount(array $items, $conn, $customid): int 
{
  $arrlength = count($items);
  $price = 0.0;
  if ($arrlength === 0)
    throw new Exception("Panier Vide");
  else 
  {
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
  }
  return (round($price * 100));
}


header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);

  $paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => calculateOrderAmount($json_obj->items, $conn, $customid),
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

