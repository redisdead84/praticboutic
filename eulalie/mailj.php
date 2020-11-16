
<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require '../vendor/autoload.php';

include "config/config.php";
include "param.php";

$method = isset($_GET ['method']) ? $_GET ['method'] : '0';
$table = isset($_GET ['table']) ? $_GET ['table'] : '0';

session_start();

if (strcmp($_SESSION['mail'],'oui') == 0)
{
  header('LOCATION: carte.php?method=' . $method . '&table=' . $table);
  exit();
}

header('Content-Type: application/json');

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try 
{
		
		
  $conn = new mysqli($servername, $username, $password, $bdd);

  if ($conn->connect_error) 
 		die("Connection failed: " . $conn->connect_error);

  //Server settings
  $mail->SMTPDebug = 0;                                 // Enable verbose debug output
  $mail->isSMTP();                                      // Set mailer to use SMTP
	
	$host = GetValeurParam("Host_mail", $conn);
  $mail->Host = $host;  // Specify main and backup SMTP servers
  
  $smtpa = GetValeurParam("SMTPAuth_mail", $conn);
  $mail->SMTPAuth = $smtpa;                               // Enable SMTP authentication
  
  $user = GetValeurParam("Username_mail", $conn);
  $mail->Username = $user;                 // SMTP username
  
  $pwd = GetValeurParam("Password_mail", $conn);
  $mail->Password = $pwd;                               // SMTP password
  
  $ssec = GetValeurParam("SMTPSecure_mail", $conn);
  $mail->SMTPSecure = $ssec;                            // Enable TLS encryption, `ssl` also accepted

  $port = GetValeurParam("Port_mail", $conn);
  $mail->Port = $port;                                    // TCP port to connect to
  
  $chars = GetValeurParam("CharSet_mail", $conn);
  $mail->CharSet = $chars;

  //Recipients
  $sendmail = GetValeurParam("Sendermail_mail", $conn);
  $sendnom = GetValeurParam("Sendernom_mail", $conn);
  $mail->setFrom($sendmail, $sendnom);

  $rcvmail = GetValeurParam("Receivermail_mail", $conn);
  $rcvnom = GetValeurParam("Receivernom_mail", $conn);
  $mail->addAddress($rcvmail, $rcvnom);     // Add a recipient
//  $mail->addAddress('ellen@example.com');               // Name is optional
//  $mail->addReplyTo('info@example.com', 'Information');
//  $mail->addCC('cc@example.com');
//  $mail->addBCC('bcc@example.com');

  //Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

  //Content
  $isHTML = GetValeurParam("isHTML_mail", $conn);
  $mail->isHTML($isHTML);                                  // Set email format to HTML

  $subject = GetValeurParam("Subject_mail", $conn);
  $mail->Subject = $subject;
  
  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);
  $tel_mobile = $json_obj->telephone;
  $text = '<!DOCTYPE html>';
  $text = $text . '<html>';
  $text = $text . '<body>';

  if ($json_obj->method == '1') 
  {
    $text = $text . '<h2>Consomation sur place<br><h2>';
    $text = $text . '<h3>Commande table numéro ' . $json_obj->table . '<br></h3>';
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
    $text = $text . '<h3>' . $json_obj->nom . '<br>' . $json_obj->prenom . '<br>' . $json_obj->telephone . '<br></h3>';
    if (strcmp($json_obj->vente, "LIVRER") == 0)
    {   
      $text = $text . '<h3>Adresse de livraison: <br></h3>';
      $text = $text . '<h3>' . $json_obj->adresse1 . '<br>' . $json_obj->adresse2 . '<br>' . $json_obj->codepostal . '<br>' . $json_obj->ville . '<br></h3>';
    }
  }
  
  $text = $text . '<h3>Information suplémentaire: <br></h3>';
  $text = $text . '<h3>' . nl2br(stripslashes(strip_tags($json_obj->infosup))) . '</h3>';
  
  $val=0;
  $sum = 0;
   
  $text = $text . '<h3>Commande: <br></h3>';
  foreach( $json_obj->items as $value) 
	{
			$text = $text . '<h3>' . $value->name . ' : ' . $value->qt . ' x ' . number_format($value->prix, 2, ',', ' ') . $value->unite . '<br>' . $value->opts . '</h3>';
			$text = $text . '<a><i>' . nl2br(stripslashes(strip_tags($value->txta))) . '</i></a>';
			$val = $val + $value->qt;
			$sum = $sum + $value->prix * $value->qt;
	}
	
  $text = $text . '<h2>Total Commande : ' . number_format($sum, 2, ',', ' ') . '€ <br><h2>';
	
  $text = $text . '</body>';
  $text = $text . '</html>';

  $mail->Body = $text;
  
  $mail->send();

	$validsms = GetValeurParam("VALIDATION_SMS", $conn);
	if (strcmp($validsms,"1") == 0)
	{
    $token = GetValeurParam("TOKEN_SMS", $conn);

    $content = 'Vote commande d\'un montant de ' . number_format($sum, 2, ',', ' ') . ' € a été transmise.'; 
    $numbers = array($tel_mobile);
    $sender = $sendnom;
    $recipients = array();
    foreach ($numbers as $n) {
      $recipients[] = array('value' => $n);
    }

    $postdata = array(
      'sms' => array(
       'message' => array(
        'text' => $content,
        'sender' => $sender
       ),
       'recipients' => array('gsm' => $recipients)
      )
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.smsfactor.com/send");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer ' . $token));
    $response = curl_exec($ch);
    curl_close($ch);
	   
	}

  $_SESSION['mail'] = 'oui';
  
  $conn->close();
  
} 
catch (Exception $e) 
{
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}    
    
