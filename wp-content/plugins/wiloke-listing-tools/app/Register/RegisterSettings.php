<?php
namespace WilokeListingTools\Register;

use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;

class RegisterSettings {
	use ListingToolsGeneralConfig;
	use GetAvailableSections;
	use ParseSection;

	public static $slug = 'wiloke-listing-tools';
	protected $usedSectionsKey = 'wiloke_lt_addlisting_sections';
	protected $aUsedSections = array();
	protected $aAvailableSections = array();
	protected $aReviewSettings = array();

	protected $aAllSections = array();
	protected $designSingleListingsKey = 'wiloke_lt_design_single_listing_tab';
	protected $aDefaultUsedSections;
	protected $isResetDefault=false;
	protected $oPredis;
	protected $aCustomPostTypes;
	protected $aCustomPostTypesKey;
	protected $aSingleNav;
	protected $aSidebarUsedSections;
	protected $aSidebarAvailableSections;
	protected $aSidebarAllSections;
	protected $aSearchUsedFields;
	protected $aAvailableSearchFields;

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_footer', array($this, 'footerCode'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_install_submission_pages', array($this, 'installSubmissionPages'));
		add_action('admin_notices', array($this, 'deleteUnpaidListingIsEnabling'));
		add_action('admin_notices', array($this, 'makeSureThatBillingTypeIsSetToRecurring'));
	}

	public function deleteUnpaidListingIsEnabling(){
		$autoDeleteListingAfter = GetWilokeSubmission::getField('delete_listing_conditional');
		if ( !empty($autoDeleteListingAfter) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p>Warning: You are enabling <strong>Automatically Delete Unpaid Listing</strong> feature. Which means an Unpaid Listing will be deleted automatically after <?php echo $autoDeleteListingAfter; ?> days from submitted day. To disable this feature, please click on <strong>Wiloke Submission -> Automatically Delete Unpaid Listing</strong> -> Leave it to empty</p>
			</div>
			<?php
		}
	}

	/*
	 * If WooCommerce Subscription is enabling, We should remind customers that they need to enable Recurring Payment
	 * on Wiloke Submission as well
	 *
	 * @since 1.2.0
	 */
	public function makeSureThatBillingTypeIsSetToRecurring(){
		if ( class_exists('\WC_Subscriptions_Coupon') ){
			if ( GetWilokeSubmission::isNonRecurringPayment() ){
				?>
				<div class="notice notice-warning is-dismissible">
					<p>Warning: <strong>WooCommerce Subscription</strong> is enabling. If you want to use <strong>Recurring Add Listing Payment</strong>, please click on <strong>Wiloke Submission -> Billing Type</strong> -> Select <strong>Recurring Payment (Subscription)</strong> mode.</p>
				</div>
				<?php
			}
		}
	}

	public function installSubmissionPages(){
		if ( !current_user_can('edit_theme_options') ){
			wp_send_json_error(array(
				array(
					'msg' => 'You do not have permission to access this page.',
					'status' => 'error'
				)
			));
		}

		$aConfigs = wilokeListingToolsRepository()->get('submission-pages');

		$aResponse = array();
		$aWilokeSubmission = GetWilokeSubmission::getAll();
		$hasUpdated = false;

		foreach ($aConfigs as $aPage){
			$check = isset($aWilokeSubmission[$aPage['key']]) ? $aWilokeSubmission[$aPage['key']] : '';
			if ( !empty($check) ){
				if ( get_post_status($check) == 'publish' ){
					continue;
				}
			}

			$postID = wp_insert_post(array(
				'post_title'    => $aPage['title'],
				'post_content'  => $aPage['content'],
				'post_status'   => 'publish',
				'post_type'     => 'page'
			));

			if ( empty($postID) || is_wp_error($postID) ){
				$aResponse[] = array(
					'status' => 'error',
					'msg'    => 'We could not create '.$aPage['title']
				);
			}else{
				if ( !empty($aPage['template']) ){
					update_post_meta($postID,'_wp_page_template', $aPage['template']);
				}
				$aWilokeSubmission[$aPage['key']] = $postID;
				$hasUpdated = true;

				$aResponse[] = array(
					'status' => 'success',
					'msg'    => $aPage['title'] . ' has been installed success fully'
				);
			}
		}

		$aResponse[] = array(
			'status' => 'success',
			'msg'    => 'Congratulations! The Wiloke Submission Pages have been installed successfully!'
		);

		if ( $hasUpdated ){
			update_option('wiloke_submission_configuration', maybe_serialize($aWilokeSubmission));
		}

		wp_send_json_success($aResponse);
	}

	public function footerCode(){
		if ( !isset($_REQUEST['page']) || strpos($_REQUEST['page'], $this->parentSlug) == -1 ){
			return '';
		}

		Inc::file('footer:icon-model');
	}

	public function removeFields($aSettings){
		if ( !General::isPostType('event') ){
			return $aSettings;
		}
	}

	public function enqueueScripts($hook){
		if ( !$this->matchedSlug($hook) ){
			return false;
		}
		$this->requiredScripts();
		wp_enqueue_script('wiloke-listing-tools', WILOKE_LISTING_TOOL_URL . 'admin/source/js/listing-tools.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
	}

	public function settingsArea(){
		Inc::file('general:index');
	}

	public function register(){
		add_menu_page( 'Wiloke Tools', 'Wiloke Tools', 'edit_theme_options', self::$slug, array($this, 'settingsArea'), '', 25);

		do_action('wilcity/wiloke-listing-tools/register-menu', $this);
	}
}