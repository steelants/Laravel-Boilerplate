
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// import $ from 'jquery';
// window.$ = $;

import $ from 'jquery';
window.$ = window.jQuery = $;

import * as bootstrap from 'bootstrap';
bootstrap.Dropdown.Default.boundary = 'body';
bootstrap.Dropdown.Default.popperConfig = { strategy: 'fixed' };
window.bootstrap = bootstrap;
