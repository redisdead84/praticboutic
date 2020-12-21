<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>


    <?php

      session_start();
    
      include "../config/common_cfg.php";
      include "../param.php";
     	
      $pseudo = isset($_POST['pseudo']) ? $_POST ['pseudo'] : '';
      $pass = isset($_POST['pass']) ? $_POST ['pass'] : '';
      
      $count2 = 0;

      // Create connection
      $conn = new mysqli($servername, $username, $password, $bdd);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
            
		  $interval = "15 MINUTE";
      $maxretry = "4";
      
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
      
      
      if($count2 > $maxretry)
      {
        echo "<h3>Vous êtes autorisé à " . $maxretry . " tentative(s)) en " . $interval . "<br /></h3>";
        echo '<a href="index.php"><button type="button">Retour</button></a>';
      }      
      else 
      { 
        //  Récupération de l'utilisateur et de son pass hashé
        
        $id = -1;
				$pass_hache = "\$1\$j4UqeoQx\$qn7Nq4gkf/VQQRw5jl9lu1";      
        
        if (strcmp($pseudo, "praticboutic") == 0)  
        	$resultat = -1;
        	
        // Comparaison du pass envoyé via le formulaire avec la base
        $isPasswordCorrect = password_verify($pass, $pass_hache);
              
        if (!$resultat)
        {
  				echo 'Mauvais identifiant ou mot de passe !<br>';
          echo '<a href="index.php"><button type="button">Retour</button></a>';      
        }
        else
        {
          if ($isPasswordCorrect) 
          {
            $_SESSION['superadmin_id'] = $id;
            $_SESSION['superadmin_pseudo'] = $pseudo;
            $_SESSION['superadmin_auth'] = 'superadmin';
            $_SESSION['superadmin_mode'] = 'superadmin';
            header("LOCATION: superadmin.php");
          }
          else 
          {
            echo 'Mauvais identifiant ou mot de passe !<br>';
            echo '<a href="index.php"><button type="button">Retour</button></a>';
          }
        }
     }
    ?>

  </body>
</html>




