<?php
  session_start();
  session_destroy();
  session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.179">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="window.location='https://pratic-boutic.fr'"/>
      <div class="bigform-content">
      <div class="modal modal-mainmenu" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-dialog-mainmenu" role="document">
          <div class="modal-content modal-content-mainmenu">
            <form method="post" action="valid.php">
              <div class="modal-header modal-header-mainmenu">
                <img id='logopbid' src='img/LOGO_PRATIC_BOUTIC.png' />
                <h6 class="modal-title">CONNEXION ARRIERE BOUTIC</h6>
              </div>
              <div class="modal-body modal-body-mainmenu">
                <div class="form-group">
                  <input class="form-control" placeholder="Courriel" type="string" id="emailid" name="email">
                </div>
                <div class="form-group">
                   <input class="form-control" placeholder="Mot de passe" type="password" id="passid" name="pass">
                 </div>
              </div>
              <div class="modal-footer">
                <input class="btn btn-primary btn-block btn-valider" type="submit" value="VALIDER">
                <a class="mr-auto mdfaddlink forgotpwd" href="./password.php">Mot de passe oublié ?</a>
                <input class="btn btn-secondary btn-block btn-creationboutic" type="button" onclick="window.location='./reg.php'" value="JE CR&Eacute;E MA BOUTIC" />
              </div>
            </form>
          </div>
        </div>
      </div>
      </div>
      <img id='illus1' src='img/illustration_1.png' />
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="window.location='https://pratic-boutic.fr'"/>
    </div>
  </body>
  <script type="text/javascript" >
    $('.modal').modal({keyboard: false});
    $('.modal').modal('show');
    //appending modal background inside the bigform-content
    $('.modal-backdrop').appendTo('.bigform-content');
    //removing body classes to able click events
    $('body').removeClass();
    $('body').removeAttr('class');
    $('body').removeAttr('style');
  </script>
</html>