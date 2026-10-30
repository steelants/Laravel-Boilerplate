
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
// Every component except Datepicker, which would pull in vanilla-calendar-pro.
import Alert from 'bootstrap/js/dist/alert.js';
import Button from 'bootstrap/js/dist/button.js';
import Carousel from 'bootstrap/js/dist/carousel.js';
import Chips from 'bootstrap/js/dist/chips.js';
import Collapse from 'bootstrap/js/dist/collapse.js';
import Combobox from 'bootstrap/js/dist/combobox.js';
import Dialog from 'bootstrap/js/dist/dialog.js';
import Drawer from 'bootstrap/js/dist/drawer.js';
import Menu from 'bootstrap/js/dist/menu.js';
import NavOverflow from 'bootstrap/js/dist/nav-overflow.js';
import OtpInput from 'bootstrap/js/dist/otp-input.js';
import Popover from 'bootstrap/js/dist/popover.js';
import Range from 'bootstrap/js/dist/range.js';
import ScrollSpy from 'bootstrap/js/dist/scrollspy.js';
import Strength from 'bootstrap/js/dist/strength.js';
import Tab from 'bootstrap/js/dist/tab.js';
import Toast from 'bootstrap/js/dist/toast.js';
import Toggler from 'bootstrap/js/dist/toggler.js';
import Tooltip from 'bootstrap/js/dist/tooltip.js';

Menu.Default.strategy = 'fixed';
window.bootstrap = {
    Alert, Button, Carousel, Chips, Collapse, Combobox, Dialog, Drawer, Menu, NavOverflow,
    OtpInput, Popover, Range, ScrollSpy, Strength, Tab, Toast, Toggler, Tooltip,
};
