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
            //sessionStorage.setItem('pb_paramb_txtcomptantid', document.getElementById("txtcomptantid").value);
            //sessionStorage.setItem('pb_paramb_txtlivraisonid', document.getElementById("txtlivraisonid").value);
            sessionStorage.setItem('pb_paramb_chxmethodeid', document.getElementById("chxmethodeid").value);
            //sessionStorage.setItem('pb_paramb_txtemporterid', document.getElementById("txtemporterid").value);
            //sessionStorage.setItem('pb_paramb_txtlivrerid', document.getElementById("txtlivrerid").value);
            sessionStorage.setItem('pb_paramb_mntmincmdid', document.getElementById("mntmincmdid").value);
            sessionStorage.setItem('pb_paramb_mntlivraisonminiid', document.getElementById("mntlivraisonminiid").value);
            //sessionStorage.setItem('pb_paramb_tailleimgid', document.getElementById("tailleimgid").value);
            sessionStorage.setItem('pb_paramb_validsmsid', document.getElementById("validsmsid").checked);
            //sessionStorage.setItem('pb_paramb_verifcpid', document.getElementById("verifcpid").checked);

          }
          window.onload=function()
          {
            if (sessionStorage.getItem('pb_paramb_chxpaieid') !== null)
              document.getElementById("chxpaieid").value = sessionStorage.getItem('pb_paramb_chxpaieid');
            //if (sessionStorage.getItem('pb_paramb_txtcomptantid') !== null)
            //  document.getElementById("txtcomptantid").value = sessionStorage.getItem('pb_paramb_txtcomptantid');
            //if (sessionStorage.getItem('pb_paramb_txtlivraisonid') !== null)
            //  document.getElementById("txtlivraisonid").value = sessionStorage.getItem('pb_paramb_txtlivraisonid');
            if (sessionStorage.getItem('pb_paramb_chxmethodeid') !== null)
              document.getElementById("chxmethodeid").value = sessionStorage.getItem('pb_paramb_chxmethodeid');
            //if (sessionStorage.getItem('pb_paramb_txtemporterid') !== null)
            //  document.getElementById("txtemporterid").value = sessionStorage.getItem('pb_paramb_txtemporterid');
            //if (sessionStorage.getItem('pb_paramb_txtlivrerid') !== null)
            //  document.getElementById("txtlivrerid").value = sessionStorage.getItem('pb_paramb_txtlivrerid');
            if (sessionStorage.getItem('pb_paramb_mntmincmdid') !== null)
              document.getElementById("mntmincmdid").value = sessionStorage.getItem('pb_paramb_mntmincmdid');
            if (sessionStorage.getItem('pb_paramb_mntlivraisonminiid') !== null)
              document.getElementById("mntlivraisonminiid").value = sessionStorage.getItem('pb_paramb_mntlivraisonminiid');
            //if (sessionStorage.getItem('pb_paramb_tailleimgid') !== null)
            //  document.getElementById("tailleimgid").value = sessionStorage.getItem('pb_paramb_tailleimgid');
            if (sessionStorage.getItem('pb_paramb_validsmsid') !== null)
              document.getElementById("validsmsid").checked = sessionStorage.getItem('pb_paramb_validsmsid');
            //if (sessionStorage.getItem('pb_paramb_verifcpid') !== null)
            // document.getElementById("verifcpid").checked = sessionStorage.getItem('pb_paramb_verifcpid');
        }

          function cancel() 
          {
            //sessionStorage.removeItem('pb_initb_aliasboutic');
            window.location.href = './moneyboutic.php';
          }
          
          /*function setchxpaie() 
          {
            if (document.getElementById("chxpaieid").value == "TOUS")
            {
              document.getElementById("iddivtxtcomptant").style.display = "flex";
              document.getElementById("iddivtxtlivraison").style.display = "flex";
            }
            else if (document.getElementById("chxpaieid").value == "COMPTANT")
            {
              document.getElementById("iddivtxtcomptant").style.display = "flex";
              document.getElementById("iddivtxtlivraison").style.display = "none";
            }
            else if (document.getElementById("chxpaieid").value == "LIVRAISON")
            {
              document.getElementById("iddivtxtcomptant").style.display = "nonne";
              document.getElementById("iddivtxtlivraison").style.display = "flex";
            }
          }

          function setchxmethode() 
          {
            if (document.getElementById("chxmethodeid").value == "TOUS")
            {
              document.getElementById("iddivtxtemporter").style.display = "flex";
              document.getElementById("iddivtxtlivrer").style.display = "flex";
            }
            else if (document.getElementById("chxmethodeid").value == "EMPORTER")
            {
              document.getElementById("iddivtxtemporter").style.display = "flex";
              document.getElementById("iddivtxtlivrer").style.display = "none";
            }
            else if (document.getElementById("chxmethodeid").value == "LIVRER")
            {
              document.getElementById("iddivtxtemporter").style.display = "none";
              document.getElementById("iddivtxtlivrer").style.display = "flex";
            }
          }*/
        </script>
        <form id="signup-form" onsubmit="bakinfo()" method="post" action="confboutic.php" autocomplete="on">
          <div class="">
            <div class="param">
              <label for="chxpaieid">Choix du paiement : </label>
                <select class="paramfieldc" id="chxpaieid" name="chxpaie">// onchange="setchxpaie()">
                  <option value='COMPTANT'>En ligne</option>
                  <option value='LIVRAISON'>En direct</option>
                  <option value='TOUS' selected>En ligne & En direct</option>
                </select><br>
            </div>
            <!--<div id="iddivtxtcomptant" class="param">
              <label id="txtcomptantidlbl" for="txtcomptant">Texte du paiement comptant : </label>
              <input class="paramfieldc" id="txtcomptantid" type='text' maxlength="255" name="txtcomptant" value="Paiement comptant standard" autocomplete="off" /><br>
            </div>
            <div id="iddivtxtlivraison" class="param">
              <label id="txtlivraisonidlbl" for="txtlivraison">Texte du paiement à la livraison : </label>
              <input class="paramfieldc" id="txtlivraisonid" type='text' maxlength="255" name="txtlivraison" value="Paiement livraison standard" autocomplete="off" /><br>
            </div>-->
            <div class="param">
              <label for="chxmethodeid">Choix de la méthode : </label>
                <select class="paramfieldc" id="chxmethodeid" name="chxmethode" value="Emporter & Livrer">// onchange="setchxmethode()">
                  <option value='EMPORTER'>Emporter</option>
                  <option value='LIVRER'>Livrer</option>
                  <option value='TOUS' selected>Emporter & Livrer</option>
                </select><br>
            </div>
            <!--<div id="iddivtxtemporter" class="param">
              <label id="txtemporterdlbl" for="txtemporter">Texte de la vente à emporter : </label>
              <input class="paramfieldc" id="txtemporterid" type='text' maxlength="255" name="txtemporter" value="Vente à emporter standard" autocomplete="off" /><br>
            </div>
            <div id="iddivtxtlivrer" class="param">
              <label id="txtlivreridlbl" for="txtlivrer">Texte de la vente à la livraison : </label>
              <input class="paramfieldc" id="txtlivrerid" type='text' maxlength="255" name="txtlivrer" value="Vente livraison standard" autocomplete="off" /><br>
            </div>-->
            <div class="param">
              <label id="mntmincmdidlbl" for="mntmincmd">Montant Commande Minimum : </label>
              <input class="paramfieldc inpprix" id="mntmincmdid" type='number' step='0.01' min='0' name="mntmincmd" placeholder="Montant minimum de commande" value="1.00" /><br>
            </div>
            <div class="param">
              <label id="mntlivraisonminlbl" for="mntlivraisonmin">Montant Livraison Minimum : </label>
              <input class="paramfieldc inpprix" id="mntlivraisonminiid" type='number' step='0.01' min='0' name="mntlivraisonmin" placeholder="Montant minimum de livraison" value="1.00" /><br>
            </div>
            <!--<div class="param">
              <label for="tailleimgid">Taille des images : </label>
              <select class="paramfieldc" id="tailleimgid" name="tailleimg" value="Petites">
                <option value="smallimg" selected>Petites</option>
                <option value="bigimg">Grandes</option>
              </select><br>
            </div>-->
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
