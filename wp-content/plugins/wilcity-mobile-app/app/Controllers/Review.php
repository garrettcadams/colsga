<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Controllers\ShareController;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;


class Review{
	use JsonSkeleton;
	use VerifyToken;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX . '/v2', '/post-review', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'postReview' )
			) );
		} );
	}

	public function postReview(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getPayLoad();

		if ( !$this->oPayLoad ){
			return array(
				'status' => 'error',
				'msg'    => 403
			);
		}

		return array(
			'status' => 'success',
			'msg' => 'success'
		);
	}
}
