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
    <title>Création Nouvelle Boutic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.01">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <div class="custombody">
    <a href="logout.php">Deconnexion</a>
    <main class="fcb">
      <div class="customform">

        <img class="centerimg" src="img/LOGO_PRATIC_BOUTIC.png" alt="Pratic Boutic image" id="logopbid" />

        <p class="center middle title">
          Initialisation de la boutic
        </p>
        <script type="text/javascript" >
          function bakinfo()
          {
            sessionStorage.setItem('pb_paramb_chxpaieid', document.getElementById("chxpaieid").value);
            sessionStorage.setItem('pb_paramb_chxmethodeid', document.getElementById("chxmethodeid").value);
            sessionStorage.setItem('pb_paramb_mntmincmdid', document.getElementById("mntmincmdid").value);
            sessionStorage.setItem('pb_paramb_validsmsid', document.getElementById("validsmsid").checked);

          }
          window.onload=function()
          {
            if (sessionStorage.getItem('pb_paramb_chxpaieid') !== null)
              document.getElementById("chxpaieid").value = sessionStorage.getItem('pb_paramb_chxpaieid');
            if (sessionStorage.getItem('pb_paramb_chxmethodeid') !== null)
              document.getElementById("chxmethodeid").value = sessionStorage.getItem('pb_paramb_chxmethodeid');
            if (sessionStorage.getItem('pb_paramb_mntmincmdid') !== null)
              document.getElementById("mntmincmdid").value = sessionStorage.getItem('pb_paramb_mntmincmdid');
            if (sessionStorage.getItem('pb_paramb_validsmsid') !== null)
              document.getElementById("validsmsid").checked = sessionStorage.getItem('pb_paramb_validsmsid');
          }

          function cancel() 
          {
            window.location.href = './moneyboutic.php';
          }
          
        </script>
        <form id="signup-form" onsubmit="bakinfo()" method="post" action="confboutic.php" autocomplete="on">
          <div class="">
            <div class="param">
              <label for="chxpaieid">Choix du paiement : </label>
                <select class="paramfieldc" id="chxpaieid" name="chxpaie">// onchange="setchxpaie()">
                  <option value='COMPTANT'>En ligne par CB</option>
                  <option value='LIVRAISON'>En direct par vos moyens</option>
                  <option value='TOUS' selected>En ligne & En direct</option>
                </select><br>
            </div>
            <div class="param">
              <label for="chxmethodeid">Choix de la méthode : </label>
                <select class="paramfieldc" id="chxmethodeid" name="chxmethode" value="Emporter & Livrer">// onchange="setchxmethode()">
                  <option value='EMPORTER'>Emporter</option>
                  <option value='LIVRER'>Livrer</option>
                  <option value='TOUS' selected>Emporter & Livrer</option>
                </select><br>
            </div>
            <div class="param">
              <label id="mntmincmdidlbl" for="mntmincmd">Montant Commande Minimum : </label>
              <input class="paramfieldc inpprix" id="mntmincmdid" type='number' step='0.01' min='0' name="mntmincmd" placeholder="Montant minimum de commande" value="1.00" /><br>
            </div>
            <div class="param">
              <label for="validsmsid">Validation des comandes par SMS : </label>
              <input class="paramfieldc" id="validsmsid" type='checkbox' name="validsms" checked /><br>
            </div>
          </div>
          <div class="param rwc margetop">
            <input class="butc regbutton" type="button" onclick="javascript:cancel()" value="Annulation" />
            <input class="butc regbutton" type="submit" value="Continuer" autofocus /><br><br>
          </div>
        </form>
      </div>
    </main>
  </div>
  </body>
</html>
