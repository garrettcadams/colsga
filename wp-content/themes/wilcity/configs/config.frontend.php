<?php
$aThemeOptions = Wiloke::getThemeOptions(true);

if ( WilokeThemeOptions::isEnable('toggle_custom_login_page') ){
	$reCaptchaCallback = 'wilcityRunReCaptcha';
}else{
	$reCaptchaCallback = 'vueRecaptchaApiLoaded';
}

if ( !empty($aThemeOptions['general_google_language']) ){
	$googleCaptchaUrl = 'https://www.google.com/recaptcha/api.js?onload='.$reCaptchaCallback.'&render=explicit&hl='.$aThemeOptions['general_google_language'];
}else{
	$googleCaptchaUrl = 'https://www.google.com/recaptcha/api.js?onload='.$reCaptchaCallback.'&render=explicit';
}

return array(
    'scripts' => array(
    	'js' => array(
//		    array('firebase', 'https://www.gstatic.com/firebasejs/5.5.8/firebase.js', 'isExternal'=>true),
		    array('vuejs', !defined('WP_DEBUG_SCRIPT') || !WP_DEBUG_SCRIPT ? 'vue/vue.min.js' : 'vue/vue.js', 'isVendor'=>true),
		    array('lazyload', 'lazyload/jquery.lazy.min.js', 'isVendor'=>true, 'conditional'=>'wilcityIsLazyLoad'),
		    array('lazyload.picture', 'lazyload/jquery.lazy.picture.min.js', 'isVendor'=>true, 'conditional'=>'wilcityIsLazyLoad'),
		    array('vue-router', 'vue/vue-router.min.js', 'isVendor'=>true, 'conditional'=>'wilcityIsDashboardPage'),
//		    array('vue-sortable', 'vue/Sortable.min.js', 'isVendor'=>true, 'conditional'=>'wilcityIsPostAuthor'),
//			array('vue-draggable', 'vue/vuedraggable.min.js', 'isVendor'=>true, 'conditional'=>'wilcityIsSingleListingPage'),
		    array('vuex', 'vue/vuex.min.js', 'isVendor'=>true),
		    array('stripe', '//checkout.stripe.com/checkout.js', 'isExternal'=>true, 'conditional'=>'wilCityAllowToEnqueueStripe'),
		    array('googleplaces-async', '//maps.googleapis.com/maps/api/js?libraries=places&key=', 'isGoogleAPI'=>true, 'conditional'=>'wilcityIsGoogleMap'),
		    array('mapbox-gl', 'https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.js', 'conditional'=>'wilcityIsMapbox', 'isExternal'=>true),
		    array('jquery-ui-slider', 'isWPLIB'=>true),
		    array('jquery-ui-autocomplete', 'isWPLIB'=>true),
		    array('jquery-ui-touch-punch', 'touchpunch/jquery.ui.touch-punch.min.js', 'isVendor'=>true, 'conditional'=>'wp_is_mobile'),
		    array('jquery-ui-datepicker', 'isWPLIB'=>true),
		    array('spectrum', 'spectrum/spectrum.js', 'isVendor'=>true, 'conditional'=>'wilcityIsAddListingPage'),
		    array('chartjs', 'chartjs/Chart.js', 'isVendor'=>true),
		    array('jqueryeasing', 'jquery.easing/jquery.easing.js', 'isVendor'=>true),
		    array('perfect-scrollbar', 'perfect-scrollbar/perfect-scrollbar.min.js', 'isVendor'=>true),
		    array('magnific-popup', 'magnific-popup/jquery.magnific-popup.js', 'isVendor'=>true),
		    array('jquery-select2', 'select2/select2.js', 'isVendor'=>true),
		    array('swiper', 'swiper/swiper.js', 'isVendor'=>true),
		    array('MagnificGalleryPopup', 'MagnificGalleryPopup.min.js'),
		    array('theia-sticky-sidebar', 'theia-sticky-sidebar/theia-sticky-sidebar.js', 'isVendor'=>true),
		    array('snazzy-info-window', 'googlemap/snazzy-info-window.min.js', 'isVendor'=>true, 'conditional'=>'wilcityIsMapPage,wilcityIsGoogleMap'),
		    array('markerclusterer', 'googlemap/markerclusterer.js', 'isVendor'=>true, 'conditional'=>'wilcityIsMapPage,wilcityIsGoogleMap'),
		    array('wilcity-shortcodes', 'shortcodes.min.js'),
		    array('waypoints-vendor', 'waypoints/jquery.waypoints.min.js', 'isVendor'=>true),
		    array('bundle', 'index.min.js'),
		    array('WilcityFavoriteStatistics', 'FavoriteStatistics.min.js'),
		    array('quick-search', 'quick-search.min.js'),
		    array('googlemap', 'googlemap.min.js', 'conditional'=>'wilcityIsMapPage,wilcityIsGoogleMap'),
		    array('mapbox', 'mapbox.min.js', 'conditional'=>'wilcityIsMapPage,wilcityIsMapbox'),
		    array('review', 'review.min.js'),
		    array('googleReCaptcha', $googleCaptchaUrl, 'isExternal'=>true, 'conditional'=>'wilcityIsLoginPage'),
		    array('dashboard', 'dashboard.min.js', 'conditional'=>'wilcityIsDashboardPage'),
		    array('wp_enqueue_media', 'isWPLIB'=>true, 'conditional'=>'wilcityIsAddListingDashboardSingleListingPage'),
		    array('addlisting', 'addlisting.min.js', 'conditional'=>'wilcityIsAddListingPage'),
			array('single-listing-handle', 'single-listing.min.js', 'conditional'=>'wilcityIsSingleListingPage'),
			array('wp_enqueue_media', 'isWPLIB'=>true, 'conditional'=>'wilcityIsLoginedSingleListingPage'),
			array('single-event', 'single-event.min.js', 'conditional'=>'wilcityIsSingleEventPage'),
			array('single-mapbox', 'single-mapbox.min.js', 'conditional'=>'wilcityIsMapbox,wilcityIsSinglePage'),
			array('single-google-map', 'single-google-map.min.js', 'conditional'=>'wilcityIsGoogleMap,wilcityIsSinglePage'),
		    array('app', 'app.min.js'),
		    array('custom-login', 'customLogin.min.js', 'conditional'=>'wilcityIsCustomLogin'),
		    array('reset-password', 'resetPassword.min.js', 'conditional'=>'wilcityIsResetPassword'),
		    array('no-map-search', 'no-map-search.min.js', 'conditional'=>'wilcityIsNoMapTemplate')
	    ),
        'css' => array(
	        array('bootstrap', 'bootstrap/grid.css', 'isVendor'=>true),
	        array('spectrum', 'spectrum/spectrum.css', 'isVendor'=>true, 'conditional'=>'wilcityIsAddListingPage'),
	        array('perfect-scrollbar', 'perfect-scrollbar/perfect-scrollbar.min.css', 'isVendor'=>true),
	        array('font-awesome', 'fontawesome/font-awesome.min.css', 'isFont'=>true),
	        array('Poppins', 'Poppins:400,500,600,700,900|Roboto:300,400|Dancing+Script&display=swap', 'isGoogleFont'=>true),
	        array('line-awesome', 'line-awesome/line-awesome.css', 'isFont'=>true),
	        array('magnific-popup', 'magnific-popup/magnific-popup.css', 'isVendor'=>true),
	        array('magnific-select2', 'select2/select2.css', 'isVendor'=>true),
	        array('swiper', 'swiper/swiper.css', 'isVendor'=>true),
	        array('jquery-ui-custom-style', 'ui-custom-style/ui-custom-style.min.css', 'isVendor'=>true),
	        array('snazzy-info-window', 'googlemap/snazzy-info-window.min.css', 'isVendor'=>true),
	        array('mapbox-gl', 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css', 'conditional'=>'wilcityIsMapbox,wilcityIsMapPageOrSinglePage', 'isExternal'=>true),
	        array('additional-woocommerce', 'woocommerce.min.css', 'conditional'=>'wilcityIsUsingWooCommerce'),
	        array('app', 'app.min.css')
        )
    ),
    'register_nav_menu'  => array(
	    'menu'  => array(
		    array(
			    'key'   => 'wilcity_menu',
			    'name'  => esc_html__('WilCity Menu', 'wilcity'),
		    ),
		    array(
			    'key'   => 'wilcity_footer_login_menu',
			    'name'  => 'Footer Custom Login Page'
		    )
	    ),
	    'config'=> array(
		    'wilcity_menu'=> array(
			    'theme_location'  => 'wilcity_menu',
			    'name'            => esc_html__('WilCity Menu', 'wilcity'),
			    'menu'            => '',
			    'container'       => '',
			    'container_class' => '',
			    'container_id'    => '',
			    'menu_class'      => 'nav-menu',
			    'menu_id'         => apply_filters('wilcity/filter/id-prefix', 'wilcity-menu'),
			    'echo'            => true,
			    'before'          => '',
			    'after'           => '',
			    'link_before'     => '',
			    'link_after'      => '',
			    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			    'depth'           => 0,
			    'walker'          => ''
		    ),
		    'wilcity_footer_login_menu'=> array(
			    'theme_location'  => 'wilcity_footer_login_menu',
			    'name'            => 'Footer Custom Login Page',
			    'menu'            => '',
			    'container'       => '',
			    'container_class' => '',
			    'container_id'    => '',
			    'menu_class'      => 'nav-menu',
			    'menu_id'         => 'wilcity-footer-login-menu',
			    'echo'            => true,
			    'before'          => '',
			    'after'           => '',
			    'link_before'     => '',
			    'link_after'      => '',
			    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			    'depth'           => 0,
			    'walker'          => ''
		    )
	    )
    ),
);
