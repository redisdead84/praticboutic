<?php
  include "config/common_cfg.php";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
  </head>
  <script type="text/javascript">
    var customer;
    var mail;
    var method;
    var table;
    var bouticid;
    var nom;
    var adr;
    var logo;
    var chm;
    var cmemp;
    var cmlivr;
    var chp;
    var cmpt;
    var livr;
    
    async function getSession()
    {
      var objboutic = { requete: "getSession"};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objboutic)
      });
      if (!response.ok) {
        throw new Error(`Error! status: ${response.status}`);
      }
      const data  = await response.json();
      customer = data[0];
      mail = data[1];
      method = data[2];
      table = data[3];
    }

    async function getClientInfo(customer)
    {
      var objboutic = { requete: "getClientInfo", customer: customer};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objboutic)
      });
      if (!response.ok) {
        throw new Error(`Error! status: ${response.status}`);
      }
      const data = await response.json();
      bouticid = data[0];
      nom = data[1];
      adr = data[2];
      logo = data[3];
    }
  
    async function getParam(bouticid, param, defval = null)
    {
      var objparam = { action: "getparam", table: "parametre", bouticid: bouticid, param: param};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objparam)
      });
      if (!response.ok) {
        throw new Error(`Error! status: ${response.status}`);
      }
      const data = await response.json();
      if (data[0] == null)
        return defval;
      return data[0];
    }
    
  </script>
    <body ondragstart="return false;" ondrop="return false;">
      <div id="loadid" class="flcentered">
        <div class="spinner-border nospmd" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div id="header" style="display:none">
        <img id="mainlogo" src="img/logo-pratic-boutic.png">
      </div>
      <div id="main" style="display:none;">
        <form name="mainform" autocomplete="on" method="post" action="paiement.php">
          <img id="logo" style="display:none">
          <p id="marqueid" class="marque" style="display:none"></p>
          <div id="grpinfo" style="display:none">
            <div class="panneau" id="livraison">Informations commande</div>
            <div id="blocnomid" class="underlined">
             <label class="lcont">Nom&nbsp;:&nbsp;</label>
             <input class="cont" type="string" id="lenom" name="nom" maxlength="60" required>
           </div>
           <div id="blocprenomid" class="underlined">
             <label class="lcont">Pr&eacute;nom&nbsp;:&nbsp;</label>
             <input class="cont" type="string" id="leprenom" name="prenom" maxlength="60" required>
           </div>
          <div class="underlined">
            <label class="lcont">T&eacute;l.&nbsp;Portable&nbsp;:&nbsp;</label>
            <input class="cont" type="string" id="letel" name="tel" autocomplete="tel" maxlength="60" required 
              pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[6-7](?:[\.\-\s]?\d\d){4}$" 
              title="Il faut un numéro de téléphone portable français valide">
          </div>
          <div class="panneau" id="met" style="display: none;">
            <div id="model">
              <div id="blemporter">
                <input class="paiers" type="radio" name="choixmeth" id="lemporter" value="EMPORTER" onclick="eraseAdrLivr(true);removeFraisLivraison()">
                <label class="lblpaiers" for="lemporter" id="lblemporter">Je viens récupérer ma commande</label><br>
                <div class="spcpandetail" id="spdemporter"></div>
                <label class="pandetail" id="pdemporter"></label>
              </div>
              <div id="bllivrer">
                <input class="paiers" type="radio" name="choixmeth" id="llivrer" value="LIVRER" onclick="javascript:eraseAdrLivr(false);getFraisLivraison(sessionStorage.getItem('sstotal'));">
                <label class="lblpaiers" for="llivrer" id="lbllivrer" >Je me fais livrer</label><br>
                <div class="spcpandetail" id="spdlivrer"></div>
                <label class="pandetail" id="pdlivrer"></label>
              </div>
            </div>
          </div>
          <div id="adrlivr" style="display:none">
            <div class="underlined">
              <label class="lcont">Adresse&nbsp;1&nbsp;:&nbsp;</label>
              <input class="cont adrliv" type="string" id="ladresse1" name="adresse1" maxlength="150" required>
            </div>
            <div class="underlined">
              <label class="lcont">Adresse&nbsp;2&nbsp;:&nbsp;</label>
              <input class="cont adrliv" type="string" id="ladresse2" name="adresse2" maxlength="150">
            </div>
            <div class="underlined">
              <label class="lcont">Code&nbsp;Postal&nbsp;:&nbsp;</label>
              <input class="cont adrliv" type="string" id="lecp" name="cp" maxlength="5" required 
                pattern="[0-9]{5}" title="Il faut un code postal français valide">
            </div>
            <div class="underlined">
              <label class="lcont ">Ville&nbsp;:&nbsp;</label>
              <input class="cont adrliv" type="string" id="laville" name="ville" maxlength="50" required>
            </div>
            <div class="panneau" id="fraislivrid" >Frais&nbsp;de&nbsp;livraison&nbsp;:&nbsp;0,00&nbsp;€</div>
          </div>
        </div>
      </form>
      <hr id="separationid" class="separation">
      <div class="panneau" id="paye" style="display: none;">
        <div id="modep">
          <div id="blcomptant">
            <input class="paiers" type="radio" name="choixpaie" id="pcomptant" value="COMPTANT">
            <label class="lblpaiers" for="pcomptant" id="lblpcomptant">Je&nbsp;paye&nbsp;en&nbsp;ligne&nbsp;</label><br>
            <div class="spcpandetail" id="spdcomptant"></div>
            <label class="pandetail"  id="pdcomptant"></label>
          </div>
          <div id="bllivraison">
            <input class="paiers" type="radio" name="choixpaie" id="plivraison" value="LIVRAISON">
            <label class="lblpaiers" for="plivraison" id="lbllivraison">Je&nbsp;paye&nbsp;à&nbsp;la&nbsp;livraison&nbsp;</label><br>
            <div class="spcpandetail" id="spdlivraison"></div>
            <label class="pandetail" id="pdlivraison"></label>
          </div>
        </div>
      </div>
    <div class="underlined">
      <label class="lcont">Code Promo.&nbsp;:&nbsp;</label>
      <input class="cont" type="string" id="lecodepromo" name="codepromo" maxlength="4" pattern="[0-9A-Z]{4}" onkeyup="getRemise(sessionStorage.getItem('sstotal'), this)">
    </div>
    <div class="panneau" id="remiseid"></div>
    <div class="panneau" id="cgv">
      <input type="checkbox" id="chkcgv" name="okcgv" value="valcgv">
      <label class="lblcgv" for="valcgv">J'accepte <a id="cgvlink" href="javascript:bakInfo();window.location.href = 'CGV.php'">les conditions générales de vente</a></label><br>
    </div>
    <textarea id="infosup" name="infosup" placeholder="Informations supplémentaires (date, heure, code interphone, ...)" maxlength="300"></textarea>
    </div>
    <div id="footer" style="visibility:hidden">
      <div id="grpbnid" class="grpbn">
        <input id="validcarte" class="navindic" type="button" value="Poursuivre" onclick="checkInfo()">
        <input id="retourcarte" class="navindic" type="button" value="Retour" onclick="bakInfo();window.location.href = 'carte.php'">
      </div>
    </div>
    <script type="text/javascript">
      window.onload = async function()
      {
        await getSession();
        if (!customer)
          document.location.href = 'error.php?code=nocustomer';
        await getClientInfo(customer);
        if (!bouticid)
          document.location.href = 'error.php?code=nobouticid';
        if (!mail)
          document.location.href = 'error.php?code=noemail';
        if (mail == 'oui')
          document.location.href = 'error.php?code=alreadysent';
        
        
        chm = await getParam(bouticid, "Choix_Method",  "TOUS");
        cmemp = await getParam(bouticid, "CM_Emporter", "Retrait Standard");
        cmlivr = await getParam(bouticid, "CM_Livrer", "Livraison Standard");
        chp = await getParam(bouticid, "Choix_Paiement", "TOUS", "");
        cmpt = await getParam(bouticid, "MP_Comptant", "Prochain écran par CB");
        livr = await getParam(bouticid, "MP_Livraison", "Paiement à la livraison");

        
        document.getElementById("logo").src = "../upload/" + logo;
        document.getElementById("marqueid").innerHTML = nom;
  
        var verifcp = await getParam(bouticid, "VerifCP");
        if (logo)
        {
          document.getElementById("logo").style.display = "block";
          document.getElementById("marqueid").style.display = "none";
        }
        else 
        {
          document.getElementById("logo").style.display = "none";
          document.getElementById("marqueid").style.display = "block";
        }
        
        if (parseInt(method)>0)
        {
          document.getElementById("grpinfo").style.display = "block";
          document.getElementById("grpbnid").style.display = "block";
        }
        else 
        {
          document.getElementById("grpinfo").style.display = "none";
          document.getElementById("grpbnid").style.display = "none";
        }
        
        if (parseInt(method)>2)
        {
          document.getElementById("blocnomid").style.display = "block";
          document.getElementById("blocprenomid").style.display = "block";
          document.getElementById("met").style.display = "block";
          document.getElementById("paye").style.display = "block";
          if (chm == "TOUS")
          {
            document.getElementById("blemporter").style.display = "block";
            document.getElementById("lemporter").style.display = "inline-block";
            document.getElementById("lblemporter").style.display = "inline-block";
            document.getElementById("spdemporter").style.display = "block";
            document.getElementById("pdemporter").style.display = "inline-block";
            document.getElementById("pdemporter").innerHTML = cmemp;
            document.getElementById("bllivrer").style.display = "block";
            document.getElementById("llivrer").style.display = "inline-block";
            document.getElementById("lbllivrer").style.display = "inline-block";
            document.getElementById("spdlivrer").style.display = "block";
            document.getElementById("pdlivrer").style.display = "inline-block";
            document.getElementById("pdlivrer").innerHTML = cmlivr;
          }
          if (chm == "EMPORTER")
          {
            document.getElementById("blemporter").style.display = "block";
            document.getElementById("lemporter").style.display = "none";
            document.getElementById("lblemporter").style.display = "inline-block";
            document.getElementById("spdemporter").style.display = "block";
            document.getElementById("pdemporter").style.display = "block";
            document.getElementById("pdemporter").innerHTML = cmemp;
            document.getElementById("bllivrer").style.display = "none";
            document.getElementById("llivrer").style.display = "none";
            document.getElementById("lbllivrer").style.display = "none";
            document.getElementById("spdlivrer").style.display = "none";
            document.getElementById("pdlivrer").style.display = "none";
          }
          if (chm == "LIVRER")
          {
            document.getElementById("blemporter").style.display = "none";
            document.getElementById("lemporter").style.display = "none";
            document.getElementById("lblemporter").style.display = "none";
            document.getElementById("spdemporter").style.display = "none";
            document.getElementById("pdemporter").style.display = "none";
            document.getElementById("bllivrer").style.display = "block";
            document.getElementById("llivrer").style.display = "none";
            document.getElementById("lbllivrer").style.display = "inline-block";
            document.getElementById("spdlivrer").style.display = "block";
            document.getElementById("pdlivrer").style.display = "block";
            document.getElementById("pdlivrer").innerHTML = cmlivr;
          }
          if (verifcp > 0)
          {
            document.getElementById("lecp").onkeyup = function () {
              checkcp(this);
            };
            
          }
          else
          {
            document.getElementById("lecp").onkeyup = "";
            document.getElementById("lecp").setAttribute("data-inrange", "ok");
          }
          document.getElementById("separationid").style.display = "block";
          if (chp == "TOUS")
          {
            document.getElementById("blcomptant").style.display = "block";
            document.getElementById("pcomptant").style.display = "inline-block";
            document.getElementById("lblpcomptant").style.display = "inline-block";
            document.getElementById("spdcomptant").style.display = "block";
            document.getElementById("pdcomptant").style.display = "ineline-block";
            document.getElementById("pdcomptant").innerHTML = cmpt;
            document.getElementById("bllivraison").style.display = "block";
            document.getElementById("plivraison").style.display = "inline-block";
            document.getElementById("lbllivraison").style.display = "inline-block";
            document.getElementById("spdlivraison").style.display = "block";
            document.getElementById("pdlivraison").style.display = "inline-block";
            document.getElementById("pdlivraison").innerHTML = livr;
          }
          if (chp == "COMPTANT")
          {
            document.getElementById("blcomptant").style.display = "block";
            document.getElementById("pcomptant").style.display = "none";
            document.getElementById("lblpcomptant").style.display = "inline-block";
            document.getElementById("spdcomptant").style.display = "block";
            document.getElementById("pdcomptant").style.display = "block";
            document.getElementById("pdcomptant").innerHTML = cmpt;
            document.getElementById("bllivraison").style.display = "none";
            document.getElementById("plivraison").style.display = "none";
            document.getElementById("lbllivraison").style.display = "none";
            document.getElementById("spdlivraison").style.display = "none";
            document.getElementById("pdlivraison").style.display = "none";
          }
          if (chp == "LIVRAISON")
          {
            document.getElementById("blcomptant").style.display = "none";
            document.getElementById("pcomptant").style.display = "none";
            document.getElementById("lblpcomptant").style.display = "none";
            document.getElementById("spdcomptant").style.display = "none";
            document.getElementById("pdcomptant").style.display = "none";
            document.getElementById("bllivraison").style.display = "block";
            document.getElementById("plivraison").style.display = "none";
            document.getElementById("lbllivraison").style.display = "inline-block";
            document.getElementById("spdlivraison").style.display = "block";
            document.getElementById("pdlivraison").style.display = "block";
            document.getElementById("pdlivraison").innerHTML = livr;
          }
        }
        else 
        {
          document.getElementById("blocnomid").style.display = "none";
          document.getElementById("blocprenomid").style.display = "none";
          document.getElementById("met").style.display = "none";
          document.getElementById("separationid").style.display = "none";
          document.getElementById("paye").style.display = "none";
        }
        reachBottom();
        initctrl();
        if (verifcp > 0)
          checkcp(document.getElementById("lecp"));
        document.getElementById("footer").style.visibility = "visible";
        document.getElementById("loadid").style.display = "none";
        document.getElementById("main").style.display = "block";
        document.getElementById("header").style.display = "block";
      }

      // Appel asynchrone pour savoir si on est dans le périmètre de livraison 
      function checkcp(elem)
      {
        var retour;

        if (elem.value.length == 5)
        { 
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
    <script type="text/javascript">
   		// Appel asynchrone pour connaitre le montant de la remise
      function getRemise(sstotal, elem)
      {
        var retour;

        if (elem.validity.valid == true)
        {
          var obj = { sstotal: sstotal, customer: customer, code: elem.value };
          fetch("remise.php", {
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
            if (ret > 0)
            {
              document.getElementById("remiseid").innerHTML = 'Montant de la remise : ' + ret.toFixed(2) + ' €';
              sessionStorage.setItem("remise", data);
            }
            else {
              removeRemise();
            }
          })
        }
        else {
          removeRemise();
        }
      }
    </script>
    <script type="text/javascript">
      function removeRemise()
      {
        document.getElementById("remiseid").innerHTML = '';
        sessionStorage.setItem("remise", 0);
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
        document.getElementById("adrlivr").style.display = etat ? "none" : "block";
      }
    </script>
    <script type="text/javascript" >
      function initctrl()
      {
        // Affiche la page avec les contrôles par defaut  
        sessionStorage.setItem("fraislivr", 0);
        sessionStorage.setItem("remise", 0);
        var verifcp = document.getElementById("main").getAttribute("data-verifcp");
        document.getElementById("letel").value = sessionStorage.getItem("telephone");
        if (parseInt(method)>2)
        {
          document.getElementById("lenom").value = sessionStorage.getItem("nom");    
          document.getElementById("leprenom").value = sessionStorage.getItem("prenom");
          if (chp == "TOUS")
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
          if (chm == "TOUS")
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
          if (chm == "LIVRER")
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
          if (chm == "EMPORTER")
          {
          	removeFraisLivraison();
            eraseAdrLivr(true);
          }
  
        }
        document.getElementById("lecodepromo").value = sessionStorage.getItem("codepromo");
        getRemise(sessionStorage.getItem("sstotal"), document.getElementById("lecodepromo"));
        document.getElementById("infosup").value = sessionStorage.getItem("infosup");
      }
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
      window.addEventListener("resize", function() {
        reachBottom();
      })
    </script>
    <script type="text/javascript" >
    	// Sauvegarde les valeurs de la pages
      function bakInfo()
      {
      	sessionStorage.setItem("telephone", document.getElementById("letel").value);
        if (parseInt(method)>2)
        {
          sessionStorage.setItem("nom", document.getElementById("lenom").value);
          sessionStorage.setItem("prenom", document.getElementById("leprenom").value);
          sessionStorage.setItem("adresse1", document.getElementById("ladresse1").value);
          sessionStorage.setItem("adresse2", document.getElementById("ladresse2").value);
          sessionStorage.setItem("codepostal", document.getElementById("lecp").value);
          sessionStorage.setItem("ville", document.getElementById("laville").value);
          if (chp == "TOUS")
          {
            if (document.getElementById("pcomptant").checked == true)
              sessionStorage.setItem("choice", "COMPTANT");
            else if (document.getElementById("plivraison").checked == true)
              sessionStorage.setItem("choice", "LIVRAISON");
            else 
              sessionStorage.setItem("choice", "NONE");
          }
          else
            sessionStorage.setItem("choice", chp);
          if (chm == "TOUS")
          {
            if (document.getElementById("lemporter").checked == true)
              sessionStorage.setItem("choicel", "EMPORTER");
            else if (document.getElementById("llivrer").checked == true)
              sessionStorage.setItem("choicel", "LIVRER");
            else 
              sessionStorage.setItem("choicel", "NONE");
          }
          else
            sessionStorage.setItem("choicel", chm);

	        if (sessionStorage.getItem("choicel") == "LIVRER")
	          getFraisLivraison(sessionStorage.getItem("sstotal"));
	        else
  	      	removeFraisLivraison();
  	    }
  	    sessionStorage.setItem("codepromo", document.getElementById("lecodepromo").value);
        sessionStorage.setItem("infosup", document.getElementById("infosup").value);
      }
    </script>
    <script type="text/javascript" >
    
      function isHidden(el) {
        return (el.offsetParent === null)
      }
      // Vérifie que les infos nécessaires ont été saisi pour quitter le formulaire
      function checkInfo() 
      {
        var failed = false;
        
        bakInfo();
        if (parseInt(method)>2)
        {
	        if (sessionStorage.getItem("choicel") == "LIVRER") {
	          if ( document.getElementById("lecp").getAttribute("data-inrange") !== "ok") {
	            alert("Vous n\'êtes pas situé dans notre zone de livraison, vous devez venir chercher votre commande à notre boutique " + adr);
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
          if ((document.forms["mainform"][j].checkValidity() == false) && (failed == false) && (isHidden(document.forms["mainform"][j]) == false))
          {
            alert(document.forms["mainform"][j].name + " : " + document.forms["mainform"][j].validationMessage);
            failed = true;
          }
        }
        
        if ((document.getElementById("lecodepromo").checkValidity() == false) && (failed == false))
        {
          alert(document.getElementById("lecodepromo").name + " : " + document.getElementById("lecodepromo").validationMessage);
          failed = true;
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
