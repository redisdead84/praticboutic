<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.12">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <div class="modal" tabindex="-1" role="dialog" data-backdrop="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">ERREUR</h5>
          </div>
          <div class="modal-body">
            <?php 
              session_start();
              
              if (empty($_SESSION['verify_email']) == TRUE)
              {
                header("LOCATION: index.php");
                exit();
              }
                
              require_once '../../vendor/autoload.php';
              include "../config/common_cfg.php";
              include "../param.php";
              try
              {
                // Create connection
                $conn = new mysqli($servername, $username, $password, $bdd);
                
                $conn->autocommit(false);
                // Check connection
                if ($conn->connect_error) 
                {
                  throw new Error("Connection failed: " . $conn->connect_error);
                }
                
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
                $dotenv->load();
                
                $output ="";

                // Initialize the Stripe client 
                // For sample support and debugging. Not required for production:
                \Stripe\Stripe::setAppInfo(
                  "pratic-boutic/subscription/fixed-price",
                  "0.0.2",
                  "https://praticboutic.fr"
                );

                $stripe = new \Stripe\StripeClient([
                // TODO replace hardcoded apikey by env variable
                  'api_key' => $_ENV['STRIPE_SECRET_KEY'],
                  'stripe_version' => '2020-08-27',
                ]);

                $subquery = "SELECT count(*) FROM `client` WHERE email = '" . $_SESSION['verify_email'] . "'";

                if ($result = $conn->query($subquery)) 
                {
                  if ($row = $result->fetch_row()) 
                  {
                    if (intval($row[0])>0)
                      throw new Error("Impossible d'avoir plusieurs fois le même courriel " . $_SESSION['verify_email']);
                  }
                  $result->close();
                }

                $cpwd = password_hash($_SESSION['registration_pass'], PASSWORD_DEFAULT);
                $query = "INSERT INTO client(email, pass, qualite, nom, prenom, adr1, adr2, cp, ville, tel, stripe_customer_id, actif) VALUES ";
                $query = $query . "('" . $_SESSION['verify_email']  . "','" . $cpwd. "','" . $_SESSION['registration_qualite'] . "','" . addslashes($_SESSION['registration_nom']) . "','";
                $query = $query . addslashes($_SESSION['registration_prenom']) . "','" . addslashes($_SESSION['registration_adr1']) . "','" . addslashes($_SESSION['registration_adr2']) . "','" . addslashes($_SESSION['registration_cp']) . "','";
                $query = $query . addslashes($_SESSION['registration_ville']) . "','" . $_SESSION['registration_tel'] . "','" . $_SESSION['registration_stripe_customer_id'] . "','1')";
                
                //error_log($query);

                // remove following comments to enable writing in db
                if ($conn->query($query) === FALSE)
                {
                  throw new Error($conn->error);
                }
                
                $cltid = $conn->insert_id;
                
                if (empty($_SESSION['initboutic_aliasboutic'])==TRUE ) {
                  throw new Error("Identifiant vide");
                }
                
                $notid = array('admin', 'common', 'route', 'upload', 'vendor');
                if(in_array($_SESSION['initboutic_aliasboutic'], $notid)) //Si l'extension n'est pas dans le tableau
                {
                  throw new Error('Identifiant interdit');
                }
                
                $q = "INSERT INTO customer (cltid, customer, nom, adresse1, adresse2, codepostal, ville, logo, courriel) ";
                $q = $q . "VALUES ('" . $cltid . "', '" . $_SESSION['initboutic_aliasboutic'] . "', '" . addslashes($_SESSION['initboutic_nom']) . "', '" .  addslashes($_SESSION['initboutic_adresse1']) . "', '";
                $q = $q . addslashes($_SESSION['initboutic_adresse2']) . "', '" . $_SESSION['initboutic_codepostal'] . "', '" . addslashes($_SESSION['initboutic_ville']) . "', '";
                $q = $q . $_SESSION['initboutic_logo'] . "', '" . $_SESSION['initboutic_email'] . "')";
                //error_log($q);
                
                if ($conn->query($q) === FALSE) 
                {
                  throw new Error("erreur lors de l insertion: " . $conn->connect_error);
                }
                
                $bouticid = $conn->insert_id;
                
                $query = "INSERT INTO abonnement(cltid, creationboutic, bouticid, stripe_subscription_id, actif) VALUES ";
                $query = $query . "('$cltid', '0', '$bouticid', '" . $_SESSION['creationabonnement_stripe_subscription_id'] . "', '1')";
            
                //error_log($query);
            
                // remove following comments to enable writing in db
                if ($conn->query($query) === FALSE)
                {
                  throw new Error($conn->error);
                }
                
                $parametres = array (
                  array("isHTML_mail", "1", "HTML activé pour l'envoi de mail"),
                  array("Subject_mail","Commande PraticBoutic","Sujet du courriel pour l'envoi de mail"),
                  array("VALIDATION_SMS", $_SESSION['confboutic_validsms'], "Commande validée par sms ?"),
                  array("VerifCP", "0", "Activation de la verification des codes postaux"),
                  array("Choix_Paiement", $_SESSION['confboutic_chxpaie'], "COMPTANT ou LIVRAISON ou TOUS"),
                  array("MP_Comptant", "Par carte bancaire", "Texte du paiement comptant"),
                  array("MP_Livraison", "Moyens conventionnels", "Texte du paiement à la livraison"),
                  array("Choix_Method", $_SESSION['confboutic_chxmethode'], "TOUS ou EMPORTER ou LIVRER"),
                  array("CM_Livrer", "Vente avec livraison", "Texte de la vente à la livraison"),
                  array("CM_Emporter", "Vente avec passage à la caisse", "Texte de la vente à emporter"),
                  array("MntCmdMini", $_SESSION['confboutic_mntmincmd'], "Montant commande minimal"),
                  array("MntLivraisonMini", $_SESSION['confboutic_mntlivraisonmin'], "Montant Minimum pour accepter la livraison"),
                  array("SIZE_IMG", "smallimg", "bigimg ou smallimg"),
                  array("CMPT_CMD", "0", "Compteur des références des commandes"),
                  array("MONEY_SYSTEM", $_SESSION['moneysys_moneysys'], "STRIPE ou PAYPAL"),
                  array("PublicKey", $_SESSION['moneysys_stripepubkey'], "Clé public stripe"),
                  array("SecretKey", $_SESSION['moneysys_stripeseckey'], "Clé privé stripe"),
                  array("ID_CLT_PAYPAL", $_SESSION['moneysys_paypalid'], "ID Client PayPal"),
                );

                for($i=0; $i<count($parametres); $i++)
                {
                  $q = ' INSERT INTO parametre (customid, nom, valeur, commentaire) ';
                  $q = $q . 'VALUES ("' . $bouticid . '","' . $parametres[$i][0] . '","' . addslashes($parametres[$i][1]) . '","' . $parametres[$i][2] . '")';
                  //error_log($q);
                  if ($conn->query($q) === FALSE) 
                  {
                    throw new Error("Erreur lors de l'insertion d'un parametre : " . $conn->error);
                  }
                }

                $statuts = array (
                  array("Commande à faire", "#E2001A", "Bonjour, votre commande à été transmise. %boutic% vous remercie et vous tiendra informé de son avancé. ", 1, 1),
                  array("En cours de préparation", "#EB690B","Votre commande est en cours de préparation. ", 0, 1),
                  array("En cours de livraison", "#E2007A", "Votre commande est en cours de livraison, ", 0, 1),
                  array("Commande à disposition", "#009EE0", "Votre commande est à disposition", 0, 1),
                  array("Commande terminée", "#009036", "%boutic% vous remercie pour votre commande. À très bientôt. ", 0, 1),
                  array("Commande anulée", "#1A171B", "Nous ne pouvons donner suite à votre commande. Pour plus d\'informations, merci de nous contacter. ", 0, 1),
                );

                for($i=0; $i<count($statuts); $i++)
                {
                  $q = "INSERT INTO statutcmd (customid, etat, couleur, message, defaut, actif) ";
                  $q = $q . "VALUES ('" . $bouticid . "','" . $statuts[$i][0] . "','" . $statuts[$i][1] . "','" . $statuts[$i][2] . "','" . $statuts[$i][3] . "','" . $statuts[$i][4] . "')";
                  //error_log($q);
                  if ($conn->query($q) === FALSE) 
                  {
                    throw new Error("Erreur lors de l'insertion d'un statut de commande : " . $conn->error);
                  }
                }
                $conn->commit();
                $_SESSION['bo_stripe_customer_id'] = $_SESSION['registration_stripe_customer_id'];
                $_SESSION['bo_id'] = $bouticid;
                $_SESSION['bo_email'] = $_SESSION['verify_email'];
                $_SESSION['bo_auth'] = 'oui';
                header("LOCATION: admin.php");
              }
              catch (Error $e)
              {
                echo $e->getMessage();
                $conn->rollback();
              }
            ?>
            </div>
            <div class="modal-footer">
              <a href="register.php"><button class="btn btn-primary btn-block" type="button" value="Valider">OK</button></a>
            </div>
         </div>
      </div>
    </div>
  </body>
  <script type="text/javascript">
    $('.modal').modal('show');
  </script>
</html>