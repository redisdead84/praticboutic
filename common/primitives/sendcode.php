<?php

  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  header('Access-Control-Max-Age: 86400');

  $postdata = file_get_contents("php://input");
  if (isset($postdata))
    $request = json_decode($postdata);
    
  if (isset($input->sessionid))
    session_id($input->sessionid);
  session_start();

  // Import PHPMailer classes into the global namespace
  // These must be at the top of your script, not inside a function
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  //Load composer's autoloader
  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";

  $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
  try 
  {
    $sent = 0;
    $hash = md5(microtime(TRUE)*100000);
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $lecode = str_pad(strval(floor( rand() * pow(10, 6) )), 6, '0');
    error_log($lecode);
    $encryptedCode = strval(openssl_encrypt($lecode, 'AES-256-ECB', $_ENV['IDENTIFICATION_KEY'], OPENSSL_RAW_DATA ));

    error_log($encryptedCode);
    //$decryptedCode = openssl_decrypt($code, 'AES-256-ECB', $_ENV['IDENTIFICATION_KEY'], OPENSSL_RAW_DATA);
    //error_log($decryptedCode);
    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);

    // Check connection
    if ($conn->connect_error) 
    {
      throw new Error("Connection failed: " . $conn->connect_error);
    }

    $subquery = "SELECT count(*) FROM `client` WHERE email = '" . $request->email . "'";

    $result = $conn->query($subquery);
    $row = $result->fetch_row();
    if (intval($row[0])>0)
    {
      throw new Error('Le courriel ' . $request->email . ' est déjà attribué à un client. Impossible de continuer.');
    }
    else
    {
      $q1 = "INSERT INTO identifiant(email, hash, actif) VALUES ('$request->email','$hash', '0')";
      //error_log($q1);
      if ($r1 = $conn->query($q1)) 
      {
        if ($r1 === FALSE) 
        {
          throw new Error("Error: " . $q1 . "<br>" . $conn->error);
        }
      }

      //$mail->SMTPDebug = 4;                                 // Enable verbose debug output
      // $debug = '';
      //$mail->Debugoutput = function($str, $level) {
      //  $GLOBALS['debug'] .= "$level: $str\n";
      //};
      
      $mail->isSMTP();                                      // Set mailer to use SMTP
      
      $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
          )
      );

      $mail->Host = $host;  // Specify main and backup SMTP servers
      $mail->SMTPAuth = $smtpa;                               // Enable SMTP authentication
      $mail->Username = $user;                 // SMTP username
      $mail->Password = $pwd;                               // SMTP password
      $mail->SMTPSecure = $ssec;                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = $port;                                    // TCP port to connect to
      $mail->CharSet = $chars;
      $mail->setFrom($sendmail, $sendnom);
      $rcvmail = $request->email; //GetValeurParam("Receivermail_mail", $conn);
      $rcvnom = ""; //GetValeurParam("Receivernom_mail", $conn);
      $mail->addAddress($rcvmail, $rcvnom);     // Add a recipient
      $isHTML = "TRUE";
      $mail->isHTML($isHTML);

      $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
      $subject = "Votre code confidentiel";
      $mail->Subject = $subject;

      $text = '<!DOCTYPE html>';
      $text = $text . '<html>';
      $text = $text . '<head>';
      $text = $text . '<link href=\'https://fonts.googleapis.com/css?family=Sans\' rel=\'stylesheet\'>';
      $text = $text . '</head>';
      $text = $text . '<body>';
      $text = $text . '<img src="' . $protocol . $_SERVER['SERVER_NAME'] . '/common/customerarea/img/logo.png' . '" width="253" height="114" alt="">';
      $text = $text . '<br><br>';
      $text = $text . '<p style="font-family: \'Sans\'">Bonjour ';
      $text = $text . $request->email . '<br><br>';
      $text = $text . 'Voici le code de vérification : ' . $lecode;
      $text = $text . '<br>';
      $text = $text . 'Cordialement<br><br>L\'équipe praticboutic<br><br></p>';
      $text = $text . '</body>';
      $text = $text . '</html>';

      $mail->Body = $text;

      $mail->send();

      $conn->close();

      echo json_encode($encryptedCode);
    }
  }
  catch (Exception $e) 
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
?>
