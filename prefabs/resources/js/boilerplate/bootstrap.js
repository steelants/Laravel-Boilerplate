
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// import $ from 'jquery';
// window.$ = $;

import $ from 'jquery';
window.$ = window.jQuery = $;

// Bootstrap 6 ships ES modules only, inline scripts in Blade still use the global.
import * as bootstrap from 'bootstrap';
bootstrap.Menu.Default.strategy = 'fixed';
window.bootstrap = bootstrap;
