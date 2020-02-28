<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Routing\Controller;

class ShareController extends Controller {
	public function __construct() {
		add_action('wp_ajax_wilcity_count_shared', array($this, 'saveShared'));
		add_action('wp_ajax_nopriv_wilcity_count_shared', array($this, 'saveShared'));
	}

	public static function renderCountShared($countShared){
		return $countShared  . ' ' . esc_html__('Shared', 'wiloke-listing-tools') ;
	}

	public static function countShared($postID, $hasDecoration=true){
		$countShared = GetSettings::getPostMeta($postID, 'shared');
		$countShared = empty($countShared) ? 0 : abs($countShared);

		if ( !$hasDecoration ){
			return $countShared;
		}

		echo '<span class="wilcity-count-shared-'.esc_attr($postID).'">'. self::renderCountShared($countShared) . '</span>';
	}

	public function saveShared(){
		$this->middleware(['isPublishedPost'], array(
			'postID'=>$_POST['postID']
		));

		$countShared = GetSettings::getPostMeta($_POST['postID'], 'shared');
		$countShared = empty($countShared) ? 1 : absint($countShared) + 1;

		SetSettings::setPostMeta($_POST['postID'], 'shared', $countShared);

		wp_send_json_success(array(
			'countShared' => self::countShared($countShared, false)
		));
	}
}