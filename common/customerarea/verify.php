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
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace">
        <img id='illus2' src='img/illustration_2.png' />
        <div class="bigform-content">
          <div class="modal" tabindex="-1" role="dialog" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-reg" role="document">
              <div class="modal-content modal-content-mainmenu">
                <div class="modal-header">
                  <h5 class="modal-title">INFORMATION</h5>
                </div>
                <div class="modal-body">
                  <?php
                    session_start();
                    
                    require_once '../../vendor/autoload.php';
                    include "../config/common_cfg.php";
                    include "../param.php";
                    
                    $email = isset($_GET['email']) ? $_GET['email'] : '';
                    $hash = isset($_GET['hash']) ? $_GET['hash'] : '';
                    
                    $activation = 0;
                    
                    $conn = new mysqli($servername, $username, $password, $bdd);
                    if ($conn->connect_error) 
                    {
                      die("Connection failed: " . $conn->connect_error);
                    }
                    //  Récupération de l'utilisateur et de son pass hashé
                    $req = $conn->prepare('SELECT idtid, actif FROM identifiant WHERE email = ? AND hash = ? ');
                    $req->bind_param("ss", $email, $hash);
                    $req->execute();
                    $req->bind_result($idtid, $actif);
                    $resultat = $req->fetch();
                    $req->close();
                    if (strcmp($idtid, "") == 0 )
                    {
                      echo "Erreur ! Votre courriel ne peut être validé...";
                    }
                    else if($actif == '1') // Si le compte est déjà actif on prévient
                    {
                      $activation = 1;
                      echo "Votre courriel est déjà actif !";
                    }
                    else // Si ce n'est pas le cas on passe aux comparaisons
                    {
                      echo "Votre courriel a bien été validé !";
                      $q1 = "UPDATE identifiant SET actif = 1 WHERE idtid = $idtid";
                      if ($r1 = $conn->query($q1)) 
                      {
                        if ($r1 === FALSE) 
                        {
                          echo "Error: " . $q1 . "<br>" . $conn->error;
                        }
                        else 
                        {
                          $activation = 1;
                          $_SESSION['verify_email'] = $email;
                        }
                      }
                    }
                  ?>
                </div>
                <div class="modal-footer">
                 <?php 
                   if ($activation == 1)
                     echo '<a href="register.php"><button class="btn btn-primary btn-block" type="button" value="Valider">OK</button></a>';
      			       else 
      			         echo '<a href="reg.php"><button class="btn btn-primary btn-block" type="button" value="Valider">OK</button></a>';
      			     ?>
      			    </div>
       		    </div>
      		  </div>
      		</div>
      	</div>
      </div>
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
