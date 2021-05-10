<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.07">
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
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		    	<div class="modal-header">
			    	<h5 class="modal-title">Information</h5>
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
						try {
								$email = isset($_POST['email']) ? $_POST['email'] : '';
						    $conn = new mysqli($servername, $username, $password, $bdd);
							  if ($conn->connect_error) 
							  {
						   		die("Connection failed: " . $conn->connect_error);
						    }

						 		$idadmin = 0;
						 		$customid = 0;
						 		$query = 'SELECT adminid, customid FROM administrateur WHERE email = "' . $email . '" AND actif = 1';
						    if ($result = $conn->query($query)) 
								{
						    	if ($row = $result->fetch_row()) 
						    	{
						    		$idadmin = $row[0];
						    		$password = generateStrongPassword();
						    		$customid = $row[1];
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
						    $isHTML = GetValeurParam("isHTML_mail", $conn, $customid,"TRUE");
						    $mail->isHTML($isHTML);                                  // Set email format to HTML
						
						    $subject = "Confidentiel"; //GetValeurParam("Subject_mail", $conn);
						    $mail->Subject = $subject;
						
						    $text = '<!DOCTYPE html>';
						    $text = $text . '<html>';
						    $text = $text . '<body>';
						    $text = $text . '<h3>Bonjour ';
						    $text = $text . $email . '<br /><br />';    		
						    $text = $text . '  Comme vous avez oubli&eacute; votre mot de passe praticboutic un nouveau a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; automatiquement <br />';    		
						    $text = $text . 'Voici votre nouveau mot de mot de passe administrateur praticboutic : ';
						    $text = $text . $password . '<br />';
						    $text = $text . 'Vous pourrez en personnaliser un nouveau à partir de l\'écran d\'administration.<br />';
						    $text = $text . 'Cordialement<br />L\'équipe praticboutic<br /></h3>';
						    $text = $text . '</body>';
						    $text = $text . '</html>';
						
						    $mail->Body = $text;
						
						    if($count2 >= $maxretry)
						    {
						      echo "Vous êtes autorisé à " . $maxretry . " tentative(s)) en " . $interval . "<br />";
						    }      
						    else 
						    { 
						      if ( $idadmin > 0 ) 
						      {
						        $mail->send();
						        $query2 = 'UPDATE administrateur SET pass = "' . password_hash($password, PASSWORD_DEFAULT) . '" WHERE customid = ' . $customid . ' AND adminid = "' . $idadmin . '"';
						        if ($result2 = $conn->query($query2)) 
						  		  {
						          if ($result2 === FALSE) 
						          {
						    		    echo "Error: " . $q . "<br>" . $conn->error;
						  	      }
						  		  }
						  		}
						  		else 
						  		{
								    $q1 = "INSERT INTO connexion (ip, ts) VALUES ('$ip',CURRENT_TIMESTAMP)";
								    if ($r1 = $conn->query($q1)) 
										{
								    	if ($r1 === FALSE) 
								     	{
								     		echo "Error: " . $q1 . "<br>" . $conn->error;
								     	}
								 		}
						  		}	
						      echo "Un email contenant un mot de passe automatique vous a été envoyé.<br />";
					    	}
						    $conn->close();
						  }
						  catch (Exception $e) 
						  {
							  echo 'Mailer Error: ' . $mail->ErrorInfo;    
							  echo 'Erreur Le message n a pu être envoyé<br />';
						  }
						  
						?>
    				</div>
    			  <div class="modal-footer">
			      	<a href="index.php"><button class="btn btn-primary btn-block" type="button" value="Valider">OK</button></a>
			      </div>
 		    </div>
		  </div>
		</div>
  </body>
  <script type="text/javascript" >
  	$('.modal').modal('show');
  </script>
</html>



