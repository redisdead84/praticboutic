<?php
  session_start();

  $customer = $_GET['customer'];
  
  
  include "../" . $customer . "/config/custom_cfg.php";
  include "config/common_cfg.php";
  include "param.php";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $bdd);
  // Check connection
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);

  $reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
  $reqci->bind_param("s", $customer);
  $reqci->execute();
  $reqci->bind_result($customid);
  $resultatci = $reqci->fetch();
  $reqci->close();

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
    <title>Ecran de fin</title>
    <meta name="description" content="A demo of a card payment on Stripe" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
		<link rel="stylesheet" media="screen" href="css/style2.css?v=<?php echo $ver_com_css;?>" />
    <link rel="stylesheet" href="css/style.css?v=<?php echo $ver_com_css;?>" />
    <link rel="stylesheet" href="../<?php echo $customer;?>/css/custom.css?v=<?php echo $ver_cust_css;?>">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="js/mail.js?v=1.25"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	  <script type="text/javascript" src="js/bandeau.js?v=1.01"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>

  <body>
    <div id="main">
      <?php
        $logo = GetValeurParam("master_logo",$conn, $customid);     
        echo '<img id="logo" src="../' . $customer . '/' . $logo . '">';
      ?>
      <div class="panneau" id="envoieok">Votre commande a été envoyée.</div> 
    </div>
    <div id="footer">
      <?php
        echo '<input class="inpmove poursuivre" type="button" value="Commander à nouveau" onclick="window.location.href = \'carte.php?method=' . $method . '&table=' . $table . '&customer=' . $customer . '\'">';
      ?>
    </div>
    <script type="text/javascript" >
    	var close = 0; 
    	if (sessionStorage.getItem("barre") == "close")
    		close = 1;
      sessionStorage.clear();
      if (close == 1)
      	sessionStorage.setItem("barre", "close");
    </script>
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
