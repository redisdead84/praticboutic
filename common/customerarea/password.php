<?php
    	session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/back.css?v=1.26">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quittermenu()"/>
      <div id="workspace" class="spacemodal">
        <div class="modal-content-mainmenu modal-content-cb elemcb">
			    <form method="post" action="email.php">
			      <div class="modal-header-cb">
 			      	<img id='logopbid' src='img/LOGO_PRATIC_BOUTIC.png' />
			        <h6 class="modal-title-cb">RENVOI DU MOT DE PASSE</h6>
			      </div>
			      <div class="modal-body-cb">
			      	<div class="form-group">
	     					<input class="form-control" placeholder="Courriel" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" type="email" id="emailid" name="email">
					    </div>
			      </div>
			      <div class="modal-footer-cb param rwc2">
							<a href="index.php"><button class="btn btn-secondary btn-nbsecondary" type="button">Retour</button></a>
			        <input class="btn btn-primary btn-nbprimary" type="submit" value="Valider">
			      </div>
			    </form>
		    </div>
		  </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quittermenu()"/>
		</div>
  </body>
  <script type="text/javascript" >
    function quittermenu() 
    {
      if (confirm("Voulez-vous quitter la récupération du mot de passe ?") == true)
      {
        window.location.href ='exit.php';
      }
    }
  </script>
</html>
