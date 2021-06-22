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
      $nom = isset($_POST['nom']) ? $_POST ['nom'] : '';
      $adresse1 = isset($_POST['adresse1']) ? $_POST ['adresse1'] : '';
      $adresse2 = isset($_POST['adresse2']) ? $_POST ['adresse2'] : '';
      $codepostal = isset($_POST['codepostal']) ? $_POST ['codepostal'] : '';
      $ville = isset($_POST['ville']) ? $_POST ['ville'] : '';
      //$logo = isset($_POST['logo']) ? $_POST ['logo'] : '';
      $courriel = isset($_POST['courriel']) ? $_POST ['courriel'] : '';
      $metdef = isset($_POST['metdef']) ? $_POST ['metdef'] : '';

      
      if (empty($id)==TRUE ) {
        die("Identifiant vide");
      }
      
      $notid = array('admin', 'common', 'css', 'img', 'vendor');
      if(in_array($id, $notid)) //Si l'extension n'est pas dans le tableau
      {
        die('Identifiant interdit');
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
		  
		  umask(0000);
			chdir ("../..");
			// creation repertoire du client
			if (!file_exists($id))
			{
				if (!mkdir($id, 0775))
				{
					$error = error_get_last();
    			echo $error['message'];
    			echo "<br>";
    			die("Impossible de crér le répertoire : " . $id);				
				}
				else
					echo "Repertoire " . $id . " créé<br>";
			}
			else
				die("répertoire déjà existant");
					
			chdir ($id);		
			$idx = fopen("index.php", "w");
			fwrite($idx, "<?php\n\n  include \"config/custom_cfg.php\";\n\n  header('LOCATION: ../common/carte.php?method=' . \$metdef . '&customer=' . \$customer);\n\n?>");
			fclose($idx);
			echo "fichier index.php créé<br>";
			
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
			fwrite($config, "<?php\n    \$customer = \"" . $id . "\";\n    \$metdef = \"" . $metdef . "\";\n?>");
			fclose($config);
			echo "fichier custom_cfg.php créé<br>";
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
/*
			$dossier = "";
			
			$fichier = isset($_FILES['logo']) ? basename($_FILES['logo']['name']) : '';
		
	 	  if(empty($fichier) == FALSE)
	 	  {  
        $dossier = '../../' . $id . '/upload/';
        $taille_maxi = intval($maxfilesize);
        $taille = filesize($_FILES['logo']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
        $mimes = array('image/gif','image/png','image/jpeg'); 
        $extension = strtolower(strrchr($_FILES['logo']['name'], '.')); 
        $mimetype = mime_content_type($_FILES['logo']['tmp_name']);
        //Début des vérifications de sécurité...
        if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
        {
          $error = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg...';
        }
        if($taille>$taille_maxi)
        {
          $error = 'Le fichier est trop gros...';
        } 
        if(!in_array($mimetype, $mimes))
		    {
    			$error = 'type mime non reconnu';
    		}
        if(empty($error) == TRUE) //S'il n'y a pas d'erreur, on upload
        {
          //On formate le nom du fichier ici...
          $fichier = strtr($fichier, 
                'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
                'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
          $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
          if(!(move_uploaded_file($_FILES['logo']['tmp_name'], $dossier . $fichier))) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
          {
            $error = 'Echec de l\'upload !';
          }
        }

	      if(empty($error) == FALSE)
	      {
					die($error);
	      }
	      else
	      	echo("Le logo a été uploadé<br><br>");
			}
		*/
		  $q = ' INSERT INTO customer (customer, nom, adresse1, adresse2, codepostal, ville, logo, courriel) ';
  	  $q = $q . 'VALUES ("' . $id . '", "' . $nom . '", "' . $adresse1 . '", "' . $adresse2 . '", "' . $codepostal . '", "' . $ville . '", "", "' . $courriel . '")';
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