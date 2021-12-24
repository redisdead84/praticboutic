<?php

session_id("customerarea");
session_start();

if (empty($_SESSION['verify_email']) == TRUE)
{
  header("LOCATION: index.php");
  exit();
}

require_once '../../vendor/autoload.php';
require_once '../config/common_cfg.php';

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
        <div class="customform">
          <p class="center middle title">
            Choix de paiement
          </p>
          <form id="moneysys-form" name="mainform" onsubmit="bakinfo()" method="post" action="moneysys.php" autocomplete="off">
            <input id="moneysystemid" name="moneysystem" type="hidden" value="NONE" />
            <input id="caisseid" name="caisse" type="hidden" value="NONE" />
            <div class="chxpaisys">
              <div class="blocsysmoney">
                <img id="stripeico" name="stripeico" class="paieico" src="img/stripe_unselected.png" onclick="toggle(this)" data-state="off">
                <div id="idparamstripe" style="display: none;">
                  <div id="iddivpubkey" class="param">
                    <input class="paramfieldc" id="publickeyid" maxlength="255" name="publickey" type="text" value="" autocomplete="off" placeholder="Clé Public Stripe" maxlength="255" />
                  </div>
                  <div id="iddivseckey" class="param">
                    <input class="paramfieldc" id="secretkeyid" maxlength="255" name="secretkey" type='password' value="" autocomplete="one-time-code" placeholder="Clé Privé Stripe" maxlength="255" />
                  </div>
                  <a id="idlienstripe" href="https://www.stripe.com/" target="_blank">Stripe (site officiel) - Standard du paiement en ligne</a>
                </div>
              </div>
              <div class="blocsysmoney">
                <img id="paypalico" name="paypalico" class="paieico" src="img/paypal_unselected.png" onclick="toggle(this)" data-state="off">
                <div id="idparampaypal" style="display: none;">
                  <div id="iddivppakey" class="param">
                    <input class="paramfieldc" id="idcltpaypalid" type='text' maxlength="255" name="idcltpaypal" autocomplete="off" placeholder="ID Client Paypal"/>
                  </div>
                  <a id="idlienpaypal" href="https://www.paypal.com/" target="_blank">Paiements en ligne - Transferts d'argent | PayPal FR</a>
                </div>
              </div>
              <div class="blocsysmoney">
                <img id="caisseico" name="caisseico" class="paieico" src="img/caisse_unselected.png" onclick="toggle(this)" data-state="off">
              </div>
            </div>
            <div class="param rwc margetop">
              <input class="butc btn-mssecondary" id="msannul" type="button" onclick="javascript:cancel()" value="ANNULATION" />
              <input class="butc btn-msprimary" id="msvalid" type="button" onclick="javascript:bakinfo()" value="CONFIRMATION" autofocus style="opacity: 0.5" /><br><br>
            </div>
          </form>
        </div>
        <img id='illus5' src='img/illustration_5.png' />
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript">
    function bakinfo()
    {
      checkinfo();
      sessionStorage.setItem('pb_initb_moneysys', document.getElementById("moneysystemid").value);
      sessionStorage.setItem('pb_initb_caisseid', document.getElementById("caisseid").value);
      sessionStorage.setItem('pb_initb_publickeyid', document.getElementById("publickeyid").value);
      sessionStorage.setItem('pb_initb_secretkeyid', document.getElementById("secretkeyid").value);
      sessionStorage.setItem('pb_initb_idcltpaypalid', document.getElementById("idcltpaypalid").value);
    }
    
    function checkinfo()
    {
      var failed = false;
      var astk = ('<?php echo $allowstripetestkey; ?>' === 'oui');
      
      if (document.forms["mainform"]["stripeico"].getAttribute("data-state") == "on")
      {
        var pubkey = document.forms["mainform"]["publickey"].value;
        var seckey = document.forms["mainform"]["secretkey"].value;
        if (!((pubkey.startsWith('pk_live')==true)||((pubkey.startsWith('pk_test') == true)&&(astk == true))))
        {
          failed = true;
          if (astk == false)
            alert("La clé public Stripe doit commencer par 'pk_live'");
          else
            alert("La clé public Stripe doit commencer par 'pk_test' ou 'pk_live'");
        }
        if (!((seckey.startsWith('sk_live')==true)||((seckey.startsWith('sk_test') == true)&&(astk == true))))
        {
          failed = true;
          if (astk == false)
            alert("La clé secrète Stripe doit commencer par 'sk_live'");
          else
            alert("La clé secrète Stripe doit commencer par 'sk_test' ou 'sk_live'");
        }
      }
      if (failed == false)
        document.forms["mainform"].submit();
    }
    
    window.onload=function()
    {
      if (sessionStorage.getItem('pb_initb_moneysys') !== null)
        document.getElementById("moneysystemid").value = sessionStorage.getItem('pb_initb_moneysys');
      document.getElementById("publickeyid").value = sessionStorage.getItem('pb_initb_publickeyid');
      document.getElementById("secretkeyid").value = sessionStorage.getItem('pb_initb_secretkeyid');
      document.getElementById("idcltpaypalid").value = sessionStorage.getItem('pb_initb_idcltpaypalid');
      document.getElementById("caisseid").value = sessionStorage.getItem('pb_initb_caisseid');
      document.getElementById("msvalid").disabled = true;
      document.getElementById("msvalid").style = "opacity: 0.5";
      var elemc = document.getElementById("caisseid");
      if (elemc.value == 'NONE')
      {
        document.getElementById("caisseico").setAttribute("data-state", "off");
        document.getElementById("caisseico").src = "img/caisse_unselected.png";
      }
      else if (elemc.value == 'COMPTANT')
      {
        document.getElementById("caisseico").setAttribute("data-state", "off");
        document.getElementById("caisseico").src = "img/caisse_unselected.png";
      }
      else if (elemc.value == 'LIVRAISON')
      {
        document.getElementById("caisseico").setAttribute("data-state", "on");
        document.getElementById("caisseico").src = "img/caisse_selected.png";
        document.getElementById("msvalid").disabled = false;
        document.getElementById("msvalid").style = "opacity: 1";
      }
      else if (elemc.value == 'TOUS')
      {
        document.getElementById("caisseico").setAttribute("data-state", "on");
        document.getElementById("caisseico").src = "img/caisse_selected.png";
        document.getElementById("msvalid").disabled = false;
        document.getElementById("msvalid").style = "opacity: 1";
      }
      var elemms = document.getElementById("moneysystemid");
      if (elemms.value == 'NONE')
      {
        document.getElementById("stripeico").setAttribute("data-state", "off");
        document.getElementById("paypalico").setAttribute("data-state", "off");
        document.getElementById("stripeico").src = "img/stripe_unselected.png";
        document.getElementById("paypalico").src = "img/paypal_unselected.png";
        document.getElementById("idparamstripe").style.display = "none";
        document.getElementById("idparampaypal").style.display = "none";
      }
      else if (elemms.value == 'PAYPAL')
      {
        document.getElementById("stripeico").setAttribute("data-state", "off");
        document.getElementById("paypalico").setAttribute("data-state", "on");
        document.getElementById("stripeico").src = "img/stripe_unselected.png";
        document.getElementById("paypalico").src = "img/paypal_selected.png";
        document.getElementById("idparamstripe").style.display = "none";
        document.getElementById("idparampaypal").style.display = "block";
        document.getElementById("msvalid").disabled = false;
        document.getElementById("msvalid").style = "opacity: 1";
      }
      else if (elemms.value == 'STRIPE')
      {
        document.getElementById("paypalico").setAttribute("data-state", "off");
        document.getElementById("stripeico").setAttribute("data-state", "on");
        document.getElementById("stripeico").src = "img/stripe_selected.png";
        document.getElementById("paypalico").src = "img/paypal_unselected.png";
        document.getElementById("idparamstripe").style.display = "block";
        document.getElementById("idparampaypal").style.display = "none";
        document.getElementById("msvalid").disabled = false;
        document.getElementById("msvalid").style = "opacity: 1";
      }
    }
  
    function cancel() 
    {
      bakinfo();
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
  <script type="text/javascript" >
    function toggle(elem)
    {
      if (elem.id == "paypalico")
      {
        if (elem.getAttribute("data-state") == "off")
        {
          document.getElementById("stripeico").setAttribute("data-state", "off");
          document.getElementById("stripeico").src = "img/stripe_unselected.png";
          elem.setAttribute("data-state", "on");
          elem.src = "img/paypal_selected.png";
          document.getElementById("moneysystemid").value = "PAYPAL";
          document.getElementById("idparamstripe").style.display = "none";
          document.getElementById("idparampaypal").style.display = "block";
          if (document.getElementById("caisseico").getAttribute("data-state") == "on")
             document.getElementById("caisseid").value = "TOUS";
          else if (document.getElementById("caisseico").getAttribute("data-state") == "off")
             document.getElementById("caisseid").value = "COMPTANT";
          document.getElementById("msvalid").disabled = false;
          document.getElementById("msvalid").style = "opacity: 1";
        }
        else if (elem.getAttribute("data-state") == "on")
        {
          elem.setAttribute("data-state", "off");
          elem.src = "img/paypal_unselected.png";
          document.getElementById("moneysystemid").value = "NONE";
          document.getElementById("caisseid").value = "LIVRAISON";
          document.getElementById("idparamstripe").style.display = "none";
          document.getElementById("idparampaypal").style.display = "none";
          if (document.getElementById("caisseico").getAttribute("data-state") == "on")
          {
            document.getElementById("caisseid").value = "LIVRAISON";
            document.getElementById("msvalid").disabled = false;
            document.getElementById("msvalid").style = "opacity: 1";
          }
          else if (document.getElementById("caisseico").getAttribute("data-state") == "off")
          {
            document.getElementById("caisseid").value = "NONE";
            document.getElementById("msvalid").disabled = true;
            document.getElementById("msvalid").style = "opacity: 0.5";
          }
        }
      }
      else if (elem.id == "stripeico")
      {
        if (elem.getAttribute("data-state") == "off")
        {
          document.getElementById("paypalico").setAttribute("data-state", "off");
          document.getElementById("paypalico").src = "img/paypal_unselected.png";
          elem.setAttribute("data-state", "on");
          elem.src = "img/stripe_selected.png";
          document.getElementById("moneysystemid").value = "STRIPE";
          document.getElementById("idparamstripe").style.display = "block";
          document.getElementById("idparampaypal").style.display = "none";
          if (document.getElementById("caisseico").getAttribute("data-state") == "on")
             document.getElementById("caisseid").value = "TOUS";
          else if (document.getElementById("caisseico").getAttribute("data-state") == "off")
             document.getElementById("caisseid").value = "COMPTANT";
          document.getElementById("msvalid").disabled = false;
          document.getElementById("msvalid").style = "opacity: 1";
        }
        else if (elem.getAttribute("data-state") == "on")
        {
          elem.setAttribute("data-state", "off");
          elem.src = "img/stripe_unselected.png";
          document.getElementById("moneysystemid").value = "NONE";
          document.getElementById("caisseid").value = "LIVRAISON";
          document.getElementById("idparamstripe").style.display = "none";
          document.getElementById("idparampaypal").style.display = "none";
          if (document.getElementById("caisseico").getAttribute("data-state") == "on")
          {
            document.getElementById("caisseid").value = "LIVRAISON";
            document.getElementById("msvalid").disabled = false;
            document.getElementById("msvalid").style = "opacity: 1";
          }
          else if (document.getElementById("caisseico").getAttribute("data-state") == "off")
          {
            document.getElementById("caisseid").value = "NONE";
            document.getElementById("msvalid").disabled = true;
            document.getElementById("msvalid").style = "opacity: 0.5";
          }
        }
      }
      else if (elem.id == "caisseico")
      {
        if (elem.getAttribute("data-state") == "off")
        {
          elem.src = "img/caisse_selected.png";
          elem.setAttribute("data-state", "on");
          if (document.getElementById("moneysystemid").value == "NONE")
            document.getElementById("caisseid").value = "LIVRAISON";
          else if (document.getElementById("moneysystemid").value != "NONE")
            document.getElementById("caisseid").value = "TOUS";
          document.getElementById("msvalid").disabled = false;
          document.getElementById("msvalid").style = "opacity: 1";
        }
        else if (elem.getAttribute("data-state") == "on")
        {
          elem.src = "img/caisse_unselected.png";
          elem.setAttribute("data-state", "off");
          if (document.getElementById("moneysystemid").value == "NONE")
          {
            document.getElementById("caisseid").value = "NONE";
            document.getElementById("msvalid").disabled = true;
            document.getElementById("msvalid").style = "opacity: 0.5";
          }
          else if (document.getElementById("moneysystemid").value != "NONE")
          {
            document.getElementById("caisseid").value = "COMPTANT";
            document.getElementById("msvalid").disabled = false;
            document.getElementById("msvalid").style = "opacity: 1";
          }
        }
      }
    }
  </script>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter ?") == true)
      {
        window.location.href ='exit.php';
      }
    }
  </script>
</html>
