<?php

session_id("customerarea");
session_start();

if (empty($_SESSION['verify_email']) == TRUE)
{
  header("LOCATION: index.php");
  exit();
}
 	
require_once '../../vendor/autoload.php';

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Initialisation de la boutic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/back.css?v=1.04">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body class="custombody">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spaceflexnb">
        <div class="customform">
          <p class="center middle title">
            Création de votre <span style="color:#e2007a">pratic</span><span style="color:#6c757d">boutic</span>
          </p>
          <form id="signup-form" onsubmit="bakinfo()" method="post" action="initboutic.php" autocomplete="on">
            <div class="twocol">
              <div class="param">
                <input class="paramfieldc" id="nom" maxlength="100" name="nom" type="text" placeholder="Nom de la boutic" value="" required /><br>
              </div>
              <div class="param">
                <input class="paramfieldc" id="aliasboutic" maxlength="100" name="aliasboutic" type="text" placeholder="Alias de la Boutic" value="" pattern="[a-z0-9]{3,}" title="Boutic Alias uniquement des minuscules et des chiffres" required /><br>
              </div>
              <div class="param">
                <input class="paramfieldc" id="adresse1" maxlength="150" name="adresse1" type="text" placeholder="Adresse (ligne1) de la boutic" value="" /><br>
              </div>
              <div class="param">
                <input class="paramfieldc" id="adresse2" maxlength="150" name="adresse2" type="text" placeholder="Adresse (ligne2) de la boutic" value="" /><br>
              </div>
              <div class="param">
                <input class="paramfieldc" id="codepostal" maxlength="5" name="codepostal" pattern="[0-9]{5}" type="text" placeholder="Code postal de la boutic" value="" /><br>
              </div>
              <div class="param">
                <input class="paramfieldc" id="ville" maxlength="50" name="ville" type="text" placeholder="Ville de la boutic" value="" /><br>
              </div>
              <div class="param">
                <input class="paramfieldc" id="email" maxlength="255" name="email" type="email" placeholder="Courriel de la Boutic" title="Courriel où sera envoyé les commandes" autocomplete="on" required /><br>
              </div>
              <div id="bloclogoid">
                <label class="paramfieldlf" title="facultatif">Logo de la boutic :</label><input class="form-control-file cb-form-control-file" id="artlogofile" maxlength="100" name="logo" type="file" title="Si aucun le nom de la boutic sera affiché" accept="image/png, image/jpeg" data-artlogo="artlogo" onchange="uploadlogo(this)" />
                <div class="frameimg">
                  <img id="artlogo" class="imgart" src="" alt="">
                  <img id="logofermer" class="imgclose" src="../img/fermer.png" style="display: none" data-artlogofile="artlogofile" data-artlogo="artlogo" onclick="closeimg(this)" alt="">
                </div>
              </div>
              <div class="param rwc2">
                <input class="btn-nbsecondary" type="button" onclick="javascript:cancel()" value="ANNULATION" />
                <input class="btn-nbprimary" type="submit" name="continuer" value="CONFIRMATION" autofocus />
              </div>
            </div>
          </form>
        </div>
        <img id='illus4' src='img/illustration_4.png' />
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript" >
    function bakinfo()
    {
      sessionStorage.setItem('pb_initb_aliasboutic', document.getElementById("aliasboutic").value);
      sessionStorage.setItem('pb_initb_nom', document.getElementById("nom").value);
      sessionStorage.setItem('pb_initb_adresse1', document.getElementById("adresse1").value);
      sessionStorage.setItem('pb_initb_adresse2', document.getElementById("adresse2").value);
      sessionStorage.setItem('pb_initb_codepostal', document.getElementById("codepostal").value);
      sessionStorage.setItem('pb_initb_ville', document.getElementById("ville").value);
      sessionStorage.setItem('pb_initb_logo', document.getElementById("artlogo").src);
      sessionStorage.setItem('pb_initb_email', document.getElementById("email").value);

    }
    window.onload=function()
    {
      document.getElementById("aliasboutic").value = sessionStorage.getItem('pb_initb_aliasboutic');
      document.getElementById("nom").value = sessionStorage.getItem('pb_initb_nom');
      document.getElementById("adresse1").value = sessionStorage.getItem('pb_initb_adresse1');
      document.getElementById("adresse2").value = sessionStorage.getItem('pb_initb_adresse2');
      document.getElementById("codepostal").value = sessionStorage.getItem('pb_initb_codepostal');
      document.getElementById("ville").value = sessionStorage.getItem('pb_initb_ville');
      if ((sessionStorage.getItem('pb_initb_logo') !== "") && (sessionStorage.getItem('pb_initb_logo') !== null))
      {
        document.getElementById("artlogo").src = sessionStorage.getItem('pb_initb_logo');
        document.getElementById("logofermer").style.display = 'block';
      }
      document.getElementById("email").value = sessionStorage.getItem('pb_initb_email');
    }

    function cancel() 
    {
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
        window.location.href ='exit.php';
      }
    }
  </script>
</html>
