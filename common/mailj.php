
<?php

session_start();

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function

//Load composer's autoloader
require '../vendor/autoload.php';
include "config/common_cfg.php";
include "param.php";
  
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Stripe\Stripe;
use Ramsey\Uuid\Uuid;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try 
{

  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);

  //error_log($json_str);
	$customer = htmlspecialchars($json_obj->customer);
  
  if (strcmp($customer, "") == 0)
	{
	  throw new Exception("Client Vide");
	}
	
	if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
	{
	  throw new Exception("Courriel déjà envoyé");
	}

  $conn = new mysqli($servername, $username, $password, $bdd);

  if ($conn->connect_error) 
 		die("Connection failed: " . $conn->connect_error);
 		
  $reqci = $conn->prepare('SELECT customid, nom, courriel FROM customer WHERE customer = ?');
  $reqci->bind_param("s", $customer);
  $reqci->execute();
  $reqci->bind_result($customid, $nom, $courriel);
  $resultatci = $reqci->fetch();
  $reqci->close();

	$validsms = GetValeurParam("VALIDATION_SMS", $conn, $customid, "0");
	
	//error_log($validsms);
	//error_log("ok");

  //Server settings
  $mail->SMTPDebug = 0;                                 // Enable verbose debug output
  $mail->isSMTP();                                      // Set mailer to use SMTP
	
  //$host = GetValeurParam("Host_mail", $conn, $customid);
  $mail->Host = $host;  // Specify main and backup SMTP servers
  
  //$smtpa = GetValeurParam("SMTPAuth_mail", $conn, $customid);
  $mail->SMTPAuth = $smtpa;                               // Enable SMTP authentication
  
  //$user = GetValeurParam("Username_mail", $conn, $customid);
  $mail->Username = $user;                 // SMTP username
  
  //$pwd = GetValeurParam("Password_mail", $conn, $customid);
  $mail->Password = $pwd;                               // SMTP password
  
  //$ssec = GetValeurParam("SMTPSecure_mail", $conn, $customid);
  $mail->SMTPSecure = $ssec;                            // Enable TLS encryption, `ssl` also accepted

  //$port = GetValeurParam("Port_mail", $conn, $customid);
  $mail->Port = $port;                                    // TCP port to connect to
  
  //$chars = GetValeurParam("CharSet_mail", $conn, $customid);
  $mail->CharSet = $chars;

  //Recipients
  $mail->setFrom($sendmail, $sendnom);
 
	if (strcmp($courriel, "") == 0)
		$courriel = $maildef;  
	if (strcmp($nom, "") == 0)
		$nom = "Ma PraticBoutic";
  
  $mail->addAddress($courriel, $nom);     // Add a recipient

  //Content
  $isHTML = GetValeurParam("isHTML_mail", $conn, $customid, "TRUE");
  $mail->isHTML($isHTML);                                  // Set email format to HTML

  $subject = GetValeurParam("Subject_mail", $conn, $customid, "Nouvelle commande via PraticBoutic");
  $mail->Subject = $subject;
  
  $tel_mobile = $json_obj->telephone;
  $text = '<!DOCTYPE html>';
  $text = $text . '<html>';
  $text = $text . '<head>';
  $text = $text . '<link href=\'https://fonts.googleapis.com/css?family=Sans\' rel=\'stylesheet\'>';
  $text = $text . '</head>';
  $text = $text . '<body>';
  if ($json_obj->method == '1') 
  {
    $text = $text . '<p style="font-family: \'Sans\'"><b>Vente : </b>Consomation sur place<br></p>';
    $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
    $text = $text . '<p style="font-family: \'Sans\'"><b>Commande table numéro : </b> ' . $json_obj->table . '<br></p>';
    $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
    $text = $text . '<p style="font-family: \'Sans\'"><b>Téléphone : </b>' . $json_obj->telephone . '<br></p>';
    $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
  }
  if ($json_obj->method == '3') 
  {
    if (strcmp($json_obj->vente, "EMPORTER") == 0)
    {
      $text = $text . '<p style="font-family: \'Sans\'"><b>Vente : </b> A emporter<br></p>';
      $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
    }
    if (strcmp($json_obj->vente, "LIVRER") == 0)
    {
      $text = $text . '<p style="font-family: \'Sans\'"><b>Vente : </b> A livrer<br></p>';
    	$text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
    }
    if (strcmp($json_obj->paiement, "COMPTANT") == 0)
    {
      $text = $text . '<p style="font-family: \'Sans\'"><b>Paiement : </b> Au comptant<br></p>';
    	$text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
    }
    if (strcmp($json_obj->paiement, "LIVRAISON") == 0)
    {
      $text = $text . '<p style="font-family: \'Sans\'"><b>Paiement : </b> A la livraison<br></p>';
      $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
    }
    $text = $text . '<p style="font-family: \'Sans\'"><b>Nom du client : </b>' . htmlspecialchars($json_obj->nom) . ' ' . htmlspecialchars($json_obj->prenom) . '<br></p>';
    $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">'; 
    $text = $text . '<p style="font-family: \'Sans\'"><b>Téléphone : </b>' . $json_obj->telephone . '<br></p>';
    $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
    
    if (strcmp($json_obj->vente, "LIVRER") == 0)
    {
      $text = $text . '<p style="font-family: \'Sans\'"><b>Adresse (ligne1) : </b>' . htmlspecialchars($json_obj->adresse1) . '</p><hr style="width:50%;text-align:left;margin-left:0">';
      $text = $text . '<p style="font-family: \'Sans\'"><b>Adresse (ligne2) : </b>' . htmlspecialchars($json_obj->adresse2) . '</p><hr style="width:50%;text-align:left;margin-left:0">';
      $text = $text . '<p style="font-family: \'Sans\'"><b>Code Postal : </b>' . $json_obj->codepostal . '<br><hr style="width:50%;text-align:left;margin-left:0"></p>';
      $text = $text . '<p style="font-family: \'Sans\'"><b>Ville : </b>' . htmlspecialchars($json_obj->ville) . '<br><hr style="width:50%;text-align:left;margin-left:0"></p>';
    }
  }
  
  $text = $text . '<p style="font-family: \'Sans\'"><b>Information complémentaire : </b>';
  $text = $text . nl2br(stripslashes(strip_tags($json_obj->infosup))) . '</p>';
  $text = $text . '<hr style="border: 3px solid black;margin-top:15px;margin-bottom:25px;width:50%;text-align:left;margin-left:0">';
  
  $val=0;
  $sum = 0;
   
  $text = $text . '<p style="font-size:130%;margin-bottom:25px;font-family: \'Sans\'"><b>Détail de la commande : </b><br></p>';
  $numitems = count($json_obj->items);
  $i = 0;
  foreach( $json_obj->items as $value) 
	{
		$i++;
		$text = $text . '<p style="font-family: \'Sans\'">';
		$text = $text . 'Ligne ' . $i . '<br>';
		$text = $text . '<b>' . htmlspecialchars($value->name) . '</b><br>';
		$text = $text . $value->qt . ' x ' . number_format($value->prix, 2, ',', ' ') . htmlspecialchars($value->unite) . '<br>';
		$text = $text . $value->opts;
		$text = $text . '<i>' . nl2br(stripslashes(strip_tags(htmlspecialchars($value->txta)))) . '</i>';
		$text = $text . '</p>';
		if($i !== $numitems) 
	    $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
		
		$val = $val + $value->qt;
		$sum = $sum + $value->prix * $value->qt;
	}
	$text = $text . '<hr style="border: 3px solid black;margin-top:15px;margin-bottom:25px;width:50%;text-align:left;margin-left:0">';
	if (strcmp($json_obj->vente, "LIVRER") !== 0)
	{
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'"><b>Total Commande : ' . number_format($sum, 2, ',', ' ') . '€ </b><br></p>';
  }
  else 
  {
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'">Sous-total Commande : ' . number_format($sum, 2, ',', ' ') . '€ <br></p>';
  	$text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'">Frais de Livraison : ' . number_format($json_obj->fraislivr, 2, ',', ' ') . '€ <br></p>';
  	$text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'"><b>Total Commande : ' . number_format($sum + $json_obj->fraislivr, 2, ',', ' ') . '€ </b><br></p>';
  }
  
  $text = $text . '</body>';
  $text = $text . '</html>';

  $mail->Body = $text;
  
  //$mail->send();
  if(!$mail->send()) {
    throw new Exception('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
  }
   
	$compt = 0;
	// todo insertion 
	$qcpt = 'SELECT valeur FROM parametre WHERE customid = "' . $customid . '" AND nom = "CMPT_CMD" FOR UPDATE';
	if ($result = $conn->query($qcpt)) 
	{
		$row = $result->fetch_row();
  	if ($row != NULL) 
  		$compt = $row[0] + 1;
 	  else
			throw new Exception("Problème compteur commande");
	}

	$qcptu = "UPDATE parametre SET valeur = '$compt' WHERE customid = '$customid' AND nom = 'CMPT_CMD'";
	if ($conn->query($qcptu) == FALSE) 
		throw new Exception("Problème maj compteur commande");

	// Remplir avec des zeros
	$compt = str_pad($compt, 10, "0", STR_PAD_LEFT);
	
	$methodstr = "INCONNU";	
	
	if ($json_obj->method == 1)
		$methodstr = "ATABLE";
	if ($json_obj->method >= 2)
		$methodstr = "CLICKNCOLLECT";
	
  $qcmdi = "INSERT INTO commande (customid, numref, nom, prenom, telephone, adresse1, adresse2, codepostal, ville, vente, paiement, sstotal, fraislivraison, total, commentaire, method, `table`, datecreation, statid ) VALUES ('$customid','$compt','" . htmlspecialchars(addslashes($json_obj->nom)) . "',";
  $qcmdi = $qcmdi . "'" . htmlspecialchars(addslashes($json_obj->prenom)) . "','$json_obj->telephone','" . htmlspecialchars(addslashes($json_obj->adresse1)) . "','" . htmlspecialchars(addslashes($json_obj->adresse2)) . "','$json_obj->codepostal','" . htmlspecialchars(addslashes($json_obj->ville)) . "','$json_obj->vente','$json_obj->paiement','" . strval($sum) . "','" . strval($json_obj->fraislivr) . "',";
  $qcmdi = $qcmdi . "'" . strval(floatval($sum) + floatval($json_obj->fraislivr)) . "','" . nl2br(stripslashes(strip_tags(htmlspecialchars(addslashes($json_obj->infosup))))) . "','$methodstr','$json_obj->table', NOW(), (SELECT statid FROM statutcmd WHERE customid = '$customid' AND defaut = 1 LIMIT 1)) ";
  
  //error_log($qcmdi);			
  
  if ($conn->query($qcmdi)== FALSE) 
  {
		//error_log("Error: " . $qcmdi . "<br>" . $conn->error);
  	throw new Exception("Error: " . $qcmdi . "<br>" . $conn->error);
  }
  	
  // todo insertion 
	$qcpt = "SELECT cmdid FROM commande WHERE customid = '$customid' AND numref = '$compt'";
	if ($result = $conn->query($qcpt)) 
	{
		$row = $result->fetch_row();
  	if ($row != NULL) 
  		$cmdid = $row[0];
 	  else
			throw new Exception("Erreur recuperation id commande");
	}
    
  $ordre = 0;
  foreach( $json_obj->items as $value) 
	{
		$ordre++;
		$artid = 0;
		$optid = 0;
		
		if (strcmp($value->type, "article") == 0 )
			$artid = $value->id;
		else if (strcmp($value->type, "option") == 0)
			$optid = $value->id;
		
		$qlncmdi = "INSERT INTO lignecmd (customid, cmdid, ordre, type, nom, prix, quantite, commentaire, artid, optid ) VALUES ('$customid', '$cmdid','" . $ordre . "','$value->type',";
		$qlncmdi = $qlncmdi . "'" . htmlspecialchars($value->name) . "', '$value->prix', '$value->qt', '" . nl2br(stripslashes(strip_tags(htmlspecialchars($value->txta)))) . "', '$artid', '$optid')"; 
		
		//error_log($qlncmdi);		
		
		if ($conn->query($qlncmdi)== FALSE) 
  		throw new Exception("Error: " . $qlncmdi . "<br>" . $conn->error);

	}
	//error_log("balise1");
	
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
  
	$stq = "SELECT aboid, stripe_subscription_id FROM abonnement WHERE bouticid = " . $customid . " AND actif = 1 LIMIT 1";
  if ($result = $conn->query($stq)) 
  { 
    while ($row = $result->fetch_row()) 
    {
      $subscription = $stripe->subscriptions->retrieve($row[1]);
      // Recuperation de la souscription grace à de l'id subscription stripe 
      //error_log($subscription->items->data[0]->id);
      $subscription_items = $stripe->subscriptionItems->all([
        'subscription' => $subscription,
      ]);
      //error_log(print_r($subscription_items, TRUE));
      //error_log(count($subscription_items->data));
      for ($j = 0; $j < count($subscription_items->data); $j++)
      {
        $subscription_item = $subscription_items->data[$j];
        //error_log(print_r($subscription_item, TRUE));
        $metered = $subscription_item->price->recurring->usage_type;
        //error_log(print_r($subscription_item->price->recurring, TRUE));
        //error_log($metered);
        if (strcmp($metered, "metered") == 0)
        {
          //error_log('metered2');
          //$price = $subscription_item->price->unit_amount;
          //error_log($price);
          $usage_quantity = $sum;// * $price;
          //error_log($usage_quantity);
          $action = 'set';
          
          $date = date_create();
          $timestamp = date_timestamp_get($date);
          // The idempotency key allows you to retry this usage record call if it fails.
          $idempotency_key = Uuid::uuid4()->toString();
          
          //error_log($subscription_item->id);
          //error_log($usage_quantity);
          //error_log($timestamp);
          //error_log($action);
          //error_log($idempotency_key);
          $subscription_item_id = $subscription_item->id;
          try {
            $stripe->subscriptionItems->createUsageRecord(
              $subscription_item_id,
              [
                'quantity' => intval($usage_quantity),
                'timestamp' => $timestamp,
                'action' => $action,
              ],
              [
                'idempotency_key' => $idempotency_key,
              ]
            );
          } catch (\Stripe\Exception\ApiErrorException $error) {
            //error_log("test1");
            //error_log(print_r($error, TRUE));
            throw new Exception("Usage report failed for item ID $subscription_item_id with idempotency key $idempotency_key: $error.toString()");
          }
        }
      }
    }
    $result->close();
  }
	
	
	if (strcmp($validsms,"1") == 0)
	{
				
		$query = 'SELECT commande.telephone, statutcmd.message, commande.numref, commande.nom, commande.prenom, commande.adresse1, commande.adresse2, commande.codepostal, commande.ville, ';
  	$query = $query . 'commande.vente, commande.paiement, commande.sstotal, commande.fraislivraison, commande.total, commande.commentaire, statutcmd.etat, customer.nom FROM commande ';
  	$query = $query . 'INNER JOIN statutcmd ON commande.statid = statutcmd.statid '; 
  	$query = $query . 'INNER JOIN customer ON commande.customid = statutcmd.customid '; 
  	$query = $query . 'WHERE statutcmd.defaut = 1 AND commande.cmdid = ' . $cmdid . ' AND commande.customid = ' . $customid . ' AND statutcmd.customid = ' . $customid . ' AND customer.customid = ' . $customid;
  	$query = $query . ' ORDER BY commande.cmdid LIMIT 1';

  	//error_log($query);
		
		$message = "";
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
	  		$content = $row[1];  	
				$content = str_replace("%boutic%", $row[16], $content);
				$content = str_replace("%telephone%", $row[0], $content);		
				$content = str_replace("%numref%", $row[2], $content);  
				$content = str_replace("%nom%", $row[3], $content);  
				$content = str_replace("%prenom%", $row[4], $content);
				$content = str_replace("%adresse1%", $row[5], $content);		
				$content = str_replace("%adresse2%", $row[6], $content);
				$content = str_replace("%codepostal%", $row[7], $content);
				$content = str_replace("%ville%", $row[8], $content);
				$content = str_replace("%vente%", $row[9], $content);
				$content = str_replace("%paiement%", $row[10], $content);
				$content = str_replace("%sstotal%", number_format($row[11], 2, ',', ' '), $content);
				$content = str_replace("%fraislivraison%", number_format($row[12], 2, ',', ' '), $content);
				$content = str_replace("%total%", number_format($row[13], 2, ',', ' '), $content);
				$content = str_replace("%commentaire%", $row[14], $content);
				$content = str_replace("%etat%", $row[15], $content);
		  	$message = $content;
	    }						
		  $result->close();
	  }   
		
    $numbers = array($tel_mobile);
    
    $recipients = array();
    foreach ($numbers as $n) {
      $recipients[] = array('value' => $n);
    }
    
    $postdata = array(
      'sms' => array(
       'message' => array(
        'text' => $message,
        'sender' => $sendersms
       ),
       'recipients' => array('gsm' => $recipients)
      )
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.smsfactor.com/send");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer ' . $tokensms));
    $response = curl_exec($ch);
    curl_close($ch);
	   
	}

  $_SESSION[$customer . '_mail'] = 'oui';
  
  $conn->close();
  echo json_encode("OK");
} 
catch (Exception $e) 
{
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}    
    
