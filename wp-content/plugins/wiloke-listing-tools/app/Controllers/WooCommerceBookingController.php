<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User;

class WooCommerceBookingController extends Controller {
	public function __construct() {
		add_action('wp_ajax_wilcity_fetch_my_room', array($this, 'fetchMyRoom'));
		add_action('wp_print_scripts', array($this, 'removeScripts'), 999);
	}

	public function removeScripts(){
	    if ( !function_exists('wilcityIsNoMapTemplate') ){
	        return false;
        }
		// Removing jquery smooth on Search Page to resolve conflict Event Calendar style issue
		if ( wilcityIsNoMapTemplate() || is_tax() || wilcityIsGoogleMap() ){
			wp_dequeue_script('jquery-ui-style');
		}
	}

	public static function getProductsByUserID($userID, $s=''){
		$aArgs = array(
			'post_type'     => 'product',
			's'             => $s,
			'posts_per_page'=> 20,
			'post_status'   => array('publish', 'pending'),
			'author'        => $userID,
            'tax_query'     => [
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => ['booking']
                ]
            ]
		);

		$oUserData = get_userdata($userID);
		if ( in_array('administrator', $oUserData->roles) ){
			unset($aArgs['author']);
		}

		if ( empty($s) ){
			unset($aArgs['s']);
		}

		$query = new \WP_Query($aArgs);

		$aOptions = array();
		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$aOptions[] = General::buildSelect2OptionForm($query->post);
			}
		}

		return $aOptions;
	}

	public function fetchMyRoom(){
		$s = '';
		if ( isset($_GET['search']) ){
			$s = $_GET['search'];
		}else if ( isset($_GET['q']) ){
			$s = $_GET['q'];
		}

		$aOptions = self::getProductsByUserID(get_current_user_id(), $s);
		if ( empty($aOptions) ){
			wp_send_json_error();
		}
		wp_send_json_success(array(
			'msg' => array(
				'results' => $aOptions
			)
		));
	}
}
