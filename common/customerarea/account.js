

document.addEventListener('DOMContentLoaded', async () => {
  var login = document.getElementById("bodyid").getAttribute("data-login");
  var obj2 = { action: "lienscreationboutic", login: login };
  const lienscreation = await fetch('abo.php', {
    method: "POST",
    body: JSON.stringify(obj2)}).then((r) => r.json());
  const bouticlinksDiv = document.querySelector('#bouticlinks');
  var pbenabled = 0;
  for (var i=0; i<lienscreation.length; i++)
  {
    var hr = document.createElement("HR")
    bouticlinksDiv.appendChild(hr);
    var hsub = document.createElement("H4");
    hsub.innerHTML = lienscreation[i].stripe_subscription.id;// ${subscription.id}
    bouticlinksDiv.appendChild(hsub);
    var pst = document.createElement("P");
    pst.innerHTML = "Status: " + lienscreation[i].stripe_subscription.status;// ${subscription.id}
    if (lienscreation[i].stripe_subscription.status == 'active')
      pbenabled = 1;
    bouticlinksDiv.appendChild(pst);
    var tarif = document.createElement("P");
    if ((lienscreation[i].stripe_subscription.items.data[0].price.recurring.usage_type == "recurring") || 
    (lienscreation[i].stripe_subscription.items.data[0].price.recurring.usage_type == "licensed"))
      tarif.innerHTML = "Tarif : " + ((lienscreation[i].stripe_subscription.items.data[0].price.unit_amount_decimal) / 100).toFixed(2) + ' ' 
      + (lienscreation[i].stripe_subscription.items.data[0].price.metadata.currency_symbol) + ' ' 
      + (lienscreation[i].stripe_subscription.items.data[0].price.metadata.fr_interval);
    else if (lienscreation[i].stripe_subscription.items.data[0].price.recurring.usage_type == "metered")
      tarif.innerHTML = "Tarif : " + (lienscreation[i].stripe_subscription.items.data[0].price.unit_amount_decimal) + ' % de commission' 
    bouticlinksDiv.appendChild(tarif);
    /*var obj3 = { action: "listboutic", login: login };
    const listboutic = await fetch('abo.php', {
      method: "POST",
      body: JSON.stringify(obj3)}).then((r) => r.json());
    var lbb = document.createElement("LABEL");
    lbb.innerHTML = "Boutic reliée : ";
    bouticlinksDiv.appendChild(lbb);
    var selb = document.createElement("SELECT");
    selb.id = "selbid";
    selb.name = "selb";
    selb.setAttribute("data-aboid", lienscreation[i].aboid);
    selb.onchange = function()
    {
      var obj4 = { action: "updateboutic", aboid: this.getAttribute("data-aboid"), bouticid:this.value };
      fetch('abo.php', {
      method: "POST",
      body: JSON.stringify(obj4)}).then((s) => s.json());
      document.location.reload();
    };
    var optb = document.createElement("OPTION");
    optb.value = "0";
    optb.innerHTML = "";
    selb.appendChild(optb);
    for (var j=0; j<listboutic.length; j++)
    {
      var optb = document.createElement("OPTION");
      if (lienscreation[i].bouticid == listboutic[j].bouticid)
        optb.selected = true;
      optb.value = listboutic[j].bouticid;
      optb.innerHTML = listboutic[j].bouticalias;
      selb.appendChild(optb);
    }
    bouticlinksDiv.appendChild(selb);
    bouticlinksDiv.appendChild(document.createElement("BR"));
    bouticlinksDiv.appendChild(document.createElement("BR"));
    if ((lienscreation[i].creationboutic == 1)&&(lienscreation[i].bouticid == 0))
    {
      var form = document.createElement("FORM");
      form.id = "cb-form";
      form.method = "post";
      form.action = "newboutic.php";
      //form.target = "_blank";
      var inpcr = document.createElement("INPUT");
      inpcr.type = "hidden";
      inpcr.value = lienscreation[i].subscriptionid;
      inpcr.name = "abonnement";
      var inpab = document.createElement("INPUT");
      inpab.type = "hidden";
      inpab.value = lienscreation[i].aboid;
      inpab.name = "aboid";
      form.appendChild(inpab);
      var sub = document.createElement("INPUT");
      sub.type = "submit";
      sub.value = "Lancer la création de la Boutic"
      form.appendChild(sub);
      bouticlinksDiv.appendChild(form);
    }
    else if ((lienscreation[i].creationboutic == 0) && (lienscreation[i].bouticid > 0))
    {
      var form1 = document.createElement("FORM");
      form1.id = "bob-form";
      form1.method = "post";
      form1.action = "boboutic.php";
      //form1.target = "_blank";
      var inpcr = document.createElement("INPUT");
      inpcr.type = "hidden";
      inpcr.value = lienscreation[i].bouticid;
      inpcr.name = "bouticid";
      form1.appendChild(inpcr);
      var subbo = document.createElement("INPUT");
      subbo.type = "submit";
      subbo.value = "Aller à l'Arrière Boutic"
      form1.appendChild(subbo);
      bouticlinksDiv.appendChild(form1);
      bouticlinksDiv.appendChild(document.createElement("BR"));
      var form2 = document.createElement("FORM");
      form2.id = "fob-form";
      form2.method = "post";
      form2.action = "foboutic.php";
      form2.target = "_blank";
      var inpcr = document.createElement("INPUT");
      inpcr.type = "hidden";
      inpcr.value = lienscreation[i].bouticid;
      inpcr.name = "bouticid";
      form2.appendChild(inpcr);
      var subfo = document.createElement("INPUT");
      subfo.type = "submit";
      subfo.value = "Voir la Boutic"
      form2.appendChild(subfo);
      bouticlinksDiv.appendChild(form2);
    }*/
    //bouticlinksDiv.appendChild(document.createElement("BR"));
    /*let last4 = lienscreation[i].stripe_subscription.default_payment_method?.card?.last4 || '';
    var pl4 = document.createElement("P");
    pl4.innerHTML = "4 Dernier Digit de la carte: " + last4; // ${subscription.id}
    bouticlinksDiv.appendChild(pl4);
    var pcpe = document.createElement("P");
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    pcpe.innerHTML = "Fin de la période courante: " + new Date(lienscreation[i].stripe_subscription.current_period_end * 1000).toLocaleDateString('fr-FR', options); // ${subscription.id}
    bouticlinksDiv.appendChild(pcpe);*/
    if (lienscreation[i].stripe_subscription.status != 'canceled')
    {
      var acl = document.createElement("A");
      acl.href = "cancel.php?subscription=" + lienscreation[i].stripe_subscription.id;
      acl.innerHTML = "Annulation";
      bouticlinksDiv.appendChild(acl);
    }
  }
  if (pbenabled == 1)
    document.getElementById('quitlienid').style.display = "block";
  else
    document.getElementById('quitlienid').style.display = "none";
  
})
