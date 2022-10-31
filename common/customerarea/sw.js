self.onnotificationclick = (event) => {
  //console.log('On notification click: ', event.notification.tag);
  event.notification.close();

  // This looks to see if the current is already open and
  // focuses if it is
  event.waitUntil(clients.matchAll({
    type: "window"
  }).then((clientList) => {
    for (const client of clientList) {
      client.postMessage({
        msg: "NEWORDERS",
      });
      if (client.url === self.location.origin + '/common/customerarea/admin.php' && 'focus' in client)
        return client.focus();
    }
    if (clients.openWindow)
      return clients.openWindow(self.location.origin + '/common/customerarea/admin.php');
  }));
};