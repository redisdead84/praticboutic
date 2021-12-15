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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.03">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Tarifs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body class="custombody">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spaceflex">
        <main class="fcb">
          <div class="customform">
            <p class="center middle title">
              Choississez la formule d'abonnement
            </p>
            <div class="formulespace">
              <img id="commissionico" class="formuleico" src="img/commission_unselected.png" onclick="toggle(this)" data-state="off">
              <img id="engagementico" class="formuleico" src="img/engagement_unselected.png" onclick="toggle(this)" data-state="off">
            </div>
            <div class="param rwc">
              <input type="checkbox" id="cgvid" name="cgv" value="on" onclick="allow()" />
              <label for="cgv"> En cochant cette case vous accpetez <a href="javascript:bakinfo();window.location='bocgv.php'">les conditions générales de vente</a></label>
            </div>
            <div class="param rwc grpbtnfor">
              <input class="butc btn-cfsecondary" type="button" id="cfannul" onclick="javascript:cancel()" value="ANNULATION" />
              <input class="butc btn-cfprimary" type="button" id="cfvalid" value="CONFIRMATION" autofocus disabled style="opacity: 0.5" data-ready="2"/>
            </div>
          </div>
        </main>
        <img id='illus7' src='img/illustration_7.png' />
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript" >
    var linkfixe;
    var linkconso;

    function bakinfo()
    {
      sessionStorage.setItem('pb_bochxfor_engagement', document.getElementById("engagementico").getAttribute("data-state"));
      sessionStorage.setItem('pb_bochxfor_commission', document.getElementById("commissionico").getAttribute("data-state"));
      sessionStorage.setItem('pb_bochxfor_cgv', document.getElementById("cgvid").checked);
    }
    window.onload=function()
    {
      document.getElementById("cfvalid").disabled = true;
      document.getElementById("cfvalid").style = "opacity: 0.5";
      changelink();
      if (sessionStorage.getItem('pb_bochxfor_engagement') !== null)
        document.getElementById("engagementico").setAttribute("data-state", sessionStorage.getItem('pb_bochxfor_engagement'));
      if (sessionStorage.getItem('pb_bochxfor_commission') !== null)
        document.getElementById("commissionico").setAttribute("data-state", sessionStorage.getItem('pb_bochxfor_commission'));
      document.getElementById("cgvid").checked = (sessionStorage.getItem('pb_bochxfor_cgv') === 'true');
      if (document.getElementById("engagementico").getAttribute("data-state") == "on")
        document.getElementById("engagementico").src = "img/engagement_selected.png";
      else 
        document.getElementById("engagementico").src = "img/engagement_unselected.png";
      if (document.getElementById("commissionico").getAttribute("data-state") == "on")
        document.getElementById("commissionico").src = "img/commission_selected.png";
      else 
        document.getElementById("commissionico").src = "img/commission_unselected.png";
      allow();
    }
  </script>
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
        document.getElementById("cfvalid").setAttribute("data-linkfixe", params.toString() );
        document.getElementById("cfvalid").setAttribute("data-ready", String(parseInt(document.getElementById("cfvalid").getAttribute("data-ready")) - 1) );
        allow();
      }
    })
  }
  </script>
  <script type="text/javascript" >
    function conso(priceId) 
    {
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
          const params = new URLSearchParams(window.location.search);
          params.append('customerId', data.customerId);
          params.append('priceId', data.priceId);
          document.getElementById("cfvalid").setAttribute("data-linkconso", params.toString() );
          document.getElementById("cfvalid").setAttribute("data-ready", String(parseInt(document.getElementById("cfvalid").getAttribute("data-ready")) - 1) );
          allow();
        }
      })
    }
  </script>
  <script type="text/javascript" >
    function changelink()
    {
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
          if(!data.prices) 
          {
            document.getElementById("engagementico").style = "opacity : 0.5";
            document.getElementById("engagementico").onclick = "";
            document.getElementById("commissionico").style = "opacity : 0.5";
            document.getElementById("commissionico").onclick = "";
          }
          else
          {
            data.prices.forEach((price) => {
              if (price.lookup_key == "pb_fixe")
                createSubscription(price.id);
              else if (price.lookup_key == "pb_conso")
                conso(price.id);
            });
          }
        }
      })
    }
    function cancel() 
    {
      bakinfo();
      window.location.href = './account.php';
    }
    
    function allow() 
    {
      if ((document.getElementById("cgvid").checked == true) && (document.getElementById("cfvalid").getAttribute("data-ready") == "0"))
      {
        if (document.getElementById("engagementico").getAttribute("data-state") == "on")
        {
          document.getElementById("cfvalid").disabled = false;
          document.getElementById("cfvalid").style = "opacity: 1";
          document.getElementById("cfvalid").onclick = function()
          {
            bakinfo();
            window.location.href = 'bosubscribe.php?' + document.getElementById("cfvalid").getAttribute("data-linkfixe");
          };
        }
        else if (document.getElementById("commissionico").getAttribute("data-state") == "on")
        {
          document.getElementById("cfvalid").disabled = false;
          document.getElementById("cfvalid").style = "opacity: 1";
          document.getElementById("cfvalid").onclick = function()
          {
            bakinfo();
            window.location.href = 'boconso.php?' + document.getElementById("cfvalid").getAttribute("data-linkconso");
          };
        }
        else 
        {
          document.getElementById("cfvalid").disabled = true;
          document.getElementById("cfvalid").style = "opacity: 0.5";
        }
      }
      else 
      {
        document.getElementById("cfvalid").disabled = true;
        document.getElementById("cfvalid").style = "opacity: 0.5";
      }
    }
    
  </script>
  <script type="text/javascript" >
    function toggle(elem)
    {
      if (elem.id == "commissionico")
      {
        if (elem.getAttribute("data-state") == "off")
        {
          document.getElementById("engagementico").setAttribute("data-state", "off");
          document.getElementById("engagementico").src = "img/engagement_unselected.png";
          elem.setAttribute("data-state", "on");
          elem.src = "img/commission_selected.png";
        }
        else if (elem.getAttribute("data-state") == "on")
        {
          elem.setAttribute("data-state", "off");
          elem.src = "img/commission_unselected.png";
        }
      }
      else if (elem.id == "engagementico")
      {
        if (elem.getAttribute("data-state") == "off")
        {
          document.getElementById("commissionico").setAttribute("data-state", "off");
          document.getElementById("commissionico").src = "img/commission_unselected.png";
          elem.setAttribute("data-state", "on");
          elem.src = "img/engagement_selected.png";
        }
        else if (elem.getAttribute("data-state") == "on")
        {
          elem.setAttribute("data-state", "off");
          elem.src = "img/engagement_unselected.png";
        }
      }
      allow();
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
