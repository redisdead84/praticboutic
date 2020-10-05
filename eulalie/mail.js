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
    items: JSON.parse(sessionStorage.getItem("commande"))
  };
  
  fetch("mailj.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(commande)
  })
   
 