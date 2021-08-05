// 2020-03-14
// "Respond to the `/justuno/service-worker.js` request with the provided JavaScript":
// https://github.com/justuno-com/m2/issues/10
// 2020-03-15
// "Replace the `/justuno/service-worker.js` URL with  `/apps/justuno/service-worker.js`":
// https://github.com/justuno-com/m2/issues/11
self.addEventListener('push', function (event) {
	console.log('Push message!', event.data.text());
	const payload = JSON.parse(event.data.text());
	event.waitUntil(
		self.registration.showNotification(payload.title, {
			body: payload.body,
			data: {link: payload.link},
			icon: payload.icon,
			image: payload.image,
			requireInteraction: payload.requireInteraction
		})
	);
});
self.addEventListener('notificationclick', function (event) {
	console.log('Notification click: tag', event.notification.tag);
	event.notification.close();
	if (event.notification.data.link) {
		event.waitUntil(clients.openWindow(event.notification.data.link));
	}
});