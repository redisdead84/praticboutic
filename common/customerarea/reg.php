<?php

session_start();

$_SESSION['reg_mailsent'] = 'non';

require_once '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.04">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/enterprise.js?render=<?php echo $_ENV['RECAPTCHA_KEY']; ?>"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body class="custombody">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spacemodal">
        <img id='illus2' src='img/illustration_2.png' class='elemcb'/>
        <div class="modal-content-mainmenu">
          <form id="signup-form" onsubmit="bakinfo()" method="post" action="valrecapi.php" autocomplete="on">
            <div class="modal-header-cb">
              <img id='logopbid' src='img/LOGO_PRATIC_BOUTIC.png' />
              <h6 class="modal-title modal-title-cb">INSCRIPTION</h6>
            </div>
            <div class="modal-body-mainmenu modal-body-cb">
              <input class="form-control" id="email" maxlength="255" name="email" type="email" placeholder="Courriel" value="" autocomplete="username" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Doit être une adresse de courriel valide" required />
              <input type="hidden" id="gRecaptchaResponse" name="gRecaptchaResponse">
            </div>
            <input type="submit" class="btn btn-primary enlarged btn-valider g-recaptcha" data-sitekey=<?php echo $_ENV['RECAPTCHA_KEY']; ?> data-callback='onSubmit' data-action='submit' value="INSCRIPTION" />
            <div class="modal-footer-cb">
              <input class="btn btn-secondary enlarged btn-annuler" type="button" onclick="window.location='./index.php'" value="RETOUR" />
            </div>
          </form>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript" >
    function bakinfo()
    {
      sessionStorage.setItem('pb_reg_email', document.getElementById("email").value);
    }
    window.onload=function()
    {
      document.getElementById("email").value = sessionStorage.getItem('pb_reg_email');
    }
    function cancel() 
    {
      sessionStorage.removeItem('pb_reg_email');
      window.location.href = './index.php';
    }
  </script>
  <script>
    function onSubmit(token) 
    {
      document.getElementById("gRecaptchaResponse").value = token;
      document.getElementById("signup-form").submit();
    }
  </script>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter le consructeur de boutic ?") == true)
      {
        window.location ='https://pratic-boutic.fr';
      }
    }
  </script>
</html>