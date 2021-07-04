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
     	
      $id = isset($_GET['identif']) ? $_GET['identif'] : '';
      $ishtml = isset($_POST['ishtml']) ? $_POST ['ishtml'] : '';
      $subject = isset($_POST['subject']) ? $_POST ['subject'] : '';
			$validsms = isset($_POST['validsms']) ? $_POST ['validsms'] : '';
			$verifcp = isset($_POST['verifcp']) ? $_POST ['verifcp'] : '';
			$paiement = isset($_POST['paiement']) ? $_POST ['paiement'] : '';
			$livr = isset($_POST['livr']) ? $_POST ['livr'] : '';      
			$compt = isset($_POST['compt']) ? $_POST ['compt'] : '';
			$vente = isset($_POST['vente']) ? $_POST ['vente'] : '';
			$livrer = isset($_POST['livrer']) ? $_POST ['livrer'] : '';      
			$emporter = isset($_POST['emporter']) ? $_POST ['emporter'] : '';
			$minicmd = isset($_POST['minicmd']) ? $_POST ['minicmd'] : '';      
			$minilivr = isset($_POST['minilivr']) ? $_POST ['minilivr'] : '';
			$sizeimg = isset($_POST['sizeimg']) ? $_POST ['sizeimg'] : '';
			$syspaie = isset($_POST['syspaie']) ? $_POST ['syspaie'] : '';
			$pkey = isset($_POST['pkey']) ? $_POST ['pkey'] : '';
			$skey = isset($_POST['skey']) ? $_POST ['skey'] : '';
			$clientpp = isset($_POST['clientpp']) ? $_POST ['clientpp'] : '';

      if (empty($id)==TRUE ) {
        die("Identifiant vide");
      }
     
			      
			$parametres = array (
  			array("isHTML_mail", $ishtml, "HTML activé pour l'envoi de mail"),
  			array("Subject_mail",$subject,"Sujet du courriel pour l'envoi de mail"),
  			array("VALIDATION_SMS", $validsms, "Commande validée par sms ?"),
  			array("VerifCP", $verifcp, "Activation de la verification des codes postaux"),
  			array("Choix_Paiement", $paiement, "COMPTANT ou LIVRAISON ou TOUS"),
  			array("MP_Comptant", $compt, "Texte du paiement comptant"),
  			array("MP_Livraison", $livr, "Texte du paiement à la livraison"),
  			array("Choix_Method", $vente, "TOUS ou EMPORTER ou LIVRER"),
  			array("CM_Livrer", $livrer, "Texte de la vente à la livraison"),
  			array("CM_Emporter", $emporter, "Texte de la vente à emporter"),
  			array("MntCmdMini", $minicmd, "Montant commande minimal"),
  			array("MntLivraisonMini", $minilivr, "Montant Minimum pour accepter la livraison"),
  			array("SIZE_IMG", $sizeimg, "bigimg ou smallimg"),
  			array("CMPT_CMD", "0", "Compteur des références des commandes"),
  			array("MONEY_SYSTEM", $syspaie, "STRIPE ou PAYPAL"),
  			array("PublicKey", $pkey, "Clé public stripe"),
  			array("SecretKey", $skey, "Clé privé stripe"),
  			array("ID_CLT_PAYPAL", $clientpp, "ID Client PayPal"),
			);

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
			
			for($i=0; $i<count($parametres); $i++)
			{			
	      $q = ' INSERT INTO parametre (customid, nom, valeur, commentaire) ';
	  	  $q = $q . 'VALUES ("' . $customid . '","' . $parametres[$i][0] . '","' . $parametres[$i][1] . '","' . $parametres[$i][2] . '")';
	  	  
	      if ($conn->query($q) === FALSE) 
	      {
			    die("Erreur lors de l'insertion d'un parametre : " . $conn->error);
	      }
	      else
	      	echo ("Parametre " . $parametres[$i][0] . " inséré <br>");
	    }
	    

		$statuts = array (
  			array("Commande à faire", "#E2001A", "Bonjour, votre commande à été transmise. %boutic% vous remercie et vous tiendra informé de son avancé. ", 1, 1),
  			array("En cours de préparation", "#EB690B","Votre commande est en cours de préparation. ", 0, 1),
  			array("En cours de livraison", "#E2007A", "Votre commande est en cours de livraison, ", 0, 1),
  			array("Commande à disposition", "#009EE0", "Votre commande est à disposition", 0, 1),
  			array("Commande terminée", "#009036", "%boutic% vous remercie pour votre commande. À très bientôt. ", 0, 1),
  			array("Commande anulée", "#1A171B", "Nous ne pouvons donner suite à votre commande. Pour plus d\'informations, merci de nous contacter. ", 0, 1),
			);

		for($i=0; $i<count($statuts); $i++)
		{			
	    $q = ' INSERT INTO statutcmd (customid, etat, couleur, message, defaut, actif) ';
	    $q = $q . 'VALUES ("' . $customid . '","' . $statuts[$i][0] . '","' . $statuts[$i][1] . '","' . $statuts[$i][2] . '","' . $statuts[$i][3] . '","' . $statuts[$i][4] . '")';
	  	  
	    if ($conn->query($q) === FALSE) 
	    {
		    die("Erreur lors de l'insertion d'un statut de commande : " . $conn->error);
	    }
	    else
	    	echo ("Statut de commande " . $statuts[$i][0] . " inséré <br>");
	  }

	  echo("<br>");	  
    echo("<button onclick=\"window.location.href = 'depart.php'\">Cliquez ici pour créer une autre boutique</button>");
    echo "<br>";
    echo("<button onclick=\"window.location.href = 'logout.php'\">Cliquez ici pour quitter le superadmin</button>");


    ?>

  </body>
</html>
