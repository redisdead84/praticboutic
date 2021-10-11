<?php

		session_start();		
		
    if (empty($_SESSION['superadmin_auth']) == TRUE)
    {
   	  header("LOCATION: index.php");
   	  exit();
    }	
    
    if (strcmp($_SESSION['superadmin_auth'],'superadmin') != 0)
	  {
   	  header("LOCATION: index.php");
   	  exit();
	  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <form autocomplete="off" method="post" action="configuration.php?identif=<?php echo $_GET['identif']; ?>">
    <div class="main">
    <p>
      <label for="ishtml">Format du courriel : </label><br>
		  <select name="ishtml" id="ishtmlid">
    		<option value="1" selected>HTML</option>
		    <option value="0">Texte</option>
		  </select><br>
		  <label>Sujet du courriel : </label><br>
      <input type="string" id="subjectid" name="subject"><br>
      <label for="validsms">Validation par sms : </label><br>
		  <select name="validsms" id="validsmsid">
    		<option value="1" selected>Oui</option>
		    <option value="0">Non</option>
		  </select><br>
      <label for="verifcp">Verification code postal : </label><br>
		  <select name="verifcp" id="verifcpid">
		    <option value="0" selected>Non</option>
    		<option value="1">Oui</option>
		  </select><br>
      <label for="paiement">Choix de la méthode de paiement : </label><br>
		  <select name="paiement" id="paiementid">
    		<option value="TOUS" selected>COMPTANT ET LIVRAISON</option>
		    <option value="LIVRAISON">LIVRAISON</option>
		    <option value="COMPTANT">COMPTANT</option>
		  </select><br>
		  <label>Texte du paiement à livraison : </label><br>
      <input type="string" id="livrid" name="livr" value="Paiement à la livraison"><br>
		  <label>Texte du paiement comptant : </label><br>
      <input type="string" id="comptid" name="compt" value="Prochain écran par CB"><br>
      <label for="vente">Choix de la méthode de vente : </label><br>
		  <select name="vente" id="venteidid">
    		<option value="TOUS" selected>EMPORTER ET LIVRER</option>
		    <option value="LIVRER">LIVRER</option>
		    <option value="EMPORTER">EMPORTER</option>
		  </select><br>
		  <label>Texte de la vente par livraison : </label><br>
      <input type="string" id="livrerid" name="livrer" value="Livraison Standard"><br>
		  <label>Texte de la vente à emporter : </label><br>
      <input type="string" id="emporterid" name="emporter" value="Retrait standard"><br>
		  <label>Montant minimum des commandes : </label><br>
      <input type="string" id="minicmdid" name="minicmd" value="0"><br>
		  <label>Montant minimum des livraisons : </label><br>
      <input type="string" id="minilivrid" name="minilivr" value="0"><br>
      <label for="sizeimg">Format des miniatures : </label><br>
		  <select name="sizeimg" id="sizeimgid">
    		<option value="smallimg" selected>PETITE</option>
		    <option value="bigimg">GRANDE</option>
		  </select><br>
      <label for="syspaie">Système de paiement</label><br>
		  <select name="syspaie" id="syspaieid">
    		<option value="STRIPE" selected>STRIPE</option>
		    <option value="PAYPAL">PAYPAL</option>
		  </select><br>
		  <label>Clé public Stripe : </label><br>
      <input type="string" id="pkeyid" name="pkey" value=""><br>
		  <label>Clé secrète Stripe : </label><br>
      <input type='password' id="skeyid" name="skey" value="" autocomplete="one-time-code"><br>
		  <label>Identifiant Client Paypal: </label><br>
      <input type="string" id="clientppid" name="clientpp" value=""><br>
    </p>
   <input class="inpmove" type="submit" value="Valider"><br>
   </div>
   </form>
 </body>
</html>
