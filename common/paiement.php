<?php
  session_start();
  $customer = htmlspecialchars($_GET['customer']);

  include "config/common_cfg.php";
  include "param.php";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $bdd);
  // Check connection
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);    

  $method = htmlspecialchars(isset($_GET ['method']) ? $_GET ['method'] : '0');
  $table = htmlspecialchars(isset($_GET ['table']) ? $_GET ['table'] : '0');

  if (empty($_SESSION[$customer . '_mail']) == TRUE)
  {
    header('LOCATION: ../' . $customer . '/index.php');
    exit();
  }
  
  if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
  {
    header('LOCATION: carte.php?method=' . $method . '&table=' . $table . '&customer=' . $customer);
    exit();
  }

  $reqci = $conn->prepare('SELECT customid, logo FROM customer WHERE customer = ?');
  $reqci->bind_param("s", $customer);
  $reqci->execute();
  $reqci->bind_result($customid, $logo);
  $resultatci = $reqci->fetch();
  $reqci->close();
  
  $mnysys = GetValeurParam("MONEY_SYSTEM", $conn, $customid, "STRIPE");
  $idcpp = GetValeurParam("ID_CLT_PAYPAL", $conn, $customid);
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <title>Validation de la commande</title>
    <meta name="description" content="PraticBoutic formulaire de paiement" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" media="screen" href="css/style2.css?v=<?php echo $ver_com_css;?>" />
    <link rel="stylesheet" href="css/global.css?v=<?php echo $ver_com_css;?>" />
    <!--<link rel="stylesheet" href="css/style.css?v=1.22" />-->
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
		<?php    
    	if (strcmp($mnysys, "STRIPE") == 0)
    	{
    		echo '<script src="https://js.stripe.com/v3/"></script>';
				echo '<script src="js/client.js?v=1.26" defer></script>';
			}    		 
    ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	  <script type="text/javascript" src="js/bandeau.js?v=1.01"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <?php
 	    
    $pkey = GetValeurParam("PublicKey", $conn, $customid);
    echo '<div id="header">';
		echo '<img id="mainlogo" src="img/logo-pratic-boutic.png">';
		echo '</div>';		

    echo '<div id="main" data-publickey="' . $pkey . '">';
    
    echo '<img id="logo" src="../' . $customer . '/upload/' . $logo . '">';
    
    ?>
	   	<div id="pan">
	     	<div id="methodid"></div>
	      <div id="tableid"></div>
	      <div id="commandediv"></div>
	      <div class="fraistotal" id="sstotalid"></div>
	      <div class="fraistotal" id="fraislivid"></div>
				<div class="fraistotal mbot" id="totalid"></div>
				<div class="fpay" id="payid"></div>
	      <br>
	    </div>
    </div>

		<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $idcpp;?>&currency=EUR"> // Replace YOUR_CLIENT_ID with your sandbox client ID
   	</script>
    <!-- Display a payment form -->
    <script type="text/javascript">
    	var mnysys = "<?php echo $mnysys;?>";
      if ((sessionStorage.getItem("method")==3) && (sessionStorage.getItem("choice")=="COMPTANT")) {
    		if (mnysys == "STRIPE")
    		{
    			document.write('<div id="payementfooter" style="height:225px">');
          document.write('<form class="frm" id="payment-form">');
          document.write('<div id="card-element"><!--Stripe.js injects the Card Element--></div>');
          document.write('<button class="btn" id="submit">');
          document.write('<div class="spinner hidden" id="spinner"></div>');
          document.write('<span id="button-text">Payer</span>');
          document.write('</button>');
          document.write('<div class="intercalaire">');
          document.write('<p id="card-error" role="alert"></p>');
          document.write('<p class="result-message hidden">');
          document.write('Paiement effectué<!--, Voyez le résultat dans votre');
          document.write('<a href="" target="_blank">interface Stripe.</a> Rafraichisser la page pour payer encore-->.');
          document.write('</p>');
          document.write('</div>');
          document.write('</form>');
     		}
     		else if (mnysys == "PAYPAL")
     		{
     			document.write('<div id="payementfooter" style="height:140px">');
      		document.write('<div id="paypal-button-container"></div>');
      	}
        document.write('<div class="solobn">');
        document.write('<button class="navindicsolo" id="retourcarte" onclick="window.location.href = \'getinfo.php?method=' + sessionStorage.getItem("method") + '&table=' + sessionStorage.getItem("table") + '&customer=' + sessionStorage.getItem("customer") + '\'">');
				document.write('Revenir sur les informations');
      	document.write('</button>');
      	document.write('</div>');
     		document.write('</div>');
    	} else {
      	document.write('<div id="footer">');
        document.write('<div class="grpbn">');
        document.write('<button class="navindic" id="retourcarte" onclick="window.location.href = \'getinfo.php?method=' + sessionStorage.getItem("method") + '&table=' + sessionStorage.getItem("table") + '&customer=' + sessionStorage.getItem("customer") + '\'">');
      	document.write('Retour');
      	document.write('</button>');
        document.write('<button class="navindic" id="validcarte" onclick="window.location.href = \'fin.php?method=' + sessionStorage.getItem("method") + '&table=' + sessionStorage.getItem("table") + '&customer=' + sessionStorage.getItem("customer") + '\'">');
        document.write('Valider');
        document.write('</button>');
        document.write('</div>');
        document.write('</div>');
      }
    </script>
	  <script>
	    if ((sessionStorage.getItem("method")==3) && (sessionStorage.getItem("choice")=="COMPTANT")) {
	    	if (mnysys == "PAYPAL") 
	    	{
	    		if (sessionStorage.getItem("choicel")=="LIVRER")
	    		{
			      paypal.Buttons({
			      	enableStandardCardFields: false,
							style: {
								layout: 'horizontal',
	 							tagline: 'false'
							},		      	
			      	createOrder: function(data, actions) {
						    return actions.order.create({
						    	enableStandardCardFields: false,
						      intent: 'CAPTURE',
						      payer: {
						        name: {
						          given_name: sessionStorage.getItem("nom"),
						          surname: sessionStorage.getItem("prenom")
						        },
						        address: {
						          address_line_1: sessionStorage.getItem("adresse1"),
						          address_line_2: sessionStorage.getItem("adresse2"),
						          admin_area_2: sessionStorage.getItem("ville"),
						          admin_area_1: '',
						          postal_code: sessionStorage.getItem("codepostal"),
						          country_code: 'FR'
						        },
						        //email_address: "",
						        phone: {
						          phone_type: "MOBILE",
						          phone_number: {
						            national_number: sessionStorage.getItem("telephone")
						          }
						        }
						      },
					      	purchase_units: [
						        {
						          amount: {
						            value: (parseFloat(sessionStorage.getItem("sstotal")) + parseFloat(sessionStorage.getItem("fraislivr"))).toString(),
						            currency_code: 'EUR'
						          },
						          shipping: {
						            address: {
							          address_line_1: sessionStorage.getItem("adresse1"),
						  	        address_line_2: sessionStorage.getItem("adresse2"),
						    	      admin_area_2: sessionStorage.getItem("ville"),
						      	    admin_area_1: '',
						        	  postal_code: sessionStorage.getItem("codepostal"),
						          	country_code: 'FR'
						            }
						          },
						          experience: {
								        input_fields: {
						    		      no_shipping: 1
						        		}
						      		}
						        }
					      	],
					    	});
					  	},
					  	onApprove: function(data, actions) {
			      	// This function captures the funds from the transaction.
				 	    	return actions.order.capture().then(function(details) {
		    	    		// This function shows a transaction success message to your buyer.
		        			document.location.href = 'fin.php?method=' + sessionStorage.getItem("method") + '&table=' + sessionStorage.getItem("table") + '&customer=' + sessionStorage.getItem("customer");
				      	});
				    	}
						}).render('#paypal-button-container'); // Display payment options on your web page
					}
					else if (sessionStorage.getItem("choicel")=="EMPORTER")
					{
			      paypal.Buttons({
			      	enableStandardCardFields: false,
							style: {
								layout: 'horizontal',
	 							tagline: 'false'
							},		      	
			      	createOrder: function(data, actions) {
						    return actions.order.create({
						    	enableStandardCardFields: false,
						      intent: 'CAPTURE',
						      payer: {
						        name: {
						          given_name: sessionStorage.getItem("nom"),
						          surname: sessionStorage.getItem("prenom")
						        },
						        //email_address: "",
						        phone: {
						          phone_type: "MOBILE",
						          phone_number: {
						            national_number: sessionStorage.getItem("telephone")
						          }
						        }
						      },
					      	purchase_units: [
						        {
						          amount: {
						            value: sessionStorage.getItem("sstotal"),
						            currency_code: 'EUR'
						          },
						          experience: {
								        input_fields: {
						    		      no_shipping: 1
						        		}
						      		}
						        }
					      	],
					    	});
					  	},
					  	onApprove: function(data, actions) {
			      	// This function captures the funds from the transaction.
				 	    	return actions.order.capture().then(function(details) {
		    	    		// This function shows a transaction success message to your buyer.
		        			document.location.href = 'fin.php?method=' + sessionStorage.getItem("method") + '&table=' + sessionStorage.getItem("table") + '&customer=' + sessionStorage.getItem("customer");
				      	});
				    	}
						}).render('#paypal-button-container'); // Display payment options on your web page
					}
				}
			}
	  </script>
    <script type="text/javascript">
      var cart = JSON.parse(sessionStorage.getItem("commande"));
      var str = "";
      var somme = 0;
    		
    	str = str + "<p class='pres'>Résumé de votre commande</p>";	
      str = str + "<table>"; 
      //str = str + "<theader>";
      str = str + "<colgroup>";
      str = str + "<col class='colart'>";
      str = str + "<col class='colstd'>";
      str = str + "<col class='colstd'>";
      str = str + "<col class='colprx'>";
      str = str + "</colgroup>";
      
      str = str + "<tr>";
      str = str + "<th class='colart'>Article</th>";
      str = str + "<th class='colstd'>Prix</th>";
      str = str + "<th class='colstd'>Qté</th>";
      str = str + "<th class='colprx'>Total</th>";
      str = str + "</tr>";
      //str = str + "</theader>";
      //str = str + "<tbody>";
        for (var art in cart) {
          str = str + "<tr>";
          str = str + "<td class='colart'>";
          str = str + cart[art].name;
          str = str + "</td>";
          str = str + "<td class='colstd'>";
          var ton_chiffre = parseFloat(cart[art].prix); // Ta variable de chiffre
          var ton_chiffre2 = ton_chiffre.toFixed(2); 
          str = str + ton_chiffre2 + " € ";
          str = str + "</td>";
          str = str + "<td class='colstd'>";
          str = str + cart[art].qt;
          str = str + "</td>";
          str = str + "<td class='colprx'>";
          str = str + (cart[art].qt * cart[art].prix).toFixed(2) + " € ";
          somme = somme + cart[art].qt * cart[art].prix;
          str = str + "</td>";
          str = str + "</tr>";
        }
      //str = str + "</tbody>";
      str = str + "</table>"; 

      var method = sessionStorage.getItem("method");
      var method_txt = "";
      if (method == 1)
      {
        method_txt = "Consomation sur place";
        document.getElementById("methodid").innerHTML = "<p class='pres'>" + method_txt + "</p>";
      } 
