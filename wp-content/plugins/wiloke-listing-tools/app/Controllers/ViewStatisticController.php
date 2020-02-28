<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\HTML;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\ViewStatistic;

class ViewStatisticController extends Controller {

	public function __construct() {
		add_action('wp_ajax_wilcity_count_views', array($this, 'update'));
		add_action('wp_ajax_nopriv_wilcity_count_views', array($this, 'update'));

		add_action('wp_ajax_wilcity_views_latest_week', array($this, 'getViewsOfLatestWeek'));
		add_action('wp_ajax_wilcity_fetch_views_general', array($this, 'fetchViewsGeneral'));

		add_action('wp_ajax_wilcity_fetch_compare_views', array($this, 'fetchComparison'));

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/dashboard/(?P<postID>\d+)/compare-views', array(
				'methods' => 'GET',
				'callback' => array($this, 'getCompareViews')
			));
		});
	}

	public function getCompareViews($oData){
		$postID = $oData->get_param('postID');
		$aComparison  = ViewStatisticController::compare(get_post_field('post_author', $postID), $postID);

		if ($aComparison['number'] > 1 ){
			$aComparison['text'] = esc_html__('Views', 'wiloke-listing-tools');
		}else{
			$aComparison['text'] = esc_html__('View', 'wiloke-listing-tools');
		}

		return array('data'=>$aComparison);
	}

	public static function getViewsToday($userID){
		$today = Time::mysqlDate(\time());
		$viewToday = ViewStatistic::getTotalViewsOfAuthorInDay($userID, $today);
		return absint($viewToday);
	}

	public static function compare($authorID, $postID=null){
		$totalViews = ViewStatistic::getTotalViewsOfAuthor($authorID);

		$mondayThisWeek = Time::mysqlDate(strtotime('monday this week'));
		$sundayThisWeek = Time::mysqlDate(strtotime('sunday this week'));

		$mondayLastWeek = Time::mysqlDate(strtotime('monday last week'));
		$sundayLastWeek = Time::mysqlDate(strtotime('sunday last week'));

		$totalViewLastWeek = ViewStatistic::getTotalViewsInRange($authorID, $mondayLastWeek, $sundayLastWeek, $postID);
		$totalViewThisWeek = ViewStatistic::getTotalViewsInRange($authorID, $mondayThisWeek, $sundayThisWeek, $postID);
		$viewsToday = self::getViewsToday($authorID);
		//$totalViews += absint($totalViews) + $viewsToday;
		//$totalViewThisWeek += $viewsToday;

		$changing = $totalViewThisWeek - $totalViewLastWeek;
		$is = 'up';
		if( $changing == 0 ){
			$status = '';
		}else if ( $changing > 0 ){
			$status = 'green';
		}else{
			$status = 'red';
			$is = 'down';
		}

		return array(
			'total'=> $totalViews,
			'number'    => $changing,
			'status'    => $status,
			'is'        => $is
		);
	}

	public function fetchViewsGeneral(){
		$this->middleware(['isUserLoggedIn'], array());
		$userID = get_current_user_id();
		$aCompareViews = self::compare($userID);

		wp_send_json_success(
			array(
				'totalViews'        => $aCompareViews['total'],
				'oChanging'         => array(
					'number'        => $aCompareViews['number'],
					'description'   => esc_html__('Compared to the last week', 'wiloke-listing-tools'),
					'title'         => esc_html__('Post Reach', 'wiloke-listing-tools'),
					'status'        => $aCompareViews['status'],
					'is'            => $aCompareViews['is']
				)
			)
		);

	}

	public function getViewsOfLatestWeek(){
		$this->middleware(['isUserLoggedIn'], array());

		$aDateInThisWeek = Time::getAllDaysInThis();
		$aCountViewsOfWeek = array();
		$today = Time::mysqlDate(\time());
		$userID = get_current_user_id();

		foreach ($aDateInThisWeek as $date){
			if ( $today == $date ){
				$viewsToday = self::getViewsToday($userID);
				$aCountViewsOfWeek[] = $viewsToday;
			}else{
				$aCountViewsOfWeek[] = ViewStatistic::getTotalViewsOfAuthorInDay($userID, $date);
			}
		}

		$start = date(get_option('date_format'), strtotime($aDateInThisWeek['monday']));
		$end = date(get_option('date_format'), strtotime(end($aDateInThisWeek)));

		wp_send_json_success(array(
			'data'  => $aCountViewsOfWeek,
			'range' => $start . ' - ' . $end
		));
	}

	public static function getViews($postID, $isRestyleText=false){
//		$viewStatistic = FileSystem::fileGetContents(self::$cacheFile);
//		if ( empty($viewStatistic) ){
//			$today = Time::mysqlDate(\time());
//			$aViewStatistic = maybe_serialize($viewStatistic);
//			$countViewsToday = isset($aViewStatistic[$today]) ? abs($aViewStatistic[$today]) : 1;
//		}else{
//			$countViewsToday = 1;
//		}

		$countViews = ViewStatistic::countViews($postID);

		if ( empty($countViews) ){
			return !$isRestyleText ? 1 : HTML::reStyleText($countViews);
		}

		$totalViewed = abs($countViews);
		return !$isRestyleText ? $totalViewed : HTML::reStyleText($totalViewed);
	}

	public function update(){
		$this->middleware(['isPublishedPost'], array(
			'postID'    => $_POST['postID']
		));
		$postID = abs($_POST['postID']);
		if ( $countViewToday = ViewStatistic::countViewsInDay($postID, Time::mysqlDate()) ){
			ViewStatistic::update($postID);
		}else{
			ViewStatistic::insert($postID, 1);
		}

		$postViewed = GetSettings::getPostMeta($postID, 'count_viewed');
		$postViewed = absint($postViewed) + 1;
		SetSettings::setPostMeta($postID, 'count_viewed', $postViewed);

		wp_send_json_success($countViewToday+1);
	}
}