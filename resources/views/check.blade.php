<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Firebase Push Notifications</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Firebase Push Notifications Test</h1>

    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

    <script>
        // 🔹 Firebase config from your web app
        const firebaseConfig = {
  apiKey: "AIzaSyDZKDVcp8s6G85hFGGLFVeYVnPYQfF1FV8",
  authDomain: "xspend-n.firebaseapp.com",
  projectId: "xspend-n",
  storageBucket: "xspend-n.firebasestorage.app",
  messagingSenderId: "545513429280",
  appId: "1:545513429280:web:3900e95bfa222ff3e69f45"
};

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // Register service worker first
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then((registration) => {
                    console.log('Service Worker registered:', registration);

                    // Request permission
                    return Notification.requestPermission();
                })
                .then(permission => {
                    if (permission === 'granted') {
                        // Get FCM token
                        return messaging.getToken();
                    } else {
                        throw new Error('Notification permission denied');
                    }
                })
                .then(token => {
                    console.log('FCM Token:', token);

                    // Send token to Laravel backend
                    fetch('/save-fcm-token', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ fcm_token: token })
})
.then(res => res.json())
.then(data => {
    console.log('Token saved:', data);

    // 🔹 Trigger notification after 5 seconds
    setTimeout(() => {
        fetch('/send-notification', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                title: 'Welcome!',
                body: 'Your FCM token has been registered successfully.'
            })
        })
        .then(res => res.json())
        .then(resp => {
            alert("success")
            console.log('Notification sent:', resp)
    })
        .catch(err => console.error('Send notification error:', err));
    }, 5000); // 5000ms = 5 seconds
})
.catch(err => console.error('Save token error:', err));
                })
                .catch(err => console.error('FCM Error:', err));
        }

        // Handle foreground messages
        messaging.onMessage(payload => {
            console.log('Foreground message received:', payload);
            alert(payload.notification.title + "\n" + payload.notification.body);
        });
    </script>
</body>
</html>
