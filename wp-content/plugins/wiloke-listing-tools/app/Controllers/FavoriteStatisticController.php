<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\HTML;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\FavoriteStatistic;

class FavoriteStatisticController extends Controller {
	public function __construct() {
		add_action('wp_ajax_wilcity_favorite_statistics', array($this, 'listenAjaxUpdate'));
		add_action('wp_ajax_nopriv_wilcity_favorite_statistics', array($this, 'listenAjaxUpdate'));

		add_action('wp_ajax_wilcity_favorites_latest_week', array($this, 'getFavoritesOfLatestWeek'));
		add_action('wp_ajax_wilcity_fetch_favorites_general', array($this, 'fetchFavoritesGeneral'));
		add_action('wp_ajax_wilcity_fetch_my_favorites', array($this, 'fetchMyFavorites'));
		add_action('wp_ajax_wilcity_remove_favorite_from_my_list', array($this, 'removeFavoritesFromMyList'));

//		add_action('wp_ajax_wilcity_fetch_compare_favorites', array($this, 'fetchComparison'));
//		add_action('wp_ajax_wilcity_fetch_user_liked', array($this, 'fetchUserLiked'));

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/users/(?P<userID>\d+)/liked', array(
				'methods' => 'GET',
				'callback' => array($this, 'getUserLiked')
			));

			register_rest_route( WILOKE_PREFIX.'/v2', '/dashboard/(?P<postID>\d+)/compare-favorites', array(
				'methods' => 'GET',
				'callback' => array($this, 'getCompareFavorites')
			));
		});
	}

	public function getCompareFavorites($oData){
		$postID = $oData->get_param('postID');

		$aComparison = self::compare(get_post_field('post_author', $postID), $postID);
		if ( $aComparison['number'] > 1 ){
			$aComparison['text'] = esc_html__('Favorites', 'wiloke-listing-tools');
		}else{
			$aComparison['text'] = esc_html__('Favorite', 'wiloke-listing-tools');
		}
		return array('data'=>$aComparison);
	}

	public function removeFavoritesFromMyList(){
		$aRawFavorites = GetSettings::getUserMeta(get_current_user_id(), 'my_favorites');
		array_splice($aRawFavorites, $_POST['id'], 1);
		SetSettings::setUserMeta(get_current_user_id(), 'my_favorites', $aRawFavorites);
	}

	public static function getFavoritesByPage($aRawFavorites, $page){
		$limit = 20;
		$offset = $limit*$page;
		$total = count($aRawFavorites);
		$aRawFavorites = array_reverse($aRawFavorites);
		$aFavorites = array_splice($aRawFavorites, $offset, $limit);

		if ( empty($aFavorites) ){
			return array(
				'reachedMaximum' => 'yes'
			);
		}

		$aListings = array();
		foreach ($aFavorites as $id => $postID){
			if ( get_post_status($postID) != 'publish' ){
				unset($aFavorites[$id]);
				continue;
			}

			$aData = array(
				'postID'   => $postID,
				'order'    => $id,
				'postLink' => get_permalink($postID),
				'postTitle'=> get_the_title($postID),
				'tagLine'  => GetSettings::getTagLine($postID, true),
				'thumbnail'=> GetSettings::getFeaturedImg($postID, 'thumbnail'),
				'address'  => GetSettings::getAddress($postID, false),
				'mapPage'  => GetSettings::getAddress($postID, true)
			);

			if ( get_post_type($postID) == 'post' ){
				$oRawCat = GetSettings::getLastPostTerm($postID, 'category');
				if ( $oRawCat ){
					$aData['oCategory'] = array(
						'link' => get_term_link($oRawCat->term_id),
						'name' => $oRawCat->name,
						'oIcon' => 'no'
					);
				}
			}else{
				$oRawCat = GetSettings::getLastPostTerm($postID, 'listing_cat');
				if ( $oRawCat ){
					$aData['oCategory'] = array(
						'link' => get_term_link($oRawCat->term_id),
						'name' => $oRawCat->name,
						'oIcon' => \WilokeHelpers::getTermOriginalIcon($oRawCat)
					);
				}
			}


			if ( !isset($aData['oCategory']) ){
				$aData['oCategory'] = 'no';
			}

			$aListings[] = $aData;
		}

		return array(
			'aInfo'     => $aListings,
			'total'     => $total,
			'maxPages'  => ceil($total/$limit)
		);
	}

	public function fetchMyFavorites(){
		$aRawFavorites = GetSettings::getUserMeta(get_current_user_id(), 'my_favorites');

		if ( empty($aRawFavorites) ){
			wp_send_json_error(array(
				'msg' => esc_html__('There are no favorites', 'wiloke-listing-tools')
			));
		}

		$page = isset($_POST['page']) ? abs($_POST['page']) - 1 : 0;

		$aResult = self::getFavoritesByPage($aRawFavorites, $page);
		if ( isset($aResult['reachedMaximum']) ){
			wp_send_json_error($aResult);
		}
		wp_send_json_success($aResult);
	}

	public static function compare($authorID, $postID=null){
		$totalFavorites = FavoriteStatistic::getTotalFavoritesOfAuthor($authorID);
		$mondayThisWeek = Time::mysqlDate(strtotime('monday this week'));
		$sundayThisWeek = Time::mysqlDate(strtotime('sunday this week'));

		$mondayLastWeek = Time::mysqlDate(strtotime('monday last week'));
		$sundayLastWeek = Time::mysqlDate(strtotime('sunday last week'));

		$totalFavoritesLastWeek = FavoriteStatistic::getTotalFavoritesInRange($authorID, $mondayLastWeek, $sundayLastWeek, $postID);
		$totalFavoritesThisWeek = FavoriteStatistic::getTotalFavoritesInRange($authorID, $mondayThisWeek, $sundayThisWeek, $postID);
		$changing          = $totalFavoritesThisWeek - $totalFavoritesLastWeek;

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
			'total'=> $totalFavorites,
			'number'        => $changing,
			'status'        => $status,
			'is'            => $is
		);
	}

	public function fetchFavoritesGeneral(){
		$this->middleware(['isUserLoggedIn'], array());
		$userID = get_current_user_id();

		$aCompareFavorites = self::compare($userID);

		wp_send_json_success(
			array(
				'totalFavorites'=> $aCompareFavorites['total'],
				'oChanging'         => array(
					'number'        => $aCompareFavorites['number'],
					'description'   => esc_html__('Compared to the last week', 'wiloke-listing-tools'),
					'title'         => esc_html__('Favorites', 'wiloke-listing-tools'),
					'status'        => $aCompareFavorites['status'],
					'is'            => $aCompareFavorites['is']
				)
			)
		);

	}

	public function getFavoritesOfLatestWeek(){
		$this->middleware(['isUserLoggedIn'], array());

		$aDateInThisWeek = Time::getAllDaysInThis();
		$aCountViewsOfWeek = array();
		$userID = get_current_user_id();

		foreach ($aDateInThisWeek as $date){
			$aCountViewsOfWeek[] = FavoriteStatistic::getTotalFavoritesOfAuthorInDay($userID, $date);
		}

		$start = date(get_option('date_format'), strtotime($aDateInThisWeek['monday']));
		$end = date(get_option('date_format'), strtotime(end($aDateInThisWeek)));

		wp_send_json_success(array(
			'data'  => $aCountViewsOfWeek,
			'range' => $start . ' - ' . $end
		));
	}

	public static function getFavorites($postID, $isRestyleText=false){
		$today = Time::mysqlDate(\time());
		$countViewsToday = FavoriteStatistic::getTotalFavoritesOfAuthorInDay(get_current_user_id(), $today);

		$countViews = FavoriteStatistic::countFavorites($postID);

		if ( empty($countViews) ){
			return !$isRestyleText ? $countViewsToday : HTML::reStyleText($countViews);
		}

		$totalViewed = $countViewsToday + abs($countViews);
		return !$isRestyleText ? $totalViewed : HTML::reStyleText($totalViewed);
	}

	public function fetchUserLiked(){
		$userID = get_current_user_id();
		$aLiked = GetSettings::getUserMeta($userID, 'my_favorites');
		if ( empty($aLiked) ){
			wp_send_json_error();
		}else{
			wp_send_json_success($aLiked);
		}
	}

	public function getUserLiked($oInfo){
		$aError = array(
			'error' => array(
				'internalMessage' => 'User does not exist',
				'status' => 404
			)
		);

		$userID = $oInfo->get_param('userID');
		if ( empty($userID) ){
			return $aError;
		}
		$aLiked = GetSettings::getUserMeta($userID, 'my_favorites');

		if ( empty($aLiked) ){
			$aError['error']['internalMessage'] = 'No Like';
			return $aError;
		}

		return array('data'=>$aLiked);
	}

	public static function update($postID, $userID=''){
		$countFavorites = GetSettings::getPostMeta($postID, 'count_favorites');
		$countFavorites = empty($countFavorites) ? 0 : abs($countFavorites);
		$userID = empty($userID) ? get_current_user_id() : $userID;

		if ( empty($userID) ){
			return false;
		}

		$aFavorites = GetSettings::getUserMeta($userID, 'my_favorites');
		if ( empty($aFavorites) ){
			SetSettings::setUserMeta($userID, 'my_favorites', [$postID]);
			$countFavorites++;
			$is = 'added';
			$isPlus = true;
		}else{
			if ( in_array($postID, $aFavorites) ){
				$key = array_search($postID, $aFavorites);
				unset($aFavorites[$key]);
				if ( empty($aFavorites) ){
					SetSettings::deleteUserMeta($userID, 'my_favorites');
				}else{
					SetSettings::setUserMeta($userID, 'my_favorites', $aFavorites);
				}
				$countFavorites--;
				$is = 'removed';
				$isPlus = false;
			}else{
				$countFavorites++;
				$aFavorites[] = $postID;
				SetSettings::setUserMeta($userID, 'my_favorites', $aFavorites);
				$is = 'added';
				$isPlus = true;
			}
		}
		FavoriteStatistic::update($postID, $isPlus);
		SetSettings::setPostMeta($postID, 'count_favorites', $countFavorites);
		return $is;
	}

	public function listenAjaxUpdate(){
		$this->middleware(['isUserLoggedIn', 'isPublishedPost', 'isThemeOptionSupport'], array(
			'postID'    => $_POST['postID'],
			'feature'   => 'listing_toggle_favorite'
		));
		$postID = abs($_POST['postID']);
		$is = self::update($postID);
		wp_send_json_success($is);
	}
}