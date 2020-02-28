<?php
/*
 * Plugin Name: Wiloke Listing Tools
 * Plugin URI: https://wiloke.com
 * Author: Wiloke
 * Author URI: https://wiloke.com
 * Version: 1.2.8
 * Description: This tool allows customizing your Add Listing page
 * Text Domain: wiloke-listing-tools
 * Domain Path: /languages/
 */
define('WILOKE_LISTING_TOOL_VERSION', '1.2.8');
define('WILOKE_LISTING_DOMAIN', 'wiloke-listing-tools');
define('WILOKE_LISTING_PREFIX', 'wilcity_');
define('WILOKE_LISTING_TOOL_URL', plugin_dir_url(__FILE__));
define('WILOKE_LISTING_TOOL_DIR', plugin_dir_path(__FILE__));

if ( !defined('WILOKE_PREFIX') ){
	define('WILOKE_PREFIX', 'wiloke');
}

add_action( 'plugins_loaded', 'wiloke_listing_tools_load_textdomain' );
function wiloke_listing_tools_load_textdomain() {
	load_plugin_textdomain( 'wiloke-listing-tools', false, basename(dirname(__FILE__)) . '/languages' );
}

require plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use WilokeListingTools\Controllers\GuardController;
new GuardController;

use \WilokeListingTools\Framework\Helpers\QRCodeGenerator;

// MetaBox
use WilokeListingTools\MetaBoxes\CustomCMB2Fields as CustomCMB2Fields;
use WilokeListingTools\MetaBoxes\Listing as MetaBoxesListing;
use WilokeListingTools\MetaBoxes\EventPlan as WilokeMetaboxEventPlan;
use WilokeListingTools\MetaBoxes\ListingPlan as MetaBoxesListingPlan;
use WilokeListingTools\MetaBoxes\ListingCategory as MetaboxesListingCategory;
use WilokeListingTools\MetaBoxes\ListingLocation as MetaboxesListingLocation;
use WilokeListingTools\MetaBoxes\UserMeta as MetaboxesUserMeta;
use WilokeListingTools\MetaBoxes\ListingTag as MetaboxesListingTag;
use WilokeListingTools\MetaBoxes\Discount as WilokeDiscount;
use WilokeListingTools\MetaBoxes\Event as WilokeMetaboxEvent;
use WilokeListingTools\MetaBoxes\ClaimListing as WilokeClaimListing;
use WilokeListingTools\MetaBoxes\Review as MetaboxReview;
use WilokeListingTools\MetaBoxes\EventComment as MetaboxEventComment;
use WilokeListingTools\MetaBoxes\CustomFieldsForPostType as MetaboxCustomFieldsForPostType;
use WilokeListingTools\MetaBoxes\Report as MetaboxReport;
use WilokeListingTools\MetaBoxes\Promotion as MetaboxPromotion;
use WilokeListingTools\MetaBoxes\BookingComBannerCreator;
use WilokeListingTools\MetaBoxes\WooCommerce as MetaBoxWooCommerce;
use WilokeListingTools\MetaBoxes\Post as PostMetaBox;
use WilokeListingTools\MetaBoxes\Coupon as CouponMetaBox;

new CustomCMB2Fields;
new MetaBoxesListing;
new MetaboxesListingTag;
new WilokeMetaboxEventPlan;
new MetaBoxesListingPlan;
new MetaboxesListingCategory;
new MetaboxesListingLocation;
new MetaboxesUserMeta;
new WilokeDiscount;
new WilokeMetaboxEvent;
new WilokeClaimListing;
new MetaboxReview;
new MetaboxPromotion;
new MetaboxEventComment;
new MetaboxCustomFieldsForPostType;
new MetaboxReport;
new BookingComBannerCreator;
new MetaBoxWooCommerce;
new PostMetaBox;
new CouponMetaBox;

use WilokeListingTools\Register\RegisterReportSubmenu;
use WilokeListingTools\Register\RegisterPromotionPlans;
use WilokeListingTools\Register\RegisterClaimSubMenu;
use WilokeListingTools\Register\RegisterEventSettings;
use WilokeListingTools\Register\RegisterListingSetting;
use WilokeListingTools\Register\RegisterSettings;
use WilokeListingTools\Register\RegisterPostTypes;
use WilokeListingTools\Register\WilokeSubmission;
use WilokeListingTools\Register\RegisterInvoiceSubMenu;
use WilokeListingTools\Register\RegisterSaleSubMenu;
use WilokeListingTools\Register\RegisterSaleDetailSubMenu;
use WilokeListingTools\Register\RegisterSubscriptions;
use WilokeListingTools\Register\AddCustomPostType;
use WilokeListingTools\Register\RegisterSubmenuQuickSearchForm;
use WilokeListingTools\Register\RegisterMobileMenu;
use WilokeListingTools\Register\RegisterFirebaseNotification;
use WilokeListingTools\Register\RegisterImportExportWilokeTools;

