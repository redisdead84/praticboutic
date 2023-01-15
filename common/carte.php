<?php

  session_start();

  if (empty($_SESSION['customer']) != 0)
  {
    header('LOCATION: 404.html');
    exit();
  }

  $customer = $_SESSION['customer'];
  $method = $_SESSION['method'];
  $table = $_SESSION['table'];

  require_once '../vendor/autoload.php';

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  include "config/common_cfg.php";
  include "param.php";

  $conn = new mysqli($servername, $username, $password, $bdd);
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);

  $reqci = $conn->prepare('SELECT customid, logo, nom FROM customer WHERE customer = ?');
  $reqci->bind_param("s", $customer);
  $reqci->execute();
  $reqci->bind_result($customid, $logo, $nom);
  $resultatci = $reqci->fetch();
  $reqci->close();

  if (strcmp($customid, "") == 0 )
  {
    header('LOCATION: 404.html');
    exit;
  }

  $mntcmdmini = GetValeurParam("MntCmdMini",$conn, $customid,"0");
  $sizeimg = GetValeurParam("SIZE_IMG",$conn, $customid,"bigimg");

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Prise de commande</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" media="screen" href="css/style2.css?v=<?php echo $ver_com_css;?>" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css?v=<?php echo $ver_com_css;?>">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="js/bandeau.js?v=2.01"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $_ENV['RECAPTCHA_KEY']; ?>"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="header">
      <a href="https://pratic-boutic.fr"><img id="mainlogo" src="img/logo-pratic-boutic.png"></a>
    </div>
    <div id="main" data-method="<?php echo $method;?>" data-table="<?php echo $table;?>" data-mntcmdmini="<?php echo $mntcmdmini;?>" data-customer="<?php echo $customer;?>">

    <img id="logo" src="../upload/<?php echo $logo;?>">
    <p id="marqueid" class="marque"><?php echo $nom;?></p>

    <form id="mainformid" name="mainform" autocomplete="off" method="post" action="valrecap.php">

    <?php
    //echo "Connected successfully";
    $query = 'SELECT catid, nom, visible FROM categorie WHERE customid = ' . $customid . ' OR catid = 0 ORDER BY catid';

		if ($result = $conn->query($query)) {
    		while ($row = $result->fetch_row()) {
    			if (($row[2] > 0 ) || ($row[0] == 0))
    			{
    			  if ($row[0] > 0)
    			  {
    			    echo '<button type="button" class="accordion">';
    				  echo html_entity_decode($row[1]);
    				  echo '</button>';
    				  echo '<div class="panel">';
    				}
    				else
    				{
    			    echo '<button type="button" class="accordion" style="display:none">';
    				  echo html_entity_decode($row[1]);
    				  echo '</button>';
   				    echo '<div class="panel" style="max-height:max-content">';

    				}
    				
		      	$query2 = 'SELECT artid, nom, prix, unite, description, image FROM article WHERE customid = ' . $customid . ' AND visible = 1 AND catid = ' . $row[0] . ' ORDER BY artid';
		      	if ($result2 = $conn->query($query2)) 
	  				{
	  				  while ($row2 = $result2->fetch_row()) 
    					{
    						if (strcmp($sizeimg,"bigimg")==0)
    						{
	    					  echo '<div class="artcel artcelb" id="artid' . $row2[0] . '" data-name="' . $row2[1] . '" data-prix="' . $row2[2] . '" data-unite="' . $row2[3] . '">';
	  	        		if (!empty($row2[5]))
 	            	  	echo '<img class="pic ' . $sizeimg . '" src="../upload/' . $row2[5] . '" alt = "nopic">';
	                echo '<div class="rowah">';
	                echo '<div class="colb1">';
	    					  echo '<div class="nom">';
	       	      	echo $row2[1];
	       	      	echo '<br />';
	       	      	echo '</div>';
	       	      	echo '<div class="desc">';
	       	      	if (!empty($row2[4]))
	       	      	{
	       	      	 echo $row2[4];
	                 echo '<br />'; 
	       	      	}
	       	      	echo '</div>';
	       	      	echo '</div>';
	       	      	echo '<div class="colb2">';
	       	      	echo '</div>';
	       	      	echo '</div>';
	       	      	echo '<div class="rowah">';
	       	      	echo '<div class="colb1">';
	       	      	if($method > 0) 
	       	      	{
	       	      		echo '<div class="vctrqte">';
						  		  echo '<p class="qte">Quantit&eacute;s :&nbsp;&nbsp;</p>';
						  		  $id = 'qt' . $row2[0];
						  		  $name = 'qty' . $row2[0];    
	            		  echo '<img class="bts bmoins" src="img/bouton-moins-inactif.png" onclick="subqt(this)" disabled />';
	            		  echo '<p class="artqt" id="' . $id . '" name="' . $name . '" onkeyup="showoptions(this)" onchange="showoptions(this)" > 0 </p>';
	            		  echo '<img class="bts bplus" src="img/bouton-plus.png" onclick="addqt(this)" />';
	            		  echo '</div>';
	              	}
	              	echo '</div>';
	              	echo '<div class="colb2">';
									echo '<p class="prix">';
	       	      	echo number_format($row2[2], 2, ',', ' ');
	       	      	echo ' ';
	       	      	echo $row2[3];
	       	      	echo '<br />';
	       	      	echo '</p>';
	       	      	echo '</div>';
	       	      	echo '</div>';
								}

    						else if (strcmp($sizeimg,"smallimg")==0)
    						{
	    					  echo '<div class="artcel artcelb" id="artid' . $row2[0] . '" data-name="' . $row2[1] . '" data-prix="' . $row2[2] . '" data-unite="' . $row2[3] . '">';
	    					  echo '<div class="rowah">';
	    					  echo '<div class="cola1">';
	              	echo '<div class="nom">';
	       	      	echo $row2[1];
	       	      	echo '<br />';
	       	      	echo '</div>';
	       	      	echo '<div class="desc">';
	       	      	if (!empty($row2[4]))
	       	      	{
	       	      	 echo $row2[4];
	                 echo '<br />'; 
	       	      	}
	       	      	echo '</div>';
	       	      	if($method > 0) 
	       	      	{
	       	      		echo '<div class="vctrqte">';
						  		  echo '<p class="qte">Quantit&eacute;s :&nbsp;&nbsp;</p>';
						  		  $id = 'qt' . $row2[0];
						  		  $name = 'qty' . $row2[0];    
	            		  //echo '<label> ';
	            		  echo '<img class="bts bmoins" src="img/bouton-moins-inactif.png" onclick="subqt(this)" disabled />';
	            		  //echo ' ';
	            		  echo '<p class="artqt" id="' . $id . '" name="' . $name . '" onkeyup="showoptions(this)" onchange="showoptions(this)" > 0 </p>';
	            		  //echo ' ';
	            		  echo '<img class="bts bplus" src="img/bouton-plus.png" onclick="addqt(this)" />';
	            		  //echo ' </label>';
	            		  echo '</div>';
	              	}

									echo '<div class="prixsm">';
	       	      	echo number_format($row2[2], 2, ',', ' ');
	       	      	echo ' ';
	       	      	echo $row2[3];
	       	      	echo '<br />';
	       	      	echo '</div>';
	       	      	echo '</div>';
	       	      	echo '<div class="cola2">';
	  	        		if (!empty($row2[5]))
 	            	  	echo '<img class="pic ' . $sizeimg . '" src="../upload/' . $row2[5] . '" alt = "nopic">';
	                echo '</div>';
	                echo '</div>';
								}

                echo '<textarea id="idtxta' . $row2[0] . '" name="txta' . $row2[0] . '" placeholder="Saisissez ici vos besoins spécifiques sur cet article" maxlength="300" hidden></textarea>';              
                
 				  		  $id = 'opt' . $row2[0];
 				  		  $name = 'opty' . $row2[0];    
				        
       	      	if($method > 0) 
       	      	{
  				        echo '<div class="divopt" id="' . $id . '" name="' . $name . '" hidden>';
  				        echo '<div class="slide" data-artid="' . $row2[0] . '" data-nom="' . html_entity_decode($row2[1]) . '" hidden></div>';
				          echo '<div class="divopt2" id="' . $id . '" name="' . $name . '" style="display:none">';
				        }
				        else 
				        {
  				        echo '<div class="divopt" id="' . $id . '" name="' . $name . '">';
  				        echo '<div class="slide" data-artid="' . $row2[0] . '" data-nom="' . html_entity_decode($row2[1]) . '" style="display:none"></div>';
				          echo '<div class="divopt2" id="' . $id . '" name="' . $name . '">';
				        }
				        
                $query3 = 'SELECT groupeopt.grpoptid, groupeopt.nom, groupeopt.multiple FROM relgrpoptart, groupeopt WHERE relgrpoptart.customid = ' . $customid . ' AND groupeopt.customid = ' . $customid . ' AND relgrpoptart.visible = 1 AND groupeopt.visible = 1 AND artid = ' . $row2[0] . ' AND relgrpoptart.grpoptid = groupeopt.grpoptid ORDER BY groupeopt.grpoptid';
  					    if ($result3 = $conn->query($query3)) 
	  				    {
    					    while ($row3 = $result3->fetch_row()) 
    					    {
    					    	echo '<div class="flexsp">';
  					      	if ($row3[2] == 0)
  					      	{
  					      		echo '<label>' . $row3[1] . ' (unique)</label><br>';
  					      		echo '<select class="selb" id="art' . $row2[0] . 'op' . $row3[0] . '" onchange="totaliser()">';
  					      	}
  					      	else if ($row3[2] == 1)
  					      	{
  					      		echo '<label>' . $row3[1] . ' (multiple)</label><br>';
  					        	echo '<select class="selb" id="art' . $row2[0] . 'op' . $row3[0] . '" onchange="totaliser()" multiple>';
 										}
    					      /*if ($row3[2] == 0)
                      echo '<legend>' . $row3[1] . ' (une seul option possible)</legend>';
                    else if ($row3[2] == 1)
                      echo '<legend>' . $row3[1] . ' (plusieurs choix possible)</legend>';*/
     					      $query4 = 'SELECT optid, nom, surcout FROM `option` WHERE customid = ' . $customid . ' AND visible = 1 AND grpoptid = ' . $row3[0] . ' ORDER BY optid';
       					    if ($result4 = $conn->query($query4)) 
         				    {
         				    	$init = 0;
         					    while ($row4 = $result4->fetch_row()) 
         					    {
         					    	if ($init == 0)
         					    		$def = 'selected';
         					    	else 
         					    		$def = '';
                        if ($row3[2] == 0)
                        {
                          if ($row4[2]>0) 
                           	echo '<option data-surcout="' . $row4[2] . '" class="" value="' . $row4[1] . '" ' . $def . ' id="art' . $row2[0] . 'opt' . $row4[0] . '">' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</option>';
                          else 
                            echo '<option data-surcout="' . $row4[2] . '" class="" value="' . $row4[1] . '" ' . $def . ' id="art' . $row2[0] . 'opt' . $row4[0] . '">' . $row4[1] . '</option>';
                        }
                        else if ($row3[2] == 1)
                        {
                          if ($row4[2]>0) 
                            echo '<option data-surcout="' . $row4[2] . '" class="" value="' . $row4[1] . '" onclick="totaliser()" id="art' . $row2[0] . 'opt' . $row4[0] . '">' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</option>';
                          else 
                            echo '<option data-surcout="' . $row4[2] . '" class="" value="' . $row4[1] . '" onclick="totaliser()" id="art' . $row2[0] . 'opt' . $row4[0] . '">' . $row4[1] . '</option>';
                        }

                        echo '<br/>';
                        $init++;
                      }
                    }
                    echo '</select>';
                    echo '</div>';
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
      ?>
        <input type="hidden" id="gRecaptchaResponse" name="gRecaptchaResponse">
      </form>
    </div>
    <div id="footer">
      <div class="grpbn">
        <input id="totaliseur" class="navindic" type="<?php echo ($method>0) ? 'button' : 'hidden'; ?>" value="Total" <?php echo ($method>0) ? "" : "disabled"; ?> >
        <input id="validcarte" class="navindic" type="<?php echo ($method>0) ? 'button' : 'hidden'; ?>" value="Poursuivre" <?php echo ($method>0) ? "" : "disabled"; ?> >
      </div>
    </div>
    <script type="text/javascript">
      window.onload=function()
      {
        var nom = '<?php echo $nom;?>';
        if (nom != "")
        {
          document.getElementById("logo").style.display = "block";
          document.getElementById("marqueid").style.display = "none";
        }
        else 
        {
          document.getElementById("logo").style.display = "none";
          document.getElementById("marqueid").style.display = "block";
        }
        var bouticid = '<?php echo $customid; ?>';
        var objcat = { bouticid: bouticid, nom:"categories"};
        
        fetch('frontquery.php', {
              method: "POST",
              body:objcat
             })
        .then((response) => response.json())
        .then((data) => {
          for (var dat of data)
          {
            if ((dat[2] > 0 ) || (dat[0] == 0))
            {
              var method = '<?php echo $method; ?>';
              var but = document.createElement("BUTTON");
              but.type = "button";
              but.innerHTML = dat[1];
              but.style.display = (dat[0] > 0) ? "block" : "none";
              document.getElementById("mainformid").appendChild(but);
              var divpan = document.createElement("DIV");
              divpan.id = "divpanid" + dat[0];
              divpan.classList.add("panel");
              divpan.style.max-height = (dat[0] > 0) ? "initial" : "max-content";
              document.getElementById("mainformid").appendChild(divpan);
              var objart = { bouticid: bouticid, nom:"articles", catid:data[0]};
              fetch('frontquery.php', {
                method: "POST",
                body:objart
               })
              .then((response) => response.json())
              .then((data) => {
                for (var dat of data)
                {
                  var sizeimg = '<?php echo $sizeimg;?>';
                  var divart = document.createElement("DIV");
                  if (sizeimg == "bigimg")
                  {
                    divart.id = "artid" + data[0];
                    divart.classList.add("artcel");
                    divart.classList.add("artcelb");
                    divart.setAttribute("data-name", data[1]);
                    divart.setAttribute("data-prix", data[2]);
                    divart.setAttribute("data-unite", data[3]);
                    if (data[5] !== "")
                    {
                      var imgb = document.createElement("IMG");
                      imgb.classList.add('pic');
                      imgb.classList.add(sizemig);
                      imgb.src = "../upload/" + data[5];
                      imgb.alt = "nopic";
                      divart.appendChild(imgb);
                    }
                    var rowah = document.createElement("DIV");
                    rowah.classList.add("rowah");
                    var col1b = document.createElement("DIV");
                    col1b.classList.add("col1b");
                    var nom = document.createElement("DIV");
                    nom.classList.add("nom");
                    nom.innerHTML = data[1];
                    nom.appendChild(document.createElement("BR"));
                    col1b.appendChild(nom);
                    rowah.appendChild(col1b);
                    var desc = document.createElement("DIV");
                    desc.classList.add("desc");
                    if (data[4] != "")
                    {
                      desc.innerHTML = data[4];
                      desc.appendChild(document.createElement("BR"));
                    }
                    rowah.appendChild(desc);
                    divart.appendChild(rowah);
                    var col2b = document.createElement("DIV");
                    col2b.classList.add("col2b");
                    divart.appendChild(col2b);
                    var rowah = document.createElement("DIV");
                    rowah.classList.add("rowah");
                    var col1b = document.createElement("DIV");
                    col1b.classList.add("col1b");
                    if (method > 0)
                    {
                      var vctrqte = document.createElement("DIV");
                      vctrqte.classList.add("vctrqte");
                      var qte = document.createElement("P");
                      qte.classList.add("qte");
                      qte.innerHTML = "Quantit&eacute;s :&nbsp;&nbsp;";
                      vctrqte.appendChild(qte);
                      var id = 'qt' + data[0];
                      var name = 'qty' + data[0];
                      var bmoins = document.createElement("IMG");
                      bmoins.classList.add('bts');
                      bmoins.classList.add('bmoins');
                      bmoins.src = "img/bouton-moins-inactif.png";
                      bmoins.onclick = function() {subqt(this);};
                      bmoins.disabled = true;
                      vctrqte.appendChild(bmoins);
                      var artqt = document.createElement("P");
                      artqt.classList.add("artqt");
                      artqt.id = id;
                      artqt.name = name;
                      artqt.onkeyup = function() {showoptions(this);};
                      artqt.onchange = function() {showoptions(this);};
                      artqt.innerHTML = " 0 ";
                      vctrqte.appendChild(artqt);
                      var bplus = document.createElement("IMG");
                      bplus.classList.add('bts');
                      bplus.classList.add('bplus');
                      bplus.src = "img/bouton-plus.png";
                      bplus.onclick = function() {addqt(this);};
                      vctrqte.appendChild(bplus);
                      divart.appendChild(vctrqte);
                    }
                    rowah.appendChild(col1b);
                    var col2b = document.createElement("DIV");
                    col2b.classList.add("col2b");
                    var prix = document.createElement("P");
                    prix.innerHTML = data[2].toFixed(2) + ' ' + data[3];
                    prix.appendChild(document.createElement("BR"));
                    col2b.appendChild(prix);
                    divart.appendChild(col2b);
                    divart.appendChild(rowah);
                  }
                  else if (sizeimg == "smallimg")
                  {
                    divart.id = "artid" + data[0];
                    divart.classList.add("artcel");
                    divart.classList.add("artcelb");
                    divart.setAttribute("data-name", data[1]);
                    divart.setAttribute("data-prix", data[2]);
                    divart.setAttribute("data-unite", data[3]);
                    var rowah = document.createElement("DIV");
                    rowah.classList.add("rowah");
                    var cola1 = document.createElement("DIV");
                    cola1.classList.add("cola1");
                    var nom = document.createElement("DIV");
                    nom.classList.add("nom");
                    nom.innerHTML = data[1];
                    nom.appendChild(document.createElement("BR"));
                    cola1.appendChild(nom);
                    var desc = document.createElement("DIV");
                    desc.classList.add("desc");
                    if (data[4] != "")
                    {
                      desc.innerHTML = data[4];
                      desc.appendChild(document.createElement("BR"));
                    }
                    cola1.appendChild(desc);
                    if (method > 0)
                    {
                      var vctrqte = document.createElement("DIV");
                      vctrqte.classList.add("vctrqte");
                      var qte = document.createElement("P");
                      qte.classList.add("qte");
                      qte.innerHTML = "Quantit&eacute;s :&nbsp;&nbsp;";
                      vctrqte.appendChild(qte);
                      var id = 'qt' + data[0];
                      var name = 'qty' + data[0];
                      var bmoins = document.createElement("IMG");
                      bmoins.classList.add('bts');
                      bmoins.classList.add('bmoins');
                      bmoins.src = "img/bouton-moins-inactif.png";
                      bmoins.onclick = function() {subqt(this);};
                      bmoins.disabled = true;
                      vctrqte.appendChild(bmoins);
                      var artqt = document.createElement("P");
                      artqt.classList.add("artqt");
                      artqt.id = id;
                      artqt.name = name;
                      artqt.onkeyup = function() {showoptions(this);};
                      artqt.onchange = function() {showoptions(this);};
                      artqt.innerHTML = " 0 ";
                      vctrqte.appendChild(artqt);
                      var bplus = document.createElement("IMG");
                      bplus.classList.add('bts');
                      bplus.classList.add('bplus');
                      bplus.src = "img/bouton-plus.png";
                      bplus.onclick = function() {addqt(this);};
                      vctrqte.appendChild(bplus);
                      divart.appendChild(vctrqte);
                    }
                    var prixsm = document.createElement("DIV");
                    prixsm.classList.add("prixsm");
                    prixsm.innerHTML = data[2].toFixed(2) + ' ' + data[3];
                    prixsm.appendChild(document.createElement("BR"));
                    cola1.appendChild(prixsm);
                    rowah.appendChild(cola1);
                    var cola2 = document.createElement("DIV");
                    cola2.classList.add("cola2");
                    if (data[5] !== "")
                    {
                      var imgb = document.createElement("IMG");
                      imgb.classList.add('pic');
                      imgb.classList.add(sizemig);
                      imgb.src = "../upload/" + data[5];
                      imgb.alt = "nopic";
                      cola2.appendChild(imgb);
                    }
                    rowah.appendChild(cola2);
                    divart.appendChild(rowah);
                  }
                  var txta = document.createElement("TEXTAREA");
                  txta.id = 'idtxta' + data[0];
                  txta.name = 'txta' + data[0];
                  txta.placeholder = "Saisissez ici vos besoins spécifiques sur cet article";
                  txta.maxlength = "300";
                  txta.hidden = true;
                  divart.appendChild(txta);
                  document.getElementById("divpanid" + body.catid).appendChild(divart);
                  var divopt = document.createElement("DIV");
                  divopt.classList.add("divopt");
                  divopt.id = id;
                  divopt.name = name;
                  divopt.style.display = (method > 0) ? "none" : "block";
                  divart.appendChild(divopt);
                  var slide = document.createElement("DIV");
                  slide.classList.add("slide");
                  slide.setAttribute("data-artid", data[0]);
                  slide.setAttribute("data-nom", data[1]);
                  slide.style.display = (method > 0) ? "none" : "block";
                  divart.appendChild(slide);
                  var divopt2 = document.createElement("DIV");
                  divopt2.classList.add("divopt2");
                  divopt2.id = id;
                  divopt2.name = name;
                  divopt2.hidden = (method > 0) ? "none" : "block";
                  divart.appendChild(divopt2);
                  var objgrp = { bouticid: bouticid, nom:"groupesoptions", artid:data[0]};
                  fetch('frontquery.php', {
                    method: "POST",
                    body:objgrp
                  })
                  .then((response) => response.json())
                  .then((data) => {
                    for (var dat of data)
                    {
                      var flexsp = document.createElement("DIV");
                      flexsp.classList.add("flexsp");
                      var lbl = document.createElement("LABEL");
                      lbl.innerHTML = data[1] + (data[2] == 0) ? "(unique)" : "(multiple)";
                      lbl.appendChild(document.createElement("BR"));
                      flexsp.appendChild(lbl);
                      var selb = document.createElement("SELECT");
                      selb.classList.add("selb");
                      selb.id = "art" + data[0] + "op" + data[0];
                      selb.onchange = function () {totaliser();};
                      selb.mpultiple = (data[2] == 1);
                      flexsp.appendChild(selb);
                      var objopt = { bouticid: bouticid, nom:"options", grpoptid:data[0]};
                      fetch('frontquery.php', {
                        method: "POST",
                        body:objopt
                      })
                      .then((response) => response.json())
                      .then((data) => {
                        for (var dat of data)
                        {
                          init = 0;
                          if (init == 0)
                            def = 'selected';
                          else
                            def = '';
                          if (dat[2] == 0)
                          {
                            if (dat[2]>0)
                            {
                              var option = document.createElement("OPTION");
                              option.setAttribute("data-surcout", dat[2]);
                              option.value = dat[1];
                              option.selected = ((init == 0) && (dat[2]>0));
                              option.id = "art" + dat[0] + "opt" + dat[0];
                            }
                             	echo '<option data-surcout="' . $row4[2] . '" class="" value="' . $row4[1] . '" ' . $def . ' id="art' . $row2[0] . 'opt' . $row4[0] . '">' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</option>';
                            else 
                              echo '<option data-surcout="' . $row4[2] . '" class="" value="' . $row4[1] . '" ' . $def . ' id="art' . $row2[0] . 'opt' . $row4[0] . '">' . $row4[1] . '</option>';
                          }
                          else if ($row3[2] == 1)
                          {
                            if ($row4[2]>0) 
                              echo '<option data-surcout="' . $row4[2] . '" class="" value="' . $row4[1] . '" onclick="totaliser()" id="art' . $row2[0] . 'opt' . $row4[0] . '">' . $row4[1] . ' + ' . number_format($row4[2], 2, ',', ' ') . ' € ' . '</option>';
                            else 
                              echo '<option data-surcout="' . $row4[2] . '" class="" value="' . $row4[1] . '" onclick="totaliser()" id="art' . $row2[0] . 'opt' . $row4[0] . '">' . $row4[1] . '</option>';
                          }

                          echo '<br/>';
                          $init++;

                        }
                      })
                    }
                  })
                  .catch((error) => console.error(error));
                }
              .catch((error) => console.error(error));
             })
            .catch((error) => console.error(error));
            }
          }
        })
        .catch((error) => console.error(error));

        reachBottom();

        var artcel = document.getElementsByClassName("artcel");
        var artqt = document.getElementsByClassName("artqt");
  
        for (var i = 0; i<artqt.length; i++ )
        {
          bakqt = sessionStorage.getItem(artqt[i].id);
          if (bakqt !== null)
          {
            artqt[i].innerHTML = " " + bakqt + " "; 
            if ((parseInt(artqt[i].innerText) > 0) && (artqt[i].hidden !== true))
            {
              showoptions(artqt[i]);
              artqt[i].previousElementSibling.disabled = false;
              artqt[i].previousElementSibling.src = 'img/bouton-moins.png';
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
  	          				if (sefld[l].tagName == "DIV")
  	          				{ 
  		          				var chsefld = sefld[l].children;
  		            			if (chsefld[2].tagName == "SELECT") 
  		            			{
  			            			var secase = chsefld[2].children;                	
  		                    for (m=0; m<secase.length; m++) 
  		                    {
  		                      if (secase[m].tagName == "OPTION") 
  		                      {
  	                          if (sessionStorage.getItem(secase[m].id) == 1)
  	                            secase[m].selected = true;
  	                          else 
  	                            secase[m].selected = false;
  		                      }
  		                    }
  	                    }
                    	}
                    }              
                  }         
                }
              }
         	    artqt[i].parentElement.parentElement.parentElement.parentElement.parentElement.previousElementSibling.classList.add("active");
         	    var panel = artqt[i].parentElement.parentElement.parentElement.parentElement.parentElement;
              panel.style.maxHeight = panel.scrollHeight + "px";
            }
          }
        }
   
       
        totaliser();
      }  
    </script>
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
          qtc = parseInt(artqt[i].innerText); 
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
          				if (sefld[il].tagName == "DIV")
          				{ 
	          				var chsefld = sefld[il].children;
	            			if (chsefld[2].tagName == "SELECT") 
	            			{
		            			var secase = chsefld[2].children;                	
		                  for (im=0; im<secase.length; im++) 
		                  {
	  	                  if (secase[im].tagName == "OPTION") 
	    	                {
	      	                if (secase[im].selected == true)
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
          }
        }
        document.getElementById("totaliseur").value = "Total : " + somme.toFixed(2) + " €";
        sessionStorage.setItem("sstotal", somme.toFixed(2));      	
      }
    </script>

    <script type="text/javascript">
      function addqt(elem)
      {
        elem.previousElementSibling.innerHTML = " " + (parseInt(elem.previousElementSibling.innerText) + 1) + " ";
        showoptions(elem.previousElementSibling);
        if (parseInt(elem.previousElementSibling.innerText) > 0)
        {
          elem.previousElementSibling.previousElementSibling.disabled = false;
          elem.previousElementSibling.previousElementSibling.src = 'img/bouton-moins.png';
        }
        totaliser();

      }
      function subqt(elem)
      {
        if (parseInt(elem.nextElementSibling.innerText) > 0)
        {
          elem.nextElementSibling.innerHTML = " " + (parseInt(elem.nextElementSibling.innerText) - 1) + " ";
          showoptions(elem.nextElementSibling);
          if (parseInt(elem.nextElementSibling.innerText) == 0)
          {
            elem.disabled = true;
            elem.src = 'img/bouton-moins-inactif.png';
          }
        }
        totaliser();
      }
    </script>    
    
    <script type="text/javascript">
    document.getElementById("validcarte").addEventListener("click", function(e)
    {
      e.preventDefault();
      grecaptcha.ready(function() {
        var key = '<?php echo $_ENV['RECAPTCHA_KEY']; ?>';
        grecaptcha.execute(key, {action: 'submit'}).then(function(token) {
          var somme =0;
          var failed = false;
          var opt = [];
          var mntcmdmini = document.getElementById("main").getAttribute("data-mntcmdmini");
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
              if (artqt[i].hidden !== true )
                sessionStorage.setItem(artqt[i].id, artqt[i].innerText);
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
              				if (sefld[l].tagName == "DIV")
              				{ 
    	                  var alfa = true;
    	          				var chsefld = sefld[l].children;
    	            			if (chsefld[2].tagName == "SELECT") 
    	            			{
    		            			var secase = chsefld[2].children;                	
    		                  for (m=0; m<secase.length; m++) 
    		                  {
    		                    if (secase[m].tagName == "OPTION") 
    		                    {
    		                      if (chsefld[2].multiple == false)
    		                      {
    		                        if (secase[m].selected == true)
    		                        {
    		                          options = options + " / " + secase[m].value;
    		                          alfa = false;
    		                          sessionStorage.setItem(secase[m].id, 1);
    		                        }
    		                        else
    		                        	sessionStorage.setItem(secase[m].id, 0);
    		                      }
    		                      if (chsefld[2].multiple == true)
    		                      {
    		                        alfa = false;
    		                        if (secase[m].selected == true)
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
              qtc = artqt[i].innerText; 
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
              				if (sefld[il].tagName == "DIV")
              				{ 
    	          				var chsefld = sefld[il].children;
    	            			if (chsefld[2].tagName == "SELECT") 
    	            			{
    		            			var secase = chsefld[2].children;                	
    	                  	for (im=0; im<secase.length; im++) 
      	                	{
        	                	if (secase[im].tagName == "OPTION") 
          	              	{
            	              	if (secase[im].selected == true)
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
          
          if ((somme < mntcmdmini) && (failed == false)) {
            alert("La commmande doit être au moins de " + parseFloat(mntcmdmini).toFixed(2) + " € or la commande est de " + parseFloat(somme).toFixed(2) + " €");
            failed = true;
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
          {
            document.forms["mainform"].elements.namedItem("gRecaptchaResponse").value = token;
            document.forms["mainform"].submit();
          }
        });
      });
    });
    </script>
    <script type="text/javascript" >
      function showoptions(eleminp) 
      {
        var fart = eleminp.parentElement.parentElement.parentElement.parentElement.getElementsByTagName("TEXTAREA")[0];
        
        if (parseInt(eleminp.innerText) > 0)
          fart.hidden = false;
        else
        	fart.hidden = true;
        
        eleminp.blur();
        var elemopt = eleminp.parentElement.parentElement.parentElement.parentElement.getElementsByClassName("divopt")[0];
       
        var slide = elemopt.getElementsByClassName("slide")[0]; 
        
        slide.innerHTML = "";        
        var cur = 1;
        var nbtab = parseInt(eleminp.innerText);
        
      	var nom = slide.getAttribute("data-nom");
      	var artid = slide.getAttribute("data-artid");
      	if (nbtab == 0)
          sessionStorage.removeItem("slidepos" + artid);
        if (nbtab == 1)
          sessionStorage.setItem("slidepos" + artid, 1); 
      	if (nbtab > 1)
          cur = sessionStorage.getItem("slidepos" + artid);
      	
      	var lbl = document.createElement("P");
      	lbl.innerHTML = nom + "&nbsp;numéro&nbsp;";
      	lbl.classList.add("sli"); 
        slide.appendChild(lbl);
      	var inputg = document.createElement("IMG");
      	inputg.id = "art"+ artid + "fg";
      	inputg.classList.add("arrow");
      	inputg.classList.add("bts"); 
      	//inputg.type ="button";
      	inputg.src = "img/left-arrow.png";
        inputg.onclick = function() {setart(this, -1)};
        if (cur == 1)
        {
          inputg.style.pointerEvents = "none";
          inputg.style.opacity = 0.5;
        }
        slide.appendChild(inputg);
        var cura = document.createElement("P");
        cura.innerHTML = cur;
        cura.classList.add("curarticle");
        cura.classList.add("sli");
        cura.classList.add("cursor");
        slide.appendChild(cura);
      	var inputd = document.createElement("IMG");
      	inputd.id = "art"+ artid + "fd";
      	inputd.classList.add("arrow"); 
      	inputd.classList.add("bts");
      	//inputd.type ="button";
      	inputd.src = "img/right-arrow.png";
        inputd.onclick = function() {setart(this, 1)};
        if (nbtab == cur)
        {
          inputd.style.pointerEvents = "none";
          inputd.style.opacity = 0.5;
        }
        slide.appendChild(inputd);
        var lbl2 = document.createElement("P");
        lbl2.innerHTML = "&nbsp;/&nbsp;";
        lbl2.classList.add("sli");
        slide.appendChild(lbl2);     
        var totala = document.createElement("P");
        totala.innerHTML = nbtab;
        totala.classList.add("totarticle");
        totala.classList.add("sli");
        slide.appendChild(totala);
                
        var etodel = elemopt.getElementsByClassName("divopttab");
       
        while (etodel.length > parseInt(eleminp.innerText)) // modif here replaced 0 by eleminp.value
        {
          if (cur > parseInt(eleminp.innerText))
          {
            cur = cur - 1;
            setart(inputg, -1);
            inputd.style.pointerEvents = "none";
            inputd.style.opacity = 0.5;
          }
          etodel[parseInt(eleminp.innerText)].remove();     // here too
          for (var i=0; i<etodel.length; i++) 
          {
            if (i == (cur - 1))
              etodel[cur - 1].style.display = "flex";
            else
            	etodel[i].style.display = "none";
          }       
        }

        var etodup = elemopt.getElementsByClassName("divopt2")[0];
        
        if (etodup.innerHTML == "")
          slide.style.display = "none";
        else
        	slide.style.display = "flex";
                
        while ((elemopt.childElementCount - 2) < parseInt(eleminp.innerText))
        {
          var edup = etodup.cloneNode(true);
          
          edup.style.display = "none";
          
          if ((elemopt.childElementCount - 2) == (cur - 1))
            edup.style.display = "flex";

          edup.setAttribute("class","divopttab");
          edup.setAttribute("data-numero", elemopt.childElementCount - 2);
                    
          var sefld = edup.children;
          
          for (k=0; k<sefld.length; k++) 
          {
          	if (sefld[k].tagName == "DIV")
          	{ 
	          	var chsefld = sefld[k].children;
	            if (chsefld[2].tagName == "SELECT") 
	            {
		            var secase = chsefld[2].children;
		            var cnt=0;
		            for (l=0; l<secase.length; l++) 
		            {
		              if (secase[l].tagName == "OPTION") 
		              {
		                //secase[l].name = "art" + artid + "num" + k + "case" + l;
		                secase[l].id = "art" + artid + "num" + (elemopt.childElementCount - 2) + secase[l].id;
		                cnt = cnt + 1;
		              }
								}
								chsefld[2].size = cnt;   
								        
	            }
            }
          }         
          elemopt.appendChild(edup);
        }

        eleminp.parentElement.parentElement.parentElement.parentElement.parentElement.previousElementSibling.classList.add("active");        
        
      	var panel = eleminp.parentElement.parentElement.parentElement.parentElement.parentElement;
        if (parseInt(eleminp.innerText) > 0)
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
            listtab[j].style.display = "flex";
          else {
          	listtab[j].style.display = "none";
          }
        }
        var padg = elemopt.children[0].children[1];
        var padd = elemopt.children[0].children[3];

        padg.style.pointerEvents = "auto";
        padg.style.opacity = 1;
        padd.style.pointerEvents = "auto";
        padd.style.opacity = 1;
        
        if (valdef == 1)
        {
          padg.style.pointerEvents = "none";
          padg.style.opacity = 0.5;
        }
        
        if (valdef == listtab.length)
        {        
          padd.style.pointerEvents = "none";
          padd.style.opacity = 0.5;
        }

      }
    </script>
    <script type="text/javascript">
      function reachBottom() 
      {
        var x = window.innerHeight - document.getElementById("footer").clientHeight - document.getElementById("header").clientHeight;
        x = x + "px";
        document.getElementById("main").style.height = x;
      }
    </script>
    <!-- TODO further see if this script should be removed -->
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
      	    this.parentElement.parentElement.parentElement.parentElement.parentElement.previousElementSibling.classList.add("active");
      	    var panel = this.parentElement.parentElement.parentElement.parentElement.parentElement;
            panel.style.maxHeight = panel.scrollHeight + "px";
          });
        }
    </script>
    <!-- TODO This script should be removed parce que on ne supporte plus la lécture seul -->
    <script type="text/javascript" >
      reachBottom();
      var sle = document.getElementsByTagName("SELECT");
      for (var i = 0; i<sle.length; i++) 
      {
				sle[i].size = sle[i].length;
				if (document.getElementById("main").getAttribute("data-method") == 0)
				{
					sle[i].selectedIndex = "-1";
					sle[i].disabled = true;
				}
      }
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
