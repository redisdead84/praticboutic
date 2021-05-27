<?php
	session_start();

  if (empty($_SESSION['boutic']) == TRUE)
  {
 	  header("LOCATION: index.php");
 	  exit();
 	}
  else	
	  $boutic = $_SESSION['boutic'];
	
  if (empty($_SESSION[$boutic . '_auth']) == TRUE)
  {
 	  header("LOCATION: index.php");
 	  exit();
  }	
  
  if (strcmp($_SESSION[$boutic . '_auth'],'oui') != 0)
  {
 	  header("LOCATION: index.php");
 	  exit();
  }
     
  include "../config/common_cfg.php";
  include "../param.php";
    
  
?>

<!DOCTYPE html>
<html id="backhtml">
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.10">
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
	var boutic = "<?php echo $boutic;?>" ;
	var pathimg = '../../' + boutic + '/upload/';

	
	var deflimite = 5;
	var defoffset = 0;
	var offset = 0;  
  
	var tables = [
								{nom:"categorie", desc:"Catégories", cs:"nom", champs:[{nom:"catid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""},{nom:"nom", desc:"Nom", typ:"ref", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"visible", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}]},
	              {nom:"article", desc:"Articles", cs:"nom", champs:[{nom:"artid", desc:"Identifiant", typ:"pk", defval:"", vis:"n", ordre:"0", sens:""},{nom:"nom", desc:"Nom", typ:"ref", defval:"", vis:"o", ordre:"0", sens:""}, {nom:"prix", desc:"Prix", typ:"prix", defval:"0.00", vis:"o", ordre:"0", sens:""}, {nom:"description", desc:"Description", typ:"text", defval:"", vis:"n", ordre:"0", sens:""}, 
	                {nom:"visible", desc:"Actif", typ:"bool", defval:"1", vis:"o", ordre:"0", sens:""}, {nom:"catid", desc:"Catégorie", typ:"fk", defval:"", vis:"o", ordre:"0", sens:""},
	                {nom:"unite", desc:"Unité", typ:"text", defval:"€", vis:"n", ordre:"0", sens:""}, {nom:"image", desc:"Fichier Image", typ:"image", defval:"", vis:"n", ordre:"0", sens:""}, {nom:"obligatoire", desc:"Frais de Préparation", typ:"bool", defval:"0", vis:"n", ordre:"0", sens:""}]},
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

  $(function() {
    gettable( "table0", "table0", "categorie", deflimite, defoffset);
    gettable( "table1", "table1", "article", deflimite, defoffset);
    //gettable( "table2", "table2", "relgrpoptart", deflimite, defoffset);
    gettable( "table3", "table3", "groupeopt", deflimite, defoffset);
    //gettable( "table4", "table4", "option", deflimite, defoffset);
    gettable( "table5", "table5", "administrateur", deflimite, defoffset);
    gettable( "table6", "table6", "parametre", deflimite, defoffset);
    gettable( "table7", "table7", "cpzone", deflimite, defoffset);
    gettable( "table8", "table8", "barlivr", deflimite, defoffset);
    gettable( "table9", "table9", "commande", deflimite, defoffset);
    gettable( "table11", "table11", "statutcmd", deflimite, defoffset);
  });
 
  </script>
  
	  <div class="vertical-nav" id="sidebar">
	  	<ul class="nav nav-menu flex-column">
	  		<img id='logopblid' src='img/LOGO_PRATIC_BOUTIC.png' />
			  <span class="navbar-text breakword">
			    Bienvenue sur le backoffice de la boutic <?php echo $boutic;?>, <?php echo $_SESSION[$boutic . '_email']; ?> ! 
			  </span>
			  <li class="nav-item">
			    <a class="nav-link active" id="commandes-tab" data-toggle="tab" href="#commandes" role="tab" aria-controls="commandes" aria-selected="false"><img class='picto' src='img/picto_mes-commandes.png' />Mes Commandes</a>
			  </li>
			  <li class="nav-item">
			    <a class="nav-link" id="produit-tab" data-toggle="tab" href="#produit" role="tab" aria-controls="produit" aria-selected="false"><img class='picto' src='img/picto_mes-produits.png' />Mes Produits</a>
			  </li>
 			  <li class="nav-item">
			    <a class="nav-link" id="livraison-tab" data-toggle="tab" href="#livraison" role="tab" aria-controls="livraison" aria-selected="false"><img class='picto' src='img/LIVRAISON.png' />Livraisons</a>
			  </li>			  
 			  <li class="nav-item">
			    <a class="nav-link" id="administration-tab" data-toggle="tab" href="#administration" role="tab" aria-controls="administration" aria-selected="false"><img class='picto' src='img/picto_mon_compte.png' />Administration</a>
			  </li>			  
 			  <li class="nav-item">
			    <a class="nav-link" href="logout.php"><p class="nopicto">Deconnexion</p></a>
			  </li>
			</ul>
		</div>
		<div class="tab-content page-content" id="myMenuContent">
			<div class="tab-pane active" id="commandes" role="tabpanel" aria-labelledby="commandes-tab">
				<p class="title">Commandes</p>
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="commande-tab" data-toggle="tab" href="#commande" role="tab" aria-controls="commande" aria-selected="true">COMMANDES CLIENTS</a>
						</li>
						<?php if (strcmp($statutcmd, "n")==0) echo "<!--" ?>
						<li class="nav-item">
							<a class="nav-link" id="statutcmd-tab" data-toggle="tab" href="#statutcmd" role="tab" aria-controls="statutcmd" aria-selected="false">STATUTS DES COMMANDES</a>
						</li>
						<?php if (strcmp($statutcmd, "n")==0) echo "-->" ?>
					</ul>
				<div class="tab-content" id="myTabCmdContent">
					<div class="tab-pane active" id="commande" role="tabpanel" aria-labelledby="commande-tab">
					  <div class='tbl' id="table9"></div>
					  <div class='tbl form-group' id="det9" data-vuep="table9" hidden></div>
					  <div class='tbl form-group' id="det10" data-vuep="det9" hidden></div>
					</div>
					<div class="tab-pane" id="statutcmd" role="tabpanel" aria-labelledby="statutcmd-tab">
				  	<div class='tbl' id="table11"></div>	
			 	  	<div class='tbl form-group' id="ins11" data-vuep="table11" hidden></div>
			 	  	<div class='tbl form-group' id="maj11" data-vuep="table11" hidden></div>	
					</div>
				</div>
			</div>
			<div class="tab-pane" id="produit" role="tabpanel" aria-labelledby="produit-tab">
				<p class="title">Produits</p>
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="categorie-tab" data-toggle="tab" href="#categorie" role="tab" aria-controls="categorie" aria-selected="true">CATEGORIES</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="article-tab" data-toggle="tab" href="#article" role="tab" aria-controls="article" aria-selected="false">PRODUITS</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="groupeopt-tab" data-toggle="tab" href="#groupeopt" role="tab" aria-controls="groupeopt" aria-selected="false">GROUPES D'OPTION</a>
					</li>
				</ul>
				<div class="tab-content" id="myTabProdContent">
					<div class="tab-pane active" id="categorie" role="tabpanel" aria-labelledby="categorie-tab">
					  <div class='tbl' id="table0"></div>
					  <div class='tbl form-group' id="ins0" data-vuep="table0" hidden></div>
					  <div class='tbl form-group' id="maj0" data-vuep="table0" hidden></div>
					</div>
					<div class="tab-pane" id="article" role="tabpanel" aria-labelledby="article-tab">
				  	<div class='tbl' id="table1"></div>	
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
			<p class="title">Administration</p>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="administrateur-tab" data-toggle="tab" href="#administrateur" role="tab" aria-controls="administrateur" aria-selected="false">UTILISATEURS</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="parametre-tab" data-toggle="tab" href="#parametre" role="tab" aria-controls="parametre" aria-selected="false">PARAMETRES</a>
				</li>
			</ul>
			<div class="tab-content" id="myTabAdminContent">
				<div class="tab-pane active" id="administrateur" role="tabpanel" aria-labelledby="administrateur-tab">
				  <div class='tbl' id="table5"></div>	
			 	  <div class='tbl form-group' id="ins5" data-vuep="table5" hidden></div>
			 	  <div class='tbl form-group' id="maj5" data-vuep="table5" hidden></div>	
				</div>
					<div class="tab-pane" id="parametre" role="tabpanel" aria-labelledby="parametre-tab">
				  <div class='tbl' id="table6"></div>	
			 	  <div class='tbl' id="ins6" data-vuep="table6" hidden></div>
		 	  	<div class='tbl' id="maj6" data-vuep="table6" hidden></div>	
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
										formdata.append('boutic', boutic);
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
									vue.appendChild(divp);
									vue.appendChild(document.createElement("BR"));
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
								
								vue.appendChild(lbl);
								inp.name = 'itable' + numtable + '_' + champs[i].nom;
								inp.id = 'itable' + numtable + '_' + 'inp' + i;
								inp.setAttribute("data-table",tables[numtable].nom);
								inp.setAttribute("data-champ",champs[i].nom);
								vue.appendChild(inp);
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
										vue.appendChild(lbl);
										vue.appendChild(lien);
										for (var k=0; k<tables.length; k++)
										{
											if (tables[k].nom == liens[j].dsttbl)
												getoptions(	'itable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs );      
										}
									}
								}
							}
							var br = document.createElement('br');
							vue.appendChild(br);
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
					  		errmsg = fld.title;
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
   			    //alert(fld.title);
       			$('.modal').modal('show');
					}
				}; 

				vue.appendChild(okbtn);
				
				var clbtn = document.createElement('button');
				clbtn.id = "clbtn" + numtable;
				clbtn.type = "button";
				clbtn.innerHTML = "Cancel";
				clbtn.classList.add("btn");
				clbtn.classList.add("btn-secondary");
				clbtn.classList.add("btn-block");
				clbtn.setAttribute("data-vuep", vueparent);
				clbtn.setAttribute("data-vue", 'ins' + numtable);
				clbtn.onclick = function(){
					vuep = document.getElementById(this.getAttribute("data-vuep"));
					vue = document.getElementById(this.getAttribute("data-vue"));

					vuep.hidden = false
					vue.hidden = true;
					vue.innerHTML = '';
				}; 
				vue.appendChild(clbtn);

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
				
				var obj = { customer: boutic, action:"getvalues", tables:tables, table:tables[numtable].nom, liens:liens, colonne:"", row:"", idtoup:idtoup };

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
										vue.appendChild(lbl);
										var inp = document.createElement('input');
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
												formdata.append('boutic', boutic);
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
											image.id = 'utable' + numtable + '_' + 'artimg' + i;;
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
											vue.appendChild(divp);
											vue.appendChild(document.createElement("BR"));
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
										vue.appendChild(inp);
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
												vue.appendChild(lbl);
												
												for (k=0; k<tables.length; k++)
													if (tables[k].nom == liens[j].dsttbl)
														getoptions('utable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs, data[i]) ;
												
												vue.appendChild(lien);
											}
										}
									}
									var br = document.createElement('br');
									vue.appendChild(br);
								}
							}
						}
						
						var lnk = vue.getAttribute("data-lnkchild");
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
							
							vue.appendChild(titre);
			
							var rgp = document.createElement('DIV');
							rgp.classList.add("tbl");
							rgp.classList.add("form-group");
							rgp.id = "tablesub" + subnumtable;
							rgp.hidden = false;
							vue.appendChild(rgp);

							gettable( "maj" + numtable, "tablesub" + subnumtable, tables[subnumtable].nom, deflimite, defoffset, jpk, idtoup);							
						
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
						vue.appendChild(okbtn);
						
						var clbtn = document.createElement('button');
						clbtn.id = "clbtn" + numtable;
						clbtn.type = "button";
						clbtn.innerHTML = "Cancel";
						clbtn.classList.add("btn");
						clbtn.classList.add("btn-secondary");
						clbtn.classList.add("btn-block");
						clbtn.setAttribute("data-vuep", vueparent);
						clbtn.setAttribute("data-vue", 'maj' + numtable);

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

			/*function relgrpoptart(numtable, idtoup, limite, offset ) 
			{
				var champs = tables[numtable].champs;
				document.getElementById('table' + numtable).hidden = true;
				document.getElementById('ins' + numtable).hidden = true;
				document.getElementById('maj' + numtable).hidden = true;
				if (numtable == 1)
					document.getElementById('opt' + numtable).hidden = false;
				var obj = { customer: boutic, action:"getcs", tables:tables, table:tables[numtable].nom, liens:liens, colonne:"", row:"", idtoup:idtoup };

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
						var titre = document.createElement('H5');
						titre.id = 'itable'+ numtable +'titre';
						titre.innerHTML = 'Sélection des groupes d\'option de l\'article : ' + data;
						document.getElementById('opt' + numtable).appendChild(titre);
		
						var rgp = document.createElement('DIV');
						rgp.classList.add("tbl");
						rgp.classList.add("form-group");
						rgp.id = "tablesubrgpa";
						rgp.hidden = false;
						document.getElementById('opt' + numtable).appendChild(rgp);
		
						gettable( "opt1", "tablesubrgpa", "relgrpoptart", deflimite, defoffset, "artid", idtoup);
						
						var clbtn = document.createElement('button');
						clbtn.id = "clbtn" + numtable;
						clbtn.type = "button";
						clbtn.innerHTML = "Close";
						clbtn.classList.add("btn");
						clbtn.classList.add("btn-primary");
						clbtn.classList.add("btn-block");
						clbtn.onclick = function(){
							document.getElementById('table' + numtable).hidden = false;
							document.getElementById('opt' + numtable).hidden = true;
							document.getElementById('opt' + numtable).innerHTML = "";
						}; 
						document.getElementById('opt' + numtable).appendChild(clbtn);
					}
      	})
			}

			function option(numtable, idtoup, limite, offset ) 
			{
				var champs = tables[numtable].champs;
				document.getElementById('table' + numtable).hidden = true;
				document.getElementById('ins' + numtable).hidden = true;
				document.getElementById('maj' + numtable).hidden = true;
				if (numtable == 3)
					document.getElementById('opt' + numtable).hidden = false;
				var obj = { customer: boutic, action:"getcs", tables:tables, table:tables[numtable].nom, liens:liens, colonne:"", row:"", idtoup:idtoup };

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
						var titre = document.createElement('H5');
						titre.id = 'itable'+ numtable +'titre';
						titre.innerHTML = 'Sélection des options du groupe d\'option : ' + data;
						document.getElementById('opt' + numtable).appendChild(titre);
		
						var rgp = document.createElement('DIV');
						rgp.classList.add("tbl");
						rgp.classList.add("form-group");
						rgp.id = "tablesubgopt";
						rgp.hidden = false;
						document.getElementById('opt' + numtable).appendChild(rgp);
		
						gettable( "opt4", "tablesubgopt", "option", deflimite, defoffset, "grpoptid", idtoup);
						
						var clbtn = document.createElement('button');
						clbtn.id = "clbtn" + numtable;
						clbtn.type = "button";
						clbtn.innerHTML = "Close";
						clbtn.classList.add("btn");
						clbtn.classList.add("btn-primary");
						clbtn.classList.add("btn-block");
						clbtn.onclick = function(){
							document.getElementById('table' + numtable).hidden = false;
							document.getElementById('opt' + numtable).hidden = true;
							document.getElementById('opt' + numtable).innerHTML = "";
						}; 
						document.getElementById('opt' + numtable).appendChild(clbtn);
					}
      	})
			}*/
			
			function changeFunc(vue, place, tablestr, $i, selcol="", selid=0) 
			{
				limite = $i;				
				gettable( vue, place, tablestr, limite, 0, selcol, selid);
   		}
   		
   		
 			/*function vartotable(vue, place, tablestr, donnees, total, limite, offset, selcol="", selid=0)
 			{
			 	var tab = '';
			 	var pkval;
			 	nummtable = getnumtable(tablestr);
			 	table = tables[numtable];

				if (tablestr == "lignecmd")
			 		tab = tab + '<div class=""><table class="table table-bordered table-striped"><thead><tr class="">';
			 	else	
			 		tab = tab + '<div class=""><table class="table table-bordered table-striped table-hover"><thead><tr class="">';
				for (var i=0; i<table.champs.length; i++)          	
			 	{
			 		if ((table.champs[i].typ != "pk") && (table.champs[i].vis != "n") && (table.champs[i].nom != selcol))
			 		{
			   		tab = tab + '<th class="">';
			   		if (table.champs[i].typ != "fk")
			   			tab = tab + table.champs[i].desc;
			   		else
			   		{
							for (var j=0; j<liens.length; j++)          	
			 				{
			 					if ((liens[j].srctbl == table.nom) && (liens[j].srcfld == table.champs[i].nom))
			 						tab = tab + liens[j].desc; 
							}
			   		}	
			   		tab = tab + '</th>';
			 		}
			 	}

			 	tab = tab + '</tr></thead><tbody>';
			 	for (var j=0; j<donnees.length; j++)
			 	{
					for (var i=0; i<donnees[j].length; i++)          	
			   	{
			   		if ((table.champs[i].typ != "pk") && (table.champs[i].vis != "n") && (table.champs[i].nom != selcol))
			 			{
			     		tab = tab + '<td class="">';
			     		
			     		if (table.champs[i].typ != "bool")
			     		{
			     			var val = donnees[j][i];
			     			if (table.champs[i].typ == "prix")
			     				tab = tab + parseFloat(val).toFixed(2);
			     			else if (table.champs[i].typ == "date")
								{
										const event = new Date(Date.parse(val));
										tab = tab + event.toLocaleString('fr-FR');
								}
			     			else
			     				tab = tab + val;	 
			     		}
			     		else {
			     			if (donnees[j][i] > 0)
			     				tab = tab + '<input type="checkbox" checked disabled>';
			     			else {
			     				tab = tab + '<input type="checkbox" disabled>';
			     			}
			     		}
			     		tab = tab + '</td>';
			   		}
			   		else if (table.champs[i].typ == "pk")
			   		{
			   			pkval = donnees[j][i];
							if (tablestr == "commande")
								tab = tab + '<tr onclick="detail(' + numtable + ',' + pkval + ',' + limite + ',' + offset + ')" class="clickable-row colored">';
							else if (tablestr == "lignecmd")
								tab = tab + '<tr onclick="">';
							else
								tab = tab + '<tr onclick="update(' + numtable + ',' + pkval + ',' + limite + ',' + offset + ',\'' + vue + '\',\'' + place + '\',\'' + selcol + '\',' + selid + ')" class="clickable-row">';
					  }
					  
			   	}
			   	tab = tab + '</tr>';    	
				}
			 	
			 	tab = tab + '</tbody></table></div>';
				if ((tablestr !== "commande") &&  (tablestr !== "lignecmd"))
			   	tab = tab + '<button class="btn btn-primary" onclick="insert(' + numtable + ',' + limite + ',' + offset + ',\'' + vue + '\',\'' + place + '\',\'' + selcol + '\',' + selid + ')">Insérer</button>';
			 	tab = tab + '<br>';
			 	tab = tab + '<label for="rpp '+ numtable + '">Nombre de résultat par page</label>';
			 	tab = tab + '<select onchange="changeFunc(\'' + vue + '\',\'' + place +'\',\'' + tablestr + '\',value,\'' + selcol  + '\',' + selid + ');" name="rpp' + numtable + '" id="rppid' + numtable + '">';
				for (var k=0; k<rpp.length; k++)
				{
					if (limite == rpp[k])
						tab = tab + '<option value="' + rpp[k] + '" selected>' + rpp[k] + '</option>';       	
					else
						tab = tab + '<option value="' + rpp[k] + '">' + rpp[k] + '</option>';       	
				}				
				vallimite = parseInt(limite);
				tab = tab + '</select>';				
				tab = tab + '<nav aria-label="Page navigation">';
				  tab = tab + '<ul class="pagination">';
				    if ((offset - vallimite) < 0)
					    tab = tab + '<li class="page-item disabled">';
					  else
					    tab = tab + '<li class="page-item">';
				      tab = tab + '<a class="page-link" href="javascript:gettable(\'' + vue + '\',\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (offset - vallimite) + ',\'' + selcol  + '\',' + selid + ')" aria-label="Previous">';
				        tab = tab + '<span aria-hidden="true">&laquo;</span>';
				        tab = tab + '<span class="sr-only">Previous</span>';
				      tab = tab + '</a>';
				    tab = tab + '</li>';
				    var totalpage = Math.ceil(total / vallimite);
				    for (var k=0; k<totalpage;k++)
				    {
				    	if ((offset/ vallimite) == k)
				    		tab = tab + '<li class="page-item active"><a class="page-link" href="javascript:gettable(\'' + vue + '\',\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (k*limite) + ',\'' + selcol  + '\',' + selid + ')">' + k + '</a></li>';
				    	else
				    		tab = tab + '<li class="page-item"><a class="page-link" href="javascript:gettable(\'' + vue + '\',\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (k*limite) + ',\'' + selcol  + '\',' + selid + ')">' + k + '</a></li>';
				    }
				    if ((offset + vallimite) >= total)
				    	tab = tab + '<li class="page-item disabled">';
				    else
				    	tab = tab + '<li class="page-item">';
				      tab = tab + '<a class="page-link" href="javascript:gettable(\'' + vue + '\',\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (offset + vallimite) + ',\'' + selcol  + '\',' + selid + ')" aria-label="Next">';
				        tab = tab + '<span aria-hidden="true">&raquo;</span>';
				        tab = tab + '<span class="sr-only">Next</span>';
				      tab = tab + '</a>';
				    tab = tab + '</li>';
				  tab = tab + '</ul>';
				tab = tab + '</nav>' ;   	
			 	
			 	
			 	return tab; 			
			}*/
			
			function luminosite(couleur)
			{
	      var maxi, mini, lumi;
   			var r = parseInt(couleur.slice(1, 3), 16);
   			var g = parseInt(couleur.slice(3, 5), 16);
   			var b = parseInt(couleur.slice(5, 7), 16);
   			
   			/*if ((r>=g) && (r>=b)) 
					maxi = r; 	         			
   			if ((g>=r) && (g>=b)) 
					maxi = g; 	         			
   			if ((b>=r) && (b>=g)) 
					maxi = b; 	        
   			if ((r<g) && (r<b)) 
					mini = r; 	         			
   			if ((g<r) && (g<b)) 
					mini = g; 	         			
   			if ((b<r) && (b<g)) 
					mini = b; */	        
				
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
			  	//document.getElementById(place).appendChild(document.createElement("BR"));
				}
				if (total > 0)
				{
				 	//document.getElementById(place).appendChild(document.createElement("BR"));
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
				     				updaterow("", "", "", this.getAttribute("data-table"), row, this.parentElement.parentElement.getAttribute("data-pknom"), this.parentElement.parentElement.getAttribute("data-pkval"), limite, offset, "", 0);
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
	
				 	//document.getElementById(place).appendChild(document.createElement("BR"));
				 	if (pagination == true)
				 	{
				 		var divrpp = document.createElement("DIV");
				 		divrpp.classList.add("divrpp");
					 	lblrpp = document.createElement("LABEL");
					 	lblrpp.for = "rpp" + numtable ;
					 	lblrpp.innerHTML = "Nombre de résultat par page";
					 	divrpp.appendChild(lblrpp);
					 	selres = document.createElement("SELECT");
					 	selres.onchange = function () {
					 		changeFunc(vue, place, tablestr, this.value, selcol, selid);
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
							gettable(vue,place,tablestr, limite,(offset - vallimite),selcol,selid);
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
				    document.getElementById(place).appendChild(nav);
			    }
	 				if (tablestr == "commande")
	 				{
						var j=0;
						var obj3 = { customer: boutic, action:"colorrow", tables:tables, table:"", liens:liens, colonne:"", row:"", idtoup:"", limite:limite, offset:offset, selcol:"", selid:0};
	
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
				else 
				{
				  var nodatap = document.createElement("P");
				  var ita = document.createElement("I");
				  ita.innerText = 'Il n\'y a pas de ' + tables[getnumtable(table)].desc + ' à afficher';
				  nodatap.appendChild(ita);
			  	document.getElementById(place).appendChild(nodatap);				
			  }
			}
			   			
			function editcol(tablestr, limite , offset) 
			{
				$('.modal-title').html('Configuration des colonnes');
				$('.modal-dialog').removeClass("modal-lg");
				document.getElementsByClassName('modal-body')[0].innerHTML = '';
				var tbl = document.createElement("TABLE");
				tbl.id = 'modtbl';
				document.getElementsByClassName('modal-body')[0].appendChild(tbl);
				var trh = document.createElement("TR");
				//tr.id = 'modtr';
				tbl.appendChild(trh);
				var th1 = document.createElement("TH");
				//th.id = 'modtr';
				th1.innerHTML = "Champs";
				trh.appendChild(th1);
				var th2 = document.createElement("TH");
				th2.innerHTML = "Visible";
				trh.appendChild(th2);
				var th3 = document.createElement("TH");
				th3.innerHTML = "Ordre";
				trh.appendChild(th3);
				
				
				for (var i=0;i<tables.length; i++)
				{
					if (tables[i].nom == tablestr)
					{
						for (var j=0;j<tables[i].champs.length; j++)
						{
							if (tables[i].champs[j].typ != "pk")
							{
								var trd = document.createElement("TR");
								var td1 = document.createElement("TD");
								td1.innerHTML = tables[i].champs[j].desc;
								trd.appendChild(td1);
								var td2 = document.createElement("TD");
								td2.classList.add("center")
								var chkb = document.createElement("INPUT");
								chkb.type = "checkbox";
								chkb.setAttribute("data-numtbl", i);
								chkb.setAttribute("data-numfld", j);
								chkb.onchange = function () {
									if (this.checked == true)
									{
										tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].vis ="o";									
									}
									else {
										tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].vis ="n";
									}
								}
								
								if (tables[i].champs[j].vis == "o")
									chkb.checked = true;
								else {
									chkb.checked = false;
								}
								td2.appendChild(chkb);
								trd.appendChild(td2);
								var td3 = document.createElement("TD");
								td3.setAttribute("data-numtbl", i);
								td3.setAttribute("data-numfld", j);
								//td3.setAttribute("data-numordre", 0);
								//td3.setAttribute("data-sens", "");
								td3.classList.add("tdtri");
								td3.onclick = function () {
									//numtri = getmaxvalue();
									var listtdtri = document.getElementsByClassName("tdtri");
									var tempval = 0;
									var maxval = 0;
									for (var k=0; k<listtdtri.length; k++)
									{
										tempval = parseInt(tables[listtdtri[k].getAttribute("data-numtbl")].champs[listtdtri[k].getAttribute("data-numfld")].ordre);
										if (tempval >= maxval)
											maxval = tempval + 1;								
									}
									if (tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].sens =="")
									{
										tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].sens = "A";
										tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].ordre = maxval;
										this.innerHTML = "&#9650;" + maxval;	
									}
									else if (tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].sens =="A")
									{
										tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].sens = "D";
										this.innerHTML = "&#9660;" + tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].ordre;	
									}
									else if (tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].sens =="D")
									{
										tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].sens = "";
										var removedval = parseInt(tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].ordre);
										for (var l=0; l<listtdtri.length; l++)
										{
											tempval = parseInt(tables[listtdtri[l].getAttribute("data-numtbl")].champs[listtdtri[l].getAttribute("data-numfld")].ordre);
											if (tempval > removedval)
											{
												tables[listtdtri[l].getAttribute("data-numtbl")].champs[listtdtri[l].getAttribute("data-numfld")].ordre = tempval - 1;
												if (tables[listtdtri[l].getAttribute("data-numtbl")].champs[listtdtri[l].getAttribute("data-numfld")].sens =="A")
													listtdtri[l].innerHTML = "&#9650;" + tables[listtdtri[l].getAttribute("data-numtbl")].champs[listtdtri[l].getAttribute("data-numfld")].ordre;
												else if (tables[listtdtri[l].getAttribute("data-numtbl")].champs[listtdtri[l].getAttribute("data-numfld")].sens =="D")
													listtdtri[l].innerHTML = "&#9660;" + tables[listtdtri[l].getAttribute("data-numtbl")].champs[listtdtri[l].getAttribute("data-numfld")].ordre;
											}								
										}
										
										tables[this.getAttribute("data-numtbl")].champs[this.getAttribute("data-numfld")].ordre = 0;
										this.innerHTML = "&#9650;/&#9660;";
									}
								}
								if ((tables[i].champs[j].sens == "A") && (parseInt(tables[i].champs[j].ordre) > 0))
									td3.innerHTML = "&#9650;" + tables[i].champs[j].ordre;
								else if ((tables[i].champs[j].sens == "D") && (parseInt(tables[i].champs[j].ordre) > 0))
									td3.innerHTML = "&#9660;" + tables[i].champs[j].ordre;
								else {
									td3.innerHTML = "&#9650;/&#9660;";
								}
								trd.appendChild(td3);
								tbl.appendChild(trd);
							}
						}	
					}
				}							
				
				var okbtn = document.getElementById('okbtn');
				okbtn.onclick = function () 
				{
					gettable( "table9", "commande", limite, offset);
				}

				$('.modal').modal('show');
			}
			
			function editfil(tablestr, limite , offset) 
			{
				$('.modal-title').html('Configuration des filtres');
				$('.modal-dialog').addClass("modal-lg");
				//$('.modal-body').addClass("bodyflex");
				document.getElementsByClassName('modal-body')[0].innerHTML = '';
				
				for (var k=0;k<maxfiltre;k++) 
				{
					var divpan = document.createElement("DIV");
					divpan.id = 'divpan' + k;
					//divpan.classList.add("filpan");
									
					document.getElementsByClassName('modal-body')[0].appendChild(divpan);
					var lblfld = document.createElement("LABEL");
					lblfld.id ='lblfld' + k;
					lblfld.for = 'fld' + k;
					lblfld.innerHTML= 'Champs';
					document.getElementById('divpan' + k).appendChild(lblfld);
					var fld = document.createElement("SELECT");
					fld.id = 'fld' + k;
					document.getElementById('divpan' + k).appendChild(fld);
					
					var opt = document.createElement("OPTION");
					opt.innerHTML = "";
					document.getElementById('fld' + k).appendChild(opt);					
					
					for (var i=0;i<tables.length; i++)
					{
						if (tables[i].nom == tablestr)
						{
							for (var j=0;j<tables[i].champs.length; j++)
							{
								if (tables[i].champs[j].typ != "pk")
								{
									var opt = document.createElement("OPTION");
									opt.innerHTML = tables[i].champs[j].desc;
									document.getElementById('fld' + k).appendChild(opt);
								}
							}
						}
					}
					
					var lblop = document.createElement("LABEL");
					lblop.id ='lblopd' + k;
					lblop.for = 'op' + k;
					lblop.innerHTML= '&nbsp;Operators';
					document.getElementById('divpan' + k).appendChild(lblop);
	
					var ope = document.createElement("SELECT");
					ope.id = 'op' + k;
					document.getElementById('divpan' + k).appendChild(ope);

					for (var i=0;i<op.length; i++)
					{
						var opo = document.createElement("OPTION");
						opo.innerHTML = op[i];
						document.getElementById('op' + k).appendChild(opo);
					}
					
					var lblval = document.createElement("LABEL");
					lblval.id ='lblvald' + k;
					lblval.for = 'val' + k;
					lblval.innerHTML= '&nbsp;Valeur';
					document.getElementById('divpan' + k).appendChild(lblval);
	
					var inpval = document.createElement("INPUT");
					inpval.id ='inpval' + k;
					document.getElementById('divpan' + k).appendChild(inpval);
					
					/*var chpm = document.createElement("DIV");
					chpm.id ='chpm' + k;
					chpm.innerHTML= '+';
					chpm.style.cursor ="default";
					chpm.setAttribute("data-num", k);
					chpm.onclick = function () 
					{
						var preve = document.getElementById('divpan' + this.getAttribute("data-num"));
						if (chpm.innerHTML == '+') 					
						{	
							preve.style.display = "contents";
							chpm.innerHTML= '-';
						}
						else if (chpm.innerHTML == '-')
						{
							preve.style.display = "none";
							chpm.innerHTML= '+';
						}
					}
					document.getElementsByClassName('modal-body')[0].appendChild(chpm);*/
					/*var br1 = document.createElement("BR");
					document.getElementsByClassName('modal-body')[0].appendChild(br1);
					var br2 = document.createElement("BR");
					document.getElementsByClassName('modal-body')[0].appendChild(br2);*/
				}				

								
				var okbtn = document.getElementById('okbtn');
				okbtn.onclick = function () 
				{
					filtres = [];
					for (var l=0;l<maxfiltre;l++) 
					{
						var table = tablestr;
						var champ = document.getElementById('fld' + l).value;
						var operateur = document.getElementById('op' + l).value;
						var valeur = document.getElementById('inpval' + l).value;

						var filtre= {table:table, champ:champ, operateur:operateur, valeur:valeur};

						if (champ!="")
							filtres.push(filtre);

					}					
					
					gettable( "table9", "commande", limite, offset);
					$('.modal-dialog').removeClass("modal-lg");
					$('.modal-body').removeClass("bodyflex");
				}

				$('.modal').modal('show');
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
				
				var obj = { customer: boutic, action:"getvalues", tables:tables, table:tables[numtable].nom, liens:liens, colonne:"", row:"", idtoup:idtoup };

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
													updaterow('table' + numtable, 'table' + numtable, tables[numtable].nom, row, "cmdid", objectid, limite, offset, "", 0);
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
							gettable( "table10", "table10", "lignecmd", deflimite, defoffset, "cmdid", objectid);
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
			
      function gettable(vue, place, table, limite, offset, selcol="", selid=0)      
      {
      	
      	var obj = { customer: boutic, action:"elemtable", tables:tables, table:table, liens:liens, colonne:"", row:"", idtoup:"", limite:"", offset:"", selcol:selcol, selid:selid, filtres:filtres };
  	
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
			    	var total = parseInt(data[0]);
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
			      var obj2 = { customer: boutic, action:"vuetable", tables:tables, table:table, liens:liens, colonne:"", row:"", idtoup:"", limite:limite, offset:offset, selcol:selcol, selid:selid, filtres:filtres };
			  	
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
        
      	var obj = { customer: boutic, action:"rempliroption", tables:tables, table:table, liens:liens, colonne:colonne };
      	
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
        
      	var obj = { customer: boutic, action:"insertrow", tables:tables, table:table, liens:liens, colonne:"", row:row };
     	
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
        
      	var obj = { customer: boutic, action:"updaterow", tables:tables, table:table, liens:liens, colonne:pknom, row:row, idtoup:idtoup };
     	
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
      
      function getarray(myarrray, place, table, limite, offset, selcol="", selid=0)      
      {
      	
				var larray = [];
      	
	    	var total = myarrray.length;

 				document.getElementById(place).innerHTML = vartotable(place, table, myarrray, total, limite, offset, selcol, selid) ;
 				
      } 
      
			function sendSMS( telephone, message)      
      {
        var retour;      
        
      	var obj = { customer: boutic, telephone:telephone, message:message };
     	
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
        
      	var obj = { customer: boutic, action:"getcomdata", tables:tables, table:"commande", liens:liens, cmdid:cmdid };
     	
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
         		sendSMS( data[0], data[1]) 
         	}
      	})
      }      
      
    </script>
  </body>
</html>
