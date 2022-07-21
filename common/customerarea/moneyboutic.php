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
  require_once "../param.php";
  
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

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
            Comment encaisser vos ventes ?
          </p>
          <form id="moneysys-form" name="mainform" onsubmit="bakinfo()" method="post" action="moneysys.php" autocomplete="off">
            <input id="moneysystemid" name="moneysystem" type="hidden" value="NONE" />
            <input id="caisseid" name="caisse" type="hidden" value="NONE" />
            <div class="chxpaisys">
              <div class="blocsysmoney">
                <img id="stripeico" name="stripeico" class="paieico" src="img/stripe_unselected.png" onclick="toggle(this)" data-state="off">
                <div id="idparamstripe"  style="display: block;">
                  <a id="idlienstripe" href="https://www.stripe.com/" target="_blank">Stripe (site officiel) - Standard du paiement en ligne</a>
                </div>
              </div>
              <div class="blocsysmoney">
                <img id="paypalico" name="paypalico" class="paieico" src="img/paypal_unselected.png" onclick="toggle(this)" data-state="off">
                <div id="idparampaypal" style="display: none;">
                  <div id="iddivppakey" class="param">
                    <input class="paramfieldc" id="idcltpaypalid" type='text' maxlength="255" name="idcltpaypal" autocomplete="off" placeholder="ID Client Paypal"/>
                  </div>
                  <span id='lippButton'></span>
                  <script src='https://www.paypalobjects.com/js/external/api.js'></script>
                  <script>
                    paypal.use( ['login'], function (login) {
                      login.render ({
                        "appid":"AQTFxFjirZ4jnrHFeik5AQFuFJuSvhPe0n274XMjK1ogWD1W7HOsyZWy_rKrN4NJY7jHZYHWKp0MeBtO",
                        "authend":"sandbox",
                        "scopes":"openid",
                        "containerid":"lippButton",
                        "responseType":"code",
                        "locale":"fr-fr",
                        "buttonType":"LWP",
                        "buttonShape":"pill",
                        "buttonSize":"lg",
                        "fullPage":"true",
                        "returnurl":"http://127.0.0.1/common/customerarea/moneyboutic.php"
                      });
                    });
                  </script>
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
-     sessionStorage.setItem('pb_initb_moneysys', document.getElementById("moneysystemid").value);
      sessionStorage.setItem('pb_initb_caisseid', document.getElementById("caisseid").value);
      sessionStorage.setItem('pb_initb_stripeaccid', <?php echo "'" . $_SESSION['STRIPE_ACCOUNT_ID'] . "'" ?> );
      sessionStorage.setItem('pb_initb_idcltpaypalid', document.getElementById("idcltpaypalid").value);
    }
    
    window.onload=function()
    {
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
      document.getElementById("stripeico").setAttribute("data-state", "off");
      document.getElementById("paypalico").setAttribute("data-state", "off");
      document.getElementById("idparampaypal").style.display = "none";

      /*if (elemms.value == 'PAYPAL')
      {
        document.getElementById("stripeico").setAttribute("data-state", "off");
        document.getElementById("paypalico").setAttribute("data-state", "on");
        document.getElementById("stripeico").src = "img/stripe_unselected.png";
        document.getElementById("paypalico").src = "img/paypal_selected.png";
        document.getElementById("idparamstripe").style.display = "none";
        document.getElementById("idparampaypal").style.display = "block";
        document.getElementById("msvalid").disabled = false;
        document.getElementById("msvalid").style = "opacity: 1";
      }*/
      var stripeaccid = <?php echo "'" . $_SESSION['STRIPE_ACCOUNT_ID'] . "'" ?>;
      if (stripeaccid == '')
      {
        document.getElementById("stripeico").setAttribute("data-state", "off");
        document.getElementById("stripeico").src = "img/stripe_unselected.png";
      }
      else 
      {
        document.getElementById("stripeico").setAttribute("data-state", "on");
        document.getElementById("stripeico").src = "img/stripe_selected.png";
        document.getElementById("msvalid").disabled = false;
        document.getElementById("msvalid").style = "opacity: 1";
        document.getElementById("moneysystemid").value = 'STRIPE MARKETPLACE';
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
        /*if (elem.getAttribute("data-state") == "off")
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
        }*/
      }
      else if (elem.id == "stripeico")
      {
        if (elem.getAttribute("data-state") == "off")
        {
          document.location = 'stripe.php';
        }
        /*else if (elem.getAttribute("data-state") == "on")
        {
          <?php $_SESSION['STRIPE_ACCOUNT_ID'] = '' ?>;
          elem.setAttribute("data-state", "off");
          elem.src = "img/stripe_unselected.png";
          document.getElementById("caisseid").value = "LIVRAISON";
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
        }*/
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
  <!--<script type="text/javascript" >
    const img = '/to-do-notifications/img/icon-128.png';
    const text = 'Coucou ! Votre tâche toto arrive maintenant à échéance.';
    const notification = new Notification('Liste de trucs à faire', { body: text, icon: img });
  </script>-->
  <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="c21f7fea-9f56-47ca-af0c-f8978eff4c9b";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
</html>
