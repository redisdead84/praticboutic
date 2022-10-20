<?php
  session_start();

  if (empty($_SESSION['verify_email']) == TRUE)
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
    <link rel="stylesheet" href="css/back.css?v=1.705">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Souscription</title>
    <script src="https://js.stripe.com/v3/"></script>
  </head>
  <body class="custombody" ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()" class='epure'/>
      <div id="workspace" class="spaceflex">
        <div id="loadid" class="spinner-border nospmd" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div id="pagecontainerid" class="pagecontainer" style="display: none;">
          <img id='filetape5' src="img/fil_Page_6.png" class="fileelem" />
          <div class="filecontainer">
            <div id="spaceid" class="spaceflex">
              <img id='illus8' src='img/illustration_8.png' class='epure'/>
              <div class="customform tiersspacemax">
                <p class="center middle title">
                  On touche au but ! JE SAISIE MES INFORMATIONS DE PAIEMENT
                </p>
                <form id="subscribe-form">
                  <div class="stripeelem" id="card-element">
                    <!-- Elements will create input elements here -->
                  </div>
                  <input type="text" id="name" class="paramfieldc enlarged" value="" placeholder="Nom complet" />
                  <br />
                  <div class="ifgrpbtn">
                    <input class="btn-ifsecondary" type="button" onclick="javascript:cancel()" value="RETOUR" />
                    <button class="btn-ifprimary" type="submit">C'EST PARTI</button>
                  </div>
                </form>
                <div id="messages"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()" class='epure'/>
    </div>
  </body>
  <script type="text/javascript" >
    document.getElementById("loadid").style.display = "none";
    document.getElementById("pagecontainerid").style.display = "flex";
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
  						cssSrc: 'https://fonts.googleapis.com/css?family=Montserrat:wght@400;500'
  					}
  				],
  			});
        var style = {
        hidePostalCode: true,
        base: {
          color: "#444444",
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
      document.getElementById("loadid").style.display = "flex";
      document.getElementById("pagecontainerid").style.display = "none";
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
          document.getElementById("loadid").style.display = "none";
          document.getElementById("pagecontainerid").style.display = "flex";
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
      document.getElementById("loadid").style.display = "block";
      document.getElementById("pagecontainerid").style.display = "none";
      window.location.href = './prices.php';
    }
  </script>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter ?") == true)
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("pagecontainerid").style.display = "none";
        window.location.href ='exit.php';
      }
    }
  </script>
</html>
