<?php

namespace WILCITY_APP\Controllers;


class Filter {
	use JsonSkeleton;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2/', 'get-listing-filters', array(
				'methods'   => 'GET',
				'callback'  => array($this, 'getListingFilters')
			));
		});
	}

	public function getListingFilters(){
		return array(
			'status'    => 'success',
			'oResults'  => array(
				array(
					'type' => 'input',
					'key'  => 's',
					'name' => esc_html__('What are you looking for?', WILCITY_MOBILE_APP),
					'value'=> ''
				),
				array(
					'type' => 'checkbox',
					'key'  => 'best_rated',
					'name' => esc_html__('Best Rating', WILCITY_MOBILE_APP),
					'value'=> 'no'
				),
				array(
					'type' => 'checkbox',
					'key'  => 'open_now',
					'name' => esc_html__('Open Now', WILCITY_MOBILE_APP),
					'value'=> 'no'
				),
				array(
					'type' => 'select',
					'key'  => 'price_range',
					'name' => esc_html__('Price Range', WILCITY_MOBILE_APP),
					'value' => 'nottosay',
					'options' => array(
						array(
							'name' => esc_html__('All Range', WILCITY_MOBILE_APP),
							'id'   => 'nottosay',
							'selected' => true
						),
						array(
							'name' => esc_html__('Cheap', WILCITY_MOBILE_APP),
							'id'   => 'cheap',
							'selected' => false
						),
						array(
							'name' => esc_html__('Moderate', WILCITY_MOBILE_APP),
							'id'   => 'moderate',
							'selected' => false
						),
						array(
							'name' => esc_html__('Expensive', WILCITY_MOBILE_APP),
							'id'   => 'expensive',
							'selected' => false
						),
						array(
							'name' => esc_html__('Ultra High', WILCITY_MOBILE_APP),
							'id'   => 'ultra_high',
							'selected' => false
						)
					)
				),
				array(
					'type' => 'google_auto_complete',
					'key'  => 'address',
					'name' => esc_html__('Where do you want to go?', WILCITY_MOBILE_APP),
					'value'=> ''
				)
			)
		);
	}
}