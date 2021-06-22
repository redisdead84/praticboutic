<!--
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

      if (empty($id)==TRUE ) {
        die("Identifiant vide");
      }
			
      $conn = new mysqli($servername, $username, $password, $bdd);

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
					echo "<br>";
					$customid = $row1[0];
					echo "Id de la boutic : ";
					echo $customid;
					echo "<br>";
				}
		  	$result->close();
		  }
		 	else
		 		die("Erreur lors de la sélection de la boutic");
		 		
			$dossier = "";
			$fichier = isset($_FILES['logo']) ? basename($_FILES['logo']['name']) : ''; 
 
	 	  if(empty($fichier) == FALSE)
	 	  {  
        $dossier = '../../' . $id . '/img/';
        $taille_maxi = intval(GetValeurParam("Max_file_size", $conn, $customid, "5000000"));
        $taille = filesize($_FILES['logo']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
        $extension = strtolower(strrchr($_FILES['logo']['name'], '.')); 
        //Début des vérifications de sécurité...
        if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
        {
          $error = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg...';
        }
        if($taille>$taille_maxi)
        {
          $error = 'Le fichier est trop gros...';
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
	      if(empty($error) == TRUE)
	      {
          $q = 'UPDATE parametre ';
          $q = $q . 'SET valeur = "img/' . $fichier . '" ';
       	  $q = $q . 'WHERE customid = ' . $customid . ' AND nom = "master_logo"';
       	  
      	  if ($conn->query($q) === FALSE) 
      	  {
     		    $error = $conn->error;
   	      }
   	      else 
   	      {
     	      echo "Logo MAJ OK<br>";
     	      echo("<button onclick=\"window.location.href = 'depart.php'\">Cliquez ici pour créer une autre boutique</button>");
     	      echo "<br>";
     	      echo("<button onclick=\"window.location.href = 'logout.php'\">Cliquez ici pour quitter le superadmin</button>");
     	    }
	      }
				
	      if(empty($error) == FALSE)
	      {
					die($error);
	      }
			}
			else
			  die("fichier vide");
    ?>

  </body>
</html>
-->