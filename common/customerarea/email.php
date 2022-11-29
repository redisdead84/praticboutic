<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.703">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()" class="epure"/>
      <div id="workspace" class="spacemodal">
        <img id='illus2' src='img/illustration_2.png' class="elemcb epure" />
        <div class="modal-content-mainmenu elemcb">
          <div class="modal-header-cb" style="display:none">
            <h5 class="modal-title-cb">INFORMATION</h5>
          </div>
          <div class="modal-body-cb mdbodynh">
            <script type="text/javascript">
              var modal = $('.modal');
              function changetitle(title) {
                modal.find('.modal-title').text(title);
              }
            </script>
            <?php

              session_start();

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
                $email = isset($_POST['email']) ? $_POST['email'] : '';
                $conn = new mysqli($servername, $username, $password, $bdd);
                if ($conn->connect_error) 
                {
                  echo '<script type="text/javascript">changetitle("ERREUR") </script>';
                  echo "Connection failed: " . $conn->connect_error;
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
                $protocol = empty($_SERVER['HTTPS']) == false ? 'https://' : 'http://';
                
                $text = '<!DOCTYPE html>';
                $text = $text . '<html>';
                $text = $text . '<head>';
                $text = $text . '<link href=\'https://fonts.googleapis.com/css?family=Sans\' rel=\'stylesheet\'>';
                $text = $text . '</head>';
                $text = $text . '<body>';
                $text = $text . '<img src="' . $protocol . $_SERVER['SERVER_NAME'] . '/common/customerarea/img/logo.png' . '" width="253" height="114">';
                $text = $text . '<br><br>';
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
                  echo "Vous êtes autorisé à " . $maxretry . " tentative(s)) en " . $interval . "<br />";
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
                        echo '<script type="text/javascript">changetitle("ERREUR") </script>';
                        echo "Error: " . $q . "<br>" . $conn->error;
                      }
                      else
                      {
                        echo "Un email contenant un mot de passe automatique vous a été envoyé.<br />";
                      }
                    }
                  }
                  else
                  {
                    echo '<script type="text/javascript">changetitle("ERREUR") </script>';
                    echo "Courriel non-trouvé<br />";
                  }
                  $q1 = "INSERT INTO connexion (ip, ts) VALUES ('$ip',CURRENT_TIMESTAMP)";
                  if ($r1 = $conn->query($q1)) 
                  {
                    if ($r1 === FALSE) 
                    {
                      echo '<script type="text/javascript">changetitle(title)("ERREUR") </script>';
                      echo "Error: " . $q1 . "<br>" . $conn->error;
                    }
                  }
                }
                $conn->close();
              }
              catch (Exception $e) 
              {
                echo '<script type="text/javascript">changetitle("ERREUR") </script>';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
                echo 'Erreur Le message n a pu être envoyé<br />';
              }
            ?>
            </div>
            <div class="modal-footer-cb">
              <a href="index.php"><button class="btn btn-primary btn-block" type="button" value="Valider">OK</button></a>
            </div>
         </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()" class="epure"/>
    </div>
  </body>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter ?") == true)
      {
        window.location.href ='exit.php';
      }
    }
  </script>
  <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="c21f7fea-9f56-47ca-af0c-f8978eff4c9b";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
</html>
