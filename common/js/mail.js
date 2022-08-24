  var commande = {
    method: sessionStorage.getItem("method"),
    table: sessionStorage.getItem("table"),
    nom: sessionStorage.getItem("nom"),
    prenom: sessionStorage.getItem("prenom"),
    adresse1: sessionStorage.getItem("adresse1"),
    adresse2: sessionStorage.getItem("adresse2"),
    codepostal: sessionStorage.getItem("codepostal"),
    ville: sessionStorage.getItem("ville"),
    telephone: sessionStorage.getItem("telephone"),
    paiement: sessionStorage.getItem("choice"),
    vente: sessionStorage.getItem("choicel"),
    infosup: sessionStorage.getItem("infosup"),
    items: JSON.parse(sessionStorage.getItem("commande")),
    fraislivr: sessionStorage.getItem("fraislivr"),
    customer: sessionStorage.getItem("customer")
  };
  if (commande.items.length > 0)
  { 
    fetch("mailj.php", {
      method: "POST",
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(commande)
    })
    .then(function(result) {
      return result.json();
    }) 
    .then(function(data) {
      if (typeof (data.error) !== "undefined")
      {
        alert("Erreur : " + data.error);
        window.location.href = "404.html";
      }
    })
  }
   
 