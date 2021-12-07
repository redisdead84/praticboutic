
<?php
	session_start();
  
  if (empty($_SESSION['bo_id']) == TRUE)
  {
 	  header("LOCATION: index.php");
 	  exit();
 	}
	
  if (empty($_SESSION['bo_auth']) == TRUE)
  {
 	  header("LOCATION: index.php");
 	  exit();
  }	
  
  if (strcmp($_SESSION['bo_auth'],'oui') != 0)
  {
 	  header("LOCATION: index.php");
 	  exit();
  }
  
  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";
  
  $_SESSION['bo_init'] = 'non';

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Compte Client</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.01">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <script src="https://js.stripe.com/v3/"></script>
    <script src="account.js?v=1.09" defer></script>
  </head>
  <body id="bodyid" class="custombody" data-login="<?php echo $_SESSION['bo_email']?>" >
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spaceflex fcb enlarged vscroll">
        <div class="spaceflexcols">
          <div class="spaceflexcols">
            <a href="logout.php">Déconnexion</a>
            <a id="quitlienid" href="admin.php">Revenir à l'arrière boutic</a>
            <p class="center titleac">Votre compte</p>
            <a id="addaboid" href="boprices.php">Ajouter un abonnement</a>
            <h3>Abonnements</h3>
          </div>
          <div id="bouticlinks">
            <!-- see account.js to see how this div is populated -->
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter l'espace client de la boutic ?") == true)
      {
        window.location ='https://pratic-boutic.fr';
      }
    }
  </script>
</html>
