
function timedCount() {
  postMessage("RefreshCommande");
  setTimeout("timedCount()",5000);
}

timedCount();
