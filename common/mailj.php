
<?php

session_start();

if (empty($_SESSION['customer']) != 0)
{
  header('LOCATION: error.php?code=nocustomer');
  exit();
}

$customer = $_SESSION['customer'];
$method = $_SESSION['method'];
$table = $_SESSION['table'];

if (empty($_SESSION[$customer . '_mail']) == TRUE)
{
  header('LOCATION: error.php?code=noemail');
  exit();
}

if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
{
  header('LOCATION: error.php?code=alreadysent');
  exit();
}

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

  $mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
      )
  );

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

  $text = '<!DOCTYPE html>';
  $text = $text . '<html>';
  $text = $text . '<head>';
  $text = $text . '<link href=\'https://fonts.googleapis.com/css?family=Public+Sans\' rel=\'stylesheet\'>';
  $text = $text . '</head>';
  $text = $text . '<body>';
  $text = $text . '<svg version="1.2" baseProfile="tiny" id="Calque_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 295.7 51.8" width="300" height="51.057" overflow="visible" xml:space="preserve">
  <g>
  	<path fill="none" d="M205.2,14.5c-3.2,0-5.2,2.5-5.2,5.5v0.1c0,3,2.1,5.6,5.2,5.6c3.2,0,5.2-2.5,5.2-5.5v-0.1
  		C210.5,17.1,208.3,14.5,205.2,14.5z"></path>
  	<path fill="none" d="M183.1,14.4c-2.6,0-4.8,2.2-4.8,5.6v0.1c0,3.3,2.2,5.6,4.8,5.6c2.6,0,4.9-2.2,4.9-5.6V20
  		C187.9,16.7,185.7,14.4,183.1,14.4z"></path>
  	<path fill="none" d="M87.5,14.4c-2.6,0-4.8,2.2-4.8,5.6v0.1c0,3.3,2.2,5.6,4.8,5.6c2.6,0,4.9-2.2,4.9-5.6V20
  		C92.4,16.7,90.2,14.4,87.5,14.4z"></path>
  	<path fill="none" d="M124,22c-1-0.5-2.2-0.8-3.6-0.8c-2.4,0-3.9,1-3.9,2.8v0.1c0,1.5,1.3,2.4,3.1,2.4c2.6,0,4.4-1.5,4.4-3.5L124,22
  		L124,22z"></path>
  	<path fill="#E1007A" d="M98.1,20c0-6.7-4.4-10.4-9.1-10.4c-2.9,0-4.8,1.4-6.2,3.3v-0.3c0-1.6-1.3-2.9-2.8-2.9
  		c-1.6,0-2.8,1.3-2.8,2.9v20.9c0,1.6,1.3,2.9,2.8,2.9c1.6,0,2.8-1.3,2.8-2.9v-6c1.3,1.6,3.2,3,6.2,3C93.7,30.5,98.1,26.8,98.1,20
  		L98.1,20z M92.4,20.1c0,3.4-2.2,5.6-4.9,5.6c-2.6,0-4.8-2.2-4.8-5.6V20c0-3.3,2.2-5.6,4.8-5.6C90.2,14.5,92.4,16.7,92.4,20.1
  		L92.4,20.1z"></path>
  	<path fill="#E1007A" d="M112.1,12.5c0-1.6-1-2.8-2.8-2.8c-1.7,0-3,1.8-3.8,3.8v-0.9c0-1.6-1.3-2.9-2.8-2.9c-1.6,0-2.8,1.3-2.8,2.9
  		v14.9c0,1.6,1.3,2.9,2.8,2.9c1.6,0,2.8-1.3,2.8-2.9v-5.3c0-4.1,1.6-6.3,4.5-7C111.2,14.9,112.1,14,112.1,12.5z"></path>
  	<path fill="#E1007A" d="M123.9,28c0,1.2,1.1,2.4,2.7,2.4c1.5,0,2.8-1.2,2.8-2.7v-9.2c0-2.7-0.7-4.9-2.2-6.4
  		c-1.4-1.4-3.6-2.3-6.7-2.3c-2.6,0-4.6,0.4-6.4,1.1c-0.9,0.3-1.5,1.2-1.5,2.2c0,1.3,1,2.3,2.3,2.3c0.3,0,0.5,0,0.8-0.1
  		c1.1-0.3,2.3-0.6,3.9-0.6c2.8,0,4.3,1.3,4.3,3.7v0.3c-1.4-0.5-2.9-0.8-4.9-0.8c-4.7,0-8,2-8,6.4v0.1c0,4,3.1,6.2,6.9,6.2
  		C120.6,30.5,122.5,29.5,123.9,28L123.9,28z M119.6,26.6c-1.8,0-3.1-0.9-3.1-2.4V24c0-1.8,1.5-2.8,3.9-2.8c1.4,0,2.6,0.3,3.6,0.8v1
  		C124,25.1,122.2,26.6,119.6,26.6z"></path>
  	<path fill="#E1007A" d="M140.8,14.9c1.3,0,2.4-1.1,2.4-2.4c0-1.4-1.1-2.4-2.4-2.4h-2.5V7.5c0-1.6-1.3-2.9-2.8-2.9
  		c-1.6,0-2.8,1.3-2.8,2.9V10h-0.2c-1.3,0-2.4,1.1-2.4,2.4c0,1.4,1.1,2.4,2.4,2.4h0.2v9.6c0,4.7,2.3,6.1,5.8,6.1
  		c1.2,0,2.2-0.2,3.2-0.6c0.8-0.3,1.5-1.1,1.5-2.1c0-1.3-1.1-2.4-2.3-2.4c-0.1,0-0.5,0-0.7,0c-1.3,0-1.8-0.6-1.8-2v-8.6L140.8,14.9
  		L140.8,14.9z"></path>
  	<path fill="#E1007A" d="M151.1,5.2c0-1.7-1.4-2.7-3.2-2.7s-3.2,1-3.2,2.7v0.1c0,1.7,1.4,2.7,3.2,2.7S151.1,6.9,151.1,5.2L151.1,5.2
  		z"></path>
  	<path fill="#E1007A" d="M147.9,9.7c-1.6,0-2.8,1.3-2.8,2.9v14.9c0,1.6,1.3,2.9,2.8,2.9s2.8-1.3,2.8-2.9V12.6
  		C150.7,11,149.5,9.7,147.9,9.7z"></path>
  	<path fill="#E1007A" d="M163.1,30.6c3.3,0,5.4-1.1,7.1-2.6c0.5-0.5,0.8-1.1,0.8-1.8c0-1.4-1-2.4-2.4-2.4c-0.7,0-1.2,0.3-1.5,0.5
  		c-1.1,0.9-2.2,1.4-3.7,1.4c-3.1,0-5.1-2.5-5.1-5.6V20c0-3,2-5.5,4.8-5.5c1.5,0,2.5,0.5,3.5,1.3c0.3,0.3,0.9,0.6,1.6,0.6
  		c1.4,0,2.6-1.1,2.6-2.6c0-1-0.5-1.7-0.9-2c-1.7-1.4-3.8-2.3-6.8-2.3c-6.1,0-10.5,4.7-10.5,10.5v0.1
  		C152.7,25.9,157.1,30.6,163.1,30.6z"></path>
  	<path fill="#595959" d="M193.6,20.1L193.6,20.1c0-6.8-4.4-10.5-9.1-10.5c-2.9,0-4.8,1.4-6.2,3.3V5.3c0-1.6-1.3-2.9-2.8-2.9
  		c-1.6,0-2.8,1.3-2.8,2.9v22.2c0,1.6,1.3,2.9,2.8,2.9c1.6,0,2.8-1.3,2.8-2.8v0c1.3,1.6,3.2,3,6.2,3
  		C189.2,30.5,193.6,26.8,193.6,20.1z M187.9,20.1c0,3.4-2.2,5.6-4.9,5.6c-2.6,0-4.8-2.2-4.8-5.6V20c0-3.3,2.2-5.6,4.8-5.6
  		C185.7,14.5,187.9,16.7,187.9,20.1L187.9,20.1z"></path>
  	<path fill="#595959" d="M216.1,20.1L216.1,20.1c0-5.9-4.6-10.5-10.8-10.5c-6.2,0-10.8,4.7-10.8,10.5v0.1c0,5.8,4.6,10.4,10.8,10.4
  		C211.4,30.6,216.1,25.9,216.1,20.1z M210.5,20.2c0,3-1.9,5.5-5.2,5.5c-3.1,0-5.2-2.6-5.2-5.6V20c0-3,1.9-5.5,5.2-5.5
  		C208.3,14.5,210.5,17.1,210.5,20.2L210.5,20.2z"></path>
  	<path fill="#595959" d="M235.9,27.5V12.6c0-1.6-1.3-2.9-2.8-2.9s-2.8,1.3-2.8,2.9v8.6c0,2.7-1.4,4.1-3.5,4.1
  		c-2.2,0-3.4-1.4-3.4-4.1v-8.6c0-1.6-1.3-2.9-2.8-2.9c-1.6,0-2.8,1.3-2.8,2.9V23c0,4.6,2.5,7.5,6.8,7.5c2.9,0,4.5-1.5,5.8-3.2v0.2
  		c0,1.6,1.3,2.9,2.8,2.9S235.9,29.1,235.9,27.5z"></path>
  	<path fill="#595959" d="M250.1,27.8c0-1.3-1.1-2.4-2.3-2.4c-0.1,0-0.5,0-0.7,0c-1.3,0-1.8-0.6-1.8-2v-8.6h2.5
  		c1.3,0,2.4-1.1,2.4-2.4s-1.1-2.4-2.4-2.4h-2.5V7.5c0-1.6-1.3-2.9-2.8-2.9c-1.6,0-2.8,1.3-2.8,2.9V10h-0.2c-1.3,0-2.4,1.1-2.4,2.4
  		c0,1.4,1.1,2.4,2.4,2.4h0.2v9.6c0,4.7,2.3,6.1,5.8,6.1c1.2,0,2.2-0.2,3.2-0.6C249.4,29.6,250.1,28.8,250.1,27.8z"></path>
  	<path fill="#595959" d="M254.9,2.5c-1.8,0-3.2,1-3.2,2.7v0.1c0,1.7,1.4,2.7,3.2,2.7c1.8,0,3.2-1.1,3.2-2.7V5.2
  		C258,3.5,256.6,2.5,254.9,2.5z"></path>
  	<path fill="#595959" d="M254.9,9.7c-1.6,0-2.8,1.3-2.8,2.9v14.9c0,1.6,1.3,2.9,2.8,2.9c1.6,0,2.8-1.3,2.8-2.9V12.6
  		C257.7,11,256.4,9.7,254.9,9.7z"></path>
  	<path fill="#595959" d="M275.6,23.7c-0.7,0-1.2,0.3-1.5,0.5c-1.1,0.9-2.2,1.4-3.7,1.4c-3.1,0-5.1-2.5-5.1-5.6V20c0-3,2-5.5,4.8-5.5
  		c1.5,0,2.5,0.5,3.5,1.3c0.3,0.3,0.9,0.6,1.6,0.6c1.4,0,2.6-1.1,2.6-2.6c0-1-0.5-1.7-0.9-2c-1.7-1.4-3.8-2.3-6.8-2.3
  		c-6.1,0-10.5,4.7-10.5,10.5v0.1c0,5.8,4.4,10.4,10.4,10.4c3.3,0,5.4-1.1,7.1-2.6c0.5-0.5,0.8-1.1,0.8-1.8
  		C278,24.8,276.9,23.7,275.6,23.7z"></path>
  	<path fill="#595959" d="M279.9,27.1c-0.9,0-1.5,0.6-1.5,1.5v0.2c0,0.8,0.6,1.5,1.5,1.5c0.8,0,1.5-0.6,1.5-1.5v-0.2
  		C281.3,27.8,280.7,27.1,279.9,27.1z"></path>
  	<path fill="#595959" d="M287.1,16.8c0.2,0,0.4,0,0.5,0c0.6,0,1.1-0.5,1.1-1.1c0-0.6-0.4-1-0.9-1.1c-0.4-0.1-0.8-0.1-1.3-0.1
  		c-1.1,0-1.9,0.3-2.5,0.9c-0.6,0.6-0.9,1.5-0.9,2.7v0.8h-0.4c-0.6,0-1.1,0.5-1.1,1.1c0,0.6,0.5,1.1,1.1,1.1h0.4V29
  		c0,0.7,0.6,1.3,1.2,1.3c0.7,0,1.3-0.6,1.3-1.3v-7.8h1.9c0.6,0,1.1-0.5,1.1-1.1c0-0.6-0.5-1.1-1.1-1.1h-2v-0.6
  		C285.7,17.4,286.2,16.8,287.1,16.8z"></path>
  	<path fill="#595959" d="M294.4,18.9c-1.1,0-2.2,1.1-2.8,2.4v-1.1c0-0.7-0.6-1.3-1.3-1.3c-0.7,0-1.2,0.6-1.2,1.3V29
  		c0,0.7,0.6,1.3,1.2,1.3c0.7,0,1.3-0.6,1.3-1.3v-3.3c0-2.6,1.2-4,3-4.3c0.6-0.1,1-0.5,1-1.2C295.7,19.4,295.2,18.9,294.4,18.9z"></path>
  </g>
  <polygon fill="none" points="11.3,25.2 5.2,27.7 11.3,25.2 11.3,25.2 "></polygon>
  <polygon fill="none" points="59.2,15.4 59.4,5.2 26.3,11.2 34,26.4 "></polygon>
  <path fill="#595959" d="M11.8,13.9l9.6-1.7l9.1,18l0.6,8.8c0,0.8,0.5,1.5,1.1,1.9c0.4,0.2,0.8,0.4,1.2,0.4c0.3,0,0.6-0.1,0.9-0.2
  	l28.8-12.6c1.2-0.5,1.7-1.9,1.2-3.1c-0.5-1.2-1.9-1.8-3.1-1.2L35.6,35.3L35.4,31l27.1-11.8c0.9-0.4,1.4-1.2,1.4-2.2l0.2-14.6
  	c0-0.7-0.3-1.4-0.8-1.9c-0.5-0.5-1.2-0.7-1.9-0.5L11,9.2c-1.3,0.2-2.1,1.5-1.9,2.8C9.3,13.2,10.5,14.1,11.8,13.9z M59.4,5.2
  	l-0.2,10.2L34,26.4l-7.7-15.2L59.4,5.2z"></path>
  <path fill="#E1007A" d="M11.3,25.2C11.3,25.2,11.3,25.2,11.3,25.2L11.3,25.2l0.4-0.2c0.6-0.3,1.4,0.1,1.6,0.7
  	c0.3,0.6-0.1,1.4-0.7,1.6l0,0L0.8,32.2c-0.6,0.3-0.9,1-0.7,1.6c0.3,0.6,1,0.9,1.6,0.7l13.1-5.3c0.6-0.3,1.4,0.1,1.6,0.7
  	c0.3,0.6-0.1,1.4-0.7,1.6l-5.4,2.2l-2,0.8c-0.6,0.3-0.9,1-0.7,1.6c0.2,0.6,1,0.9,1.6,0.7l11.3-4.6c0,0,0,0,0,0
  	c2.5-1,4.2-1.7,4.2-1.7c0.9-0.4,1.3-1.4,1-2.3l-3.3-8.2c-0.4-0.9-1.4-1.4-2.3-1c0,0-4.8,1.9-10.2,4.2l0,0l-9.2,3.7
  	c-0.6,0.3-0.9,1-0.7,1.6c0.3,0.6,1,0.9,1.6,0.7l3.5-1.4L11.3,25.2z"></path>
  <path fill="#595959" d="M57.3,35.8c-0.2-0.5-0.8-0.8-1.4-0.6l-34,14.6c-0.5,0.2-0.8,0.8-0.5,1.4c0.2,0.4,0.5,0.6,1,0.6
  	c0.1,0,0.3,0,0.4-0.1l34-14.6C57.3,36.9,57.5,36.3,57.3,35.8z"></path>
  <path fill="#595959" d="M55,40.4L39.7,47c-0.5,0.2-0.8,0.8-0.5,1.4c0.2,0.4,0.6,0.6,1,0.6c0.1,0,0.3,0,0.4-0.1l15.2-6.5
  	c0.5-0.2,0.8-0.8,0.5-1.4S55.5,40.2,55,40.4z"></path>
  <g>
  	<g>
  		<path fill="#58585A" d="M98.7,45c-0.2-0.1-0.3-0.4-0.3-0.6c0-0.4,0.3-0.7,0.7-0.7c0.2,0,0.3,0,0.5,0.2c0.3,0.2,0.6,0.4,0.9,0.4
  			c0.6,0,1-0.4,1-1.2v-3.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v3.9c0,0.8-0.2,1.4-0.7,1.9c-0.4,0.4-1.1,0.6-1.8,0.6
  			C99.7,45.6,99.1,45.3,98.7,45z"></path>
  		<path fill="#58585A" d="M108.1,45.6c-1.6,0-2.8-1.1-2.8-2.8v0c0-1.5,1.1-2.8,2.6-2.8c1.8,0,2.6,1.5,2.6,2.6c0,0.4-0.3,0.7-0.7,0.7
  			h-3c0.2,0.7,0.6,1,1.3,1c0.4,0,0.8-0.1,1.1-0.4c0.1-0.1,0.2-0.1,0.4-0.1c0.3,0,0.6,0.3,0.6,0.6c0,0.2-0.1,0.3-0.2,0.4
  			C109.5,45.4,108.9,45.6,108.1,45.6z M109.1,42.4c-0.1-0.7-0.5-1.1-1.1-1.1c-0.6,0-1,0.4-1.2,1.1H109.1z"></path>
  		<path fill="#58585A" d="M116.9,42.8L116.9,42.8c0-1.5,1.2-2.8,2.8-2.8c0.8,0,1.4,0.2,1.8,0.6c0.1,0.1,0.2,0.3,0.2,0.5
  			c0,0.4-0.3,0.7-0.7,0.7c-0.2,0-0.4-0.1-0.4-0.2c-0.3-0.2-0.5-0.3-0.9-0.3c-0.8,0-1.3,0.7-1.3,1.5v0c0,0.8,0.5,1.5,1.4,1.5
  			c0.4,0,0.7-0.1,1-0.4c0.1-0.1,0.2-0.1,0.4-0.1c0.4,0,0.6,0.3,0.6,0.6c0,0.2-0.1,0.4-0.2,0.5c-0.4,0.4-1,0.7-1.9,0.7
  			C118,45.6,116.9,44.4,116.9,42.8z"></path>
  		<path fill="#58585A" d="M124,40.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v0.2c0.2-0.5,0.6-1,1-1c0.5,0,0.7,0.3,0.7,0.7
  			c0,0.4-0.3,0.6-0.6,0.7c-0.8,0.2-1.2,0.8-1.2,1.8v1.4c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8V40.8z"></path>
  		<path fill="#58585A" d="M131.8,45.6c-1.6,0-2.8-1.1-2.8-2.8v0c0-1.5,1.1-2.8,2.6-2.8c1.8,0,2.6,1.5,2.6,2.6c0,0.4-0.3,0.7-0.7,0.7
  			h-3c0.2,0.7,0.6,1,1.3,1c0.4,0,0.8-0.1,1.1-0.4c0.1-0.1,0.2-0.1,0.4-0.1c0.3,0,0.6,0.3,0.6,0.6c0,0.2-0.1,0.3-0.2,0.4
  			C133.2,45.4,132.6,45.6,131.8,45.6z M132.8,42.4c-0.1-0.7-0.5-1.1-1.1-1.1c-0.6,0-1,0.4-1.2,1.1H132.8z M131.2,39.3
  			c0-0.1,0-0.2,0.1-0.3l0.5-0.9c0.1-0.2,0.3-0.3,0.6-0.3c0.4,0,0.8,0.2,0.8,0.5c0,0.1-0.1,0.2-0.2,0.4l-0.6,0.6
  			c-0.3,0.3-0.5,0.3-0.9,0.3C131.4,39.6,131.2,39.5,131.2,39.3z"></path>
  		<path fill="#58585A" d="M139.1,45.6c-1.6,0-2.8-1.1-2.8-2.8v0c0-1.5,1.1-2.8,2.6-2.8c1.8,0,2.6,1.5,2.6,2.6c0,0.4-0.3,0.7-0.7,0.7
  			h-3c0.2,0.7,0.6,1,1.3,1c0.4,0,0.8-0.1,1.1-0.4c0.1-0.1,0.2-0.1,0.4-0.1c0.3,0,0.6,0.3,0.6,0.6c0,0.2-0.1,0.3-0.2,0.4
  			C140.5,45.4,139.9,45.6,139.1,45.6z M140.1,42.4c-0.1-0.7-0.5-1.1-1.1-1.1c-0.6,0-1,0.4-1.2,1.1H140.1z"></path>
  		<path fill="#58585A" d="M148.1,40.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v0.1c0.4-0.4,0.8-0.9,1.6-0.9
  			c0.7,0,1.2,0.3,1.5,0.8c0.5-0.5,1-0.8,1.8-0.8c1.1,0,1.8,0.7,1.8,2v2.8c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8v-2.3
  			c0-0.7-0.3-1.1-0.9-1.1c-0.6,0-0.9,0.4-0.9,1.1v2.3c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8v-2.3c0-0.7-0.3-1.1-0.9-1.1
  			c-0.6,0-0.9,0.4-0.9,1.1v2.3c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8V40.8z"></path>
  		<path fill="#58585A" d="M158.5,44L158.5,44c0-1.2,0.9-1.7,2.2-1.7c0.5,0,0.9,0.1,1.3,0.2v-0.1c0-0.6-0.4-1-1.1-1
  			c-0.4,0-0.8,0.1-1,0.1c-0.1,0-0.1,0-0.2,0c-0.4,0-0.6-0.3-0.6-0.6c0-0.3,0.2-0.5,0.4-0.6c0.5-0.2,1-0.3,1.7-0.3
  			c0.8,0,1.4,0.2,1.8,0.6c0.4,0.4,0.6,1,0.6,1.7v2.4c0,0.4-0.3,0.7-0.7,0.7c-0.4,0-0.7-0.3-0.7-0.6v0c-0.4,0.4-0.9,0.7-1.6,0.7
  			C159.3,45.6,158.5,45,158.5,44z M162,43.6v-0.3c-0.3-0.1-0.6-0.2-1-0.2c-0.6,0-1,0.3-1,0.7v0c0,0.4,0.3,0.6,0.8,0.6
  			C161.5,44.5,162,44.2,162,43.6z"></path>
  		<path fill="#58585A" d="M170.1,38.9c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v2c0.4-0.5,0.9-0.9,1.7-0.9c1.2,0,2.4,1,2.4,2.8
  			v0c0,1.8-1.2,2.8-2.4,2.8c-0.8,0-1.3-0.4-1.7-0.8v0c0,0.4-0.3,0.7-0.8,0.7c-0.4,0-0.8-0.3-0.8-0.8V38.9z M174.2,42.8L174.2,42.8
  			c0-0.9-0.6-1.5-1.3-1.5s-1.3,0.6-1.3,1.5v0c0,0.9,0.6,1.5,1.3,1.5S174.2,43.7,174.2,42.8z"></path>
  		<path fill="#58585A" d="M177.8,42.8L177.8,42.8c0-1.6,1.2-2.8,2.9-2.8c1.7,0,2.9,1.2,2.9,2.8v0c0,1.5-1.2,2.8-2.9,2.8
  			C179,45.6,177.8,44.4,177.8,42.8z M182.1,42.8L182.1,42.8c0-0.8-0.6-1.5-1.4-1.5c-0.9,0-1.4,0.7-1.4,1.5v0c0,0.8,0.6,1.5,1.4,1.5
  			C181.6,44.3,182.1,43.6,182.1,42.8z"></path>
  		<path fill="#58585A" d="M190.8,44.8c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8v-0.1c-0.3,0.4-0.8,0.9-1.6,0.9
  			c-1.1,0-1.8-0.8-1.8-2v-2.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v2.3c0,0.7,0.3,1.1,0.9,1.1c0.6,0,0.9-0.4,0.9-1.1v-2.3
  			c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8V44.8z"></path>
  		<path fill="#58585A" d="M193.6,44v-2.5h-0.1c-0.4,0-0.6-0.3-0.6-0.6s0.3-0.6,0.6-0.6h0.1v-0.7c0-0.4,0.3-0.8,0.8-0.8
  			c0.4,0,0.8,0.3,0.8,0.8v0.7h0.7c0.4,0,0.6,0.3,0.6,0.6s-0.3,0.6-0.6,0.6h-0.7v2.3c0,0.3,0.2,0.5,0.5,0.5c0.1,0,0.2,0,0.2,0
  			c0.3,0,0.6,0.3,0.6,0.6c0,0.3-0.2,0.5-0.4,0.6c-0.3,0.1-0.5,0.2-0.9,0.2C194.2,45.6,193.6,45.2,193.6,44z"></path>
  		<path fill="#58585A" d="M198.6,38.9c0-0.4,0.4-0.7,0.9-0.7c0.5,0,0.8,0.3,0.8,0.7v0c0,0.4-0.4,0.7-0.8,0.7
  			C199,39.6,198.6,39.3,198.6,38.9L198.6,38.9z M198.7,40.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v4c0,0.4-0.3,0.8-0.8,0.8
  			c-0.4,0-0.8-0.3-0.8-0.8V40.8z"></path>
  		<path fill="#58585A" d="M208.3,46.4c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8v-1.7c-0.4,0.5-0.9,0.9-1.7,0.9
  			c-1.2,0-2.4-1-2.4-2.8v0c0-1.8,1.2-2.8,2.4-2.8c0.8,0,1.3,0.4,1.7,0.8v0c0-0.4,0.3-0.7,0.8-0.7c0.4,0,0.8,0.3,0.8,0.8V46.4z
  			 M204.2,42.8L204.2,42.8c0,0.9,0.6,1.5,1.3,1.5c0.7,0,1.3-0.6,1.3-1.5v0c0-0.9-0.6-1.5-1.3-1.5C204.8,41.3,204.2,41.9,204.2,42.8z
  			"></path>
  		<path fill="#58585A" d="M215.7,44.8c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8v-0.1c-0.3,0.4-0.8,0.9-1.6,0.9
  			c-1.1,0-1.8-0.8-1.8-2v-2.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v2.3c0,0.7,0.3,1.1,0.9,1.1c0.6,0,1-0.4,1-1.1v-2.3
  			c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8V44.8z"></path>
  		<path fill="#58585A" d="M220.8,45.6c-1.6,0-2.8-1.1-2.8-2.8v0c0-1.5,1.1-2.8,2.6-2.8c1.8,0,2.6,1.5,2.6,2.6c0,0.4-0.3,0.7-0.7,0.7
  			h-3c0.2,0.7,0.6,1,1.3,1c0.4,0,0.8-0.1,1.1-0.4c0.1-0.1,0.2-0.1,0.4-0.1c0.3,0,0.6,0.3,0.6,0.6c0,0.2-0.1,0.3-0.2,0.4
  			C222.2,45.4,221.6,45.6,220.8,45.6z M221.8,42.4c-0.1-0.7-0.5-1.1-1.1-1.1c-0.6,0-1,0.4-1.2,1.1H221.8z"></path>
  		<path fill="#58585A" d="M232.3,45.6c-1.6,0-2.8-1.1-2.8-2.8v0c0-1.5,1.1-2.8,2.6-2.8c1.8,0,2.6,1.5,2.6,2.6c0,0.4-0.3,0.7-0.7,0.7
  			h-3c0.1,0.7,0.6,1,1.3,1c0.4,0,0.8-0.1,1.1-0.4c0.1-0.1,0.2-0.1,0.4-0.1c0.3,0,0.6,0.3,0.6,0.6c0,0.2-0.1,0.3-0.2,0.4
  			C233.7,45.4,233.1,45.6,232.3,45.6z M233.3,42.4c-0.1-0.7-0.5-1.1-1.1-1.1c-0.6,0-1,0.4-1.2,1.1H233.3z"></path>
  		<path fill="#58585A" d="M237.1,40.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v0.1c0.3-0.4,0.8-0.9,1.6-0.9
  			c1.1,0,1.8,0.8,1.8,2v2.8c0,0.4-0.3,0.8-0.8,0.8s-0.8-0.3-0.8-0.8v-2.3c0-0.7-0.3-1.1-0.9-1.1c-0.6,0-0.9,0.4-0.9,1.1v2.3
  			c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8V40.8z"></path>
  		<path fill="#58585A" d="M248.8,38.9c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v5.9c0,0.4-0.3,0.8-0.8,0.8
  			c-0.4,0-0.8-0.3-0.8-0.8V38.9z"></path>
  		<path fill="#58585A" d="M252.9,38.9c0-0.4,0.4-0.7,0.9-0.7c0.5,0,0.8,0.3,0.8,0.7v0c0,0.4-0.4,0.7-0.8,0.7
  			C253.3,39.6,252.9,39.3,252.9,38.9L252.9,38.9z M253,40.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v4c0,0.4-0.3,0.8-0.8,0.8
  			c-0.4,0-0.8-0.3-0.8-0.8V40.8z"></path>
  		<path fill="#58585A" d="M257.6,46.7c-0.3-0.1-0.4-0.3-0.4-0.6c0-0.3,0.3-0.6,0.6-0.6c0.1,0,0.2,0,0.2,0c0.4,0.2,0.9,0.3,1.5,0.3
  			c1,0,1.5-0.5,1.5-1.5v-0.3c-0.4,0.5-0.9,0.9-1.7,0.9c-1.2,0-2.4-0.9-2.4-2.5v0c0-1.6,1.1-2.5,2.4-2.5c0.8,0,1.3,0.3,1.7,0.8v0
  			c0-0.4,0.3-0.7,0.8-0.7c0.4,0,0.8,0.3,0.8,0.8v3.4c0,1-0.2,1.7-0.7,2.1c-0.5,0.5-1.3,0.7-2.3,0.7C258.9,47.1,258.2,47,257.6,46.7z
  			 M261.1,42.6L261.1,42.6c0-0.7-0.6-1.3-1.3-1.3c-0.7,0-1.3,0.5-1.3,1.2v0c0,0.7,0.6,1.2,1.3,1.2C260.5,43.8,261.1,43.3,261.1,42.6
  			z"></path>
  		<path fill="#58585A" d="M265.1,40.8c0-0.4,0.3-0.8,0.8-0.8c0.4,0,0.8,0.3,0.8,0.8v0.1c0.3-0.4,0.8-0.9,1.6-0.9
  			c1.1,0,1.8,0.8,1.8,2v2.8c0,0.4-0.3,0.8-0.8,0.8s-0.8-0.3-0.8-0.8v-2.3c0-0.7-0.3-1.1-0.9-1.1c-0.6,0-0.9,0.4-0.9,1.1v2.3
  			c0,0.4-0.3,0.8-0.8,0.8c-0.4,0-0.8-0.3-0.8-0.8V40.8z"></path>
  		<path fill="#58585A" d="M275.1,45.6c-1.6,0-2.8-1.1-2.8-2.8v0c0-1.5,1.1-2.8,2.6-2.8c1.8,0,2.6,1.5,2.6,2.6c0,0.4-0.3,0.7-0.7,0.7
  			h-3c0.2,0.7,0.6,1,1.3,1c0.4,0,0.8-0.1,1.1-0.4c0.1-0.1,0.2-0.1,0.4-0.1c0.3,0,0.6,0.3,0.6,0.6c0,0.2-0.1,0.3-0.2,0.4
  			C276.5,45.4,275.9,45.6,275.1,45.6z M276.1,42.4c-0.1-0.7-0.5-1.1-1.1-1.1c-0.6,0-1,0.4-1.2,1.1H276.1z"></path>
  	</g>
  </g>
  </svg>';
  $text = $text . '<br><br>';
  $text = $text . '<p style="font-family: \'Sans\'"><b>Référence commande: </b> ' . $compt . '<br></p>';
  $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';

  if ($json_obj->method == '2')
  {
    $text = $text . '<p style="font-family: \'Sans\'"><b>Vente : </b>Consomation sur place<br></p>';
    $text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
    $text = $text . '<p style="font-family: \'Sans\'"><b>Commande table numéro : </b> ' . $table . '<br></p>';
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
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'">Remise : ' . number_format( - $json_obj->remise, 2, ',', ' ') . '€ <br></p>';
  	$text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'"><b>Total Commande : ' . number_format($sum - $json_obj->remise, 2, ',', ' ') . '€ </b><br></p>';
  }
  else
  {
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'">Sous-total Commande : ' . number_format($sum, 2, ',', ' ') . '€ <br></p>';
  	$text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'">Remise : ' . number_format( - $json_obj->remise, 2, ',', ' ') . '€ <br></p>';
  	$text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'">Frais de Livraison : ' . number_format($json_obj->fraislivr, 2, ',', ' ') . '€ <br></p>';
  	$text = $text . '<hr style="width:50%;text-align:left;margin-left:0">';
  	$text = $text . '<p style="font-size:130%;font-family: \'Sans\'"><b>Total Commande : ' . number_format($sum - $json_obj->remise + $json_obj->fraislivr, 2, ',', ' ') . '€ </b><br></p>';
  }

  $text = $text . '</body>';
  $text = $text . '</html>';

  $mail->Body = $text;

  //$mail->send();
  if(!$mail->send()) {
    throw new Exception('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
  }

	$methodstr = "INCONNU";

	if (intval($method) == 1)
		$methodstr = "ATABLE";
	if (intval($method) >= 2)
		$methodstr = "CLICKNCOLLECT";

  $qcmdi = "INSERT INTO commande (customid, numref, nom, prenom, telephone, adresse1, adresse2, codepostal, ville, vente, paiement, sstotal, remise, " .
           "fraislivraison, total, commentaire, method, `table`, datecreation, statid ) VALUES ('$customid','$compt','" . htmlspecialchars(addslashes($json_obj->nom)) . "',";
  $qcmdi = $qcmdi . "'" . htmlspecialchars(addslashes($json_obj->prenom)) . "','$json_obj->telephone','" . htmlspecialchars(addslashes($json_obj->adresse1)) .
            "','" . htmlspecialchars(addslashes($json_obj->adresse2)) . "','$json_obj->codepostal','" . htmlspecialchars(addslashes($json_obj->ville)) .
            "','$json_obj->vente','$json_obj->paiement','" . strval($sum) . "','" . strval( - $json_obj->remise) . "','" . strval($json_obj->fraislivr) . "',";
  $qcmdi = $qcmdi . "'" . strval($sum - $json_obj->remise + $json_obj->fraislivr) . "','" . nl2br(stripslashes(strip_tags(htmlspecialchars(addslashes($json_obj->infosup))))) .
           "','$methodstr','$table', NOW(), (SELECT statid FROM statutcmd WHERE customid = '$customid' AND defaut = 1 LIMIT 1)) ";

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

  $urcreated = false;
	$stq = "SELECT aboid, stripe_subscription_id FROM abonnement WHERE bouticid = " . $customid;
  if ($result = $conn->query($stq))
  {
    while ($row = $result->fetch_row())
    {
      if ($urcreated == false)
      {
        $subscription = $stripe->subscriptions->retrieve($row[1]);
        if ($subscription->status == "active")
        {
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
              $usage_quantity = $sum - $json_obj->remise + $json_obj->fraislivr;// * $price;
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
                $urcreated = true;
              } catch (\Stripe\Exception\ApiErrorException $error) {
                //error_log("test1");
                //error_log(print_r($error, TRUE));
                throw new Exception("Usage report failed for item ID $subscription_item_id with idempotency key $idempotency_key: $error.toString()");
              }
            }
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
