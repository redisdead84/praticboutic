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
    <title>Construction de la Boutic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
    <script>window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
  </head>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quittermenu()" class="epure" alt="">
      <div id="workspace" class="spacemodal">
        <div id="loadid" class="spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div id="modalid" class="modal-content-error modal-content-cb elemcb">
          <div class="modal-header-cb">
            <h5 class="modal-title-cb">ERREUR</h5>
          </div>
          <div class="modal-body-cb">
            <!-- msg error here -->
          </div>
          <div class="modal-footer-cb">
            <form style="display: inline" action="register.php" method="get">
              <button class="btn btn-primary btn-block" type="button" value="Valider">OK</button>
            </form>
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quittermenu()" class="epure" alt="">
    </div>
    <script>
      var obj = { action: "buildboutic", table: ""};
      fetch('boquery.php', {
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
          document.getElementById("loadid").style.display = "block";
          document.getElementById("modalid").style.display = "none";
          var modal = $('.modal-content-mainmenu');
          //$('.modal-title').html('Erreur');
          modal.find('.modal-body-cb').text(data.error);
          //$('.modal').modal('show');
        }
        else 
        {
          window.location = "admin.php";
        }
      })
    </script>
    <script>
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
  </body>
</html>