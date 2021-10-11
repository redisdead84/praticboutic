<!DOCTYPE html>
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
    <div class="main">
    <p>
     <label>Départ du process de création d'une boutique</label><br><br>
        <button onclick="window.location.href = 'depart.php';">Cliquez ici pour le démarrer</button>
     <br />
   </p>
   </div>
   </form>
   <br />
  </body>
</html>

