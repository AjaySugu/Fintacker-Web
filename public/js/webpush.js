import { PushSubscription } from 'web-push';

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then(registration => {
            console.log('Service Worker registered', registration);
        });
}

// Request permission
Notification.requestPermission().then(permission => {
    if(permission === 'granted'){
        axios.post('/save-subscription', {
            subscription: JSON.stringify(window.pushSubscription) // send to backend
        });
    }
});
