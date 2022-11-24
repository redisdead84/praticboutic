<?php
  session_start();

  if (empty($_SESSION['verify_email']) == TRUE)
  {
 	  header("LOCATION: index.php");
 	  exit();
 	}

  require_once '../../vendor/autoload.php';
  
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/back.css?v=1.703">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script>window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
  </head>
  <body class="custombody" ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()" class='epure' alt="">
      <div id="workspace" class="spaceflex">
        <div id="loadid" class="spinner-border nospmd" role="status" style="display: block;">
          <span class="sr-only">Loading...</span>
        </div>
        <div id="pagecontainerid" class="pagecontainer" style="display: none;">
          <img id='filetape2' src="img/fil_Page_2.png" class="fileelem" alt="">
          <div class="filecontainer">
            <img id='illus3' src='img/illustration_3.png' class='epure' alt="">
            <div id='mainform' class="customform">
              <p class="center middle title">
                Formulaire d'inscription
              </p>
              <form id="signup-form" onsubmit="return bakinfo()" method="post" action="registration.php" autocomplete="on">
                <div class="twocol">
                  <div class="param rwse">
                    <div class="param center"><input class="paramfieldr center" type="radio" id="homme" name="qualite" value="Monsieur" required><label class="paramfieldr" for="homme">&nbsp;Monsieur&nbsp;</label></div><div class="param center"><input class="paramfieldc center" type="radio" id="femme" name="qualite" value="Madame"><label class="paramfieldr">&nbsp;Madame&nbsp;</label></div><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="nom" name="nom" type="text" placeholder="Nom" value="" maxlength="60" required><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="prenom" name="prenom" type="text" placeholder="Prénom" value="" maxlength="60" required><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="adr1" name="adr1" type="text" placeholder="Adresse (ligne 1)" value="" maxlength="150" required><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="adr2" name="adr2" type="text" placeholder="Adresse (ligne 2)" value="" maxlength="150"><br>
                  </div>
                  <div class="param epure">
                    <div class="paramfieldb" id="blank" name="blank"></div><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="cp" name="cp" type="text" placeholder="Code Postal" value="" maxlength="5" pattern="[0-9]{5}" required title="Il faut un code postal français valide"><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="ville" name="ville" type="text" placeholder="Ville" value="" maxlength="50" required><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="tel" name="tel" type="text" placeholder="Téléphone" value="" autocomplete="tel" maxlength="60" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[0-9](?:[\.\-\s]?\d\d){4}$" title="Il faut un numéro de téléphone français valide"><br>
                  </div>
                  <div style="display: none;" class="param">
                    <input class="paramfieldc" id="courriel" name="courriel" type="email" value="<?php echo $_SESSION['verify_email'];?>" placeholder="Courriel" maxlength="255" required><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc inputeye" id="pass" maxlength="255" name="pass" type="password" placeholder="Créez votre mot de passe" value="" pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}" title="Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères" autocomplete="new-password" required>
                    <i class="bi bi-eye-slash bi-eye eyeico" id="togglepass"></i><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc inputeye" id="passconf" maxlength="255" name="passconf" type="password" placeholder="Mot de passe(confirmation)" value="" pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}" title="Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères" autocomplete="new-password" required>
                    <i class="bi bi-eye-slash bi-eye eyeico" id="togglepassconf"></i><br>
                  </div>
                  <div class="param">
                    <p class="passinfo">Le mot de passe doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères</p>
                  </div>
                </div>
                <div class="param rwc">
                  <input class="butc regsubmit" type="submit" value="CONTINUER" autofocus><br>
                </div>
                <p class="changeable">Les informations pourront être modifié par la suite à partir de votre espace</p>
              </form>
            </div>
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()" class='epure' alt="">
    </div>
    <script>
      const togglePassword1 = document.querySelector('#togglepass');
      const password1 = document.querySelector('#pass');
      
      togglePassword1.addEventListener('click', function (e) {
      // toggle the type attribute
          const type = password1.getAttribute('type') === 'password' ? 'text' : 'password';
          password1.setAttribute('type', type);
          // toggle the eye / eye slash icon
          this.classList.toggle('bi-eye');
      });
      const togglePassword2 = document.querySelector('#togglepassconf');
      const password2 = document.querySelector('#passconf');
      
      togglePassword2.addEventListener('click', function (e) {
      // toggle the type attribute
          const type = password2.getAttribute('type') === 'password' ? 'text' : 'password';
          password2.setAttribute('type', type);
          // toggle the eye / eye slash icon
          this.classList.toggle('bi-eye');
      });
    </script>
    <script>
      function bakinfo()
      {
        if (document.getElementById("pass").value !== document.getElementById("passconf").value)
        {
          alert("Les mots de passe ne sont pas identique. Impossible de continuer.");
          return false;
        }
          
        document.getElementById("loadid").style.display = "block";
        document.getElementById("pagecontainerid").style.display = "none";
        sessionStorage.setItem('pb_reg_courriel', document.getElementById("courriel").value);
        if (document.getElementById("homme").checked == true)
          sessionStorage.setItem('pb_reg_qualite', "Monsieur");
        if (document.getElementById("femme").checked == true)
          sessionStorage.setItem('pb_reg_qualite', "Madame");
        sessionStorage.setItem('pb_reg_nom', document.getElementById("nom").value);
        sessionStorage.setItem('pb_reg_prenom', document.getElementById("prenom").value);
        sessionStorage.setItem('pb_reg_adr1', document.getElementById("adr1").value);
        sessionStorage.setItem('pb_reg_adr2', document.getElementById("adr2").value);
        sessionStorage.setItem('pb_reg_cp', document.getElementById("cp").value);
        sessionStorage.setItem('pb_reg_ville', document.getElementById("ville").value);
        sessionStorage.setItem('pb_reg_tel', document.getElementById("tel").value);
        return true;
      }
      window.onload=function()
      {
        if (sessionStorage.getItem('pb_reg_qualite')  == "Monsieur")
        {
          document.getElementById("homme").checked = true;
          document.getElementById("femme").checked = false;
        }
        if (sessionStorage.getItem('pb_reg_qualite')  == "Madame")
        {
          document.getElementById("homme").checked = false;
          document.getElementById("femme").checked = true;
        }
        document.getElementById("nom").value = sessionStorage.getItem('pb_reg_nom');
        document.getElementById("prenom").value = sessionStorage.getItem('pb_reg_prenom');
        document.getElementById("adr1").value = sessionStorage.getItem('pb_reg_adr1');
        document.getElementById("adr2").value = sessionStorage.getItem('pb_reg_adr2');
        document.getElementById("cp").value = sessionStorage.getItem('pb_reg_cp');
        document.getElementById("ville").value = sessionStorage.getItem('pb_reg_ville');
        document.getElementById("tel").value = sessionStorage.getItem('pb_reg_tel');
        document.getElementById("loadid").style.display = "none";
        document.getElementById("pagecontainerid").style.display = "flex";
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
  </body>
</html>
