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
  
  if (strcmp($_SESSION['bo_auth'], 'oui') != 0)
  {
   	  header("LOCATION: index.php");
   	  exit();
  }

  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.51">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()" class='epure'/>
      <div id="workspace" class="spacemodal">
        <div class="pagecontainer">
          <div class="filecontainer">
            <div id="loadid" class="spinner-border" role="status" style="display: none;">
              <span class="sr-only">Loading...</span>
            </div>
            <img id='illus2' src='img/illustration_2.png' class="elemcb epure" style="display: block;" />
            <div id='mainmenu' class="modal-content-mainmenu elemcb" style="display: block;">
              <div class="modal-body-cb">
                <?php
                  
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
                    echo "<p class='txtbig'>Erreur ! Votre courriel ne peut être validé...</p>";
                  }
                  else if($actif == '1') // Si le compte est déjà actif on prévient
                  {
                    $activation = 1;
                    echo "<p class='txtbig'>Votre courriel est déjà actif !</p>";
                    $_SESSION['bo_email'] = $email;
                  }
                  else // Si ce n'est pas le cas on passe aux comparaisons
                  {
                    echo "<p class='txtbig'>Votre courriel a bien été validé !</p>";
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
                        $req = $conn->prepare('SELECT cltid FROM customer WHERE customid = ? ');
                        $req->bind_param("s", $_SESSION['bo_id']);
                        $req->execute();
                        $req->bind_result($cltid);
                        $resultat = $req->fetch();
                        $req->close();
                        $q2 = "UPDATE client SET email = '" . $email . "' WHERE cltid = $cltid";
                        if ($r2 = $conn->query($q2)) 
                        {
                          if ($r2 === FALSE) 
                          {
                            echo "Error: " . $q2 . "<br>" . $conn->error;
                          }
                          else
                          {
                            $_SESSION['bo_email'] = $email;
                          }
                        }
                      }
                    }
                  }
                ?>
              </div>
              <div class="modal-footer-cb">
               <?php 
                 if ($activation == 1)
                   echo '<a href="admin.php"><button class="btn btn-primary btn-block" type="button" value="Valider">CONTINUER</button></a>';
    			       else 
    			         echo '<a href="reg.php"><button class="btn btn-primary btn-block" type="button" value="Valider">CONTINUER</button></a>';
    			     ?>
       		    </div>
      		  </div>
      		</div>
    		</div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()" class='epure'/>
    </div>
  </body>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter ?") == true)
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("mainmenu").style.display = "none";
        document.getElementById("illus2").style.display = "none";
        window.location.href ='exit.php';
      }
    }
  </script>
</html>