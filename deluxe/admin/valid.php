<!DOCTYPE html>
<html>
  <head>
    <title>Bienvenue sur l'administration du site de la poisonnerie d'Eulalie</title>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>


    <?php
    
      include "../config/config.php";
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
      
      
      if($count2 > $maxretry)
      {
        echo "<h3>Vous êtes autorisé à " . $maxretry . " tentative(s)) en " . $interval . "<br /></h3>";
        echo '<a href="index.php"><button type="button">Retour</button></a>';
      }      
      else 
      { 
        //  Récupération de l'utilisateur et de son pass hashé
        $req = $conn->prepare('SELECT adminid, pass FROM administrateur WHERE pseudo = ? AND actif=1');
        $req->bind_param("s", $pseudo);
        $req->execute();
        $req->bind_result($id,$pass_hache);
        $resultat = $req->fetch();
      
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
            session_start();
            $_SESSION['id'] = $id;
            $_SESSION['pseudo'] = $pseudo;
            $_SESSION['auth'] = 'oui';
            $_SESSION['mode'] = 'basique';
            $_SESSION['customer'] = $customer;
            header("LOCATION: admin.php#tabCat");
          }
          else 
          {
            echo 'Mauvais identifiant ou mot de passe !<br>';
            echo '<a href="index.php"><button type="button">Retour</button></a>';
          }
        }

        $req->close();
     }
    ?>

  </body>
</html>




