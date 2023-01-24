<?php
  require_once '../vendor/autoload.php';

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  include "config/common_cfg.php";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
  </head>
  <script type="text/javascript" >
    var customer;
    var bouticid;
    var logo;
    var nom;
    var mntcmdmini;
    var sizeimg;
    var method;
    
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
    async function getBouticInfo(customer)
    {
      var objboutic = { requete: "getBouticInfo", customer: customer};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objboutic)
      });
      if (!response.ok) {
        throw new Error(`Error! status: ${response.status}`);
      }
      const data = await response.json();
      bouticid = data[0];
      logo = data[1];
      nom = data[2];
    }
    
    async function getParam(bouticid, param)
    {
      var objparam = { action: "getparam", table: "parametre", bouticid: bouticid, param: param};
      const response = await fetch('customerarea/boquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objparam)
      });
      if (!response.ok) {
        throw new Error(`Error! status: ${response.status}`);
      }
      const data = await response.json();
      return data[0];
    }
  
    async function getCategories(method, bouticid)
    {
      
      var objcat = { bouticid: bouticid, requete:"categories"};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objcat)
      });
      if (!response.ok) {
        throw new Error(`Error! status: ${response.status}`);
      }
      const data = await response.json();

      for (var dat of data)
      {
        if ((dat[2] > 0 ) || (dat[0] == 0))
        {
          var but = document.createElement("BUTTON");
          but.type = "button";
          but.classList.add("accordion");
          but.innerHTML = dat[1];
          but.style.display = (dat[0] > 0) ? "block" : "none";
          document.getElementById("mainformid").appendChild(but);
          var divpan = document.createElement("DIV");
          divpan.id = "divpanid" + dat[0];
          divpan.classList.add("panel");
          divpan.style.maxHeight = (dat[0] > 0) ? "initial" : "max-content";
          document.getElementById("mainformid").appendChild(divpan);
          const catid = dat[0];
          await getArticles(method, bouticid, catid);
            
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
                artqt[i].parentElement.parentElement.parentElement.parentElement.parentElement.previousElementSibling.classList.add("activepb");
                var panel = artqt[i].parentElement.parentElement.parentElement.parentElement.parentElement;
                panel.style.maxHeight = "initial"; /*panel.scrollHeight + "px";*/
              }
            }
          }
          totaliser();
          var aqt = document.getElementsByClassName("artqt");
          var i;
    
          for (i = 0; i < aqt.length; i++) 
          {
            aqt[i].addEventListener("focus", function() {
        	    this.parentElement.parentElement.parentElement.parentElement.parentElement.previousElementSibling.classList.add("activepb");
        	    var panel = this.parentElement.parentElement.parentElement.parentElement.parentElement;
              panel.style.maxHeight = panel.scrollHeight + "px";
            });
          }
        }
      }
    }

    async function getArticles(method, bouticid, catid)
    {
      var objart = { bouticid: bouticid, requete:"articles", catid:catid};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objart)
      });
      const data = await response.json();
      for (var dat of data)
      {
        const artid = dat[0];
        var divart = document.createElement("DIV");
        if (sizeimg == "bigimg")
        {
          divart.id = "artid" + artid;
          divart.classList.add("artcel");
          divart.classList.add("artcelb");
          divart.setAttribute("data-name", dat[1]);
          divart.setAttribute("data-prix", dat[2]);
          divart.setAttribute("data-unite", dat[3]);
          document.getElementById("divpanid" + catid).appendChild(divart);
          var divcarou = document.createElement("DIV");
          divcarou.id = "carousel" + artid;
          divcarou.classList.add('pic');
          divcarou.classList.add(sizeimg);
          divcarou.classList.add('carousel');
          divcarou.classList.add('slide');
          divcarou.setAttribute("data-ride", "carousel");
          divcarou.setAttribute("data-interval", false);
          divcarou.style.display = "none";
          document.getElementById("artid" + artid).appendChild(divcarou)
          var divcarin = document.createElement("DIV");
          divcarin.id = 'carinid' + artid;
          divcarin.classList.add('carousel-inner');
          document.getElementById("carousel" + artid).appendChild(divcarin);
          await getImages(method, bouticid, artid);
          var ccp = document.createElement("A");
          ccp.classList.add("carousel-control-prev");
          ccp.href = "#carousel" + artid;
          ccp.role = "button";
          ccp.setAttribute("data-slide", "prev")
          var ccpi = document.createElement("SPAN");
          ccpi.classList.add("carousel-control-prev-icon");
          ccpi.setAttribute("aria-hidden", "true");
          ccp.appendChild(ccpi);
          var prev = document.createElement("SPAN");
          prev.classList.add("sr-only");
          prev.innerHTML = "Previous";
          ccp.appendChild(prev);
          document.getElementById('carinid' + artid).appendChild(ccp);
          var ccn = document.createElement("A");
          ccn.classList.add("carousel-control-next");
          ccn.href = "#carousel" + artid;
          ccn.role = "button";
          ccn.setAttribute("data-slide", "next")
          var ccni = document.createElement("SPAN");
          ccni.classList.add("carousel-control-next-icon");
          ccni.setAttribute("aria-hidden", "true");
          ccn.appendChild(ccni);
          var next = document.createElement("SPAN");
          next.classList.add("sr-only");
          next.innerHTML = "Next";
          ccn.appendChild(next);
          document.getElementById('carinid' + artid).appendChild(ccn);
          var rowah = document.createElement("DIV");
          rowah.classList.add("rowah");
          var colb1 = document.createElement("DIV");
          colb1.classList.add("colb1");
          var nom = document.createElement("DIV");
          nom.classList.add("nom");
          nom.innerHTML = dat[1];
          nom.appendChild(document.createElement("BR"));
          colb1.appendChild(nom);
          var desc = document.createElement("DIV");
          desc.classList.add("desc");
          if (dat[4] != "")
          {
            desc.innerHTML = dat[4];
            desc.appendChild(document.createElement("BR"));
          }
          colb1.appendChild(desc);
          rowah.appendChild(colb1);
          var colb2 = document.createElement("DIV");
          colb2.classList.add("colb2");
          rowah.appendChild(colb2);
          divart.appendChild(rowah);
          var rowah2 = document.createElement("DIV");
          rowah2.classList.add("rowah");
          rowah2.id = "rowah" + artid;
          var colb1 = document.createElement("DIV");
          colb1.classList.add("colb1");
          if (method > 0)
          {
            var vctrqte = document.createElement("DIV");
            vctrqte.classList.add("vctrqte");
            var qte = document.createElement("P");
            qte.classList.add("qte");
            qte.innerHTML = "Quantit&eacute;s :&nbsp;&nbsp;";
            vctrqte.appendChild(qte);
            var id = 'qt' + dat[0];
            var name = 'qty' + dat[0];
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
            colb1.appendChild(vctrqte);
          }
          rowah2.appendChild(colb1);
          var colb2 = document.createElement("DIV");
          colb2.classList.add("colb2");
          var prix = document.createElement("P");
          prix.classList.add("prix");
          prix.innerHTML = parseFloat(dat[2]).toFixed(2) + ' ' + dat[3];
          prix.appendChild(document.createElement("BR"));
          colb2.appendChild(prix);
          rowah2.appendChild(colb2);
          divart.appendChild(rowah2);
        }
        else if (sizeimg == "smallimg")
        {
          divart.id = "artid" + dat[0];
          divart.classList.add("artcel");
          divart.classList.add("artcelb");
          divart.setAttribute("data-name", dat[1]);
          divart.setAttribute("data-prix", dat[2]);
          divart.setAttribute("data-unite", dat[3]);
          document.getElementById("divpanid" + catid).appendChild(divart);
          var rowah = document.createElement("DIV");
          rowah.classList.add("rowah");
          var cola1 = document.createElement("DIV");
          cola1.classList.add("cola1");
          var nom = document.createElement("DIV");
          nom.classList.add("nom");
          nom.innerHTML = dat[1];
          nom.appendChild(document.createElement("BR"));
          cola1.appendChild(nom);
          var desc = document.createElement("DIV");
          desc.classList.add("desc");
          if (dat[4] != "")
          {
            desc.innerHTML = dat[4];
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
            var id = 'qt' + dat[0];
            var nameqt = 'qty' + dat[0];
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
            artqt.name = nameqt;
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
            cola1.appendChild(vctrqte);
          }
          var prixsm = document.createElement("DIV");
          prixsm.classList.add("prixsm");
          prixsm.innerHTML = parseFloat(dat[2]).toFixed(2) + ' ' + dat[3];
          prixsm.appendChild(document.createElement("BR"));
          cola1.appendChild(prixsm);
          rowah.appendChild(cola1);
          var cola2 = document.createElement("DIV");
          cola2.classList.add("cola2");
          var divcarou = document.createElement("DIV");
          divcarou.id = "carousel" + artid;
          divcarou.classList.add('pic');
          divcarou.classList.add(sizeimg);
          divcarou.classList.add('carousel');
          divcarou.classList.add('slide');
          divcarou.setAttribute("data-ride", "carousel");
          divcarou.setAttribute("data-interval", false);
          divcarou.style.display = "none";
          cola2.appendChild(divcarou);
          rowah.appendChild(cola2);
          document.getElementById("artid" + artid).appendChild(rowah);
          var divcarin = document.createElement("DIV");
          divcarin.id = 'carinid' + artid;
          divcarin.classList.add('carousel-inner');
          document.getElementById("carousel" + artid).appendChild(divcarin);
          await getImages(method, bouticid, artid);
          var ccp = document.createElement("A");
          ccp.classList.add("carousel-control-prev");
          ccp.href = "#carousel" + artid;
          ccp.role = "button";
          ccp.setAttribute("data-slide", "prev")
          var ccpi = document.createElement("SPAN");
          ccpi.classList.add("carousel-control-prev-icon");
          ccpi.setAttribute("aria-hidden", "true");
          ccp.appendChild(ccpi);
          var prev = document.createElement("SPAN");
          prev.classList.add("sr-only");
          prev.innerHTML = "Previous";
          ccp.appendChild(prev);
          document.getElementById('carinid' + artid).appendChild(ccp);
          var ccn = document.createElement("A");
          ccn.classList.add("carousel-control-next");
          ccn.href = "#carousel" + artid;
          ccn.role = "button";
          ccn.setAttribute("data-slide", "next")
          var ccni = document.createElement("SPAN");
          ccni.classList.add("carousel-control-next-icon");
          ccni.setAttribute("aria-hidden", "true");
          ccn.appendChild(ccni);
          var next = document.createElement("SPAN");
          next.classList.add("sr-only");
          next.innerHTML = "Next";
          ccn.appendChild(next);
          document.getElementById('carinid' + artid).appendChild(ccn);
        }
        var txta = document.createElement("TEXTAREA");
        txta.id = 'idtxta' + dat[0];
        txta.name = 'txta' + dat[0];
        txta.placeholder = "Saisissez ici vos besoins spécifiques sur cet article";
        txta.maxlength = "300";
        txta.hidden = true;
        divart.appendChild(txta);
        const ido = 'opt' + dat[0];
        const namo = 'opty' + dat[0];
        var divopt = document.createElement("DIV");
        divopt.classList.add("divopt");
        divopt.id = ido;
        divopt.setAttribute('name', namo);
        divopt.style.display = (method > 0) ? "none" : "block";
        divart.appendChild(divopt);
        var slide = document.createElement("DIV");
        slide.classList.add("slidepb");
        slide.setAttribute("data-artid", dat[0]);
        slide.setAttribute("data-nom", dat[1]);
        slide.style.display = (method > 0) ? "flex" : "none";
        divopt.appendChild(slide);
        var divopt2 = document.createElement("DIV");
        divopt2.classList.add("divopt2");
        divopt2.id = ido;
        divopt2.setAttribute('name', namo);
        divopt2.style.display = (method > 0) ? "none" : "block";
        divopt.appendChild(divopt2);
        document.getElementById("divpanid" + catid).appendChild(divart);
        await getGroupes(method, bouticid, artid);
      }
    }

    async function getGroupes(method, bouticid, artid)
    {
      var objgrp = { bouticid: bouticid, requete:"groupesoptions", artid:artid};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objgrp)
      });
      const data = await response.json();
      for (var dat of data)
      {
        var flexsp = document.createElement("DIV");
        flexsp.classList.add("flexsp");
        var lbl = document.createElement("LABEL");
        lbl.innerHTML = dat[1] + ((dat[2] == 0) ? " (unique)" : " (multiple)");
        flexsp.appendChild(lbl);
        flexsp.appendChild(document.createElement("BR"));
        var selb = document.createElement("SELECT");
        selb.classList.add("selb");
        selb.id = "art" + artid + "op" + dat[0];
        selb.mpultiple = (dat[2] == 1);
        flexsp.appendChild(selb);
        document.querySelector('#opt' + artid + " .divopt2").appendChild(flexsp);
        document.getElementById("art" + artid + "op" + dat[0]).setAttribute('onchange', 'totaliser()');
        const grpoptid = dat[0];
        await getOptions(method, bouticid, artid, grpoptid);
      }
    }

    async function getOptions(method, bouticid, artid, grpoptid)
    {
      var objopt = { bouticid: bouticid, requete:"options", grpoptid:grpoptid};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objopt)
      });
      const data = await response.json();
      for (var dat of data)
      {
        var init = 0;
        var option = document.createElement("OPTION");
        option.setAttribute("data-surcout", dat[2]);
        option.value = dat[1];
        option.selected = ((init == 0) && (dat[2]>0));
        option.id = "art" + artid + "opt" + dat[0];
        option.innerHTML = (dat[2]>0) ? dat[1] + ' + ' + parseFloat(dat[2]).toFixed(2) + ' € ' : dat[1];
        document.getElementById("art" + artid + "op" + grpoptid).appendChild(option);
        init++;
      }
    }

    async function getImages(method, bouticid, artid)
    {
      var objimg = { bouticid: bouticid, requete:"images", artid:artid};
      const response = await fetch('frontquery.php', {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body:JSON.stringify(objimg)
      });
      const data = await response.json();
      var first = false;
      for (var dat of data)
      {
        if ((dat[0] != null) && (dat[0] != null))
        {
          var caritem = document.createElement("DIV");
          caritem.classList.add('carousel-item');
          if (!first)
          {
            caritem.classList.add('active');
            first = true;
            document.getElementById("carousel" + artid).style.display = "block";
          }
          var imgitem = document.createElement("IMG");
          imgitem.classList.add("pic");
          imgitem.classList.add(sizeimg);
          imgitem.src = '../upload/' + dat[0];
          imgitem.alt = "nopic";
          //imgitem.onload = function() {
            /*if (sizeimg == "bigimg")
              this.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.maxHeight = this.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.scrollHeight + "px";
            else if (sizeimg == "smallimg")
              this.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.style.maxHeight = this.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.scrollHeight + "px";*/
          //};
          caritem.appendChild(imgitem);
        }
        document.getElementById('carinid' + artid).appendChild(caritem);
      }
    }

    window.onload = async function()
    {
      
      customer = sessionStorage.getItem('customer');
      method = sessionStorage.getItem('method');
      if (!customer)
        document.location.href = 'error.php?code=nocustomer';
      await getBouticInfo(customer);
      if (!bouticid)
        document.location.href = 'error.php?code=nobouticid';
      
      document.getElementById("logo").src = "../upload/" + logo;
      document.getElementById("marqueid").innerHTML = nom;
      
      document.getElementById("totaliseur").type = (method>0) ? 'button' : 'hidden';
      document.getElementById("validcarte").type = (method>0) ? 'button' : 'hidden';
      document.getElementById("totaliseur").disabled = (method<=0);
      document.getElementById("validcarte").disabled = (method<=0);
        
      mntcmdmini = await getParam(bouticid, "MntCmdMini");
      sizeimg = await getParam(bouticid, "SIZE_IMG");

      if (logo)
      {
        document.getElementById("logo").style.display = "block";
        document.getElementById("marqueid").style.display = "none";
      }
      else 
      {
        document.getElementById("logo").style.display = "none";
        document.getElementById("marqueid").style.display = "block";
      }
      
      await getCategories(method, bouticid);
      
      var acc = document.getElementsByClassName("accordion");
      var i;

      for (i = 0; i < acc.length; i++) 
      {
        acc[i].addEventListener("click", function() {
          this.classList.toggle("activepb");
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
      
      document.getElementById("loadid").style.display = "none";
      document.getElementById("header").style.display = "block";
      document.getElementById("main").style.display = "block";
      document.getElementById("footer").style.display = "block";
      reachBottom();
    }
  </script>

  <body ondragstart="return false;" ondrop="return false;">
    <div id="loadid" class="flcentered">
      <div class="spinner-border nospmd" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    <div id="header" style="display:none">
      <a href="https://pratic-boutic.fr"><img id="mainlogo" src="img/logo-pratic-boutic.png"></a>
    </div>
    <div id="main" style="display:none">
      <img id="logo">
      <p id="marqueid" class="marque"></p>
      <form id="mainformid" name="mainform" autocomplete="off" method="post" action="valrecap.php">
        <input type="hidden" id="gRecaptchaResponse" name="gRecaptchaResponse">
      </form>
    </div>
    <div id="footer" style="display:none">
      <div class="grpbn">
        <input id="totaliseur" class="navindic" value="Total">
        <input id="validcarte" class="navindic" value="Poursuivre">
      </div>
    </div>
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
          if (method > 0)
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
       
        var slide = elemopt.getElementsByClassName("slidepb")[0]; 
        
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

        eleminp.parentElement.parentElement.parentElement.parentElement.parentElement.previousElementSibling.classList.add("activepb");        
        
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
