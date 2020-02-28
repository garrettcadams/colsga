<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Controllers\FavoriteStatisticController;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Models\ReviewMetaModel;

class FavoritesController{
	use VerifyToken;
	use JsonSkeleton;
	use ParsePost;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/get-my-favorites', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'getMyFavorites' ),
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/add-to-my-favorites', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'addToMyFavorites' ),
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/remove-from-my-favorites', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'addToMyFavorites' ),
			) );
		} );
	}

	public function addToMyFavorites(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();
		$aData = $this->parsePost();

		if ( !isset($aData['postID']) || empty($aData['postID']) ){
			return array(
				'status' => 'error',
				'msg'    => 403
			);
		}

		if ( !\WilokeThemeOptions::isEnable('listing_toggle_favorite') ){
			return array(
				'status' => 'error',
				'msg'    => 403
			);
		}

		if ( get_post_status($aData['postID']) !== 'publish' ){
			return array(
				'status' => 'error',
				'msg'    => 403
			);
		}

		$is = FavoriteStatisticController::update($aData['postID'], $oToken->userID);
		if ( $is === false ){
			return array(
				'status' => 'error',
				'msg'    => 403
			);
		}

		return array(
			'status' => 'success',
			'msg'    => 'Success',
			'is'     => $is
		);
	}

	public function getMyFavorites(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();

		$page = isset($_GET['page']) ? abs($_GET['page']) : 1;
		$aRawFavorites = GetSettings::getUserMeta($oToken->userID, 'my_favorites');
		if ( empty($aRawFavorites) ){
			return array(
				'status' => 'error',
				'msg'    => 'noFavorites'
			);
		}

		$aFavorites = FavoriteStatisticController::getFavoritesByPage($aRawFavorites, $page - 1);
		if ( isset($aFavorites['reachedMaximum']) ){
			return array(
				'status' => 'error',
				'msg'    => 'gotAllFavorites'
			);
		}

		$aListings = array();
		foreach ($aFavorites['aInfo'] as $order => $aListing){
			$aListing['logo'] = GetSettings::getLogo($aListing['postID'], 'full');

			$averageRating = GetSettings::getPostMeta($aListing['postID'], 'average_reviews');
			$postType      = get_post_type($aListing['postID']);

			$aListing['oReview'] = array(
				'mode'      => ReviewController::getMode($postType),
				'average'   => $averageRating,
				'quality'   => ReviewMetaModel::getReviewQualityString($averageRating, $postType)
			);

			unset($aListing['address']);
			unset($aListing['mapPage']);
			unset($aListing['oCategory']);
			$aListing['ID'] = $aListing['postID'];
			unset($aListing['postID']);
			$aListings[$order] = $aListing;
			$aListings[$order]['oFeaturedImg'] = $this->getFeaturedImg(get_post($aListing['ID']));

			unset($aListings[$order]['thumbnail']);
		}

		if ( $page < $aFavorites['maxPages'] ){
			$next = $page+1;
		}else{
			$next = false;
		}

		return array(
			'status'    => 'success',
			'oResults'  => $aListings,
			'total'     => $aFavorites['total'],
			'maxPages'  => $aFavorites['maxPages'],
			'next'      => $next
		);
	}

}
