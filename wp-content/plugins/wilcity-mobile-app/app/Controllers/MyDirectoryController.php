<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Controllers\DashboardController;
use WilokeListingTools\Controllers\EventController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;

class MyDirectoryController {
	use JsonSkeleton;
	use VerifyToken;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/get-my-listings', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'getMyListings' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/get-listing-status', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'getPostStatus' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/get-listing-types', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'getListingTypes' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/get-event-status', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'getEventStatus' )
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/get-my-events', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'getMyEvents' )
			) );
		} );
	}

	public function getEventStatus(){
		$aEventStatus = EventController::getEventStatuses(false);
		foreach ($aEventStatus as $order => $aInfo){
			$aEventStatus[$order]['total'] = DashboardController::countPostStatus($aInfo['post_status'], 2);
		}

		return array(
			'oResults' => array_values($aEventStatus),
			'status'   => 'success'
		);
	}

	public function getListingTypes(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();

		$aCustomPostTypes = GetSettings::getFrontendPostTypes(false, true);

		foreach ($aCustomPostTypes as $order => $aPostType){
			$aCount = User::countUserPosts($oToken->userID, $aPostType['key']);
			$aCustomPostTypes[$order]['total'] = abs($aCount['total']);
		}

		return array(
			'oResults' => array_values($aCustomPostTypes),
			'status'   => 'success'
		);
	}

	public function getPostStatus(){
		$aTranslation = wilcityAppGetLanguageFiles();

		$aPostStatus = $aTranslation['aPostStatus'];
		$aPostStatus = apply_filters('wilcity/dashboard/general-listing-status-statistic', $aPostStatus);

		foreach ($aPostStatus as $order => $aInfo){
			$aPostStatus[$order]['total'] = DashboardController::countPostStatus($aInfo['post_status']);
		}

		return array(
			'status'    => 'success',
			'oResults'  => $aPostStatus
		);
	}

	protected function getListings($aArgs){
		$query = new \WP_Query($aArgs);

		if ( !$query->have_posts() ){
			wp_reset_postdata();
			return array(
				'status' => 'error',
				'msg'    => 'doNotHaveAnyArticleYet'
			);
		}

		$aListings = array();

		while ($query->have_posts()){
			$query->the_post();
			$aListings[] = $this->listingSkeleton($query->post);
		}

		if ( $aArgs['paged'] < $query->max_num_pages ){
			$next = $aArgs['paged']+1;
		}else{
			$next = false;
		}

		return array(
			'status'   => 'success',
			'oResults' => $aListings,
			'maxPages' => $query->max_num_pages,
			'next'     => $next
		);
	}

	public function getMyListings(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}

		$oToken->getUserID();

		$aListings = General::getPostTypeKeys(false, true);
		if ( !isset($_GET['postType']) || ( !empty($_GET['postType']) && $_GET['postType'] != 'all' && !in_array($_GET['postType'], $aListings) ) ){
			return array(
				'status' => 'error',
				'msg'    => 403
			);
		}

		if ( empty($_GET['postType']) || $_GET['postType'] == 'all' ){
			$postType = array_filter($aListings, function($type){
				return $type != 'event';
			});
		}else{
			$postType   = $_GET['postType'];
		}

		$postStatus = isset($_GET['postStatus']) ? $_GET['postStatus'] : 'publish';
		$page       = isset($_GET['page']) ? $_GET['page'] : 1;
		$postsPerPage   = isset($_GET['postsPerPage']) ? $_GET['postsPerPage'] : 10;
		$postsPerPage = $postsPerPage > 100 ? 10 : $postsPerPage;

		if ( $postType == 'all' || empty($postType) ){
			$postType = GetSettings::getFrontendPostTypes(true);
			$postType = array_filter($postType, function($type){
				return $type != 'key';
			});
		}

		return $this->getListings(array(
			'post_type'     => $postType,
			'post_status'   => $postStatus,
			'posts_per_page'=> $postsPerPage,
			'paged'          => $page,
			'author'        => $oToken->userID
		));

	}

	public function getMyEvents(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}

		$oToken->getUserID();

		$postStatus = isset($_GET['postStatus']) ? $_GET['postStatus'] : 'publish';
		$page       = isset($_GET['page']) ? $_GET['page'] : 1;
		$postsPerPage   = isset($_GET['postsPerPage']) ? $_GET['postsPerPage'] : 10;
		$postsPerPage = $postsPerPage > 100 ? 10 : $postsPerPage;

		return $this->getListings(array(
			'post_type'     => 'event',
			'post_status'   => $postStatus,
			'posts_per_page'=> $postsPerPage,
			'paged'         => $page,
			'author'        => $oToken->userID
		));
	}
}
