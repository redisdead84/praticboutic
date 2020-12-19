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
    <form autocomplete="off" method="post" action="users.php?identif=<?php echo $_GET['identif']; ?>">
    <div class="main">
    <p>
     <label>pseudo</label>
     <input type="string" id="pseudoid" name="pseudo" required><br>
     <br />
     <label>mot de passe</label>
     <input type="password" id="passid" name="pass" required 
  	    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}" 
  	    title="Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? 
  	     et être de au moins 8 caractères"><br>
     <br />
     <label>courriel</label>
     <input type="email" id="emailid" name="email" required 
          pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
  	  	  title="Une adresse de courriel valide"><br>
     <br />
    </p>
   <input class="inpmove" type="submit" value="Valider">
   </div>
   </form>
 </body>
</html>
