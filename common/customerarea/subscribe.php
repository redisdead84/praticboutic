<?php
  session_start();

  if (empty($_SESSION['verify_email']) == TRUE)
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
    <title>Souscription</title>
    <script src="https://js.stripe.com/v3/"></script>
    <!--<script src="subscribe.js" defer></script>-->
  </head>
  <body class="custombody">
  <a href="logout.php">Deconnexion</a>
    <main class="fcb">
      <h1>Informations de Paiement</h1>
<!--
      <p>
        Try the successful test card: <span>4242424242424242</span>.
      </p>

      <p>
        Try the test card that requires SCA: <span>4000002500003155</span>.
      </p>

      <p>
        Use any <i>future</i> expiry date, CVC, and 5 digit postal code.
      </p>

      <hr />
-->
      <form id="subscribe-form">

        <div id="card-element">
          <!-- the card element will be mounted here -->
        </div>

        <label>
          Nom complet
          <input type="text" id="name" value="" />
        </label>
        
        <br />

        <input class="butc regbutton" type="button" onclick="javascript:cancel()" value="Annulation" />

        <button type="submit">
          Inscription
        </button>

        <div id="messages"></div>
      </form>
    </main>
  </body>
    <script type="text/javascript" >
  // helper method for displaying a status message.
    const setMessage = (message) => {
      const messageDiv = document.querySelector('#messages');
      messageDiv.innerHTML += "<br>" + message;
    }
    
    // Fetch public key and initialize Stripe.
    let stripe, cardElement;
    
    var obj = { action: "configuration", login: <?php echo '"' . $_SESSION['verify_email'] . '"'; ?>};
    fetch('abo.php', {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(obj)
      })
      .then((resp) => resp.json())
      .then((resp) => {
        stripe = Stripe(resp.publishableKey);
        const elements = stripe.elements({
  				fonts: [
  					{
  						cssSrc: 'https://fonts.googleapis.com/css?family=Public+Sans'
  					}
  				],
  			});
        var style = {
        hidePostalCode: true,
        base: {
          color: "black",
          backgroundColor: "white",
          fontFamily: 'Public Sans',
          fontSmoothing: "antialiased",
          fontSize: "16px",
          "::placeholder": {
            color: "black"
          }
        },
        invalid: {
          fontFamily: 'Arial, sans-serif',
          color: "#fa755a",
          iconColor: "#fa755a"
        }
      };    
 
  			
        cardElement = elements.create("card", {hidePostalCode: true, style: style });
        cardElement.mount('#card-element');
      });
    
    // Extract the client secret query string argument. This is
    // required to confirm the payment intent from the front-end.
    const params = new URLSearchParams(window.location.search);
    const subscriptionId = params.get('subscriptionId');
    const clientSecret = params.get('clientSecret');
    
    // This sample only supports a Subscription with payment
    // upfront. If you offer a trial on your subscription, then
    // instead of confirming the subscription's latest_invoice's
    // payment_intent. You'll use stripe.confirmCardSetup to confirm
    // the subscription's pending_setup_intent.
    // See https://stripe.com/docs/billing/subscriptions/trials
    
    // Payment info collection and confirmation
    // When the submit button is pressed, attempt to confirm the payment intent
    // with the information input into the card element form.
    // - handle payment errors by displaying an alert. The customer can update
    //   the payment information and try again
    // - Stripe Elements automatically handles next actions like 3DSecure that are required for SCA
    // - Complete the subscription flow when the payment succeeds
    const form = document.querySelector('#subscribe-form');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const nameInput = document.getElementById('name');
    
      // Create payment method and confirm payment intent.
      stripe.confirmCardPayment(clientSecret, {
        payment_method: {
          card: cardElement,
          billing_details: {
            name: nameInput.value,
          },
        }
      }).then((result) => {
        if(result.error) {
          setMessage(`Payment failed: ${result.error.message}`);
        } else {
          var obj = { action: "activationabonnement", login: <?php echo '"' . $_SESSION['verify_email'] . '"'; ?>, subscriptionId:subscriptionId};
          fetch('abo.php', {
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
              // Redirect the customer to their account page
              setMessage('Congratulations! Nous vous redirigeons vers votre compte.');
              window.location.href = 'buildboutic.php';
            }
          })
        }
      });
    });

    function cancel() 
    {
      window.location.href = './prices.php';
    }

  </script>
</html>
