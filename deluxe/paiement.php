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
    <title>Validation de la commande</title>
    <meta name="description" content="A demo of a card payment on Stripe" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="global.css" />
    <link rel="stylesheet" href="css/custom.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="client.js" defer></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    
  </head>

  <body onload="reachBottom()">

    <?php
 	    
    $pkey = GetValeurParam("PublicKey", $conn);
    
    echo '<div id="main" data-publickey="' . $pkey . '">';
    
    $logo = GetValeurParam("master_logo",$conn);     
    echo '<img id="logo" src="' . $logo . '">';
    
    ?>
    </div>
    <div class="inpmove" id="footer">
      <!-- Display a payment form -->
      <script type="text/javascript">
      
        if ((sessionStorage.getItem("method")==3) && (sessionStorage.getItem("choice")=="COMPTANT")) {
          document.write('<form id="payment-form">');
          document.write('<div id="card-element"><!--Stripe.js injects the Card Element--></div>');
          document.write('<button id="submit">');
          document.write('<div class="spinner hidden" id="spinner"></div>');
          document.write('<span id="button-text">Payer</span>');
          document.write('</button>');
          document.write('<p id="card-error" role="alert"></p>');
          document.write('<p class="result-message hidden">');
          document.write('Paiement effectué<!--, Voyez le résultat dans votre');
          document.write('<a href="" target="_blank">interface Stripe.</a> Rafraichisser la page pour payer encore-->.');
          document.write('</p>');
          document.write('</form>');
        } else {
          document.write('<button id="validbutton" onclick="window.location.href = \'fin.php?method=' + sessionStorage.getItem("method") + '&table=' + sessionStorage.getItem("table") + '\'">');
          //document.write('<button id="validbutton" onclick="window.location.href = \'fin.php?method=3&table=0\'">');
          //document.write('window.location.href = "fin.php">');
          document.write('Valider la commande');
          document.write('</button>');
        }
        
        document.write('<button id="backbutton" ');
        document.write('onclick="window.history.back()">');
        document.write('Revenir sur la commande');
        document.write('</button>');
        
      </script>      
    </div>
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
