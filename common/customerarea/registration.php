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
  <body>
    <div class="modal" tabindex="-1" role="dialog" data-backdrop="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">ERREUR</h5>
          </div>
           <div class="modal-body">
            <?php
              session_start();

              require '../../vendor/autoload.php';
              include "../config/common_cfg.php";
              include "../param.php";
              
              $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
              $dotenv->load();

              $_SESSION['registration_pass'] = isset($_POST['pass']) ? $_POST ['pass'] : '';
              $_SESSION['registration_passconf'] = isset($_POST['passconf']) ? $_POST ['passconf'] : '';
              $_SESSION['registration_qualite'] = isset($_POST['qualite']) ? $_POST ['qualite'] : '';
              $_SESSION['registration_nom'] = isset($_POST['nom']) ? $_POST ['nom'] : '';
              $_SESSION['registration_prenom'] = isset($_POST['prenom']) ? $_POST ['prenom'] : '';
              $_SESSION['registration_adr1'] = isset($_POST['adr1']) ? $_POST ['adr1'] : '';
              $_SESSION['registration_adr2'] = isset($_POST['adr2']) ? $_POST ['adr2'] : '';
              $_SESSION['registration_cp'] = isset($_POST['cp']) ? $_POST ['cp'] : '';
              $_SESSION['registration_ville'] = isset($_POST['ville']) ? $_POST ['ville'] : '';
              $_SESSION['registration_tel'] = isset($_POST['tel']) ? $_POST ['tel'] : '';
              
              $stripe = new \Stripe\StripeClient([
                // TODO replace hardcoded apikey by env variable
                  'api_key' => $_ENV['STRIPE_SECRET_KEY'],
                  'stripe_version' => '2020-08-27',
                ]);
              
               $customer = $stripe->customers->create([
                'address' => ['city' => $_SESSION['registration_ville'],
                              'country' => 'FRANCE',
                              'line1' => $_SESSION['registration_adr1'],
                              'line2' => $_SESSION['registration_adr2'],
                              'postal_code' => $_SESSION['registration_cp']],
                'email' => $_SESSION['verify_email'],
                'name' => $_SESSION['registration_nom'],
                'phone' => $_SESSION['registration_tel']
              ]);
              
              $_SESSION['registration_stripe_customer_id'] = $customer->id;

              if (strcmp($_SESSION['registration_pass'], $_SESSION['registration_passconf']) != 0)
              {
                echo 'Les mots de passe ne sont pas identiques !<br>';
              }
              else 
              {
                header("LOCATION: newboutic.php");
              }
             ?>
            </div>
            <div class="modal-footer">
              <a href="register.php"><button class="btn btn-primary btn-block" type="button" value="Valider">OK</button></a>
            </div>
         </div>
      </div>
    </div>
  </body>
  <script type="text/javascript" >
    $('.modal').modal('show');
  </script>
</html>
