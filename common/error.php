<?php

include "config/common_cfg.php";

session_start();

$customer = $_SESSION['customer'];


error_log($customer);
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Page d'erreur</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" media="screen" href="css/style2.css?v=<?php echo $ver_com_css;?>" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css?v=<?php echo $ver_com_css;?>">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>

  <body ondragstart="return false;" ondrop="return false;">
    <div id="header">
      <img id="mainlogo" src="img/logo-pratic-boutic.png">
    </div>
    <h3>Vous êtes arrivé ici à cause de l'erreur suivante : </h3>
    <p id="errmsgid">Non spécifié</p>
    <button id="retbtnid" class="inpmove revenir" onclick="window.location = 'https://pratic-boutic.fr/'">Quitter la boutic</button>
  </body>
  <script>
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const code = urlParams.get('code');
    const errmsgid = document.getElementById('errmsgid');
    const retbtnid = document.getElementById('retbtnid');
    
    switch(code) {
      case 'nocustomer':
        errmsgid.innerHTML = 'L\'alias de la boutic n\'est pas défini, impossible d\'obtenir le guichet';
      break;
      case 'nobouticid':
        errmsgid.innerHTML = 'L\'identifiant de la boutic n\'est pas défini, impossible d\'obtenir le guichet';
      break;
      case 'noemail':
        errmsgid.innerHTML = 'Impossible de savoir si le courriel de commande est passé';
        retbtnid.innerHTML = 'Réinitialiser la boutic';
        retbtnid.onclick = function(){
          window.location = 'index.php?customer=' + '<?php echo $customer;?>';
        };
      break;
      case 'alreadysent':
        errmsgid.innerHTML = 'Commande déjà envoyé';
        errmsgid.innerHTML = 'Impossible de savoir si le courriel de commande est passé';
        retbtnid.innerHTML = 'Réinitialiser la boutic';
        retbtnid.onclick = function(){
          window.location = 'index.php?customer=' + '<?php echo $customer;?>';
        };
      break;
      case 'noabo':
        errmsgid.innerHTML = 'Il n\'y a pas d\'abonnement actif sur la boutic ';
      break;
      case 'nostripeid':
        errmsgid.innerHTML = 'Impossible de trouver l\'identifiant Stripe de la boutic';
      break;
      case 'cantinitstripe':
        errmsgid.innerHTML = 'Impossible d\'initialiser Stripe';
        retbtnid.innerHTML = 'Changer le mode de paiement';
        retbtnid.onclick = function(){
          window.location = 'getinfo.php';
        };
      break;
      case 'errmail':
        errmsgid.innerHTML = 'Erreur lors de l\'envoi du courriel';
        errmsgid.innerHTML = 'Impossible de savoir si le courriel de commande est passé';
        retbtnid.innerHTML = 'Réinitialiser la boutic';
        retbtnid.onclick = function(){
          window.location = 'index.php?customer=' + '<?php echo $customer;?>';
        };
      break;
    }
  </script>
</html>
