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
			
	    if (empty($_SESSION['superadmin_auth']) == TRUE)
	    {
	   	  header("LOCATION: index.php");
	   	  exit();
	    }	
	    
	    if (strcmp($_SESSION['superadmin_auth'],'superadmin') != 0)
		  {
	   	  header("LOCATION: index.php");
	   	  exit();
		  }
   
      include "../config/common_cfg.php";
      include "../param.php";
     	
      $id = isset($_POST['identif']) ? $_POST ['identif'] : '';
      
      if (empty($id)==TRUE ) {
        die("Identifiant vide");
      }

      // Create connection
      $conn = new mysqli($servername, $username, $password, $bdd);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

		  $query = 'SELECT customid FROM customer WHERE customer = "' . $id .'"';
		
			if ($result = $conn->query($query)) {
		    if ($result->num_rows > 0)
		      die("Identifiant déjà existant ");
		  }
			
		  $q = ' INSERT INTO customer (customer) ';
  	  $q = $q . 'VALUES ("' . $id . '")';
      if ($conn->query($q) === FALSE) 
      {
		    die("erreur lors de l insertion: " . $conn->connect_error);
      }
      else
      {
        echo("La boutique a été déclaré dans la base de donnée<br><br>");
        echo("<button onclick=\"window.location.href = 'admin.php?identif=" . $id . "';\">Cliquez ici pour créer un administrateur</button>");
      }

    ?>

  </body>
</html>