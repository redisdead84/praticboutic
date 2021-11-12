<?php
  session_start();

  if (empty($_SESSION['bo_id']) == TRUE)
  {
    header("LOCATION: index.php");
    exit();
  }

  if (empty($_SESSION['bo_auth']) == TRUE)
  {
    header("LOCATION: index.php");
    exit();
  }
  
  if (strcmp($_SESSION['bo_auth'],'oui') != 0)
  {
    header("LOCATION: index.php");
    exit();
  }
  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";
  
  // Create connection
  $conn = new mysqli($servername, $username, $password, $bdd);
  // Check connection
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);

  $req = $conn->prepare('SELECT stripe_customer_id FROM client WHERE email = ? AND actif = 1 ');
  $req->bind_param("s", $_SESSION['bo_email']);
  $req->execute();
  $req->bind_result($stripe_customer_id);
  $resultat = $req->fetch();
  $req->close();
  if (strcmp($stripe_customer_id, "") == 0 )
  {
    header("LOCATION: index.php");
    exit();
  }

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  $stripe = new \Stripe\StripeClient([
    'api_key' => $_ENV['STRIPE_SECRET_KEY'],
    'stripe_version' => '2020-08-27',
  ]);
  $subscriptions = $stripe->subscriptions->all(['customer' => $stripe_customer_id,
                               'status' => 'active'
  ]);
  if ($subscriptions->count() == 0)
  {
    header("LOCATION: account.php");
    exit();
  }

  $bouticid = $_SESSION['bo_id'];

?>

