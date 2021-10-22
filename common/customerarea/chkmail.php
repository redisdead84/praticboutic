<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.12">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <div class="modal" tabindex="-1" role="dialog" data-backdrop="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">INFORMATION</h5>
          </div>
          <div class="modal-body">
            <?php
              session_start();

              // Import PHPMailer classes into the global namespace
              // These must be at the top of your script, not inside a function
              use PHPMailer\PHPMailer\PHPMailer;
              use PHPMailer\PHPMailer\Exception;

              //Load composer's autoloader
              require '../../vendor/autoload.php';

              use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
              use Google\Cloud\RecaptchaEnterprise\V1\Event;
              use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
              use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

              include "../config/common_cfg.php";
              include "../param.php";

              /**
             * Create an assessment to analyze the risk of a UI action.
             * @param string $siteKey The key ID for the reCAPTCHA key (See https://cloud.google.com/recaptcha-enterprise/docs/create-key)
             * @param string $token The user's response token for which you want to receive a reCAPTCHA score. (See https://cloud.google.com/recaptcha-enterprise/docs/create-assessment#retrieve_token)
             * @param string $project Your Google Cloud project ID
             */
             function create_assessment(
                string $siteKey,
                string $token,
                string $project
             ): void {
               
                $client = new RecaptchaEnterpriseServiceClient();
                $projectName = $client->projectName($project);
          
                $event = (new Event())
                    ->setSiteKey($siteKey)
                    ->setToken($token);
          
                $assessment = (new Assessment())
                    ->setEvent($event);
          
                try {
                    $response = $client->createAssessment(
                        $projectName,
                        $assessment
                    );
          
                    // You can use the score only if the assessment is valid,
                    // In case of failures like re-submitting the same token, getValid() will return false
                    if ($response->getTokenProperties()->getValid() == false) {
                        printf('The CreateAssessment() call failed because the token was invalid for the following reason: ');
                        printf(InvalidReason::name($response->getTokenProperties()->getInvalidReason()));
                        die();
                    } 
                    else 
                    {
                      //printf('The score for the protection action is:');
                      //printf($response->getRiskAnalysis()->getScore());
          
                        // Optional: You can use the following methods to get more data about the token
                        // Action name provided at token generation.
                        // printf($response->getTokenProperties()->getAction() . PHP_EOL);
                        // The timestamp corresponding to the generation of the token.
                        // printf($response->getTokenProperties()->getCreateTime()->getSeconds() . PHP_EOL);
                        // The hostname of the page on which the token was generated.
                        // printf($response->getTokenProperties()->getHostname() . PHP_EOL);
                    }
                } catch (exception $e) {
                    printf('CreateAssessment() call failed with the following error: ');
                    printf($e);
                    die();
                }
             }
             
             $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
             $dotenv->load();
             
             putenv($_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
             // TODO(Developer): Replace the following before running the sample
             create_assessment($_ENV['RECAPTCHA_KEY'], $_POST['gRecaptchaResponse'], $_ENV['GOOGLE_PROJECT']);
             
              $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
              try 
              {
                $sent = 0;
                $email = isset($_POST['email']) ? $_POST ['email'] : '';
                $captcha = isset($_POST['captcha']) ? $_POST ['captcha'] : '';
                $hash = md5(microtime(TRUE)*100000);

                // Create connection
                $conn = new mysqli($servername, $username, $password, $bdd);

                // Check connection
                if ($conn->connect_error) 
                {
                  die("Connection failed: " . $conn->connect_error);
                }

                $subquery = "SELECT count(*) FROM `client` WHERE email = '" . $email . "'";
  
                $result = $conn->query($subquery);
                $row = $result->fetch_row();
                if (intval($row[0])>0)
                {
                  echo 'Le courriel ' . $email . ' est déjà attribué à un client. Impossible de continuer.';
                }
                else if (strcmp($_SESSION['reg_mailsent'], 'oui') == 0)
                {
                  header('LOCATION: reg.php');
                }
                else
                {
                  $q1 = "INSERT INTO identifiant(email, hash, actif) VALUES ('$email','$hash', '0')";
                  //error_log($q1);
                  if ($r1 = $conn->query($q1)) 
                  {
                    if ($r1 === FALSE) 
                    {
                      echo "Error: " . $q1 . "<br>" . $conn->error;
                    }
                  }

                  $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                  $mail->isSMTP();                                      // Set mailer to use SMTP

                  $mail->Host = $host;  // Specify main and backup SMTP servers
                  $mail->SMTPAuth = $smtpa;                               // Enable SMTP authentication
                  $mail->Username = $user;                 // SMTP username
                  $mail->Password = $pwd;                               // SMTP password
                  $mail->SMTPSecure = $ssec;                            // Enable TLS encryption, `ssl` also accepted
                  $mail->Port = $port;                                    // TCP port to connect to
                  $mail->CharSet = $chars;
                  $mail->setFrom($sendmail, $sendnom);
                  $rcvmail = $email; //GetValeurParam("Receivermail_mail", $conn);
                  $rcvnom = ""; //GetValeurParam("Receivernom_mail", $conn);
                  $mail->addAddress($rcvmail, $rcvnom);     // Add a recipient
                  $isHTML = "TRUE";
                  $mail->isHTML($isHTML);

                  $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
                  $subject = "Confidentiel"; //GetValeurParam("Subject_mail", $conn);
                  $mail->Subject = $subject;

                  $text = '<!DOCTYPE html>';
                  $text = $text . '<html>';
                  $text = $text . '<head>';
                  $text = $text . '<link href=\'https://fonts.googleapis.com/css?family=Sans\' rel=\'stylesheet\'>';
                  $text = $text . '</head>';
                  $text = $text . '<body>';
                  $text = $text . '<p style="font-family: \'Sans\'">Bonjour ';
                  $text = $text . $email . '<br><br>';    
                  $text = $text . 'Vous pouvez valider votre courriel avec le lien suivant : ';
                  $text = $text . '<a href="' . $protocol . $_SERVER['SERVER_NAME'] . '/common/customerarea/verify.php?email=' . urlencode($email) . '&hash=' . urlencode($hash) . '">Le lien</a><br>';
                  $text = $text . 'Cordialement<br><br>L\'équipe praticboutic<br><br></p>';
                  $text = $text . '</body>';
                  $text = $text . '</html>';

                  $mail->Body = $text;
                  //error_log($mail->Body);
                  $mail->send();
                  $sent = 1;
                  $_SESSION['reg_mailsent'] = 'oui';

                  echo "Un email contenant un lien pour finaliser votre inscription vous a été envoyé.<br />";

                  $conn->close();
                }
              }
              catch (Exception $e) 
              {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
                echo 'Erreur Le message n a pu être envoyé<br />';
              }
            ?>
          </div>
          <div class="modal-footer">
            <?php 
              if ($sent== 0)
                echo '<a href="reg.php"><button class="btn btn-primary btn-block" type="button" value="Valider">OK</button></a>';      
            ?>
          </div>
         </div>
        </div>
    </div>
  </body>
  <script type="text/javascript" >
    $('.modal').modal('show');
  </script>
</html>
