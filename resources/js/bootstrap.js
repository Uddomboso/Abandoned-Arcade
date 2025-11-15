// bootstrap javascript module
// initializes core javascript libraries and utilities
// sets up axios for http requests and configures csrf token handling

import 'bootstrap';

// load axios http library
// allows easy http requests to laravel backend
// automatically handles csrf token from cookie
import axios from 'axios';
window.axios = axios;

// set default header for all axios requests
// identifies requests as ajax requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// echo real time broadcasting (commented out)
// uncomment and configure if you need real time features
// allows subscribing to channels and listening for broadcast events

// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
