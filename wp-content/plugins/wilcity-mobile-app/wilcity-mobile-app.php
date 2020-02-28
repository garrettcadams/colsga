<?php
/*
 * Plugin Name: Wilcity Mobile App
 * Plugin URI: https://wilcity.com
 * Author: Wilcity
 * Author URI: https://wilcity.com
 * Version: 1.4.8
 * Description: Wilcity Mobile App
 */

add_action('admin_notices', function(){
	if ( version_compare(PHP_VERSION, '7.0.0', '<') ){
		?>
		<div class="notice notice-error" style="padding: 20px; border-left:  4px solid #dc3232; color: red;">
			In order to use Wilcity App, you need to upgrade PHP version to 7.0 or higher (7.2 recommended). Please read <a href="https://documentation.wilcity.com/knowledgebase/wordpress-and-wilcity-server-requirements/" target="_blank" style="color: red;">WordPress and Wilcity Server Requirements</a> to know more.
		</div>
		<?php
		return false;
	}
});

use WILCITY_APP\Controllers\HomeController;
use WILCITY_APP\Controllers\TermController;
use WILCITY_APP\Controllers\Taxonomies as WilokeMTaxonomies;
use WILCITY_APP\Controllers\PostTypes as PostTypes;
use WILCITY_APP\Controllers\Listings;
use WILCITY_APP\Controllers\Listing;
use WILCITY_APP\Controllers\OrderBy;
use WILCITY_APP\Controllers\Filter;
use WILCITY_APP\Controllers\NearByMe;
use WILCITY_APP\Controllers\Translations;
use WILCITY_APP\Controllers\Events;
use WILCITY_APP\Controllers\Event;
use WILCITY_APP\Controllers\Review;
use WILCITY_APP\Controllers\Blog;
use WILCITY_APP\Controllers\MenuController;
use WILCITY_APP\Controllers\SearchField;

if (!function_exists('apache_request_headers'))
{
	function apache_request_headers()
	{
		$headers = [];
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}

function wilcityAppStripTags($text){
	return is_array($text) ? $text : strip_tags($text);
}

function wilcityAppGetLanguageFiles($field='', $lang=''){
	$aFinalTranslation = include get_template_directory() . '/configs/config.translation.php';

	if ( empty($lang) ){
		$translationDir = get_stylesheet_directory() . '/configs/config.translation.php';
	}else{
		$translationDir = get_stylesheet_directory() . '/configs/config.translation-'.$lang.'.php';
	}
	if ( is_file($translationDir) ){
		$aTranslation = include $translationDir;
	}

	if ( isset($aTranslation) ){
		$aFinalTranslation = $aTranslation + $aFinalTranslation;
	}

	if ( !empty($field) ){
		if ( isset($aFinalTranslation[$field]) ){
			return $aFinalTranslation[$field];
		}
		return '';
	}

	return $aFinalTranslation;
}

add_filter('wiloke-listing-tools/config/middleware', function($aMiddleware){
	$aMiddleware['isLoggedInToFirebase'] = 'WILCITY_APP\Middleware\IsLoggedInFirebase';
	$aMiddleware['verifyFirebaseChat'] = 'WILCITY_APP\Middleware\VerifyFirebaseChat';
	$aMiddleware['isOwnerOfReview'] = 'WILCITY_APP\Middleware\IsOwnerOfReview';
	return $aMiddleware;
});

if ( !function_exists('wilcityModifyProductCatsQuery') ){
    add_filter( 'kc_autocomplete_product_cats', 'wilcityModifyProductCatsQuery' );

    function wilcityModifyProductCatsQuery( $data ){
        $aTerms = wilcitySCSearchTerms($_POST['s'], 'product_cat');
        if ( !$aTerms ){
            return false;
        }

        return array('Select Terms'=>$aTerms);
    }
}

if ( !function_exists('wilcityModifyProductIDsQuery') ) {
    add_filter('kc_autocomplete_product_ids', 'wilcityModifyProductIDsQuery');

    function wilcityModifyProductIDsQuery($aData)
    {
        $query     = new WP_Query(
            [
                'post_type'      => 'product',
                'posts_per_page' => 20,
                's'              => $aData['s'],
                'post_status'    => 'publish'
            ]
        );
        $aListings = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $aListings[] = $query->post->ID.':'.$query->post->post_title;
            }
        }

        return ['Select Listings' => $aListings];
    }
}

// Fix for WooCommerce 3.7
add_filter('woocommerce_is_rest_api_request', 'loadWooCommerceForWilcityRestAPI');

