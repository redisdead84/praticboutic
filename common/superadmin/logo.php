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
    <form autocomplete="off" method="post" action="upload.php?identif=<?php echo $_GET['identif']; ?>" enctype="multipart/form-data">
    <div class="main">
    <p>
		  <label for="logo">Selectionner le fichier du logo : </label>
		  <input type="file" id="logo" name="logo" accept="image/png, image/jpeg"><br><br>
		</p>
		<input class="inpmove" type="submit" value="Valider"><br>
   </div>
   </form>
 </body>
</html>
