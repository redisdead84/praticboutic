<?php

session_start();

$_SESSION['reg_mailsent'] = 'non';

require_once '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.714">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/enterprise.js?render=<?php echo $_ENV['RECAPTCHA_KEY']; ?>"></script>
    <script>window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <meta name="theme-color" content="#e2007a">
  </head>
  <body class="custombody" ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()" class='epure' alt="">
      <div id="workspace" class="spacemodal">
        <div id="loadid" class="spinner-border" role="status" style="display: none;">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="pagecontainer" id="pagecontainerid">
          <img id='filetape1' src="img/fil_Page_1.png" class="fileelem" alt="">
          <div class="filecontainer">
            <div id="mainmenu" class="center">
              <div class="modal-content-mainmenu">
                <form id="signup-form" name="signup-form" method="post" autocomplete="on" action="valrecapi.php">
                  <div class="modal-header-cb">
                    <img id='logopbid' class='logopbidbig' src='img/LOGO_PRATIC_BOUTIC.png' alt="">
                  </div>
                  <div class="modal-body-mainmenu modal-body-cb">
                    <p class="firstelem">Entrez votre email afin de créer votre PraticBoutic</p>
                    <input class="form-control anyelem" id="email" maxlength="255" name="email" type="email" placeholder="Courriel" value="" autocomplete="username" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Doit être une adresse de courriel valide" required>
                    <span class="error white" data-errinpid="email">Le courriel doit être de la forme user@domain.ext</span>
                    <input type="hidden" id="gRecaptchaResponse" name="gRecaptchaResponse" style="display: none;">
                    <input type="submit" class="btn btn-primary enlarged btn-valider g-recaptcha lastelem" data-sitekey=<?php echo $_ENV['RECAPTCHA_KEY']; ?> data-callback='onSubmit' data-action='submit' value="CONTINUER">
                  </div>
                  <br>
                  <div class="modal-footer-cb">
                    <div id="g_id_onload" data-client_id="<?php echo $_ENV['GOOGLE_CLIENTID']; ?>" data-callback="handleCredentialResponse" data-auto_prompt="false"></div>
                    <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="sign_in_with" data-shape="rectangular" data-logo_alignment="left"></div>
                  </div>
                </form>
              </div>
              <a class="mr-auto mdfaddlink forgotpwd" href="./index.php">J'ai déjà un compte</a>
            </div>
            <img id='illus2' src='img/illustration_2.png' class='elemcb epure' alt="">
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()" class='epure' alt="">
    </div>
    <script>
      function bakinfo()
      {
        var erron = false;
        for (var fld of document.forms['signup-form'].elements)
        {
          if (fld.validity.valid == false)
          {
            const el = document.querySelector('[data-errinpid="' + fld.id + '"]');
            el.style.display = 'block';
            erron = true;
          }
        }
        if (erron == true)
          return false;
        sessionStorage.setItem('pb_reg_email', document.getElementById("email").value);
        return true;
      }
      window.onload=function()
      {
        document.getElementById("email").value = sessionStorage.getItem('pb_reg_email');
        document.getElementById("loadid").style.display = "none";
        document.getElementById("pagecontainerid").style.display = "flex";
      }
      function cancel() 
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("pagecontainerid").style.display = "none";
        sessionStorage.removeItem('pb_reg_email');
        window.location.href = './index.php';
      }
    </script>
    <script>
      function onSubmit(token) 
      {
  
        if (bakinfo() == false)
          return;
        document.getElementById("loadid").style.display = "block";
        document.getElementById("pagecontainerid").style.display = "none";
        document.getElementById("gRecaptchaResponse").value = token;
        document.getElementById("signup-form").submit();
      }
    </script>
    <script>
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
    <script>
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
              document.getElementById("pagecontainerid").style.display = "none";
              window.location = data;
            }
          })
      }
    </script>
  </body>
</html>