document.addEventListener('DOMContentLoaded', async () => {
  // Fetch the ID of the subscription from the query string
  // params.
  const params = new URLSearchParams(window.location.search);
  const subscriptionId = params.get('subscription');

  // When the cancel button is clicked, send an AJAX request
  // to our server to cancel the subscription.
  const cancelBtn = document.querySelector('#cancel-btn');
  cancelBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    setMessage("Cancelling subscription...");

    const {subscription} = await fetch('abo.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action : "boannulerabonnement",
        subscriptionid : subscriptionId,
        login: login
      }),
    })
      .then((response) => response.json())

    // Display the status of the subscription after attempting to
    // cancel.
    setMessage(`Subscription status: ${subscription.status}`);
    setMessage(`Redirecting back to account in 7s.`);

    // Redirect to the account page.
    setTimeout(() => {
      window.location.href = "account.php";
    }, 7 * 1000);
  });

  const setMessage = (message) => {
    const messagesDiv = document.querySelector('#messages');
    messagesDiv.innerHTML += "<br>" + message;
  }
});
