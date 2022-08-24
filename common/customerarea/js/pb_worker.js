
function timedCount() {
  setTimeout(sendMessage, 20000);
}

function sendMessage()
{
  postMessage("RefreshCommande");
  timedCount();
}

timedCount();

