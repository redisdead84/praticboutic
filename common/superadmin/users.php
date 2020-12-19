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
     	
      $id = isset($_GET['identif']) ? $_GET['identif'] : '';
      $pseudo = isset($_POST['pseudo']) ? $_POST ['pseudo'] : '';
      $pass = isset($_POST['pass']) ? $_POST ['pass'] : '';
      $email = isset($_POST['email']) ? $_POST ['email'] : '';
      
      if (empty($id)==TRUE ) {
        die("Identifiant vide");
      }
      if (empty($pseudo)==TRUE ) {
        die("Pseudo vide");
      }
      if (empty($pass)==TRUE ) {
        die("Mot de passe vide");
      }
      if (empty($email)==TRUE ) {
        die("Courriel vide");
      }


      // Create connection
      $conn = new mysqli($servername, $username, $password, $bdd);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

		  $query = 'SELECT customid FROM customer WHERE customer = "' . $id .'"';
		
			if ($result = $conn->query($query)) {
		    if ($result->num_rows == 0)
		      die("Pas de boutic trouvé avec cette identifiant");
		    if ($result->num_rows > 1)
		      die("Errreur de duplication");
		    if ($row1 = $result->fetch_row()) 
				{
					$customid = $row1[0];					
				}
		  	$result->close();
		  }
		 	else
		 		die("Erreur lors de la sélection de la boutic");
			
      $q = ' INSERT INTO administrateur (customid, pseudo, pass, email, actif) ';
  	  $q = $q . 'VALUES ("' . $customid . '","' . $pseudo . '","' . password_hash($pass, PASSWORD_DEFAULT) . '",
  	  "' . $email . '","' . 1 . '")';
      if ($conn->query($q) === FALSE) 
      {
		    die("Erreur lors de l'insertion de l'administrateur : " . $conn->error);
      }
      else
      {
        echo("L administrateur a été correctement inséré dans la boutic<br>");
        echo("<button onclick=\"window.location.href = 'directory.php?identif=" . $id . "';\">Cliquez ici pour créer l'arborscence</button>");
      }
    ?>

  </body>
</html>