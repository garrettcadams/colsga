// Bootstrap

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window._ = require('lodash');
window.Popper = require('popper.js').default;

import Popper from 'popper.js/dist/umd/popper.js';

try {
    window.$ = window.jQuery = require('jquery');

    //window.Popper = Popper;

    require('bootstrap');
} catch (e) {}


//import 'bootstrap';


// require('bootstrap/js/dist/util');
// require('bootstrap/js/dist/alert');
// require('bootstrap/js/dist/button');
// require('bootstrap/js/dist/carousel');
// require('bootstrap/js/dist/collapse');
// require('bootstrap/js/dist/dropdown');
// require('bootstrap/js/dist/modal');
// require('bootstrap/js/dist/scrollspy');
// require('bootstrap/js/dist/tab');
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');