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
    <form autocomplete="off" method="post" action="declaration.php">
    <div class="main">
    <p>
     <label>identifiant de la boutique à créer</label>
     <input type="string" id="identifid" name="identif" pattern="[a-z0-9]{3,}" required><br>
     <br />
    </p>
   <input class="inpmove" type="submit" value="Valider">
   </div>
   </form>
 </body>
</html>
