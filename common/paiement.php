<?php
  session_start();
  $customer = $_GET['customer'];

  include "config/common_cfg.php";
  include "param.php";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $bdd);
  // Check connection
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);    

  $method = isset($_GET ['method']) ? $_GET ['method'] : '0';
  $table = isset($_GET ['table']) ? $_GET ['table'] : '0';

  if (empty($_SESSION[$customer . '_mail']) == TRUE)
  {
    header('LOCATION: ../' . $customer . '/index.php');
    exit();
  }
  
  if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
  {
    header('LOCATION: carte.php?method=' . $method . '&table=' . $table . '&customer=' . $customer);
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
    <!--<link rel="stylesheet" href="css/style.css?v=1.22" />-->
    <link rel="stylesheet" href="css/global.css?v=1.23" />
    <link rel="stylesheet" href="../<?php echo $customer;?>/css/custom.css?v=1.23">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="js/client.js?v=1.24" defer></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>

    <?php
    
    $reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
    $reqci->bind_param("s", $customer);
    $reqci->execute();
    $reqci->bind_result($customid);
    $resultatci = $reqci->fetch();
    $reqci->close();
 	    
    $pkey = GetValeurParam("PublicKey", $conn, $customid);
    
    echo '<div id="main" data-publickey="' . $pkey . '">';
    
    $logo = GetValeurParam("master_logo",$conn, $customid);     
    echo '<img id="logo" src="../' . $customer . '/' . $logo . '">';
    
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
          document.write('<div class="intercalaire">');
          document.write('<p id="card-error" role="alert"></p>');
          document.write('<p class="result-message hidden">');
          document.write('Paiement effectué<!--, Voyez le résultat dans votre');
          document.write('<a href="" target="_blank">interface Stripe.</a> Rafraichisser la page pour payer encore-->.');
          document.write('</p>');
          document.write('</div>');
          document.write('</form>');
        } else {
          document.write('<button class="poursuivre" id="validbutton" onclick="window.location.href = \'fin.php?method=' + sessionStorage.getItem("method") + '&table=' + sessionStorage.getItem("table") + '&customer=' + sessionStorage.getItem("customer") + '\'">');
          document.write('Valider la commande');
          document.write('</button>');
        }
        
        document.write('<button class="revenir" id="backbutton" onclick="window.location.href = \'getinfo.php?method=' + sessionStorage.getItem("method") + '&table=' + sessionStorage.getItem("table") + '&customer=' + sessionStorage.getItem("customer") + '\'">');
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
    <script type="text/javascript">
      reachBottom();
    </script>
    <script type="text/javascript">
      window.addEventListener("resize", function() {
        reachBottom();
      })
    </script>
  </body>
</html>
