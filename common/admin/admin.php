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
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.06">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body id="backbody">
	  <div class="vertical-nav" id="sidebar">
	  	<ul class="nav flex-column">
			  <span class="navbar-text">
			    Bienvenue <?php echo $_SESSION[$boutic . '_pseudo']; ?> ! 
			  </span>
			  <li class="nav-item">
			    <a class="nav-link active" id="commande-tab" data-toggle="tab" href="#commande" role="tab" aria-controls="commande" aria-selected="false">COMMANDE</a>
			  </li>
			  <li class="nav-item">
			    <a class="nav-link" id="produit-tab" data-toggle="tab" href="#produit" role="tab" aria-controls="produit" aria-selected="false">PRODUIT</a>
			  </li>
 			  <li class="nav-item">
			    <a class="nav-link" id="options-tab" data-toggle="tab" href="#options" role="tab" aria-controls="options" aria-selected="false">OPTIONS</a>
			  </li>
 			  <li class="nav-item">
			    <a class="nav-link" id="livraison-tab" data-toggle="tab" href="#livraison" role="tab" aria-controls="livraison" aria-selected="false">LIVRAISON</a>
			  </li>			  
 			  <li class="nav-item">
			    <a class="nav-link" id="administration-tab" data-toggle="tab" href="#administration" role="tab" aria-controls="administration" aria-selected="false">ADMINISTRATION</a>
			  </li>			  
 			  <li class="nav-item">
			    <a class="nav-link" href="logout.php">Deconnexion</a>
			  </li>
			</ul>
		</div>
		<div class="tab-content page-content" id="myMenuContent">
			<div class="tab-pane active" id="commande" role="tabpanel" aria-labelledby="commande-tab">
				<p class="title">Commande</p>
			  <div class='tbl' id="table9"></div>
			  <div class='tbl form-group' id="det9" hidden></div>
			</div>
			<div class="tab-pane" id="produit" role="tabpanel" aria-labelledby="produit-tab">
				<p class="title">Produit</p>
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="categorie-tab" data-toggle="tab" href="#categorie" role="tab" aria-controls="categorie" aria-selected="true">CATEGORIE</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="article-tab" data-toggle="tab" href="#article" role="tab" aria-controls="article" aria-selected="false">ARTICLE</a>
					</li>
				</ul>
				<div class="tab-content" id="myTabProdContent">
					<div class="tab-pane active" id="categorie" role="tabpanel" aria-labelledby="categorie-tab">
					  <div class='tbl' id="table0"></div>
					  <div class='tbl form-group' id="ins0" hidden></div>
					  <div class='tbl form-group' id="maj0" hidden></div>
					</div>
					<div class="tab-pane" id="article" role="tabpanel" aria-labelledby="article-tab">
				  	<div class='tbl' id="table1"></div>	
			 	  	<div class='tbl form-group' id="ins1" hidden></div>
			 	  	<div class='tbl form-group' id="maj1" hidden></div>	
					</div>
				</div>
			</div>
			<div class="tab-pane" id="options" role="tabpanel" aria-labelledby="options-tab">
				<p class="title">Options</p>
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="option-tab" data-toggle="tab" href="#option" role="tab" aria-controls="option" aria-selected="false">OPTION</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="groupeopt-tab" data-toggle="tab" href="#groupeopt" role="tab" aria-controls="groupeopt" aria-selected="false">GROUPEOPT</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="relgrpoptart-tab" data-toggle="tab" href="#relgrpoptart" role="tab" aria-controls="relgrpoptart" aria-selected="false">RELGRPOPTART</a>
					</li>
				</ul>
				<div class="tab-content" id="myTabOptContent">
					<div class="tab-pane active" id="option" role="tabpanel" aria-labelledby="option-tab">
				  	<div class='tbl' id="table4"></div>	
			 	  	<div class='tbl form-group' id="ins4" hidden></div>
			 	  <div class='tbl form-group' id="maj4" hidden></div>	
				</div>
				<div class="tab-pane" id="groupeopt" role="tabpanel" aria-labelledby="groupeopt-tab">
				  <div class='tbl' id="table3"></div>	
			 	  <div class='tbl form-group' id="ins3" hidden></div>
			 	  <div class='tbl form-group' id="maj3" hidden></div>	
				</div>
				<div class="tab-pane" id="relgrpoptart" role="tabpanel" aria-labelledby="relgrpoptart-tab">
				  <div class='tbl' id="table2"></div>	
			 	  <div class='tbl form-group' id="ins2" hidden></div>
			 	  <div class='tbl form-group' id="maj2" hidden></div>	
				</div>
			</div>
		</div>
		<div class="tab-pane" id="livraison" role="tabpanel" aria-labelledby="livraison-tab">
			<p class="title">Livraison</p>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="cpzone-tab" data-toggle="tab" href="#cpzone" role="tab" aria-controls="cpzone" aria-selected="false">CPZONE</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="barlivr-tab" data-toggle="tab" href="#barlivr" role="tab" aria-controls="barlivr" aria-selected="false">BARLIVR</a>
				</li>
			</ul>
			<div class="tab-content" id="myTabLivrContent">
				<div class="tab-pane active" id="cpzone" role="tabpanel" aria-labelledby="cpzone-tab">
				  <div class='tbl' id="table7"></div>	
			 	  <div class='tbl form-group' id="ins7" hidden></div>
			 	  <div class='tbl form-group' id="maj7" hidden></div>	
				</div>
				<div class="tab-pane" id="barlivr" role="tabpanel" aria-labelledby="barlivr-tab">
				  <div class='tbl' id="table8"></div>	
			 	  <div class='tbl form-group' id="ins8" hidden></div>
			 	  <div class='tbl form-group' id="maj8" hidden></div>	
				</div>
			</div>
		</div>
		<div class="tab-pane" id="administration" role="tabpanel" aria-labelledby="administration-tab">
			<p class="title">Administration</p>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="administrateur-tab" data-toggle="tab" href="#administrateur" role="tab" aria-controls="administrateur" aria-selected="false">ADMINISTRATEUR</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="parametre-tab" data-toggle="tab" href="#parametre" role="tab" aria-controls="parametre" aria-selected="false">PARAMETRE</a>
				</li>
			</ul>
			<div class="tab-content" id="myTabAdminContent">
				<div class="tab-pane active" id="administrateur" role="tabpanel" aria-labelledby="administrateur-tab">
				  <div class='tbl' id="table5"></div>	
			 	  <div class='tbl form-group' id="ins5" hidden></div>
			 	  <div class='tbl form-group' id="maj5" hidden></div>	
				</div>
					<div class="tab-pane" id="parametre" role="tabpanel" aria-labelledby="parametre-tab">
				  <div class='tbl' id="table6"></div>	
			 	  <div class='tbl' id="ins6" hidden></div>
		 	  	<div class='tbl' id="maj6" hidden></div>	
				</div>
			</div>
		</div>			
	</div>	
		
	<div class="modal" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
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
		
  <script>
	var boutic = "<?php echo $boutic;?>" ;
	
	var deflimite = 5;
	var defoffset = 0;
	var offset = 0;  
  
	var tables = [
								{nom:"categorie", cs:"nom", champs:[{nom:"catid", typ:"pk", vis:"n", ordre:"0", sens:""},{nom:"nom", typ:"ref", vis:"o", ordre:"0", sens:""}, {nom:"visible", typ:"bool", vis:"o", ordre:"0", sens:""}]},
	              {nom:"article", cs:"nom", champs:[{nom:"artid", typ:"pk", vis:"n", ordre:"0", sens:""},{nom:"nom", typ:"ref", vis:"o", ordre:"0", sens:""}, {nom:"prix", typ:"prix", vis:"o", ordre:"0", sens:""}, {nom:"description", typ:"text", vis:"o", ordre:"0", sens:""}, 
	              {nom:"visible", typ:"bool", vis:"o", ordre:"0", sens:""}, {nom:"catid", typ:"fk", vis:"o", ordre:"0", sens:""},
	                {nom:"unite", typ:"text", vis:"o", ordre:"0", sens:""}, {nom:"image", typ:"image", vis:"o", ordre:"0", sens:""}, {nom:"imgvisible", typ:"bool", vis:"o", ordre:"0", sens:""}, {nom:"obligatoire", typ:"bool", vis:"o", ordre:"0", sens:""}]},
	              {nom:"relgrpoptart", cs:"", champs:[{nom:"relgrpoartid", typ:"pk", vis:"n", ordre:"0", sens:""}, {nom:"grpoptid", typ:"fk", vis:"o", ordre:"0", sens:""}, {nom:"artid", typ:"fk", vis:"o", ordre:"0", sens:""}, {nom:"visible", typ:"bool", vis:"o", ordre:"0", sens:""}]},
	              {nom:"groupeopt", cs:"nom", champs:[{nom:"grpoptid", typ:"pk", vis:"n", ordre:"0", sens:""}, {nom:"nom", typ:"ref", vis:"o", ordre:"0", sens:""}, {nom:"visible", typ:"bool", vis:"o", ordre:"0", sens:""}, {nom:"multiple", typ:"bool", vis:"o", ordre:"0", sens:""}]},
	              {nom:"option", cs:"nom", champs:[{nom:"optid", typ:"pk", vis:"n", ordre:"0", sens:""}, {nom:"nom", typ:"ref", vis:"o", ordre:"0", sens:""}, {nom:"surcout", typ:"prix", vis:"o", ordre:"0", sens:""}, {nom:"grpoptid", typ:"fk", vis:"o", ordre:"0", sens:""}, {nom:"visible", typ:"bool", vis:"o", ordre:"0", sens:""}]},
	              {nom:"administrateur", cs:"pseudo", champs:[{nom:"adminid", typ:"pk", vis:"n", ordre:"0", sens:""},{nom:"pseudo", typ:"text", vis:"o", ordre:"0", sens:""},{nom:"pass", typ:"pass", vis:"o", ordre:"0", sens:""},{nom:"email", typ:"email", vis:"o", ordre:"0", sens:""},{nom:"actif", typ:"bool", vis:"o", ordre:"0", sens:""}]},
	              {nom:"parametre", cs:"nom", champs:[{nom:"paramid", typ:"pk", vis:"n", ordre:"0", sens:""},{nom:"nom", typ:"ref", vis:"o", ordre:"0", sens:""},{nom:"valeur", typ:"text", vis:"o", ordre:"0", sens:""},{nom:"commentaire", typ:"text", vis:"o", ordre:"0", sens:""}]},
	              {nom:"cpzone", cs:"codepostal", champs:[{nom:"cpzoneid", typ:"pk", vis:"n", ordre:"0", sens:""},{nom:"codepostal", typ:"codepostal", vis:"o", ordre:"0", sens:""},{nom:"ville", typ:"text", vis:"o", ordre:"0", sens:""},{nom:"actif", typ:"bool", vis:"o", ordre:"0", sens:""}]},
	              {nom:"barlivr", cs:"", champs:[{nom:"barlivrid", typ:"pk", vis:"n", ordre:"0", sens:""},{nom:"valminin", typ:"prix", vis:"o", ordre:"0", sens:""},{nom:"valmaxex", typ:"prix", vis:"o", ordre:"0", sens:""},{nom:"surcout", typ:"prix", vis:"o", ordre:"0", sens:""},
	              	{nom:"limitebasse", typ:"bool", vis:"o", ordre:"0", sens:""},{nom:"limitehaute", typ:"bool", vis:"o", ordre:"0", sens:""}]},
	              {nom:"commande", cs:"numref", champs:[{nom:"cmdid", typ:"pk", vis:"n", ordre:"0", sens:""}, {nom:"numref", typ:"ref", vis:"o", ordre:"0", sens:""}, {nom:"nom", typ:"text", vis:"n", ordre:"0", sens:""}, {nom:"prenom", typ:"text", vis:"n", ordre:"0", sens:""}, 
	                {nom:"telephone", typ:"text", vis:"n", ordre:"0", sens:""}, {nom:"adresse1", typ:"text", vis:"n", ordre:"0", sens:""}, {nom:"adresse2", typ:"text", vis:"n", ordre:"0", sens:""}, {nom:"codepostal", typ:"text", vis:"n", ordre:"0", sens:""}, 
	                {nom:"ville", typ:"text", vis:"n", ordre:"0", sens:""}, {nom:"vente", typ:"text", vis:"n", ordre:"0", sens:""}, {nom:"paiement", typ:"text", vis:"n", ordre:"0", sens:""},
								  {nom:"sstotal", typ:"prix", vis:"n", ordre:"0", sens:""}, {nom:"fraislivraison", typ:"prix", vis:"n", ordre:"0", sens:""}, {nom:"total", typ:"prix", vis:"o", ordre:"0", sens:""}, {nom:"commentaire", typ:"text", vis:"n", ordre:"0", sens:""}, 
								  {nom:"method", typ:"text", vis:"n", ordre:"0", sens:""}, {nom:"table", typ:"text", vis:"n", ordre:"0", sens:""}, {nom:"datecreation", typ:"date", vis:"o", ordre:"0", sens:""}]},
	              {nom:"lignecmd", cs:"", champs:[{nom:"lignecmdid", typ:"pk", vis:"n", ordre:"0", sens:""}, {nom:"cmdid", typ:"fk", vis:"o", ordre:"0", sens:""}, {nom:"ordre", typ:"text", vis:"o", ordre:"0", sens:""}, {nom:"type", typ:"text", vis:"o", ordre:"0", sens:""}, 
	                {nom:"nom", typ:"text", vis:"o", ordre:"0", sens:""}, {nom:"prix", typ:"prix", vis:"o", ordre:"0", sens:""}, {nom:"quantite", typ:"text", vis:"o", ordre:"0", sens:""}, {nom:"commentaire", typ:"text", vis:"o", ordre:"0", sens:""}]}
	              ];  

  var liens = [{nom:"categorie", srctbl:"article", srcfld:"catid", dsttbl:"categorie", dstfld:"catid"},
  						 {nom:"groupeopt", srctbl:"relgrpoptart", srcfld:"grpoptid", dsttbl:"groupeopt", dstfld:"grpoptid"},
  						 {nom:"article", srctbl:"relgrpoptart", srcfld:"artid", dsttbl:"article", dstfld:"artid"},
  						 {nom:"groupeopt", srctbl:"option", srcfld:"grpoptid", dsttbl:"groupeopt", dstfld:"grpoptid"},
  						 {nom:"commande", srctbl:"lignecmd", srcfld:"cmdid", dsttbl:"commande", dstfld:"cmdid"}
  						 ];
  						 
	var rpp = [5,10,15,20,50,100];  

	var op = ["=",">","<",">=","<=","<>","LIKE"];	
	
	var filtres = [];
	
	var maxfiltre = 10;

  $(function() {
    gettable( "table0", "categorie", deflimite, defoffset);
    gettable( "table1", "article", deflimite, defoffset);
    gettable( "table2", "relgrpoptart", deflimite, defoffset);
    gettable( "table3", "groupeopt", deflimite, defoffset);
    gettable( "table4", "option", deflimite, defoffset);
    gettable( "table5", "administrateur", deflimite, defoffset);
    gettable( "table6", "parametre", deflimite, defoffset);
    gettable( "table7", "cpzone", deflimite, defoffset);
    gettable( "table8", "barlivr", deflimite, defoffset);
    gettable( "table9", "commande", deflimite, defoffset);
  });
 
  </script>

	<script type="text/javascript" >
			function getnumtable(nom)
			{
				for (var i=0; i<tables.length; i++)
       		if (nom == tables[i].nom)
       			numtable = i;

				return numtable; 			
			}	
	
	
			function insert(numtable, limite, offset) {
				var champs = tables[numtable].champs;
				document.getElementById('table' + numtable).hidden = true;
				document.getElementById('ins' + numtable).hidden = false;
				document.getElementById('maj' + numtable).hidden = true;
				var titre = document.createElement('H5');
				titre.id = 'itable'+ numtable +'titre';
				titre.innerHTML = 'Insertion dans table ' + tables[numtable].nom;
				document.getElementById('ins' + numtable).appendChild(titre);
				var br = document.createElement('br');
				document.getElementById('ins' + numtable).appendChild(br);
				var labels = [];
				var input = [];
				for(i=0; i<champs.length; i++)				
				{
					if (champs[i].typ != "pk")
					{
						var lbl = document.createElement('label');
						lbl.id = 'itable'+ numtable +'lbl' + i;
						lbl.htmlFor = 'itable'+ numtable + 'inp' + i;
						if (champs[i].typ != "fk")
						{
							lbl.innerHTML = champs[i].nom + '&nbsp;:&nbsp;';
							
							var inp = document.createElement('input');
							if (champs[i].typ == "text")
							{
								inp.classList.add('form-control');								
								inp.type = 'text';
							}
							else if (champs[i].typ == "ref")
							{
								inp.classList.add('form-control');								
								inp.type = 'text';
								inp.required = true;
							}
							else if (champs[i].typ == "bool")
							{
								inp.type = 'checkbox';
							}
							else if (champs[i].typ == "prix")
							{
								inp.classList.add('form-control');
								inp.type = 'number';
								inp.step = '0.01';
								inp.value = '0';
								inp.min = '0';
							}
							else if (champs[i].typ == "image")
							{
								inp.classList.add('form-control-file');
								inp.type = 'file';
								inp.accept="image/png, image/jpeg";
							}
							else if (champs[i].typ == "pass")
							{
								inp.classList.add('form-control');
								inp.type = 'password';
								inp.pattern = "(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}";
								inp.title = "Le mot de passe doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères";
								inp.required = true;
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
								inp.title = "Le code postal doit être valoide";
								inp.required = true;
							}
							
							document.getElementById('ins' + numtable).appendChild(lbl);
							inp.name = 'itable' + numtable + '_' + champs[i].nom;
							inp.id = 'itable' + numtable + '_' + 'inp' + i;
							inp.setAttribute("data-table",tables[numtable].nom);
							inp.setAttribute("data-champ",champs[i].nom);
							document.getElementById('ins' + numtable).appendChild(inp);
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
									lbl.innerHTML = liens[j].nom + '&nbsp;:&nbsp;';
									document.getElementById('ins' + numtable).appendChild(lbl);
									document.getElementById('ins' + numtable).appendChild(lien);
									for (var k=0; k<tables.length; k++)
									{
										if (tables[k].nom == liens[j].dsttbl)
											getoptions(	'itable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs );      
									}
								}
							}
						}
						var br = document.createElement('br');
						document.getElementById('ins' + numtable).appendChild(br);
					}
				}
				var okbtn = document.createElement('button');
				okbtn.id = "okbtn" + numtable;
				okbtn.type = "button";
				okbtn.innerHTML = "Ok";
				okbtn.classList.add("btn");
				okbtn.classList.add("btn-primary");
				okbtn.classList.add("btn-block");
				okbtn.onclick = function(){
					var row = [];
					var error = false;
					for (var i=0; i<champs.length; i++)
					{
						var val;
						
						if (champs[i].typ == 'image')
						{
							const fileInput = document.querySelector('#' + 'itable' + numtable + '_' + 'inp' + i) ;
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
			        })
			        if (typeof(fileInput.files[0]) != "undefined")
			        	val = fileInput.files[0].name;
			        else {
			        	val ="";
			        }
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
							val = document.getElementById('itable' + numtable + '_' + 'lien' + i).value;
						} 
						else if (champs[i].typ !='pk'){
							fld = document.getElementById('itable' + numtable + '_' + 'inp' + i);
						  val = fld.value;
						  if ( fld.required == true)
						  {
						  	if (val == "")
						  	{
									alert("Le champ " + champs[i].nom + " ne peut pas être vide");	
									error = true;					  	
						  	}
						  }
						  if (!fld.checkValidity())
						  {
								alert(fld.title);
								error = true;					  	
						  }						  
						}
						if (champs[i].typ !='pk')
						{
							var col = {nom:champs[i].nom, valeur:val, type:champs[i].typ};
							row.push(col);
						}					
					}
					if (error == false)
						insertrow(tables[numtable].nom, row, limite, offset);
				}; 

				document.getElementById('ins' + numtable).appendChild(okbtn);
				
				var clbtn = document.createElement('button');
				clbtn.id = "clbtn" + numtable;
				clbtn.type = "button";
				clbtn.innerHTML = "Cancel";
				clbtn.classList.add("btn");
				clbtn.classList.add("btn-secondary");
				clbtn.classList.add("btn-block");
				clbtn.onclick = function(){
					document.getElementById('table' + numtable).hidden = false;
					document.getElementById('ins' + numtable).hidden = true;
					document.getElementById('maj' + numtable).hidden = true;
					document.getElementById('ins' + numtable).innerHTML = "";
				}; 
				document.getElementById('ins' + numtable).appendChild(clbtn);

			}
	
			function update(numtable, idtoup, limite, offset ) 
			{
				var champs = tables[numtable].champs;
				document.getElementById('table' + numtable).hidden = true;
				document.getElementById('ins' + numtable).hidden = true;
				document.getElementById('maj' + numtable).hidden = false;
				
				var titre = document.createElement('H5');
				titre.id = 'itable'+ numtable +'titre';
				titre.innerHTML = 'Mise à jour dans table ' + tables[numtable].nom;
				document.getElementById('maj' + numtable).appendChild(titre);
				var br = document.createElement('br');
				document.getElementById('maj' + numtable).appendChild(br);				
				
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
            //document.getElementById(place).innerHTML = vartotable(tables[numtable].champs, data, numtable);
						var labels = [];
						var input = [];
						for(i=0; i<champs.length; i++)				
						{
							if (champs[i].typ != "pk")
							{
								var lbl = document.createElement('label');
								lbl.id = 'utable'+ numtable +'lbl' + i;
								lbl.htmlFor = 'utable'+ numtable + 'inp' + i;
								if (champs[i].typ != "fk")
								{
									lbl.innerHTML = champs[i].nom + '&nbsp;:&nbsp;';
									document.getElementById('maj' + numtable).appendChild(lbl);
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
										inp.value = data[i];
										inp.min = '0';
									}
									else if (champs[i].typ == "image")
									{
										inp.classList.add('form-control-file');
										inp.type = 'file';
										inp.accept="image/png, image/jpeg";
										inp.filename = data[i];
									}
									else if (champs[i].typ == "pass")
									{
										inp.classList.add('form-control');
										inp.type = 'password';
										inp.pattern = "(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%&*?]).{8,}";
										inp.title = "Doit contenir au moins un chiffre, une majuscule, une minuscule, un signe parmi !@#$%&*? et être de au moins 8 caractères";
										inp.required = false;
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
									document.getElementById('maj' + numtable).appendChild(inp);
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
											lbl.innerHTML = liens[j].nom + '&nbsp;:&nbsp;';
											document.getElementById('maj' + numtable).appendChild(lbl);
											
											for (k=0; k<tables.length; k++)
												if (tables[k].nom == liens[j].dsttbl)
													getoptions('utable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs, data[i]) ;

											
											document.getElementById('maj' + numtable).appendChild(lien);
											//document.getElementById('utable' + numtable + '_' + 'lien' + i).selectedIndex = parseInt(data[i]);
										}
									}
								}
								var br = document.createElement('br');
								document.getElementById('maj' + numtable).appendChild(br);
							}
						}
						var okbtn = document.createElement('button');
						okbtn.id = "okbtn" + numtable;
						okbtn.type = "button";
						okbtn.innerHTML = "Ok";
						okbtn.classList.add("btn");
						okbtn.classList.add("btn-primary");
						okbtn.classList.add("btn-block");
						okbtn.onclick = function()
						{
							var row = [];
							var pknom;
							var error = false;
							for (var i=0; i<champs.length; i++)
							{
								var val;
								if (champs[i].typ != 'pk')
								{
									if (champs[i].typ == 'image')
									{
										const fileInput = document.querySelector('#' + 'utable' + numtable + '_' + 'inp' + i) ;
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
						        })
						        if (typeof(fileInput.files[0]) != "undefined")
						        	val = fileInput.files[0].name;
						        else {
						        	val ="";
						        }
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
										val = document.getElementById('utable' + numtable + '_' + 'lien' + i).value;
									} 
									else 
									{
										fld = document.getElementById('utable' + numtable + '_' + 'inp' + i);
									  val = fld.value;
 									  if (!fld.checkValidity())
									  {
											//alert(fld.validationMessage);	
											alert(fld.getAttribute("data-champ")  + " : " + fld.validationMessage);
											error = true;					  	
						  			}						  
									}
									var col = {nom:champs[i].nom, valeur:val, type:champs[i].typ};
									row.push(col);					
								}
								else {
									pknom = champs[i].nom;
								}
							}
							if (error == false)
								updaterow(tables[numtable].nom, row, pknom, idtoup, limite, offset);
						};
						document.getElementById('maj' + numtable).appendChild(okbtn);

						var clbtn = document.createElement('button');
						clbtn.id = "clbtn" + numtable;
						clbtn.type = "button";
						clbtn.innerHTML = "Cancel";
						clbtn.classList.add("btn");
						clbtn.classList.add("btn-secondary");
						clbtn.classList.add("btn-block");
						clbtn.onclick = function(){
							document.getElementById('table' + numtable).hidden = false;
							document.getElementById('ins' + numtable).hidden = true;
							document.getElementById('maj' + numtable).hidden = true;
							document.getElementById('maj' + numtable).innerHTML = "";
						}; 
						document.getElementById('maj' + numtable).appendChild(clbtn);
					}
      	})
			}
			
			function changeFunc(place, tablestr, $i, selcol="", selid=0) 
			{
				limite = $i;				
				gettable( place, tablestr, limite, 0, selcol, selid);
   		}
   		
 			function vartotable(place, tablestr, donnees, total, limite, offset, selcol="", selid=0)
 			{
			 	var tab = '';
			 	var pkval;
			 	nummtable = getnumtable(tablestr);
			 	table = tables[numtable];
				/*if (tablestr == "commande")
				{
					tab = tab + '<button onclick="editcol(\'commande\',' + limite + ',' + offset + ')">Colonnes</button>&nbsp;';
					tab = tab + '<button onclick="editfil(\'commande\',' + limite + ',' + offset + ')">Filtres</button><br><br>';
				}*/
			 	tab = tab + '<div class=""><table class="table table-bordered table-striped"><thead><tr class="">';
				for (var i=0; i<table.champs.length; i++)          	
			 	{
			 		if ((table.champs[i].typ != "pk") && (table.champs[i].vis != "n") && (table.champs[i].nom != selcol))
			 		{
			   		tab = tab + '<th class="">';
			   		if (table.champs[i].typ != "fk")
			   			tab = tab + table.champs[i].nom;
			   		else
			   		{
							for (var j=0; j<liens.length; j++)          	
			 				{
			 					if ((liens[j].srctbl == table.nom) && (liens[j].srcfld == table.champs[i].nom))
			 						tab = tab + liens[j].nom; 
							}
			   		}	
			   		tab = tab + '</th>';
			 		}
			 	}
			 	if ((tablestr !== "commande") && (tablestr !== "lignecmd"))
			 		tab = tab + '<th class=""></th>';
			 	tab = tab + '</tr></thead><tbody>';
			 	for (var j=0; j<donnees.length; j++)
			 	{
			 		tab = tab + '<tr class="">';
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
			   			pkval = donnees[j][i];
			   	}
					if (tablestr == "commande")
						tab = tab + '<td width="1%"><button class="btn btn-primary" onclick="detail(' + numtable + ',' + pkval + ',' + limite + ',' + offset + ')">Détails</button></td></tr>';
					else if (tablestr == "lignecmd")
						tab = tab + '</tr>';
					else
				    tab = tab + '<td width="1%"><button class="btn btn-primary" onclick="update(' + numtable + ',' + pkval + ',' + limite + ',' + offset + ')">Modifier</button></td></tr>';
							       	
				}
			 	
			 	tab = tab + '</tbody></table></div>';
				if ((tablestr !== "commande") &&  (tablestr !== "lignecmd"))
			   	tab = tab + '<button class="btn btn-primary" onclick="insert(' + numtable + ',' + limite + ',' + offset + ')">Insérer</button>';
			 	tab = tab + '<br>';
			 	tab = tab + '<label for="rpp '+ numtable + '">Nombre de résultat par page</label>';
			 	tab = tab + '<select onchange="changeFunc(\'' + place +'\',\'' + tablestr + '\',value,\'' + selcol  + '\',' + selid + ');" name="rpp' + numtable + '" id="rppid' + numtable + '">';
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
				      tab = tab + '<a class="page-link" href="javascript:gettable(\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (offset - vallimite) + ',\'' + selcol  + '\',' + selid + ')" aria-label="Previous">';
				        tab = tab + '<span aria-hidden="true">&laquo;</span>';
				        tab = tab + '<span class="sr-only">Previous</span>';
				      tab = tab + '</a>';
				    tab = tab + '</li>';
				    var totalpage = Math.ceil(total / vallimite);
				    for (var k=0; k<totalpage;k++)
				    {
				    	if ((offset/ vallimite) == k)
				    		tab = tab + '<li class="page-item active"><a class="page-link" href="javascript:gettable(\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (k*limite) + ',\'' + selcol  + '\',' + selid + ')">' + k + '</a></li>';
				    	else
				    		tab = tab + '<li class="page-item"><a class="page-link" href="javascript:gettable(\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (k*limite) + ',\'' + selcol  + '\',' + selid + ')">' + k + '</a></li>';
				    }
				    if ((offset + vallimite) >= total)
				    	tab = tab + '<li class="page-item disabled">';
				    else
				    	tab = tab + '<li class="page-item">';
				      tab = tab + '<a class="page-link" href="javascript:gettable(\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (offset + vallimite) + ',\'' + selcol  + '\',' + selid + ')" aria-label="Next">';
				        tab = tab + '<span aria-hidden="true">&raquo;</span>';
				        tab = tab + '<span class="sr-only">Next</span>';
				      tab = tab + '</a>';
				    tab = tab + '</li>';
				  tab = tab + '</ul>';
				tab = tab + '</nav>' ;   	
			 	
			 	
			 	return tab; 			
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
								td1.innerHTML = tables[i].champs[j].nom;
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
									opt.innerHTML = tables[i].champs[j].nom;
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
			
			function detail(numtable, idtoup, limite, offset ) 
			{
				var champs = tables[numtable].champs;
				var cmdid = 0;
				document.getElementById('table' + numtable).hidden = true;
				document.getElementById('det' + numtable).hidden = false;
				
				var titre = document.createElement('H5');
				titre.id = 'itable'+ numtable +'titre';
				titre.innerHTML = 'Détails ' + tables[numtable].nom;
				document.getElementById('det' + numtable).appendChild(titre);
				var br = document.createElement('br');
				document.getElementById('det' + numtable).appendChild(br);				
				
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
            //document.getElementById(place).innerHTML = vartotable(tables[numtable].champs, data, numtable);
						var labels = [];
						var input = [];
						var cmdhead = document.createElement("DIV");
						cmdhead.classList.add('twocol');
						document.getElementById('det' + numtable).appendChild(cmdhead);
						for(i=0; i<champs.length; i++)				
						{
							if (champs[i].typ != "pk")
							{
								var dat = document.createElement('p');
								dat.id = 'utable'+ numtable +'dat' + i;
								//dat.htmlFor = 'utable'+ numtable + 'inp' + i;
								if (champs[i].typ != "fk")
								{
									dat.innerHTML = champs[i].nom + '&nbsp;:&nbsp;';
									//document.getElementById('det' + numtable).appendChild(lbl);
									//var inp = document.createElement('p');
									if (champs[i].typ == "text")
									{
										dat.innerHTML = dat.innerHTML + data[i];
									}
									if (champs[i].typ == "date")
									{
										const event = new Date(Date.parse(data[i]));
										dat.innerHTML = dat.innerHTML + event.toLocaleString('fr-FR');
									}
									if (champs[i].typ == "ref")
									{
										dat.innerHTML = dat.innerHTML + data[i];
									}
									else if (champs[i].typ == "bool")
									{
										if (data[i] == "1")
											dat.innerHTML = dat.innerHTML + 'oui';
										else {
											dat.innerHTML = dat.innerHTML + 'non';
										}
									}
									else if (champs[i].typ == "prix")
									{
										dat.innerHTML = dat.innerHTML + parseFloat(data[i]).toFixed(2);
									}
									else if (champs[i].typ == "image")
									{
										dat.innerHTML = dat.innerHTML + data[i];
									}
									else if (champs[i].typ == "pass")
									{
										dat.innerHTML = dat.innerHTML + data[i];
									}
									else if (champs[i].typ == "email")
									{
										dat.innerHTML = dat.innerHTML + data[i];
									}
									else if (champs[i].typ == "codepostal")
									{
										dat.innerHTML = dat.innerHTML + data[i];
									}
									
									//inp.name = 'utable' + numtable + '_' + champs[i].nom;
									//inp.id = 'utable' + numtable + '_' + 'inp' + i;
									
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
											lbl.innerHTML = liens[j].nom + '&nbsp;:&nbsp;' + data[i];
											
											
											/*for (k=0; k<tables.length; k++)
												if (tables[k].nom == liens[j].dsttbl)
													getoptions('utable' + numtable + '_' + 'lien' + i, tables[k].nom, tables[k].cs, data[i]) ;*/
											
											
											lbl.classList.add('form-control');
											lbl.name = 'utable' + numtable + '_' + champs[numtable].nom;
											lbl.id = 'utable' + numtable + '_' + 'lien' + i;
											lbl.setAttribute("data-table", tables[numtable].nom);
											lbl.setAttribute("data-champ", champs[i].nom);
											cmdhead.appendChild(lbl);
											
											//document.getElementById('utable' + numtable + '_' + 'lien' + i).selectedIndex = parseInt(data[i]);
										}
									}
								}
							}
							else {
								cmdid = parseInt(data[i]);
							}
						}
						var lignecmd = document.createElement('DIV');
						lignecmd.classList.add("tbl");
						lignecmd.classList.add("form-group");
						lignecmd.id = "table10";
						lignecmd.hidden = false;
						document.getElementById('det' + numtable).appendChild(lignecmd);
						gettable( "table10", "lignecmd", deflimite, defoffset, "cmdid", cmdid);
						
						var clbtn = document.createElement('button');
						clbtn.id = "clbtn" + numtable;
						clbtn.type = "button";
						clbtn.innerHTML = "Close";
						clbtn.classList.add("btn");
						clbtn.classList.add("btn-primary");
						clbtn.classList.add("btn-block");
						clbtn.onclick = function(){
							document.getElementById('table' + numtable).hidden = false;
							document.getElementById('det' + numtable).hidden = true;
							document.getElementById('det' + numtable).innerHTML = "";
						}; 
						document.getElementById('det' + numtable).appendChild(clbtn);
					}
      	})
			}
			
      function gettable(place, table, limite, offset, selcol="", selid=0)      
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
			    	var total = data[0];	
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
	         				document.getElementById(place).innerHTML = vartotable(place, table, data, total, limite, offset, selcol, selid) ;
		        })
        	}
        })
      } 
      
			function datatooption(donnees, selidx)
			{
				var options="";
				for (i=0; i<donnees.length; i++)
				{
					if (donnees[i][0] == selidx)
						options = options + '<option value=' + donnees[i][0] + ' selected>';
					else {
						options = options + '<option value=' + donnees[i][0] + '>';
					}
					options = options + donnees[i][1];
			    options = options + '</option>';
				}
				return options;			
			}      
      
			function getoptions( place, table, colonne, selidx)      
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
            	document.getElementById(place).innerHTML = datatooption(data, selidx);
        })
          
      } 
      
			function insertrow( table, row, limite, offset)      
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
         		numtable = getnumtable(table);
	          gettable('table' + numtable, table, limite, offset);
						document.getElementById('table' + numtable).hidden = false;
						document.getElementById('ins' + numtable).hidden = true;
						document.getElementById('maj' + numtable).hidden = true;
						document.getElementById('ins' + numtable).innerHTML = "";
         	}
      	})
      } 

			function updaterow( table, row, pknom, idtoup, limite, offset)      
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
         		numtable = getnumtable(table);
	          gettable('table' + numtable, table, limite, offset);
						document.getElementById('table' + numtable).hidden = false;
						document.getElementById('ins' + numtable).hidden = true;
						document.getElementById('maj' + numtable).hidden = true;
						document.getElementById('maj' + numtable).innerHTML = "";
         	}
      	})
      } 
    </script>
  </body>
</html>
