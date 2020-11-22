<?php
  $customer = $_GET['customer'];
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
    <form method="post" action="valid.php?customer=<?php echo $customer;?>">
    <div class="main">
    <p>
     <label>pseudo</label>
     <input type="string" id="pseudoid" name="pseudo"><br>
     <br />
     <label>mot de passe</label>
     <input type="password" id="passid" name="pass"><br>
     <br />
   </p>
   <input class="inpmove" type="submit" value="Valider">
   </div>
   </form>
   <br />
   <a href="./password.php?customer=<?php echo $customer;?>">Mot de passe oublié ?</a>
  </body>
</html>

