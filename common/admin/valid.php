<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.11">
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
			    	<h5 class="modal-title">ERREUR</h5>
			    </div>
			   	<div class="modal-body">
				    <?php
				    	session_start();
 
				      include "../config/common_cfg.php";
				      include "../param.php";
				     	
				      $email = isset($_POST['email']) ? $_POST ['email'] : '';
				      $pass = isset($_POST['pass']) ? $_POST ['pass'] : '';
				      
				      $count2 = 0;
				
				      $conn = new mysqli($servername, $username, $password, $bdd);
				
				      if ($conn->connect_error)
				      	die("Connection failed: " . $conn->connect_error);

			        //  Récupération de l'utilisateur et de son pass hashé
			        $req = $conn->prepare('SELECT administrateur.adminid, administrateur.pass, customer.customer, customer.customid FROM administrateur, customer WHERE administrateur.email = ? AND administrateur.actif = 1 AND administrateur.customid = customer.customid');
			        $req->bind_param("s", $email);
			        $req->execute();
			        $req->bind_result($id, $pass_hache, $boutic, $customid);
			        $resultat = $req->fetch();
			      	$req->close();
				      
				      $ip = $_SERVER["REMOTE_ADDR"];
				    
				      $q2 = "SELECT COUNT(*) FROM `connexion` WHERE `ip` LIKE '$ip' AND `ts` > (now() - interval $interval)";
				
				      if ($r2 = $conn->query($q2)) 
				    	  if ($row2 = $r2->fetch_row()) 
				    		  $count2 = $row2[0];
				
				      //$r2->close();
				      
				      if($count2 >= $maxretry)
				      	echo "Vous êtes autorisé à " . $maxretry . " tentatives en " . $interval . "<br />";
				      else 
				      { 
				      	
				        // Comparaison du pass envoyé via le formulaire avec la base
				        $isPasswordCorrect = password_verify($pass, $pass_hache);
				              
				        if (!$resultat)
				        {
						      $q1 = "INSERT INTO connexion (ip, ts) VALUES ('$ip',CURRENT_TIMESTAMP)";
						      if ($conn->query($q1)== FALSE) 
						      	echo "Error: " . $q1 . "<br>" . $conn->error;
						      else
				          	echo 'Mauvais identifiant ou mot de passe !<br>';
				        }
				        else
				        {
				          if ($isPasswordCorrect) 
				          {
				          	$_SESSION['boutic'] = $boutic;
				            $_SESSION[$boutic . '_id'] = $id;
				            $_SESSION[$boutic . '_email'] = $email;
				            $_SESSION[$boutic . '_auth'] = 'oui';
				            header("LOCATION: admin.php");
				          }
				          else 
				          {
							      $q1 = "INSERT INTO connexion (ip, ts) VALUES ('$ip',CURRENT_TIMESTAMP)";
							      if ($conn->query($q1)== FALSE) 
							      	echo "Error: " . $q1 . "<br>" . $conn->error;
							      else
				            	echo 'Mauvais identifiant ou mot de passe !<br>';
  			          }
				        }
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




