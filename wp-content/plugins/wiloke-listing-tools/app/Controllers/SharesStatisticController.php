<?php
namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\HTML;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\SharesStatistic;

class SharesStatisticController extends Controller {

	public function __construct() {
		add_action('wp_ajax_wilcity_count_shares', array($this, 'update'));
		add_action('wp_ajax_nopriv_wilcity_count_shares', array($this, 'update'));

		add_action('wp_ajax_wilcity_shares_latest_week', array($this, 'getSharesOfLatestWeek'));
		add_action('wp_ajax_wilcity_fetch_shares_general', array($this, 'fetchSharesGeneral'));

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/dashboard/(?P<postID>\d+)/compare-shares', array(
				'methods' => 'GET',
				'callback' => array($this, 'getCompareShare')
			));
		});
	}

	public function getCompareShare($oData){
		$postID = $oData->get_param('postID');
		$aComparison  = self::compare(get_post_field('post_author', $postID), $postID);

		if ( $aComparison['number'] > 1 ){
			$aComparison['text'] = esc_html__('Shares', 'wiloke-listing-tools');
		}else{
			$aComparison['text'] = esc_html__('Share', 'wiloke-listing-tools');
		}

		return array('data'=>$aComparison);
	}

	public static function getShareToday($userID){
		$today = Time::mysqlDate(\time());
		$shareToday = SharesStatistic::getTotalSharesOfAuthorInDay($userID, $today);
		if ( empty($shareToday) ){
			$shareToday = 0;
		}
		return 0;
	}

	public static function compare($authorID, $postID=null){
		$totalShares = SharesStatistic::getTotalSharesOfAuthor($authorID);

		$mondayThisWeek = Time::mysqlDate(strtotime('monday this week'));
		$sundayThisWeek = Time::mysqlDate(strtotime('sunday this week'));

		$mondayLastWeek = Time::mysqlDate(strtotime('monday last week'));
		$sundayLastWeek = Time::mysqlDate(strtotime('sunday last week'));

		$totalSharesLastWeek = SharesStatistic::getTotalSharesInRange($authorID, $mondayLastWeek, $sundayLastWeek, $postID);
		$totalSharesThisWeek = SharesStatistic::getTotalSharesInRange($authorID, $mondayThisWeek, $sundayThisWeek, $postID);

		$shareToday = self::getShareToday($authorID);
		$totalShares += absint($totalShares) + $shareToday;
		$totalSharesThisWeek = $totalSharesThisWeek + $shareToday;

		$changing = $totalSharesThisWeek - $totalSharesLastWeek;

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
			'total'     => $totalShares,
			'number'    => $changing,
			'status'    => $status,
			'is'        => $is
		);
	}

	public function fetchSharesGeneral(){
		$this->middleware(['isUserLoggedIn'], array());
		$userID = get_current_user_id();
		$aCompare = self::compare($userID);

		wp_send_json_success(
			array(
				'totalShares'=> $aCompare['total'],
				'oChanging'         => array(
					'number'        => $aCompare['number'],
					'description'   => esc_html__('Compared to the last week', 'wiloke-listing-tools'),
					'status'        => $aCompare['status'],
					'is'            => $aCompare['is']
				)
			)
		);

	}

	public function getSharesOfLatestWeek(){
		$this->middleware(['isUserLoggedIn'], array());

		$aDateInThisWeek = Time::getAllDaysInThis();
		$aCountViewsOfWeek = array();
		$today = Time::mysqlDate(\time());
		$userID = get_current_user_id();

		foreach ($aDateInThisWeek as $date){
			if ( $today == $date ){
				$shareToday = self::getShareToday($userID);
				$aCountViewsOfWeek[] = $shareToday;
			}else{
				$aCountViewsOfWeek[] = SharesStatistic::getTotalSharesOfAuthorInDay($userID, $date);
			}
		}

		$start = date(get_option('date_format'), strtotime($aDateInThisWeek['monday']));
		$end = date(get_option('date_format'), strtotime(end($aDateInThisWeek)));

		wp_send_json_success(array(
			'data'  => $aCountViewsOfWeek,
			'range' => $start . ' - ' . $end
		));
	}

	public static function renderShared($postID, $hasDecoration=true){
		$countShared = GetSettings::getPostMeta($postID, 'count_shared');
		$countShared = abs($countShared);

		if ( !$hasDecoration ){
			return $countShared;
		}
		$countShared = empty($countShared) ? 0 : $countShared;

		echo '<span class="wilcity-count-shared-'.esc_attr($postID).'">'. $countShared  . ' ' . esc_html__('Shared', 'wiloke-listing-tools') . '</span>';
	}

	public function update(){
		$this->middleware(['isPublishedPost'], array(
			'postID'    => $_POST['postID']
		));
		$postID = abs($_POST['postID']);

		if ( $countSharedToday = SharesStatistic::countSharesToday($postID, Time::mysqlDate()) ){
			SharesStatistic::update($postID);
		}else{
			SharesStatistic::insert($postID, 1);
		}

		$postShared = GetSettings::getPostMeta($postID, 'count_shared');
		$postShared = absint($postShared) + 1;
		SetSettings::setPostMeta($postID, 'count_shared', $postShared);

		wp_send_json_success(array(
			'countShared' => $postShared,
			'text'  => esc_html__('Shared', 'wiloke-listing-tools')
		));
	}
}