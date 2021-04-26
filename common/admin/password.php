<?php
    	session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.07">
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
			    <form method="post" action="email.php">
			      <div class="modal-header">
			        <h5 class="modal-title">Renvoi du mot de passe</h5>
			      </div>
			      <div class="modal-body">
			      	<div class="form-group">
	     					<label>Votre courriel</label>
	     					<input class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" type="email" id="emailid" name="email"><br>
					   </div>
			      </div>
			      <div class="modal-footer">
							<a href="index.php"><button class="btn btn-secondary" type="button">Cancel</button></a>			        
			        <input class="btn btn-primary" type="submit" value="Valider">
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