/*      if (method >= 2) 
        method_txt = "Vente à emporter ou à livrer";*/

      if (method == 1) 
      {
        document.getElementById("tableid").innerHTML = "<p class='pres'>Table numéro " + sessionStorage.getItem("table") + "</p>";
      }      
      document.getElementById("commandediv").innerHTML = str;
      var frliv = 0;
      if (method >= 2) 
				frliv = parseFloat(sessionStorage.getItem("fraislivr"));
			var tota = frliv + somme;      
      
			if ((sessionStorage.getItem("choicel") == "LIVRER") && (method >= 2))
			{
	      document.getElementById("sstotalid").innerHTML = "<p class='fleft'>Sous-total : </p><p class='fright'>" + somme.toFixed(2) + " € </p><br>";
	 	    document.getElementById("fraislivid").innerHTML = "<p class='fleft'>Frais de livraison : </p><p class='fright'>" + frliv.toFixed(2) + " € </p><br>";
	      document.getElementById("totalid").innerHTML = "<p class='wbld fleft'>Total de la commande : </p><p class='wbld fright'>" + tota.toFixed(2) + " € </p><br>";

			}
			else if ((sessionStorage.getItem("choicel") == "EMPORTER") || (method == 1))
			{
				document.getElementById("sstotalid").style.display = "none";
				document.getElementById("fraislivid").style.display = "none";
	      document.getElementById("totalid").innerHTML = "<p class='wbld fleft'>Total de la commande : </p><p class='wbld fright'>" + somme.toFixed(2) + " € </p><br>";
			}      
			if ((sessionStorage.getItem("method")>=2) && (sessionStorage.getItem("choice")=="COMPTANT"))
				document.getElementById("payid").innerHTML = "<p class='mntpay'>MONTANT &Agrave; PAYER : " + tota.toFixed(2) + " € </p>";
			else {
				document.getElementById("payid").style.display = "none";
			}
    </script>

    <script type="text/javascript">
      function reachBottom() 
      {
      	var x;
      	if ((sessionStorage.getItem("method")==3) && (sessionStorage.getItem("choice")=="COMPTANT"))
      	  x = window.innerHeight - document.getElementById("payementfooter").clientHeight - document.getElementById("header").clientHeight;
      	else
      	  x = window.innerHeight - document.getElementById("footer").clientHeight - document.getElementById("header").clientHeight;

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
  </body>
</html>
