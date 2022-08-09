<?php
  
  session_id("customerarea");
  session_start();
  $lifetime=141209600;
  setcookie(session_name(),session_id(),time()+$lifetime);
  if ($_SESSION["active"] == 1)
  {
    header("LOCATION: admin.php");
    exit();
  }
  session_destroy();
  session_id("customerarea");
  session_start();
  session_write_close();
  
  require_once '../../vendor/autoload.php';

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/back.css?v=1.26">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $_ENV['GOOGLE_ANALYTICS_ID']; ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', '<?php echo $_ENV['GOOGLE_ANALYTICS_ID']; ?>');
    </script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quittermenu()"/>
      <div id="workspace" class="spacemodal">
        <div id="loadid" class="spinner-border" role="status" style="display: none;">
          <span class="sr-only">Loading...</span>
        </div>
        <div id="mainmenu" class="modal-content-mainmenu modal-content-cb elemcb" style="display: block;">
          <form method="post" action="valid.php">
            <div class="modal-header-mainmenu modal-header-cb">
              <img id='logopbid' src='img/LOGO_PRATIC_BOUTIC.png' />
              <h6 class="modal-title-cb">CONNEXION ARRI&Egrave;RE BOUTIC</h6>
            </div>
            <div class="modal-body-mainmenu modal-body-cb">
              <div class="form-group">
                <input class="form-control" placeholder="Courriel" type="string" id="emailid" name="email">
              </div>
              <div class="form-group">
                 <input class="form-control" placeholder="Mot de passe" type="password" id="passid" name="pass">
               </div>
            </div>
            <div class="modal-footer-mainmenu">
              <input class="btn btn-primary btn-block btn-valider" type="submit" value="VALIDER">
              <a class="mr-auto mdfaddlink forgotpwd" href="./password.php">Mot de passe oublié ?</a>
              <input class="btn btn-secondary btn-block btn-creationboutic" type="button" onclick="window.location='./reg.php'" value="JE CR&Eacute;E MA BOUTIC" />
              <div id="g_id_onload" data-client_id="<?php echo $_ENV['GOOGLE_CLIENTID']; ?>" data-callback="handleCredentialResponse" data-auto_prompt="false"></div>
              <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="sign_in_with" data-shape="rectangular" data-logo_alignment="left"></div>
            </div>
          </form>
        </div>
        <img id='illus1' src='img/illustration_1.png' class='elemcb' style="display: block;"/>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quittermenu()"/>
    </div>
  </body>
  <script type="text/javascript" >
    function quittermenu() 
    {
      if (confirm("Voulez-vous quitter ?") == true)
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("mainmenu").style.display = "none";
        document.getElementById("illus1").style.display = "none";
        window.location.href ='exit.php';
      }
    }
  </script>
  <script type="text/javascript" >
    function decodeJwtResponse(token) 
    {
      var base64Url = token.split('.')[1];
      var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
          return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));
      return JSON.parse(jsonPayload);
    };

    function handleCredentialResponse(response) 
    {
      const responsePayload = decodeJwtResponse(response.credential);
      var obj = { courriel: responsePayload.email };
      fetch("googlesignin.php", {
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
            document.getElementById("loadid").style.display = "block";
            document.getElementById("mainmenu").style.display = "none";
            document.getElementById("illus1").style.display = "none";
            window.location = data;
          }
        })
    }
  </script>
</html>