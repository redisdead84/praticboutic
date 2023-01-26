<?php

require '../vendor/autoload.php';

include "config/common_cfg.php";
include "param.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $bdd);
// Check connection
if ($conn->connect_error) 
  die("Connection failed: " . $conn->connect_error); 

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
  $arr = array();
  if (isset($input->sessionid))
    session_id($input->sessionid);
  session_start();

  if (!isset($_SESSION))
  {
    throw new Error('Session expirée');
  }

  if (strcmp($input->requete, "categories") == 0)
  {
    $query = 'SELECT catid, nom, visible FROM categorie WHERE customid = ' . $input->bouticid . ' OR catid = 0 ORDER BY catid';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        $arm = array();
        array_push($arm, $row[0], $row[1], $row[2]);
        array_push($arr, $arm);
      }
    }
  }
  
  if (strcmp($input->requete, "articles") == 0)
  {
    $query = 'SELECT artid, nom, prix, unite, description, image FROM article WHERE customid = ' . $input->bouticid . ' AND visible = 1 AND catid = ' . $input->catid . ' ORDER BY artid';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        $arm = array();
        array_push($arm, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
        array_push($arr, $arm);
      }
    }
  }
  
  if (strcmp($input->requete, "groupesoptions") == 0)
  {
    $query = 'SELECT groupeopt.grpoptid, groupeopt.nom, groupeopt.multiple FROM relgrpoptart, groupeopt WHERE relgrpoptart.customid = ' . $input->bouticid . ' AND groupeopt.customid = ' . $input->bouticid . ' AND relgrpoptart.visible = 1 AND groupeopt.visible = 1 AND artid = ' . $input->artid . ' AND relgrpoptart.grpoptid = groupeopt.grpoptid ORDER BY groupeopt.grpoptid';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        $arm = array();
        array_push($arm, $row[0], $row[1], $row[2]);
        array_push($arr, $arm);
      }
    }
  }
  
  if (strcmp($input->requete, "options") == 0)
  {
    $query = 'SELECT optid, nom, surcout FROM `option` WHERE customid = ' . $input->bouticid . ' AND visible = 1 AND grpoptid = ' . $input->grpoptid . ' ORDER BY optid';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        $arm = array();
        array_push($arm, $row[0], $row[1], $row[2]);
        array_push($arr, $arm);
      }
    }
  }
  
  if (strcmp($input->requete,"getBouticInfo") == 0)
  {
    $reqci = $conn->prepare('SELECT customid, logo, nom FROM customer WHERE customer = ?');
    $reqci->bind_param("s", $input->customer);
    $reqci->execute();
    $reqci->bind_result($customid, $logo, $nom);
    $resultatci = $reqci->fetch();
    $reqci->close();
    array_push($arr, $customid, $logo, $nom);
  }
  
  if (strcmp($input->requete,"getClientInfo") == 0)
  {
    $reqci = $conn->prepare('SELECT CU.customid, CU.nom, CL.adr1, CL.adr2, CL.cp, CL.ville, CU.logo FROM customer CU, client CL WHERE CU.customer = ? AND CL.cltid = CU.cltid LIMIT 1');
    //error_log($customer);
    $reqci->bind_param("s", $input->customer);
    $reqci->execute();
    $reqci->bind_result($customid, $nom, $adresse1, $adresse2, $codepostal, $ville,  $logo);
    $resultatci = $reqci->fetch();
    $reqci->close();
     
    $adr = $nom . ' ' . $adresse1 . ' ' . $adresse2 . ' ' . $codepostal . ' ' . $ville;
    array_push($arr, $customid, $nom, $adr, $logo);
  }
  
  if (strcmp($input->requete, "images") == 0)
  {
    $query = 'SELECT image FROM artlistimg WHERE customid = ' . $input->bouticid . ' AND visible = 1 AND artid = ' . $input->artid . ' ORDER BY favori DESC, artlistimgid ASC';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        $arm = array();
        array_push($arm, $row[0]);
        array_push($arr, $arm);
      }
    }
  }


  if (strcmp($input->requete, "aboactif") == 0)
  {

    $reqai = $conn->prepare('SELECT client.stripe_customer_id FROM abonnement, client WHERE abonnement.bouticid = ? AND abonnement.cltid = client.cltid LIMIT 1');
    $reqai->bind_param("i", $input->bouticid);
    $reqai->execute();
    $reqai->bind_result($stripe_customer_id);
    $resultataci = $reqai->fetch();
    $reqai->close();
    if (strcmp($stripe_customer_id, "") == 0 )
    {
      throw new Error('Impossible de récupérer l\'identifiant Stripe de la boutic');
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
    $arr = $subscriptions->data;
  }
  
  if (strcmp($input->requete, "initSession") == 0)
  {
    $_SESSION['customer'] = $input->customer;
    $_SESSION[$input->customer . '_mail'] = "non";
    $_SESSION['method'] = htmlspecialchars(isset($input->method) ? $input->method : '3');
    $_SESSION['table'] = htmlspecialchars(isset($input->table) ? $input->table : '0');
  }
  
  if (strcmp($input->requete, "getSession") == 0)
  {
    $customer = $_SESSION['customer'];
    $mail = $_SESSION[$customer . '_mail'];
    $method = $_SESSION['method'];
    $table = $_SESSION['table'];
    $arm = array();
    array_push($arr, $customer, $mail, $method, $table);
  }

  
  
  $conn->close();
  $output = $arr;
  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
  