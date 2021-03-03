<?php
	session_start();

  if (empty($_SESSION['boutic']) == TRUE)
 	  exit();
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
    <link rel="stylesheet" href="css/back.css?v=1.04">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body id="backbody">
	  <p>Bienvenue <?php echo $_SESSION[$boutic . '_pseudo']; ?> ! <a href="logout.php"><button type="button" class="btn btn-primary">Deconnexion</button></a></p>
  
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="categorie-tab" data-toggle="tab" href="#categorie" role="tab" aria-controls="categorie" aria-selected="true">CATEGORIE</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="article-tab" data-toggle="tab" href="#article" role="tab" aria-controls="article" aria-selected="false">ARTICLE</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="relgrpoptart-tab" data-toggle="tab" href="#relgrpoptart" role="tab" aria-controls="relgrpoptart" aria-selected="false">RELGRPOPTART</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="groupeopt-tab" data-toggle="tab" href="#groupeopt" role="tab" aria-controls="groupeopt" aria-selected="false">GROUPEOPT</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="option-tab" data-toggle="tab" href="#option" role="tab" aria-controls="option" aria-selected="false">OPTION</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="administrateur-tab" data-toggle="tab" href="#administrateur" role="tab" aria-controls="administrateur" aria-selected="false">ADMINISTRATEUR</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="parametre-tab" data-toggle="tab" href="#parametre" role="tab" aria-controls="parametre" aria-selected="false">PARAMETRE</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="cpzone-tab" data-toggle="tab" href="#cpzone" role="tab" aria-controls="cpzone" aria-selected="false">CPZONE</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="barlivr-tab" data-toggle="tab" href="#barlivr" role="tab" aria-controls="barlivr" aria-selected="false">BARLIVR</a>
			</li>
		</ul>
		<div class="tab-content" id="myTabContent">
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
		<div class="tab-pane" id="relgrpoptart" role="tabpanel" aria-labelledby="relgrpoptart-tab">
	  <div class='tbl' id="table2"></div>	
 	  <div class='tbl form-group' id="ins2" hidden></div>
 	  <div class='tbl form-group' id="maj2" hidden></div>	
		</div>
		<div class="tab-pane" id="groupeopt" role="tabpanel" aria-labelledby="groupeopt-tab">
	  <div class='tbl' id="table3"></div>	
 	  <div class='tbl form-group' id="ins3" hidden></div>
 	  <div class='tbl form-group' id="maj3" hidden></div>	
		</div>
		<div class="tab-pane" id="option" role="tabpanel" aria-labelledby="option-tab">
	  <div class='tbl' id="table4"></div>	
 	  <div class='tbl form-group' id="ins4" hidden></div>
 	  <div class='tbl form-group' id="maj4" hidden></div>	
		</div>
		<div class="tab-pane" id="administrateur" role="tabpanel" aria-labelledby="administrateur-tab">
	  <div class='tbl' id="table5"></div>	
 	  <div class='tbl form-group' id="ins5" hidden></div>
 	  <div class='tbl form-group' id="maj5" hidden></div>	
		</div>
		<div class="tab-pane" id="parametre" role="tabpanel" aria-labelledby="parametre-tab">
	  <div class='tbl' id="table6"></div>	
 	  <div class='tbl' id="ins6" hidden></div>
 	  <div class='tbl' id="maj6" hidden></div>	
		</div>
		<div class="tab-pane" id="cpzone" role="tabpanel" aria-labelledby="cpzone-tab">
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
		
		<div class="modal" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Erreur</h5>
		      </div>
		      <div class="modal-body">
		        <p>Modal body text goes here.</p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">OK</button>
		      </div>
		    </div>
		  </div>
		</div>
		
  <script>
	var boutic = "<?php echo $boutic;?>" ;
	
	var deflimite = 5;
	var offset = 0;  
  
	var tables = [
								{nom:"categorie", cs:"nom", champs:[{nom:"catid", typ:"pk"},{nom:"nom", typ:"ref"}, {nom:"visible", typ:"bool"}]},
	              {nom:"article", cs:"nom", champs:[{nom:"artid", typ:"pk"},{nom:"nom", typ:"ref"}, {nom:"prix", typ:"prix"}, {nom:"description", typ:"text"}, {nom:"visible", typ:"bool"}, {nom:"catid", typ:"fk"},
	                {nom:"unite", typ:"text"}, {nom:"image", typ:"image"}, {nom:"imgvisible", typ:"bool"}, {nom:"obligatoire", typ:"bool"}]},
	              {nom:"relgrpoptart", cs:"", champs:[{nom:"relgrpoartid", typ:"pk"}, {nom:"grpoptid", typ:"fk"}, {nom:"artid", typ:"fk"}, {nom:"visible", typ:"bool"}]},
	              {nom:"groupeopt", cs:"nom", champs:[{nom:"grpoptid", typ:"pk"}, {nom:"nom", typ:"ref"}, {nom:"visible", typ:"bool"}, {nom:"multiple", typ:"bool"}]},
	              {nom:"option", cs:"nom", champs:[{nom:"optid", typ:"pk"}, {nom:"nom", typ:"ref"}, {nom:"surcout", typ:"prix"}, {nom:"grpoptid", typ:"fk"}, {nom:"visible", typ:"bool"}]},
	              {nom:"administrateur", cs:"pseudo", champs:[{nom:"adminid", typ:"pk"},{nom:"pseudo", typ:"text"},{nom:"pass", typ:"pass"},{nom:"email", typ:"email"},{nom:"actif", typ:"bool"}]},
	              {nom:"parametre", cs:"nom", champs:[{nom:"paramid", typ:"pk"},{nom:"nom", typ:"ref"},{nom:"valeur", typ:"text"},{nom:"commentaire", typ:"text"}]},
	              {nom:"cpzone", cs:"codepostal", champs:[{nom:"cpzoneid", typ:"pk"},{nom:"codepostal", typ:"codepostal"},{nom:"ville", typ:"text"},{nom:"actif", typ:"bool"}]},
	              {nom:"barlivr", cs:"", champs:[{nom:"barlivrid", typ:"pk"},{nom:"valminin", typ:"prix"},{nom:"valmaxex", typ:"prix"},{nom:"surcout", typ:"prix"},
	              	{nom:"limitebasse", typ:"bool"},{nom:"limitehaute", typ:"bool"}]}
	              ];  

  var liens = [{nom:"categorie", srctbl:"article", srcfld:"catid", dsttbl:"categorie", dstfld:"catid"},
  						 {nom:"groupeopt", srctbl:"relgrpoptart", srcfld:"grpoptid", dsttbl:"groupeopt", dstfld:"grpoptid"},
  						 {nom:"article", srctbl:"relgrpoptart", srcfld:"artid", dsttbl:"article", dstfld:"artid"},
  						 {nom:"groupeopt", srctbl:"option", srcfld:"grpoptid", dsttbl:"groupeopt", dstfld:"grpoptid"}
  						 ];
  						 
	var rpp = [5,10,15,20,50,100];  
  
  $(function() {
    gettable( "table0", "categorie", deflimite, offset);
    gettable( "table1", "article", deflimite, offset);
    gettable( "table2", "relgrpoptart", deflimite, offset);
    gettable( "table3", "groupeopt", deflimite, offset);
    gettable( "table4", "option", deflimite, offset);
    gettable( "table5", "administrateur", deflimite, offset);
    gettable( "table6", "parametre", deflimite, offset);
    gettable( "table7", "cpzone", deflimite, offset);
    gettable( "table8", "barlivr", deflimite, offset);
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
			            modal.find('.modal-body p').text(data.error);
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
            modal.find('.modal-body p').text(data.error);
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
										inp.required = true;
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
						            modal.find('.modal-body p').text(data.error);
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
			
			function changeFunc(place, tablestr, $i) 
			{
				limite = $i;				
				gettable( place, tablestr, limite, 0);
   		}
   		
 			function vartotable(place, tablestr, donnees, total, limite, offset)
 			{
       	var tab;
       	var pkval;
       	nummtable = getnumtable(tablestr);
       	table = tables[numtable];
       	tab = '<table class="table table-bordered table-striped"><theader><tr>';
				for (var i=0; i<table.champs.length; i++)          	
       	{
       		if (table.champs[i].typ != "pk")
       		{
	       		tab = tab + '<th>';
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
     		tab = tab + '<th></th>';
       	tab = tab + '</tr></theader><tbody>';
       	for (var j=0; j<donnees.length; j++)
       	{
       		tab = tab + '<tr>';
					for (var i=0; i<donnees[j].length; i++)          	
	       	{
	       		if (table.champs[i].typ != "pk")
       			{
		       		tab = tab + '<td>';
		       		if (table.champs[i].typ != "bool")
		       			tab = tab + donnees[j][i];
		       		else {
		       			if (donnees[j][i] > 0)
		       				tab = tab + '<input type="checkbox" checked disabled>';
		       			else {
		       				tab = tab + '<input type="checkbox" disabled>';
		       			}
		       		}
		       		tab = tab + '</td>';
	       		}
	       		else {
	       			pkval = donnees[j][i];
	       		}
	       	}
	       	tab = tab + '<td width="1%"><button class="btn btn-primary" onclick="update(' + numtable + ',' + pkval + ',' + limite + ',' + offset + ')">Modifier</button></td></tr>';
       	}
       	
       	tab = tab + '</tbody></table>';
       	tab = tab + '<button class="btn btn-primary" onclick="insert(' + numtable + ',' + limite + ',' + offset + ')">Insérer</button>';
       	tab = tab + '<br>';
       	tab = tab + '<label for="rpp '+ numtable + '">Nombre de résultat par page</label>';
       	tab = tab + '<select onchange="changeFunc(\'' + place +'\',\'' + tablestr + '\',value);" name="rpp' + numtable + '" id="rppid' + numtable + '">';
				for (var k=0; k<rpp.length; k++)
				{
					if (limite == rpp[k])
						tab = tab + '<option value="' + rpp[k] + '" selected>' + rpp[k] + '</option>';       	
					else
						tab = tab + '<option value="' + rpp[k] + '">' + rpp[k] + '</option>';       	
				}				
      	vallimite = parseInt(limite);
       	//<option value="10">10</option><option value="15">15</option><option value="20">20</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="' + total + '">TOUT</option></select>';
				tab = tab + '</select>';				
				tab = tab + '<nav aria-label="Page navigation">';
				  tab = tab + '<ul class="pagination">';
				    if ((offset - vallimite) < 0)
					    tab = tab + '<li class="page-item disabled">';
					  else
					    tab = tab + '<li class="page-item">';
				      tab = tab + '<a class="page-link" href="javascript:gettable(\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (offset - vallimite) + ')" aria-label="Previous">';
				        tab = tab + '<span aria-hidden="true">&laquo;</span>';
				        tab = tab + '<span class="sr-only">Previous</span>';
				      tab = tab + '</a>';
				    tab = tab + '</li>';
				    var totalpage = Math.ceil(total / vallimite);
				    for (var k=0; k<totalpage;k++)
				    {
				    	if ((offset/ vallimite) == k)
				    		tab = tab + '<li class="page-item active"><a class="page-link" href="javascript:gettable(\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (k*limite) + ')">' + k + '</a></li>';
				    	else
				    		tab = tab + '<li class="page-item"><a class="page-link" href="javascript:gettable(\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (k*limite) + ')">' + k + '</a></li>';
				    }
				    if ((offset + vallimite) >= total)
				    	tab = tab + '<li class="page-item disabled">';
				    else
				    	tab = tab + '<li class="page-item">';
				      tab = tab + '<a class="page-link" href="javascript:gettable(\'' + place + '\',\'' + tablestr + '\',' + limite + ',' + (offset + vallimite) + ')" aria-label="Next">';
				        tab = tab + '<span aria-hidden="true">&raquo;</span>';
				        tab = tab + '<span class="sr-only">Next</span>';
				      tab = tab + '</a>';
				    tab = tab + '</li>';
				  tab = tab + '</ul>';
				tab = tab + '</nav>' ;   	
       	
       	
       	return tab; 			
 			}  
 			
      function gettable(place, table, limite, offset)      
      {
      	
      	var obj = { customer: boutic, action:"elemtable", tables:tables, table:table, liens:liens, colonne:"", row:"", idtoup:"", limite:"", offset:"" };
  	
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
			      modal.find('.modal-body p').text(data.error);
			      $('.modal').modal('show');
			    }
			    else 
			    {
			    	var total = data[0];	
		      	var obj2 = { customer: boutic, action:"vuetable", tables:tables, table:table, liens:liens, colonne:"", row:"", idtoup:"", limite:limite, offset:offset };
		  	
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
						      modal.find('.modal-body p').text(data.error);
						      $('.modal').modal('show');
		          	}
		         		else 
			            document.getElementById(place).innerHTML = vartotable(place, table, data, total, limite, offset) ;
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
	            modal.find('.modal-body p').text(data.error);
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
            modal.find('.modal-body p').text(data.error);
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
            modal.find('.modal-body p').text(data.error);
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
