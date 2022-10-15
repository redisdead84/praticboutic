self.addEventListener('notificationclick', function(event) {
  event.waitUntil((async () => {
      // Exit early if we don't have access to the client.
      // Eg, if it's cross-origin.
      clients.matchAll({
        includeUncontrolled: false,
        type: 'window',
      })
      .then(function(clientList) {
        for(var i = 0 ; i < clientList.length ; i++)
        {
          clientList[i].postMessage({
            msg: "NEWORDERS",
          });
        }
      });
  })());
});
