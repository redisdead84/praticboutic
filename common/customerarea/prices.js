// Fetch price data.
const pricesDiv = document.querySelector('#price-list');

fetch('/config')
  .then((response) => response.json())
  .then((data) => {
    pricesDiv.innerHTML = '';
    if(!data.prices) {
      pricesDiv.innerHTML = `
        <h3>Pas de tarif trouvé</h3>

        <p>This sample requires two prices, one with the lookup_key sample_basic and another with the lookup_key sample_premium</p>

        <p>You can create these through the API or with the Stripe CLI using the provided seed.json fixture file with: <code>stripe fixtures seed.json</code>
      `
    }

    data.prices.forEach((price) => {
      pricesDiv.innerHTML += `
        <div>
          <span>
            ${(price.unit_amount / 100).toFixed(2)} ${price.metadata.currency_symbol} ${price.metadata.fr_interval}
          </span>
          <button onclick="createSubscription('${price.id}')">Sélection</button>
        </div>
      `;
    });
  })
  .catch((error) => {
    console.error('Error:', error);
  });


const createSubscription = (priceId) => {
  const params = new URLSearchParams(window.location.search);
  const customerId = params.get('customerId');

  return fetch('/create-subscription', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      priceId: priceId,
      customerId: customerId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      const params = new URLSearchParams(window.location.search);
      params.append('subscriptionId', data.subscriptionId);
      params.append('clientSecret', data.clientSecret);
      window.location.href = '/subscribe.html?' + params.toString();
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}
