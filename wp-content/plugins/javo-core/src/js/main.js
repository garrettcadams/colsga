/**
 * Manage global libraries like jQuery or THREE from the package.json file
 */

// Import from node
import 'jquery-slimscroll';

// Import custom modules
import './modules/bootstrap-tabdrop';
import './modules/jquery.scrolling-tabs';
import './modules/grid-loader/AnimOnScroll';

// Chart
import 'chart.js';
import './modules/dashboard-chart';
import AOS from 'aos';
import 'aos/dist/aos.css';

(function ($) {
    $("#buddypress [data-bp-list]").on('bp_ajax_request bp_ajax_append', function (event, data) {
        $.each({
            members: 'members-list',
            groups: 'groups-list',
            //activity: 'activity-list'
        }, function (component, selector) {
            var
                element = document.getElementById(selector),
                aosInstance;
            if (element) {
                aosInstance = new AnimOnScroll(element, {
                    minDuration: 0.4,
                    maxDuration: 0.7,
                    viewportFactor: 0.8
                });
                $(document).ajaxComplete(function (event, status, settings) {
                    if (-1 < settings.data.indexOf('get_single_activity_content')) {
                        var
                            m = new Masonry(aosInstance.el, {
                                itemSelector: 'li',
                                transitionDuration: 0
                            }),
                            interval = setInterval(function(){
                                m.layout();
                                clearInterval(interval);
                            }, 500);
                    }
                });
            }
        });
    });
    if($('body').hasClass('my-account')) {
        var
            nav = '.bp-navs.main-navs',
            $nav = $(nav);
        if($nav.length){
            $('<a>').addClass('jvbpd-bp-navs-opener').append(
                $('<i>').addClass('fa fa-bars')
            ).on('click', function(event){
                var $this = $(this);
                event.preventDefault();
                $this.closest(nav).toggleClass('open');
            }).appendTo($nav);
        }
    }
    $(window).on('lava:favorite-not-user-loggedin', function(){
        $('#login_panel').modal('show');
    });
})(jQuery);

// Other scripts
jQuery(document).ready(function ($) {
    jQuery('.responsive-tabdrop').tabdrop();
});
jQuery(document).ready(function ($) {
    $('.scroll-tabs').scrollingTabs({
        bootstrapVersion: 4,
        cssClassLeftArrow: 'fa fa-chevron-left',
        cssClassRightArrow: 'fa fa-chevron-right'
    });
});
jQuery(document).ready(function ($) {
    $("body").addClass('jv-jsloaded');
});

AOS.init({
    // Global settings:
    disable: false, // accepts following values: 'phone', 'tablet', 'mobile', boolean, expression or function
    startEvent: 'DOMContentLoaded', // name of the event dispatched on the document, that AOS should initialize on
    initClassName: 'aos-init', // class applied after initialization
    animatedClassName: 'aos-animate', // class applied on animation
    useClassNames: false, // if true, will add content of `data-aos` as classes on scroll
    disableMutationObserver: false, // disables automatic mutations' detections (advanced)
    debounceDelay: 50, // the delay on debounce used while resizing window (advanced)
    throttleDelay: 99, // the delay on throttle used while scrolling the page (advanced)


    // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
    offset: 120, // offset (in px) from the original trigger point
    delay: 500, // values from 0 to 3000, with step 50ms
    duration: 800, // values from 0 to 3000, with step 50ms
    easing: 'ease', // default easing for AOS animations
    once: false, // whether animation should happen only once - while scrolling down
    mirror: false, // whether elements should animate out while scrolling past them
    anchorPlacement: 'top-bottom', // defines which position of the element regarding to window should trigger the animation

  });