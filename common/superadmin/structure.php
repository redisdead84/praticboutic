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
     	
      $id = isset($_GET['identif']) ? $_GET ['identif'] : '';
      $metdef = isset($_POST['metdef']) ? $_POST ['metdef'] : '';
      
      
      if (empty($id)==TRUE ) {
        die("Identifiant vide");
      }

      $mdef = intval($metdef);

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
					echo "Boutic trouvé <br>";					
				}
		  	$result->close();
		  }
		 	else
		 		die("Erreur lors de la sélection de la boutic");

			umask(0000);
			chdir ("../..");
			// creation repertoire du client
			if (!file_exists($id))
				if (!mkdir($id, 0775))
				{
					$error = error_get_last();
    			echo $error['message'];
    			echo "<br>";
    			die("Impossible de crér le répertoire : " . $id);				
				}
				else
					echo "Repertoire " . $id . " créé<br>";
					
			chdir ($id);		
			$idx = fopen("index.php", "w");
			fwrite($idx, "<?php\n\n  include \"config/custom_cfg.php\";\n\n  header('LOCATION: ../common/carte.php?method=' . \$metdef . '&customer=' . \$customer);\n\n?>");
			fclose($idx);
			echo "fichier index.php créé<br>";
							
			/*if (!file_exists("admin"))
				if (!mkdir("admin"))
				{
					$error = error_get_last();
    			echo $error['message'];
    			echo "<br>";
    			die("Impossible de crér le répertoire : " . $id . "/admin");				
				}
				else
					echo "Repertoire admin créé<br>";
					
			chdir("admin");		
			$idx2 = fopen("index.php", "w");
			fwrite($idx2, "<?php\n\n  session_start();\n  include \"../config/custom_cfg.php\";\n  \$_SESSION['boutic'] = \$customer;\n  header('LOCATION: ../../common/admin/index.php?customer=' . \$customer);\n\n?>");
			fclose($idx2);
			echo "fichier index.php créé<br>";
			chdir("..");*/
			
			if (!file_exists("config"))
				if (!mkdir("config"))
				{
					$error = error_get_last();
    			echo $error['message'];
    			echo "<br>";
    			die("Impossible de crér le répertoire : " . $id . "/config");				
				}
		  else
				echo "Repertoire config créé<br>";
		  chdir("config");		
			$config = fopen("custom_cfg.php", "w");
			fwrite($config, "<?php\n    \$customer = \"" . $id . "\";\n    \$metdef = \"" . $mdef . "\";\n    \$ver_cust_css = \"1.00\";\n?>");
			fclose($config);
			echo "fichier custom_cfg.php créé<br>";
			chdir("..");
			
			if (!file_exists("css"))
				if (!mkdir("css"))
				{
					$error = error_get_last();
    			echo $error['message'];
    			echo "<br>";
    			die("Impossible de crér le répertoire : " . $id . "/css");				
				}
				else
				  echo "Repertoire css créé<br>";
			chdir("css");
			$ccss = fopen("custom.css", "w");
			fwrite($ccss, "\n");
			fclose($ccss);
			echo "fichier custom.css créé<br>";
			chdir("..");
			
			if (!file_exists("img"))
				if (!mkdir("img"))
				{
					$error = error_get_last();
    			echo $error['message'];
    			echo "<br>";
    			die("Impossible de crér le répertoire : " . $id . "/img");				
				}
				else
				  echo "Repertoire img créé<br>";
				  
			if (!file_exists("upload"))
				if (!mkdir("upload"))
				{
					$error = error_get_last();
    			echo $error['message'];
    			echo "<br>";
    			die("Impossible de créer le répertoire : " . $id . "/upload");				
				}
				else
				  echo "Repertoire upload créé<br>";
				  
			echo("<br>");	  
			echo("<button onclick=\"window.location.href = 'parametre.php?identif=" . $id . "';\">Cliquez ici pour créer les paramétres</button>");
			
    ?>

  </body>
</html>


