<?php

session_start();

if (empty($_SESSION['verify_email']) == TRUE)
{
  header("LOCATION: index.php");
  exit();
}

require_once '../../vendor/autoload.php';

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Système de paiement de la boutic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/back.css?v=1.02">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body class="custombody">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spaceflex">
        <main class="fcb">
          <div class="customform">
            <p class="center middle title">
              Choix de paiement
            </p>
            <form id="moneysys-form" onsubmit="bakinfo()" method="post" action="moneysys.php" autocomplete="on">
              <div class="param">
                <label for="moneysystemid">Choix du système</label>
                <select class="paramfieldc" id="moneysystemid" name="moneysystem" onchange="setmoneysystem()">
                  <option value="STRIPE" selected>STRIPE</option>
                  <option value="PAYPAL">PAYPAL</option>
                </select><br>
              </div>
              <div id="idparamstripe" class="param">
                <div id="iddivpubkey" class="param">
                  <label id="publickeyidlbl" for="publickeyid">Clé Public Stripe : </label>
                  <input class="paramfieldc" id="publickeyid" maxlength="255" name="publickey" type="text" value="" autocomplete="off" />
                </div>
                <div id="iddivseckey" class="param">
                  <label id="secretkeyidlbl"  for="secretkeyid">Clé Privé Stripe : </label>
                  <input class="paramfieldc" id="secretkeyid" maxlength="255" name="secretkey" type='password' value="" autocomplete="one-time-code" />
                </div>
                <a id="idlienstripe" href="https://www.stripe.com/" target="_blank">Stripe (site officiel) - Standard du paiement en ligne</a>
              </div>
              <div id="idparampaypal" class="param"> 
                <div id="iddivppakey" class="param">
                  <label  id="idcltpaypalidlbl" for="idcltpaypalid">ID Client Paypal : </label>
                  <input class="paramfieldc" id="idcltpaypalid" type='text' maxlength="255" name="idcltpaypal" autocomplete="off" />
                </div>
                <a id="idlienpaypal" href="https://www.paypal.com/" target="_blank">Paiements en ligne - Transferts d'argent | PayPal FR</a>
              </div>
              <div class="param rwc margetop">
                <input class="butc regbutton" type="button" onclick="javascript:cancel()" value="Annulation" />
                <input class="butc regbutton" type="submit" value="Continuer" autofocus /><br><br>
              </div>
            </form>
          </div>
        </main>
        <img id='illus5' src='img/illustration_5.png' />
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript">
    function setmoneysystem() 
    {
      if (document.getElementById("moneysystemid").value == "STRIPE")
      {
        document.getElementById("idparamstripe").style.display = "block";
        document.getElementById("idparampaypal").style.display = "none";
      }
      else if (document.getElementById("moneysystemid").value == "PAYPAL")
      {
        document.getElementById("idparamstripe").style.display = "none";
        document.getElementById("idparampaypal").style.display = "block";
      }
    }
  
    function bakinfo()
    {
      sessionStorage.setItem('pb_initb_moneysys', document.getElementById("moneysystemid").value);
      sessionStorage.setItem('pb_initb_publickeyid', document.getElementById("publickeyid").value);
      sessionStorage.setItem('pb_initb_secretkeyid', document.getElementById("secretkeyid").value);
      sessionStorage.setItem('pb_initb_idcltpaypalid', document.getElementById("idcltpaypalid").value);
    }
    window.onload=function()
    {
      if (sessionStorage.getItem('pb_initb_moneysys') !== null)
        document.getElementById("moneysystemid").value = sessionStorage.getItem('pb_initb_moneysys');
      document.getElementById("publickeyid").value = sessionStorage.getItem('pb_initb_publickeyid');
      document.getElementById("secretkeyid").value = sessionStorage.getItem('pb_initb_secretkeyid');
      document.getElementById("idcltpaypalid").value = sessionStorage.getItem('pb_initb_idcltpaypalid');
      setmoneysystem();
    }
  
    function cancel() 
    {
      window.location.href = './newboutic.php';
    }
  </script>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter le consructeur de boutic ?") == true)
      {
        window.location ='https://pratic-boutic.fr';
      }
    }
  </script>
</html>
