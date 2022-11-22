<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>Configuration de la Boutic</title>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.12">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
  </head>
  <body ondragstart="return false;" ondrop="return false;">
    <div class="modal" tabindex="-1" role="dialog" data-backdrop="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">ERREUR</h5>
          </div>
          <div class="modal-body">
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
              try
              {
                $_SESSION['confboutic_chxpaie'] = isset($_POST['chxpaie']) ? $_POST['chxpaie'] : '';
                $_SESSION['confboutic_chxmethode'] = isset($_POST['chxmethode']) ? $_POST ['chxmethode'] : '';
                $_SESSION['confboutic_mntmincmd'] = isset($_POST['mntmincmd']) ? $_POST ['mntmincmd'] : '';
                $_SESSION['confboutic_validsms'] = isset($_POST['validsms']) ? ((strcmp($_POST ['validsms'], "on")==0) ? "1" : "0") : '';

                header("LOCATION: prices.php");
              }
              catch (Error $e)
              {
                echo $e->getMessage();
              }
            ?>
            </div>
            <div class="modal-footer">
              <form style="display: inline" action="newboutic.php" method="get">
                <button class="btn btn-primary btn-block" type="submit" value="Valider">OK</button>
              </form>
            </div>
         </div>
      </div>
    </div>
    <script>
      $('.modal').modal('show');
    </script>
  </body>
</html>
