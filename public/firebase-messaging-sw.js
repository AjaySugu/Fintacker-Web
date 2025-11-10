importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyDZKDVcp8s6G85hFGGLFVeYVnPYQfF1FV8",
    authDomain: "xspend-n.firebaseapp.com",
    projectId: "xspend-n",
    storageBucket: "xspend-n.firebasestorage.app",
    messagingSenderId: "545513429280",
    appId: "1:545513429280:web:3900e95bfa222ff3e69f45"
});

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
    console.log('Background Message:', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/favicon.ico'
    };
    return self.registration.showNotification(notificationTitle, notificationOptions);
});
