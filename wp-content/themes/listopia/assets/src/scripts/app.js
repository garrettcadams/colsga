/**
 * Manage global libraries like jQuery or THREE from the package.json file
 */

// Import libraries

// Import custom modules
import App from "./modules/app.js";
import jvbpdCommon from "./common.js"
import smoothWheel from "./modules/smooth-wheel.js";

// /** Boostrap JS **/
//require('bootstrap-sass');

const app = new App();

// preloading
jQuery(document).ready(function () {
  jQuery("#preloader-wrap").addClass("disable");
});

// Go to top when you click a tag
// jQuery(document).ready(function () {
//   jQuery('a[href^="http"]').click(function () {
//     jQuery("html, body").animate({
//         scrollTop: 0
//       },
//       600
//     ); //IE, FF
//   });
// });

// Smooth Wheel
// jQuery(document).ready(function () {
//     jQuery('html').smoothWheel()
// });

// Go to top button
jQuery(document).ready(function () {
  //Check to see if the window is top if not then display button
  jQuery(window).scroll(function () {
    if (jQuery(this).scrollTop() > 100) {
      jQuery(".scrollToTop").fadeIn();
    } else {
      jQuery(".scrollToTop").fadeOut();
    }
  });

  //Click event to scroll to top
  jQuery(".scrollToTop").click(function () {
    jQuery("html, body").animate({
        scrollTop: 0
      },
      800
    );
    return false;
  });
});

//global.$ = global.jQuery = require('jquery');
//window.$ = window.jQuery = require('jquery');