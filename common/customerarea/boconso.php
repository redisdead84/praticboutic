<?php
  session_start();

  if (empty($_SESSION['bo_email']) == TRUE)
  {
    header("LOCATION: index.php");
    exit();
  }
  
  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";
  
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  
  $pubkey = $_ENV['STRIPE_PUBLISHABLE_KEY'];

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/back.css?v=1.04">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Souscription</title>
    <script src="https://js.stripe.com/v3/"></script>
  </head>
  <body class="custombody">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spaceflex">
        <div id="loadid" class="spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div id="spaceid" class="spaceflex" style="display: none;">
          <img id='illus8' src='img/illustration_8.png' />
          <div class="customform">
            <p class="center middle title">
              Informations de Paiement
            </p>
            <form id="subscribe-form">
              <div class="stripeelem" id="card-element">
                <!-- Elements will create input elements here -->
              </div>
              <input type="text" id="name" class="paramfieldc enlarged" value="" placeholder="Nom complet" />
              <br />
              <!-- We'll put the error messages in this element -->
              <div id="card-element-errors" role="alert"></div>
              <div class="ifgrpbtn">
                <input class="btn-ifsecondary" type="button" onclick="javascript:cancel()" value="ANNULATION" />
                <button class="btn-ifprimary" type="submit">CONFIRMATION</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript" >
    document.getElementById("loadid").style.display = "none";
    document.getElementById("spaceid").style.display = "flex";
    var pkey = "<?php echo $pubkey;?>";
    let stripe = Stripe(pkey);
    //let elements = stripe.elements();
    let elements = stripe.elements({
				fonts: [
					{
						cssSrc: 'https://fonts.googleapis.com/css?family=Montserrat:wght@400;500'
					}
				],
			});
    var style = {
      base: {
        color: "#444444",
        //margin-top: "5px",
        //margin-bottom: "5px",
        backgroundColor: "#EEEEEE",
        fontFamily: 'Montserrat',
        fontWeight: "500",
        lineHeight: "2.5",
        fontSmoothing: "antialiased",
        fontSize: "13px",
        "::placeholder": {
          color: "#444444"
        }
      },
      invalid: {
        fontFamily: 'Montserrat',
        color: "#fa755a",
        iconColor: "#fa755a"
      }
    };

    let card = elements.create('card', {hidePostalCode: true, style: style });
    card.mount('#card-element');
    
    card.on('change', function (event) {
      displayError(event);
    });
    
    let paymentForm = document.getElementById('subscribe-form');
    if (paymentForm) {
      paymentForm.addEventListener('submit', function (evt) {
        document.getElementById("loadid").style.display = "flex";
        document.getElementById("spaceid").style.display = "none";

        evt.preventDefault();
        //changeLoadingStateprices(true);
  
        // If a previous payment was attempted, get the lastest invoice
        //const latestInvoicePaymentIntentStatus = localStorage.getItem(
        //  'latestInvoicePaymentIntentStatus'
        //);
  
       // if (latestInvoicePaymentIntentStatus === 'requires_payment_method') {
       //   const invoiceId = localStorage.getItem('latestInvoiceId');
       //   const isPaymentRetry = true;
          // create new payment method & retry payment on invoice with new payment method
       //   createPaymentMethod({
       //     card,
       //     isPaymentRetry,
       //     invoiceId,
       //   });
        //} else {
          // create new payment method & create subscription
          const params = new URLSearchParams(window.location.search);
          const customerId = params.get('customerId');
          const priceId = params.get('priceId');
          createPaymentMethod( card, customerId, priceId);
        //}
      });
    }      
    
    function displayError(event) 
    {
      document.getElementById("loadid").style.display = "none";
      document.getElementById("spaceid").style.display = "flex";
      //changeLoadingStatePrices(false);
      let displayError = document.getElementById('card-element-errors');
      if (event) {
        displayError.textContent = event.message;
      } else {
        displayError.textContent = '';
      }
    }
    
    function createPaymentMethod(cardElement, customerId, priceId) 
    {
      return stripe
        .createPaymentMethod({
          type: 'card',
          card: cardElement,
        })
        .then((result) => {
          if (result.error) {
            displayError(result.error);
          } else {
            createSubscription(
              customerId,
              result.paymentMethod.id,
              priceId
            );
          }
        });
    }
    
    function handlePaymentThatRequiresCustomerAction({
      subscription,
      invoice,
      priceId,
      paymentMethodId
    })
    {
      let setupIntent = subscription.pending_setup_intent;
    
      if (setupIntent && setupIntent.status === 'requires_action')
      {
        return stripe
          .confirmCardSetup(setupIntent.client_secret, {
            payment_method: paymentMethodId,
          })
          .then((result) => {
            if (result.error) {
              // start code flow to handle updating the payment details
              // Display error message in your UI.
              // The card was declined (i.e. insufficient funds, card has expired, etc)
              throw result;
            } else {
              if (result.setupIntent.status === 'succeeded') {
                // There's a risk of the customer closing the window before callback
                // execution. To handle this case, set up a webhook endpoint and
                // listen to setup_intent.succeeded.
                return {
                  priceId: priceId,
                  subscription: subscription,
                  invoice: invoice,
                  paymentMethodId: paymentMethodId,
                };
              }
            }
          });
      }
      else {
        // No customer action needed
        return { subscription, priceId, paymentMethodId };
      }
    }
    
    function createSubscription(customerId, paymentMethodId, priceId) {
    return (
      fetch('abo.php', {
        method: 'post',
        headers: {
          'Content-type': 'application/json',
        },
        body: JSON.stringify({
          action: "boconsocreationabonnement",
          customerId: customerId,
          paymentMethodId: paymentMethodId,
          priceId: priceId,
        }),
      })
        .then((response) => {
          return response.json();
        })
        // If the card is declined, display an error to the user.
        .then((result) => {
          if (result.error) {
            // The card had an error when trying to attach it to a customer.
            throw result;
          }
          return result;
        })
        // Normalize the result to contain the object returned by Stripe.
        // Add the additional details we need.
        .then((result) => {
          document.write('<p>Congratulations! Nous vous redirigeons vers votre compte.</p>');
          window.location.href = 'admin.php';
          return {
            paymentMethodId: paymentMethodId,
            priceId: priceId,
            subscription: result,
          };
        })
        // Some payment methods require a customer to be on session
        // to complete the payment process. Check the status of the
        // payment intent to handle these actions.
        //.then(handlePaymentThatRequiresCustomerAction)
        .then(handlePaymentThatRequiresCustomerAction)
        // If attaching this card to a Customer object succeeds,
        // but attempts to charge the customer fail, you
        // get a requires_payment_method error.
        //.then(handleRequiresPaymentMethod)
        // No more actions required. Provision your service for the user.
        //.then(onSubscriptionComplete)
        // No more actions required. Provision your service for the user.
        //.then(onSubscriptionComplete)
        .catch((error) => {
          // An error has happened. Display the failure to the user here.
          // We utilize the HTML element we created.
          displayError(error);
        })
    );
  }
  function cancel() 
  {
    window.location.href = './boprices.php';
  }
  </script>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter l'espace client de la boutic ?") == true)
      {
        window.location ='https://pratic-boutic.fr';
      }
    }
  </script>  
  
</html>
 
