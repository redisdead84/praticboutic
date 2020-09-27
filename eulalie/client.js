
var pkey = document.getElementById("main").getAttribute("data-publickey");

// public key test
// "pk_test_51H8fNKHGzhgYgqhxjTrk1jhYn1AyPAZvKUjjTXOAcOZ3AS8wXYAHbTgq6kS2tVw8bg57KNmJTUEJ6jciNwwc5KbX00ovJ3hmZD"

// A reference to Stripe.js initialized with your real test publishable API key.
var stripe = Stripe(pkey);

var obj = JSON.parse(localStorage.getItem("commande"));

// The items the customer wants to buy
var purchase = {
  items: obj
};

// Recuperer la somme de la commande
// Si inferieur à 15 €
// Ne pas continuer

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
    var elements = stripe.elements();

    var style = {
      base: {
        color: "#32325d",
        fontFamily: 'Arial, sans-serif',
        fontSmoothing: "antialiased",
        fontSize: "16px",
        "::placeholder": {
          color: "#32325d"
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
  
  document.getElementById("backbutton").innerHTML = "Commander à nouveau";  
  reachBottom();  
  
  var commande = {
    method: localStorage.getItem("method"),
    table: localStorage.getItem("table"),
    nom: localStorage.getItem("nom"),
    prenom: localStorage.getItem("prenom"),
    adresse1: localStorage.getItem("adresse1"),
    adresse2: localStorage.getItem("adresse2"),
    codepostal: localStorage.getItem("codepostal"),
    ville: localStorage.getItem("ville"),
    telephone: localStorage.getItem("telephone"),
    items: JSON.parse(localStorage.getItem("commande"))
  };
  
  fetch("mailj.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(commande)
  })
    .then(function(result) {
      return result.text();
    })  
  
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
