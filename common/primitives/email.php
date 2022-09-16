            <?php
              session_id("customerarea");
              session_start();

              header('Access-Control-Allow-Origin: *');
              header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
              header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
              header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
              header('Access-Control-Max-Age: 86400');
              header('Content-Type: application/json');

              // Import PHPMailer classes into the global namespace
              // These must be at the top of your script, not inside a function
              use PHPMailer\PHPMailer\PHPMailer;
              use PHPMailer\PHPMailer\Exception;

              //Load composer's autoloader
              require '../../vendor/autoload.php';
              include "../config/common_cfg.php";
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
              try 
              {
                
                $json_str = file_get_contents('php://input');
                $input = json_decode($json_str);
  
                $email = $input->email;
                error_log($email);
                $conn = new mysqli($servername, $username, $password, $bdd);
                if ($conn->connect_error) 
                {
                  throw new Error("Connection failed: " . $conn->connect_error);
                }

                $idclient = 0;
                $query = 'SELECT cltid FROM client WHERE email = "' . $email . '" AND actif = 1';
                if ($result = $conn->query($query)) 
                {
                  if ($row = $result->fetch_row()) 
                  {
                    $idclient = $row[0];
                    $password = generateStrongPassword();
                    //error_log($idclient);
                  }
                }

                $count2 = 0;
                $ip = $_SERVER["REMOTE_ADDR"];
                $q2 = "SELECT COUNT(*) FROM `connexion` WHERE `ip` LIKE '$ip' AND `ts` > (now() - interval $interval)";
                if ($r2 = $conn->query($q2)) 
                 {
                   if ($row2 = $r2->fetch_row()) 
                   {
                     $count2 = $row2[0];
                   }
                }
                

                //$mail->SMTPDebug = 0;                                 // Enable verbose debug output
                $mail->isSMTP();

                // Set mailer to use SMTP
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
                $rcvmail = $email; //GetValeurParam("Receivermail_mail", $conn);
                $rcvnom = ""; //GetValeurParam("Receivernom_mail", $conn);
                $mail->addAddress($rcvmail, $rcvnom);     // Add a recipient
                $isHTML = "TRUE";
                $mail->isHTML($isHTML);                                  // Set email format to HTML

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
                $text = $text . '&nbsp;&nbsp;Comme vous avez oubli&eacute; votre mot de passe praticboutic un nouveau a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; automatiquement. <br>';        
                $text = $text . 'Voici votre nouveau mot de mot de passe administrateur praticboutic : ';
                $text = $text . '<b>' . $password . '</b><br>';
                $text = $text . 'Vous pourrez en personnaliser un nouveau à partir du formulaire client de l\'arrière boutic.<br><br>';
                $text = $text . 'Cordialement<br><br>L\'équipe praticboutic<br><br></p>';
                $text = $text . '</body>';
                $text = $text . '</html>';

                $mail->Body = $text;

                if($count2 >= $maxretry)
                {
                  throw Error("Vous êtes autorisé à " . $maxretry . " tentative(s)) en " . $interval );
                }
                else
                {
                  if ( $idclient > 0 ) 
                  {
                    $mail->send();
                    $query2 = 'UPDATE client SET pass = "' . password_hash($password, PASSWORD_DEFAULT) . '" WHERE cltid = "' . $idclient . '"';
                    if ($result2 = $conn->query($query2)) 
                    {
                      if ($result2 === FALSE) 
                      {
                        throw new Error("Error: " . $q . "<br>" . $conn->error);
                      }
                    }
                  }
                  else
                  {
                    throw new Error("Courriel non-trouvé");
                  }
                  $q1 = "INSERT INTO connexion (ip, ts) VALUES ('$ip',CURRENT_TIMESTAMP)";
                  if ($r1 = $conn->query($q1)) 
                  {
                    if ($r1 === FALSE) 
                    {
                      throw new Error("Error: " . $q1 . "<br>" . $conn->error);
                    }
                  }
                }
                $conn->close();
                echo json_encode("OK");
              }
              catch (Error $e) 
              {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
              }
            ?>
