<?php
$prefix = 'wilcity_';
return array(
	'discount_general_settings' => array(
		'id'            => 'discount_general_settings',
		'title'         => esc_html__('Discount Settings', 'wiloke-listing-tools'),
		'object_types'  => array('discount'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'    => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'discount_general:type',
				'name'          => esc_html__('Discount Type', 'wiloke-listing-tools'),
				'options'       => array(
					'percentage'    => esc_html__('Percentage Discount', 'wiloke-listing-tools'),
					'fixed_price'   => esc_html__('Fixed Price Discount', 'wiloke-listing-tools')
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'text',
				'id'            => 'discount_general:amount',
				'name'          => esc_html__('Discount Amount', 'wiloke-listing-tools')
			),
//			array(
//				'type'          => 'wiloke_field',
//				'fieldType'     => 'checkbox',
//				'id'            => 'discount_general:for_post_type1',
//				'name'          => esc_html__('Discount For', 'wiloke-listing-tools'),
//				'options'       => array(
//					'listing-plan' => esc_html__('Add Listing Plan Only', 'wiloke-listing-tools'),
//					'event-plan'   => esc_html__('Add Event Plan Only', 'wiloke-listing-tools'),
//					'both'         => esc_html__('The both', 'wiloke-listing-tools')
//				)
//			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'discount_general:for_post_type',
				'name'          => esc_html__('Discount For', 'wiloke-listing-tools'),
				'options'       => array(
					'both'         => esc_html__('The both', 'wiloke-listing-tools'),
					'listing_plan' => esc_html__('Add Listing Plan Only', 'wiloke-listing-tools'),
					'event_plan'   => esc_html__('Add Event Plan Only', 'wiloke-listing-tools')
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'multicheck',
				'id'            => 'discount_general:exclude_add_listing_plans',
				'name'          => esc_html__('Exclude Add Listing Plans', 'wiloke-listing-tools'),
				'object_types'  => 'listing_plan'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'multicheck',
				'id'            => 'discount_general:exclude_event_plans',
				'name'          => esc_html__('Exclude Event Plans', 'wiloke-listing-tools'),
				'object_types'  => 'event_plan'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'text_date',
				'id'            => 'discount_general:expiry_date',
				'name'          => esc_html__('Expiry Date', 'wiloke-listing-tools')
			)
		)
	),
);