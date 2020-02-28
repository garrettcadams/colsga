<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Controllers\SearchFormController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

class NearByMe {
	use JsonSkeleton;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/nearbyme', array(
				'methods' => 'GET',
				'callback' => array($this, 'getNearByMe'),
			));
		} );
	}

	public function getNearByMe($aData){
		$aQuery = $aData;
		$aQuery['postType'] = isset($aData['postType']) ? $aData['postType'] : General::getPostTypeKeys(false, false);
		$aQuery['page'] = isset($aData['page']) ? abs($aData['page']) : 1;

		if ( !isset($aData['postsPerPage']) || (abs($aData['postsPerPage']) > 100) ){
			$aData['postsPerPage'] = 18;
		}

		if ( isset($aData['lat']) && isset($aData['lng']) ){
			$aQuery['oAddress'] = array(
				'lat'       => trim($aData['lat']),
				'lng'       => trim($aData['lng']),
				'radius'    => isset($aData['radius']) ? abs($aData['radius']) : \WilokeThemeOptions::getOptionDetail('default_radius'),
				'unit'      => isset($aData['unit']) ? $aData['unit'] : 'km'
			);
			if ( empty($aQuery['oAddress']['radius']) ){
				$aQuery['oAddress']['radius'] = 10;
			}
		}else{
			return array(
				'status' => 'error',
				'msg'    => esc_html__('No Posts Found',  'wilcity-mobile-app')
			);
		}

		$aArgs = SearchFormController::buildQueryArgs($aQuery);
		$query = new \WP_Query($aArgs);
		if ( $query->have_posts() ){
			$aPosts = array();
			while ($query->have_posts()){
				$query->the_post();
				$aPosts[] = $this->listingSkeleton($query->post);
			}

			$aReturn['status'] = 'success';
			if ( $aQuery['page'] < $query->max_num_pages ){
				$aReturn['next'] = $nextPage = $aQuery['page']+1;
			}else{
				$aReturn['next'] = false;
			}
			$aReturn['oResults'] = $aPosts;
			return $aReturn;
		}else{
			return array(
				'status' => 'error',
				'msg'    => esc_html__('No Posts Found',  'wilcity-mobile-app')
			);
		}
	}
}
