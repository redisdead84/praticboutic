<?php

  session_id("customerarea");
  session_start();
  
  if (empty($_SESSION['verify_email']) == TRUE)
  {
    header("LOCATION: index.php");
    exit();
  }
  
  require_once '../../vendor/autoload.php';
  
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Création Nouvelle Boutic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.02">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body class="custombody">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spaceflex">
        <div id="loadid" class="spinner-border" role="status" style="display: block;">
          <span class="sr-only">Loading...</span>
        </div>
        <img id='illus6' src='img/illustration_6.png' style="display: none;"/>
        <main class="fcb">
          <div id='mainform' class="customform" style="display: none;">
            <p class="center middle title">
              Paramétrage de l'achat
            </p>
            <form id="signup-form" onsubmit="bakinfo()" method="post" action="confboutic.php" autocomplete="on">
              <div class="">
                <div class="param">
                  <input class="" id="surplaceid" name="surplace" type="checkbox" title="Logo automatique" autocomplete="on" checked />
                  <label class="paramlabellf" for="surplaceid" title="facultatif">Je dispose de tables pour la restauration sur place</label>
                </div>
                <div class="param">
                  <label for="chxmethodeid">Méthode de vente : </label>
                    <select class="paramfieldc" id="chxmethodeid" name="chxmethode" value="Emporter & Livrer">
                      <option value='EMPORTER'>Emporter</option>
                      <option value='LIVRER'>Livrer</option>
                      <option value='TOUS' selected>Emporter & Livrer</option>
                    </select><br>
                </div>
                <div class="param">
                  <input class="" id="caisseid" name="caisse" type="checkbox" title="Caisse" autocomplete="on" value="CAISSE" />
                  <label class="paramlabellf" for="caisseid" title="">Je propose également de payer au comptant (espèces, tickets restaurant, chèques ...)</label>
                </div>
                <div class="param">
                  <label id="mntmincmdidlbl" for="mntmincmd">Montant Commande Minimum : </label>
                  <input class="paramfieldc inpprix" id="mntmincmdid" type='number' step='0.01' min='0' name="mntmincmd" placeholder="Montant minimum de commande" value="1.00" /><br>
                </div>
                <div class="param rwse">
                  <label id="validsmslbl" for="validsms">Validation Commande par SMS : </label>
                  <div class="param center"><input class="paramfieldc center" type="radio" id="smson" name="validsms" value="on" required checked><label class="paramfieldr" for="validsmson">&nbsp;Activé&nbsp;</label></div><div class="param center"><input class="paramfieldc center" type="radio" id="smsoff" name="validsms" value="off"><label class="paramfieldr">&nbsp;Désactivé&nbsp;</label></div><br>
                </div>
              </div>
              <div class="param rwc margetop">
                <input class="butc btn-mssecondary" id="msannul" type="button" onclick="javascript:cancel()" value="ANNULATION" />
                <input class="butc btn-msprimary" id="msvalid" type="submit" value="CONFIRMATION" autofocus /><br><br>
              </div>
            </form>
          </div>
        </main>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript" >
    function bakinfo()
    {
      document.getElementById("loadid").style.display = "block";
      document.getElementById("mainform").style.display = "none";
      document.getElementById("illus6").style.display = "none";
      sessionStorage.setItem('pb_paramb_chxmethodeid', document.getElementById("chxmethodeid").value);
      sessionStorage.setItem('pb_paramb_mntmincmdid', document.getElementById("mntmincmdid").value);
      if (document.getElementById("smson").checked == true)
        sessionStorage.setItem('pb_reg_validsms', "on");
      if (document.getElementById("smsoff").checked == true)
        sessionStorage.setItem('pb_reg_validsms', "off");
  
    }
    window.onload=function()
    {
      if (sessionStorage.getItem('pb_paramb_chxmethodeid') !== null)
        document.getElementById("chxmethodeid").value = sessionStorage.getItem('pb_paramb_chxmethodeid');
      if (sessionStorage.getItem('pb_paramb_mntmincmdid') !== null)
        document.getElementById("mntmincmdid").value = sessionStorage.getItem('pb_paramb_mntmincmdid');
      if (sessionStorage.getItem('pb_reg_validsms')  == "on")
      {
        document.getElementById("smson").checked = true;
        document.getElementById("smsoff").checked = false;
      }
      if (sessionStorage.getItem('pb_reg_validsms')  == "off")
      {
        document.getElementById("smson").checked = false;
        document.getElementById("smsoff").checked = true;
      }
      document.getElementById("loadid").style.display = "none";
      document.getElementById("mainform").style.display = "block";
      document.getElementById("illus6").style.display = "block";
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
      if (confirm("Voulez-vous quitter ?") == true)
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("mainform").style.display = "none";
        document.getElementById("illus6").style.display = "none";
        window.location.href ='exit.php';
      }
    }
  </script>
</html>
