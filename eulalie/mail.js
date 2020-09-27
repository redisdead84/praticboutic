
var commande = {
  method: localStorage.getItem("method"),
  table: localStorage.getItem("table"),
  nom: localStorage.getItem("nom"),
  prenom: localStorage.getItem("prenom"),
  adresse1: localStorage.getItem("adresse1"),
  adresse2: localStorage.getItem("adresse2"),
  codepostal: localStorage.getItem("codepostal"),
  ville: localStorage.getItem("ville"),
  telephone: localStorage.getItem("telephone"),
  items: JSON.parse(localStorage.getItem("commande"))
};

fetch("mailj.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/json"
  },
  body: JSON.stringify(commande)
})
  .then(function(result) {
    return result.text();
  })