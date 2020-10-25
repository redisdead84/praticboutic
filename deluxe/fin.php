<?php

  include "config/config.php";
  include "param.php";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $bdd);
  // Check connection
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);    

  $method = isset($_GET ['method']) ? $_GET ['method'] : '0';
  $table = isset($_GET ['table']) ? $_GET ['table'] : '0';

  session_start();
  
  if (strcmp($_SESSION['mail'],'oui') == 0)
  {
    header('LOCATION: carte.php?method=' . $method . '&table=' . $table);
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Ecran de fin</title>
    <meta name="description" content="A demo of a card payment on Stripe" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="global.css" />
    <link rel="stylesheet" href="css/custom.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="mail.js"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>

  <body onload="reachBottom()">
    <div id="main">
      <?php
        $logo = GetValeurParam("master_logo",$conn);     
        echo '<img id="logo" src="' . $logo . '">';
      ?>
      <div id="envoieok">Votre commande a été envoyé</div> 
    </div>
    <div id="footer">
      <?php
        echo '<input class="inpmove poursuivre" type="button" value="Commander à nouveau" onclick="window.location.href = \'carte.php?method=' . $method . '&table=' . $table . '\'">';
      ?>
    </div>
    <script type="text/javascript" >
      sessionStorage.clear();
    </script>
    <script type="text/javascript">
      function reachBottom()
      {
        var x = window.innerHeight - document.getElementById("footer").clientHeight;
        x = x + "px";
        document.getElementById("main").style.height = x;
      }
    </script>
  </body>
</html>
