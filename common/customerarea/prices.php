<?php
  session_start();

  if (empty($_SESSION['verify_email']) == TRUE)
  {
    header("LOCATION: index.php");
    exit();
  }

  require_once '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";
  
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  
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
    <link rel="stylesheet" href="css/back.css?v=1.704">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Tarifs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body class="custombody" ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()" class='epure'/>
      <div id="workspace" class="spaceflex" data-ready="2">
        <div id="loadid" class="spinner-border nospmd" role="status" style="display: block;">
          <span class="sr-only">Loading...</span>
        </div>
        <div id="pagecontainerid" class="pagecontainer" style="display: none;">
          <img id='filetape5' src="img/fil_Page_5.png" class="fileelem" />
          <div class="filecontainer">
            <main id="mainid" class="fcb">
              <div id="cfid" class="customform">
                <p class="center middle title">
                  Je choisis ma formule d'abonnement
                </p>
                <div class="formulespace">
                  <img id="commissionico" class="formuleico" src="img/commission_unselected.png?v=0.2" onclick="toggle(this)" data-state="off">
                  <img id="engagementico" class="formuleico" src="img/engagement_unselected.png?v=0.2" onclick="toggle(this)" data-state="off">
                </div>
                <div class="param rwc centerable">
                  <input type="checkbox" id="cgvid" name="cgv" value="on" onclick="allow()" />
                  <label for="cgv">J'accepte <a href="javascript:bakinfo();window.location='cgv.php'">les conditions générales de vente</a></label>
                </div>
                <div class="param rwc">
                  <input class="regsubmit" type="button" id="cfvalid" type="button" name="JEVALIDE" value="JE VALIDE !" autofocus disabled style="opacity: 0.5" />
                </div>
                <p class="changeable">Les informations pourront être modifié par la suite à partir de votre espace</p>
              </div>
            </main>
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()" class='epure'/>
    </div>
  </body>
  <script type="text/javascript" >
    var linkfixe;
    var linkconso;

    function bakinfo()
    {
      document.getElementById("loadid").style.display = "block";
      document.getElementById("pagecontainerid").style.display = "none";
      sessionStorage.setItem('pb_chxfor_engagement', document.getElementById("engagementico").getAttribute("data-state"));
      sessionStorage.setItem('pb_chxfor_commission', document.getElementById("commissionico").getAttribute("data-state"));
    }
    window.onload=function()
    {
      document.getElementById("cfvalid").disabled = true;
      document.getElementById("cfvalid").style = "opacity: 0.5";
      changelink();
      if (sessionStorage.getItem('pb_chxfor_engagement') !== null)
        document.getElementById("engagementico").setAttribute("data-state", sessionStorage.getItem('pb_chxfor_engagement'));
      if (sessionStorage.getItem('pb_chxfor_commission') !== null)
        document.getElementById("commissionico").setAttribute("data-state", sessionStorage.getItem('pb_chxfor_commission'));
      if (document.getElementById("engagementico").getAttribute("data-state") == "on")
        document.getElementById("engagementico").src = "img/engagement_selected.png?v=0.2";
      else 
        document.getElementById("engagementico").src = "img/engagement_unselected.png?v=0.2";
      if (document.getElementById("commissionico").getAttribute("data-state") == "on")
        document.getElementById("commissionico").src = "img/commission_selected.png?v=0.2";
      else 
        document.getElementById("commissionico").src = "img/commission_unselected.png?v=0.2";
      allow();
    }
  </script>
  <script type="text/javascript" >
    const createSubscription = (priceId) => {
    const params = new URLSearchParams(window.location.search);
    const customerId = params.get('customerId');
    var obj = { action: "creationabonnement", login: <?php echo '"' . $_SESSION['verify_email'] . '"'; ?>, priceid:priceId};
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
        document.getElementById("workspace").setAttribute("data-ready", String(parseInt(document.getElementById("workspace").getAttribute("data-ready")) - 1) );
        allow();
      }
    })
  }
  </script>
  <script type="text/javascript" >
    function conso(priceId) 
    {
      var obj = { action: "conso", login: <?php echo '"' . $_SESSION['verify_email'] . '"'; ?>, priceid:priceId};
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
          document.getElementById("workspace").setAttribute("data-ready", String(parseInt(document.getElementById("workspace").getAttribute("data-ready")) - 1) );
          allow();
        }
      })
    }
  </script>
  <script type="text/javascript" >
    function changelink()
    {
      var obj = { action: "configuration", login: <?php echo '"' . $_SESSION['verify_email'] . '"'; ?>};

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
      window.location.href = './paramboutic.php';
    }
    
    function allow() 
    {
      if (document.getElementById("workspace").getAttribute("data-ready") == "0")
      {
        document.getElementById("loadid").style.display = "none";
        document.getElementById("pagecontainerid").style.display = "flex";
        if (document.getElementById("cgvid").checked == true)
        {
          if (document.getElementById("engagementico").getAttribute("data-state") == "on")
          {
            document.getElementById("cfvalid").disabled = false;
            document.getElementById("cfvalid").style = "opacity: 1";
            document.getElementById("cfvalid").onclick = function()
            {
              bakinfo();
              window.location.href = 'subscribe.php?' + document.getElementById("cfvalid").getAttribute("data-linkfixe");
            };
          }
          else if (document.getElementById("commissionico").getAttribute("data-state") == "on")
          {
            document.getElementById("cfvalid").disabled = false;
            document.getElementById("cfvalid").style = "opacity: 1";
            document.getElementById("cfvalid").onclick = function()
            {
              bakinfo();
              window.location.href = 'conso.php?' + document.getElementById("cfvalid").getAttribute("data-linkconso");
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
          document.getElementById("engagementico").src = "img/engagement_unselected.png?v=0.2";
          elem.setAttribute("data-state", "on");
          elem.src = "img/commission_selected.png?v=0.2";
        }
        else if (elem.getAttribute("data-state") == "on")
        {
          elem.setAttribute("data-state", "off");
          elem.src = "img/commission_unselected.png?v=0.2";
        }
      }
      else if (elem.id == "engagementico")
      {
        if (elem.getAttribute("data-state") == "off")
        {
          document.getElementById("commissionico").setAttribute("data-state", "off");
          document.getElementById("commissionico").src = "img/commission_unselected.png?v=0.2";
          elem.setAttribute("data-state", "on");
          elem.src = "img/engagement_selected.png?v=0.2";
        }
        else if (elem.getAttribute("data-state") == "on")
        {
          elem.setAttribute("data-state", "off");
          elem.src = "img/engagement_unselected.png?v=0.2";
        }
      }
      allow();
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
