<?php
session_start();
$customer = $_GET['customer'];
$_SESSION[$customer . '_mail'] = "non";
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Prise de commande</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css?v=1.28">
    <link rel="stylesheet" href="../<?php echo $customer;?>/css/custom.css?v=1.28">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <?php

    include "config/common_cfg.php";
    include "param.php";

    $method = isset($_GET ['method']) ? $_GET ['method'] : '0';
    $table = isset($_GET ['table']) ? $_GET ['table'] : '0';

    $conn = new mysqli($servername, $username, $password, $bdd);
    if ($conn->connect_error) 
 	    die("Connection failed: " . $conn->connect_error);
 	    
    $reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
 	  $reqci->bind_param("s", $customer);
 	  $reqci->execute();
 	  $reqci->bind_result($customid);
 	  $resultatci = $reqci->fetch();
 	  $reqci->close();

    $logo = GetValeurParam("master_logo",$conn, $customid); 

    $mntcmdmini = GetValeurParam("MntCmdMini",$conn, $customid,"0");
    
    $mntlivraisonmini = GetValeurParam("MntLivraisonMini",$conn, $customid, "0");

    $sizeimg = GetValeurParam("SIZE_IMG",$conn, $customid,"smallimg");

    echo '<div id="main" data-method="' . $method . '" data-table="' . $table . '" data-mntcmdmini="' . $mntcmdmini .'" data-mntlivraisonmini="' . $mntlivraisonmini .'" data-customer="' . $customer .'">';
    echo '<img id="logo" src="../' . $customer . '/' . $logo . '">';

    echo '<form name="mainform" autocomplete="off" method="post" action="getinfo.php?method=';
    echo $method ;
    echo '&table=';
    echo $table ;
    echo '&customer=';
    echo $customer ;
    echo '">';
    
    //echo "Connected successfully";
    $query = 'SELECT catid, nom, visible FROM categorie WHERE customid = ' . $customid;

		if ($result = $conn->query($query)) {
    		while ($row = $result->fetch_row()) {
    			if ($row[2] > 0 )
    			{
    			  echo '<button type="button" class="accordion">';
    				echo html_entity_decode($row[1]);
    				echo '</button>';
    				echo '<div class="panel">';
		      	$query2 = 'SELECT artid, nom, prix, unite, description, image, imgvisible FROM article WHERE customid = ' . $customid . ' AND visible = 1 AND obligatoire = 0 AND catid = ' . $row[0] ;
		      	if ($result2 = $conn->query($query2)) 
	  				{
	  				  while ($row2 = $result2->fetch_row()) 
    					{
    					  echo '<div class="artcel" id="artid' . $row2[0] . '" data-name="' . $row2[1] . '" data-prix="' . $row2[2] . '" data-unite="' . $row2[3] . '">';
              	if ($row2[6]>0)
                {
              	  echo '<img class="pic ' . $sizeimg . '" src="../' . $customer . '/upload/' . $row2[5] . '" alt = "nopic">';
                }
              	echo '<a class="nom">';
       	      	echo $row2[1];
       	      	echo '<br />';
       	      	echo '</a>';
       	      	echo '<a class="desc">';
       	      	if (!empty($row2[4]))
       	      	{
       	      	 echo $row2[4];
                 echo '<br />'; 
       	      	}
       	      	echo '</a>';
								echo '<a class="prix">';
       	      	echo number_format($row2[2], 2, ',', ' ');
       	      	echo ' ';
       	      	echo $row2[3];
       	      	echo '<br />';
       	      	echo '</a>';
       	      	if($method > 0) 
       	      	{
					  		  echo '<label>Quantit&eacute;</label>';
					  		  $id = 'qt' . $row2[0];
					  		  $name = 'qty' . $row2[0];    
            		  echo '<input class="artqt" type="number" id="' . $id . '" name="' . $name . '" value="0" min="0" max="100" onkeyup="showoptions(this)" onchange="showoptions(this)" readonly>';
            		  echo '<label> ';
            		  echo '<button class="bts bplus" type="button" onclick="addqt(this)">  +  </button>';
            		  echo ' ';
            		  echo '<button class="bts bmoins" type="button" onclick="subqt(this)" disabled>  -  </button>';
            		  echo ' </label>';
                  echo '<br />'; 
              	}
              	else 
              	{
                  echo '<br />';
                  echo '<br />'; 
              	}

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
				        
                $query3 = 'SELECT groupeopt.grpoptid, groupeopt.nom, groupeopt.multiple FROM relgrpoptart, groupeopt WHERE relgrpoptart.customid = ' . $customid . ' AND groupeopt.customid = ' . $customid . ' AND relgrpoptart.visible = 1 AND groupeopt.visible = 1 AND artid = ' . $row2[0] . ' AND relgrpoptart.grpoptid = groupeopt.grpoptid';
  					    if ($result3 = $conn->query($query3)) 
	  				    {
    					    while ($row3 = $result3->fetch_row()) 
    					    {
    					      echo '<fieldset>';
    					      if ($row3[2] == 0)
                      echo '<legend>' . $row3[1] . ' (une seul option possible)</legend>';
                    else if ($row3[2] == 1)
                      echo '<legend>' . $row3[1] . ' (plusieurs choix possible)</legend>';
     					      $query4 = 'SELECT optid, nom, surcout FROM `option` WHERE customid = ' . $customid . ' AND visible = 1 AND grpoptid = ' . $row3[0];
       					    if ($result4 = $conn->query($query4)) 
         				    {
         					    while ($row4 = $result4->fetch_row()) 
         					    {
         					      if($method > 0)
         					      {
                          if ($row3[2] == 0)
                          {
                            if ($row4[2]>0) 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="radio" name="art' . $row2[0] . 'op' . $row3[0] . '" id="art' . $row2[0] . 'opt' . $row4[0] . '" value="' . $row4[1] . '" onclick="totaliser()">' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</input>';
                            else 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="radio" name="art' . $row2[0] . 'op' . $row3[0] . '" id="art' . $row2[0] . 'opt' . $row4[0] . '" value="' . $row4[1] . '" onclick="totaliser()">' . $row4[1] . '</input>';
                          }
                          else if ($row3[2] == 1)
                          {
                            if ($row4[2]>0) 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="checkbox" name="art' . $row2[0] . 'op' . $row3[0] . '" id="art' . $row2[0] . 'opt' . $row4[0] . '" value="' . $row4[1] . '" onclick="totaliser()">' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</input>';
                            else 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="checkbox" name="art' . $row2[0] . 'op' . $row3[0] . '" id="art' . $row2[0] . 'opt' . $row4[0] . '" value="' . $row4[1] . '" onclick="totaliser()">' . $row4[1] . '</input>';
                          }
                        }
                        else 
                        {
                          if ($row3[2] == 0)
                          {
                            if ($row4[2]>0) 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="radio" name="art' . $row2[0] . 'op' . $row3[0] . '" id="art' . $row2[0] . 'opt' . $row4[0] . '" value="' . $row4[1] . '" disabled>' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</input>';
                            else 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="radio" name="art' . $row2[0] . 'op' . $row3[0] . '" id="art' . $row2[0] . 'opt' . $row4[0] . '" value="' . $row4[1] . '" disabled>' . $row4[1] . '</input>';
                          }
                          else if ($row3[2] == 1)
                          {
                            if ($row4[2]>0) 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="checkbox" name="art' . $row2[0] . 'op' . $row3[0] . '" id="art' . $row2[0] . 'opt' . $row4[0] . '" value="' . $row4[1] . '" disabled>' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</input>';
                            else 
                              echo '<input data-surcout="' . $row4[2] . '" class="qtopt" type="checkbox" name="art' . $row2[0] . 'op' . $row3[0] . '" id="art' . $row2[0] . 'opt' . $row4[0] . '" value="' . $row4[1] . '" disabled>' . $row4[1] . '</input>';
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
    	$query3 = 'SELECT artid, nom, prix, unite, description, image, imgvisible FROM article WHERE customid = ' . $customid . ' AND visible = 1 AND obligatoire = 1';
			if ($result3 = $conn->query($query3)) 
			{
				while ($row3 = $result3->fetch_row()) 
				{
      		echo '<div class="artcel" id="artid' . $row3[0] . '" data-name="' . $row3[1] . '" data-prix="' . $row3[2] . '" data-unite="' . $row3[3] . '">';
        	if ($row3[6]>0)
                {
        	  echo '<img class="pic ' . $sizeimg . '" src="../' . $customer . '/upload/' . $row3[5] . '" alt = "nopic">';
                  echo '<br>';
                }
        	echo '<a class="nom">';
 	      	echo $row3[1];
 	      	echo '<br />';
 	      	echo '</a>';
 	      	echo '<a class="desc">';
 	      	if (!empty($row3[4]))
 	      	{
 	      	 echo $row3[4];
           echo '<br />'; 
 	      	} 
 	      	echo '</a>';
 	      	echo '<a class="prix">';
 	      	echo number_format($row3[2], 2, ',', ' ');
 	      	echo ' ';
 	      	echo $row3[3];
 	      	echo '<br />';
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
      
      echo '</form>';
            
      echo '</div>';
    ?>
    <div id="footer">
      <?php
        if  ($method > 0)
        {
          echo '<div class="tot">';
          echo '<label class="labtot" for="totaliseur">Total :</label>';
          echo '<input name="totaliseur" id="totaliseur" type="number" readonly>';
          echo '<label class="labtot" for="totaliseur"> &euro; </label>';
          echo '</div>';
          echo '<input id="validcarte" class="inpmove poursuivre" type="button" value="Poursuivre" onclick="genCartList()">';
        }
      ?>
    </div>
    <script type="text/javascript" >
      function totaliser() 
      {
        var artcel = document.getElementsByClassName("artcel");
        var artqt = document.getElementsByClassName("artqt");
        var somme = 0;
        var opt = [];
        
        for (var i = 0; i<artqt.length; i++ )
        {
          idc = artcel[i].id.substr(5);  
          qtc = artqt[i].value; 
          if (qtc === "")
            qtc = 0;          
          if (qtc > 0)
          {
            somme = somme + artcel[i].getAttribute("data-prix") * qtc;
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
                  var secase = sefld[il].children;
                  for (im=0; im<secase.length; im++) 
                  {
                    if (secase[im].tagName == "INPUT") 
                    {
                      if (secase[im].checked == true)
                      {
                        somme = somme + parseFloat(secase[im].getAttribute("data-surcout"));                            
                      } 
                    }
                  }
                }              
              }         
            }
          }
        }
        document.getElementById("totaliseur").value = somme.toFixed(2);      	
      }
    </script>

    <script type="text/javascript">
      function addqt(elem)
      {
        elem.parentElement.previousSibling.value = parseInt(elem.parentElement.previousSibling.value) + 1;
        showoptions(elem.parentElement.previousSibling);
        if (parseInt(elem.parentElement.previousSibling.value) > 0)
          elem.nextElementSibling.disabled = false
        totaliser();

      }
      function subqt(elem)
      {
        if (parseInt(elem.parentElement.previousSibling.value) > 0)
        {
          elem.parentElement.previousSibling.value = parseInt(elem.parentElement.previousSibling.value) - 1;
          showoptions(elem.parentElement.previousSibling);
          if (parseInt(elem.parentElement.previousSibling.value) == 0)
            elem.disabled = true;
        }
        totaliser();
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
      sessionStorage.setItem("method", document.getElementById("main").getAttribute("data-method"));
      sessionStorage.setItem("table", document.getElementById("main").getAttribute("data-table"));
      sessionStorage.setItem("customer", document.getElementById("main").getAttribute("data-customer"));
      if (sessionStorage.getItem("method") > 0)
      {
        var artcel = document.getElementsByClassName("artcel");
        var artqt = document.getElementsByClassName("artqt");
        
        var ligne = [];
        var idc = 0;
        var qtc = 0;
        var j = 0;
        for (var i = 0; i<artcel.length; i++ )
        {
          if (artqt[i].type !== "hidden" )
            sessionStorage.setItem(artqt[i].id, artqt[i].value);
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
                          sessionStorage.setItem(secase[m].id, 1);
                        }
                        else
                        	sessionStorage.setItem(secase[m].id, 0);
                      }
                      if (secase[m].type == "checkbox")
                      {
                        alfa = false;
                        if (secase[m].checked == true)
                        {
                          options = options + " + " + secase[m].value;
                          sessionStorage.setItem(secase[m].id, 1);
                        }
                        else
                        	sessionStorage.setItem(secase[m].id, 0);
                      }
                    }
                  }
                  if ((alfa == true) && (failed == false))
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
          {
            txt = txtf.value;
            sessionStorage.setItem(txtf.id, txt);
          }          
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
                        var mystr = secase[im].id;
                        var theid = mystr.substring(mystr.indexOf('opt')+3, mystr.length);
                        var myoption = {id:theid, type:"option", name:secase[im].value, prix:secase[im].getAttribute("data-surcout"), qt:1, unite:"€", opts:"", txta:""};
                        var alfd = false;                          
                        for(io=0;io<opt.length;io++)
                        {
                          var mystr2 = opt[io].id;
                          if (mystr2 == myoption.id )
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
          
        sessionStorage.setItem("commande", jsonligne);
      }
      
      if (sessionStorage.getItem("method")==3) {
        if ((somme < mntlivraisonmini) && (failed == false)) {
          alert("Les livraisons sont acceptées à partir de " + parseFloat(mntlivraisonmini).toFixed(2) + " € or la commande est de " + parseFloat(somme).toFixed(2) + " €");
          failed = true;
        }
      } else {
        if ((somme < mntcmdmini) && (failed == false)) {
          alert("La commmande doit être au moins de " + parseFloat(mntcmdmini).toFixed(2) + " € or la commande est de " + parseFloat(somme).toFixed(2) + " €");
          failed = true;
        }
      }
      
      for (var j=0; j < document.forms["mainform"].length; j++)
      {
        if ((document.forms["mainform"][j].checkValidity() == false) && (failed == false))
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
        
        eleminp.blur();
        var elemopt = eleminp.parentElement.getElementsByClassName("divopt")[0];
       
        var slide = elemopt.getElementsByClassName("slide")[0]; 
        
        slide.innerHTML = "";        
        var cur = 1;
        var nbtab = eleminp.value;
        
      	var nom = slide.getAttribute("data-nom");
      	var artid = slide.getAttribute("data-artid");
      	if (nbtab == 0)
          sessionStorage.removeItem("slidepos" + artid);
        if (nbtab == 1)
          sessionStorage.setItem("slidepos" + artid, 1); 
      	if (nbtab > 1)
          cur = sessionStorage.getItem("slidepos" + artid);
      	
      	var lbl = document.createElement("A");
      	lbl.innerHTML = nom + " numéro";
        slide.appendChild(lbl);
      	var inputg = document.createElement("INPUT");
      	inputg.id = "art"+ artid + "fg";
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
      	inputd.id = "art"+ artid + "fd";
      	inputd.classList.add("arrow"); 
      	inputd.type ="button";
      	inputd.value = ">";
        inputd.onclick = function() {setart(this, 1)};
        if (nbtab == cur)
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
       
        while (etodel.length > eleminp.value) // modif here replaced 0 by eleminp.value
        {
          if (cur > eleminp.value)
          {
            cur = cur - 1;
            setart(inputg, -1);
            inputd.disabled = true;
          }
          etodel[eleminp.value].remove();     // here too
          for (var i=0; i<etodel.length; i++) 
          {
            if (i == (cur - 1))
              etodel[cur - 1].hidden = false;
            else
            	etodel[i].hidden = true;
          }       
        }

        var etodup = elemopt.getElementsByClassName("divopt2")[0];
        
        if (etodup.innerHTML != "")
          slide.hidden = false;
        else
        	slide.hidden = true;
                
        while ((elemopt.childElementCount - 2) < eleminp.value)
        {
          var edup = etodup.cloneNode(true);
          
          edup.hidden = true;
          
          if ((elemopt.childElementCount - 2) == (cur - 1))
            edup.hidden = false;

          edup.setAttribute("class","divopttab");
          edup.setAttribute("data-numero", elemopt.childElementCount - 2);
                    
          var sefld = edup.children;
          
          for (k=0; k<sefld.length; k++) 
          {
            var secase = sefld[k].children;
            for (l=0; l<secase.length; l++) 
            {
              if (secase[l].tagName == "INPUT") 
              {
                secase[l].name = "art" + artid + "num" + (elemopt.childElementCount - 2) + "case" + k;
                secase[l].id = "art" + artid + "num" + (elemopt.childElementCount - 2) + secase[l].id;
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
         
        valdef = Number(elemopt.getElementsByClassName("curarticle")[0].innerHTML);
        valdef = valdef + val;
        elemopt.getElementsByClassName("curarticle")[0].innerHTML = valdef;
        sessionStorage.setItem("slidepos" + elem.parentElement.getAttribute("data-artid"), valdef);
                 
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
      function reachBottom() 
      {
        var x = window.innerHeight - document.getElementById("footer").clientHeight;
        x = x + "px";
        document.getElementById("main").style.height = x;
      }
    </script>
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
    <script type="text/javascript" >
    window.onload=function()
    {
      var artcel = document.getElementsByClassName("artcel");
      var artqt = document.getElementsByClassName("artqt");

      for (var i = 0; i<artqt.length; i++ )
      {
        bakqt = sessionStorage.getItem(artqt[i].id);
        if (bakqt !== null)
        {
          artqt[i].value = bakqt; 
          if ((artqt[i].value > 0) && (artqt[i].type !== "hidden"))
          {
            showoptions(artqt[i]);
            artqt[i].nextElementSibling.children[1].disabled = false;
            var txtf = artcel[i].getElementsByTagName("TEXTAREA")[0];
            txtf.value = sessionStorage.getItem(txtf.id);
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
                    var secase = sefld[l].children;
                    for (m=0; m<secase.length; m++) 
                    {
                      if (secase[m].tagName == "INPUT") 
                      {
                        if ((secase[m].type == "radio") ||(secase[m].type == "checkbox"))
                        { 
                          if (sessionStorage.getItem(secase[m].id) == 1)
                            secase[m].checked = true;
                          else 
                            secase[m].checked = false;
                        }
                      }
                    }
                  }              
                }         
              }
            }
       	    artqt[i].parentElement.parentElement.previousElementSibling.classList.add("active");
       	    var panel = artqt[i].parentElement.parentElement;
            panel.style.maxHeight = panel.scrollHeight + "px";
          }
        }
      }
      totaliser();
    }  
    </script>
    <script type="text/javascript" >
      reachBottom();
    </script>
    <script type="text/javascript">
      window.addEventListener("resize", function() {
        reachBottom();
      })
    </script>
    <script type="text/javascript">
      totaliser();
    </script>
  </body>
</html>
