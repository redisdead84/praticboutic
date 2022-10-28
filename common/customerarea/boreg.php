<?php

session_start();

if (empty($_SESSION['bo_id']) == TRUE)
{
 	  header("LOCATION: index.php");
 	  exit();
}

if (empty($_SESSION['bo_auth']) == TRUE)
{
 	  header("LOCATION: index.php");
 	  exit();
}	

if (strcmp($_SESSION['bo_auth'],'oui') != 0)
{
 	  header("LOCATION: index.php");
 	  exit();
}
  
$_SESSION['reg_mailsent'] = 'non';

require_once '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Changement de courriel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.706">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body class="custombody" ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()" class='epure' />
      <div id="workspace" class="spacemodal">
        <div id="loadid" class="spinner-border" role="status" style="display: none;">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="pagecontainer" id="pagecontainerid">
          <div class="filecontainer">
            <div id="mainmenu" class="modal-content-mainmenu">
              <form id="signup-form" name="signup-form" method="post" onsubmit="onSubmit()" autocomplete="on" action="bochkmail.php">
                <div class="modal-header-cb">
                  <img id='logopbid' src='img/LOGO_PRATIC_BOUTIC.png' />
                  <h6 class="modal-title modal-title-cb">SAISIE DU NOUVEAU COURRIEL</h6>
                </div>
                <div class="modal-body-mainmenu modal-body-cb">
                  <input class="form-control" id="email" maxlength="255" name="email" type="email" placeholder="Courriel" value="" autocomplete="username" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Doit être une adresse de courriel valide" required />
                  <span class="error white" data-errinpid="email">Le courriel doit être de la forme user@domain.ext</span>
                </div>
                <input type="submit" class="btn btn-primary enlarged btn-valider" value="CONTINUER" />
                <div class="modal-footer-cb">
                  <a class="mr-auto mdfaddlink forgotpwd" href="./admin.php">Je ne veux pas changer de courriel</a>
                </div>
              </form>
            </div>
            <img id='illus2' src='img/illustration_2.png' class='elemcb epure'/>
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()" class='epure' />
    </div>
  </body>
  <script type="text/javascript" >
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
    function onSubmit() 
    {

      if (bakinfo() == false)
        return;
      document.getElementById("loadid").style.display = "block";
      document.getElementById("pagecontainerid").style.display = "none";
      document.getElementById("signup-form").submit();
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