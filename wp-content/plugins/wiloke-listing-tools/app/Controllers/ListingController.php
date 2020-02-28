<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\MetaBoxes\Listing;
use WilokeListingTools\Models\FavoriteStatistic;
use WilokeListingTools\Models\ListingModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PostModel;
use WilokeListingTools\Models\PromotionModel;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\SharesStatistic;
use WilokeListingTools\Models\ViewStatistic;

class ListingController extends Controller {
	use SingleJsonSkeleton;
	use SetCustomButton;

	public function __construct() {
		add_action('wp_ajax_wilcity_fetch_listings_json', array($this, 'fetchListingsJson'));
		add_action('wp_ajax_wilcity_fetch_general_data', array($this, 'fetchGeneralData'));
		add_action('wp_ajax_wilcity_load_more_listings', array($this, 'loadMoreListings'));
		add_action('wp_ajax_nopriv_wilcity_load_more_listings', array($this, 'loadMoreListings'));
//		add_action('wp_ajax_wilcity_button_settings', array($this, 'fetchButtonSettings'));

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/listings/(?P<postID>\d+)/button-settings', array(
				'methods' => 'GET',
				'callback' => array($this, 'getButtonSettings')
			));
		});


		add_action('wp_ajax_wilcity_save_page_button', array($this, 'saveButtonSettings'));
		add_action('wiloke-listing-tools/payment-succeeded', array($this, 'updateListingsToPublishAfterChangingPaymentStatus'));
	}

	public function updateListingsToPublishAfterChangingPaymentStatus($aResponse){
		if ( isset($aResponse['packageType']) && $aResponse['packageType'] == 'promotion' ){
			return false;
		}

		if ( !isset($aResponse['gateway']) || $aResponse['gateway'] != 'banktransfer' ){
			return false;
		}

		$planID = PaymentModel::getField('planID', $aResponse['paymentID']);
		$postID = PostModel::getLastListingIDByBelongsToPlanID($planID);

		if ( empty($postID) ){
			return false;
		}

		wp_update_post(
			array(
				'ID' => $postID,
				'post_status' => 'publish'
			)
		);
	}

	public function saveButtonSettings(){
		$this->middleware(['isPostAuthor'], [
			'postID' => $_POST['postID'],
			'postAuthor' => User::getCurrentUserID(),
			'passedIfAdmin' => 'yes'
		]);

		$aButtonSettings = $_POST['aButtons'];
		$data = array(
			'button_link'	=> '',
			'button_icon'	=> '',
			'button_name'	=> ''
		);

		if( isset( $aButtonSettings['buttonName'] ) ) {
			$data['button_name'] = $aButtonSettings['buttonName'];
		}

		if( isset( $aButtonSettings['websiteLink'] ) ) {
			$data['button_link'] = $aButtonSettings['websiteLink'];
		}

		if( isset( $aButtonSettings['button_icon'] ) ) {
			$data['button_icon'] = $aButtonSettings['icon'];
		}

		$this->setCustomButtonToListing($_POST['postID'], $data);

		wp_send_json_success(array(
			'msg' => esc_html__('Congratulations! The button settings have been updated.', 'wiloke-listing-tools')
		));
	}

	public function getButtonSettings($oData){
		$postID = $oData->get_param('postID');
		$aSettings['buttonName'] = GetSettings::getPostMeta($postID, 'button_name');
		$aSettings['websiteLink'] = GetSettings::getPostMeta($postID, 'button_link');
		$aSettings['icon'] = GetSettings::getPostMeta($postID, 'button_icon');

		return array(
			'data' => $aSettings
		);
	}

	public static function renderClaimStatus($postID){
		return PostModel::isClaimed($postID) ? esc_html__('Claimed', 'wiloke-listing-tools') : '';
	}

	public function fetchGeneralData(){
		$aRawPostTypes = GetSettings::getFrontendPostTypes();

		$aPostTypes = array();
		$aPostStatus = General::getPostsStatus();

		$aCountPosts = array();
		foreach ($aRawPostTypes as $aOption){
			if ( $aOption['key'] == 'event' ){
				continue;
			}
			$aPostTypes[] = array(
				'name' => $aOption['singular_name'],
				'value'=> $aOption['key']
			);

			$aCountPosts[$aOption['key']] = User::countUserPosts(get_current_user_id(), $aOption['key']);
		}

		wp_send_json_success(array(
			'oPostTypes' => $aPostTypes,
			'aCountPosts'=> $aCountPosts,
			'aPostStatus'=> $aPostStatus
		));
	}

	public function loadMoreListings(){
		$page  = isset($_POST['page']) ? abs($_POST['page']) : 2;
		$aPostTypeKeys = General::getPostTypeKeys(true);

		if ( !in_array($_POST['postType'], $aPostTypeKeys) ){
			wp_send_json_error(array(
				'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
			));
		}

		$aData = array();
		foreach ($_POST as $key => $val){
			$aData[$key] = sanitize_text_field($val);
		}

		$query = new \WP_Query(
			array(
				'post_type'         => $aData['postType'],
				'posts_per_page'    => $aData['postsPerPage'],
				'paged'             => $page,
				'post_status'       => 'publish'
			)
		);

		if ( $query->have_posts() ){
			ob_start();
			while ($query->have_posts()){
				$query->the_post();
				wilcity_render_grid_item($query->post, array(
					'img_size'                   => $aData['img_size'],
					'maximum_posts_on_lg_screen' => $aData['maximum_posts_on_lg_screen'],
					'maximum_posts_on_md_screen' => $aData['maximum_posts_on_md_screen'],
					'maximum_posts_on_sm_screen' => $aData['maximum_posts_on_sm_screen'],
					'style' => 'grid',
				));
			}
			$contents = ob_get_contents();
			ob_end_clean();
			wp_send_json_success(array('msg'=>$contents));
		}else{
			wp_send_json_error(
				array(
					'msg' => sprintf(esc_html__('Oops! Sorry, We found no %s', 'wiloke-listing-tools'), $aData['postType'])
				)
			);
		}
	}

	public function fetchListingsJson(){
		$aArgs = array(
			'post_type'     => $_POST['postType'],
			'post_status'   => $_POST['postStatus'],
			'posts_per_page'=> 10,
			'paged'         => isset($_POST['page']) ? abs($_POST['page']) : 1,
			'author'        => User::getCurrentUserID()
		);
		if ( isset($_POST['s']) && !empty($_POST['s']) ){
			$aArgs['s'] = trim($_POST['s']);
		}
		$query = new \WP_Query($aArgs);

		if ( !$query->have_posts() ){
			wp_reset_postdata();

			wp_send_json_error(array(
				'msg' => esc_html__('You do not have any listing yet.', 'wiloke-listing-tools'),
			));
		}

		$aListings = array();

		$reviewMode = GetSettings::getOptions(General::getReviewKey('mode', $_POST['postType']));
		$reviewMode = empty($reviewMode) ? 5 : $reviewMode;

		while ($query->have_posts()){
			$query->the_post();
			$aListing = $this->json($query->post);
			$aListing['oReview'] = array(
				'mode' => $reviewMode,
				'average' => GetSettings::getAverageRating($query->post->ID)
			);
			$aListings[] = $aListing;
		}

		wp_send_json_success(array(
			'listings' => $aListings,
			'maxPages' => $query->max_num_pages
		));
	}
}
