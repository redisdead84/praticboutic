document.addEventListener('DOMContentLoaded', async () => {
  // Fetch the ID of the subscription from the query string
  // params.
  const params = new URLSearchParams(window.location.search);
  const subscriptionId = params.get('subscription');

  // When the cancel button is clicked, send an AJAX request
  // to our server to cancel the subscription.
  const cancelBtn = document.querySelector('#cancel-btn');
  cancelBtn.addEventListener('click', async (e) => {
    document.getElementById("loadid").style.display = "block";
    document.getElementById("modalid").style.display = "none";
    e.preventDefault();
    
    var obj = {
        action : "boannulerabonnement",
        subscriptionid : subscriptionId,
        login: login
        };
        
    fetch('abo.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(obj)
    })
    .then(function(result) {
      return result.json();
    }) 
    .then(function(data) {
        window.location = "account.php";
    });
  });
});