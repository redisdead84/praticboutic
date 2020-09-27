<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require '../vendor/autoload.php';

include "../config/config.php";
include "../param.php";

function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
{
	$sets = array();
	if(strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if(strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if(strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
	if(strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';

	$all = '';
	$password = '';
	foreach($sets as $set)
	{
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}

	$all = str_split($all);
	for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];

	$password = str_shuffle($password);

	if(!$add_dashes)
		return $password;

	$dash_len = floor(sqrt($length));
	$dash_str = '';
	while(strlen($password) > $dash_len)
	{
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
	}
	$dash_str .= $password;
	return $dash_str;
}

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
		

    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);
    // Check connection
	  if ($conn->connect_error) 
	  {
   		die("Connection failed: " . $conn->connect_error);
    }
    $count2 = 0;
    
	  $interval = GetValeurParam("Interval_try", $conn);
    $maxretry = GetValeurParam("Max_try", $conn);

    $ip = $_SERVER["REMOTE_ADDR"];
    
    $q1 = "INSERT INTO connexion (ip, ts) VALUES ('$ip',CURRENT_TIMESTAMP)";
    if ($r1 = $conn->query($q1)) 
		{
    	if ($r1 === FALSE) 
     	{
     		echo "Error: " . $q1 . "<br>" . $conn->error;
     	}
 		}

    $q2 = "SELECT COUNT(*) FROM `connexion` WHERE `ip` LIKE '$ip' AND `ts` > (now() - interval $interval)";
    if ($r2 = $conn->query($q2)) 
 		{
   	  if ($row2 = $r2->fetch_row()) 
   	  {
   		  $count2 = $row2[0];
     	}
	  }

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

    $rcvmail = $_POST['email']; //GetValeurParam("Receivermail_mail", $conn);
    $rcvnom = ""; //GetValeurParam("Receivernom_mail", $conn);
    $mail->addAddress($rcvmail, $rcvnom);     // Add a recipient
/*    $mail->addAddress('ellen@example.com');               // Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');*/

    //Attachments
/*    $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name*/

    //Content
    $isHTML = GetValeurParam("isHTML_mail", $conn);
    $mail->isHTML($isHTML);                                  // Set email format to HTML

    $subject = "Confidentiel"; //GetValeurParam("Subject_mail", $conn);
    $mail->Subject = $subject;

    $text = '<!DOCTYPE html>';
    $text = $text . '<html>';
    $text = $text . '<body>';
    
		// vérifier que l'email est bien dans la base administrateur
 		
 		$idadmin = 0;
    $query = 'SELECT adminid, pseudo FROM administrateur WHERE email = "' . $rcvmail . '"';
    if ($result = $conn->query($query)) 
		{
    	if ($row = $result->fetch_row()) 
    	{
    		$idadmin = $row[0];
    		$password = generateStrongPassword();
    		$pseudo = $row[1];
    	}
		}
		
    $text = $text . '<h3>Bonjour ';
    $text = $text . $pseudo . '<br /><br />';    		
    $text = $text . '  Comme vous avez oubli&eacute; votre mot de passe qlickresto un nouveau a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; automatiquement <br />';    		
    $text = $text . 'Voici votre nouveau mot de mot de passe administrateur qlickresto : ';
    $text = $text . $password . '<br />';
    $text = $text . 'Vous pourrez en personnaliser un nouveau à partir de l\'écran d\'administration.<br />';
    $text = $text . 'Cordialement<br />L\'équipe qlickresto<br /></h3>';
    $text = $text . '</body>';
    $text = $text . '</html>';

    $mail->Body = $text;

/*    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';*/
    if($count2 > $maxretry)
    {
      echo "<h3>Vous êtes autorisé à " . $maxretry . " tentative(s)) en " . $interval . "<br /></h3>";
      echo '<a href="index.php"><button type="button">Retour</button></a>';
    }      
    else 
    { 
      if ( $idadmin > 0 ) 
      {
        $mail->send();
        $query2 = 'UPDATE administrateur SET pass = "' . password_hash($password, PASSWORD_DEFAULT) . '" WHERE adminid = "' . $idadmin . '"';
        if ($result2 = $conn->query($query2)) 
  		  {
          if ($result2 === FALSE) 
          {
    		    echo "Error: " . $q . "<br>" . $conn->error;
  	      }
  		  }
        echo "<h3>Un email contenant un mot de passe automatique vous a été envoyé.<br /></h3>";
        echo '<a href="index.php"><button type="button">Retour</button></a>';
      }
    }
    $conn->close();
  }
  catch (Exception $e) 
  {
	  echo 'Mailer Error: ' . $mail->ErrorInfo;    
	  echo '<script language=\'Javascript\'>alert(\'Erreur Le message n a pu etre envoye\' );location.href = \'index.php\';</script>';
  }
  
?>