// Front-end
use WilokeListingTools\Frontend\GenerateURL as GenerateURL;
//use WilokeListingTools\Frontend\EnqueueScripts as EnqueueScripts;
use WilokeListingTools\Controllers\BookingComController;

new GenerateURL;
//new EnqueueScripts;

require WILOKE_LISTING_TOOL_DIR . 'functions.php';

new RegisterSettings;
new RegisterClaimSubMenu;
new RegisterEventSettings;
new RegisterListingSetting;
new RegisterPostTypes;
new RegisterPromotionPlans;
new WilokeSubmission;
new RegisterSubmenuQuickSearchForm;
new RegisterMobileMenu;
new RegisterFirebaseNotification;

// Alter Table
use WilokeListingTools\AlterTable\AlterTableLatLng;
use WilokeListingTools\AlterTable\AlterTableBusinessHours;
use WilokeListingTools\AlterTable\AlterTableBusinessHourMeta;
use WilokeListingTools\AlterTable\AlterTablePaymentHistory;
use WilokeListingTools\AlterTable\AlterTablePaymentPlanRelationship;
use WilokeListingTools\AlterTable\AlterTablePaymentMeta;
use WilokeListingTools\AlterTable\AlterTableInvoices;
use WilokeListingTools\AlterTable\AlterTablePlanRelationships;
use WilokeListingTools\AlterTable\AlterTableReviewMeta;
use WilokeListingTools\AlterTable\AlterTableEventsData;
use WilokeListingTools\AlterTable\AlterTableMessage;
use WilokeListingTools\AlterTable\AlterTableFollower;
use WilokeListingTools\AlterTable\AlterTableFavoritesStatistic;
use WilokeListingTools\AlterTable\AlterTableViewStatistic;
use WilokeListingTools\AlterTable\AlterTableSharesStatistic;
use WilokeListingTools\AlterTable\AlterTableNotifications;
use WilokeListingTools\AlterTable\AlterTableInvoiceMeta;
use WilokeListingTools\Controllers\VerifyPurchaseCode;


if ( is_admin() ){
	new AlterTableBusinessHours;
	new AlterTableBusinessHourMeta;
	new AlterTableFollower;
	new AlterTableLatLng;
	new AlterTablePaymentHistory;
	new AlterTablePaymentMeta;
	new AlterTablePaymentPlanRelationship;
	new AlterTableInvoices;
	new AlterTablePlanRelationships;
	new AlterTableReviewMeta;
	new AlterTableEventsData;

	new RegisterInvoiceSubMenu;
	new RegisterSaleSubMenu;
	new RegisterSubscriptions;
	new RegisterSaleDetailSubMenu;
	new RegisterReportSubmenu;
	new RegisterImportExportWilokeTools;
	new AddCustomPostType;
	new AlterTableMessage;
	new AlterTableFavoritesStatistic;
	new AlterTableViewStatistic;
	new AlterTableSharesStatistic;
	new AlterTableNotifications;
	new AlterTableInvoiceMeta;

	new WilokeListingTools\Controllers\ChangePlanStatusController;
	new WilokeListingTools\Register\General;
	new WilokeListingTools\Controllers\TaxonomiesControllers;

//	new VerifyPurchaseCode;

	add_action('wp_ajax_wiloke_load_line_icon', function(){
		$content = file_get_contents(WILOKE_LISTING_TOOL_DIR . 'views/icons/line-icon.php');
		wp_send_json_success($content);
	});
}

use WilokeListingTools\Frontend\SingleListing;
new SingleListing;


