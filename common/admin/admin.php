<!DOCTYPE html>
<html id="backhtml">
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.01">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body id="backbody">

  <?php
    
    $customer = $_GET['customer'];    
    
  	session_start();
  	
    if (empty($_SESSION[$customer . '_auth']) == TRUE)
    {
   	  header("LOCATION: ../../" . $customer . "/index.php");
   	  exit();
    }	
    
    if (strcmp($_SESSION[$customer . '_auth'],'oui') != 0)
	  {
   	  header("LOCATION: index.php");
   	  exit();
	  }
       
    include "../config/common_cfg.php";
    include "../param.php";


    if (empty($customer) == TRUE)
    {
   	  header("LOCATION: ../../" . $customer . "/admin/index.php");
   	  exit();
    }	
    
/*    if (strcmp($customer, $customer) != 0)
	  {
   	  header("LOCATION: ../../" . $customer . "/admin/index.php");
   	  exit();
	  }*/
		
    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);
    // Check connection
    if ($conn->connect_error) 
    	die("Connection failed: " . $conn->connect_error);
    
    $reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
 	  $reqci->bind_param("s", $customer);
 	  $reqci->execute();
 	  $reqci->bind_result($customid);
 	  $resultatci = $reqci->fetch();
 	  $reqci->close();
    
    
  	$table = isset($_GET['table']) ? $_GET ['table'] : '';
		$cmpcat = strcmp($table,'categorie');
		$cmpart = strcmp($table,'article');
		$cmprelgrpoptart = strcmp($table,'relgrpoptart');
		$cmpgrpopt = strcmp($table,'groupeopt');
		$cmpopt = strcmp($table,'option');
		$cmpcpzone = strcmp($table,'cpzone');
		$cmpbarlivr = strcmp($table,'barlivr');
		$cmpadmin = strcmp($table,'administrateur');
		$cmpparam = strcmp($table,'parametre');
		$cmd = isset($_GET['commande']) ? $_GET ['commande'] : '';
		$cmpi = strcmp($cmd,'insert');
		$cmpu = strcmp($cmd,'update');
  	$modif = isset($_GET['modifier']) ? $_GET ['modifier'] : '';
		$ins = isset($_GET['inserer']) ? $_GET ['inserer'] : '';
		$recupd = isset($_GET['rectou']) ? $_GET ['rectou'] : '';
	  $mode = isset($_GET['mode']) ? $_GET ['mode'] : '';
		
		$error = '';
		
	  if ($cmpcat == 0)
	  {
	    $q='';			
  	  $bVis = 1;
  	  if (isset($_POST['inpvis']))
	      $bVis = 1;
	    else 
	 	    $bVis = 0;

      if ($cmpi == 0)
      { 
      	$q = ' INSERT INTO categorie (customid, nom, visible) ';
    	  $q = $q . 'VALUES ("' . $customid . '","' . htmlentities($_POST['inpnom']) . '" ,"' . $bVis . '")';
        if ($conn->query($q) === FALSE) 
        {
  		    $ins = 1;
  		    $error = $conn->error;
  		    $cmpu = 1; $modif = 0;
	      }
	      else
          header("LOCATION: admin.php?customer=" . $customer );
      }
      if ($cmpu == 0)
      {
        $q = 'UPDATE categorie ';
        $q = $q . 'SET customid = "' . $customid . '",nom = "' . htmlentities($_POST['inpnom']) . '",visible = "' . $bVis . '" ';
    	  $q = $q . 'WHERE catid = "' . $recupd . '"';
    	  if ($conn->query($q) === FALSE) 
    	  {
  		    $modif = $recupd;
  		    $error = $conn->error;
  		    $cmpi = 1;
	      }
	      else 
  	      header("LOCATION: admin.php?customer=" . $customer );
   	  }
      
	    if ($modif > 0)
	    {
	      $req1 = $conn->prepare('SELECT nom, visible FROM categorie WHERE catid = ?');
     	  $req1->bind_param("s", $modif);
     	  $req1->execute();
     	  $req1->bind_result($nom,$visible);
     	  $resultat1 = $req1->fetch();
     	  $req1->close();
	    }
	  }

	  if ($cmpart == 0)
	  {
	    $q='';			
  	  $bVis = 1;
  	  if (isset($_POST['inpvisart']))
  	    $bVis = 1;
	    else 
	 	    $bVis = 0;

  	  if (isset($_POST['inpimgvis']))
  	    $bImgVis = 1;
	    else 
	 	    $bImgVis = 0;
	 	    
	 	  if (isset($_POST['inpobliga']))
  	    $bObliga = 1;
	    else 
	 	    $bObliga = 0;

	 	    	 	    
      $dossier = "";
      $fichier = isset($_FILES['inpimgart']) ? basename($_FILES['inpimgart']['name']) : '';  
	 	  if(empty($fichier) == FALSE)
	 	  {  
        $dossier = '../../' . $customer . '/upload/';
        $taille_maxi = intval(GetValeurParam("Max_file_size", $conn, $customid, "5000000"));
        $taille = filesize($_FILES['inpimgart']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg');
        $extension = strtolower(strrchr($_FILES['inpimgart']['name'], '.')); 
        //Début des vérifications de sécurité...
        if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
        {
          $error = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg...';
          if ($cmpi == 0)
          {
            $ins = 1;
            $modif = 0;
          }          
          if ($cmpu == 0)
          {
            $ins = 0;
            $modif = $recupd;
          }          
        }
        if($taille>$taille_maxi)
        {
          $error = 'Le fichier est trop gros...';
          if ($cmpi == 0)
          {
            $ins = 1;
            $modif = 0;
          }          
          if ($cmpu == 0)
          {
            $ins = 0;
            $modif = $recupd;
          }          
        } 
        if(empty($error) == TRUE) //S'il n'y a pas d'erreur, on upload
        {
          //On formate le nom du fichier ici...
          $fichier = strtr($fichier, 
                'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
                'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
          $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
          if(!(move_uploaded_file($_FILES['inpimgart']['tmp_name'], $dossier . $fichier))) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
          {
            $error = 'Echec de l\'upload !';
            if ($cmpi == 0)
            {
              $ins = 1;
              $modif = 0;
            }          
            if ($cmpu == 0)
            {
              $ins = 0;
              $modif = $recupd;
            }          
          }
        }
      }
      if(empty($error) == TRUE)
      {
        if ($cmpi == 0)
        {
          $q = ' INSERT INTO article (customid, nom, prix, description, catid, unite, visible, image, imgvisible, obligatoire) ';
      	  $q = $q . 'VALUES ("' . $customid . '","' . htmlentities($_POST['inpnomart']) . '","' . $_POST['inpprixart'] . '",
       	  "' . htmlentities($_POST['inpdescart']) . '","' . $_POST['inpcatart'] . '",
       	  "' . htmlentities($_POST['inpuniteart']) . '","' . $bVis . '","' . $fichier . '",
       	  "' . $bImgVis . '","' . $bObliga . '" )';
          if ($conn->query($q) === FALSE) 
          {
     		    $ins = 1;
     		    $error = $conn->error;
     		    $cmpu = 1; $modif = 0;
          }
          else
             header("LOCATION: admin.php?customer=" . $customer );
        }
        if ($cmpu == 0)
        {
          if (strcmp($fichier, "") == 0)
          {
            $q = 'UPDATE article ';
            $q = $q . 'SET customid = "' . $customid . '", nom = "' . htmlentities($_POST['inpnomart']) . '", prix = "' . $_POST['inpprixart'] . '",
            description = "' . htmlentities($_POST['inpdescart']) . '", catid = "' . $_POST['inpcatart'] . '",
            unite = "' . htmlentities($_POST['inpuniteart']) . '", visible = "' . $bVis . '", imgvisible = "' . $bImgVis . '",  
            obligatoire = "' . $bObliga . '" ';
         	  $q = $q . 'WHERE artid = "' . $recupd . '"';
          }
          else 
          {
            $q = 'UPDATE article ';
            $q = $q . 'SET customid = "' . $customid . '", nom = "' . htmlentities($_POST['inpnomart']) . '", prix = "' . $_POST['inpprixart'] . '",
            description = "' . htmlentities($_POST['inpdescart']) . '", catid = "' . $_POST['inpcatart'] . '",
            unite = "' . htmlentities($_POST['inpuniteart']) . '", visible = "' . $bVis . '", 
            image = "' . $fichier . '", imgvisible = "' . $bImgVis . '", obligatoire = "' . $bObliga . '" ';
         	  $q = $q . 'WHERE artid = "' . $recupd . '"';
    	    }          
          
      	  if ($conn->query($q) === FALSE) 
      	  {
    		    $modif = $recupd;
     		    $error = $conn->error;
     		    $cmpi = 1;
   	      }
   	      else 
     	      header("LOCATION: admin.php?customer=" . $customer );
       	}
      }
        
	    if ($modif > 0)
	    {
	      $req2 = $conn->prepare('SELECT nom, prix, description, catid, 
	    					   unite, visible, image, imgvisible, obligatoire FROM article WHERE artid = ?');
        $req2->bind_param("s", $modif);
        $req2->execute();
        $req2->bind_result($nom2, $prix2, $desc2, $catid2, $unite2, $visible2, $image2, $imgvis2, $obliga2);
        $resultat2 = $req2->fetch();
        $req2->close(); 
      	
	    }
	  }

 	  if ($cmprelgrpoptart == 0)
	  {
	    $q='';			
  	  $bVis = 1;
  	  if (isset($_POST['inpvis']))
	      $bVis = 1;
	    else 
	 	    $bVis = 0;

      if ($cmpi == 0)
      { 
      	$q = ' INSERT INTO relgrpoptart (customid, grpoptid, artid, visible) ';
    	  $q = $q . 'VALUES ("' . $customid . '","' . $_POST['inpgrpopt'] . '", "' . $_POST['inpart'] . '", "' . $bVis . '")';
        if ($conn->query($q) === FALSE) 
        {
  		    $ins = 1;
  		    $error = $conn->error;
  		    $cmpu = 1; $modif = 0;
	      }
	      else
          header("LOCATION: admin.php?customer=" . $customer );
      }
      if ($cmpu == 0)
      {
        $q = 'UPDATE relgrpoptart ';
        $q = $q . 'SET customid = "' . $customid . '", grpoptid = "' . $_POST['inpgrpopt'] . '", artid = "' . $_POST['inpart'] . '", visible = "' . $bVis . '" ';
    	  $q = $q . 'WHERE relgrpoartid = "' . $recupd . '"';
    	  if ($conn->query($q) === FALSE) 
    	  {
  		    $modif = $recupd;
  		    $error = $conn->error;
  		    $cmpi = 1;
	      }
	      else 
  	      header("LOCATION: admin.php?customer=" . $customer );
   	  }
      
	    if ($modif > 0)
	    {
	      $req5 = $conn->prepare('SELECT grpoptid, artid, visible FROM relgrpoptart WHERE relgrpoartid = ?');
     	  $req5->bind_param("s", $modif);
     	  $req5->execute();
     	  $req5->bind_result($grpoptid5, $artid5, $visible5);
     	  $resultat5 = $req5->fetch();
     	  $req5->close();
	    }
	  }

 	  if ($cmpgrpopt == 0)
	  {
	    $q='';			
  	  $bVis = 1;
  	  if (isset($_POST['inpvis']))
	      $bVis = 1;
	    else 
	 	    $bVis = 0;
  	  $bMulti = 0;
  	  if (isset($_POST['inpmulti']))
	      $bMulti = 1;
	    else 
	 	    $bMulti = 0;
	 	    
      if ($cmpi == 0)
      { 
      	$q = 'INSERT INTO groupeopt (customid, nom, visible, multiple) ';
    	  $q = $q . 'VALUES ("' . $customid . '", "' . htmlentities($_POST['inpnom']) . '", "' . $bVis . '", "' . $bMulti . '")';
        if ($conn->query($q) === FALSE) 
        {
  		    $ins = 1;
  		    $error = $conn->error;
  		    $cmpu = 1; $modif = 0;
	      }
	      else
          header("LOCATION: admin.php?customer=" . $customer );
      }
      if ($cmpu == 0)
      {
        $q = 'UPDATE groupeopt ';
        $q = $q . 'SET customid = "' . $customid . '", nom = "' . htmlentities($_POST['inpnom']) . '", visible = "' . $bVis . '", multiple = "' . $bMulti . '" ';
    	  $q = $q . 'WHERE grpoptid = "' . $recupd . '"';
    	  if ($conn->query($q) === FALSE) 
    	  {
  		    $modif = $recupd;
  		    $error = $conn->error;
  		    $cmpi = 1;
	      }
	      else 
  	      header("LOCATION: admin.php?customer=" . $customer );
   	  }
      
	    if ($modif > 0)
	    {
	      $req6 = $conn->prepare('SELECT nom, visible, multiple FROM groupeopt WHERE grpoptid = ?');
     	  $req6->bind_param("s", $modif);
     	  $req6->execute();
     	  $req6->bind_result($nom6,$visible6,$multiple6);
     	  $resultat6 = $req6->fetch();
     	  $req6->close();
	    }
	  }

 	  if ($cmpopt == 0)
	  {
	    $q='';			
  	  $bVis = 1;
  	  if (isset($_POST['inpvis']))
	      $bVis = 1;
	    else 
	 	    $bVis = 0;

      if ($cmpi == 0)
      { 
      	$q = 'INSERT INTO `option` (customid, nom, surcout, grpoptid, visible) ';
    	  $q = $q . 'VALUES ("' . $customid . '","' . htmlentities($_POST['inpnom']) . '","' . $_POST['inpsurcout'] . '","' . $_POST['inpgrpopt'] . '", "' . $bVis . '")';
        if ($conn->query($q) === FALSE) 
        {
  		    $ins = 1;
  		    $error = $conn->error;
  		    $cmpu = 1; $modif = 0;
	      }
	      else
          header("LOCATION: admin.php?customer=" . $customer );
      }
      if ($cmpu == 0)
      {
        $q = 'UPDATE `option` ';
        $q = $q . 'SET customid = "'. $customid . '", nom = "' . htmlentities($_POST['inpnom']) . '", surcout = "' . $_POST['inpsurcout'] . '", grpoptid = "' . $_POST['inpgrpopt'] . '", visible = "' . $bVis . '" ';
    	  $q = $q . 'WHERE optid = "' . $recupd . '"';
    	  if ($conn->query($q) === FALSE) 
    	  {
  		    $modif = $recupd;
  		    $error = $conn->error;
  		    $cmpi = 1;
	      }
	      else 
  	      header("LOCATION: admin.php?customer=" . $customer );
   	  }
      
	    if ($modif > 0)
	    {
	      $req7 = $conn->prepare('SELECT nom, surcout, grpoptid, visible FROM `option` WHERE optid = ?');
     	  $req7->bind_param("s", $modif);
     	  $req7->execute();
     	  $req7->bind_result($nom7, $surcout7, $grpoptid7, $visible7);
     	  $resultat7 = $req7->fetch();
     	  $req7->close();
	    }
	  }

	  if ($cmpadmin == 0)
	  {
  	  $q='';			
    	$bAct = 1;
  	  if (isset($_POST['inpactifad']))
  	    $bAct = 1;
	    else 
	 	    $bAct = 0;

      if ($cmpi == 0)
      {
        $q = ' INSERT INTO administrateur (customid, pseudo, pass, email, actif) ';
    	  $q = $q . 'VALUES ("' . $customid . '","' . htmlentities($_POST['inppseudoad']) . '","' . password_hash($_POST['inppassad'], PASSWORD_DEFAULT) . '",
    	  "' . $_POST['inpemailad'] . '","' . $bAct . '")';
        if ($conn->query($q) === FALSE) 
        {
  		    $ins = 1;
  		    $error = $conn->error;
  		    $cmpu = 1; $modif = 0;
	      }
	      else
          header("LOCATION: admin.php?customer=" . $customer );
      }
      if ($cmpu == 0)
      {
        $motdepasse = $_POST['inppassad'];
        if (strcmp($motdepasse, "") == 0)
        {
          $q = 'UPDATE administrateur ';
          $q = $q . 'SET customid = "' . $customid . '", pseudo = "' . htmlentities($_POST['inppseudoad']) . '", email = "' . $_POST['inpemailad'] . '", actif = "' . $bAct . '" ';
      	  $q = $q . 'WHERE adminid = "' . $recupd . '"';
        }
        else 
        {
          $q = 'UPDATE administrateur ';
          $q = $q . 'SET customid="' . $customid . '",pseudo = "' . htmlentities($_POST['inppseudoad']) . '", pass = "' . password_hash($motdepasse, PASSWORD_DEFAULT) . '",
          email = "' . $_POST['inpemailad'] . '", actif = "' . $bAct . '" ';
      	  $q = $q . 'WHERE adminid = "' . $recupd . '"';
  	    }
    	  if ($conn->query($q) === FALSE) 
    	  {
  		    $modif = $recupd;
  		    $error = $conn->error;
  		    $cmpi = 1;
	      }
	      else 
  	      header("LOCATION: admin.php?customer=" . $customer );
   	  }
      
	    if ($modif > 0)
	    {
	      $req3 = $conn->prepare('SELECT pseudo, email, actif  
	    					   FROM administrateur WHERE adminid = ?');
     	  $req3->bind_param("s", $modif);
     	  $req3->execute();
     	  $req3->bind_result($pseudo3, $email3, $actif3);
     	  $resultat3 = $req3->fetch();
     	  $req3->close();
     	
	    } 
	  }
	    
	  if ($cmpparam == 0)
	  {
	    $q='';			

      if ($cmpi == 0)
      { 
      	$q = ' INSERT INTO parametre (customid, nom, valeur, commentaire) ';
    	  $q = $q . 'VALUES ("' . $customid . '","' . $_POST['inpnompa'] . '" ,"' . $_POST['inpvaleurpa'] . '" ,"' . $_POST['inpcommentpa'] . '")';
        if ($conn->query($q) === FALSE) 
        {
  		    $ins = 1;
  		    $error = $conn->error;
  		    $cmpu = 1; $modif = 0;
	      }
	      else
          header("LOCATION: admin.php?customer=" . $customer );
      }
      if ($cmpu == 0)
      {
        $q = 'UPDATE parametre ';
        $q = $q . 'SET customid = "'. $customid . '",nom = "' . $_POST['inpnompa'] . '",valeur = "' . $_POST['inpvaleurpa'] . '",commentaire = "' . $_POST['inpcommentpa'] . '" ';
    	  $q = $q . 'WHERE paramid = "' . $recupd . '"';
          	  
    	  if ($conn->query($q) === FALSE) 
    	  {
  		    $modif = $recupd;
  		    $error = $conn->error;
  		    $cmpi = 1;
	      }
	      else 
  	      header("LOCATION: admin.php?customer=" . $customer );
   	  }
      
	    if ($modif > 0)
	    {
	      $req4 = $conn->prepare('SELECT nom, valeur, commentaire FROM parametre WHERE paramid = ?');
     	  $req4->bind_param("s", $modif);
     	  $req4->execute();
     	  $req4->bind_result($nom4,$valeur4,$commentaire4);
     	  $resultat4 = $req4->fetch();
     	  $req4->close();
	    }
	  }
	  
 	  if ($cmpcpzone == 0)
	  {
	    $q='';			
  	  $bActif = 1;
  	  if (isset($_POST['inpactif']))
	      $bActif = 1;
	    else 
	 	    $bActif = 0;

      if ($cmpi == 0)
      { 
      	$q = 'INSERT INTO `cpzone` (customid, codepostal, ville, actif) ';
    	  $q = $q . 'VALUES ("' . $customid . '","' . $_POST['inpcp'] . '","' . htmlentities($_POST['inpville']) . '","' . $bActif . '")';
        if ($conn->query($q) === FALSE) 
        {
  		    $ins = 1;
  		    $error = $conn->error;
  		    $cmpu = 1; $modif = 0;
	      }
	      else
          header("LOCATION: admin.php?customer=" . $customer );
      }
      if ($cmpu == 0)
      {
        $q = 'UPDATE `cpzone` ';
        $q = $q . 'SET customid = "' . $customid . '", codepostal = "' . $_POST['inpcp'] . '", ville = "' . htmlentities($_POST['inpville']) . '", actif = "' . $bActif . '" ';
    	  $q = $q . 'WHERE cpzoneid = "' . $recupd . '"';
    	  if ($conn->query($q) === FALSE) 
    	  {
  		    $modif = $recupd;
  		    $error = $conn->error;
  		    $cmpi = 1;
	      }
	      else 
  	      header("LOCATION: admin.php?customer=" . $customer );
   	  }
      
	    if ($modif > 0)
	    {
	      $req8 = $conn->prepare('SELECT codepostal, ville, actif FROM `cpzone` WHERE cpzoneid = ?');
     	  $req8->bind_param("s", $modif);
     	  $req8->execute();
     	  $req8->bind_result($cp8, $ville8, $actif8);
     	  $resultat8 = $req8->fetch();
     	  $req8->close();
	    }
	  }
	  
	  if ($cmpbarlivr == 0)
	  {
	    $q='';		
	    	
  	  $bLimHaute = 1;
  	  if (isset($_POST['inplimitehaute']))
	      $bLimHaute = 1;
	    else 
	 	    $bLimHaute = 0;
	 	    
  	  $bLimBasse = 1;
  	  if (isset($_POST['inplimitebasse']))
	      $bLimBasse = 1;
	    else 
	 	    $bLimBasse = 0;
	 	    
      if ($cmpi == 0)
      { 
      	$q = 'INSERT INTO `barlivr` (customid, valminin, valmaxex, surcout, limitebasse, limitehaute) ';
    	  $q = $q . 'VALUES ("' . $customid . '","' . $_POST['inpvalminin'] . '","' . $_POST['inpvalmaxex'] . '","' . $_POST['inpsurcout'] . '","' . $bLimBasse . '","' . $bLimHaute . '")';
        if ($conn->query($q) === FALSE) 
        {
  		    $ins = 1;
  		    $error = $conn->error;
  		    $cmpu = 1; $modif = 0;
	      }
	      else
          header("LOCATION: admin.php?customer=" . $customer );
      }
      if ($cmpu == 0)
      {
        $q = 'UPDATE `barlivr` ';
        $q = $q . 'SET customid = "' . $customid . '", valminin = "' . $_POST['inpvalminin'] . '", valmaxex = "' . $_POST['inpvalmaxex'] . '", surcout = "' . $_POST['inpsurcout'] . '", ';
        $q = $q . 'limitebasse = "' . $bLimBasse . '", limitehaute = "' . $bLimHaute . '" '; 
    	  $q = $q . 'WHERE barlivrid = "' . $recupd . '"';
    	  if ($conn->query($q) === FALSE) 
    	  {
  		    $modif = $recupd;
  		    $error = $conn->error;
  		    $cmpi = 1;
	      }
	      else 
  	      header("LOCATION: admin.php?customer=" . $customer );
   	  }
      
	    if ($modif > 0)
	    {
	      $req9 = $conn->prepare('SELECT valminin, valmaxex, surcout, limitebasse, limitehaute FROM `barlivr` WHERE barlivrid = ?');
     	  $req9->bind_param("s", $modif);
     	  $req9->execute();
     	  $req9->bind_result($valminin9, $valmaxex9, $surcout9, $limitebasse9, $limitehaute9);
     	  $resultat9 = $req9->fetch();
     	  $req9->close();
	    }
	  }

	  
    if (strcmp($mode, 'basique') == 0)
    {
      $_SESSION[$customer . '_mode'] = 'basique';
    }
    else if (strcmp($mode, 'avance') == 0)
    {
      $_SESSION[$customer . '_mode'] = 'avance';
    }

      
	  echo 'Bienvenue ';
	  echo $_SESSION[$customer . '_pseudo'];
	  echo ' ! <br />';
	  echo '<a href="logout.php?customer=' . $customer . '"><button type="button">Deconnexion</button></a>'," \n ";
    if (strcmp($_SESSION[$customer . '_mode'],'basique')== 0)
    {
      echo '<a href="admin.php?customer=' . $customer . '&mode=avance#tabRgoa"> <input type="button" value="Mode Avancé"> </a>';
    }
    else if (strcmp($_SESSION[$customer . '_mode'],'avance')== 0)  
    {
      echo '<a href="admin.php?customer=' . $customer . '&mode=basique#tabCat"> <input type="button" value="Mode Basique"> </a>';
    }
	  
	  echo '<br />';
	  echo '<br />';
      
    echo '<div class=tabs>'," \n ";
    echo '<div id=tabCat> <a href="admin.php?customer=' . $customer . '#tabCat">CATEGORIE</a>'," \n ";
    echo '<div class=sheet>'," \n ";

    
    if (empty($modif) && empty($ins))
    {
      echo '<div id="inlistCat">'," \n ";

      echo '<table>'," \n ";
      echo '<tr>';
      echo '<th>Nom</th>';
      echo '<th>Visible</th>';
      echo '</tr>'," \n ";
      $query2 = 'SELECT catid, nom, visible FROM categorie WHERE customid = ' . $customid  ;
      if ($result2 = $conn->query($query2)) 
	    {	
	      while ($row2 = $result2->fetch_row()) 
    	  {	
       	  echo '<tr>';
       	  echo '<td>';
       	  echo $row2[1];
       	  echo '</td>';
       	  echo '<td>';
       	  if ($row2[2] > 0) 
       	    echo '<input type="checkbox" disabled="disabled" checked>';
       	  else
       	    echo '<input type="checkbox" disabled="disabled">';
       	  echo '</td>';
       	  echo '<td>';
		      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row2[0] . '&table=categorie#tabCat"> <input type="button" value="Modifier"> </a>';
       	  echo '</td>';
      	  echo '</tr>'," \n ";

		    }						
	  	  $result2->close();
      }
      
      echo '</table>'," \n ";
	    echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=categorie#tabCat"> <input type="button" value="Insérer"> </a>'," \n ";
      echo '</div>'," \n ";      	
	  }
		
	  if ((!(empty($modif) && empty($ins))) && $cmpcat == 0)
  	{ 
	    echo '<div id="inRecordCat">'," \n ";
	    if (!empty($modif))
	    {
	      $action  = 'admin.php?customer=' . $customer . '&commande=update&table=categorie&rectou=' . $modif . '#tabCat';
		    echo '<form autocomplete="off" action="';
		    echo $action;
		    echo  '" method="POST">'," \n ";
	  	  echo '<label for="inpnom">Nom : </label><br>'," \n ";
	  	  echo '<input type="text" id="inpnom" name="inpnom" value="' . $nom . '" maxlength="40" required></input><br>'," \n ";
  	  	echo '<label for="inpvis">Visible : </label><br>'," \n ";
  	  	if ($visible == 1)
		      echo '<input type="checkbox" id="inpvis" name="inpvis" value="' . $visible . '" checked></input><br>'," \n ";
		    else 
		      echo '<input type="checkbox" id="inpvis" name="inpvis" value="' . $visible . '"></input><br>'," \n ";
		  		
		    echo '<a href="admin.php?customer=' . $customer . '#tabCat"><button type="button">Cancel</button></a>'," \n ";
      	echo '<input type="submit" value="Submit">'," \n ";
      	echo '</form>'," \n ";
      	
	    }
	    if (!(empty($ins)))
	    {
	      $action  = 'admin.php?customer=' . $customer . '&commande=insert&table=categorie#tabCat';
		    echo '<form autocomplete="off" action="';
		    echo $action;
		    echo  '" method="POST">'," \n ";
	  	  echo '<label for="inpnom">Nom : </label><br>'," \n ";
	  	  echo '<input type="text" id="inpnom" name="inpnom" maxlength="40" required></input><br>'," \n ";
  	  	echo '<label for="inpvis">Visible : </label><br>'," \n ";
	  	  echo '<input type="checkbox" id="inpvis" name="inpvis" value="1" checked></input><br>'," \n ";     
    		echo '<a href="admin.php?customer=' . $customer . '#tabCat"><button type="button">Cancel</button></a>'," \n ";
      	echo '<input type="submit" value="Submit">'," \n ";
	      echo '</form>'," \n ";
      }
      echo '<br />' . $error . " \n ";
	    echo '</div>'," \n ";
  	} 
  	echo '</div>'," \n ";
  	  
	  echo '</div>'," \n ";
      
    echo '<div id=tabArt> <a href="admin.php?customer=' . $customer . '#tabArt">ARTICLE</a>'," \n ";
    echo '<div class=sheet>'," \n ";

    if (empty($modif) && empty($ins))
    {
      echo '<div id="inlistArt">'," \n ";

      echo '<table>'," \n ";
      echo '<tr>';
      echo '<th>Nom</th>';
      echo '<th>Categorie</th>';
      echo '<th>Prix</th>';
      echo '<th>Unite</th>';
      echo '<th>Description</th>';
      echo '<th>Visible</th>';
      echo '<th>Image</th>';
      echo '<th>Afficher Image</th>';
      echo '<th>Obligatoire</th>';
      echo '</tr>'," \n ";
      $query2 = 'SELECT artid, article.nom, categorie.nom as catnom, article.prix,
                 article.unite, article.description, article.visible, article.image, 
                 article.imgvisible, article.obligatoire  
                 FROM article, categorie WHERE article.customid = ' . $customid . ' AND article.catid = categorie.catid';
      if ($result2 = $conn->query($query2)) 
      {	
	      while ($row2 = $result2->fetch_row()) 
    	  {	
       	  echo '<tr>';
       	  echo '<td>';
       	  echo $row2[1];
       	  echo '</td>';
       	  echo '<td>';
       	  echo $row2[2];
       	  echo '</td>';
       	  echo '<td>';
       	  echo number_format($row2[3], 2, ',', ' ');
       	  echo '</td>';
       	  echo '<td>';
       	  echo $row2[4];
       	  echo '</td>';
       	  echo '<td>';
       	  echo $row2[5];
       	  echo '</td>';
       	  echo '<td>';
       	  if ($row2[6] > 0) 
       	    echo '<input type="checkbox" disabled="disabled" checked>';
       	  else
       	    echo '<input type="checkbox" disabled="disabled">';
       	  echo '</td>';
       	  echo '<td>';
       	  echo $row2[7];
       	  echo '</td>';
       	  echo '<td>';
       	  if ($row2[8] > 0) 
       	    echo '<input type="checkbox" disabled="disabled" checked>';
       	  else
       	    echo '<input type="checkbox" disabled="disabled">';
       	  echo '</td>';
       	  echo '<td>';
       	  if ($row2[9] > 0) 
       	    echo '<input type="checkbox" disabled="disabled" checked>';
       	  else
       	    echo '<input type="checkbox" disabled="disabled">';
       	  echo '</td>';
       	  echo '<td>';
		      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row2[0] . '&table=article#tabArt"> <input type="button" value="Modifier"> </a>';
       	  echo '</td>';
      	  echo '</tr>'," \n ";
		    }  						
	  	  $result2->close();
      }
      
      echo '</table>'," \n ";
	    echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=article#tabArt"> <input type="button" value="Insérer"> </a>'," \n ";
      echo '</div>'," \n ";      	
	  }
		
	  if ((!(empty($modif) && empty($ins))) && $cmpart == 0)
	  { 
	    echo '<div id="inRecordArt">'," \n ";

	    if (!empty($modif))
	    {
		    $action1  = 'admin.php?customer=' . $customer . '&commande=update&table=article&rectou=' . $modif . '#tabArt';
		    echo '<form autocomplete="off" action="';
		    echo $action1;
		    echo  '" method="POST"  enctype="multipart/form-data">'," \n ";
	  	  echo '<label for="inpnomart">Nom : </label><br>'," \n ";
	  	  echo '<input type="text" id="inpnomart" name="inpnomart" value="' . $nom2 . '" required></input><br>'," \n ";
		
	  	  echo '<label for="inpcatart">Categorie : </label><br>'," \n ";
	  	  echo '<select id="inpcatart" name="inpcatart" required>'," \n ";
	  	  $queryopt2 = 'SELECT catid, nom, visible FROM categorie WHERE customid = ' . $customid;
      	if ($resultopt2 = $conn->query($queryopt2)) 
	  	  {	
	        while ($rowopt2 = $resultopt2->fetch_row()) 
    	    {	
					  if (strcmp($rowopt2[0],$catid2) == 0)
    	        echo '<option value="' . $rowopt2[0] . '" selected>' . $rowopt2[1] . '</option>'," \n ";
    	      else					    	    
    	        echo '<option value="' . $rowopt2[0] . '">' . $rowopt2[1] . '</option>'," \n ";
		      }
		    }       	  
  	  	$resultopt2->close();
        echo '</select><br>'," \n ";
        		
	    	echo '<label for="inpprixart">Prix : </label><br>'," \n ";
	    	echo '<input type="number" id="inpprixart" name="inpprixart" value="' . $prix2 . '" step="0.01" required></input><br>'," \n ";
 
		    echo '<label for="inpuniteart">Unite : </label><br>'," \n ";
	  	  echo '<input type="text" id="inpuniteart" name="inpuniteart" value="' . $unite2 . '" maxlength="40"></input><br>'," \n ";

		    echo '<label for="inpdescart">Description : </label><br>'," \n ";
	  	  echo '<input type="text" id="inpdescart" name="inpdescart" value="' . $desc2 . '" maxlength="300"></input><br>'," \n ";

        echo '<label for="inpvisart">Visible : </label><br>'," \n ";
  	    if ($visible2 == 1)
		      echo '<input type="checkbox" id="inpvisart" name="inpvisart" value="' . $visible2 . '" checked></input><br>'," \n ";
	      else 
		      echo '<input type="checkbox" id="inpvisart" name="inpvisart" value="' . $visible2 . '"></input><br>'," \n ";
		      
		    echo '<label for="inpimgart">Image : </label><br />'," \n ";
	  	  echo '<input type="file" id="inpimgart" name="inpimgart" value="' . $image2 . '" accept="image/png, image/jpeg"></input><br />'," \n ";
	  	  
	  	  echo '<label for="inpimgvis">Afficher Image : </label><br>'," \n ";
  	    if ($imgvis2 == 1)
		      echo '<input type="checkbox" id="inpimgvis" name="inpimgvis" value="' . $imgvis2 . '" checked></input><br>'," \n ";
	      else 
		      echo '<input type="checkbox" id="inpimgvis" name="inpimgvis" value="' . $imgvis2 . '"></input><br>'," \n ";
	  	  
	  	  echo '<label for="inpobliga">Obligatoire : </label><br>'," \n ";
  	    if ($obliga2 == 1)
		      echo '<input type="checkbox" id="inpobliga" name="inpobliga" value="' . $obliga2 . '" checked></input><br>'," \n ";
	      else 
		      echo '<input type="checkbox" id="inpobliga" name="inpobliga" value="' . $obliga2 . '"></input><br>'," \n ";

        echo '<br />'," \n ";
          
	      echo '<a href="admin.php?customer=' . $customer . '#tabArt"><button type="button">Cancel</button></a>'," \n ";
      	echo '<input type="submit" value="Submit">'," \n ";
      	echo '</form>'," \n ";
	    }
	  
	    if (!(empty($ins)))
	    {
		    $action2  = 'admin.php?customer=' . $customer . '&commande=insert&table=article#tabArt';
		    echo '<form autocomplete="off" action="';
		    echo $action2;
		    echo  '" method="POST" enctype="multipart/form-data">'," \n ";
	  	  echo '<label for="inpnomart">Nom : </label><br>'," \n ";
	  	  echo '<input type="text" id="inpnomart" name="inpnomart" maxlength="40" required></input><br>'," \n ";
	  	
	  	  echo '<label for="inpcatart">Categorie : </label><br>'," \n ";
	  	  echo '<select name="inpcatart" required>'," \n ";
	  	  $queryopt2 = 'SELECT catid, nom, visible FROM categorie WHERE customid = ' . $customid  ;
      	if ($resultopt2 = $conn->query($queryopt2)) 
	  	  {	
	        while ($rowopt2 = $resultopt2->fetch_row()) 
    	    {	
    	      echo '<option value="' . $rowopt2[0] . '">' . $rowopt2[1] . '</option>'," \n ";
		      }
		    }       	  
	  	  $resultopt2->close();
        echo '</select><br>'," \n ";

	  	  echo '<label for="inpprixart">Prix : </label><br>'," \n ";
	  	  echo '<input type="number" id="inpprixart" name="inpprixart" value="1" step="0.01" required></input><br>'," \n ";

		    echo '<label for="inpuniteart">Unite : </label><br>'," \n ";
	  	  echo '<input type="text" id="inpuniteart" name="inpuniteart" maxlength="40"></input><br>'," \n ";

		    echo '<label for="inpdescart">Description : </label><br>'," \n ";
	  	  echo '<input type="text" id="inpdescart" name="inpdescart" maxlength="150"></input><br>'," \n ";
	  	
  	  	echo '<label for="inpvisart">Visible : </label><br>'," \n ";
	  	  echo '<input type="checkbox" id="inpvisart" name="inpvisart" value="1" checked></input><br>'," \n ";
	  	  
		    echo '<label for="inpimgart">Image : </label><br>'," \n ";
	  	  echo '<input type="file" id="inpimgart" name="inpimgart" accept="image/png, image/jpeg"></input><br>'," \n ";
	  	
  	  	echo '<label for="inpimgvis">Afficher Image : </label><br>'," \n ";
	  	  echo '<input type="checkbox" id="inpimgvis" name="inpimgvis" value="1"></input><br>'," \n ";

  	  	echo '<label for="inpobliga">Obligatoire : </label><br>'," \n ";
	  	  echo '<input type="checkbox" id="inpobliga" name="inpobliga" value="1"></input><br>'," \n ";
	  	  
	  	  echo '<br />'," \n ";
	  	  
		    echo '<a href="admin.php?customer=' . $customer . '#tabArt"><button type="button">Cancel</button></a>'," \n ";
      	echo '<input type="submit" value="Submit">'," \n ";
	      echo '</form>'," \n ";
      }
      echo '<br />' . $error . " \n ";
      echo '</div>'," \n ";
  	} 

	  echo '</div>'," \n ";
    echo '</div>'," \n ";

    if (strcmp($_SESSION[$customer . '_mode'],'avance')== 0)  
    {
    
      echo '<div id=tabRgoa> <a href="admin.php?customer=' . $customer . '#tabRgoa">RELGRPOPTART</a>'," \n ";
      echo '<div class=sheet>'," \n ";
  
     
      if (empty($modif) && empty($ins))
      {
        echo '<div id="inlistRgoa">'," \n ";
  
        echo '<table>'," \n ";
        echo '<tr>';
        echo '<th>Groupeopt</th>';
        echo '<th>Article</th>';
        echo '<th>Visible</th>';
        echo '</tr>'," \n ";
        $query2 = 'SELECT relgrpoartid, groupeopt.nom as grponom, article.nom as artnom, relgrpoptart.visible FROM relgrpoptart, groupeopt, article ';
        $query2 = $query2 . 'WHERE relgrpoptart.customid = ' . $customid . ' AND relgrpoptart.grpoptid = groupeopt.grpoptid AND relgrpoptart.artid = article.artid';
        //echo $query2;
        if ($result2 = $conn->query($query2)) 
  	    {	
  	      while ($row2 = $result2->fetch_row()) 
      	  {	
         	  echo '<tr>';
         	  echo '<td>';
         	  echo $row2[1];
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row2[2];
         	  echo '</td>';
         	  echo '<td>';
         	  if ($row2[3] > 0) 
         	    echo '<input type="checkbox" disabled="disabled" checked>';
         	  else
         	    echo '<input type="checkbox" disabled="disabled">';
         	  echo '</td>';
         	  echo '<td>';
  		      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row2[0] . '&table=relgrpoptart#tabRgoa"> <input type="button" value="Modifier"> </a>';
         	  echo '</td>';
        	  echo '</tr>'," \n ";
  
  		    }						
  	  	  $result2->close();
        }
        
        echo '</table>'," \n ";
  	    echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=relgrpoptart#tabRgoa"> <input type="button" value="Insérer"> </a>'," \n ";
        echo '</div>'," \n ";      	
  	  }
  		
  	  if ((!(empty($modif) && empty($ins))) && $cmprelgrpoptart == 0)
    	{ 
  	    echo '<div id="inRecordRgoa">'," \n ";
  	    if (!empty($modif))
  	    {
  	      $action  = 'admin.php?customer=' . $customer . '&commande=update&table=relgrpoptart&rectou=' . $modif . '#tabRgoa';
  		    echo '<form autocomplete="off" action="';
  		    echo $action;
  		    echo  '" method="POST">'," \n ";
  	  	  echo '<label for="inpgrpopt">GroupeOpt : </label><br>'," \n ";
  	  	  echo '<select name="inpgrpopt" required>'," \n ";
  	  	  $queryopt2 = 'SELECT grpoptid, nom, visible FROM groupeopt WHERE customid = '  . $customid  ;
        	if ($resultopt2 = $conn->query($queryopt2)) 
  	  	  {	
  	        while ($rowopt2 = $resultopt2->fetch_row()) 
      	    {	
      	       if (strcmp($rowopt2[0],$grpoptid5) == 0)
      	         echo '<option value="' . $rowopt2[0] . '" selected>' . $rowopt2[1] . '</option>'," \n ";
      	       else
      	         echo '<option value="' . $rowopt2[0] . '">' . $rowopt2[1] . '</option>'," \n ";
  		      }
  		    }       	  
  	  	  $resultopt2->close();
          echo '</select><br>'," \n ";
  
  	  	  echo '<label for="inpart">Article : </label><br>'," \n ";
  	  	  echo '<select name="inpart" required>'," \n ";
  	  	  $queryopt2 = 'SELECT artid, nom, visible FROM article WHERE customid = ' . $customid  ;
        	if ($resultopt2 = $conn->query($queryopt2)) 
  	  	  {	
  	        while ($rowopt2 = $resultopt2->fetch_row()) 
      	    {	
      	      if (strcmp($rowopt2[0],$artid5) == 0)
    	          echo '<option value="' . $rowopt2[0] . '" selected>' . $rowopt2[1] . '</option>'," \n ";
      	      else
      	        echo '<option value="' . $rowopt2[0] . '">' . $rowopt2[1] . '</option>'," \n ";
      	    } 
  		    }       	  
  	  	  $resultopt2->close();
          echo '</select><br>'," \n ";
          
    	  	echo '<label for="inpvis">Visible : </label><br>'," \n ";
    	  	if ($visible5 == 1)
  		      echo '<input type="checkbox" id="inpvis" name="inpvis" value="' . $visible5 . '" checked></input><br>'," \n ";
  		    else 
  		      echo '<input type="checkbox" id="inpvis" name="inpvis" value="' . $visible5 . '"></input><br>'," \n ";
  		  		
  		    echo '<a href="admin.php?customer=' . $customer . '#tabRgoa"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
        	echo '</form>'," \n ";
        	
  	    }
  	    if (!(empty($ins)))
  	    {
  	      $action  = 'admin.php?customer=' . $customer . '&commande=insert&table=relgrpoptart#tabRgoa';
  		    echo '<form autocomplete="off" action="';
  		    echo $action;
  		    echo  '" method="POST">'," \n ";
  	  	  echo '<label for="inpgrpopt">Groupeopt : </label><br>'," \n ";
  	  	  echo '<select name="inpgrpopt" required>'," \n ";
  	  	  $queryopt2 = 'SELECT grpoptid, nom, visible FROM groupeopt WHERE customid = ' . $customid  ;
        	if ($resultopt2 = $conn->query($queryopt2)) 
  	  	  {	
  	        while ($rowopt2 = $resultopt2->fetch_row()) 
      	    {	
      	      echo '<option value="' . $rowopt2[0] . '">' . $rowopt2[1] . '</option>'," \n ";
  		      }
  		    }       	  
  	  	  $resultopt2->close();
          echo '</select><br>'," \n ";
  	  	  echo '<label for="inpart">Article : </label><br>'," \n ";
  	  	  echo '<select name="inpart" required>'," \n ";
  	  	  $queryopt2 = 'SELECT artid, nom, visible FROM article WHERE customid = ' . $customid  ;
        	if ($resultopt2 = $conn->query($queryopt2)) 
  	  	  {	
  	        while ($rowopt2 = $resultopt2->fetch_row()) 
      	    {	
      	      echo '<option value="' . $rowopt2[0] . '">' . $rowopt2[1] . '</option>'," \n ";
  		      }
  		    }       	  
  	  	  $resultopt2->close();
          echo '</select><br>'," \n ";
          
    	  	echo '<label for="inpvis">Visible : </label><br>'," \n ";
  	  	  echo '<input type="checkbox" id="inpvis" name="inpvis" value="1" checked></input><br>'," \n ";     
      		echo '<a href="admin.php?customer=' . $customer . '#tabRgoa"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
  	      echo '</form>'," \n ";
        }
        echo '<br />' . $error . " \n ";
  	    echo '</div>'," \n ";
    	} 
    	echo '</div>'," \n ";
      echo '</div>'," \n ";
  
      echo '<div id=tabGrpo> <a href="admin.php?customer=' . $customer . '#tabGrpo">GROUPEOPT</a>'," \n ";
      echo '<div class=sheet>'," \n ";
  
      
      if (empty($modif) && empty($ins))
      {
        echo '<div id="inlistGrpo">'," \n ";
  
        echo '<table>'," \n ";
        echo '<tr>';
        echo '<th>Nom</th>';
        echo '<th>Visible</th>';
        echo '<th>Multiple</th>';
        echo '</tr>'," \n ";
        $query2 = 'SELECT grpoptid, nom, visible, multiple FROM groupeopt WHERE customid = ' . $customid  ;
        if ($result2 = $conn->query($query2)) 
  	    {	
  	      while ($row2 = $result2->fetch_row()) 
      	  {	
         	  echo '<tr>';
         	  echo '<td>';
         	  echo $row2[1];
         	  echo '</td>';
         	  echo '<td>';
         	  if ($row2[2] > 0) 
         	    echo '<input type="checkbox" disabled="disabled" checked>';
         	  else
         	    echo '<input type="checkbox" disabled="disabled">';
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row2[3];
         	  echo '</td>';
         	  echo '<td>';
  		      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row2[0] . '&table=groupeopt#tabGrpo"> <input type="button" value="Modifier"> </a>';
         	  echo '</td>';
        	  echo '</tr>'," \n ";
  
  		    }						
  	  	  $result2->close();
        }
        
        echo '</table>'," \n ";
  	    echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=groupeopt#tabGrpo"> <input type="button" value="Insérer"> </a>'," \n ";
        echo '</div>'," \n ";      	
  	  }
  		
  	  if ((!(empty($modif) && empty($ins))) && $cmpgrpopt == 0)
    	{ 
  	    echo '<div id="inRecordGrpo">'," \n ";
  	    if (!empty($modif))
  	    {
  	      $action  = 'admin.php?customer=' . $customer . '&commande=update&table=groupeopt&rectou=' . $modif . '#tabGrpo';
  		    echo '<form autocomplete="off" action="';
  		    echo $action;
  		    echo  '" method="POST">'," \n ";
  	  	  echo '<label for="inpnom">Nom : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpnom" name="inpnom" value="' . $nom6 . '" maxlength="40" required></input><br>'," \n ";
    	  	echo '<label for="inpvis">Visible : </label><br>'," \n ";
    	  	if ($visible6 == 1)
  		      echo '<input type="checkbox" id="inpvis" name="inpvis" value="' . $visible6 . '" checked></input><br>'," \n ";
  		    else 
  		      echo '<input type="checkbox" id="inpvis" name="inpvis" value="' . $visible6 . '"></input><br>'," \n ";
    	  	echo '<label for="inpvis">Multiple : </label><br>'," \n ";
    	  	if ($multiple6 == 1)
  		      echo '<input type="checkbox" id="inpmulti" name="inpmulti" value="' . $multiple6 . '" checked></input><br>'," \n ";
  		    else 
  		      echo '<input type="checkbox" id="inpmulti" name="inpmulti" value="' . $multiple6 . '"></input><br>'," \n ";
  		  		
  		    echo '<a href="admin.php?customer=' . $customer . '#tabGrpo"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
        	echo '</form>'," \n ";
        	
  	    }
  	    if (!(empty($ins)))
  	    {
  	      $action  = 'admin.php?customer=' . $customer . '&commande=insert&table=groupeopt#tabGrpo';
  		    echo '<form autocomplete="off" action="';
  		    echo $action;
  		    echo  '" method="POST">'," \n ";
  	  	  echo '<label for="inpnom">Nom : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpnom" name="inpnom" maxlength="40" required></input><br>'," \n ";
    	  	echo '<label for="inpvis">Visible : </label><br>'," \n ";
  	  	  echo '<input type="checkbox" id="inpvis" name="inpvis" value="1" checked></input><br>'," \n ";     
    	  	echo '<label for="inpmulti">Multiple : </label><br>'," \n ";
  	  	  echo '<input type="checkbox" id="inpmulti" name="inpmulti" value="0"></input><br>'," \n ";     
      		echo '<a href="admin.php?customer=' . $customer . '#tabGrpo"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
  	      echo '</form>'," \n ";
        }
        echo '<br />' . $error . " \n ";
  	    echo '</div>'," \n ";
    	} 
    	echo '</div>'," \n ";
  	  echo '</div>'," \n ";
      
      echo '<div id=tabOpt> <a href="admin.php?customer=' . $customer . '#tabOpt">OPTION</a>'," \n ";
      echo '<div class=sheet>'," \n ";
  
      if (empty($modif) && empty($ins))
      {
        echo '<div id="inlistOpt">'," \n ";
  
        echo '<table>'," \n ";
        echo '<tr>';
        echo '<th>Nom</th>';
        echo '<th>Surcout</th>';
        echo '<th>GroupeOption</th>';
        echo '<th>Visible</th>';
        echo '</tr>'," \n ";
        $query2 = 'SELECT optid, `option`.nom as optnom, surcout, groupeopt.nom as grponom, 
                   `option`.visible    
                   FROM `option`, groupeopt WHERE `option`.customid = ' . $customid . ' AND `option`.grpoptid = groupeopt.grpoptid';
        if ($result2 = $conn->query($query2)) 
        {	
  	      while ($row2 = $result2->fetch_row()) 
      	  {	
         	  echo '<tr>';
         	  echo '<td>';
         	  echo $row2[1];
         	  echo '</td>';
         	  echo '<td>';
         	  echo number_format($row2[2], 2, ',', ' ');
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row2[3];
         	  echo '</td>';
         	  echo '<td>';
         	  if ($row2[4] > 0) 
         	    echo '<input type="checkbox" disabled="disabled" checked>';
         	  else
         	    echo '<input type="checkbox" disabled="disabled">';
         	  echo '</td>';
         	  echo '<td>';
  		      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row2[0] . '&table=option#tabOpt"> <input type="button" value="Modifier"> </a>';
         	  echo '</td>';
        	  echo '</tr>'," \n ";
  		    }  						
  	  	  $result2->close();
        }
        
        echo '</table>'," \n ";
  	    echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=option#tabOpt"> <input type="button" value="Insérer"> </a>'," \n ";
        echo '</div>'," \n ";      	
  	  }
  		
  	  if ((!(empty($modif) && empty($ins))) && $cmpopt == 0)
  	  { 
  	    echo '<div id="inRecordOpt">'," \n ";
  
  	    if (!empty($modif))
  	    {
  		    $action1  = 'admin.php?customer=' . $customer . '&commande=update&table=option&rectou=' . $modif . '#tabOpt';
  		    echo '<form autocomplete="off" action="';
  		    echo $action1;
  		    echo  '" method="POST"  enctype="multipart/form-data">'," \n ";
  	  	  echo '<label for="inpnom">Nom : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpnom" name="inpnom" value="' . $nom7 . '" required></input><br>'," \n ";
  
  	    	echo '<label for="inpsurcout">Surcout : </label><br>'," \n ";
  	    	echo '<input type="number" id="inpsurcout" name="inpsurcout" value="' . $surcout7 . '" step="0.01" required></input><br>'," \n ";
  		
  	  	  echo '<label for="inpgrpopt">GroupeOption : </label><br>'," \n ";
  	  	  echo '<select id="inpgrpopt" name="inpgrpopt" required>'," \n ";
  	  	  $queryopt2 = 'SELECT grpoptid, nom, visible FROM groupeopt WHERE customid = ' . $customid;
        	if ($resultopt2 = $conn->query($queryopt2)) 
  	  	  {	
  	        while ($rowopt2 = $resultopt2->fetch_row()) 
      	    {	
  					  if (strcmp($rowopt2[0],$grpoptid7) == 0)
      	        echo '<option value="' . $rowopt2[0] . '" selected>' . $rowopt2[1] . '</option>'," \n ";
      	      else					    	    
      	        echo '<option value="' . $rowopt2[0] . '">' . $rowopt2[1] . '</option>'," \n ";
  		      }
  		    }       	  
    	  	$resultopt2->close();
          echo '</select><br>'," \n ";
          		
          echo '<label for="inpvisible">Visible : </label><br>'," \n ";
    	    if ($visible7 == 1)
  		      echo '<input type="checkbox" id="inpvis" name="inpvis" value="' . $visible7 . '" checked></input><br>'," \n ";
  	      else 
  		      echo '<input type="checkbox" id="inpvis" name="inpvis" value="' . $visible7 . '"></input><br>'," \n ";
  
          echo '<br />'," \n ";
            
  	      echo '<a href="admin.php?customer=' . $customer . '#tabOpt"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
        	echo '</form>'," \n ";
  	    }
  	  
  	    if (!(empty($ins)))
  	    {
  		    $action2  = 'admin.php?customer=' . $customer . '&commande=insert&table=option#tabOpt';
  		    echo '<form autocomplete="off" action="';
  		    echo $action2;
  		    echo  '" method="POST" enctype="multipart/form-data">'," \n ";
  	  	  echo '<label for="inpnom">Nom : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpnom" name="inpnom" maxlength="40" required></input><br>'," \n ";
  
  	  	  echo '<label for="inpsurcout">Surcout : </label><br>'," \n ";
  	  	  echo '<input type="number" id="inpsurcout" name="inpsurcout" value="0" step="0.01" required></input><br>'," \n ";
  	  	
  	  	  echo '<label for="inpgrpopt">GroupeOption : </label><br>'," \n ";
  	  	  echo '<select name="inpgrpopt" required>'," \n ";
  	  	  $queryopt2 = 'SELECT grpoptid, nom, visible FROM groupeopt WHERE customid = ' . $customid  ;
        	if ($resultopt2 = $conn->query($queryopt2)) 
  	  	  {	
  	        while ($rowopt2 = $resultopt2->fetch_row()) 
      	    {	
      	      echo '<option value="' . $rowopt2[0] . '">' . $rowopt2[1] . '</option>'," \n ";
  		      }
  		    }       	  
  	  	  $resultopt2->close();
          echo '</select><br>'," \n ";
  
    	  	echo '<label for="inpvisible">Visible : </label><br>'," \n ";
  	  	  echo '<input type="checkbox" id="inpvis" name="inpvis" value="1" checked></input><br>'," \n ";
  	  	  
  	  	  echo '<br />'," \n ";
  	  	  
  		    echo '<a href="admin.php?customer=' . $customer . '#tabOpt"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
  	      echo '</form>'," \n ";
        }
        echo '<br />' . $error . " \n ";
        echo '</div>'," \n ";
    	} 
  
  	  echo '</div>'," \n ";
      echo '</div>'," \n ";
      
      
      echo '<div id=tabAdmin> <a href="admin.php?customer=' . $customer . '#tabAdmin">ADMINISTRATEUR</a>'," \n ";
      echo '<div class=sheet>'," \n ";
  
      if (empty($modif) && empty($ins))
      {
        echo '<div id="inlistAdmin">'," \n ";
  
        echo '<table>'," \n ";
        echo '<tr>';
        echo '<th>Pseudo</th>';
        echo '<th>Mot de passe</th>';
        echo '<th>Couriel</th>';
        echo '<th>Actif</th>';
        echo '</tr>'," \n ";
        $query3 = 'SELECT adminid, pseudo, pass, email, actif FROM administrateur WHERE customid = ' . $customid;
        if ($result3 = $conn->query($query3)) 
        {	
  	      while ($row3 = $result3->fetch_row()) 
      	  {	
         	  echo '<tr>';
         	  echo '<td>';
         	  echo $row3[1];
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row3[2];
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row3[3];
         	  echo '</td>';
         	  echo '<td>';
         	  if ($row3[4] > 0) 
         	    echo '<input type="checkbox" disabled="disabled" checked>';
         	  else
         	    echo '<input type="checkbox" disabled="disabled">';
         	  echo '</td>';
         	  echo '<td>';
  		      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row3[0] . '&table=administrateur#tabAdmin"> <input type="button" value="Modifier"> </a>';
         	  echo '</td>';
        	  echo '</tr>'," \n ";
        	}						
  	  		$result3->close();
        }
       
      	echo '</table>'," \n ";
  	  	echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=administrateur#tabAdmin"> <input type="button" value="Insérer"> </a>'," \n ";
      	echo '</div>'," \n ";      	
  		}
  		
  		if ((!(empty($modif) && empty($ins))) && $cmpadmin == 0)
  		{ 
  	  	echo '<div id="inRecordAdmin">'," \n ";
  
  	  	if (!empty($modif))
  	  	{
  				$action1  = 'admin.php?customer=' . $customer . '&commande=update&table=administrateur&rectou=' . $modif . '#tabAdmin';
  				echo '<form autocomplete="off" action="';
  				echo $action1;
  				echo  '" method="POST">'," \n ";
  	  		echo '<label for="inppseudoad">Pseudo : </label><br>'," \n ";
  	  		echo '<input type="text" id="inppseudoad" name="inppseudoad" value="' . $pseudo3 . '" maxlength="40" required></input><br>'," \n ";
          		
  		  	echo '<label for="inppassad">Mot de passe : </label><br>'," \n ";
    	  	echo '<input type="password" id="inppassad" name="inppassad" 
  	    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}" 
  	    title="Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? 
  	     et être de au moins 8 caractères"></input><br>'," \n ";
  
  	     	echo '<label for="inpemailad">Courriel : </label><br>'," \n ";
  	  	  echo '<input type="email" id="inpemailad" name="inpemailad" value="' . $email3 . '" maxlength="320" required 
  	  	  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
  	  	  title="Une adresse de courriel valide"></input><br>'," \n ";
  
          echo '<label for="inpactifad">Actif : </label><br>'," \n ";
    	    if ($actif3 == 1)
  		      echo '<input type="checkbox" id="inpactifad" name="inpactifad" value="' . $actif3 . '" checked></input><br>'," \n ";
  	      else 
  		      echo '<input type="checkbox" id="inpactifad" name="inpactifad" value="' . $actif3 . '"></input><br>'," \n ";
  		  		
  	      echo '<a href="admin.php?customer=' . $customer . '#tabAdmin"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
        	echo '</form>'," \n ";
  	    }
  	  
    	  if (!(empty($ins)))
  	    {
  		  	$action2  = 'admin.php?customer=' . $customer . '&commande=insert&table=administrateur#tabAdmin';
  			  echo '<form autocomplete="off" action="';
  			  echo $action2;
  			  echo  '" method="POST">'," \n ";
  	  	  echo '<label for="inppseudoad">Pseudo : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inppseudoad" name="inppseudoad" maxlength="40" required></input><br>'," \n ";
  	  	
  	  	  echo '<label for="inppassad">Mot de passe : </label><br>'," \n ";
  	  	  echo '<input type="password" id="inppassad" name="inppassad" value="" required 
  	  	  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}"  
  	    title="Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? 
  	     et être de au moins 8 caractères"></input><br>'," \n ";
  
  		    echo '<label for="inpemailad">Courriel : </label><br>'," \n ";
  	  	  echo '<input type="email" id="inpemailad" name="inpemailad" maxlength="320" required 
  	  	  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
  	  	  title="Une adresse de courriel valide"></input><br>'," \n ";
  	
   	  	  echo '<label for="inpactifad">Actif : </label><br>'," \n ";
  	  	  echo '<input type="checkbox" id="inpactifad" name="inpactifad" value="1"></input><br>'," \n ";     
  			  echo '<a href="admin.php?customer=' . $customer . '#tabAdmin"><button type="button">Cancel</button></a>'," \n ";
       	  echo '<input type="submit" value="Submit">'," \n ";
  	      echo '</form>'," \n ";
        }
        echo '<br />' . $error . " \n ";
        echo '</div>'," \n ";
      } 
  
    	echo '</div>'," \n ";
      echo '</div>'," \n ";
  
      echo '<div id=tabParam> <a href="admin.php?customer=' . $customer . '#tabParam">PARAMETRE</a>'," \n ";
      echo '<div class=sheet>'," \n ";
      
      if (empty($modif) && empty($ins))
      {
        echo '<div id="inlistParam">'," \n ";
  
        echo '<table>'," \n ";
        echo '<tr>';
        echo '<th>Nom</th>';
        echo '<th>Valeur</th>';
        echo '<th>Commentaire</th>';
        echo '</tr>'," \n ";
        $query4 = 'SELECT paramid, nom, valeur, commentaire FROM parametre WHERE customid = ' . $customid  ;
        if ($result4 = $conn->query($query4)) 
  	    {	
          while ($row4 = $result4->fetch_row()) 
      	  {	
         	  echo '<tr>';
         	  echo '<td>';
         	  echo $row4[1];
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row4[2];
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row4[3];
         	  echo '</td>';
         	  echo '<td>';
  		      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row4[0] . '&table=parametre#tabParam"> <input type="button" value="Modifier"> </a>';
         	  echo '</td>';
        	  echo '</tr>'," \n ";
  
  		    }				
  	  	  $result4->close();
        }
        
        echo '</table>'," \n ";
  	    echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=parametre#tabParam"> <input type="button" value="Insérer"> </a>'," \n ";
        echo '</div>'," \n ";      	
  	  }
  		
  	  if ((!(empty($modif) && empty($ins))) && $cmpparam == 0)
    	{ 
  	    echo '<div id="inRecordParam">'," \n ";
  	    if (!empty($modif))
  	    {
  	      $action41  = 'admin.php?customer=' . $customer . '&commande=update&table=parametre&rectou=' . $modif . '#tabParam';
  		    echo '<form autocomplete="off" action="';
  		    echo $action41;
  		    echo  '" method="POST">'," \n ";
  	  	  echo '<label for="inpnompa">Nom : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpnompa" name="inpnompa" value="' . $nom4 . '" maxlength="40" required></input><br>'," \n ";
  	  	  echo '<label for="inpvaleurpa">Valeur : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpvaleurpa" name="inpvaleurpa" value="' . $valeur4 . '" maxlength="500"></input><br>'," \n ";
  	  	  echo '<label for="inpcommentpa">Commentaire : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpcommentpa" name="inpcommentpa" value="' . $commentaire4 . '" maxlength="130"></input><br>'," \n ";
  		  		
  		    echo '<a href="admin.php?customer=' . $customer . '#tabParam"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
        	echo '</form>'," \n ";
  	    }
  	    if (!(empty($ins)))
  	    {
  	      $action42  = 'admin.php?customer=' . $customer . '&commande=insert&table=parametre#tabParam';
  		    echo '<form autocomplete="off" action="';
  		    echo $action42;
  		    echo  '" method="POST">'," \n ";
  	  	  echo '<label for="inpnompa">Nom : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpnompa" name="inpnompa" maxlength="40" required></input><br>'," \n ";
  	  	  echo '<label for="inpvaleurpa">Valeur : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpvaleurpa" name="inpvaleurpa" maxlength="500"></input><br>'," \n ";
  	  	  echo '<label for="inpcommentpa">Commentaire : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpcommentpa" name="inpcommentpa" maxlength="130"></input><br>'," \n ";
  
      		echo '<a href="admin.php?customer=' . $customer . '#tabParam"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
  	      echo '</form>'," \n ";
        }
        echo '<br />' . $error . " \n ";
  	    echo '</div>'," \n ";
    	} 
    	echo '</div>'," \n ";
      echo '</div>'," \n ";
  
      echo '<div id=tabZone> <a href="admin.php?customer=' . $customer . '#tabZone">CPZONE</a>'," \n ";
      echo '<div class=sheet>'," \n ";
      
      if (empty($modif) && empty($ins))
      {
        echo '<div id="inlistzone">'," \n ";
    
        echo '<table>'," \n ";
        echo '<tr>';
        echo '<th>CodePostal</th>';
        echo '<th>Ville</th>';
        echo '<th>Actif</th>';
        echo '</tr>'," \n ";
        $query2 = 'SELECT cpzoneid, codepostal, ville, actif FROM cpzone WHERE customid = ' . $customid;
        
        //echo $query2;
        if ($result2 = $conn->query($query2)) 
        {	
          while ($row2 = $result2->fetch_row()) 
      	  {	
         	  echo '<tr>';
         	  echo '<td>';
         	  echo $row2[1];
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row2[2];
         	  echo '</td>';
         	  echo '<td>';
         	  if ($row2[3] > 0) 
         	    echo '<input type="checkbox" disabled="disabled" checked>';
         	  else
         	    echo '<input type="checkbox" disabled="disabled">';
         	  echo '</td>';
         	  echo '<td>';
    	      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row2[0] . '&table=cpzone#tabZone"> <input type="button" value="Modifier"> </a>';
         	  echo '</td>';
        	  echo '</tr>'," \n ";
    
    	    }						
      	  $result2->close();
        }
        
        echo '</table>'," \n ";
        echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=cpzone#tabZone"> <input type="button" value="Insérer"> </a>'," \n ";
        echo '</div>'," \n ";      	
      }
    	
      if ((!(empty($modif) && empty($ins))) && $cmpcpzone == 0)
    	{ 
        echo '<div id="inRecordZone">'," \n ";
        if (!empty($modif))
        {
          $action  = 'admin.php?customer=' . $customer . '&commande=update&table=cpzone&rectou=' . $modif . '#tabZone';
    	    echo '<form autocomplete="off" action="';
    	    echo $action;
    	    echo  '" method="POST">'," \n ";
      	  echo '<label for="inpcp">CodePostal : </label><br>'," \n ";
      	  echo '<input type="text" id="inpcp" name="inpcp" value="' . $cp8 . '" pattern="[0-9]{5}" minlength="5" maxlength="5" required></input><br>'," \n ";
    
      	  echo '<label for="inpville">Ville : </label><br>'," \n ";
      	  echo '<input type="text" id="inpville" name="inpville" value="' . $ville8 . '" maxlength="45"></input><br>'," \n ";
          
    	  	echo '<label for="inpactif">Actif : </label><br>'," \n ";
    	  	if ($actif8 == 1)
    	      echo '<input type="checkbox" id="inpactif" name="inpactif" value="' . $actif8 . '" checked></input><br>'," \n ";
    	    else 
    	      echo '<input type="checkbox" id="inpactif" name="inpactif" value="' . $actif8 . '"></input><br>'," \n ";
    	  		
    	    echo '<a href="admin.php?customer=' . $customer . '#tabZone"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
        	echo '</form>'," \n ";
        	
        }
        if (!(empty($ins)))
        {
          $action  = 'admin.php?customer=' . $customer . '&commande=insert&table=cpzone#tabZone';
    	    echo '<form autocomplete="off" action="';
    	    echo $action;
    	    echo  '" method="POST">'," \n ";
  
  	  	  echo '<label for="inpcp">CodePostal : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpcp" name="inpcp" pattern="[0-9]{5}" minlength="5" maxlength="5" required></input><br>'," \n ";
  	  	  echo '<label for="inpville">Ville : </label><br>'," \n ";
  	  	  echo '<input type="text" id="inpville" name="inpville" maxlength="45"></input><br>'," \n ";
  
    	  	echo '<label for="inpactif">Actif : </label><br>'," \n ";
      	  echo '<input type="checkbox" id="inpactif" name="inpactif" value="1" checked></input><br>'," \n ";     
      		echo '<a href="admin.php?customer=' . $customer . '#tabZone"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
          echo '</form>'," \n ";
        }
        echo '<br />' . $error . " \n ";
        echo '</div>'," \n ";
    	} 
    	echo '</div>'," \n ";
      echo '</div>'," \n ";
      
      echo '<div id=tabLivr> <a href="admin.php?customer=' . $customer . '#tabLivr">BARLIVR</a>'," \n ";
      echo '<div class=sheet>'," \n ";
      
      if (empty($modif) && empty($ins))
      {
        echo '<div id="inlistlivr">'," \n ";
    
        echo '<table>'," \n ";
        echo '<tr>';
        echo '<th>ValMinIn</th>';
        echo '<th>ValMaxEx</th>';
        echo '<th>Surcout</th>';
        echo '<th>Limite Basse</th>';
        echo '<th>Limite Haute</th>';
        echo '</tr>'," \n ";
        $query2 = 'SELECT barlivrid, valminin, valmaxex, surcout, limitebasse, limitehaute FROM barlivr WHERE customid = ' . $customid;
        
        //echo $query2;
        if ($result2 = $conn->query($query2)) 
        {	
          while ($row2 = $result2->fetch_row()) 
      	  {	
         	  echo '<tr>';
         	  echo '<td>';
         	  echo $row2[1];
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row2[2];
         	  echo '</td>';
         	  echo '<td>';
         	  echo $row2[3]; 
         	  echo '</td>';
         	  echo '<td>';
         	  if ($row2[4] > 0) 
         	    echo '<input type="checkbox" disabled="disabled" checked>';
         	  else
         	    echo '<input type="checkbox" disabled="disabled">';
         	  echo '</td>';
         	  echo '<td>';
         	  if ($row2[5] > 0) 
         	    echo '<input type="checkbox" disabled="disabled" checked>';
         	  else
         	    echo '<input type="checkbox" disabled="disabled">';
         	  echo '</td>';
         	  echo '<td>';
    	      echo '<a href="admin.php?customer=' . $customer . '&modifier=' . $row2[0] . '&table=barlivr#tabLivr"> <input type="button" value="Modifier"> </a>';
         	  echo '</td>';
        	  echo '</tr>'," \n ";
    
    	    }						
      	  $result2->close();
        }
        
        echo '</table>'," \n ";
        echo '<a href="admin.php?customer=' . $customer . '&inserer=1&table=barlivr#tabLivr"> <input type="button" value="Insérer"> </a>'," \n ";
        echo '</div>'," \n ";      	
      }
    	
      if ((!(empty($modif) && empty($ins))) && $cmpbarlivr == 0)
    	{ 
        echo '<div id="inRecordLivr">'," \n ";
        if (!empty($modif))
        {
          $action  = 'admin.php?customer=' . $customer . '&commande=update&table=barlivr&rectou=' . $modif . '#tabLivr';
    	    echo '<form autocomplete="off" action="';
    	    echo $action;
    	    echo  '" method="POST">'," \n ";
      	  echo '<label for="inpvalminin"> Valeur mini incluse: </label><br>'," \n ";
      	  echo '<input type="number" id="inpvalminin" name="inpvalminin" value="' . $valminin9 . '"></input><br>'," \n ";
    
      	  echo '<label for="inpvalmaxex"> Valeur maxi excluse: </label><br>'," \n ";
      	  echo '<input type="number" id="inpvalmaxex" name="inpvalmaxex" value="' . $valmaxex9 . '"></input><br>'," \n ";
          
      	  echo '<label for="inpsurcout"> Surcout : </label><br>'," \n ";
      	  echo '<input type="number" id="inpsurcout" name="inpsurcout" value="' . $surcout9 . '"></input><br>'," \n ";
    	  		
    	  	echo '<label for="inplimitebasse">Limite Basse : </label><br>'," \n ";
    	  	if ($limitebasse9 == 1)
    	      echo '<input type="checkbox" id="inplimitebasse" name="inplimitebasse" value="' . $limitebasse9 . '" checked></input><br>'," \n ";
    	    else 
    	      echo '<input type="checkbox" id="inplimitebasse" name="inplimitebasse" value="' . $limitebasse9 . '"></input><br>'," \n ";
    	  		
    	  	echo '<label for="inplimitehaute">Limite Haute : </label><br>'," \n ";
    	  	if ($limitehaute9 == 1)
    	      echo '<input type="checkbox" id="inplimitehaute" name="inplimitehaute" value="' . $limitehaute9 . '" checked></input><br>'," \n ";
    	    else 
    	      echo '<input type="checkbox" id="inplimitehaute" name="inplimitehaute" value="' . $limitehaute9 . '"></input><br>'," \n ";
    	  		
    	    echo '<a href="admin.php?customer=' . $customer . '#tabLivr"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
        	echo '</form>'," \n ";
        	
        }
        if (!(empty($ins)))
        {
          $action  = 'admin.php?customer=' . $customer . '&commande=insert&table=barlivr#tabLivr';
    	    echo '<form autocomplete="off" action="';
    	    echo $action;
    	    echo  '" method="POST">'," \n ";
  
      	  echo '<label for="inpvalminin"> Valeur mini incluse : </label><br>'," \n ";
      	  echo '<input type="number" id="inpvalminin" name="inpvalminin"></input><br>'," \n ";
    
      	  echo '<label for="inpvalmaxex"> Valeur maxi excluse : </label><br>'," \n ";
      	  echo '<input type="number" id="inpvalmaxex" name="inpvalmaxex"></input><br>'," \n ";
          
      	  echo '<label for="inpsurcout"> Surcout : </label><br>'," \n ";
      	  echo '<input type="number" id="inpsurcout" name="inpsurcout"></input><br>'," \n ";
      	  
    	  	echo '<label for="inplimitebasse">Limite basse : </label><br>'," \n ";
      	  echo '<input type="checkbox" id="inplimitebasse" name="inplimitebasse" value="1" checked></input><br>'," \n ";     

    	  	echo '<label for="inplimitehaute">Limite haute : </label><br>'," \n ";
      	  echo '<input type="checkbox" id="inplimitehaute" name="inplimitehaute" value="1" checked></input><br>'," \n ";     

      		echo '<a href="admin.php?customer=' . $customer . '#tabLivr"><button type="button">Cancel</button></a>'," \n ";
        	echo '<input type="submit" value="Submit">'," \n ";
          echo '</form>'," \n ";
        }
        echo '<br />' . $error . " \n ";
        echo '</div>'," \n ";
    	} 
    	echo '</div>'," \n ";
      echo '</div>'," \n ";
  
    }      
    echo '</div>'," \n ";
  ?>
  </body>
</html>
