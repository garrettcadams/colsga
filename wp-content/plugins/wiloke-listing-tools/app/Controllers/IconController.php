<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Routing\Controller;

class IconController extends Controller {
	public function __construct() {
//		add_action('wp_ajax_wilcity_fetch_icons', array($this, 'fetchIcons'));
//		add_action('wp_ajax_nopriv_wilcity_fetch_icons', array($this, 'fetchIcons'));
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/icons', array(
				'methods' => 'GET',
				'callback' => array($this, 'getIcons')
			));
		});
	}

	public function getIcons(){
		$aIcons = [
			array(
				'icon' => 'la la-minus-square',
				'name' => 'Square'
			),
			array(
				'icon' => 'la la-calendar',
				'name' => 'Calendar'
			),
			array(
				'icon' => 'la la-envelope-o',
				'name' => 'Envelope'
			),
			array(
				'icon' => 'la la-phone',
				'name' => 'Phone'
			),
			array(
				'icon' => 'la la-home',
				'name' => 'Home'
			),
			array(
				'icon' => 'la la-hotel',
				'name'=> 'Hotel'
			),
			array(
				'icon' => 'la la-link',
				'name' => 'Link'
			),
			array(
				'icon' => 'la la-facebook',
				'name' => 'Facebook'
			),
			array(
				'icon' => 'la la-twitter',
				'name' => 'Twitter'
			),
			array(
				'icon' => 'la la-map-marker',
				'name' => 'Marker'
			),
			array(
				'icon' => 'la la-google-plus-square',
				'name' => 'Google+'
			),
			array(
				'icon' => 'la la-map-pin',
				'name' => 'Map Pin'
			),
			array(
				'icon' => 'fa fa-cutlery',
				'name' => 'Cutlery'
			)
		];

		$aIcons = apply_filters('wilcity/wiloke-listing-tools/external-button-icon', $aIcons);

		return array(
			'data' => $aIcons
		);
	}
}