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
      <label>Courriel de réception des commandes : </label>
      <input type="string" id="rcvemailid" name="rcvemail" value="redemptateur@hotmail.com"><br><br>
		  <label>Receveur des commandes : </label>
      <input type="string" id="rcvnomid" name="rcvnom" value="Ma PraticBoutic"><br> <br> 
      <label for="ishtml">Format du courriel : </label>
		  <select name="ishtml" id="ishtmlid">
    		<option value="TRUE" selected>HTML</option>
		    <option value="FALSE">Texte</option>
		  </select><br><br>
		  <label>Sujet du courriel : </label>
      <input type="string" id="subjectid" name="subject"><br><br>
      <label for="validsms">Validation par sms : </label>
		  <select name="validsms" id="validsmsid">
    		<option value="1">Oui</option>
		    <option value="0">Non</option>
		  </select><br><br>
		  <label>Texte SMS : </label>
      <input type="string" id="textsmsid" name="textsms" value="Vote commande chez %commercant% d'un montant de %somme% € a été transmise."><br><br>
		  <label>Adresse : </label>
      <input type="string" id="adrid" name="adr" value=""><br><br>
      <label for="verifcp">Verification code postal : </label>
		  <select name="verifcp" id="verifcpid">
		    <option value="0" selected>Non</option>
    		<option value="1">Oui</option>
		  </select><br><br>
      <label for="paiement">Choix de la méthode de paiement : </label>
		  <select name="paiement" id="paiementid">
    		<option value="TOUS" selected>COMPTANT ET LIVRAISON</option>
		    <option value="LIVRAISON">LIVRAISON</option>
		    <option value="COMPTANT">COMPTANT</option>
		  </select><br><br>
		  <label>Texte du paiement à livraison : </label>
      <input type="string" id="livrid" name="livr" value="Paiement à la livraison"><br><br>
		  <label>Texte du paiement comptant : </label>
      <input type="string" id="comptid" name="compt" value="Prochain écran par CB"><br><br>
      <label for="vente">Choix de la méthode de vente : </label>
		  <select name="vente" id="venteidid">
    		<option value="TOUS" selected>EMPORTER ET LIVRER</option>
		    <option value="LIVRER">LIVRER</option>
		    <option value="EMPORTER">EMPORTER</option>
		  </select><br><br>
		  <label>Texte de la vente par livraison : </label>
      <input type="string" id="livrerid" name="livrer" value="Livraison Standard"><br><br>
		  <label>Texte de la vente à emporter : </label>
      <input type="string" id="emporterid" name="emporter" value="Retrait standard"><br><br>
		  <label>Montant minimum des commandes : </label>
      <input type="string" id="minicmdid" name="minicmd" value="0"><br><br>
		  <label>Montant minimum des livraisons : </label>
      <input type="string" id="minilivrid" name="minilivr" value="0"><br><br>
      <label for="sizeimg">Format des miniatures : </label>
		  <select name="sizeimg" id="sizeimgid">
    		<option value="smallimg" selected>PETITE</option>
		    <option value="bigimg">GRANDE</option>
		  </select><br><br>
		  <label>Clé public Stripe : </label>
      <input type="string" id="pkeyid" name="pkey" value=""><br><br>
		  <label>Clé secrète Stripe : </label>
      <input type="string" id="skeyid" name="skey" value=""><br><br>
    </p>
   <input class="inpmove" type="submit" value="Valider"><br><br>
   </div>
   </form>
 </body>
</html>
