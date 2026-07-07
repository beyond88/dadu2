window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    // window.Popper = require('popper.js').default;
    // window.$ = window.jQuery = require('jquery');
    //
    // require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Resolve the application's base URL from the <meta name="base-url"> tag that
 * the server renders with url('/'). This automatically includes any subfolder
 * the app is hosted in (e.g. http://localhost/inventory), so root-absolute API
 * calls and redirects target the correct base on every environment — no manual
 * per-machine configuration required.
 *
 *  - axios.defaults.baseURL  -> prefixes all axios calls (e.g. '/admin/api/...').
 *  - window.appBaseUrl       -> the resolved base (no trailing slash).
 *  - window.appUrl(path)     -> build an absolute URL for a root-relative path,
 *                               used for window.location redirects in components.
 */
const baseUrlMeta = document.head.querySelector('meta[name="base-url"]');
const appBaseUrl = baseUrlMeta && baseUrlMeta.content ? baseUrlMeta.content.replace(/\/+$/, '') : '';

window.axios.defaults.baseURL = appBaseUrl;
window.appBaseUrl = appBaseUrl;
window.appUrl = function (path) {
    path = path == null ? '' : String(path);
    return appBaseUrl + (path.charAt(0) === '/' ? path : '/' + path);
};

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
