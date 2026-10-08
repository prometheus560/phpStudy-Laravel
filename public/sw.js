self.addEventListener('push', function (event) {
    if (!event.data) {
        return;
    }

    const data = event.data.json();

    const title = data.title || 'Study Planner';

    const options = {
        body: data.body || 'You have an upcoming task deadline.',
        icon: data.icon || '/favicon.ico',
        badge: data.badge || '/favicon.ico',
        data: {
            url: data.url || '/dashboard'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function (clientList) {

            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});