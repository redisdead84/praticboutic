
<?php

session_start();

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require '../vendor/autoload.php';

header('Content-Type: application/json');

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try 
{
  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);

	$customer = htmlspecialchars($json_obj->customer);
	
	include "config/common_cfg.php";
	include "param.php";
		
	$method = htmlspecialchars(isset($_GET ['method']) ? $_GET ['method'] : '0');
	$table = htmlspecialchars(isset($_GET ['table']) ? $_GET ['table'] : '0');
	
	if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
	{
	  throw new Exception("Courriel déjà envoyé");
	}

  $conn = new mysqli($servername, $username, $password, $bdd);

  if ($conn->connect_error) 
 		die("Connection failed: " . $conn->connect_error);
 		
  $reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
  $reqci->bind_param("s", $customer);
  $reqci->execute();
  $reqci->bind_result($customid);
  $resultatci = $reqci->fetch();
  $reqci->close();

	$validsms = GetValeurParam("VALIDATION_SMS", $conn, $customid, "0");

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
  //$sendmail = GetValeurParam("Sendermail_mail", $conn, $customid);
  //$sendnom = GetValeurParam("Sendernom_mail", $conn, $customid);
  $mail->setFrom($sendmail, $sendnom);

  $rcvmail = GetValeurParam("Receivermail_mail", $conn, $customid, $maildef);
  $rcvnom = GetValeurParam("Receivernom_mail", $conn, $customid,"Ma PraticBoutic");
  $mail->addAddress($rcvmail, $rcvnom);     // Add a recipient
//  $mail->addAddress('ellen@example.com');               // Name is optional
//  $mail->addReplyTo('info@example.com', 'Information');
//  $mail->addCC('cc@example.com');
//  $mail->addBCC('bcc@example.com');

  //Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

  //Content
  $isHTML = GetValeurParam("isHTML_mail", $conn, $customid, "TRUE");
  $mail->isHTML($isHTML);                                  // Set email format to HTML

  $subject = GetValeurParam("Subject_mail", $conn, $customid, "Une commande PraticBoutic");
  $mail->Subject = $subject;
  
  $tel_mobile = $json_obj->telephone;
  $text = '<!DOCTYPE html>';
  $text = $text . '<html>';
  $text = $text . '<body>';

  if ($json_obj->method == '1') 
  {
    $text = $text . '<h2>Consomation sur place<br><h2>';
    $text = $text . '<h3>Commande table numéro ' . $json_obj->table . '<br></h3>';
    $text = $text . '<h3>Téléphone: <br></h3>';
    $text = $text . '<h3>' . $json_obj->telephone . '<br></h3>';
    
  }
  /*if ($json_obj->method == '2') 
  {
    $text = $text . '<h2>Vente à emporter<br></h2>';
    $text = $text . '<h3>Commande pour ' . $json_obj->prenom . '<br></h3>';
  }*/
  if ($json_obj->method == '3') 
  {
    if (strcmp($json_obj->vente, "EMPORTER") == 0)
      $text = $text . '<h3>Vente à emporter<br></h3>';
    if (strcmp($json_obj->vente, "LIVRER") == 0)
      $text = $text . '<h3>Vente à livrer<br></h3>';
    if (strcmp($json_obj->paiement, "COMPTANT") == 0)
      $text = $text . '<h3>Paiement au comptant<br></h3>';
    if (strcmp($json_obj->paiement, "LIVRAISON") == 0)
      $text = $text . '<h3>Paiement à la livraison<br></h3>';
    $text = $text . '<h3>Client: <br></h3>';
    $text = $text . '<h3>' . htmlspecialchars($json_obj->nom) . '<br>' . htmlspecialchars($json_obj->prenom) . '<br>' . $json_obj->telephone . '<br></h3>';
    if (strcmp($json_obj->vente, "LIVRER") == 0)
    {   
      $text = $text . '<h3>Adresse de livraison: <br></h3>';
      $text = $text . '<h3>' . htmlspecialchars($json_obj->adresse1) . '<br>' . htmlspecialchars($json_obj->adresse2) . '<br>' . $json_obj->codepostal . '<br>' . htmlspecialchars($json_obj->ville) . '<br></h3>';
    }
  }
  
  $text = $text . '<h3>Information suplémentaire: <br></h3>';
  $text = $text . '<h3>' . nl2br(stripslashes(strip_tags($json_obj->infosup))) . '</h3>';
  
  $val=0;
  $sum = 0;
   
  $text = $text . '<h3>Commande: <br></h3>';
  foreach( $json_obj->items as $value) 
	{
			$text = $text . '<h3>' . htmlspecialchars($value->name) . ' : ' . $value->qt . ' x ' . number_format($value->prix, 2, ',', ' ') . htmlspecialchars($value->unite) . '<br>' . $value->opts . '</h3>';
			$text = $text . '<a><i>' . nl2br(stripslashes(strip_tags(htmlspecialchars($value->txta)))) . '</i></a>';
			$val = $val + $value->qt;
			$sum = $sum + $value->prix * $value->qt;
	}
	if (strcmp($json_obj->vente, "LIVRER") !== 0)
  	$text = $text . '<h2>Total Commande : ' . number_format($sum, 2, ',', ' ') . '€ <br><h2>';
  else 
  {
  	$text = $text . '<h2>Sous-total Commande : ' . number_format($sum, 2, ',', ' ') . '€ <br><h2>';
  	$text = $text . '<h2>Frais de Livraison : ' . number_format($json_obj->fraislivr, 2, ',', ' ') . '€ <br><h2>';
  	$text = $text . '<h2>Total Commande : ' . number_format($sum + $json_obj->fraislivr, 2, ',', ' ') . '€ <br><h2>';
  }
  
	/*if (strcmp($validsms,"1") == 0)
	{
	  $text = $text . '<br>';
		$text = $text . '<a href="https://api.smsfactor.com/send?text=Votre commande a été validée.&to=' . $tel_mobile . '&token=' . $tokensms . '&sender=' . $sendersms . '">Accepter la commande</a>';
		$text = $text . '<br>';
		$text = $text . '<a href="https://api.smsfactor.com/send?text=Votre commande a été rejetée.&to=' . $tel_mobile . '&token=' . $tokensms . '&sender=' . $sendersms . '">Rejeter la commande</a>';
	}*/
	
  $text = $text . '</body>';
  $text = $text . '</html>';

  $mail->Body = $text;
  
  $mail->send();
  
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
		error_log("Error: " . $qcmdi . "<br>" . $conn->error);
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
	
	if (strcmp($validsms,"1") == 0)
	{
		//$content = GetValeurParam("TEXT_SMS", $conn, $customid);
		
		$query = 'SELECT commande.telephone, statutcmd.message, commande.numref, commande.nom, commande.prenom, commande.adresse1, commande.adresse2, commande.codepostal, commande.ville,
  						commande.vente, commande.paiement, commande.sstotal, commande.fraislivraison, commande.total, commande.commentaire, statutcmd.etat  FROM commande ';
  	$query = $query . 'INNER JOIN statutcmd ON commande.statid = statutcmd.statid '; 
  	$query = $query . 'WHERE statutcmd.defaut = 1 AND commande.cmdid = ' . $cmdid . ' AND commande.customid = ' . $customid . ' AND statutcmd.customid = ' . $customid;
  	$query = $query . ' ORDER BY commande.cmdid LIMIT 1';

  	//error_log($query);
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
	  		$content = $row[1];  	
				$content = str_replace("%boutic%", $rcvnom, $content);
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
  
} 
catch (Exception $e) 
{
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}    
    