// Payment
use WilokeListingTools\Controllers\AjaxUploadImgController;
use WilokeListingTools\Controllers\PayPalController;
use WilokeListingTools\Controllers\StripeController;
use WilokeListingTools\Controllers\DirectBankTransferController;
use WilokeListingTools\Controllers\PaymentStatusController;
use WilokeListingTools\Controllers\PaymentMetaController;
use WilokeListingTools\Controllers\UserPlanController;
use WilokeListingTools\Controllers\PlanRelationshipController;
use WilokeListingTools\Controllers\PostController;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Controllers\WooCommerceController;
use WilokeListingTools\Controllers\EventController;
use WilokeListingTools\Controllers\AddListingController;
use WilokeListingTools\Controllers\ListingController;
use WilokeListingTools\Controllers\ClaimController;
use WilokeListingTools\Controllers\CouponController;
use WilokeListingTools\Controllers\AddListingButtonController;
use WilokeListingTools\Controllers\AddMorePhotosVideosController;
use WilokeListingTools\Controllers\PromotionController;
use WilokeListingTools\Controllers\MessageController;
use WilokeListingTools\Controllers\AuthorPageController;
use WilokeListingTools\Controllers\FollowController;
use WilokeListingTools\Controllers\SearchFormController;
use WilokeListingTools\Controllers\FavoriteStatisticController;
use WilokeListingTools\Controllers\ViewStatisticController;
use WilokeListingTools\Controllers\SharesStatisticController;
use WilokeListingTools\Controllers\NotificationsController;
use WilokeListingTools\Controllers\ReportController;
use WilokeListingTools\Controllers\RegisterLoginController;
use WilokeListingTools\Controllers\SessionController;
use WilokeListingTools\Controllers\ProfileController;
use WilokeListingTools\Controllers\BillingControllers;
use WilokeListingTools\Controllers\EmailController;
use WilokeListingTools\Controllers\ContactFormController;
use WilokeListingTools\Controllers\InvoiceController;
use WilokeListingTools\Controllers\DashboardController;
use WilokeListingTools\Controllers\UserController;
use WilokeListingTools\Controllers\TermsAndPolicyController;
use WilokeListingTools\Controllers\IconController;
use WilokeListingTools\Controllers\GridItemController;
use \WilokeListingTools\Controllers\SchemaController;
use \WilokeListingTools\Controllers\PermalinksController;
use \WilokeListingTools\Controllers\DokanController;
use \WilokeListingTools\Controllers\WooCommerceBookingController;
use \WilokeListingTools\Controllers\NoticeController;
use \WilokeListingTools\Controllers\FreePlanController;
use \WilokeListingTools\Controllers\GoogleReCaptchaController;
use \WilokeListingTools\Controllers\OptimizeScripts;
use \WilokeListingTools\Controllers\NextBillingPaymentController;
use \WilokeListingTools\Controllers\RestaurantMenuController;
use \WilokeListingTools\Controllers\FacebookLoginController;

new IconController;
new AuthorPageController;
new PromotionController;
new MessageController;
new AddMorePhotosVideosController;
new AddListingButtonController;
new CouponController;
new PayPalController;
new StripeController;
new DirectBankTransferController;
new PaymentStatusController;
new PaymentMetaController;
new UserPlanController;
new PostController;
new PlanRelationshipController;
new WooCommerceController;
new ViewStatisticController;
new ReviewController;
new EventController;
new AddListingController;
new ListingController;
new ClaimController;
new FollowController;
new SearchFormController;
new FavoriteStatisticController;
new SharesStatisticController;
new NotificationsController;
new ReportController;
new RegisterLoginController;
new ProfileController;
new SessionController;
new BillingControllers;
new EmailController;
new ContactFormController;
new InvoiceController;
new DashboardController;
new UserController;
new TermsAndPolicyController;
new GridItemController;
new SchemaController;
new PermalinksController;
new BookingComController;
new AjaxUploadImgController;
new NoticeController;
new FreePlanController;
new GoogleReCaptchaController;
new OptimizeScripts;
new NextBillingPaymentController;
new RestaurantMenuController;
new FacebookLoginController;

new DokanController;
new WooCommerceBookingController();

// Schedule Registration
function wilokeListingToolsScheduleRegistration(){
	if (! wp_next_scheduled ( 'wilcity_check_event_status' )) {
		wp_schedule_event(time(), 'hourly', 'wilcity_check_event_status');
	}

	if (! wp_next_scheduled ( 'wilcity_daily_events' )) {
		wp_schedule_event(time(), 'daily', 'wilcity_daily_events');
	}
}
register_activation_hook(__FILE__, 'wilokeListingToolsScheduleRegistration');

// Single Widgets
//use WilokeListingTools\Shortcodes\SinglePriceRange;

do_action('wiloke-listing-tools/run-extension');
/*
 * @params: $aData: listingID, isNewListing, planID
 */

// Flush all search Cache
register_deactivation_hook( __FILE__, array('WilokeListingTools\Controllers\SearchFormController', 'flushSearchCache'));
