<?php
$prefix = 'wilcity_';
return array(
	'woocommerce_association'  => array(
		'id'         => 'woocommerce_association',
		'title'      => esc_html__('WooCommerce Alias', 'wiloke-listing-tools'),
		'object_types' => array('event_plan'),
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type' => 'select',
				'id'   => $prefix.'woocommerce_association',
				'name' => esc_html__('Product Alias', 'wiloke-listing-tools'),
				'options_cb' => array('WilokeListingTools\MetaBoxes\EventPlan', 'renderProductAlias')
			)
		)
	),
	'is_recommended' =>  array(
		'id'            => 'is_recommended',
		'title'         => esc_html__('Is Recommended', 'wiloke-listing-tools'),
		'object_types'  => array('event_plan'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'checkbox',
				'id'        => 'wilcity_is_recommended',
				'name'      => esc_html__('Yes', 'wiloke-listing-tools')
			),
			array(
				'type'      => 'text',
				'id'        => 'wilcity_recommend_text',
				'name'      => esc_html__('Description', 'wiloke-listing-tools'),
				'default'   => esc_html__('Popular', 'wiloke-listing-tools')
			),
		)
	),
	'plan_settings'  => array(
		'id'            => 'plan_settings',
		'title'         => esc_html__('Plan Settings', 'wiloke-listing-tools'),
		'object_types'  => array('event_plan'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_event_plan:regular_price',
				'name'          => 'Regular Price'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_event_plan:availability_items',
				'name'          => 'Availability Events'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_event_plan:trial_period',
				'name'          => 'Trial Period (Unit: Day)'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_event_plan:regular_period',
				'name'          => 'Period Day'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_event_plan:toggle_gallery',
				'name'          => 'Toggle Gallery',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:maximumGalleryImages',
				'name'          => 'Maximum Gallery images can be uploaded in a listing. (*)',
				'value'         => 10
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_videos',
				'name'          => 'Toggle Video',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:maximumVideos',
				'name'          => 'Maximum Videos can be added in a listing. Leave empty means unlimited',
				'value'         => 4
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_email',
				'name'          => 'Toggle Email Address',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_phone',
				'name'          => 'Toggle Phone',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_website',
				'name'          => 'Toggle Website',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			)
		)
	),
);