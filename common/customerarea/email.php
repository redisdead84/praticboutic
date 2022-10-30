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

                $text = '<!DOCTYPE html>';
                $text = $text . '<html>';
                $text = $text . '<head>';
                $text = $text . '<link href=\'https://fonts.googleapis.com/css?family=Sans\' rel=\'stylesheet\'>';
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
