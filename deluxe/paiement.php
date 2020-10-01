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
    <title>Accept a card payment</title>
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
      <div id="pan">
      <br>
      <a id="methodid"></a><br>
      <a id="tableid"></a><br>
      <div id="commandediv"></div><br>
      <a id="sommeid"></a><br>
      <br>
      </div>
    </div>
    <div class="inpmove" id="footer">
      <!-- Display a payment form -->
      <script type="text/javascript" >
      
        if ((localStorage.getItem("method")==3) && (localStorage.getItem("choice")=="COMPTANT")) {
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
        }
        
        document.write('<button id="backbutton" ');
        document.write('onclick="document.location=\'carte.php?method=' + localStorage.getItem("method") + '&table=' + localStorage.getItem("table") + '\'">');
        
        if ((localStorage.getItem("method")==3) && (localStorage.getItem("choice")=="COMPTANT"))
          document.write('Annuler (transaction non effectuée)');
        else 
          document.write('Commander à nouveau');

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
    <script type="text/javascript">
      var cart = JSON.parse(localStorage.getItem("commande"));
      var str = "";
      var somme = 0;
      str = str + "<table>"; 
      str = str + "<thead>";
      str = str + "<tr>";
      str = str + "<th>Article</th>";
      str = str + "<th>Prix</th>";
      str = str + "<th>Qté</th>";
      str = str + "<th>Total</th>";
      str = str + "</tr>";
      str = str + "</thead>";
      str = str + "<tbody>";
        for (var art in cart) {
          str = str + "<tr>";
          str = str + "<td>";
          str = str + cart[art].name;
          str = str + "</td>";
          str = str + "<td>";
          var ton_chiffre = parseFloat(cart[art].prix); // Ta variable de chiffre
          var ton_chiffre2 = ton_chiffre.toFixed(2); 
          str = str + ton_chiffre2 + " € ";
          str = str + "</td>";
          str = str + "<td>";
          str = str + cart[art].qt;
          str = str + "</td>";
          str = str + "<td>";
          str = str + (cart[art].qt * cart[art].prix).toFixed(2) + " € ";
          somme = somme + cart[art].qt * cart[art].prix;
          str = str + "</td>";

          str = str + "</tr>";
        }
      str = str + "</tbody>";
      str = str + "</table>"; 

      var method = localStorage.getItem("method");
      var method_txt = "";
      if (method == 1) 
        method_txt = "Consomation sur place";
      if (method == 2) 
        method_txt = "Vente à emporter";
      if (method == 3) 
        method_txt = "Vente en livraison";

      document.getElementById("methodid").innerHTML = method_txt + '<br>';
      if (method == 1) 
      {
        document.getElementById("tableid").innerHTML = "Table numéro " + localStorage.getItem("table") + "<br>";
      }      
      document.getElementById("commandediv").innerHTML = str;
      document.getElementById("sommeid").innerHTML = "Prix total de la commande : " + somme.toFixed(2) + " € ";
    </script>

  </body>
</html>
