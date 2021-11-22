

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
    if (lienscreation[i].stripe_subscription.status == 'active')
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