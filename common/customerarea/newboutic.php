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
    <title>Initialisation de la boutic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/back.css?v=1.717">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script>window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo $_ENV['CRISP_WEBSITE_ID']; ?>";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
  </head>
  <body class="custombody" ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()" class='epure' alt="">
      <div id="workspace" class="spaceflexnb">
        <div id="loadid" class="spinner-border nospmd" role="status" style="display: block;">
          <span class="sr-only">Loading...</span>
        </div>
        <div id="pagecontainerid" class="pagecontainer" style="display: none;">
          <img id='filetape3' src="img/fil_Page_3.png?v=1.001" class="fileelem" alt="">
          <div class="filecontainer">
            <div id='mainform' class="customform">
              <p class="center middle title">
                Je crée ma <span style="color:#e2007a">Pratic</span><span style="color:#6c757d">Boutic</span>
              </p>
              <form id="signup-form" onsubmit="bakinfo()" method="post" action="initboutic.php" autocomplete="on">
                <div class="twocol">
                  <div class="param">
                    <input class="paramfieldc" id="nom" maxlength="100" name="nom" type="text" placeholder="Nom de la boutic" value="" required><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="aliasboutic" maxlength="100" name="aliasboutic" type="text" placeholder="Alias de la Boutic" value="" pattern="(?!admin|common|upload|vendor)[a-z0-9]{3,}" title="Boutic Alias autorisé de trois chiffres contenant uniquement des minuscules et des chiffres" required><br>
                  </div>
                  <div class="param">
                    <input class="paramfieldc" id="email" maxlength="255" name="email" type="email" placeholder="Courriel de la Boutic" title="Courriel où sera envoyé les commandes" autocomplete="on" required><br>
                  </div>
                  <div id="bloclogoid">
                    <label class="paramfieldlf" title="facultatif">Logo de la boutic :</label><input class="form-control-file cb-form-control-file" id="artlogofile" maxlength="100" name="logo" type="file" title="Si aucun le nom de la boutic sera affiché" accept="image/png, image/jpeg" data-artlogo="artlogo" onchange="uploadlogo(this)">
                    <div class="frameimg">
                      <img id="artlogo" class="imgart" alt="">
                      <img id="logofermer" class="imgclose" src="../img/fermer.png" style="display: none" data-artlogofile="artlogofile" data-artlogo="artlogo" onclick="closeimg(this)" alt="">
                    </div>
                  </div>
                  <div class="param">
                    <input class="" id="autologo" name="autologo" type="checkbox" title="Logo automatique" autocomplete="on">
                    <label class="paramlabellf" for="autologo" title="facultatif">Je n'ai pas de logo, création automatique de logo</label>
                  </div>
                </div>
                <div class="param rwc">
                  <input class="regsubmit" type="submit" name="CONTINUER" value="CONTINUER" autofocus>
                </div>
                <p class="changeable">Les informations pourront être modifié par la suite à partir de votre espace</p>
              </form>
            </div>
          </div>
        </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()" class='epure' alt="">
    </div>
    <script type="text/javascript" >
      function bakinfo()
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("pagecontainerid").style.display = "none";
        sessionStorage.setItem('pb_initb_aliasboutic', document.getElementById("aliasboutic").value);
        sessionStorage.setItem('pb_initb_nom', document.getElementById("nom").value);
        if (document.getElementById("artlogo").hasAttribute('src'))
          sessionStorage.setItem('pb_initb_logo', document.getElementById("artlogo").src);
        else
          sessionStorage.removeItem('pb_initb_logo');
        sessionStorage.setItem('pb_initb_email', document.getElementById("email").value);
      }
  
      window.onload=function()
      {
        document.getElementById("aliasboutic").value = sessionStorage.getItem('pb_initb_aliasboutic');
        document.getElementById("nom").value = sessionStorage.getItem('pb_initb_nom');
        if ((sessionStorage.getItem('pb_initb_logo') !== "") && (sessionStorage.getItem('pb_initb_logo') !== null))
        {
          document.getElementById("artlogo").src = sessionStorage.getItem('pb_initb_logo');
          document.getElementById("logofermer").style.display = 'block';
        }
        document.getElementById("email").value = sessionStorage.getItem('pb_initb_email');
        document.getElementById("loadid").style.display = "none";
        document.getElementById("pagecontainerid").style.display = "flex";
      }
  
      function cancel() 
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("pagecontainerid").style.display = "none";
        sessionStorage.removeItem('pb_initb_aliasboutic');
        // Supprimer le client ?
        window.location.href = './register.php';
     }
      
      function uploadlogo(elem)
      {
        var pathimg = '../../upload/';
    		const fileInput = elem;
  			const formdata = new FormData();
  			formdata.append('file', fileInput.files[0]);
  			
        fetch("upload.php", {
          method: "POST",
          body: formdata
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
  					fileInput.setAttribute("data-modified", 'true');
  					document.getElementById(fileInput.getAttribute("data-artlogo")).src = pathimg + data;
    				fileInput.setAttribute("data-logotruefilename", data);
    				fileInput.filename = data;
    				document.getElementById("logofermer").style.display = 'block';
  				}
        })
      }
      
      function closeimg(elem)
      {
        document.getElementById(elem.getAttribute("data-artlogofile")).setAttribute("data-modified", 'true');
  			document.getElementById(elem.getAttribute("data-artlogofile")).setAttribute("data-logotruefilename",'');
  			document.getElementById(elem.getAttribute("data-artlogofile")).value = '';
  			document.getElementById(elem.getAttribute("data-artlogo")).removeAttribute("src");
  			elem.style.display = 'none';
      }
      
    </script>
    <script type="text/javascript" >
    
    String.prototype.sansAccent = function(){
      var accent = [
          /[\300-\306]/g, /[\340-\346]/g, // A, a
          /[\310-\313]/g, /[\350-\353]/g, // E, e
          /[\314-\317]/g, /[\354-\357]/g, // I, i
          /[\322-\330]/g, /[\362-\370]/g, // O, o
          /[\331-\334]/g, /[\371-\374]/g, // U, u
          /[\321]/g, /[\361]/g, // N, n
          /[\307]/g, /[\347]/g, // C, c
      ];
      var noaccent = ['A','a','E','e','I','i','O','o','U','u','N','n','C','c'];
       
      var str = this;
      for(var i = 0; i < accent.length; i++){
          str = str.replace(accent[i], noaccent[i]);
      }
       
      return str;
  }
    
    function expurger(str)
    {
      var ret = "";
      var charok = "abcdefghijklmnopqrstuvwxyz0123456789";
      for (var i=0; i<str.length; i++) 
      {
        for (var j=0; j<charok.length; j++)
        {
          if (str[i] == charok[j])
          {
            ret =  ret + str[i];
          }
        }
      }
      return ret
    }
      document.getElementById("nom").addEventListener("keyup", function() {
        document.getElementById("aliasboutic").value = expurger(document.getElementById("nom").value.toLowerCase().sansAccent())
        });
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
  </body>
</html>
