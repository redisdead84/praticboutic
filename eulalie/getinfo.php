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
    <link rel="stylesheet" href="css/style.css?v=1.0">
    <link rel="stylesheet" href="css/custom.css?v=1.01">
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
      
      if ($method == '3')
      {
        echo '<div id="livraison">Adresse de livraison</div>';
        echo '<br>';
        echo '<label class="lcont">Nom : </label>';
        echo '<input class="cont" type="string" id="lenom" name="nom" required>';
        echo '<br>';            
      }
      if (($method == '3') ||($method == '2'))
      {
        echo '<label class="lcont">Pr&eacute;nom : </label>';
        echo '<input class="cont" type="string" id="leprenom" name="prenom" required>';
      }
      if ($method == '3')
      {
        echo '<br>';
        echo '<label class="lcont">Adresse 1 : </label>';
        echo '<input class="cont" type="string" id="ladresse1" name="adresse1" required>';
        echo '<br>';
        echo '<label class="lcont">Adresse 2 : </label>';
        echo '<input class="cont" type="string" id="ladresse2" name="adresse2">';
        echo '<br>';
        echo '<label class="lcont">Code Postal : </label>';
        if ($verifcp > 0) {
          echo '<input class="cont" type="string" id="lecp" name="cp" required 
          pattern="[0-9]{5}" title="Il faut un code postal français valide" onkeyup="checkcp(this)" data-inrange="ko">';
        } else {
          echo '<input class="cont" type="string" id="lecp" name="cp" required 
          pattern="[0-9]{5}" title="Il faut un code postal français valide" data-inrange="ok">';
        }
        echo '<br>';
        echo '<label class="lcont">Ville : </label>';
        echo '<input class="cont" type="string" id="laville" name="ville" required>';
        echo '<br>';
        echo '<label class="lcont">T&eacutel&eacutephone : </label>';
        echo '<input class="cont" type="string" id="letel" name="tel" required 
        pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$" 
        title="Il faut un numéro de téléphone français valide">';
      }
  
      echo '</div>';
      echo '</form>';
  
      if  ($method == 3)
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
    <div id="pan">
      <br>
      <a id="methodid"></a><br>
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
        
      if (sessionStorage.getItem("method")==3)
        document.getElementById("lenom").value = sessionStorage.getItem("nom");    
      if ((sessionStorage.getItem("method")==3) || (sessionStorage.getItem("method")==2)) 
        document.getElementById("leprenom").value = sessionStorage.getItem("prenom");
      if (sessionStorage.getItem("method")==3)
      {
        document.getElementById("ladresse1").value = sessionStorage.getItem("adresse1");
        document.getElementById("ladresse2").value = sessionStorage.getItem("adresse2");
        document.getElementById("lecp").value = sessionStorage.getItem("codepostal");
        checkcp(document.getElementById("lecp"));
        document.getElementById("laville").value = sessionStorage.getItem("ville");
        document.getElementById("letel").value = sessionStorage.getItem("telephone");
      }
      if (sessionStorage.getItem("method")==3)
      {
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
      }

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
        method_txt = "Consomation sur place";
      if (method == 2) 
        method_txt = "Vente à emporter";
      if (method == 3) 
        method_txt = "Vente en livraison";

      document.getElementById("methodid").innerHTML = method_txt + '<br>';
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
        if (sessionStorage.getItem("method")==3)
          sessionStorage.setItem("nom", document.getElementById("lenom").value);
        if ((sessionStorage.getItem("method")==2)||(sessionStorage.getItem("method")==3))
          sessionStorage.setItem("prenom", document.getElementById("leprenom").value);
        if (sessionStorage.getItem("method")==3)
        {
          sessionStorage.setItem("adresse1", document.getElementById("ladresse1").value);
          sessionStorage.setItem("adresse2", document.getElementById("ladresse2").value);
          sessionStorage.setItem("codepostal", document.getElementById("lecp").value);
          sessionStorage.setItem("ville", document.getElementById("laville").value);
          sessionStorage.setItem("telephone", document.getElementById("letel").value);
        }
        if (sessionStorage.getItem("method")==3)
        {
          
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
        }
      }
    </script>
    <script type="text/javascript" >
      function checkInfo() 
      {
        var failed = false;
        
        bakInfo();        
        
        if (sessionStorage.getItem("method")==3) {
          if ( document.getElementById("lecp").getAttribute("data-inrange") !== "ok") {
            alert("Vous n\'êtes pas situé dans notre zone de livraison, vous devez venir chercher votre commande à Eulalie Poisonnerie, 5 Place Ferdinand Buisson, 84800 L'Isle-sur-la-Sorgue");
          }
          if (sessionStorage.getItem("choice") == "NONE") {
            alert("Vous n\'avez pas choisi comment régler la transaction, impossible de continuer");
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