<!DOCTYPE html>
<html id="backhtml">
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.13">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body id="backbody">
    <script>
      var bouticid = "<?php echo $bouticid;?>" ;
      var login = "<?php echo $_SESSION['bo_email']; ?>";
      var init = "<?php echo $_SESSION['bo_init']; ?>";
      var initdone = "<?php $_SESSION['bo_init'] = 'non'; ?>";
      var proto = "<?php echo $_SERVER['SERVER_PROTOCOL']; ?>";
      proto = proto.toLowerCase();
      var protocole;

      if (proto.indexOf('https') != -1)
        protocole = 'https://';
      else
        protocole = 'http://';

      var server = "<?php echo $_SERVER['SERVER_NAME']; ?>";
      var pathimg = '../../upload/';

      var deflimite = 5;
      var defoffset = 0;
      var offset = 0;

      var tables = [
                {nom:"categorie", desc:"Catégories", cs:"nom", champs:[{nom:"catid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""},{nom:"nom", desc:"Nom", typ:"ref", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"visible", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}]},
                {nom:"article", desc:"Articles", cs:"nom", champs:[{nom:"artid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""},{nom:"nom", desc:"Nom", typ:"ref", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"prix", desc:"Prix", typ:"prix", defval:"0.00", vis:"o", ordre:"0", sens:""}, {nom:"description", desc:"Description", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, 
                {nom:"visible", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}, {nom:"catid", desc:"Catégorie", typ:"fk", defval:"", vis:"o", ordre:"0", sens:""},
                {nom:"unite", desc:"Unité", typ:"text", defval:"€", vis:"n", ordre:"0", sens:""}, {nom:"image", desc:"Fichier Image", typ:"image", defval:"", vis:"n", ordre:"0", sens:""}]},
                {nom:"relgrpoptart", desc:"Relations groupes d'option-articles", cs:"", champs:[{nom:"relgrpoartid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"grpoptid", desc:"", typ:"fk", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"artid", defval:"", desc:"", typ:"fk", vis:"o", ordre:"0", sens:""}, {nom:"visible", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}]},
                {nom:"groupeopt", desc:"Groupes d'option", cs:"nom", champs:[{nom:"grpoptid",  desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"nom", desc:"Nom", typ:"ref", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"visible", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}, {nom:"multiple", desc:"Choix Multiple", typ:"bool", defval:"0", vis:"o", ordre:"0", sens:""}]},
                {nom:"option", desc:"Options", cs:"nom", champs:[{nom:"optid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"nom", desc:"Nom", typ:"ref", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"surcout", desc:"Surcoût", typ:"prix", defval:"0.00", vis:"o", ordre:"0", sens:""}, {nom:"grpoptid", desc:"Groupe d'option", typ:"fk", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"visible", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}]},
                {nom:"administrateur", desc:"Utilisateurs" , cs:"email", champs:[{nom:"adminid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""},{nom:"email", desc:"Courriel", typ:"email", defval:"", vis:"o", ordre:"0", sens:""},{nom:"pass", desc:"Mot de Passe", typ:"pass", defval:"", vis:"o", ordre:"0", sens:""},{nom:"actif", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}]},
                {nom:"parametre", desc:"Paramètres", cs:"nom", champs:[{nom:"paramid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""},{nom:"nom", desc:"Nom", typ:"ref", defval:"", vis:"o", ordre:"0", sens:""},{nom:"valeur", desc:"Valeur", typ:"text", defval:"", vis:"o", ordre:"0", sens:""},{nom:"commentaire", desc:"Commentaire", typ:"text", defval:"", vis:"o", ordre:"0", sens:""}]},
                {nom:"cpzone", desc:"Zones de livraison", cs:"codepostal", champs:[{nom:"cpzoneid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""},{nom:"codepostal", desc:"Code Postal", defval:"", typ:"codepostal", vis:"o", ordre:"0", sens:""},{nom:"ville", desc:"Ville", defval:"", typ:"text", vis:"o", ordre:"0", sens:""},{nom:"actif", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}]},
                {nom:"barlivr", desc:"Barêmes de livraison", cs:"", champs:[{nom:"barlivrid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""},{nom:"valminin", desc:"Fourchette Basse (Incl.)", typ:"prix", defval:"0.00", vis:"o", ordre:"0", sens:""},{nom:"valmaxex", desc:"Fourchette Haute (Excl.)", typ:"prix", defval:"0.00", vis:"o", ordre:"0", sens:""},{nom:"surcout", desc:"Surcoût", typ:"prix", defval:"0.00", vis:"o", ordre:"0", sens:""},
                {nom:"actif", desc:"Active", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}]},
                {nom:"commande", desc:"Commandes Clients", cs:"numref", champs:[{nom:"cmdid", desc:"identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"numref", desc:"Référence", typ:"ref", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"nom", desc:"Nom", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"prenom", desc:"Prénom", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, 
                {nom:"telephone", desc:"Téléphone", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"adresse1", desc:"Ligne d'adresse n°1", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"adresse2", desc:"Ligne d'adresse n°2", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"codepostal", desc:"Code Postal", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, 
                {nom:"ville", desc:"Ville", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"vente", desc:"Type de Vente", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"paiement", desc:"Mode de Paiement", typ:"text", defval:"", vis:"n", ordre:"0", sens:""},
                {nom:"sstotal", desc:"Sous-total", typ:"prix", defval:"0.00", vis:"n", ordre:"0", sens:""}, {nom:"fraislivraison", desc:"Frais de Livraison", typ:"prix", defval:"0.00", vis:"n", ordre:"0", sens:""}, {nom:"total", desc:"Total", typ:"prix", defval:"0.00", vis:"o", ordre:"0", sens:""}, {nom:"commentaire", desc:"Commentaire", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"method", desc:"Méthode de vente", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, 
                {nom:"table", desc:"N° de la Table", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"datecreation", desc:"Date de Création", typ:"date", defval:"", vis:"o", ordre:"0", sens:""},
                {nom:"statid", desc:"Statut", typ:"fk", defval:"", vis:"o", ordre:"0", sens:""}]},
                {nom:"lignecmd", desc:"Lignes de commande", cs:"", champs:[{nom:"lignecmdid", desc:"Identifiant", typ:"pk", vis:"n", defval:"", ordre:"0", sens:""}, {nom:"cmdid", desc:"Commande", typ:"fk", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"ordre", desc:"Ordre", typ:"text", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"type", desc:"Type de Produit", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, 
                {nom:"nom", desc:"Intitulé", typ:"text", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"prix", desc:"Prix", typ:"prix", defval:"0.00", vis:"o", ordre:"0", sens:""}, {nom:"quantite", desc:"Quantité", typ:"text", defval:"0", vis:"o", ordre:"0", sens:""}, {nom:"commentaire", desc:"Commentaire", typ:"text", defval:"", vis:"o", ordre:"0", sens:""}]},
                {nom:"statutcmd", desc:"Statuts de commande", cs:"etat", champs:[{nom:"statid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"etat", desc:"Etat de la commande", typ:"text", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"couleur", desc:"Couleur du status", typ:"text", defval:"", vis:"o", ordre:"0", sens:""},
                {nom:"message", desc:"SMS à Envoyer", typ:"text", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"defaut", desc:"Defaut", typ:"bool", defval:"0", vis:"o", ordre:"0", sens:""}, {nom:"actif", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}]}
                ];

  var liens = [{nom:"categorie", desc:"Catégorie de l'article", srctbl:"article", srcfld:"catid", dsttbl:"categorie", dstfld:"catid", join:"ij"},
               {nom:"groupeopt", desc:"Groupe d'option rélié", srctbl:"relgrpoptart", srcfld:"grpoptid", dsttbl:"groupeopt", dstfld:"grpoptid", join:"ij"},
               {nom:"article", desc:"Article relié", srctbl:"relgrpoptart", srcfld:"artid", dsttbl:"article", dstfld:"artid", join:"ij"},
               {nom:"groupeopt", desc:"Groupe de l'option", srctbl:"option", srcfld:"grpoptid", dsttbl:"groupeopt", dstfld:"grpoptid", join:"ij"},
               {nom:"commande", desc:"Commande reliée", srctbl:"lignecmd", srcfld:"cmdid", dsttbl:"commande", dstfld:"cmdid", join:"ij"},
               {nom:"statut", desc:"Statut de la commande", srctbl:"commande", srcfld:"statid", dsttbl:"statutcmd", dstfld:"statid", join:"ij"}
              ];

  var rpp = [5,10,15,20,50,100];
  var op = ["=",">","<",">=","<=","<>","LIKE"];
  var filtres = [];
  var maxfiltre = 10;
  var arrrgoa = [];
  var stackvue = [];
  var w;
  var memnbcommande = 0;

  $(function() {
    inittable("table0", "table0", "categorie");
    inittable("table1", "table1", "article");
    inittable("table3", "table3", "groupeopt");
    fldCustomProp("pbaliasid", "customer", "url");
    fldCustomProp("pbnomid", "nom", "text");
    fldCustomProp("pbadr1id", "adresse1", "text");
    fldCustomProp("pbadr2id", "adresse2", "text");
    fldCustomProp("pbcpid", "codepostal", "text");
    fldCustomProp("pbvilleid", "ville", "text");
    fldCustomProp("artlogofile", "logo", "image");
    fldCustomProp("pbemailid", "courriel", "email");
    fldParam("subjectmailid", "Subject_mail", "text");
    fldParam("validationsmsid", "VALIDATION_SMS", "bool");
    fldParam("verifcpid", "VerifCP", "bool");
    fldParam("choixpaiementid", "Choix_Paiement", "select");
    fldParam("mpcomptantid", "MP_Comptant", "text");
    fldParam("mplivraisonid", "MP_Livraison", "text");
    fldParam("choixmethodid", "Choix_Method", "select");
    fldParam("cmlivrerid", "CM_Livrer", "text");
    fldParam("cmemporterid", "CM_Emporter", "text");
    fldParam("mntmincmdid", "MntCmdMini", "prix");
    fldParam("sizeimgid", "SIZE_IMG", "select");
    fldParam("moneysystemid", "MONEY_SYSTEM", "select");
    fldParam("publickeyid", "PublicKey", "text");
    fldParam("secretkeyid", "SecretKey", "text");
    fldParam("idcltpaypalid", "ID_CLT_PAYPAL", "text");
    fldClientProp("clpassid", "pass", "pass");
    fldClientProp("clhommeid", "qualite", "radio");
    fldClientProp("clfemmeid", "qualite", "radio");
    fldClientProp("clnomid", "nom", "text");
    fldClientProp("clprenomid", "prenom", "text");
    fldClientProp("cladr1id", "adr1", "text");
    fldClientProp("cladr2id", "adr2", "text");
    fldClientProp("clcpid", "cp", "text");
    fldClientProp("clvilleid", "ville", "text");
    fldClientProp("cltelid", "tel", "text");
    inittable("table7", "table7", "cpzone");
    inittable("table8", "table8", "barlivr");
    inittable("ihm9", "table9", "commande");
    inittable("table11", "table11", "statutcmd");
    if (init == "oui")
    {
      init = "non";
      initdone;
      var modal = $('.modal');
      $('.modal-title').html('Félicitations');
      modal.find('.modal-body').text('Votre Pratic Boutic a été créé. \n\n\n Insérer une catégorie puis un article pour commencer l\'expérience.');
      $('.modal').modal('show');
    }
    startWorkerCommande();
  });

  </script>
    <div class="vertical-nav" id="sidebar">
      <ul class="nav nav-menu flex-column">
        <img id='logopblid' src='img/LOGO_PRATIC_BOUTIC.png' />
        <li class="nav-item">
          <a class="nav-link" id="commandes-tab" data-toggle="tab" href="#commandes" role="tab" aria-controls="commandes" aria-selected="false" onclick="cancel(this)"><img class='picto' src='img/picto_mes-commandes.png' />Mes Commandes</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" id="produit-tab" data-toggle="tab" href="#produit" role="tab" aria-controls="produit" aria-selected="false" onclick="cancel(this)"><img class='picto' src='img/picto_mes-produits.png' />Mes Produits</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" id="livraison-tab" data-toggle="tab" href="#livraison" role="tab" aria-controls="livraison" aria-selected="false" onclick="cancel(this)"><img class='picto' src='img/LIVRAISON.png' />Livraisons</a>
        </li>  
        <div class="demiinter">
        </div>
        <li class="nav-item">
          <a class="nav-link" id="administration-tab" data-toggle="tab" href="#administration" role="tab" aria-controls="administration" aria-selected="false" onclick="cancel(this)"><img class='picto' src='img/picto_mon_compte.png' />Esapce Client</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="account.php"><p class="nopicto">Abonnement</p></a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="https://pratic-boutic.fr/#faq"><p class="nopicto">Aide</p></a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="https://pratic-boutic.fr/praticboutic"><p class="nopicto">Marketing</p></a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="logout.php"><p class="nopicto">Deconnexion</p></a>
        </li>
      </ul>
    </div>
    <div class="tab-content page-content" id="myMenuContent">
      <div class="tab-pane" id="commandes" role="tabpanel" aria-labelledby="commandes-tab">
        <p class="title">Commandes</p>
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="commande-tab" data-toggle="tab" href="#commande" role="tab" aria-controls="commande" aria-selected="true">COMMANDES CLIENTS</a>
            </li>
          </ul>
        <div class="tab-content" id="myTabCmdContent">
          <div class="tab-pane active" id="commande" role="tabpanel" aria-labelledby="commande-tab">
            <div class='tbl' id='ihm9'>
              <div id="table9"></div>
            </div>
            <div class='tbl form-group' id="det9" data-vuep="table9" hidden></div>
            <div class='tbl form-group' id="det10" data-vuep="det9" hidden></div>
          </div>
        </div>
      </div>
      <div class="tab-pane active" id="produit" role="tabpanel" aria-labelledby="produit-tab">
        <p class="title">Produits</p>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="categorie-tab" data-toggle="tab" href="#categorie" role="tab" aria-controls="categorie" aria-selected="true">CATEGORIES</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="article-tab" data-toggle="tab" href="#article" role="tab" aria-controls="article" aria-selected="false">ARTICLES</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="groupeopt-tab" data-toggle="tab" href="#groupeopt" role="tab" aria-controls="groupeopt" aria-selected="false">OPTIONS</a>
          </li>
        </ul>
        <div class="tab-content" id="myTabProdContent">
          <div class="tab-pane active" id="categorie" role="tabpanel" aria-labelledby="categorie-tab">
            <div class='tbl' id="table0"></div>
            <div class='tbl form-group' id="ins0" data-vuep="table0" hidden></div>
            <div class='tbl form-group' id="maj0" data-vuep="table0" hidden></div>
          </div>
          <div class="tab-pane" id="article" role="tabpanel" aria-labelledby="article-tab">
            <div class='tbl' id='ihm1'>
              <div id="table1"></div>
            </div>  
             <div class='tbl form-group' id="ins1" data-vuep="table1" hidden></div>
             <div class='tbl form-group' id="maj1" data-vuep="table1" data-lnkchild="article" hidden></div>
             <div class='tbl form-group' id="ins2" data-vuep="maj1" hidden></div>
             <div class='tbl form-group' id="maj2" data-vuep="maj1" hidden></div>
          </div>
          <div class="tab-pane" id="groupeopt" role="tabpanel" aria-labelledby="groupeopt-tab">
            <div class='tbl' id="table3"></div>  
             <div class='tbl form-group' id="ins3" data-vuep="table3" hidden></div>
             <div class='tbl form-group' id="maj3" data-vuep="table3" data-lnkchild="groupeopt" hidden></div>
             <div class='tbl form-group' id="ins4" data-vuep="maj3" hidden></div>
             <div class='tbl form-group' id="maj4" data-vuep="maj3" hidden></div>  
          </div>
        </div>
      </div>
      <div class="tab-pane" id="livraison" role="tabpanel" aria-labelledby="livraison-tab">
        <p class="title">Livraison</p>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="cpzone-tab" data-toggle="tab" href="#cpzone" role="tab" aria-controls="cpzone" aria-selected="false">ZONES DE LIVRAISON</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="barlivr-tab" data-toggle="tab" href="#barlivr" role="tab" aria-controls="barlivr" aria-selected="false">BAREME DE LIVRAISON</a>
          </li>
        </ul>
        <div class="tab-content" id="myTabLivrContent">
          <div class="tab-pane active" id="cpzone" role="tabpanel" aria-labelledby="cpzone-tab">
            <div class='tbl' id="table7"></div>  
             <div class='tbl form-group' id="ins7" data-vuep="table7" hidden></div>
             <div class='tbl form-group' id="maj7" data-vuep="table7" hidden></div>  
          </div>
          <div class="tab-pane" id="barlivr" role="tabpanel" aria-labelledby="barlivr-tab">
            <div class='tbl' id="table8"></div>  
             <div class='tbl form-group' id="ins8" data-vuep="table8" hidden></div>
             <div class='tbl form-group' id="maj8" data-vuep="table8" hidden></div>  
          </div>
        </div>
      </div>
      <div class="tab-pane" id="administration" role="tabpanel" aria-labelledby="administration-tab">
        <p class="title">Espace Client</p>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="perso-tab" data-toggle="tab" href="#perso" role="tab" aria-controls="perso" aria-selected="false">BOUTIC</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="reglage-tab" data-toggle="tab" href="#reglage" role="tab" aria-controls="reglage" aria-selected="false">REGLAGES</a>
          </li>
          <?php if (strcmp($statutcmd, "n")==0) echo "<!--" ?>
          <li class="nav-item">
            <a class="nav-link" id="statutcmd-tab" data-toggle="tab" href="#statutcmd" role="tab" aria-controls="statutcmd" aria-selected="false">STATUTS DES COMMANDES</a>
          </li>
          <?php if (strcmp($statutcmd, "n")==0) echo "-->" ?>
          <li class="nav-item">
            <a class="nav-link" id="backoffice-tab" data-toggle="tab" href="#backoffice" role="tab" aria-controls="backoffice" aria-selected="false">BACK-OFFICE</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="qrcode-tab" data-toggle="tab" href="#qrcode" role="tab" aria-controls="qrcode" aria-selected="false">GENERATEUR QRCODE</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="client-tab" data-toggle="tab" href="#client" role="tab" aria-controls="client" aria-selected="false">CLIENT</a>
          </li>
          <!--<li class="nav-item">
            <a class="nav-link" id="abo-tab" data-toggle="tab" href="#abo" role="tab" aria-controls="abo" aria-selected="false">ABONNEMENT</a>
          </li>-->
        </ul>
        <div class="tab-content" id="myTabAdminContent">
          <div class="tab-pane active" id="perso" role="tabpanel" aria-labelledby="perso-tab">
          <form autocomplete="off">
            <div class='tbl'>
              <div class='twocol'>
                <div class="blocurl">
                  <div class="param">
                    <label>Alias de la Boutic : </label>
                    <input data-lbl="Alias de la Boutic" class="fieldperso" id="pbaliasid" type='text' maxlength="100" pattern="[a-z0-9]{3,}" title="ne peut contenir que des chiffres ou des minuscules" oninput="persoenblbtnvc(this)" />
                  </div>
                  <br>
                  <div class="param">
                    <label>URL de la Boutic :&nbsp;</label>
                    <a id="linkid" target="_blank"></a>
                  </div>
                </div>
                <br>
                <div class="param">
                  <label>Nom de l'entreprise : </label>
                  <input data-lbl="Nom de l'entreprise" class="fieldperso" id="pbnomid" type='text' maxlength="100" oninput="persoenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Adresse (ligne1) : </label>
                  <input data-lbl="Adresse (ligne1)" class="fieldperso" id="pbadr1id" type='text' maxlength="150" oninput="persoenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Adresse (ligne2) : </label>
                  <input data-lbl="Adresse (ligne2)" class="fieldperso" id="pbadr2id" type='text' maxlength="150" oninput="persoenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Code Postal : </label>
                  <input data-lbl="Code Postal" class="fieldperso" id="pbcpid" type='text' maxlength="5" pattern="[0-9]{5}" oninput="persoenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Ville : </label>
                  <input data-lbl="Ville" class="fieldperso" id="pbvilleid" type='text' maxlength="50" oninput="persoenblbtnvc(this)" />
                </div>
                <br>
                <div class="" id="bloclogoid">
                  <label for="artlogofile">Logo : </label>
                  <input data-lbl="Logo" class="fieldperso" id="artlogofile" name="artlogofile" class="form-control-file" type="file" accept="image/png, image/jpeg" oninput="persoenblbtnvc(this)" />
                </div>
                <br>
                <form autocomplete="off">
                <div class="param">
                  <label for="pbemailid">Courriel : </label>
                  <input data-lbl="Courriel" class="fieldperso" id="pbemailid" type='email' maxlength="255" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="un courriel valide" oninput="persoenblbtnvc(this)" />
                </div>
                </form>
                <br>
              </div>
              <br>
              <div class="center">
                <input type='button' class="btn btn-primary btn-block" id='validpersoid' disabled='true' value='Valid' onclick="validpersoupdate()" />
                <input type='button' class="btn btn-secondary btn-block" id='cancelpersoid' disabled='true' value='Cancel' onclick="cancelpersoupdate()" />
              </div>
            </div>
          </form>
          </div>
          <div class="tab-pane" id="reglage" role="tabpanel" aria-labelledby="reglage-tab">
          <form autocomplete="off">
            <div class='tbl'>
              <div class='twocol'>
                <div class="param">
                  <label for="subjectmailid">Sujet du courriel : </label>
                  <input data-lbl="Sujet du courriel" class="fieldparam" id="subjectmailid" type='text' maxlength="255" oninput="paramenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label for="validationsmsid">Validation des commandes par SMS : </label>
                  <div>
                    <input data-lbl="Validation des commandes par SMS" class="fieldparam" id="validationsmsid" type='checkbox' onclick="paramenblbtnvc(this)" />
                  </div>
                </div>
                <br>
                <div class="param">
                  <label for="verifcpid">Vérification des codes postaux : </label>
                  <div>
                    <input data-lbl="Vérification des codes postaux" class="fieldparam" id="verifcpid" type='checkbox' onclick="paramenblbtnvc(this)" />
                  </div>
                </div>
                <br>
                <div class="param">
                  <label for="choixpaiementid">Choix de paiement : </label>
                  <select data-lbl="Choix de paiement" class="fieldparam" id="choixpaiementid" oninput="paramenblbtnvc(this)"  >
                    <option value='COMPTANT'>En ligne par CB</option>
                    <option value='LIVRAISON'>En direct par vos moyens</option>
                    <option value='TOUS'>En ligne & En direct</option>
                  </select>
                </div>
                <br>
                <div class="param">
                  <label for="mpcomptantid">Texte du paiement comptant : </label>
                  <input data-lbl="Texte du paiement comptant" class="fieldparam" id="mpcomptantid" type='text' maxlength="255" oninput="paramenblbtnvc(this)"  />
                </div>
                <br>
                <div class="param">
                  <label for="mplivraisonid">Texte du paiement à la livraison : </label>
                  <input data-lbl="Texte du paiement à la livraison" class="fieldparam" id="mplivraisonid" type='text' maxlength="255" oninput="paramenblbtnvc(this)"  />
                </div>
                <br>
                <div class="param">
                  <label for="choixmethodid">Choix de la méthode : </label>
                  <select data-lbl="Choix de la méthode" class="fieldparam" id="choixmethodid" onchange="paramenblbtnvc(this)"  >
                    <option value='EMPORTER'>Emporter</option>
                    <option value='LIVRER'>Livrer</option>
                    <option value='TOUS'>Emporter & Livrer</option>
                  </select>
                </div>
                <br>
                <div class="param">
                  <label for="cmlivrerid">Texte de la vente à la livraison : </label>
                  <input data-lbl="Texte de la vente à la livraison" class="fieldparam" id="cmlivrerid" type='text' maxlength="255" oninput="paramenblbtnvc(this)"  />
                </div>
                <br>
                <div class="param">
                  <label for="cmemporterid">Texte de la vente à emporter : </label>
                  <input data-lbl="Texte de la vente à emporter" class="fieldparam" id="cmemporterid" type='text' maxlength="255" oninput="paramenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label for="mntmincmdid">Montant minimum de commande : </label>
                  <input data-lbl="Montant minimum de commande" class="inpprix fieldparam" id="mntmincmdid" type='number' step='0.01' min='0' oninput="paramenblbtnvc(this)"  />
                </div>
                <br>
                <div class="param">
                  <label for="sizeimgid">Taille des images : </label>
                  <select data-lbl="Taille des images" class="fieldparam" id="sizeimgid" oninput="paramenblbtnvc(this)"  >
                    <option value='smallimg'>Petites</option>
                    <option value='bigimg'>Grandes</option>
                  </select>
                </div>
                <br>
                <div class="param">
                  <label for="moneysystemid">Système de paiement : </label>
                  <select data-lbl="Système de paiement" class="fieldparam" id="moneysystemid" oninput="paramenblbtnvc(this)" >
                    <option value='STRIPE'>STRIPE</option>
                    <option value='PAYPAL'>PAYPAL</option>
                  </select>
                </div>
                <br>
                <div class="param">
                  <label for="publickeyid">Clé Public Stripe : </label>
                  <input data-lbl="Clé Public Stripe" class="fieldparam" id="publickeyid" type='text' maxlength="255" oninput="paramenblbtnvc(this)" autocomplete="off" />
                </div>
                <br>
                <form autocomplete="off">
                  <div class="param">
                      <label for="secretkeyid">Clé Privé Stripe : </label>
                      <input data-lbl="Clé Privé Stripe" class="fieldparam" id="secretkeyid" type='password' maxlength="255" oninput="paramenblbtnvc(this)" autocomplete="one-time-code" />
                  </div>
                </form>
                <br>
                <div class="param">
                  <label for="idcltpaypalid">ID Client Paypal : </label>
                  <input data-lbl="ID Client Paypal" class="fieldparam" id="idcltpaypalid" type='text' maxlength="255" oninput="paramenblbtnvc(this)" autocomplete="off" />
                </div>
                <br>
              </div>
              <br>
              <div class="center">
                <input type='button' class="btn btn-primary btn-block" id='validparamid' disabled='true' value='Valid' onclick="validparamupdate()" />
                <input type='button' class="btn btn-secondary btn-block" id='cancelparamid' disabled='true' value='Cancel' onclick="cancelparamupdate()" />
              </div>
            </div>
          </form>
          </div>
          <div class="tab-pane" id="client" role="tabpanel" aria-labelledby="client-tab">
            <div class='tbl'>
              <div class='twocol'>
                <div class="param">
                  <label>Mot de passe : </label>
                  <input data-lbl="Mot de passe" class="fieldclient" id="clpassid" data-conffldid="clpassconfid" type='password' autocomplete="one-time-code" maxlength="255" pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}" title="Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères" oninput="clientenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Mot de passe (confirmation): </label>
                  <input data-lbl="Mot de passe (confirmation)" class="fieldclientpassconf" id="clpassconfid" type='password' autocomplete="one-time-code" maxlength="255" pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}" title="Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères" oninput="javascript:document.getElementById('clpassid').setAttribute('data-modified', true);clientenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Qualité : </label>
                  <div class="fieldclientradio center"><input class="fieldclient" type="radio" id="clhommeid" name="clqualite" value="Monsieur" oninput="clientenblbtnvc(this)"><label for="clhommeid">&nbsp;Monsieur&nbsp;</label></div>
                  <div class="fieldclientradio center"><input class="fieldclient" type="radio" id="clfemmeid" name="clqualite" value="Madame" oninput="clientenblbtnvc(this)"><label for="clfemmeid">&nbsp;Madame&nbsp;</label></div><br>
                </div>
                <br>
                <div class="param">
                  <label>Nom : </label>
                  <input data-lbl="Nom" class="fieldclient" id="clnomid" type='text' maxlength="60" oninput="clientenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Prénom : </label>
                  <input data-lbl="Prénom" class="fieldclient" id="clprenomid" type='text' maxlength="60" oninput="clientenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Adresse (ligne1) : </label>
                  <input data-lbl="Adresse (ligne1)" class="fieldclient" id="cladr1id" type='text' maxlength="150" oninput="clientenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Adresse (ligne2) : </label>
                  <input data-lbl="Adresse (ligne2)" class="fieldclient" id="cladr2id" type='text' maxlength="150" oninput="clientenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Code Postal : </label>
                  <input data-lbl="Code Postal" class="fieldclient" id="clcpid" type='text' maxlength="5" pattern="[0-9]{5}" oninput="clientenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Ville : </label>
                  <input data-lbl="Ville" class="fieldclient" id="clvilleid" type='text' maxlength="50" oninput="clientenblbtnvc(this)" />
                </div>
                <br>
                <div class="param">
                  <label>Téléphone : </label>
                  <input data-lbl="Téléphone" class="fieldclient" id="cltelid" type='text' maxlength="255" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[0-9](?:[\.\-\s]?\d\d){4}$" title="Un téléphone français valide" oninput="clientenblbtnvc(this)" />
                </div>
                <br>
              </div>
              <br>
              <div class="center">
                <input type='button' class="btn btn-primary btn-block" id='validclientid' disabled='true' value='Valid' onclick="validclientupdate()" />
                <input type='button' class="btn btn-secondary btn-block" id='cancelclientid' disabled='true' value='Cancel' onclick="cancelclientupdate()" />
              </div>
            </div>
          </div>
          <div class="tab-pane" id="statutcmd" role="tabpanel" aria-labelledby="statutcmd-tab">
            <div class='tbl' id="table11"></div>  
             <div class='tbl form-group' id="ins11" data-vuep="table11" hidden></div>
             <div class='tbl form-group' id="maj11" data-vuep="table11" hidden></div>  
          </div>
          <div class="tab-pane" id="backoffice" role="tabpanel" aria-labelledby="backoffice-tab">
            <div class='tbl'>
              <input type="button" class="btn btn-secondary" id="razctrlid" value='RAZ des Mémoires de contrôles' onclick="razctrl()"></button>
            </div>
          </div>
          <div class="tab-pane" id="qrcode" role="tabpanel" aria-labelledby="qrcode-tab">
            <div class='tbl'>  
              <form action="pdfqrcode.php" method="post" target="_blank">
                <fieldset>
                  <legend>Méthode de vente:</legend>
                  <input type="radio" id="radatbl" name="methv" value="2" onclick="javascript:document.getElementById('optnbtable').style.display='block'" checked>
                  <label for="radatbl">A Table</label><br><input type="radio" id="radqnc" name="methv" value="3" onclick="javascript:document.getElementById('optnbtable').style.display='none'">
                  <label for="radqnc">Qlick'n'Collect</label><br><br>
                </fieldset>
                <p id="optnbtable">Nombre de table : <input type="number" class="inpprix" name="nbtable" step="1" min="1" value = "1" /></p>
                <p>Nombre d'exemplaire : <input type="number" class="inpprix" name="nbex" step="1" min="1" value = "1" /></p>
                <p><input type="submit" value="Générer le PDF de QRCODE"></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
      
  	<!--<div class="modal" tabindex="-1" role="dialog">-->
  	<div class="modal" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  	  <div class="modal-dialog modal-dialog-centered" role="document">
  	    <div class="modal-content">
  	      <div class="modal-header">
  	        <h5 class="modal-title">Erreur</h5>
  	      </div>
  	      <div class="modal-body">
  	        <!--<p>Modal body text goes here.</p>-->
  	      </div>
  	      <div class="modal-footer">
  	        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close" id="okbtn">OK</button>
  	      </div>
  	    </div>
  	  </div>
  	</div>

	<script type="text/javascript" >
			function getnumtable(nom)
			{
				for (var i=0; i<tables.length; i++)
       		if (nom == tables[i].nom)
       			numtable = i;

				return numtable; 			
			}	
	
	
			function insert(numtable, limite, offset, vueparent, placeparent, selcol, selid) {
				var champs = tables[numtable].champs;
				var vue, vuep, liensel;


				vuep = document.getElementById(vueparent);
				vuep.hidden = true;	

				vue = document.getElementById('ins' + numtable);
				vue.hidden = false;				
				
				var titre = document.createElement('H5');
				titre.id = 'itable'+ numtable +'titre';
				titre.innerHTML = 'Insertion dans table ' + tables[numtable].desc;
				vue.appendChild(titre);
				var br = document.createElement('br');
				vue.appendChild(br);
				var formoff = document.createElement("form");
				formoff.autocomplete = "off";
				var labels = [];
				var input = [];
				for(i=0; i<champs.length; i++)				
				{
					if (champs[i].typ != "pk")
					{
						if (champs[i].nom != selcol)
						{						
						
							var lbl = document.createElement('label');
							lbl.id = 'itable'+ numtable +'lbl' + i;
							lbl.htmlFor = 'itable'+ numtable + 'inp' + i;
							if (champs[i].typ != "fk")
							{
								lbl.innerHTML = champs[i].desc + '&nbsp;:&nbsp;';
								
								var inp = document.createElement('input');
								inp.autocomplete = "off";
								if (champs[i].typ == "text")
								{
									inp.classList.add('form-control');
									inp.type = 'text';
									inp.value = champs[i].defval;
								}
								else if (champs[i].typ == "ref")
								{
									inp.classList.add('form-control');
									inp.type = 'text';
									inp.required = true;
									inp.value = champs[i].defval;
								}
								else if (champs[i].typ == "bool")
								{
									inp.type = 'checkbox';
									inp.classList.add('mbchk');
									if (champs[i].defval == "1")
										inp.checked = true;
									else 
										inp.checked = false;
								}
								else if (champs[i].typ == "prix")
								{
									inp.classList.add('form-control');
									inp.type = 'number';
									inp.step = '0.01';
									inp.value = champs[i].defval;
									inp.min = '0';
									inp.title = "Doit être un nombre positif avec 2 chiffres après la virgule";
								}
								else if (champs[i].typ == "image")
								{
									inp.classList.add('form-control-file');
									inp.type = 'file';
									inp.accept="image/png, image/jpeg";
									inp.setAttribute("data-artimg", 'itable' + numtable + '_' + 'artimg' + i );									
									inp.onchange = function () {
										const fileInput = this;
										const formdata = new FormData();
										formdata.append('file', fileInput.files[0]);
										
						        fetch("boupload.php", {
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
											else {
												document.getElementById(fileInput.getAttribute("data-artimg")).src = pathimg + data;
				        				fileInput.setAttribute("data-truefilename", data);
				        				fileInput.filename = data;
				        				imgclose.style.display = 'block';
											}						         	
						        })
									}
									var divp = document.createElement("DIV");
									divp.classList.add("frameimg");
									var image = document.createElement("IMG");
									image.id = 'itable' + numtable + '_' + 'artimg' + i;
									image.alt = "";
									image.classList.add("imgart");
									divp.appendChild(image);
										
									var imgclose = document.createElement("IMG");
									imgclose.src = '../img/fermer.png';
									imgclose.alt = "";
									imgclose.classList.add("imgclose");
									if (inp.filename == "")
										imgclose.style.display = 'none';
									if (image.src == "")
										imgclose.style.display = 'none';
									imgclose.setAttribute("data-artimgfile", 'itable' + numtable + '_' + 'inp' + i );
									imgclose.setAttribute("data-artimg", 'itable' + numtable + '_' + 'artimg' + i );
									imgclose.addEventListener("click", function() {
										document.getElementById(this.getAttribute("data-artimgfile")).setAttribute("data-truefilename",'');
										document.getElementById(this.getAttribute("data-artimgfile")).value = '';
										document.getElementById(this.getAttribute("data-artimg")).src = '';
										this.style.display = 'none';
									});
									divp.appendChild(imgclose); 
									formoff.appendChild(divp);
									formoff.appendChild(document.createElement("BR"));
								}
								else if (champs[i].typ == "pass")
								{
									inp.classList.add('form-control');
									inp.type = 'password';
									inp.pattern = "(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}";
									inp.title = "Le mot de passe doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères";
									inp.required = true;
									inp.autocomplete = "new-password";
								}
								else if (champs[i].typ == "email")
								{
									inp.classList.add('form-control');
									inp.type = 'email';
									inp.pattern = "[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$";
									inp.title = "Le courriel doit valide";
									inp.required = true;
								}
								else if (champs[i].typ == "codepostal")
								{
									inp.classList.add('form-control');
									inp.type = 'text';
									inp.pattern = "[0-9]{5}";
									inp.minlength = "5";
									inp.maxlength = "5";
									inp.title = "Le code postal doit être valide";
									inp.required = true;
								}
								
								formoff.appendChild(lbl);
								inp.name = 'itable' + numtable + '_' + champs[i].nom;
								inp.id = 'itable' + numtable + '_' + 'inp' + i;
								inp.setAttribute("data-table",tables[numtable].nom);
								inp.setAttribute("data-champ",champs[i].nom);
								formoff.appendChild(inp);
							}
							else
							{
								var lien = document.createElement('select');
								lien.classList.add('form-control');
								lien.name = 'itable' + numtable + '_' + champs[numtable].nom;
								lien.id = 'itable' + numtable + '_' + 'lien' + i;
								lien.setAttribute("data-table", tables[numtable].nom);
								lien.setAttribute("data-champ", champs[i].nom);
								for (var j=0; j<liens.length; j++)
								{
									if ((liens[j].srctbl == tables[numtable].nom) && (liens[j].srcfld == champs[i].nom ))
									{	
										lbl.innerHTML = liens[j].desc + '&nbsp;:&nbsp;';
										formoff.appendChild(lbl);
										formoff.appendChild(lien);
										for (var k=0; k<tables.length; k++)
										{
											if (tables[k].nom == liens[j].dsttbl)
												getoptions(	'itable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs );      
										}
									}
								}
							}
							var br = document.createElement('br');
							formoff.appendChild(br);
						}
					}
				}	
				var okbtn = document.createElement('button');
				okbtn.id = "okbtn" + numtable;
				okbtn.type = "button";
				okbtn.innerHTML = "Ok";
				okbtn.classList.add("btn");
				okbtn.classList.add("btn-primary");
				okbtn.classList.add("btn-block");
				okbtn.setAttribute("data-vuep", vueparent);
				okbtn.setAttribute("data-vue", 'ins' + numtable);
				okbtn.onclick = function(){
					var row = [];
					var error = false;
					var errmsg ="";
					for (var i=0; i<champs.length; i++)
					{
						var val;

						if (champs[i].typ == 'image')
						{
			        val = document.getElementById('itable' + numtable + '_' + 'inp' + i).getAttribute("data-truefilename");
						}
						else if (champs[i].typ =='bool')
						{
							var chkd = document.getElementById('itable' + numtable + '_' + 'inp' + i).checked;
							if (chkd == true)
								val = "1";
							else {
								val = "0";
							}
						}
						else if (champs[i].typ =='fk') 
						{
							if (selcol == champs[i].nom)
								val = selid;
							else
								val = document.getElementById('itable' + numtable + '_' + 'lien' + i).value;
						} 						
						else if (champs[i].typ !='pk'){
							fld = document.getElementById('itable' + numtable + '_' + 'inp' + i);
						  val = fld.value;
						  if ( fld.required == true)
						  {
						  	if (val == "")
						  	{
						  		error = true;
						  		errmsg = 	"Le champ " + champs[i].desc + " ne peut pas être vide";
						  		break;
						  	}
						  }
						  if (!fld.checkValidity())
						  {
								error = true;
					  		errmsg = fld.getAttribute("data-champ")  + " : " + fld.validationMessage;
								break;  	
						  }						  
						}
						if (champs[i].typ !='pk')
						{
							var col = {nom:champs[i].nom, valeur:val, type:champs[i].typ, desc:champs[i].desc};
							row.push(col);
						}					
					}
					if (error == false)
					{
						var vu = document.getElementById(this.getAttribute("data-vue"));
						insertrow(vu, vueparent, placeparent, tables[numtable].nom, row, limite, offset, selcol, selid);
					}
					else 
					{
         		var modal = $('.modal');
	       		$('.modal-title').html('Erreur');
   			    modal.find('.modal-body').text(errmsg);
       			$('.modal').modal('show');
					}
				}; 

				formoff.appendChild(okbtn);
				
				var clbtn = document.createElement('button');
				clbtn.id = "clbtn" + numtable;
				clbtn.type = "button";
				clbtn.innerHTML = "Cancel";
				clbtn.classList.add("btn");
				clbtn.classList.add("btn-secondary");
				clbtn.classList.add("btn-block");
				clbtn.classList.add("btn-cancel");
				clbtn.setAttribute("data-vuep", vueparent);
				clbtn.setAttribute("data-vue", 'ins' + numtable);
				clbtn.onclick = function(){
					vuep = document.getElementById(this.getAttribute("data-vuep"));
					vue = document.getElementById(this.getAttribute("data-vue"));

					vuep.hidden = false;
					vue.hidden = true;
					vue.innerHTML = '';
				}; 
				formoff.appendChild(clbtn);

				vue.appendChild(formoff);
				
			}
			
			function update(numtable, idtoup, limite, offset, vueparent, placeparent, selcol, selid)
			{
				var champs = tables[numtable].champs;
				
				vuep = document.getElementById(vueparent);
				vuep.hidden = true;	

				vue = document.getElementById('maj' + numtable);
				vue.hidden = false;		
				
				var titre = document.createElement('H5');
				titre.id = 'itable'+ numtable +'titre';
				titre.innerHTML = 'Mise à jour dans table ' + tables[numtable].desc;
				vue.appendChild(titre);
				var br = document.createElement('br');
				vue.appendChild(br);				
				var formoff = document.createElement("form");
				formoff.autocomplete = "off";
				var obj = { bouticid: bouticid, action:"getvalues", tables:tables, table:tables[numtable].nom, liens:liens, colonne:"", row:"", idtoup:idtoup };

        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
						var labels = [];
						var input = [];
						for(i=0; i<champs.length; i++)				
						{
							if (champs[i].typ != "pk")
							{
								if (champs[i].nom != selcol)
								{						
									var lbl = document.createElement('label');
									lbl.id = 'utable'+ numtable +'lbl' + i;
									lbl.htmlFor = 'utable'+ numtable + 'inp' + i;
									if (champs[i].typ != "fk")
									{
										lbl.innerHTML = champs[i].desc + '&nbsp;:&nbsp;';
										formoff.appendChild(lbl);
										var inp = document.createElement('input');
										inp.autocomplete = "off";
										if (champs[i].typ == "text")
										{
											inp.classList.add('form-control');			
											inp.type = 'text';
											inp.value = data[i];
										}
										if (champs[i].typ == "ref")
										{
											inp.classList.add('form-control');			
											inp.type = 'text';
											inp.value = data[i];
											inp.required = true;
										}
										else if (champs[i].typ == "bool")
										{
											inp.type = 'checkbox';
											inp.classList.add('mbchk');
											if (data[i] == "1")
												inp.checked = true;
											else {
												inp.checked = false;
											}
										}
										else if (champs[i].typ == "prix")
										{
											inp.classList.add('form-control');			
											inp.type = 'number';
											inp.step = '0.01';
											inp.value = parseFloat(data[i]).toFixed(2);
											inp.min = '0';
											inp.title = "Doit être un nombre positif avec 2 chiffres après la virgule";
										}
										else if (champs[i].typ == "image")
										{
											inp.classList.add('form-control-file');
											inp.type = 'file';
											inp.accept="image/png, image/jpeg";
											inp.filename = data[i];
											inp.setAttribute("data-truefilename", data[i]);
											inp.setAttribute("data-artimg", 'utable' + numtable + '_' + 'artimg' + i );
											inp.onchange = function () {
												const fileInput = this;
												const formdata = new FormData();
												formdata.append('file', fileInput.files[0]);
												
								        fetch("boupload.php", {
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
													else {
						        				document.getElementById(fileInput.getAttribute("data-artimg")).src = pathimg + data;
						        				fileInput.setAttribute("data-truefilename", data);
						        				fileInput.filename = data;
						        				imgclose.style.display = 'block';
													}						         	
								        })
											}
											var divp = document.createElement("DIV");
											divp.classList.add("frameimg");
											var image = document.createElement("IMG");
											image.id = 'utable' + numtable + '_' + 'artimg' + i;
											if (data[i] != "")
												image.src = pathimg + data[i];
											image.alt = "";
											image.classList.add("imgart");
											divp.appendChild(image);
												
											var imgclose = document.createElement("IMG");
											imgclose.src = '../img/fermer.png';
											imgclose.alt = "";
											imgclose.classList.add("imgclose");
											if (inp.filename == "")
												imgclose.style.display = 'none';
											if (image.src == '')
												imgclose.style.display = 'none';
  										imgclose.setAttribute("data-artimgfile", 'utable' + numtable + '_' + 'inp' + i );
  										imgclose.setAttribute("data-artimg", 'utable' + numtable + '_' + 'artimg' + i );
											imgclose.addEventListener("click", function() {
												document.getElementById(this.getAttribute("data-artimgfile")).setAttribute("data-truefilename",'');
												document.getElementById(this.getAttribute("data-artimgfile")).value = '';
												document.getElementById(this.getAttribute("data-artimg")).src = '';
												this.style.display = 'none';
											});
											divp.appendChild(imgclose);
											formoff.appendChild(divp);
											formoff.appendChild(document.createElement("BR"));
										}
										else if (champs[i].typ == "pass")
										{
											inp.classList.add('form-control');
											inp.type = 'password';
											inp.pattern = "(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}";
											inp.title = "Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères";
											inp.required = false;
											inp.autocomplete = "new-password";
										}
										else if (champs[i].typ == "email")
										{
											inp.classList.add('form-control');
											inp.type = 'email';
											inp.pattern = "[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$";
											inp.title = "Doit être une adresse de courriel valide";
											inp.required = true;
											inp.value = data[i];
										}
										else if (champs[i].typ == "codepostal")
										{
											inp.classList.add('form-control');
											inp.type = 'text';
											inp.pattern = "[0-9]{5}";
											inp.minlength = "5";
											inp.maxlength = "5";
											inp.title = "Doit contenir 5 chiffre";
											inp.required = true;
											inp.value = data[i];
										}
										
										inp.name = 'utable' + numtable + '_' + champs[i].nom;
										inp.id = 'utable' + numtable + '_' + 'inp' + i;
										
										inp.setAttribute("data-table",tables[numtable].nom);
										inp.setAttribute("data-champ",champs[i].nom);
										formoff.appendChild(inp);
									}
									else 
									{
										var lien = document.createElement('select');
										lien.classList.add('form-control');
										lien.name = 'utable' + numtable + '_' + champs[numtable].nom;
										lien.id = 'utable' + numtable + '_' + 'lien' + i;
										lien.setAttribute("data-table", tables[numtable].nom);
										lien.setAttribute("data-champ", champs[i].nom);
										for (j=0; j<liens.length; j++)
										{
											if ((liens[j].srctbl == tables[numtable].nom) && (liens[j].srcfld == champs[i].nom ))
											{	
												lbl.innerHTML = liens[j].desc + '&nbsp;:&nbsp;';
												formoff.appendChild(lbl);
												
												for (k=0; k<tables.length; k++)
													if (tables[k].nom == liens[j].dsttbl)
														getoptions('utable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs, data[i]) ;
												
												formoff.appendChild(lien);
											}
										}
									}
									var br = document.createElement('br');
									formoff.appendChild(br);
								}
							}
						}
						
						var lnk = formoff.getAttribute("data-lnkchild");
						if (lnk != null)						
						{
							var numlnk,jpk,subnumtable;
							for (var i=0; i<liens.length; i++ ) {
								if (liens[i].nom == lnk)
									numlnk = i; 
							}
							for (var i=0; i<tables.length; i++ ) {
								if (liens[numlnk].srctbl == tables[i].nom)
									subnumtable = i; 
							}
							jpk = liens[numlnk].srcfld;
						
							var titre = document.createElement('H5');
							titre.id = 'itable'+ subnumtable +'titre';
							titre.innerHTML = tables[subnumtable].desc;
							
							formoff.appendChild(titre);
			
							var rgp = document.createElement('DIV');
							rgp.classList.add("tbl");
							rgp.classList.add("form-group");
							rgp.id = "tablesub" + subnumtable;
							rgp.hidden = false;
							formoff.appendChild(rgp);

							inittable( "maj" + numtable, "tablesub" + subnumtable, tables[subnumtable].nom, jpk, idtoup);							
						
						}
						var okbtn = document.createElement('button');
						okbtn.id = "okbtn" + numtable;
						okbtn.type = "button";
						okbtn.innerHTML = "Ok";
						okbtn.classList.add("btn");
						okbtn.classList.add("btn-primary");
						okbtn.classList.add("btn-block");
						okbtn.setAttribute("data-vuep", vueparent);
						okbtn.setAttribute("data-vue", 'maj' + numtable);
						okbtn.onclick = function()
						{
							var row = [];
							var pknom;
							var error = false;
							var errmsg = "";
							for (var i=0; i<champs.length; i++)
							{
								var val;
								if (champs[i].typ != 'pk')
								{
									if (champs[i].typ == 'image')
									{
						        val = document.getElementById('utable' + numtable + '_' + 'inp' + i).getAttribute("data-truefilename");
									}
									else if (champs[i].typ =='bool')
									{
										var chkd = document.getElementById('utable' + numtable + '_' + 'inp' + i).checked;
										if (chkd == true)
											val = "1";
										else {
											val = "0";
										}
									}
									else if (champs[i].typ =='fk')
									{
										if (selcol == champs[i].nom)
											val = selid;
										else
											val = document.getElementById('utable' + numtable + '_' + 'lien' + i).value;
									} 
									else 
									{
										fld = document.getElementById('utable' + numtable + '_' + 'inp' + i);
									  val = fld.value;
 									  if (!fld.checkValidity())
									  {
											error = true;	
											errmsg = fld.getAttribute("data-champ")  + " : " + fld.validationMessage;
											break;				  	
						  			}						  
									}
									var col = {nom:champs[i].nom, valeur:val, type:champs[i].typ, desc:champs[i].desc};
									row.push(col);
								}
								else {
									pknom = champs[i].nom;
								}
							}
							if (error == false)
							{
								var vu = document.getElementById(this.getAttribute("data-vue"));
								updaterow(vu, vueparent, placeparent, tables[numtable].nom, row, pknom, idtoup, limite, offset, selcol, selid);
							}
							else 
							{
 		         		var modal = $('.modal');
			       		$('.modal-title').html('Erreur');
						    modal.find('.modal-body').text(errmsg);
    						$('.modal').modal('show');
							}
						};
					  formoff.appendChild(okbtn);
						
						var clbtn = document.createElement('button');
						clbtn.id = "clbtn" + numtable;
						clbtn.type = "button";
						clbtn.innerHTML = "Cancel";
						clbtn.classList.add("btn");
						clbtn.classList.add("btn-secondary");
						clbtn.classList.add("btn-block");
						clbtn.classList.add("btn-cancel");
						clbtn.setAttribute("data-vuep", vueparent);
						clbtn.setAttribute("data-vue", 'maj' + numtable);

						clbtn.onclick = function(){
							vuep = document.getElementById(this.getAttribute("data-vuep"));
							vue = document.getElementById(this.getAttribute("data-vue"));
				
  						vuep.hidden = false
	  					vue.hidden = true;
							vue.innerHTML = '';
						}; 
						formoff.appendChild(clbtn);
						vue.appendChild(formoff);
						
					}
      	})
			}
			
			function changeFunc(vue, place, tablestr, $i, selcol="", selid=0) 
			{
				limite = $i;				
				gettable( vue, place, tablestr, limite, 0, selcol, selid);
   		}
   		
   		
			
			function luminosite(couleur)
			{
	      var maxi, mini, lumi;
   			var r = parseInt(couleur.slice(1, 3), 16);
   			var g = parseInt(couleur.slice(3, 5), 16);
   			var b = parseInt(couleur.slice(5, 7), 16);
				
				lumi = (r + g + b) / 3;

				return lumi			
			}			
			  
 			function displaytable(vue, place, tablestr, donnees, total, pagination, limite, offset, selcol="", selid=0)
 			{ 				
			 	var pkval;
			 	nummtable = getnumtable(tablestr);
			 	table = tables[numtable];

			 	var firstdiv, maintable, mainthead, maintbody, tr, th, td, input, buttonins, lblrpp, selres, optres, nav, ul, li, apl, spanlq, spanprev, spanrq, spannext;
			 	
				if ((tablestr !== "commande") &&  (tablestr !== "lignecmd"))
				{
			    buttonins = document.createElement("BUTTON");
			  	buttonins.classList.add("btn");
			  	buttonins.classList.add("btn-primary");
			  	buttonins.classList.add("btn-insert");
			  	buttonins.onclick = function () {
			  		nummtable = getnumtable(tablestr);
			  		insert(numtable,limite,offset,vue,place,selcol,selid);
			  	}
			  	buttonins.innerHTML = "Insérer";
			  	document.getElementById(place).appendChild(buttonins);
				}
				if (total > 0)
				{
				 	firstdiv = document.createElement("DIV");
					maintable = document.createElement("TABLE");
					maintable.classList.add("table");
					maintable.classList.add("table-hover");
					mainthead = document.createElement("THEAD");		 	
					tr = document.createElement("TR");
					for (var i=0; i<table.champs.length; i++)          	
				 	{
				 		if ((table.champs[i].typ != "pk") && (table.champs[i].vis != "n") && (table.champs[i].nom != selcol))
				 		{
							th = document.createElement("TH");
				   		
				   		if (table.champs[i].typ != "fk")
				   			th.innerHTML = table.champs[i].desc;
				   		else
				   		{
								for (var j=0; j<liens.length; j++)          	
				 				{
				 					if ((liens[j].srctbl == table.nom) && (liens[j].srcfld == table.champs[i].nom))
				 						th.innerHTML = liens[j].desc; 
								}
				   		}	
				   		tr.appendChild(th);
				 		}
				 	}
				 	mainthead.appendChild(tr);
				 	maintable.appendChild(mainthead);
				 	maintbody = document.createElement("TBODY");
				 	for (var j=0; j<donnees.length; j++)
				 	{
				 		tr = document.createElement("TR");
						for (var i=0; i<donnees[j].length; i++)          	
				   	{
				   		if ((table.champs[i].typ != "pk") && (table.champs[i].vis != "n") && (table.champs[i].nom != selcol))
				 			{
				     		td = document.createElement("TD");
				     		if (table.champs[i].typ != "bool")
				     		{
				     			var val = donnees[j][i];
				     			if (table.champs[i].typ == "prix")
				     				td.innerHTML = parseFloat(val).toFixed(2);
				     			else if (table.champs[i].typ == "date")
									{
											const event = new Date(Date.parse(val));
											td.innerHTML = event.toLocaleString('fr-FR');
									}
				     			else
				     				td.innerHTML = val;
				     		}
				     		else {
			     				input = document.createElement("INPUT");
			     				input.type = 'checkbox';
			     				if ((tablestr == "commande") || (tablestr == "lignecmd")) 
			     					input.disabled = true;
			     				else
			     					input.disabled = false;
									
				     			if (donnees[j][i] > 0)
				     				input.checked = true;
				     			else
				     				input.checked = false;
	
				     			input.setAttribute("data-table", tablestr);
				     			input.setAttribute("data-field", table.champs[i].nom);
				     			input.onclick = function (e) {
				     				var row = [];
				     				var val;
				     				if (this.checked == true)
											val = 1;			     				
				     				else 
				     					val = 0;
				     				
										var col = {nom:this.getAttribute("data-field"), valeur:val, type:'checkbox'};
										row.push(col);				
				     				updaterow(null, "", "", this.getAttribute("data-table"), row, this.parentElement.parentElement.getAttribute("data-pknom"), this.parentElement.parentElement.getAttribute("data-pkval"), limite, offset, "", 0);
				     				e.stopPropagation();
				     			}
												     			
				     			td.appendChild(input);
				     		}
				     		tr.appendChild(td);
				   		}
				   		else if (table.champs[i].typ == "pk")
				   		{
				   			tr.setAttribute("data-pknom", table.champs[i].nom);
				   			tr.setAttribute("data-pkval", donnees[j][i]);
								if ((tablestr == "commande") || (tablestr == "lignecmd")) 
								{
									if (tablestr == "commande")
										tr.classList.add("colored");
									tr.onclick = function () {
										pkval = this.getAttribute("data-pkval");
										nummtable = getnumtable(tablestr);
									if (tablestr == "commande")
										detail(numtable, pkval, limite, offset, vue, place, selcol, selid);
									if (tablestr == "lignecmd")
										detail(numtable, pkval, limite, offset, "det9", "det9", selcol, selid);
										
									}
								}
								else
								{
									tr.onclick = function () {
										pkval = this.getAttribute("data-pkval");
										nummtable = getnumtable(tablestr);
										update(numtable, pkval, limite, offset, vue, place, selcol, selid);
									}
								}
								tr.classList.add("clickable-row");
						  }
				   	}
				   	maintbody.appendChild(tr);    	
					}
				 	maintable.appendChild(maintbody);
				 	firstdiv.appendChild(maintable);
				 	document.getElementById(place).appendChild(firstdiv);
				}
				else 
				{
				  var nodatap = document.createElement("P");
				  var ita = document.createElement("I");
				  ita.innerText = 'Il n\'y a pas de ' + tables[getnumtable(table)].desc + ' à afficher';
				  nodatap.appendChild(ita);
			  	document.getElementById(place).appendChild(nodatap);				
				}
				var booltxt;
			 	if (pagination == true)
					booltxt = "block";
				else
					booltxt = "none";
			 		
		 		var divrpp = document.createElement("DIV");
		 		divrpp.style.display = booltxt;
		 		divrpp.classList.add("divrpp");
			 	lblrpp = document.createElement("LABEL");
			 	lblrpp.for = "rpp" + numtable ;
			 	lblrpp.innerHTML = "Nombre de résultat par page";
			 	divrpp.appendChild(lblrpp);
			 	selres = document.createElement("SELECT");
			 	selres.onchange = function () {
			 		changeFunc(vue, place, tablestr, this.value, selcol, selid);
			 		localStorage.setItem("praticboutic_ctrl_" + server + "_" + login + "_rppid" + getnumtable(tablestr), this.value);
			 		if (selid == 0)
			 			localStorage.setItem("praticboutic_ctrl_" + server + "_" + login + "_nav" + getnumtable(tablestr), defoffset);
			 	}
			 	selres.id = "rppid" + numtable; 
				for (var k=0; k<rpp.length; k++)
				{
					optres = document.createElement("OPTION");
					optres.value = rpp[k];
					optres.innerHTML = rpp[k];
					if (limite == rpp[k])
						optres.selected = true;						        	
					selres.appendChild(optres);       	
				}				
				vallimite = parseInt(limite);
				divrpp.appendChild(selres);
				document.getElementById(place).appendChild(divrpp);

				nav  = document.createElement("NAV");
				nav.setAttribute("aria-label", "Page navigation");
				ul = document.createElement("UL");
				ul.classList.add("pagination");
		    li = document.createElement("LI");
		    li.classList.add("page-item");

		    if ((offset - vallimite) < 0)
			    li.classList.add("disabled");
				apl = document.createElement("A");
				apl.classList.add("page-link");
				apl.onclick = function () {
					gettable(vue,place,tablestr, limite, (offset - vallimite), selcol, selid);
					if (selid == 0)
						localStorage.setItem("praticboutic_ctrl_" + server + "_" + login + "_nav" + getnumtable(tablestr), (offset - vallimite));
				}
				apl.setAttribute("aria-label", "Previous");
				spanlq = document.createElement("SPAN");
				spanlq.setAttribute("aria-hidden", "true");
				spanlq.innerHTML = "&laquo;";
				apl.appendChild(spanlq);
				spanprev = document.createElement("SPAN");
				spanprev.classList.add("sr-only");
				spanprev.innerHTML = "Previous";
				apl.appendChild(spanprev);
				li.appendChild(apl);
				ul.appendChild(li);

		    var totalpage = Math.ceil(total / vallimite);
		    for (var k=0; k<totalpage;k++)
		    {
			    li = document.createElement("LI");
			    li.classList.add("page-item");
 		    	if ((offset/ vallimite) == k)
				    li.classList.add("active");
					apl = document.createElement("A");
					apl.classList.add("page-link");
					apl.setAttribute("data-num", k);
					apl.onclick = function () {
						num = this.getAttribute("data-num");
						gettable(vue, place, tablestr, limite, ( num * limite ), selcol, selid);
						if (selid == 0)
							localStorage.setItem("praticboutic_ctrl_" + server + "_" + login + "_nav" + getnumtable(tablestr), ( num * limite ));
					}
					apl.innerHTML = k;
					li.appendChild(apl);
					ul.appendChild(li);
		    }
		    li = document.createElement("LI");
		    li.classList.add("page-item");
		    
		    if ((offset + vallimite) >= total)
					li.classList.add("disabled");		    	
		    
				apl = document.createElement("A");
				apl.classList.add("page-link");
				apl.onclick = function () {
					gettable(vue, place, tablestr, limite, (offset + vallimite), selcol, selid);
					if (selid == 0)
				  	localStorage.setItem("praticboutic_ctrl_" + server + "_" + login + "_nav" + getnumtable(tablestr), (offset + vallimite));
				}
				apl.setAttribute("aria-label", "Next");
				spanrq = document.createElement("SPAN");
				spanrq.setAttribute("aria-hidden", "true");
				spanrq.innerHTML = "&raquo;";
				apl.appendChild(spanrq);
				spannext = document.createElement("SPAN");
				spannext.classList.add("sr-only");
				spannext.innerHTML = "Next";
				apl.appendChild(spannext);
				li.appendChild(apl);
				ul.appendChild(li);
				nav.appendChild(ul);
				nav.style.display = booltxt;
		    document.getElementById(place).appendChild(nav);

 				if (tablestr == "commande")
 				{
					var j=0;
					var obj3 = { bouticid: bouticid, action:"colorrow", tables:tables, table:"", liens:liens, colonne:"", row:"", idtoup:"", limite:limite, offset:offset, selcol:"", selid:0};

	        fetch("boquery.php", {
	          method: "POST",
	          headers: {
	        		'Content-Type': 'application/json',
	        		'Accept': 'application/json'
	          },
	          body: JSON.stringify(obj3)
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
	         		var rowtocolor = document.getElementsByClassName("colored");
	         		for (var i=0; i< data.length; i++)
	         		{
	         			var couleur = data[i][0];

	         			if (luminosite(couleur)>128)	         			
	         				rowtocolor[i].style.color = 'black';
	         			else 
	         				rowtocolor[i].style.color = 'white';
	         			
		         		rowtocolor[i].style.backgroundColor = couleur;
	         		}
	         	}
	        })
				}
			}
			   			
			function detail(numtable, idtoup, limite, offset, vueparent, placeparent, selcol, selid)
			{
				var champs = tables[numtable].champs;
				var objectid = 0;

				vuep = document.getElementById(vueparent);
				vuep.hidden = true;	

				vue = document.getElementById('det' + numtable);
				vue.hidden = false;		
				
				var titre = document.createElement('H5');
				titre.id = 'dtable'+ numtable +'titre';
				titre.innerHTML = 'Détails ' + tables[numtable].desc;
				vue.appendChild(titre);
				var br = document.createElement('br');
				vue.appendChild(br);				
				
				var obj = { bouticid: bouticid, action:"getvalues", tables:tables, table:tables[numtable].nom, liens:liens, colonne:"", row:"", idtoup:idtoup };

        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
						var labels = [];
						var input = [];
						var cmdhead = document.createElement("DIV");
						cmdhead.classList.add('twocol');
						vue.appendChild(cmdhead);
						var message, telephone;
						for(i=0; i<champs.length; i++)				
						{
							if (champs[i].typ != "pk")
							{
								var dat = document.createElement('p');
								dat.id = 'dtable'+ numtable +'dat' + i;
								if (champs[i].typ != "fk")
								{
									dat.innerText = champs[i].desc + ' : ';
									if (champs[i].typ == "text")
									{
										dat.innerText = dat.innerText + data[i];
									}
									if (champs[i].typ == "date")
									{
										const event = new Date(Date.parse(data[i]));
										dat.innerText = dat.innerText + event.toLocaleString('fr-FR');
									}
									if (champs[i].typ == "ref")
									{
										dat.innerText = dat.innerText + data[i];
									}
									else if (champs[i].typ == "bool")
									{
										if (data[0][i] == "1")
											dat.innerText = dat.innerText + 'oui';
										else {
											dat.innerText = dat.innerText + 'non';
										}
									}
									else if (champs[i].typ == "prix")
									{
										dat.innerText = dat.innerText + parseFloat(data[i]).toFixed(2) + " €";
									}
									else if (champs[i].typ == "image")
									{
										dat.innerText = dat.innerText + data[i];
									}
									else if (champs[i].typ == "pass")
									{
										dat.innerText = dat.innerText + data[i];
									}
									else if (champs[i].typ == "email")
									{
										dat.innerText = dat.innerText + data[i];
									}
									else if (champs[i].typ == "codepostal")
									{
										dat.innerText = dat.innerText + data[i];
									}
									dat.setAttribute("data-table",tables[numtable].nom);
									dat.setAttribute("data-champ",champs[i].nom);
									cmdhead.appendChild(dat);
								}
								else 
								{
									for (j=0; j<liens.length; j++)
									{
										if ((liens[j].srctbl == tables[numtable].nom) && (liens[j].srcfld == champs[i].nom ))
										{	
											if (liens[j].nom == "statut" )
											{
												var lbl = document.createElement('LABEL');
												lbl.name = 'dlbltable' + numtable + '_' + champs[i].nom;
												lbl.id = 'dlbltable' + numtable + '_' + 'lien' + i;
												lbl.innerText = liens[j].desc + ' : ';
												lbl.htmlFor = 'dtable' + numtable + '_' + champs[i].nom;
												cmdhead.appendChild(lbl);
												
												var lien = document.createElement('SELECT');
												lien.classList.add('form-control');
												lien.name = 'dtable' + numtable + '_' + champs[i].nom;
												lien.id = 'dtable' + numtable + '_' + 'lien' + i;
												lien.setAttribute("data-table", tables[numtable].nom);
												lien.setAttribute("data-champ", champs[i].nom);
												lien.onchange = function()
												{
													var row = [];
													var col = {nom:"statid", valeur:lien.value, type:champs[i].typ};
													row.push(col);
													updaterow(null, "", "", tables[numtable].nom, row, "cmdid", objectid, limite, offset, "", 0);
													
													sendStatutSMS(objectid);
										  		var couleur = this.options[this.selectedIndex].style.backgroundColor;
										  		this.style.backgroundColor = couleur;
         									if (luminosite(couleur)>128)
	         									this.style.color = 'black';
	         								else 
	         									this.style.color = 'white';
												};
												cmdhead.appendChild(lien);
												
												for (k=0; k<tables.length; k++)
													if (tables[k].nom == liens[j].dsttbl)
														getoptions('dtable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs + ", couleur ", data[i], true);
											}											
											else 
											{
												var lien = document.createElement('P');
												lien.name = 'dtable' + numtable + '_' + champs[i].nom;
												lien.id = 'dtable' + numtable + '_' + 'lien' + i;
												lien.innerText = liens[j].desc + ' : ';
												lien.setAttribute("data-table", tables[numtable].nom);
												lien.setAttribute("data-champ", champs[i].nom);
												cmdhead.appendChild(lien);
												
												for (k=0; k<tables.length; k++)
													if (tables[k].nom == liens[j].dsttbl)
														getoptions('dtable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs, data[i], false);
											}
										}
									}
								}
							}
							else {
								objectid = parseInt(data[i]);
							}
						}
						if (tables[numtable].nom == "commande")
						{
							var lignecmd = document.createElement('DIV');
							lignecmd.classList.add("tbl");
							lignecmd.classList.add("form-group");
							lignecmd.id = "table10";
							lignecmd.hidden = false;
							document.getElementById('det' + numtable).appendChild(lignecmd);
							inittable( "table10", "table10", "lignecmd", "cmdid", objectid);
						}
						else {
							vue.appendChild(document.createElement("BR"));
						}
						var clbtn = document.createElement('button');
						clbtn.id = "clbtn" + numtable;
						clbtn.type = "button";
						clbtn.innerHTML = "Retour";
						clbtn.classList.add("btn");
						clbtn.classList.add("btn-primary");
						clbtn.classList.add("btn-block");
						clbtn.classList.add("btn-cancel");
						clbtn.setAttribute("data-vuep", vueparent);
						clbtn.setAttribute("data-vue", 'det' + numtable);

						clbtn.onclick = function(){
							vuep = document.getElementById(this.getAttribute("data-vuep"));
							vue = document.getElementById(this.getAttribute("data-vue"));
				
  						vuep.hidden = false
	  					vue.hidden = true;
							vue.innerHTML = '';
						};
						vue.appendChild(clbtn);
					}
      	})
			}
			
      function inittable(vue, place, table, selcol="", selid=0)      
      {
     		if(!localStorage.getItem("praticboutic_ctrl_" + server + "_" + login + "_rppid" + getnumtable(table))) {
			 		localStorage.setItem("praticboutic_ctrl_" + server + "_" + login + "_rppid" + getnumtable(table), deflimite);
	 	 			limite = deflimite;
				} else {
					limite = localStorage.getItem("praticboutic_ctrl_" + server + "_" + login + "_rppid" + getnumtable(table));
				}
				if (selid == 0)
				{
					if(!localStorage.getItem("praticboutic_ctrl_" + server + "_" + login + "_nav" + getnumtable(table))) {
	 	 				localStorage.setItem("praticboutic_ctrl_" + server + "_" + login + "_nav" + getnumtable(table), defoffset);
					} else {
					  offset = localStorage.getItem("praticboutic_ctrl_" + server + "_" + login + "_nav" + getnumtable(table));
					}
				}
				else {
					offset = defoffset;
				}		

      	gettable(vue, place, table, limite, offset, selcol, selid);
			}
			
			
      function gettable(vue, place, table, limite, offset, selcol="", selid=0)      
      {
      	
      	var obj = { bouticid: bouticid, action:"elemtable", tables:tables, table:table, liens:liens, colonne:"", row:"", idtoup:"", limite:"", offset:"", selcol:selcol, selid:selid, filtres:filtres };
  	
        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
        })
        .then(function(result) {
          return result.json(); // Try to fix Error so i replace json by
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
			    	var total = parseInt(data[0]);
			    	if (table == "commande")
			    		memnbcommande = total;
			    	var pagination = true;
       			if (total <= deflimite)
		        {
							limite = total;
							offset = 0;
							pagination = false;		         			
		        }	
		        else {
		        	pagination = true;
		        }

			      var obj2 = { bouticid: bouticid, action:"vuetable", tables:tables, table:table, liens:liens, colonne:"", row:"", idtoup:"", limite:limite, offset:offset, selcol:selcol, selid:selid, filtres:filtres };
			  	
			      fetch("boquery.php", {
			        method: "POST",
			        headers: {
			      		'Content-Type': 'application/json',
			       		'Accept': 'application/json'
			        },
			        body: JSON.stringify(obj2)
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
		        		document.getElementById(place).innerHTML = "";
         				displaytable( vue, place, table, data, total, pagination, limite, offset, selcol, selid);
							}
					  })
					}
				})
      }
      
			function datatooption(place, donnees, selidx, tosel)
			{
				var options="";
				if (tosel == true)
				{
					for (i=0; i<donnees.length; i++)
					{
						var opt = document.createElement("OPTION");
						opt.value = donnees[i][0];
						if (donnees[i][0] == selidx)
							opt.selected = true;												
						//var txt = document.createTextNode(donnees[i][1]);
					  opt.innerHTML = donnees[i][1];
					  document.getElementById(place).appendChild(opt);
					  if (donnees[i].length > 2)
					  {
         			var couleur = donnees[i][2];
							opt.style.backgroundColor = couleur;
         			if (luminosite(couleur)>128)
	         			opt.style.color = 'black';
	         		else 
	         			opt.style.color = 'white';

	         		if (donnees[i][0] == selidx)
					  	{
					  		opt.parentElement.style.backgroundColor = couleur;
         				if (luminosite(couleur)>128)
	         				opt.parentElement.style.color = 'black';
	         			else 
	         				opt.parentElement.style.color = 'white';
					  	}
					  }
					}
				}
				else 
				{
					for (i=0; i<donnees.length; i++)
					{
						if (donnees[i][0] == selidx)
							options = donnees[i][1];
					}
					document.getElementById(place).innerHTML = document.getElementById(place).innerHTML + options; 
				}
			}      
      
			function getoptions( place, table, colonne, selidx, tosel=true)      
      {
        var retour;      
        
      	var obj = { bouticid: bouticid, action:"rempliroption", tables:tables, table:table, liens:liens, colonne:colonne };
      	
        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
            	datatooption(place, data, selidx, tosel);
        })
          
      } 
      
			function insertrow( vue, vueparent, place, table, row, limite, offset, selcol, selid)      
      {
        var retour;      
        
      	var obj = { bouticid: bouticid, action:"insertrow", tables:tables, table:table, liens:liens, colonne:"", row:row };
     	
        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
						document.getElementById(vueparent).hidden = false
						vue.hidden = true;
						vue.innerHTML = '';
         		
	          gettable(vueparent, place, table, limite, offset, selcol, selid);
         	}
      	})
      } 

			function updaterow( vue, vueparent, place, table, row, pknom, idtoup, limite, offset, selcol, selid)      
      {
        var retour;      
        
      	var obj = { bouticid: bouticid, action:"updaterow", tables:tables, table:table, liens:liens, colonne:pknom, row:row, idtoup:idtoup };
     	
        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
         		if (vueparent != "")
         		{
							document.getElementById(vueparent).hidden = false;
							vue.hidden = true;
							vue.innerHTML = '';
							gettable(vueparent, place, table, limite, offset, selcol, selid);
         		}
         	}
      	})
      }
      
			function sendSMS( telephone, message)      
      {
        var retour;      
        
      	var obj = { bouticid: bouticid, telephone:telephone, message:message };
     	
        fetch("sms.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
        })
      }
      
			function sendStatutSMS( cmdid)      
      {
        var retour;      
        
      	var obj = { bouticid: bouticid, action:"getcomdata", tables:tables, table:"commande", liens:liens, cmdid:cmdid };
     	
        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
         		sendSMS( data[0], data[1]); 
         	}
      	})
      }
      
			function startWorkerCommande()
			{
			  if(typeof(Worker) !== "undefined")
			  {
			    if(typeof(w) == "undefined")
			    {
			      w = new Worker("js/pb_worker.js?v=1.05");
			    }
			    w.onmessage = function(event) {

  	    		var limitcmd = document.querySelectorAll("#rppid9")[0];
  	    		var vallimcmd;
  	    		try {
              vallimcmd = parseInt(limitcmd.value);
            } catch (error) {
              vallimcmd = 0;
            }
  	    		var offsetcmd = document.querySelectorAll("#table9 > nav > ul.pagination > li.active")[0];
  		    	var off2;
  	    		if (typeof(offsetcmd) !== "undefined")
  	    		{
  		    		if (offsetcmd.childNodes.length > 0)
                off2 = parseInt(offsetcmd.childNodes[0].textContent);
  		    		else 
  		    			off2 = 0;
  	    		}
  	    		else
  	    			off2 = 0;
  	    		var valoffcmd = off2 * vallimcmd;
  					gettable("ihm9", "table9", "commande", vallimcmd, valoffcmd);
			    }
				}
				else 
				{
			  	document.getElementById("table9").innerHTML = "Sorry, your browser does not support Web Workers...";
				}
			}

			function stopWorkerCommande()
			{
			  w.terminate();
			  w = undefined;
			}

			window.addEventListener("beforeunload", function(event) {
				stopWorkerCommande();
			});
			
			function cancel(caller)
			{
				if (caller.classList.contains('active'))
				{
					var cancel = document.getElementsByClassName("btn-cancel");
					for (var i=0; i<cancel.length; i++) 
					{
						if ((cancel[i].parentElement.hidden == false) && (cancel[i].parentElement.parentElement.classList.contains('active')))
						{
							cancel[i].click();
							break;
						}
					}
				}
			}

			function fldParam( elem, param, typ)
      {
        var retour;
 
      	var obj = { bouticid: bouticid, action:"getparam", tables:tables, table:"parametre", param:param };

        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
         		var btnvalid, btncancel;
         		document.getElementById(elem).setAttribute("data-param", param);
         		document.getElementById(elem).setAttribute("data-typ", typ);
         		document.getElementById(elem).setAttribute("data-modified", 'false');
         		document.getElementById(elem).setAttribute("data-paramtype", 'norm');
         		
         		var savdata = data;
         		if (typ == "prix")
         		{
         	 		document.getElementById(elem).value = parseFloat(data).toFixed(2);
         		}
         		else if (typ == "bool")
         		{
         			if (data == "1")
         				document.getElementById(elem).checked = true;
         			else
         				document.getElementById(elem).checked = false;
         		}
         		else if (typ == "image")
         		{
         			var inp = document.getElementById("artlogofile"); 
							inp.setAttribute("data-artlogo", "artlogo" );
							inp.filename = data[0];
							inp.setAttribute("data-logotruefilename", data[0]);
							inp.setAttribute("data-artlogo", 'artlogo' );

							inp.onchange = function () {
								const fileInput = this;
								const formdata = new FormData();
								formdata.append('file', fileInput.files[0]);
								
				        fetch("boupload.php", {
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
									else {
										fileInput.setAttribute("data-modified", 'true');
										document.getElementById(fileInput.getAttribute("data-artlogo")).src = pathimg + data;
		        				fileInput.setAttribute("data-logotruefilename", data);
		        				fileInput.filename = data;
		        				imgclose.style.display = 'block';
									}						         	
				        })
							}
							var divp = document.createElement("DIV");
							divp.classList.add("frameimg");
							var image = document.createElement("IMG");
							image.id = "artlogo";
							image.alt = "";
							image.classList.add("imgart");
							if (data[0] != "")
								image.src = pathimg + data[0];
							divp.appendChild(image);

							var imgclose = document.createElement("IMG");
							imgclose.id = 'logofermer';
							imgclose.src = '../img/fermer.png';
							imgclose.alt = "";
							imgclose.classList.add("imgclose");
							if (inp.filename == "")
								imgclose.style.display = 'none';
							if (data[0] == "")
								imgclose.style.display = 'none';
							imgclose.setAttribute("data-artlogofile", "artlogofile" );
							imgclose.setAttribute("data-artlogo", "artlogo" );
							imgclose.addEventListener("click", function() {
								document.getElementById(this.getAttribute("data-artlogofile")).setAttribute("data-modified", 'true');
								document.getElementById(this.getAttribute("data-artlogofile")).setAttribute("data-logotruefilename",'');
								document.getElementById(this.getAttribute("data-artlogofile")).value = '';
								document.getElementById(this.getAttribute("data-artlogo")).removeAttribute("src");
								this.style.display = 'none';
								paramenblbtnvc(document.getElementById(this.getAttribute("data-artlogofile")));
							});
							divp.appendChild(imgclose); 
							var pp = document.getElementById(elem).parentElement;
							pp.appendChild(divp);
						}
         		else
         			document.getElementById(elem).value = data;
        	}
      	})
      }

			function fldCustomProp( elem, prop, typ)
      {
        var retour;      
        
      	var obj = { bouticid: bouticid, action:"getCustomProp", tables:tables, table:"", prop:prop };
     	
        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
         		var btnvalid, btncancel;
         		var savdata = data;
         		document.getElementById(elem).setAttribute("data-prop", prop);
         		document.getElementById(elem).setAttribute("data-typ", typ);
         		document.getElementById(elem).setAttribute("data-modified", 'false');
         		document.getElementById(elem).setAttribute("data-paramtype", 'csp');
         		if (typ == "prix")
         		{
         	 		document.getElementById(elem).value = parseFloat(data).toFixed(2);
         		}
         		else if (typ == "bool")
         		{
         			if (data == "1")
         				document.getElementById(elem).checked = true;
         			else
         				document.getElementById(elem).checked = false;
         		}
         		else if (typ == "image")
         		{
         			var inp = document.getElementById("artlogofile"); 

							inp.setAttribute("data-artlogo", "artlogo" );
							inp.filename = data[0];
							inp.setAttribute("data-logotruefilename", data[0]);
							inp.setAttribute("data-artlogo", 'artlogo' );

							inp.onchange = function () {
								const fileInput = this;
								const formdata = new FormData();
								formdata.append('file', fileInput.files[0]);
								
				        fetch("boupload.php", {
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
									else {
										fileInput.setAttribute("data-modified", 'true');
										document.getElementById(fileInput.getAttribute("data-artlogo")).src = pathimg + data;
		        				fileInput.setAttribute("data-logotruefilename", data);
		        				fileInput.filename = data;
		        				imgclose.style.display = 'block';
									}						         	
				        })
							}
							var divp = document.createElement("DIV");
							divp.classList.add("frameimg");
	
							var image = document.createElement("IMG");
							image.id = "artlogo";
							image.alt = "";
							image.classList.add("imgart");
							if (data[0] != "")
								image.src = pathimg + data[0];
							divp.appendChild(image);
	
							var imgclose = document.createElement("IMG");
							imgclose.id = 'logofermer';
							imgclose.src = '../img/fermer.png';
							imgclose.alt = "";
							imgclose.classList.add("imgclose");
							if (inp.filename == "")
								imgclose.style.display = 'none';
							if (data[0] == "")
								imgclose.style.display = 'none';
							imgclose.setAttribute("data-artlogofile", "artlogofile" );
							imgclose.setAttribute("data-artlogo", "artlogo" );
							imgclose.addEventListener("click", function() {
								document.getElementById(this.getAttribute("data-artlogofile")).setAttribute("data-modified", 'true');
								document.getElementById(this.getAttribute("data-artlogofile")).setAttribute("data-logotruefilename",'');
								document.getElementById(this.getAttribute("data-artlogofile")).value = '';
								document.getElementById(this.getAttribute("data-artlogo")).removeAttribute("src");
								this.style.display = 'none';
								persoenblbtnvc(document.getElementById(this.getAttribute("data-artlogofile"))); 
							});
							divp.appendChild(imgclose); 
							var pp = document.getElementById(elem).parentElement;
							pp.appendChild(divp);
         		}
         		else if (typ == "url")
         		{
         			document.getElementById(elem).value = data;
         			document.getElementById("linkid").innerHTML = protocole + server + "/" + data;
         			document.getElementById("linkid").href = protocole + server + "/" + data;
         		}
         		else
         			document.getElementById(elem).value = data;
         	}
      	})
      }
      
      function persoenblbtnvc(elem) 
      {
     		elem.setAttribute('data-modified', 'true');
				document.getElementById('validpersoid').disabled = false;
				document.getElementById('cancelpersoid').disabled = false;
      }      
      
      function paramenblbtnvc(elem) 
      {
     		elem.setAttribute('data-modified', 'true');
				document.getElementById('validparamid').disabled = false;
				document.getElementById('cancelparamid').disabled = false;
      }
			
			function validparamupdate()
			{
				var notvalidated = false;
				var pelem = document.getElementsByClassName("fieldparam");
				for (var j=0;j<pelem.length;j++) 
				{
					var el = pelem[j];
					if (el.checkValidity() == false)
					{
						notvalidated = true;
						if (el.getAttribute('data-paramtype') == "csp")
              alert( (el.getAttribute("data-lbl") !== null ? el.getAttribute("data-lbl") + " : " : "") + (el.getAttribute("title") !== null ? el.getAttribute("title") + " - " : "") + el.validationMessage);
						else
              alert( (el.getAttribute("data-lbl") !== null ? el.getAttribute("data-lbl") + " : " : "") + (el.getAttribute("title") !== null ? el.getAttribute("title") + " - " : "") + el.validationMessage);
					}
				}
				if (notvalidated == false)
				{
					for (var i=0;i<pelem.length;i++) 
					{
						var el = pelem[i];
						if (el.getAttribute("data-modified")== 'true')
						{
		     			var valeur;
							if (el.getAttribute("data-typ") == "prix")
		     				valeur = parseFloat(el.value).toFixed(2);
		     			else if (el.getAttribute("data-typ") == "bool")
		         	{
			 	      	if (el.checked == true)
			  	     		valeur = "1";
			     	   	else
			   	     		valeur = "0";
		   	     	}
		   	     	else if (el.getAttribute("data-typ") == "image")
		   	     	{
	         			valeur = document.getElementById("artlogofile").getAttribute("data-logotruefilename");
	       				savdata = valeur;
		   	     	}
  		   	    else if (el.getAttribute("data-typ") == "url")
           		{
           			valeur = el.value;
           		}
		     			else
		     				valeur = el.value;
		     			
		     			var obj2;
							if (el.getAttribute('data-paramtype') == "csp")
							{
			     			var prop = el.getAttribute("data-prop");
			      		obj2 = { bouticid: bouticid, action:"setCustomProp", tables:tables, table:"", prop:prop, valeur:valeur };
			      		
			      	}
			     		else {
			     			var param = el.getAttribute("data-param");
			     			obj2 = { bouticid: bouticid, action:"setparam", tables:tables, table:"parametre", param:param, valeur:valeur };
			     		}
	
	 		      	el.setAttribute('data-modified', 'false');
	
			        fetch("boquery.php", {
			          method: "POST",
			          headers: {
			        		'Content-Type': 'application/json',
			        		'Accept': 'application/json'
			          },
			          body: JSON.stringify(obj2)
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
			         	else {
									document.getElementById('validparamid').disabled = true;
									document.getElementById('cancelparamid').disabled = true;
			         	}
		       		})
						}
					}
				}				
			}
			
			function fldClientProp( elem, prop, typ)
      {
        var retour;      
        
      	var obj = { bouticid: bouticid, action:"getClientProp", tables:tables, table:"", prop:prop };
     	
        fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj)
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
         		var btnvalid, btncancel;
         		var savdata = data;
         		document.getElementById(elem).setAttribute("data-prop", prop);
         		document.getElementById(elem).setAttribute("data-typ", typ);
         		document.getElementById(elem).setAttribute("data-modified", 'false');
         		document.getElementById(elem).setAttribute("data-paramtype", 'clt');
         		if (typ == "prix")
         		{
         	 		document.getElementById(elem).value = parseFloat(data).toFixed(2);
         		}
         		else if (typ == "bool")
         		{
         			if (data == "1")
         				document.getElementById(elem).checked = true;
         			else
         				document.getElementById(elem).checked = false;
         		}
         		else if (typ == "image")
         		{
         			var inp = document.getElementById("artlogofile"); 

							inp.setAttribute("data-artlogo", "artlogo" );
							inp.filename = data[0];
							inp.setAttribute("data-logotruefilename", data[0]);
							inp.setAttribute("data-artlogo", 'artlogo' );

							inp.onchange = function () {
								const fileInput = this;
								const formdata = new FormData();
								formdata.append('file', fileInput.files[0]);
								
				        fetch("boupload.php", {
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
									else {
										fileInput.setAttribute("data-modified", 'true');
										document.getElementById(fileInput.getAttribute("data-artlogo")).src = pathimg + data;
		        				fileInput.setAttribute("data-logotruefilename", data);
		        				fileInput.filename = data;
		        				imgclose.style.display = 'block';
									}						         	
				        })
							}
							var divp = document.createElement("DIV");
							divp.classList.add("frameimg");
	
							var image = document.createElement("IMG");
							image.id = "artlogo";
							image.alt = "";
							image.classList.add("imgart");
							if (data[0] != "")
								image.src = pathimg + data[0];
							divp.appendChild(image);
	
							var imgclose = document.createElement("IMG");
							imgclose.id = 'logofermer';
							imgclose.src = '../img/fermer.png';
							imgclose.alt = "";
							imgclose.classList.add("imgclose");
							if (inp.filename == "")
								imgclose.style.display = 'none';
							if (data[0] == "")
								imgclose.style.display = 'none';
							imgclose.setAttribute("data-artlogofile", "artlogofile" );
							imgclose.setAttribute("data-artlogo", "artlogo" );
							imgclose.addEventListener("click", function() {
								document.getElementById(this.getAttribute("data-artlogofile")).setAttribute("data-modified", 'true');
								document.getElementById(this.getAttribute("data-artlogofile")).setAttribute("data-logotruefilename",'');
								document.getElementById(this.getAttribute("data-artlogofile")).value = '';
								document.getElementById(this.getAttribute("data-artlogo")).removeAttribute("src");
								this.style.display = 'none';
								persoenblbtnvc(document.getElementById(this.getAttribute("data-artlogofile"))); 
							});
							divp.appendChild(imgclose); 
							var pp = document.getElementById(elem).parentElement;
							pp.appendChild(divp);
         		}
         		else if (typ == "radio")
         		{
         		  if (document.getElementById(elem).value == data)
         		   document.getElementById(elem).checked = true;
         		}
         		else if (typ == "pass")
         		{
         		  document.getElementById(elem).value ="";
         		} 
         		else
         			document.getElementById(elem).value = data;
         	}
      	})
      }
      
      function persoenblbtnvc(elem) 
      {
     		elem.setAttribute('data-modified', 'true');
				document.getElementById('validpersoid').disabled = false;
				document.getElementById('cancelpersoid').disabled = false;
      }      
      
      function paramenblbtnvc(elem) 
      {
     		elem.setAttribute('data-modified', 'true');
				document.getElementById('validparamid').disabled = false;
				document.getElementById('cancelparamid').disabled = false;
      }
      function clientenblbtnvc(elem) 
      {
        if (elem.type == 'radio')
        {
          var radios = document.getElementsByName(elem.name);
          for (var i=0; i<radios.length;  i++) {
          	radios[i].setAttribute('data-modified', 'true');
          }
        }
        else
          elem.setAttribute('data-modified', 'true');
				document.getElementById('validclientid').disabled = false;
				document.getElementById('cancelclientid').disabled = false;
      }      
			
			function validparamupdate()
			{
				var notvalidated = false;
				var pelem = document.getElementsByClassName("fieldparam");
				for (var j=0;j<pelem.length;j++) 
				{
					var el = pelem[j];
					if (el.checkValidity() == false)
					{
						notvalidated = true;
						if (el.getAttribute('data-paramtype') == "csp")
              alert( (el.getAttribute("data-lbl") !== null ? el.getAttribute("data-lbl") + " : " : "") + (el.getAttribute("title") !== null ? el.getAttribute("title") + " - " : "") + el.validationMessage);
						else
              alert( (el.getAttribute("data-lbl") !== null ? el.getAttribute("data-lbl") + " : " : "") + (el.getAttribute("title") !== null ? el.getAttribute("title") + " - " : "") + el.validationMessage);
					}
				}
				if (notvalidated == false)
				{
					for (var i=0;i<pelem.length;i++) 
					{
						var el = pelem[i];
						if (el.getAttribute("data-modified")== 'true')
						{
		     			var valeur;
							if (el.getAttribute("data-typ") == "prix")
		     				valeur = parseFloat(el.value).toFixed(2);
		     			else if (el.getAttribute("data-typ") == "bool")
		         	{
			 	      	if (el.checked == true)
			  	     		valeur = "1";
			     	   	else
			   	     		valeur = "0";
		   	     	}
		   	     	else if (el.getAttribute("data-typ") == "image")
		   	     	{
	         			valeur = document.getElementById("artlogofile").getAttribute("data-logotruefilename");
	       				savdata = valeur;
		   	     	}
		     			else
		     				valeur = el.value;
		     			
		     			var obj2;
							if (el.getAttribute('data-paramtype') == "csp")
							{
			     			var prop = el.getAttribute("data-prop");
			      		obj2 = { bouticid: bouticid, action:"setCustomProp", tables:tables, table:"", prop:prop, valeur:valeur };
			      		
			      	}
			     		else {
			     			var param = el.getAttribute("data-param");
			     			obj2 = { bouticid: bouticid, action:"setparam", tables:tables, table:"parametre", param:param, valeur:valeur };
			     		}
	
	 		      	el.setAttribute('data-modified', 'false');
	
			        fetch("boquery.php", {
			          method: "POST",
			          headers: {
			        		'Content-Type': 'application/json',
			        		'Accept': 'application/json'
			          },
			          body: JSON.stringify(obj2)
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
			         	else {
									document.getElementById('validparamid').disabled = true;
									document.getElementById('cancelparamid').disabled = true;
			         	}
		       		})
						}
					}
				}				
			}

			function cancelparamupdate()
			{
				var pelem = document.getElementsByClassName("fieldparam");
				for (var i=0;i<pelem.length;i++) 
				{
					var el = pelem[i];
					if (el.getAttribute("data-modified") == 'true')
						subcancelparamupdate(el);
				}
			}

			
			function subcancelparamupdate(elem)
			{
				var paramtype = elem.getAttribute("data-paramtype");
				
				if (elem.getAttribute('data-paramtype') == "csp")
				{
     			var prop = elem.getAttribute("data-prop");
     			
      		obj2 = { bouticid: bouticid, action:"getCustomProp", tables:tables, table:"", prop:prop };
      	}
     		else 
     		{
     			var param = elem.getAttribute("data-param");
     			obj2 = { bouticid: bouticid, action:"getparam", tables:tables, table:"parametre", param:param };
     		}
				
				fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj2)
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
         		if (elem.getAttribute('data-typ') == "prix")
         	 		elem.value = parseFloat(data).toFixed(2);
       			if (elem.getAttribute('data-typ') == "bool")
       			{
 	       			if (data == "1")
  	     				elem.checked = true;
     	   			else
   	     				elem.checked = false;
 	     			}
						else if (elem.getAttribute('data-typ') == "image")
						{
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogofile")).setAttribute("data-logotruefilename", data);
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogo")).src = pathimg + data;
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogofile")).value = '';
							document.getElementById("logofermer").style.display = 'block';
							if (data == "")
								document.getElementById('logofermer').style.display = 'none';
							if (pathimg + data == "")
								document.getElementById('logofermer').style.display = 'none';
						}
         		else
	       			elem.value = data;
					}
				})
				elem.setAttribute("data-modified", 'false');
				document.getElementById('validparamid').disabled = true;
				document.getElementById('cancelparamid').disabled = true;
			}

      function validpersoupdate()
      {
        var notvalidated = false;
        var pelem = document.getElementsByClassName("fieldperso");
        for (var j=0;j<pelem.length;j++) 
        {
          var el = pelem[j];
          if (el.checkValidity() == false)
          {
             notvalidated = true;
             if (el.getAttribute('data-paramtype') == "csp")
               alert( (el.getAttribute("data-lbl") !== null ? el.getAttribute("data-lbl") + " : " : "") + (el.getAttribute("title") !== null ? el.getAttribute("title") + " - " : "") + el.validationMessage);
             else
               alert( (el.getAttribute("data-lbl") !== null ? el.getAttribute("data-lbl") + " : " : "") + (el.getAttribute("title") !== null ? el.getAttribute("title") + " - " : "") + el.validationMessage);
          }
        }
        if (notvalidated == false)
        {
          for (var i=0;i<pelem.length;i++) 
          {
             var el = pelem[i];
             if (el.getAttribute("data-modified")== 'true')
             {
               var valeur;
               if (el.getAttribute("data-typ") == "prix")
                 valeur = parseFloat(el.value).toFixed(2);
               else if (el.getAttribute("data-typ") == "bool")
               {
                 if (el.checked == true)
                   valeur = "1";
                  else
                    valeur = "0";
               }
               else if (el.getAttribute("data-typ") == "image")
               {
                 valeur = document.getElementById("artlogofile").getAttribute("data-logotruefilename");
                 savdata = valeur;
               }
               else if (el.getAttribute("data-typ") == "url")
               {
                 valeur = el.value;
                 document.getElementById("linkid").innerHTML = protocole + server + "/" + valeur;
                 document.getElementById("linkid").href = protocole + server + "/" + valeur;
               }
               else
                 valeur = el.value;
             
               var obj2;
               if (el.getAttribute('data-paramtype') == "csp")
               {
                 var prop = el.getAttribute("data-prop");
                 var typ = el.getAttribute("data-typ");
                 obj2 = { bouticid: bouticid, action:"setCustomProp", tables:tables, table:"", prop:prop, valeur:valeur, typ:typ };
               }
               else 
               {
                 var param = el.getAttribute("data-param");
                 var typ = el.getAttribute("data-typ");
                 obj2 = { bouticid: bouticid, action:"setparam", tables:tables, table:"parametre", param:param, valeur:valeur, typ:typ };
               }
  
               el.setAttribute('data-modified', 'false');
    
               fetch("boquery.php", {
                 method: "POST",
                 headers: {
                   'Content-Type': 'application/json',
                   'Accept': 'application/json'
                 },
                 body: JSON.stringify(obj2)
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
                   document.getElementById('validpersoid').disabled = true;
                   document.getElementById('cancelpersoid').disabled = true;
                   if (data == "KO") 
                   {
                     fldCustomProp("pbaliasid", "customer", "url");
                     var modal = $('.modal');
                     $('.modal-title').html('Impossible de continuer');
                     modal.find('.modal-body').text("Ce nom de boutic est déjà utilisé");
                     $('.modal').modal('show');
                   }
                 }
               })
            }
          }
        }        
      }

			function cancelpersoupdate()
			{
				var pelem = document.getElementsByClassName("fieldperso");
				for (var i=0;i<pelem.length;i++) 
				{
					var el = pelem[i];
					if (el.getAttribute("data-modified") == 'true')
						subcancelpersoupdate(el);
				}
			}

			
			function subcancelpersoupdate(elem)
			{
				var paramtype = elem.getAttribute("data-paramtype");
				
				if (elem.getAttribute('data-paramtype') == "csp")
				{
     			var prop = elem.getAttribute("data-prop");
     			
      		obj2 = { bouticid: bouticid, action:"getCustomProp", tables:tables, table:"", prop:prop };
      	}
     		else 
     		{
     			var param = elem.getAttribute("data-param");
     			obj2 = { bouticid: bouticid, action:"getparam", tables:tables, table:"parametre", param:param };
     		}
				
				fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj2)
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
         		if (elem.getAttribute('data-typ') == "prix")
         	 		elem.value = parseFloat(data).toFixed(2);
       			if (elem.getAttribute('data-typ') == "bool")
       			{
 	       			if (data == "1")
  	     				elem.checked = true;
     	   			else
   	     				elem.checked = false;
 	     			}
						else if (elem.getAttribute('data-typ') == "image")
						{
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogofile")).setAttribute("data-logotruefilename", data);
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogo")).src = pathimg + data;
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogofile")).value = '';
							document.getElementById("logofermer").style.display = 'block';
							if (data == "")
								document.getElementById('logofermer').style.display = 'none';
							if (pathimg + data == "")
								document.getElementById('logofermer').style.display = 'none';
						}
         		else
	       			elem.value = data;
					}
				})
				elem.setAttribute("data-modified", 'false');
				document.getElementById('validpersoid').disabled = true;
				document.getElementById('cancelpersoid').disabled = true;
			}
			
      function validclientupdate()
      {
        var notvalidated = false;
        var pelem = document.getElementsByClassName("fieldclient");
        var pass, pasconf;
        for (var j=0;j<pelem.length;j++) 
        {
          var el = pelem[j];
          if (el.checkValidity() == false)
          {
            notvalidated = true;
            if (el.getAttribute('data-paramtype') == "clt")
              alert( (el.getAttribute("data-lbl") !== null ? el.getAttribute("data-lbl") + " : " : "") + (el.getAttribute("title") !== null ? el.getAttribute("title") + " - " : "") + el.validationMessage);
            else
             alert( (el.getAttribute("data-lbl") !== null ? el.getAttribute("data-lbl") + " : " : "") + (el.getAttribute("title") !== null ? el.getAttribute("title") + " - " : "") + el.validationMessage);
          }
          if (el.getAttribute("data-typ")== "pass")
          {
            pass = el.value;
            passconf = document.getElementById("clpassconfid").value;
            if (pass != passconf)
            {
              notvalidated = true;
              alert("Les mots de passe ne correspondent pas");
            }
          } 
        }
        if ((pass != passconf) && (notvalidated == false))
        {
          notvalidated = true;
          alert("Les mots de passe ne correspondent pas");
        }
        if (notvalidated == false)
        {
          for (var i=0;i<pelem.length;i++) 
          {
             var el = pelem[i];
             if (el.getAttribute("data-modified")== 'true')
             {
               var valeur;
               if (el.getAttribute("data-typ") == "prix")
                 valeur = parseFloat(el.value).toFixed(2);
               else if (el.getAttribute("data-typ") == "bool")
               {
                 if (el.checked == true)
                   valeur = "1";
                  else
                    valeur = "0";
               }
               else if (el.getAttribute("data-typ") == "image")
               {
                 valeur = document.getElementById("artlogofile").getAttribute("data-logotruefilename");
                 savdata = valeur;
               }
               else
                 valeur = el.value;

               var obj2;
               if (el.getAttribute('data-paramtype') == "clt")
               {
                 var prop = el.getAttribute("data-prop");
                 var typ = el.getAttribute("data-typ");
                 obj2 = { bouticid: bouticid, action:"setClientProp", tables:tables, table:"", prop:prop, valeur:valeur, typ:typ };
               }
               el.setAttribute('data-modified', 'false');
               fetch("boquery.php", {
                 method: "POST",
                 headers: {
                   'Content-Type': 'application/json',
                   'Accept': 'application/json'
                 },
                 body: JSON.stringify(obj2)
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
                   document.getElementById('validclientid').disabled = true;
                   document.getElementById('cancelclientid').disabled = true;
                 }
               })
            }
          }
        }        
      }

			function cancelclientupdate()
			{
				var pelem = document.getElementsByClassName("fieldclient");
				for (var i=0;i<pelem.length;i++) 
				{
					var el = pelem[i];
					if (el.getAttribute("data-modified") == 'true')
						subcancelclientupdate(el);
				}
			}

			
			function subcancelclientupdate(elem)
			{
				var paramtype = elem.getAttribute("data-paramtype");
				
				if (elem.getAttribute('data-paramtype') == "clt")
				{
     			var prop = elem.getAttribute("data-prop");
     			
      		obj2 = { bouticid: bouticid, action:"getClientProp", tables:tables, table:"", prop:prop };
      	}
				
				fetch("boquery.php", {
          method: "POST",
          headers: {
        		'Content-Type': 'application/json',
        		'Accept': 'application/json'
          },
          body: JSON.stringify(obj2)
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
         		if (elem.getAttribute('data-typ') == "prix")
         	 		elem.value = parseFloat(data).toFixed(2);
       			if (elem.getAttribute('data-typ') == "bool")
       			{
 	       			if (data == "1")
  	     				elem.checked = true;
     	   			else
   	     				elem.checked = false;
 	     			}
						else if (elem.getAttribute('data-typ') == "image")
						{
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogofile")).setAttribute("data-logotruefilename", data);
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogo")).src = pathimg + data;
							document.getElementById(document.getElementById("logofermer").getAttribute("data-artlogofile")).value = '';
							document.getElementById("logofermer").style.display = 'block';
							if (data == "")
								document.getElementById('logofermer').style.display = 'none';
							if (pathimg + data == "")
								document.getElementById('logofermer').style.display = 'none';
						}
            else if (elem.getAttribute('data-typ') == "radio")
            {
	       			if (elem.value == data)
	       			 elem.checked = true;
	       	  }
       			else if (elem.getAttribute('data-typ') == "pass")
       			{
       			   elem.value = "";
       			   if (elem.hasAttribute("data-conffldid") == true)
       			     document.getElementById(elem.getAttribute("data-conffldid")).value = "";
       			}
            else
       			  elem.value = data;
					}
				})
				elem.setAttribute("data-modified", 'false');
				document.getElementById('validclientid').disabled = true;
				document.getElementById('cancelclientid').disabled = true;
			}


			function razctrl()
			{
				var yn = confirm("Vous êtes sur le point de supprimer toute les mémoires de pagination ! Confirmez-vous ?");
				if (yn == true)
				{
					var total = localStorage.length;
					var list = new Array();
					for (var i=0; i<total; i++)
					{
						var key = localStorage.key(i);
						if (key.startsWith("praticboutic_ctrl_" + server + "_" + login))
						{
							list.push(key);
						}
					}
					while (list.length > 0)
					{
						localStorage.removeItem(list.pop());
					}
				}
			}
      
    </script>
  </body>
</html>
