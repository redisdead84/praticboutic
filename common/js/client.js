var bouticid;

async function getParam(bouticid, param, defval = null)
{
  var objparam = { action: "getparam", table: "parametre", bouticid: bouticid, param: param};
  const response = await fetch('customerarea/boquery.php', {
    method: "POST",
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body:JSON.stringify(objparam)
  });
  if (!response.ok) {
    throw new Error(`Error! status: ${response.status}`);
  }
  const data = await response.json();
  if (data[0] == null)
    return defval;
  return data[0];
}

async function getBouticInfo(customer)
{
  var objboutic = { requete: "getBouticInfo", customer: customer};
  const response = await fetch('frontquery.php', {
    method: "POST",
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body:JSON.stringify(objboutic)
  });
  if (!response.ok) {
    throw new Error(`Error! status: ${response.status}`);
  }
  const data = await response.json();
  bouticid = data[0];
  logo = data[1];
  nom = data[2];
}


(async function () {
  if ((sessionStorage.getItem("method")==3) && (sessionStorage.getItem("choice")=="COMPTANT")) 
  {
    customer = sessionStorage.getItem('customer');
    var pkey = document.getElementById("scriptclientid").getAttribute("data-pkey"); 
    await getBouticInfo(customer);
    var caid = await getParam(bouticid, "STRIPE_ACCOUNT_ID");
    
    // A reference to Stripe.js initialized with your real test publishable API key.
    var stripe = Stripe(pkey, {
      stripeAccount: caid,
    });
    
    var obj = JSON.parse(sessionStorage.getItem("commande"));
    var customer = sessionStorage.getItem("customer");
    var choicel = sessionStorage.getItem("choicel");
    var coutlivr = sessionStorage.getItem("fraislivr");
    var codepromo = sessionStorage.getItem("codepromo");
    
    // The items the customer wants to buy
    var purchase = {
      items: obj,
      boutic: customer,
      model: choicel,
      fraislivr: coutlivr,
      codepromo: codepromo
    };
    
    // Disable the button until we have Stripe set up on the page
    document.querySelector("button").disabled = true;
  
    fetch("create.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(purchase)
    })
      .then(function(result) {
        return result.json();
      })
      .then(function(data) {
        var elements = stripe.elements({
  				fonts: [
  					{
  						cssSrc: 'https://fonts.googleapis.com/css?family=Public+Sans'
  					}
  				],
  			});
    
        var style = {
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
    
        var card = elements.create("card", {hidePostalCode: true, style: style });
        // Stripe injects an iframe into the DOM
        card.mount("#card-element");
    
        card.on("change", function (event) {
          // Disable the Pay button if there are no card details in the Element
          document.querySelector("button").disabled = event.empty;
          document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
        });
    
        var form = document.getElementById("payment-form");
        form.addEventListener("submit", function(event) {
          event.preventDefault();
          // Complete payment when the submit button is clicked
          payWithCard(stripe, card, data.clientSecret);
        });
      });
    
    // Calls stripe.confirmCardPayment
    // If the card requires authentication Stripe shows a pop-up modal to
    // prompt the user to enter authentication details without leaving your page.
    var payWithCard = function(stripe, card, clientSecret) {
      loading(true);
      stripe
        .confirmCardPayment(clientSecret, {
          payment_method: {
            card: card
          }
        })
        .then(function(result) {
          if (result.error) {
            // Show error to your customer
            showError(result.error.message);
          } else {
            // The payment succeeded!
            orderComplete(result.paymentIntent.id);
          }
        });
    };
    
    /* ------- UI helpers ------- */
    
    // Shows a success message when the payment is complete
    var orderComplete = function(paymentIntentId) {
      loading(false);
      // Suppression du lien avec l'interace stripe de test
      /*document
        .querySelector(".result-message a")
        .setAttribute(
          "href",
          "https://dashboard.stripe.com/test/payments/" + paymentIntentId
        );*/
      document.querySelector(".result-message").classList.remove("hidden");
      document.querySelector("button").disabled = true;
      // Insert here code for process after card paiement
      
  /*    document.getElementById("backbutton").innerHTML = "Commander à nouveau";  
      reachBottom();*/  
  
      // Naviguer vers fin.php
      window.location.href = "fin.php?method=" + sessionStorage.getItem("method") + "&table=" + sessionStorage.getItem("table") + "&customer=" + sessionStorage.getItem("customer");
      
    };
    
    // Show the customer the error from Stripe if their card fails to charge
    var showError = function(errorMsgText) {
      loading(false);
      var errorMsg = document.querySelector("#card-error");
      errorMsg.textContent = errorMsgText;
      setTimeout(function() {
        errorMsg.textContent = "";
      }, 4000);
    };
    
    // Show a spinner on payment submission
    var loading = function(isLoading) {
      if (isLoading) {
        // Disable the button and show a spinner
        document.querySelector("button").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#button-text").classList.add("hidden");
      } else {
        document.querySelector("button").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#button-text").classList.remove("hidden");
      }
    };
  }
})();
