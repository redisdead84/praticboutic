<?php
  include "config/common_cfg.php";
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <title>Ecran de fin</title>
    <meta name="description" content="A demo of a card payment on Stripe" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" media="screen" href="css/style2.css?v=<?php echo $ver_com_css;?>" />
    <link rel="stylesheet" href="css/style.css?v=<?php echo $ver_com_css;?>" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="js/bandeau.js?v=2.01"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="js/mail.js?v=1.702"></script>
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
    var method;
    var mail;
    var table;
    var bouticid;
    var logo;
    var nom;
    var mnysys;

    async function getSession()
    {
      var objboutic = { requete: "getSession"};
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
      const data  = await response.json();
      customer = data[0];
      mail = data[1];
      method = data[2];
      table = data[3];
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
  </script>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="loadid" class="flcentered">
      <div class="spinner-border nospmd" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    <div id="header">
      <a href="https://pratic-boutic.fr"><img id="mainlogo" src="img/logo-pratic-boutic.png"></a>
    </div>
    <script type="text/javascript">
      window.onload = async function()
      {
        await getSession();
        if (!customer)
          document.location.href = 'error.php?code=nocustomer';
        await getBouticInfo(customer);
        if (!bouticid)
          document.location.href = 'error.php?code=nobouticid';
        if (!mail)
          document.location.href = 'error.php?code=noemail';
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
        document.getElementById("loadid").style.display = "none";
        reachBottom();
        document.getElementById("footer").style.visibility = "visible";
        document.getElementById("recommander").onclick = function () {
          window.location.href = 'index.php?customer=' + customer;
          sessionStorage.clear();
        };
      }
    </script>
    <div id="finmain">
      <img id="logo" style="display:none">
      <p id="marqueid" class="marque" style="display:none"></p>
      <div class="fsub">
        <p class="panneau acenter" id="envoieok">Votre commande a été envoyée.<br>Nous vous remercions pour votre fidelité.<br></p>
      </div>
    </div>
    <div id="footer" style="visibility:hidden;">
      <div class="solobn">
        <input id="recommander" class="soloindic" type="button" value="Passer une autre commande">
      </div>
    </div>
    <script type="text/javascript" >
      var close = 0; 
      if (sessionStorage.getItem("barre") == "close")
        close = 1;
      
      if (close == 1)
        sessionStorage.setItem("barre", "close");
    </script>
    <script type="text/javascript">
      function reachBottom() 
      {
        var x = window.innerHeight - document.getElementById("footer").clientHeight - document.getElementById("header").clientHeight;
        x = x + "px";
        document.getElementById("finmain").style.height = x;
      }
    </script>
    <script type="text/javascript">
      window.addEventListener("resize", function() {
        reachBottom();
      })
    </script>
  </body>
</html>
