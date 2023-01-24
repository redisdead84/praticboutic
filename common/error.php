<?php
 $code = $_GET['code'];
?> 

<!DOCTYPE html>
<html>
  <head>
    <title>Page d'erreur</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>

  <body ondragstart="return false;" ondrop="return false;">
    <h1>Vous êtes arrivé ici à cause de l'erreur suivante : </h1>
    <p id="errmsgid">Non spécifié</p>
    <button onclick="window.location = 'https://pratic-boutic.fr/'">Quitter la boutic</button>
  </body>
  <script>
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const code = urlParams.get('code');
    const errmsgid = document.getElementById('errmsgid');
    
    switch(code) {
      case 'nocustomer':
        errmsgid.innerHTML = 'L\'alias de la boutic n\'est pas défini, impossible d\'obtenir le guichet';
      break;
      case 'nobouticid':
        errmsgid.innerHTML = 'L\'identifiant de la boutic n\'est pas défini, impossible d\'obtenir le guichet';
      break;
      case 'noemail':
        errmsgid.innerHTML = 'Impossible de savoir si le courriel de commande est passé';
      break;
      case 'alreadysent':
        errmsgid.innerHTML = 'Commande déjà envoyé';
      break;
      case 'noabo':
        errmsgid.innerHTML = 'Il n\'y a pas d\'abonnement actif sur la boutic ';
      break;
      case 'nostripeid':
        errmsgid.innerHTML = 'Impossible de trouver l\'identifiant Stripe de la boutic';
      break;
      case 'cantinitstripe':
        errmsgid.innerHTML = 'Impossible d\'initialiser Stripe';
      break;

    }
  </script>
</html>
