<?php

  session_id("customerarea");
  session_start();

  if (empty($_SESSION['bo_auth']) == TRUE)
  {
    header("LOCATION: index.php");
    exit();
  }

  if (strcmp($_SESSION['bo_auth'], 'oui') != 0)
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
    <title>Annulation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.01">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <script src="https://js.stripe.com/v3/"></script>
    <script> var login = <?php echo '"' . $_SESSION['bo_email'] . '"'; ?>; </script>
    <script src="cancel.js?v=1.104" defer></script>
  </head>
  <body class="custombody" ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spaceflex">
        <div id="modalid" class="modal-content-mainmenu modal-content-cb elemcb">
          <div class="modal-header-cb">
            <h6 class="modal-title-cb">
              ANNULATION
            </h6>
          </div>
          <Label style="color:white;">Voulez-vous annuler cet abonnement ?</label><br>
          <div class="modal-body-cb">
            <div class="param rwc grpbtnfor ffr">
              <input class="butc btn-nbsecondary" type="button" onclick="javascript:revenir()" value="Non" />
              <button class="btn-nbprimary" id="cancel-btn">Oui</button>
            </div>
          </div>
        </div>
        <div id="loadid" class="spinner-border" role="status" style="display: none;">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript" >
  function revenir()
  {
    window.location.href = './account.php';
  }
  
</script>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter ?") == true)
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("modalid").style.display = "none";
        window.location.href ='exit.php';
      }
    }
  </script>
</html>
