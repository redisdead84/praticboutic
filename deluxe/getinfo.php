<?php

  include "config/config.php";
  include "param.php";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $bdd);
  // Check connection
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);    

  $method = isset($_GET ['method']) ? $_GET ['method'] : '0';
  $table = isset($_GET ['table']) ? $_GET ['table'] : '0';

  session_start();
  
  if (strcmp($_SESSION['mail'],'oui') == 0)
  {
    header('LOCATION: carte.php?method=' . $method . '&table=' . $table);
    exit();
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Prise d'information</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css?v=1.21">
    <link rel="stylesheet" href="css/custom.css?v=1.21">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <div id="main">
    <?php
      
      echo '<form name="mainform" autocomplete="off" method="post" action="paiement.php?method=';
      echo $method ;
      echo '&table=';
      echo $table ;
      echo '">';

      $verifcp = GetValeurParam("VerifCP",$conn);    
    
      $logo = GetValeurParam("master_logo",$conn);     
      echo '<img id="logo" src="' . $logo . '">';
      
      echo '<div id="grpinfo">';
      
      if ($method >= 2)
      {
        echo '<div id="livraison">Informations concernant la livraison</div>';
        echo '<br>';
        echo '<label class="lcont">Nom : </label>';
        echo '<input class="cont" type="string" id="lenom" name="nom" required>';
        echo '<br>';            
        echo '<label class="lcont">Pr&eacute;nom : </label>';
        echo '<input class="cont" type="string" id="leprenom" name="prenom" required>';
        echo '<br>';
        echo '<label class="lcont">T&eacute;l. Portable : </label>';
        echo '<input class="cont" type="string" id="letel" name="tel" required 
        pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[6-7](?:[\.\-\s]?\d\d){4}$" 
        title="Il faut un numéro de téléphone portable français valide">';
      }
      if ($method >= 2)
      {
        $chm = GetValeurParam("Choix_Method",$conn);         
       
        $cmemp = GetValeurParam("CM_Emporter",$conn); 
    
        $cmlivr = GetValeurParam("CM_Livrer",$conn);

        echo '<div id="met">';
        echo '<div id="model" data-permis="' . $chm . '">';
        echo 'Retrait :<br>';
        if ($chm == "TOUS")
        { 
          echo '<input class="paiers" type="radio" name="choixmeth" id="lemporter" value="EMPORTER" onclick="eraseAdrLivr(true)">';
          echo '<label for="lemporter">A EMPORTER : </label><br>';
          echo '<label>';
          echo $cmemp; 
          echo '</label><br>';
          echo '<input class="paiers" type="radio" name="choixmeth" id="llivrer" value="LIVRER" onclick="eraseAdrLivr(false)">';
          echo '<label for="llivrer">EN LIVRAISON : </label><br>';
          echo '<label>';
          echo $cmlivr;
          echo '</label><br>';
        }
        if ($chm == "EMPORTER")
        {
          echo '<label for="lemporter">A EMPORTER : </label><br>';
          echo '<label>';
          echo $cmemp; 
          echo '</label><br>';
        }  
        if ($chm == "LIVRER")
        {
          echo '<label for="llivrer">EN LIVRAISON : </label><br>';
          echo '<label>';
          echo $cmlivr;
          echo '</label><br>';
        }  
        echo '</div>';
        echo '</div>';
      }

      echo '<div id="adrlivr">';
      echo '<label class="lcont">Adresse 1 : </label>';
      echo '<input class="cont adrliv" type="string" id="ladresse1" name="adresse1" required>';
      echo '<br>';
      echo '<label class="lcont">Adresse 2 : </label>';
      echo '<input class="cont adrliv" type="string" id="ladresse2" name="adresse2">';
      echo '<br>';
      echo '<label class="lcont">Code Postal : </label>';
      if ($verifcp > 0) {
        echo '<input class="cont adrliv" type="string" id="lecp" name="cp" required 
          pattern="[0-9]{5}" title="Il faut un code postal français valide" onkeyup="checkcp(this)" data-inrange="ko">';
      } else {
        echo '<input class="cont adrliv" type="string" id="lecp" name="cp" required 
          pattern="[0-9]{5}" title="Il faut un code postal français valide" data-inrange="ok">';
      }
      echo '<br>';
      echo '<label class="lcont ">Ville : </label>';
      echo '<input class="cont adrliv" type="string" id="laville" name="ville" required>';
      echo '</div>';
        
      echo '</div>';
      echo '</form>';
   
      if  ($method >= 2)
      {
        $chp = GetValeurParam("Choix_Paiement",$conn);         
       
        $cmpt = GetValeurParam("MP_Comptant",$conn); 
    
        $livr = GetValeurParam("MP_Livraison",$conn);

        echo '<div id="paye">';
        echo '<div id="modep" data-permis="' . $chp . '">';
        echo 'Paiement :<br>';
        if ($chp == "TOUS")
        { 
          echo '<input class="paiers" type="radio" name="choixpaie" id="pcomptant" value="COMPTANT">';
          echo '<label for="pcomptant">AU COMPTANT : </label><br>';
          echo '<label>';
          echo $cmpt; 
          echo '</label><br>';
          echo '<input class="paiers" type="radio" name="choixpaie" id="plivraison" value="LIVRAISON">';
          echo '<label for="plivraison">A LA LIVRAISON : </label><br>';
          echo '<label>';
          echo $livr;
          echo '</label><br>';
        }
        if ($chp == "COMPTANT")
        {
          echo '<label for="pcomptant">AU COMPTANT : </label><br>';
          echo '<label>';
          echo $cmpt; 
          echo '</label><br>';
        }  
        if ($chp == "LIVRAISON")
        {
          echo '<label for="plivraison">A LA LIVRAISON : </label><br>';
          echo '<label>';
          echo $livr;
          echo '</label><br>';
        }  
        echo '</div>';
        echo '</div>';
      }
      echo '<div id="cgv">Vous pouvez consulter <a id="cgvlink" href="javascript:bakInfo();window.location.href = \'CGV.php?method=' . $method . '&table=' . $table .  '\'">nos conditions générales de vente</a></div>';
  
    ?>
    <textarea id="infosup" name="infosup" placeholder="Informations supplémentaires (date, heure, code interphone, ...)"></textarea>
    <div id="pan">
      <br>
<!--      <a id="methodid"></a><br>-->
      <a id="tableid"></a><br>
      <div id="commandediv"></div><br>
      <a id="sommeid"></a><br>
      <br>
    </div>
    </div>    
    <div id="footer">
      <?php
        if  ($method > 0)
        {
            echo '<input class="inpmove poursuivre" type="button" value="Poursuivre la commande" onclick="checkInfo()">';
            echo '<input class="inpmove revenir" type="button" value="Revenir sur la commande" onclick="bakInfo();';
            echo 'window.location.href = \'carte.php?method=';
            echo $method;
            echo '&table=';
            echo $table;
            echo '\'"';
        }
      ?>
    </div>
    <script type="text/javascript">
   
      function checkcp(elem)      
      {
        var retour;      
      
        if (elem.value.length == 5)
        {   
      
          fetch("cpzone.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(elem.value)
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
    <script type="text/javascript" >
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
      /*if (document.getElementById("model").getAttribute("data-permis") == "LIVRER")
        eraseAdrLivr(false);
      else {
      	
      }*/
    </script>
    <script type="text/javascript" >
        
      if (sessionStorage.getItem("method")>=2)
      {
        document.getElementById("lenom").value = sessionStorage.getItem("nom");    
        document.getElementById("leprenom").value = sessionStorage.getItem("prenom");
        document.getElementById("letel").value = sessionStorage.getItem("telephone");
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
            eraseAdrLivr(true);
          }
          else if (sessionStorage.getItem("choicel") == "LIVRER")
          {
            document.getElementById("ladresse1").value = sessionStorage.getItem("adresse1");
            document.getElementById("ladresse2").value = sessionStorage.getItem("adresse2");
            document.getElementById("lecp").value = sessionStorage.getItem("codepostal");
            checkcp(document.getElementById("lecp"));
            document.getElementById("laville").value = sessionStorage.getItem("ville");
            document.getElementById("lemporter").checked = false;
            document.getElementById("llivrer").checked = true;
            eraseAdrLivr(false);
          } 
          else
          {
            document.getElementById("lemporter").checked = false;
            document.getElementById("llivrer").checked = false;
            eraseAdrLivr(true);
          } 
        }
        if (document.getElementById("model").getAttribute("data-permis") == "LIVRER")
        {
            document.getElementById("ladresse1").value = sessionStorage.getItem("adresse1");
            document.getElementById("ladresse2").value = sessionStorage.getItem("adresse2");
            document.getElementById("lecp").value = sessionStorage.getItem("codepostal");
            checkcp(document.getElementById("lecp"));
            document.getElementById("laville").value = sessionStorage.getItem("ville");
            eraseAdrLivr(false);
        }
        if (document.getElementById("model").getAttribute("data-permis") == "EMPORTER")
        {
            eraseAdrLivr(true);
        }

      }
      document.getElementById("infosup").value = sessionStorage.getItem("infosup");

    </script>
    <script type="text/javascript">
      function reachBottom()
      {
        var x = window.innerHeight - document.getElementById("footer").clientHeight;
        x = x + "px";
        document.getElementById("main").style.height = x;
      }
    </script>
    <script type="text/javascript">
      reachBottom();
    </script>
    <script type="text/javascript">
      window.addEventListener("resize", function() {
        reachBottom();
      })
    </script>
    <script type="text/javascript">
      var cart = JSON.parse(sessionStorage.getItem("commande"));
      var str = "";
      var somme = 0;
      str = str + "<table>"; 
      str = str + "<thead>";
      str = str + "<tr>";
      str = str + "<th>Article</th>";
      str = str + "<th>Prix</th>";
      str = str + "<th>Qté</th>";
      str = str + "<th>Total</th>";
      str = str + "</tr>";
      str = str + "</thead>";
      str = str + "<tbody>";
        for (var art in cart) {
          str = str + "<tr>";
          str = str + "<td>";
          str = str + cart[art].name;
          str = str + "</td>";
          str = str + "<td>";
          var ton_chiffre = parseFloat(cart[art].prix); // Ta variable de chiffre
          var ton_chiffre2 = ton_chiffre.toFixed(2); 
          str = str + ton_chiffre2 + " € ";
          str = str + "</td>";
          str = str + "<td>";
          str = str + cart[art].qt;
          str = str + "</td>";
          str = str + "<td>";
          str = str + (cart[art].qt * cart[art].prix).toFixed(2) + " € ";
          somme = somme + cart[art].qt * cart[art].prix;
          str = str + "</td>";

          str = str + "</tr>";
        }
      str = str + "</tbody>";
      str = str + "</table>"; 

      var method = sessionStorage.getItem("method");
      var method_txt = "";
      if (method == 1)
      {
        method_txt = "Consomation sur place";
        document.getElementById("methodid").innerHTML = method_txt + '<br>';
      } 
/*      if (method >= 2) 
        method_txt = "Vente à emporter ou à livrer";*/

      if (method == 1) 
      {
        document.getElementById("tableid").innerHTML = "Table numéro " + sessionStorage.getItem("table") + "<br>";
      }      
      document.getElementById("commandediv").innerHTML = str;
      document.getElementById("sommeid").innerHTML = "Prix total de la commande : " + somme.toFixed(2) + " € ";
    </script>
    <script type="text/javascript" >
      function bakInfo()
      {
        if (sessionStorage.getItem("method")>=2)
        {
          sessionStorage.setItem("nom", document.getElementById("lenom").value);
          sessionStorage.setItem("prenom", document.getElementById("leprenom").value);
          sessionStorage.setItem("telephone", document.getElementById("letel").value);
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
        }
        sessionStorage.setItem("infosup", document.getElementById("infosup").value);
      }
    </script>
    <script type="text/javascript" >
      function checkInfo() 
      {
        var failed = false;
        
        bakInfo();        
        
        if (sessionStorage.getItem("choicel") == "LIVRER") {
          if ( document.getElementById("lecp").getAttribute("data-inrange") !== "ok") {
            alert("Vous n\'êtes pas situé dans notre zone de livraison, vous devez venir chercher votre commande à Eulalie Poisonnerie, 5 Place Ferdinand Buisson, 84800 L'Isle-sur-la-Sorgue");
          }
          if (sessionStorage.getItem("choice") == "NONE") {
            alert("Vous n\'avez pas choisi comment régler la transaction, impossible de continuer");
            failed = true;
          }
          if (sessionStorage.getItem("choicel") == "NONE") {
            alert("Vous n\'avez pas choisi comment la vente aller se dérouler, impossible de continuer");
            failed = true;
          }

        }
        
        for (var j=0; j < document.forms["mainform"].length; j++)
        {
          if (document.forms["mainform"][j].checkValidity() == false)
          {
            alert(document.forms["mainform"][j].name + " : " + document.forms["mainform"][j].validationMessage);
            failed = true;
          }
        }
        if (failed == false)
          document.forms["mainform"].submit();
      }
    </script>
  </body>
</html>
