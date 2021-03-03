<?php
	session_start();
  
  if (empty($_SESSION['boutic']) == TRUE)
 	  exit();
  else	
	  $boutic = $_SESSION['boutic'];

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
  	<div class="modal" tabindex="-1" role="dialog" data-backdrop="false">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
			    <form method="post" action="valid.php">
			      <div class="modal-header">
			        <h5 class="modal-title">Identification Administration</h5>
			      </div>
			      <div class="modal-body">
 			      	<div class="form-group">
			        	<label>Boutic</label>
			        	<input class="form-control" type="text" id="bouricid" name="boutic" value="<?php echo $boutic;?>" readonly>
							</div>
			      	<div class="form-group">
			        	<label>Pseudo</label>
	     					<input class="form-control" type="string" id="pseudoid" name="pseudo">
	     				</div>
		      		<div class="form-group">
	     					<label>Mot de passe</label>
	     					<input class="form-control" type="password" id="passid" name="pass">
	     				</div>
			      </div>
			      <div class="modal-footer">
			      	<input class="btn btn-primary btn-block" type="submit" value="Valider">
			        <a class="mr-auto" href="./password.php">Mot de passe oublié ?</a>
			      </div>
			    </form>
		    </div>
		  </div>
		</div>
  </body>
  <script type="text/javascript" >
  	$('.modal').modal('show');
  </script>
</html>

