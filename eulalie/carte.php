<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom.css">
  </head>
  <body onload="reachBottom()">
    <?php

    include "config/config.php";
    include "param.php";

    session_start();
    $_SESSION['mail'] = 'non';

    $method = isset($_GET ['method']) ? $_GET ['method'] : '0';
    $table = isset($_GET ['table']) ? $_GET ['table'] : '0';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);
    // Check connection
    if ($conn->connect_error) 
 	    die("Connection failed: " . $conn->connect_error);

    $logo = GetValeurParam("master_logo",$conn); 
    
    $mntcmdmini = GetValeurParam("MntCmdMini",$conn);
    
    $mntlivraisonmini = GetValeurParam("MntLivraisonMini",$conn);
    
    $verifcp = GetValeurParam("VerifCP",$conn);    
    
    
    echo '<div id="main" data-method="' . $method . '" data-table="' . $table . '" data-mntcmdmini="' . $mntcmdmini .'" data-mntlivraisonmini="' . $mntlivraisonmini .'">';
    echo '<img id="logo" src="' . $logo . '">';

    echo '<form name="mainform" method="post" action="paiement.php?method=';
    echo $method ;
    echo '&table=';
    echo $table ;
    echo '">';
    
    echo '<div id="grpinfo">';
    
    if ($method == '3')
    {
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
    
      //echo "Connected successfully";
      $query = 'SELECT catid, nom, visible FROM categorie';

		if ($result = $conn->query($query)) {
    		while ($row = $result->fetch_row()) {
    			if ($row[2] > 0 )
    			{
    				echo '<button type="button" class="accordion">';
    				echo html_entity_decode($row[1]);
    				echo '</button>';
    				echo '<div class="panel">';
    				//$conn2 = new mysqli($servername, $username, $password);
		      	$query2 = 'SELECT artid, nom, prix, unite, description, image, imgvisible FROM article WHERE visible = 1 AND obligatoire = 0 AND catid = ' . $row[0] ;
		      	//echo $query2;
  					if ($result2 = $conn->query($query2)) 
	  				{
    					while ($row2 = $result2->fetch_row()) 
    					{
            		echo '<div class="artcel" id="artid' . $row2[0] . '" data-name="' . $row2[1] . '" data-prix="' . $row2[2] . '" data-unite="' . $row2[3] . '">';
              	if ($row2[6]>0)
              	  echo '<img class="rightpic" src="upload/' . $row2[5] . '" alt = "nopic">';
              	//echo '<div>';
              	echo '<a class="nom">';
       	      	echo $row2[1];
       	      	echo '<br />';
       	      	echo '</a>';
       	      	echo '<a class="prix">';
       	      	echo number_format($row2[2], 2, ',', ' ');
       	      	echo ' ';
       	      	echo $row2[3];
       	      	echo '<br />';
       	      	echo '</a>';
       	      	echo '<a class="desc">';
       	      	if (!empty($row2[4]))
       	      	{
       	      	 echo $row2[4];
                 echo '<br />'; 
       	      	}
       	      	echo '</a>';
       	      	if($method > 0) 
       	      	{
					  		  echo '<label>Quantit&eacute;</label>';
					  		  $id = 'qt' . $row2[0];
					  		  $name = 'qty' . $row2[0];    
            		  echo '<input class="artqt" type="number" id="' . $id . '" name="' . $name . '" min="0" max="100" onkeyup="showoptions(this)" onchange="showoptions(this)">';
                  echo '<br />'; 
              	}
              	else 
              	{
                  echo '<br />';
                  echo '<br />'; 
              	}
              	//echo '</div>';
                echo '<textarea id="idtxta' . $row2[0] . '" name="txta' . $row2[0] . '" placeholder="Saisissez ici vos besoins spécifiques sur cet article"  hidden></textarea>';              
                
 				  		  $id = 'opt' . $row2[0];
 				  		  $name = 'opty' . $row2[0];    
				        
       	      	if($method > 0) 
       	      	{
  				        echo '<div class="divopt" id="' . $id . '" name="' . $name . '" hidden>';
  				        echo '<div class="slide" data-artid="' . $row2[0] . '" data-nom="' . html_entity_decode($row2[1]) . '" hidden></div>';
				          echo '<div class="divopt2" id="' . $id . '" name="' . $name . '" hidden>';
				        }
				        else 
				        {
  				        echo '<div class="divopt" id="' . $id . '" name="' . $name . '">';
  				        echo '<div class="slide" data-artid="' . $row2[0] . '" data-nom="' . html_entity_decode($row2[1]) . '"></div>';
				          echo '<div class="divopt2" id="' . $id . '" name="' . $name . '">';
				        }
				        
                $query3 = 'SELECT groupeopt.grpoptid, groupeopt.nom, groupeopt.multiple FROM relgrpoptart, groupeopt WHERE relgrpoptart.visible = 1 AND groupeopt.visible = 1 AND artid = ' . $row2[0] . ' AND relgrpoptart.grpoptid = groupeopt.grpoptid';
                //echo $query3;
  					    if ($result3 = $conn->query($query3)) 
	  				    {
    					    while ($row3 = $result3->fetch_row()) 
    					    {
    					      echo '<fieldset>';
    					      if ($row3[2] == 0)
                      echo '<legend>' . $row3[1] . ' (une seul option possible)</legend>';
                    else if ($row3[2] == 1)
                      echo '<legend>' . $row3[1] . ' (plusieurs choix possible)</legend>';
     					      $query4 = 'SELECT optid, nom, surcout FROM `option` WHERE visible = 1 AND grpoptid = ' . $row3[0];
     					      //echo $query4;
       					    if ($result4 = $conn->query($query4)) 
         				    {
         					    while ($row4 = $result4->fetch_row()) 
         					    {
         					      if($method > 0)
         					      {
                        //echo '<label for="opt1">' . $row4[1] . '</label>';
                          if ($row3[2] == 0)
                          {
                            if ($row4[2]>0) 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="radio" name="opt1' . $row3[0] . '" id="opt1id' . $row4[0] . '" value="' . $row4[1] . '">' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</input>';
                            else 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="radio" name="opt1' . $row3[0] . '" id="opt1id' . $row4[0] . '" value="' . $row4[1] . '">' . $row4[1] . '</input>';
                          }
                          else if ($row3[2] == 1)
                          {
                            if ($row4[2]>0) 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="checkbox" name="opt1' . $row3[0] . '" id="opt1id' . $row4[0] . '" value="' . $row4[1] . '">' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</input>';
                            else 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="checkbox" name="opt1' . $row3[0] . '" id="opt1id' . $row4[0] . '" value="' . $row4[1] . '">' . $row4[1] . '</input>';
                          }
                        }
                        else 
                        {
                          if ($row3[2] == 0)
                          {
                            if ($row4[2]>0) 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="radio" name="opt1' . $row3[0] . '" id="opt1id' . $row4[0] . '" value="' . $row4[1] . '" disabled>' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</input>';
                            else 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="radio" name="opt1' . $row3[0] . '" id="opt1id' . $row4[0] . '" value="' . $row4[1] . '" disabled>' . $row4[1] . '</input>';
                          }
                          else if ($row3[2] == 1)
                          {
                            if ($row4[2]>0) 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="checkbox" name="opt1' . $row3[0] . '" id="opt1id' . $row4[0] . '" value="' . $row4[1] . '" disabled>' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</input>';
                            else 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="checkbox" name="opt1' . $row3[0] . '" id="opt1id' . $row4[0] . '" value="' . $row4[1] . '" disabled>' . $row4[1] . '</input>';
                          }
                        } 
                        echo '<br/>';
                      }
                    }
                    echo '</fieldset>';
                  }
                }
                
                echo '</div>';
                echo '</div>';
                  
         	    	echo '</div>';
					  	}						
					  	$result2->close();
        		}
			   		echo '</div>';    			
    			}
  			}
	   		$result->close();
			}
			// Affichage des Frais Fixe
    	$query3 = 'SELECT artid, nom, prix, unite, description, image, imgvisible FROM article WHERE visible = 1 AND obligatoire = 1';
    	//echo $query2;
			if ($result3 = $conn->query($query3)) 
			{
				while ($row3 = $result3->fetch_row()) 
				{
      		echo '<div class="artcel" id="artid' . $row3[0] . '" data-name="' . $row3[1] . '" data-prix="' . $row3[2] . '" data-unite="' . $row3[3] . '">';
        	if ($row3[6]>0)
        	  echo '<img class="rightpic" src="upload/' . $row3[5] . '" alt = "nopic">';
        	echo '<a class="nom">';
 	      	echo $row3[1];
 	      	echo '<br />';
 	      	echo '</a>';
 	      	echo '<a class="prix">';
 	      	echo number_format($row3[2], 2, ',', ' ');
 	      	echo ' ';
 	      	echo $row3[3];
 	      	echo '<br />';
 	      	echo '</a>';
 	      	echo '<a class="desc">';
 	      	if (!empty($row3[4]))
 	      	{
 	      	 echo $row3[4];
           echo '<br />'; 
 	      	} 
 	      	echo '</a>';
 	      	if($method > 0) 
 	      	{
		  		  echo '<label hidden>Quantit&eacute;</label>';
		  		  $id = 'qt' . $row3[0];
		  		  $name = 'qty' . $row3[0];    
      		  echo '<input class="artqt" type="hidden" id="' . $id . '" name="' . $name . '" value="1">';
        	}
        	echo '<br />';
   	    	echo '</div>';
		  	}						
		  	$result3->close();
      }
      
      if  ($method == 3)
      {
        $chp = GetValeurParam("Choix_Paiement",$conn);         
        
        $cmpt = GetValeurParam("MP_Comptant",$conn); 
    
        $livr = GetValeurParam("MP_Livraison",$conn);

        echo '<div id="paye">';
        echo '<div id="modep" data-permis="' . $chp . '">';
        echo '</div>';
        echo '<br>';
        echo '<label id="pcomptant" hidden>' . $cmpt . '</label>';
        echo '<label id="plivraison" hidden>' . $livr . '</label>';
        echo '<br>';
        echo '</div>';
      }
      echo '<div id="cgv">Vous pouvez consulter nos conditions générales de vente<a href="CGV.htm">ici</a></div>';
      echo '</form>';
            
      echo '</div>';
    ?>
    <div id="footer">
      <?php
        if  ($method > 0)
        {
            echo '<input class="inpmove" type="button" value="Envoyer la commande" onclick="genCartList(this)">';
        }
      ?>
    </div>
    <script type="text/javascript" >
      if(/Android/.test(navigator.appVersion)) {
        window.addEventListener("resize", function() {
          if(document.activeElement.tagName=="INPUT" || document.activeElement.tagName=="TEXTAREA") {
            document.activeElement.scrollIntoView();
          }
        })
      }     
    </script>
    <script type="text/javascript">
    	var acc = document.getElementsByClassName("accordion");
      var i;

        for (i = 0; i < acc.length; i++) 
        {
          acc[i].addEventListener("click", function() {
      	    this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) 
            {
            	panel.style.maxHeight = null;
            } 
            else 
            {
              panel.style.maxHeight = panel.scrollHeight + "px";
            } 
          });
        }
    </script>
    <script type="text/javascript">
    	var aqt = document.getElementsByClassName("artqt");
      var i;

        for (i = 0; i < aqt.length; i++) 
        {
          aqt[i].addEventListener("focus", function() {
      	    this.parentElement.parentElement.previousElementSibling.classList.add("active");
      	    var panel = this.parentElement.parentElement;
            panel.style.maxHeight = panel.scrollHeight + "px";
          });
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
    function genCartList()
    {
      var somme =0;
      var failed = false;
      var opt = [];
      var mntcmdmini = document.getElementById("main").getAttribute("data-mntcmdmini");
      var mntlivraisonmini = document.getElementById("main").getAttribute("data-mntlivraisonmini");
      localStorage.removeItem("method");
      localStorage.removeItem("table");
      localStorage.removeItem("nom");
      localStorage.removeItem("prenom");
      localStorage.removeItem("adresse1");
      localStorage.removeItem("adresse2");
      localStorage.removeItem("codepostal");
      localStorage.removeItem("ville");
      localStorage.removeItem("telephone");
      localStorage.setItem("method", document.getElementById("main").getAttribute("data-method"));
      localStorage.setItem("table", document.getElementById("main").getAttribute("data-table"));
      if (localStorage.getItem("method") > 0)
      {
        if (localStorage.getItem("method")==3)
          localStorage.setItem("nom", document.getElementById("lenom").value);
        if ((localStorage.getItem("method")==2)||(localStorage.getItem("method")==3))
          localStorage.setItem("prenom", document.getElementById("leprenom").value);
        if (localStorage.getItem("method")==3)
        {
          localStorage.setItem("adresse1", document.getElementById("ladresse1").value);
          localStorage.setItem("adresse2", document.getElementById("ladresse2").value);
          localStorage.setItem("codepostal", document.getElementById("lecp").value);
          localStorage.setItem("ville", document.getElementById("laville").value);
          localStorage.setItem("telephone", document.getElementById("letel").value);
        }
        if (localStorage.getItem("method")==3)
          localStorage.setItem("choice", document.getElementById("modep").getAttribute("data-choice"));        
        var artcel = document.getElementsByClassName("artcel");
        var artqt = document.getElementsByClassName("artqt");

        var ligne = [];
        var idc = 0;
        var qtc = 0;
        var j = 0;
        for (var i = 0; i<artcel.length; i++ )
        {
          var options = "";
          var artopt = artcel[i].getElementsByClassName("divopt2")[0];
          if (artopt != null)
          {
            if (artopt.innerHTML != "")
            {
              var opttab = artcel[i].getElementsByClassName("divopttab");
              for (k=0; k<opttab.length; k++)
              {
                var sefld = opttab[k].children;
                for (l=0; l<sefld.length; l++) 
                {
                  var alfa = true;
                  var secase = sefld[l].children;
                  for (m=0; m<secase.length; m++) 
                  {
                    if (secase[m].tagName == "INPUT") 
                    {
                      if (secase[m].type == "radio")
                      {
                        if (secase[m].checked == true)
                        {
                          options = options + " / " + secase[m].value;
                          alfa = false;
                        }
                      }
                      if (secase[m].type == "checkbox")
                      {
                        alfa = false;
                        if (secase[m].checked == true)
                        {
                          options = options + " + " + secase[m].value;
                        }
                      }
                    }
                  }
                  if (alfa == true)
                  {
                    alert("Il manque un choix sur l article " + artcel[i].getAttribute("data-name") + " numéro " + (k+1) + " dans le groupe d'option " + secase[0].innerHTML );
                    failed = true;
                  }
                }              
                options = options + "<br />";
              }         
            }
          }
          var txt = "";
          var txtf = artcel[i].getElementsByTagName("TEXTAREA")[0];
          if (txtf != null)          
            txt = txtf.value;
          idc = artcel[i].id.substr(5);  
          qtc = artqt[i].value; 
          if (qtc === "")
            qtc = 0;          
          if (qtc > 0)
          {
            ligne[j] = {id:idc, type:"article", name:artcel[i].getAttribute("data-name"), prix:artcel[i].getAttribute("data-prix"), qt:qtc, unite:artcel[i].getAttribute("data-unite"), opts:options, txta:txt};
            somme = somme + ligne[j].prix * ligne[j].qt;
            j++;
          }
        }
        for (var ii = 0; ii<artcel.length; ii++ )
        {
          var artopt = artcel[ii].getElementsByClassName("divopt2")[0];
          if (artopt != null)
          {
            if (artopt.innerHTML != "")
            {
              var opttab = artcel[ii].getElementsByClassName("divopttab");
              for (ik=0; ik<opttab.length; ik++)
              {
                var sefld = opttab[ik].children;
                for (il=0; il<sefld.length; il++) 
                {
                  var alfa = true;
                  var secase = sefld[il].children;
                  for (im=0; im<secase.length; im++) 
                  {
                    if (secase[im].tagName == "INPUT") 
                    {
                      if (secase[im].checked == true)
                      {
                        var theid = secase[im].id.substr(6);
                        var myoption = {id:theid, type:"option", name:secase[im].value, prix:secase[im].getAttribute("data-surcout"), qt:1, unite:"€", opts:"", txta:""};
                        var alfd = false;                          
                        for(io=0;io<opt.length;io++)
                        {
                          if (opt[io].id == myoption.id )
                          {
                            alfd = true;
                            opt[io].qt = opt[io].qt + 1;                              
                          }
                        }
                        if (alfd == false)
                        {
                          opt.push(myoption);                          
                        }                            
                      } 
                    }
                  }
                }              
              }         
            }
          }
        }
        for (jj=0;jj<opt.length;jj++)
        {
          ligne.push(opt[jj]);
          somme = somme + opt[jj].prix * opt[jj].qt;
        }
        var jsonligne = JSON.stringify(ligne);          
          
        localStorage.setItem("commande", jsonligne);
      }
      
      if (localStorage.getItem("method")==3) {
        if (somme < mntcmdmini) {
          alert("Les livraison sont acceptées à partir de " + mntlivraisonmini + " €");
          failed = true;
        }
        if ( document.getElementById("lecp").getAttribute("data-inrange") !== "ok") {
          alert("Vous n\'êtes pas situé dans notre zone de livraison, impossible de continuer.");
          failed = true;
        }
        if (document.getElementById("modep").getAttribute("data-choice") == "NONE") {
          alert("Vous n\'avez pas choisi comment régler la transaction, impossible de continuer");
          failed = true;
        }
        
      } else {
        if (somme < mntcmdmini) {
          alert("La commmande doit être au moins de " + mntcmdmini + " €");
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
    <script type="text/javascript" >
      function showoptions(eleminp) 
      {
        var fart = eleminp.parentElement.getElementsByTagName("TEXTAREA")[0];
        
        if (eleminp.value > 0)
          fart.hidden = false;
        else
        	fart.hidden = true;
        
        
        var elemopt = eleminp.parentElement.getElementsByClassName("divopt")[0];
       
        var slide = elemopt.getElementsByClassName("slide")[0]; 
        
        slide.innerHTML = "";        
        
        var cur = 1;
        var nbtab = eleminp.value;
        
      	var nom = slide.getAttribute("data-nom");
      	var artid = slide.getAttribute("data-artid");
      	
      	var lbl = document.createElement("A");
      	lbl.innerHTML = nom + " numéro";
        slide.appendChild(lbl);
      	var inputg = document.createElement("INPUT");
      	inputg.id = "fg";
      	inputg.classList.add("arrow"); 
      	inputg.type ="button";
      	inputg.value = "<";
        inputg.onclick = function() {setart(this, -1)};
        if (cur == 1)
          inputg.disabled = true;
        slide.appendChild(inputg);
        var cura = document.createElement("A");
        cura.innerHTML = cur;
        cura.classList.add("curarticle");
        slide.appendChild(cura);
      	var inputd = document.createElement("INPUT");
      	inputd.id = "fd";
      	inputd.classList.add("arrow"); 
      	inputd.type ="button";
      	inputd.value = ">";
        inputd.onclick = function() {setart(this, 1)};
        if (nbtab == 1)
          inputd.disabled = true;
        slide.appendChild(inputd);
        var lbl2 = document.createElement("A");
        lbl2.innerHTML = " / ";
        slide.appendChild(lbl2);     
        var totala = document.createElement("A");
        totala.innerHTML = nbtab;
        totala.classList.add("totarticle");
        slide.appendChild(totala);
                
        var etodel = elemopt.getElementsByClassName("divopttab");
       
        while (etodel.length > 0)
          etodel[0].remove();
        
        var etodup = elemopt.getElementsByClassName("divopt2")[0];
        
        if (etodup.innerHTML != "")
          slide.hidden = false;
        else
        	slide.hidden = true;
                
        for (j=0; j<eleminp.value; j++)
        {
          var edup = etodup.cloneNode(true);
          if (j>0)
            edup.hidden = true;
          else {
          	edup.hidden = false;
          }
          edup.setAttribute("class","divopttab");
          
          var sefld = edup.children;
          
          for (k=0; k<sefld.length; k++) 
          {
            var secase = sefld[k].children;
            for (l=0; l<secase.length; l++) 
            {
              if (secase[l].tagName == "INPUT") 
              {
                secase[l].name = "art" + artid + "opt" + j + "case" + k;
              }
            }
          }         
          elemopt.appendChild(edup);
        }

        eleminp.parentElement.parentElement.previousElementSibling.classList.add("active");        
        
      	var panel = eleminp.parentElement.parentElement;
        if (eleminp.value > 0)
        {
          elemopt.style.display = "block";
        } else {
          elemopt.style.display = "none";
        }
        panel.style.maxHeight = panel.scrollHeight + "px";
             
      }
    </script>
    <script type="text/javascript" >
      function setart(elem, val)
      {
        var valdef = 0;
        var elemopt = elem.parentElement.parentElement; 
        var eid = elem.id;
        var aeid;
        if (eid == "fd")
          aeid = "fg";
        else 
          aeid = "fd";
        	
         
        valdef = Number(elemopt.getElementsByClassName("curarticle")[0].innerHTML);
        valdef = valdef + val;
        elemopt.getElementsByClassName("curarticle")[0].innerHTML = valdef;
         
        var listtab = elemopt.getElementsByClassName("divopttab");
        for (j=0; j<listtab.length; j++)
        {
          if (j+1 == valdef)
            listtab[j].hidden = false;
          else {
          	listtab[j].hidden = true;
          }
        }
        var padg = elemopt.children[0].children[1];
        var padd = elemopt.children[0].children[3];

        padg.disabled = false;
        padd.disabled = false;
        
        if (valdef == 1)
        {
          padg.disabled = true;
        }
        
        if (valdef == listtab.length)
        {        
          padd.disabled = true;
        }

      }
    </script>
    <script type="text/javascript">
        window.addEventListener("resize", function() {
          reachBottom();
        })
    </script>
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
    <script type="text/javascript">
    
      function displaypos(choice) 
      {
        var modep = document.getElementById("modep");
      	var pcomptant = document.getElementById("pcomptant");
      	var plivraison = document.getElementById("plivraison");
        if (choice == 'COMPTANT')
        {
          pcomptant.hidden = false;
          plivraison.hidden = true;
        }
        if (choice == 'LIVRAISON')
        {
          pcomptant.hidden = true;
          plivraison.hidden = false;
        }
        modep.setAttribute("data-choice", choice);
      }    
    
      if (document.getElementById("main").getAttribute("data-method") == 3) {
        var modep = document.getElementById("modep");
        var permis = modep.getAttribute("data-permis");
        modep.setAttribute("data-choice", "NONE");
        if (permis == 'COMPTANT')
        {
          var lanode = document.createElement("label");
          var comptant = document.createTextNode("Paiement au COMPTANT"); 
          lanode.appendChild(comptant);
          modep.appendChild(lanode);
          displaypos('COMPTANT');
        }
        if (permis == 'LIVRAISON')
        {
          var lanode = document.createElement("label");
          var livraison = document.createTextNode("Paiement à la LIVRAISON"); 
          lanode.appendChild(livraison);
          modep.appendChild(lanode);
          displaypos('LIVRAISON');
        }
        if (permis == 'TOUS')
        {
          var lanode1 = document.createElement("label");
          var pmt = document.createTextNode("Paiement au ");
          var br = document.createElement("br");
          lanode1.appendChild(pmt); 
          lanode1.appendChild(br);
          var inp1 = document.createElement("input");
          inp1.classList.add("paiement");
          inp1.type = "radio";
          inp1.name = "modepaiement";
          inp1.id = "comptant";
          inp1.onclick = function() {displaypos('COMPTANT')};
          var lanode11 = document.createElement("label");
          var cpt = document.createTextNode("COMPTANT");
          lanode11.appendChild(cpt);
          var lanode2 = document.createElement("label");
          var ou = document.createTextNode(" ou à la ");
          lanode2.appendChild(ou);
          var inp2 = document.createElement("input");
          inp2.classList.add("paiement");
          inp2.type = "radio";
          inp2.name = "modepaiement";
          inp2.id = "livraison";
          inp2.onclick =  function() {displaypos('LIVRAISON')};
          var lanode21 = document.createElement("label");
          var livr = document.createTextNode("LIVRAISON");
          lanode21.appendChild(livr);
          
          modep.appendChild(lanode1);        
          modep.appendChild(inp1);
          modep.appendChild(lanode11);
          modep.appendChild(lanode2);
          modep.appendChild(inp2);
          modep.appendChild(lanode21);
          
        }
      }

    </script>
    
  </body>
</html>
