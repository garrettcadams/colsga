<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Framework\Helpers\General;

class PostTypes {
	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/post-types', array(
				'methods' => 'GET',
				'callback' => array($this, 'getPostTypes'),
			));
		});
	}

	public function getPostTypes(){
		$aPostTypes = General::getPostTypes(false);
		$aResponse = array();

		foreach ($aPostTypes as $postType => $aData){
			$aData['rest_base']   = $postType.'s';
			$oCountPosts          = wp_count_posts($postType);
			$aData['found_posts'] = abs($oCountPosts->publish);
			$aResponse[]          = $aData;
		}
		return array(
			'oResults'  => $aResponse,
			'status'    => 'success'
		);
	}
}