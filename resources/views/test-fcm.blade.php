<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Firebase FCM Test</title>
</head>
<body>
    <h2>Firebase Push Notification Test</h2>
    <button onclick="sendNotification()">Send Test Notification</button>

<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
<script>
    const firebaseConfig = {
        apiKey: "AIzaSyDZKDVcp8s6G85hFGGLFVeYVnPYQfF1FV8",
        authDomain: "xspend-n.firebaseapp.com",
        projectId: "xspend-n",
        storageBucket: "xspend-n.firebasestorage.app",
        messagingSenderId: "545513429280",
        appId: "1:545513429280:web:3900e95bfa222ff3e69f45"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    // ✅ Register the service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then(function (registration) {
                console.log('Service Worker registered:', registration.scope);
                messaging.useServiceWorker(registration);
                requestPermissionAndGetToken();
            })
            .catch(function (err) {
                console.error('Service Worker error:', err);
            });
    }

    function requestPermissionAndGetToken() {
        messaging.requestPermission()
            .then(() => messaging.getToken())
            .then((token) => {
                console.log("FCM Token:", token);
                saveTokenToServer(token);
            })
            .catch((err) => console.error("Permission error:", err));
    }

    function saveTokenToServer(token) {
        fetch('/save-fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ fcm_token: token })
        })
        .then(res => res.json())
        .then(data => console.log('Token saved:', data))
        .catch(err => console.error('Token save error:', err));
    }

    messaging.onMessage((payload) => {
        console.log('Foreground message:', payload);
        new Notification(payload.notification.title, {
            body: payload.notification.body,
            icon: '/favicon.ico'
        });
    });

    function sendNotification() {
        fetch('/send-notification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                title: 'Welcome!',
                body: 'This is a test notification 🚀'
            })
        }).then(res => res.json()).then(data => console.log(data));
    }
</script>
</body>
</html>
