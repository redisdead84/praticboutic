<?php
  session_start();

  if (empty($_SESSION['bo_email']) == TRUE)
  {
    header("LOCATION: index.php");
    exit();
  }

  include "../config/common_cfg.php";
  include "../param.php";

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.01">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Tarifs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!--<script src="./prices.js" defer></script>-->
  </head>
  <body class="custombody">
  <a href="logout.php">Deconnexion</a>
    <main class="fcb">
      <h1>Choississez la formule</h1>

      <div id="price-list" class="price-list">
        Chargement...
      </div>
      
      <input class="butc regbutton" type="button" onclick="javascript:cancel()" value="Annulation" />

    </main>
    
    <script type="text/javascript" >
      const createSubscription = (priceId) => {
      const params = new URLSearchParams(window.location.search);
      const customerId = params.get('customerId');
      var obj = { action: "bocreationabonnement", login: <?php echo '"' . $_SESSION['bo_email'] . '"'; ?>, priceid:priceId};
      return fetch('abo.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(obj)
      })
      .then(function(result) {
        return result.json();
      }) 
      .then(function(data) {
        if (typeof (data.error) !== "undefined")
        {
          var modal = $('.modal');
          $('.modal-title').html('Erreur');
          modal.find('.modal-body').text(data.error);
          $('.modal').modal('show');
        }
        else 
        {
          const params = new URLSearchParams(window.location.search);
          params.append('subscriptionId', data.subscriptionId);
          params.append('clientSecret', data.clientSecret);
          window.location.href = 'bosubscribe.php?' + params.toString();
        }
      })
    }
    </script>
    <script type="text/javascript" >
      function conso(priceId) {
        var obj = { action: "boconso", login: <?php echo '"' . $_SESSION['bo_email'] . '"'; ?>, priceid:priceId};
        return fetch('abo.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(obj)
        })
        .then(function(result) {
          return result.json();
        }) 
        .then(function(data) {
          if (typeof (data.error) !== "undefined")
          {
            var modal = $('.modal');
            $('.modal-title').html('Erreur');
            modal.find('.modal-body').text(data.error);
            $('.modal').modal('show');
          }
          else 
          {
            /*const params = new URLSearchParams(window.location.search);
            params.append('subscriptionId', data.subscriptionId);
            params.append('clientSecret', data.clientSecret);
            window.location.href = 'subscribe.php?' + params.toString();*/
            //const params = new URLSearchParams(window.location.search);
            //const customerId = params.get('customerId');
            const params = new URLSearchParams(window.location.search);
            params.append('customerId', data.customerId);
            params.append('priceId', data.priceId);
            window.location.href = 'boconso.php?' + params.toString();            
            
          }
        })
      }
    </script>
    <script type="text/javascript" >
      const pricesDiv = document.querySelector('#price-list');
      var obj = { action: "configuration", login: <?php echo '"' . $_SESSION['bo_email'] . '"'; ?>};

      fetch("abo.php", {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(obj)
      })
      .then(function(result) {
        return result.json();
      }) 
      .then(function(data) {
        if (typeof (data.error) !== "undefined")
        {
          var modal = $('.modal');
          $('.modal-title').html('Erreur');
          modal.find('.modal-body').text(data.error);
          $('.modal').modal('show');
        }
        else 
        {
          //alert("list des prix ok");
          pricesDiv.innerHTML = '';
          if(!data.prices) 
          {
            pricesDiv.innerHTML = `
            <h3>Pas de tarif trouvé</h3>
    
            <p>This sample requires two prices, one with the lookup_key sample_basic and another with the lookup_key sample_premium</p>
    
            <p>You can create these through the API or with the Stripe CLI using the provided seed.json fixture file with: <code>stripe fixtures seed.json</code>
            `
          }

          data.prices.forEach((price) => {
            if (price.lookup_key == "pb_fixe")
            {
              pricesDiv.innerHTML += `
                <div>
                  <span>
                    ${price.nickname} : ${(price.unit_amount / 100).toFixed(2)} ${price.metadata.currency_symbol} ${price.metadata.fr_interval}
                  </span>
                  <button onclick="createSubscription('${price.id}')">Sélection</button>
                </div>
              `;
            }
            else if (price.lookup_key == "pb_conso")
            {
              pricesDiv.innerHTML += `
                <div>
                  <span>
                    ${price.nickname} : ${price.unit_amount_decimal}% de commission
                  </span>
                  <button onclick="conso('${price.id}')">Sélection</button>
                </div>
              `;
            }
          });
        }
      })
      function cancel() 
      {
        window.location.href = './account.php';
      }
    </script>
  </body>
</html>