function loadWooCommerceForWilcityRestAPI($status) {
    $request = $_SERVER['REQUEST_URI'];
    if (strpos($request, 'wilcity') !== false
        || strpos($request, 'wiloke') !== false) {
        return false;
    }
    return $status;
}

add_action('wiloke-listing-tools/run-extension', function(){
	if ( !defined('WILCITY_SC_VERSION') ){
		return false;
	}

	if ( !defined('WILOKE_PREFIX') ){
		define('WILOKE_PREFIX', 'wiloke');
    }

	define('WILCITY_MOBILE_APP', 'wiloke');
	define('WILCITY_MOBILE_CAT', 'Wilcity Mobile App');
	define('WILCITY_APP_PATH', plugin_dir_path(__FILE__));
	define('WILCITY_APP_URL', plugin_dir_url(__FILE__));
	define('WILCITY_APP_IMG_PLACEHOLDER', WILCITY_APP_URL . 'assets/img/app-img-placeholder.jpg');

	require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

//	\WILCITY_APP\Controllers\Firebase\LoginRegister::testSingUpToFirebase();
//	\WILCITY_APP\Controllers\Firebase\LoginRegister::testCreateUser();

	new HomeController;
	new TermController;
	new WilokeMTaxonomies;
	new PostTypes;
	new Listings;
	new Listing;
	new OrderBy;
	new Filter;
	new NearByMe;
	new Translations;
	new Events;
	new Event;
	new Review;
	new Blog;
	new MenuController;
	new SearchField;

    // Sidebar Items
	new \WILCITY_APP\SidebarOnApp\TermBox;
	new \WILCITY_APP\SidebarOnApp\Tags;
	new \WILCITY_APP\SidebarOnApp\Statistic;
	new \WILCITY_APP\SidebarOnApp\PriceRange;
	new \WILCITY_APP\SidebarOnApp\CustomContent;
	new \WILCITY_APP\SidebarOnApp\Claim;
	new \WILCITY_APP\SidebarOnApp\Categories;
	new \WILCITY_APP\SidebarOnApp\BusinessHours;
	new \WILCITY_APP\SidebarOnApp\BusinessInfo;
	new \WILCITY_APP\Controllers\GeneralSettings;
	new \WILCITY_APP\Controllers\LoginRegister;
	new \WILCITY_APP\Controllers\FavoritesController;
	new \WILCITY_APP\Controllers\MyDirectoryController;
	new \WILCITY_APP\Controllers\UserController;
	new \WILCITY_APP\Controllers\ReportController;
	new \WILCITY_APP\Controllers\DashboardController;
	new \WILCITY_APP\Controllers\NotificationController;
	new \WILCITY_APP\Controllers\MessageController;
	new \WILCITY_APP\Controllers\ReviewController;
	new \WILCITY_APP\Controllers\ImageController;
	new \WILCITY_APP\Controllers\AdmobController;
	new \WILCITY_APP\Controllers\GlobalSettingController;

//	new \WILCITY_APP\Controllers\WooCommerceController;

	// firebase
	if ( \WilokeListingTools\Framework\Helpers\Firebase::isFirebaseEnable() ){
		new \WILCITY_APP\Controllers\Firebase\LoginRegister;
		new \WILCITY_APP\Controllers\Firebase\MessageController;
		new \WILCITY_APP\Controllers\Firebase\PushNotificationController;
	}

    add_action('woocommerce_loaded', function(){
	    new \WILCITY_APP\Controllers\WooCommerce\WooCommerceRatingController();
	    new \WILCITY_APP\Controllers\WooCommerce\WooCommerceCartController();
	    new \WILCITY_APP\Controllers\WooCommerce\WooCommerceCheckoutController();
	    new \WILCITY_APP\Controllers\WooCommerce\WooCommerceGatewayController();
	    new \WILCITY_APP\Controllers\WooCommerce\WooCommerceProductController();
	    new \WILCITY_APP\Controllers\WooCommerce\WooCommerceWishlistController();
	    new \WILCITY_APP\Controllers\WooCommerce\WooCommerceOrderController();
	    new \WILCITY_APP\Controllers\WooCommerce\WooCommerceBookingController();
    });
	
	add_action('dokan_loaded', function() {
	    new \WILCITY_APP\Controllers\WooCommerce\DokanGlobalController();
	    new \WILCITY_APP\Controllers\WooCommerce\DokanProductController();
	    new \WILCITY_APP\Controllers\WooCommerce\DokanOrderController();
	    new \WILCITY_APP\Controllers\WooCommerce\DokanStatisticController();
	    new \WILCITY_APP\Controllers\WooCommerce\DokanWithdrawnController();
    });

	require_once WILCITY_APP_PATH . 'mobile-shortcodes.php';
}, 99);

