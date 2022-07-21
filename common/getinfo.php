<?php
  session_id("boutic");
  session_start();
  
	if (empty($_SESSION['customer']) != 0)
	{
    header('LOCATION: 404.html');
    exit();
	}

  $customer = $_SESSION['customer'];
  $method = intval($_SESSION['method']);
  $table = intval($_SESSION['table']);
  
  if (empty($_SESSION[$customer . '_mail']) == TRUE)
  {
    header('LOCATION: index.php?customer=' . $customer . '');
    exit();
  }
  
  if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
  {
    header('LOCATION: index.php?customer=' . $customer . '');
    exit();
  }
  
  require '../vendor/autoload.php';
  include "config/common_cfg.php";
  include "param.php";
             
  // Create connection
  $conn = new mysqli($servername, $username, $password, $bdd);
  // Check connection
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);  
    
  $reqci = $conn->prepare('SELECT CU.customid, CU.nom, CL.adresse1, CL.adresse2, CL.codepostal, CL.ville, CU.logo FROM customer CU, client CL WHERE CU.customer = ? AND CL.cltid = CU.cltid LIMIT 1');
  $reqci->bind_param("s", $customer);
  $reqci->execute();
  $reqci->bind_result($customid, $nom, $adresse1, $adresse2, $codepostal, $ville,  $logo);
  $resultatci = $reqci->fetch();
  $reqci->close();
   
  $adr = $nom . ' ' . $adresse1 . ' ' . $adresse2 . ' ' . $codepostal . ' ' . $ville;

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Prise d'information</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" media="screen" href="css/style2.css?v=<?php echo $ver_com_css;?>" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css?v=<?php echo $ver_com_css;?>">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	  <script type="text/javascript" src="js/bandeau.js?v=2.01"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>

    <?php
      $verifcp = GetValeurParam("VerifCP", $conn, $customid, "0");    
			
			echo '<div id="header">';
			echo '<img id="mainlogo" src="img/logo-pratic-boutic.png">';
			echo '</div>';		
			
      echo '<div id="main" data-adresse="' . $adr . '" data-customer="' . $customer . '" data-verifcp="' . $verifcp . '" >';
      
      echo '<form name="mainform" autocomplete="on" method="post" action="paiement.php">';

      if (strcmp($logo,"") != 0)
        echo '<img id="logo" src="../upload/' . $logo . '">';
      else
        echo '<p class="marque">' . $nom . '</p>';
      
      if ($method > 0)
      {      
      	echo '<div id="grpinfo">';
        echo '<div class="panneau" id="livraison">Informations concernant la livraison</div>';
        if ($method > 2)
	      {      
	        echo '<div class="underlined">';
	        echo '<label class="lcont">Nom&nbsp;:&nbsp;</label>';
	        echo '<input class="cont" type="string" id="lenom" name="nom" maxlength="60" required>';
	        echo '</div>';
	        echo '<div class="underlined">';            
	        echo '<label class="lcont">Pr&eacute;nom&nbsp;:&nbsp;</label>';
	        echo '<input class="cont" type="string" id="leprenom" name="prenom" maxlength="60" required>';
        	echo '</div>';
      	}
        echo '<div class="underlined">';
        echo '<label class="lcont">T&eacute;l.&nbsp;Portable&nbsp;:&nbsp;</label>';
        echo '<input class="cont" type="string" id="letel" name="tel" autocomplete="tel" maxlength="60" required 
        pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[6-7](?:[\.\-\s]?\d\d){4}$" 
        title="Il faut un numéro de téléphone portable français valide">';
        echo '</div>';
        if ($method > 2)
	      {      
	        $chm = GetValeurParam("Choix_Method", $conn, $customid, "TOUS");         
	       
	        $cmemp = GetValeurParam("CM_Emporter", $conn, $customid, "Retrait Standard"); 
	    
	        $cmlivr = GetValeurParam("CM_Livrer", $conn, $customid, "Livraison Standard");
	
	        echo '<div class="panneau" id="met">';
	        echo '<div id="model" data-permis="' . $chm . '">';
	        //echo 'Retrait :<br>';
	        if ($chm == "TOUS")
	        { 
	          echo '<input class="paiers" type="radio" name="choixmeth" id="lemporter" value="EMPORTER" onclick="eraseAdrLivr(true);removeFraisLivraison()">';
	          echo '<label class="lblpaiers" for="lemporter">&Agrave; Emporter </label><br>';
	          echo '<div class="spcpandetail"></div>';
	          echo '<label class="pandetail">';
	          echo $cmemp; 
	          echo '</label><br>';
	          echo '<input class="paiers" type="radio" name="choixmeth" id="llivrer" value="LIVRER" onclick="eraseAdrLivr(false);getFraisLivraison(sessionStorage.getItem(\'sstotal\'))">';
	          echo '<label class="lblpaiers" for="llivrer">En Livraison </label><br>';
	          echo '<div class="spcpandetail"></div>';
	          echo '<label class="pandetail">';
	          echo $cmlivr;
	          echo '</label><br>';
	        }
	        if ($chm == "EMPORTER")
	        {
	          echo '<label class="lblpaiers" for="lemporter">&Agrave; Emporter </label><br>';
	          echo '<label class="pandetail">';
	          echo $cmemp; 
	          echo '</label><br>';
	        }  
	        if ($chm == "LIVRER")
	        {
	          echo '<label class="lblpaiers" for="llivrer">En Livraison </label><br>';
	          echo '<label class="pandetail">';
	          echo $cmlivr;
	          echo '</label><br>';
	        }  
	        echo '</div>';
	        echo '</div>';
	
		      echo '<div id="adrlivr">';
		      echo '<div class="underlined">';
		      echo '<label class="lcont">Adresse&nbsp;1&nbsp;:&nbsp;</label>';
		      echo '<input class="cont adrliv" type="string" id="ladresse1" name="adresse1" maxlength="150" required>';
		      echo '</div>';
		      echo '<div class="underlined">';
		      echo '<label class="lcont">Adresse&nbsp;2&nbsp;:&nbsp;</label>';
		      echo '<input class="cont adrliv" type="string" id="ladresse2" name="adresse2" maxlength="150">';
		      echo '</div>';
		      echo '<div class="underlined">';
		      echo '<label class="lcont">Code&nbsp;Postal&nbsp;:&nbsp;</label>';
		      if ($verifcp > 0) {
		        echo '<input class="cont adrliv" type="string" id="lecp" name="cp" maxlength="5" required 
		          pattern="[0-9]{5}" title="Il faut un code postal français valide" onkeyup="checkcp(this)" data-inrange="ko">';
		      } else {
		        echo '<input class="cont adrliv" type="string" id="lecp" name="cp" maxlength="5" required 
		          pattern="[0-9]{5}" title="Il faut un code postal français valide" data-inrange="ok">';
		      }
		      echo '</div>';
		      echo '<div class="underlined">';
		      echo '<label class="lcont ">Ville&nbsp;:&nbsp;</label>';
		      echo '<input class="cont adrliv" type="string" id="laville" name="ville" maxlength="50" required>';
		      echo '</div>';
		      echo '<div class="panneau" id="fraislivrid" >Frais&nbsp;de&nbsp;livraison&nbsp;:&nbsp;0,00&nbsp;€</div>';
		      echo '</div>';
		    }
		        
		    echo '</div>';

		    echo '</form>';
		    
		    if ($method > 2)
		    {
		      echo '<hr class="separation">';
	   
	        $chp = GetValeurParam("Choix_Paiement", $conn, $customid, "TOUS");         
	       
	        $cmpt = GetValeurParam("MP_Comptant", $conn, $customid, "Prochain écran par CB"); 
	    
	        $livr = GetValeurParam("MP_Livraison", $conn, $customid, "Paiement à la livraison");
	
	        echo '<div class="panneau" id="paye">';
	        echo '<div id="modep" data-permis="' . $chp . '">';
	        if ($chp == "TOUS")
	        { 
	          echo '<input class="paiers" type="radio" name="choixpaie" id="pcomptant" value="COMPTANT">';
	          echo '<label class="lblpaiers" for="pcomptant">Au&nbsp;Comptant&nbsp;</label><br>';
	          echo '<div class="spcpandetail"></div>';
	          echo '<label class="pandetail">';
	          echo $cmpt; 
	          echo '</label><br>';
	          echo '<input class="paiers" type="radio" name="choixpaie" id="plivraison" value="LIVRAISON">';
	          echo '<label class="lblpaiers" for="plivraison">A&nbsp;La&nbsp;Livraison&nbsp;</label><br>';
	          echo '<div class="spcpandetail"></div>';
	          echo '<label class="pandetail">';
	          echo $livr;
	          echo '</label><br>';
	        }
	        if ($chp == "COMPTANT")
	        {
	          echo '<label class="lblpaiers" for="pcomptant">Au&nbsp;Comptant&nbsp;</label><br>';
	          echo '<label class="pandetail">';
	          echo $cmpt; 
	          echo '</label><br>';
	        }  
	        if ($chp == "LIVRAISON")
	        {
	          echo '<label class="lblpaiers" for="plivraison">A&nbsp;La&nbsp;Livraison&nbsp;</label><br>';
	          echo '<label class="pandetail">';
	          echo $livr;
	          echo '</label><br>';
	        }
		    	echo '</div>';
		    	echo '</div>';
	      }  
      }
      echo '<div class="panneau" id="cgv">';
      echo '<input type="checkbox" id="chkcgv" name="okcgv" value="valcgv">';
      echo '<label class="lblcgv" for="valcgv">J\'accepte <a id="cgvlink" href="javascript:bakInfo();window.location.href = \'CGV.php\'">les conditions générales de vente</a></label><br>';
      echo '</div>';
    ?>
    <textarea id="infosup" name="infosup" placeholder="Informations supplémentaires (date, heure, code interphone, ...)" maxlength="300"></textarea>
    </div>    
    <div id="footer">
      <?php
        if  ($method > 0)
        {
        	echo '<div class="grpbn">';
         	echo '<input id="validcarte" class="navindic" type="button" value="Poursuivre" onclick="checkInfo()">';
         	echo '<input id="retourcarte" class="navindic" type="button" value="Retour" onclick="bakInfo();window.location.href = \'carte.php\'">';
          echo '</div>';
        }
      ?>
    </div>
    <script type="text/javascript">
   		// Appel asynchrone pour savoir si on est dans le périmètre de livraison 
      function checkcp(elem)      
      {
        var retour;      
      
        if (elem.value.length == 5)
        { 
        	customer = document.getElementById("main").getAttribute("data-customer");
        	var obj = { cp: elem.value, customer: customer };  
          
          fetch("cpzone.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(obj)
          })
            .then(function(result) {
              return result.json();
            })
            .then(function(data) {
              document.getElementById("lecp").setAttribute("data-inrange", data);
          })
        } else {
          document.getElementById("lecp").setAttribute("data-inrange", "ko");
        }
          
      }          
    </script>
    <script type="text/javascript">
   		// Appel asynchrone pour connaitre le cout de la livraison
      function getFraisLivraison(sstotal)      
      {
        var retour;      
      
        var customer = document.getElementById("main").getAttribute("data-customer");
				var obj = { sstotal: sstotal, customer: customer };
        fetch("fraislivr.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(obj)
        })
          .then(function(result2) {
            return result2.json();
          })
          .then(function(data) {
          	var ret = parseFloat(data);
            document.getElementById("fraislivrid").innerHTML = 'Frais de livraison : ' + ret.toFixed(2) + ' €';
            sessionStorage.setItem("fraislivr", data);
        })
      }          
    </script>
    <script type="text/javascript">
   		// Appel asynchrone pour connaitre le cout de la livraison
      function removeFraisLivraison()      
      {
	      document.getElementById("fraislivrid").innerHTML = '';
        sessionStorage.setItem("fraislivr", 0);
      }          
    </script>
    
    
    <script type="text/javascript" >
    	// Désactive la partie de formulaire utilisé pour la livraison
      function eraseAdrLivr(etat) 
      {
        var fieldAdrLiv = document.getElementsByClassName("adrliv");
        for (i=0;i<fieldAdrLiv.length;i++) 
        {
          fieldAdrLiv[i].disabled = etat;
        }
        document.getElementById("adrlivr").hidden = etat;      	
      }
    </script>
    <script type="text/javascript" >
      // Affiche la page avec les contrôles par defaut  
      sessionStorage.setItem("fraislivr", 0);
      var verifcp = document.getElementById("main").getAttribute("data-verifcp");
      document.getElementById("letel").value = sessionStorage.getItem("telephone");
      if (sessionStorage.getItem("method")>2)
      {
        document.getElementById("lenom").value = sessionStorage.getItem("nom");    
        document.getElementById("leprenom").value = sessionStorage.getItem("prenom");
        if (document.getElementById("modep").getAttribute("data-permis") == "TOUS")
        {
          if (sessionStorage.getItem("choice") == "COMPTANT")
          {
            document.getElementById("pcomptant").checked = true;
            document.getElementById("plivraison").checked = false;
          }
          else if (sessionStorage.getItem("choice") == "LIVRAISON")
          {
            document.getElementById("pcomptant").checked = false;
            document.getElementById("plivraison").checked = true;
          } 
          else
          {
            document.getElementById("pcomptant").checked = false;
            document.getElementById("plivraison").checked = false;
          } 
        }
        if (document.getElementById("model").getAttribute("data-permis") == "TOUS")
        {
          if (sessionStorage.getItem("choicel") == "EMPORTER")
          {
            document.getElementById("lemporter").checked = true;
            document.getElementById("llivrer").checked = false;
            removeFraisLivraison();
            eraseAdrLivr(true);
          }
          else if (sessionStorage.getItem("choicel") == "LIVRER")
          {
            document.getElementById("ladresse1").value = sessionStorage.getItem("adresse1");
            document.getElementById("ladresse2").value = sessionStorage.getItem("adresse2");
            document.getElementById("lecp").value = sessionStorage.getItem("codepostal");
            if (verifcp > 0)
            	checkcp(document.getElementById("lecp"));
            document.getElementById("laville").value = sessionStorage.getItem("ville");
            document.getElementById("lemporter").checked = false;
            document.getElementById("llivrer").checked = true;
						getFraisLivraison(sessionStorage.getItem("sstotal"));
            eraseAdrLivr(false);
          } 
          else
          {
            document.getElementById("lemporter").checked = false;
            document.getElementById("llivrer").checked = false;
            removeFraisLivraison();
            eraseAdrLivr(true);
          } 
        }
        if (document.getElementById("model").getAttribute("data-permis") == "LIVRER")
        {
            document.getElementById("ladresse1").value = sessionStorage.getItem("adresse1");
            document.getElementById("ladresse2").value = sessionStorage.getItem("adresse2");
            document.getElementById("lecp").value = sessionStorage.getItem("codepostal");
            if (verifcp > 0)
	            checkcp(document.getElementById("lecp"));
            document.getElementById("laville").value = sessionStorage.getItem("ville");
            getFraisLivraison(sessionStorage.getItem("sstotal"));
            eraseAdrLivr(false);
        }
        if (document.getElementById("model").getAttribute("data-permis") == "EMPORTER")
        {
        	removeFraisLivraison();
          eraseAdrLivr(true);
        }

      }
      document.getElementById("infosup").value = sessionStorage.getItem("infosup");
    </script>
    <script type="text/javascript">
    	// Défini la zone scrollable
      function reachBottom()
      {
        var x = window.innerHeight - document.getElementById("footer").clientHeight - document.getElementById("header").clientHeight;
        x = x + "px";
        document.getElementById("main").style.height = x;
      }
    </script>
    <script type="text/javascript">
	    window.onload=function()
    	{
      	reachBottom();
      }
    </script>
    <script type="text/javascript">
      window.addEventListener("resize", function() {
        reachBottom();
      })
    </script>
    <script type="text/javascript" >
    	// Sauvegarde les valeurs de la pages
      function bakInfo()
      {
      	sessionStorage.setItem("telephone", document.getElementById("letel").value);
        if (sessionStorage.getItem("method")>2)
        {
          sessionStorage.setItem("nom", document.getElementById("lenom").value);
          sessionStorage.setItem("prenom", document.getElementById("leprenom").value);
          sessionStorage.setItem("adresse1", document.getElementById("ladresse1").value);
          sessionStorage.setItem("adresse2", document.getElementById("ladresse2").value);
          sessionStorage.setItem("codepostal", document.getElementById("lecp").value);
          sessionStorage.setItem("ville", document.getElementById("laville").value);
          if (document.getElementById("modep").getAttribute("data-permis") == "TOUS")
          {
            if (document.getElementById("pcomptant").checked == true)
              sessionStorage.setItem("choice", "COMPTANT");
            else if (document.getElementById("plivraison").checked == true)
              sessionStorage.setItem("choice", "LIVRAISON");
            else 
              sessionStorage.setItem("choice", "NONE");
          }
          else
            sessionStorage.setItem("choice", document.getElementById("modep").getAttribute("data-permis"));
          if (document.getElementById("model").getAttribute("data-permis") == "TOUS")
          {
            if (document.getElementById("lemporter").checked == true)
              sessionStorage.setItem("choicel", "EMPORTER");
            else if (document.getElementById("llivrer").checked == true)
              sessionStorage.setItem("choicel", "LIVRER");
            else 
              sessionStorage.setItem("choicel", "NONE");
          }
          else
            sessionStorage.setItem("choicel", document.getElementById("model").getAttribute("data-permis"));

	        if (sessionStorage.getItem("choicel") == "LIVRER")
	          getFraisLivraison(sessionStorage.getItem("sstotal"));
	        else
  	      	removeFraisLivraison();
  	    }
        sessionStorage.setItem("infosup", document.getElementById("infosup").value);
      }
    </script>
    <script type="text/javascript" >
      // Vérifie que les infos nécessaires ont été saisi pour quitter le formulaire
      function checkInfo() 
      {
        var failed = false;
        
        bakInfo();        
        if (sessionStorage.getItem("method")>2)
        {
	        if (sessionStorage.getItem("choicel") == "LIVRER") {
	          if ( document.getElementById("lecp").getAttribute("data-inrange") !== "ok") {
	            alert("Vous n\'êtes pas situé dans notre zone de livraison, vous devez venir chercher votre commande à notre boutique " + document.getElementById("main").getAttribute("data-adresse"));
	            failed = true;
	          }
	        }
	        if ((sessionStorage.getItem("choice") == "NONE") && (failed == false)) {
	            alert("Vous n\'avez pas choisi comment régler la transaction, impossible de continuer");
	            failed = true;
	        }
	        if ((sessionStorage.getItem("choicel") == "NONE") && (failed == false)) {
	            alert("Vous n\'avez pas choisi comment la vente aller se dérouler, impossible de continuer");
	            failed = true;
	        }
	      }
	      
        for (var j=0; j < document.forms["mainform"].length; j++)
        {
          if ((document.forms["mainform"][j].checkValidity() == false) && (failed == false))
          {
            alert(document.forms["mainform"][j].name + " : " + document.forms["mainform"][j].validationMessage);
            failed = true;
          }
        }
        
        if ((document.getElementById("chkcgv").checked == false ) && (failed == false))
        {
          alert("Vous devez accepter les conditions générales de vente pour continuer");
          failed = true;        
        }         
        
        if (failed == false)
          document.forms["mainform"].submit();
      }
    </script>
  </body>
</html>
