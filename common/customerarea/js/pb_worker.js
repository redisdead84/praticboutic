
function timedCount() {
  postMessage("RefreshCommande");
  setTimeout("timedCount()",20000);
}

timedCount();

