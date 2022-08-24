<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.12">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
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
              session_id("customerarea");
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

                $output ="";
                
                // Create connection
                $conn = new mysqli($servername, $username, $password, $bdd);
                // Check connection
                if ($conn->connect_error)
                {
                  throw new Error("Connection failed: " . $conn->connect_error);
                }
            
                $sql = "SELECT count(*) FROM customer cu WHERE cu.customer = '" . $_POST ['aliasboutic'] . "' LIMIT 1";
            
                //error_log($sql);
                $result = $conn->query($sql);
            
                // output data of each row
                if($row = $result->fetch_row())
                {
                  if ($row[0] > 0)
                  {
                    throw new Error('Alias de boutic déjà utilisé');
                  }
                }
            
                $result->close();
                $conn->close();
                
                $_SESSION['initboutic_aliasboutic'] = isset($_POST['aliasboutic']) ? $_POST ['aliasboutic'] : '';
                $_SESSION['initboutic_nom'] = isset($_POST['nom']) ? $_POST ['nom'] : '';
                $_SESSION['initboutic_email'] = isset($_POST['email']) ? $_POST ['email'] : '';

                if (empty($_SESSION['initboutic_aliasboutic'])==TRUE ) {
                  throw new Error("Identifiant vide");
                }
                
                $notid = array('admin', 'common', 'route', 'upload', 'vendor');
                if(in_array($_SESSION['initboutic_aliasboutic'], $notid)) //Si l'extension n'est pas dans le tableau
                {
                  throw new Error('Identifiant interdit');
                }

                header("LOCATION: stripe.php");
              }
              catch (Error $e)
              {
                echo $e->getMessage();
              }
            ?>
            </div>
            <div class="modal-footer">
              <a href="newboutic.php"><button class="btn btn-primary btn-block" type="button" value="Valider">OK</button></a>
            </div>
         </div>
      </div>
    </div>
  </body>
  <script type="text/javascript">
    $('.modal').modal('show');
  </script>
</html>
