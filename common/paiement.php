<?php

  require "../vendor/autoload.php";
  include "config/common_cfg.php";
  include "param.php";

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  $pkey = $_ENV['STRIPE_PUBLISHABLE_KEY'];
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <title>Validation de la commande</title>
    <meta name="description" content="PraticBoutic formulaire de paiement" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" media="screen" href="css/style2.css?v=<?php echo $ver_com_css;?>" />
    <link rel="stylesheet" href="css/global.css?v=<?php echo $ver_com_css;?>" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="js/bandeau.js?v=2.01"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <!--<script id="scriptclientid" src="js/client.js?v=1.274" data-pkey="<?php echo $pkey;?>" defer></script>-->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
  </head>
  <script type="text/javascript" >
    var customer;
    var bouticid;
    var logo;
    var nom;
    var mnysys;

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
  </script>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="loadid" class="flcentered">
      <div class="spinner-border nospmd" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    <div id="header">
      <img id="mainlogo" src="img/logo-pratic-boutic.png">
    </div>
    <div id="main">
      <img id="logo" style="display:none">
      <p id="marqueid" class="marque" style="display:none"></p>
      <div id="pan">
        <div id="methodid"></div>
        <div id="tableid"></div>
        <div id="commandediv"></div>
        <div class="fraistotal" id="sstotalid"></div>
        <div class="fraistotal" id="remiseid"></div>
        <div class="fraistotal" id="fraislivid"></div>
        <div class="fraistotal mbot" id="totalid"></div>
        <div class="fpay" id="payid"></div>
        <br>
      </div>
    </div>
    <script type="text/javascript">
      window.onload = async function()
      {
        customer = sessionStorage.getItem('customer');
        method = sessionStorage.getItem('method');
        if (!customer)
          document.location.href = '404.html';
        mail = sessionStorage.getItem(customer + '_mail');
        await getBouticInfo(customer);
        if (!bouticid)
          document.location.href = '404.html';
        if (!mail)
          document.location.href = '404.html';
        if (mail == 'oui')
          document.location.href = '404.html';
        document.getElementById("logo").src = "../upload/" + logo;
        document.getElementById("marqueid").innerHTML = nom;
        if (logo)
        {
          document.getElementById("logo").style.display = "block";
          document.getElementById("marqueid").style.display = "none";
        }
        else 
        {
          document.getElementById("logo").style.display = "none";
          document.getElementById("marqueid").style.display = "block";
        }

        mnysys = await getParam(bouticid, "MONEY_SYSTEM", "STRIPE MARKETPLACE");

        var payfoot = document.createElement("DIV");
        payfoot.id = "payementfooter";
        payfoot.style.height = '225px';
        if ((sessionStorage.getItem("method")==3) && (sessionStorage.getItem("choice")=="COMPTANT")) {
          if (mnysys == "STRIPE MARKETPLACE")
          {
            var payform = document.createElement("FORM");
            payform.classList.add("frm");
            payform.id = "payment-form";
            var ce = document.createElement("DIV");
            ce.id = "card-element";
            payform.appendChild(ce);
            var subbtn = document.createElement("BUTTON");
            subbtn.classList.add("btnstripe");
            subbtn.id = "submit";
            var spin = document.createElement("DIV");
            spin.classList.add("spinner");
            spin.classList.add("hidden");
            spin.id = "spinner";
            subbtn.appendChild(spin);
            var btntxt = document.createElement("SPAN");
            btntxt.id = "button-text";
            btntxt.innerHTML = "Payer";
            subbtn.appendChild(btntxt);
            payform.appendChild(subbtn);
            var ic = document.createElement("DIV");
            ic.classList.add("intercalaire");
            var cderr = document.createElement("P");
            cderr.id = "card-error";
            cderr.role = "alert";
            ic.appendChild(cderr);
            var cmh = document.createElement("P");
            cmh.classList.add("result-message");
            cmh.classList.add("hidden");
            cmh.innerHTML = 'Paiement effectué<!--, Voyez le résultat dans votre<a href="" target="_blank">interface Stripe.</a> Rafraichisser la page pour payer encore-->.';
            ic.appendChild(cmh);
            payform.appendChild(ic);
            document.body.appendChild(payform);
          }
          var sbn = document.createElement("DIV");
          sbn.classList.add("solobn");
          var retct = document.createElement("BUTTON");
          retct.classList.add("navindicsolo");
          retct.id = "retourcarte";
          retct.onclick = function() {
            window.location.href = 'getinfo.php';
          }
          retct.innerHTML = "Revenir sur les informations";
          sbn.appendChild(retct);
          payfoot.appendChild(sbn);
          document.body.appendChild(payfoot);
        } else {
          var ft = document.createElement("DIV");
          ft.id = "footer";
          var gbtn = document.createElement("DIV");
          gbtn.classList.add("grpbn");
          var retct = document.createElement("BUTTON");
          retct.classList.add("navindicsolo");
          retct.id = "retourcarte";
          retct.onclick = function() {
            window.location.href = 'getinfo.php';
          }
          retct.innerHTML = "Revenir sur les informations";
          gbtn.appendChild(retct);
          var valct = document.createElement("BUTTON");
          valct.classList.add("navindicsolo");
          valct.id = "validcarte";
          valct.onclick = function() {
            window.location.href = 'fin.php';
          }
          valct.innerHTML = "Valider";
          gbtn.appendChild(valct);
          ft.appendChild(gbtn);
          document.body.appendChild(ft);
        }
        displaycmd();
        reachBottom();
        
        if ((sessionStorage.getItem("method")==3) && (sessionStorage.getItem("choice")=="COMPTANT")) 
        {
          customer = sessionStorage.getItem('customer');
          var pkey = "<?php echo $pkey;?>";
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
        document.getElementById("loadid").style.display = "none";
      }

      function displaycmd()
      {
        var cart = JSON.parse(sessionStorage.getItem("commande"));
        var str = "";
        var somme = 0;
        var res = document.createElement("P");
        res.classList.add("pres");
        res.innerHTML = "Résumé de votre commande";
        var tbl = document.createElement("TABLE");
        var clgrp = document.createElement("COLGROUP");
        var colart = document.createElement("COL");
        colart.classList.add("colart");
        clgrp.appendChild(colart);
        var colstd1 = document.createElement("COL");
        colstd1.classList.add("colstd");
        clgrp.appendChild(colstd1);
        var colstd2 = document.createElement("COL");
        colstd2.classList.add("colstd");
        clgrp.appendChild(colstd2);
        var colprx = document.createElement("COL");
        colprx.classList.add("colprx");
        clgrp.appendChild(colprx);
        tbl.appendChild(clgrp);
        var tr = document.createElement("TR");
        var thcolart1 = document.createElement("TH");
        thcolart1.classList.add("colart");
        thcolart1.innerHTML = "Article";
        tr.appendChild(thcolart1);
        var thcolstd1 = document.createElement("TH");
        thcolstd1.classList.add("colstd");
        thcolstd1.innerHTML = "Prix";
        tr.appendChild(thcolstd1);
        var thcolstd2 = document.createElement("TH");
        thcolstd2.classList.add("colstd");
        thcolstd2.innerHTML = "Qté";
        tr.appendChild(thcolstd2);
        var thcolprx = document.createElement("TH");
        thcolprx.classList.add("colprx");
        thcolprx.innerHTML = "Total";
        tr.appendChild(thcolprx);
        tbl.appendChild(tr);
        for (var art in cart) {
          var tr = document.createElement("TR");
          var tdcolart1 = document.createElement("TD");
          tdcolart1.classList.add("colart");
          tdcolart1.innerHTML = cart[art].name;
          tr.appendChild(tdcolart1);
          var tdcolstd1 = document.createElement("TD");
          tdcolstd1.classList.add("colstd");
          tdcolstd1.innerHTML = (parseFloat(cart[art].prix)).toFixed(2) + " € ";
          tr.appendChild(tdcolstd1);
          var tdcolstd2 = document.createElement("TD");
          tdcolstd2.classList.add("colstd");
          tdcolstd2.innerHTML = cart[art].qt;
          tr.appendChild(tdcolstd2);
          var tdcolprx = document.createElement("TD");
          tdcolprx.classList.add("colprx");
          tdcolprx.innerHTML = (cart[art].qt * cart[art].prix).toFixed(2) + " € ";
          somme = somme + cart[art].qt * cart[art].prix;
          tr.appendChild(tdcolprx);
          tbl.appendChild(tr);
        }
        var method = sessionStorage.getItem("method");
        var method_txt = "";
        if (method == 2)
        {
          method_txt = "Consomation sur place";
          var pres = document.createElement("P");
          pres.classList.add("pres");
          pres.innerHTML = method_txt;
          document.getElementById("methodid").appendChild(pres);
        } 
        if (method == 2) 
        {
          var pres = document.createElement("P");
          pres.classList.add("pres");
          pres.innerHTML = "Table numéro " + sessionStorage.getItem("table");
          document.getElementById("tableid").appendChild(pres);
        }
        document.getElementById("commandediv").appendChild(res);
        document.getElementById("commandediv").appendChild(tbl);
        var remise;
        if (sessionStorage.getItem("remise") == null)
        {
          remise = 0;
        }
        else
        {
          remise = parseFloat(sessionStorage.getItem("remise"));
        }
        if (remise == 0)
          document.getElementById("remiseid").style.display = "none";
        var frliv = 0;
        if (method > 2) 
          frliv = parseFloat(sessionStorage.getItem("fraislivr"));
        var tota = frliv + somme - remise;
        if ((sessionStorage.getItem("choicel") == "LIVRER") && (method > 2))
        {
          var sstp1 = document.createElement("P");
          sstp1.classList.add("fleft");
          sstp1.innerHTML = "Sous-total : ";
          document.getElementById("sstotalid").appendChild(sstp1);
          var sstp2 = document.createElement("P");
          sstp2.classList.add("fright");
          sstp2.innerHTML = somme.toFixed(2) + " € ";
          document.getElementById("sstotalid").appendChild(sstp2);
          document.getElementById("sstotalid").appendChild(document.createElement("BR"));
          if (remise > 0)
          {
            var sstp1 = document.createElement("P");
            sstp1.classList.add("fleft");
            sstp1.innerHTML = "Sous-total : ";
            document.getElementById("remiseid").appendChild(sstp1);
            var sstp2 = document.createElement("P");
            sstp2.classList.add("fright");
            sstp2.innerHTML = (-remise).toFixed(2) + " € ";
            document.getElementById("remiseid").appendChild(sstp2);
            document.getElementById("remiseid").appendChild(document.createElement("BR"));
          }
          var sstp1 = document.createElement("P");
          sstp1.classList.add("fleft");
          sstp1.innerHTML = "Frais de livraison : ";
          document.getElementById("fraislivid").appendChild(sstp1);
          var sstp2 = document.createElement("P");
          sstp2.classList.add("fright");
          sstp2.innerHTML = frliv.toFixed(2) + " € ";
          document.getElementById("fraislivid").appendChild(sstp2);
          document.getElementById("fraislivid").appendChild(document.createElement("BR"));
          var sstp1 = document.createElement("P");
          sstp1.classList.add("wbld");
          sstp1.classList.add("fleft");
          sstp1.innerHTML = "Frais de livraison : ";
          document.getElementById("totalid").appendChild(sstp1);
          var sstp2 = document.createElement("P");
          sstp2.classList.add("wbld");
          sstp2.classList.add("fright");
          sstp2.innerHTML = tota.toFixed(2) + " € ";
          document.getElementById("totalid").appendChild(sstp2);
          document.getElementById("totalid").appendChild(document.createElement("BR"));
        }
        else if ((sessionStorage.getItem("choicel") == "EMPORTER") || (method == 2))
        {
          document.getElementById("sstotalid").style.display = "none";
          var sstp1 = document.createElement("P");
          sstp1.classList.add("fleft");
          sstp1.innerHTML = "Sous-total : ";
          document.getElementById("remiseid").appendChild(sstp1);
          var sstp2 = document.createElement("P");
          sstp2.classList.add("fright");
          sstp2.innerHTML = (-remise).toFixed(2) + " € ";
          document.getElementById("remiseid").appendChild(sstp2);
          document.getElementById("remiseid").appendChild(document.createElement("BR"));
          document.getElementById("fraislivid").style.display = "none";
          var sstp1 = document.createElement("P");
          sstp1.classList.add("fleft");
          sstp1.innerHTML = "Frais de livraison : ";
          document.getElementById("fraislivid").appendChild(sstp1);
          var sstp2 = document.createElement("P");
          sstp2.classList.add("fright");
          sstp2.innerHTML = frliv.toFixed(2) + " € ";
          document.getElementById("fraislivid").appendChild(sstp2);
          document.getElementById("fraislivid").appendChild(document.createElement("BR"));
          var sstp1 = document.createElement("P");
          sstp1.classList.add("wbld");
          sstp1.classList.add("fleft");
          sstp1.innerHTML = "Frais de livraison : ";
          document.getElementById("totalid").appendChild(sstp1);
          var sstp2 = document.createElement("P");
          sstp2.classList.add("wbld");
          sstp2.classList.add("fright");
          sstp2.innerHTML = (somme-remise).toFixed(2) + " € ";
          document.getElementById("totalid").appendChild(sstp2);
          document.getElementById("totalid").appendChild(document.createElement("BR"));
        }
        if ((sessionStorage.getItem("method")>2) && (sessionStorage.getItem("choice")=="COMPTANT"))
        {
          var pay = document.createElement("P");
          pay.classList.add("mntpay");
          pay.innerHTML = "MONTANT &Agrave; PAYER : " + tota.toFixed(2) + " € ";
          document.getElementById("payid").appendChild(pay);
        }
        else 
        {
          document.getElementById("payid").style.display = "none";
        }
      }

      function reachBottom() 
      {
        var x;
        if ((sessionStorage.getItem("method")==3) && (sessionStorage.getItem("choice")=="COMPTANT"))
          x = window.innerHeight - document.getElementById("payementfooter").clientHeight - document.getElementById("header").clientHeight;
        else
          x = window.innerHeight - document.getElementById("footer").clientHeight - document.getElementById("header").clientHeight;
        x = x + "px";
        document.getElementById("main").style.height = x;
      }

      window.addEventListener("resize", function() {
        reachBottom();
      })
    </script>
  </body>
</html>
