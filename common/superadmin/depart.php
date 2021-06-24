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
?>
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
    <form autocomplete="off" method="post" action="declaration.php" enctype="multipart/form-data">
    <div class="main">
    <p>
     <label>identifiant de la boutic à créer : </label>
     <br />
     <input type="string" id="identifid" name="identif" pattern="[a-z0-9]{3,}" required><br>
     <br />
     <label>Nom de la boutic : </label>
     <br />
     <input type="string" id="nomid" name="nom" required><br>
     <br />
     <label>Adresse (ligne1) de la boutic : </label>
     <br />
     <input type="string" id="adresse1id" name="adresse1"><br>
     <br />
     <label>Adresse (ligne2) de la boutic : </label>
     <br />
     <input type="string" id="adresse2id" name="adresse2"><br>
     <br />
     <label>Code Postal de la boutic : </label>
     <br />
     <input type="string" id="codepostalid" name="codepostal" pattern="[0-9]{5}"><br>
     <br />
     <label>Ville de la boutic : </label>
     <br />
     <input type="string" id="villeid" name="ville"><br>
     <br />
		 <label>Logo de la boutic : </label>
		 <br />
		 <input type="file" id="logo" name="logo" accept="image/png, image/jpeg"><br>    
     <br />
     <label>Courriel de la boutic : </label>
     <br />
     <input type="string" id="courrielid" name="courriel" type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required><br>
     <br />
     <label for="metdef">Mode de la boutique</label>
     <br />
			<select name="metdef" id="metdefid">
			  <option value="3">Click and Collect</option>
			  <option value="0">Visualisation</option>
			</select>
     <br />
    </p>
   <input class="inpmove" type="submit" value="Valider">
   </div>
   </form>
 </body>
</html>
