<?php
$prefix = 'wilcity_';

return array(
	'listing_is_recommended' =>  array(
		'id'            => 'listing_is_recommended',
		'title'         => esc_html__('Is Recommended', 'wiloke-listing-tools'),
		'object_types'  => array('listing_plan'),
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
	'exclude_from_claim_plans' =>  array(
		'id'            => 'exclude_from_claim_plans',
		'title'         => 'Exclude From Paid Claim',
		'description'   => 'This plan won\'t be shown if you are using Paid Claim feature',
		'object_types'  => array('listing_plan'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'checkbox',
				'id'        => 'wilcity_exclude_from_claim_plans',
				'name'      => 'Yes'
			)
		)
	),
	'listing_plan_settings'  => array(
		'id'            => 'listing_plan_settings',
		'title'         => esc_html__('Plan Settings', 'wiloke-listing-tools'),
		'object_types'  => array('listing_plan'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:regular_price',
				'name'          => 'Regular Price'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:availability_items',
				'name'          => 'Availability Listings'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:trial_period',
				'name'          => 'Trial Period (Unit: Day) - This is for Recurring Payment only'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:regular_period',
				'name'          => 'Period Day'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_featured_image',
				'name'          => 'Toggle Featured Image',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_cover_image',
				'name'          => 'Toggle Cover Image',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_logo',
				'name'          => 'Toggle Logo',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_sidebar_statistics',
				'name'          => 'Toggle Sidebar Statistics',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_schema_markup',
				'name'          => 'Toggle Schema Markup',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_business_hours',
				'name'          => 'Toggle Business Hours',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_price_range',
				'name'          => 'Toggle Price Range',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_single_price',
				'name'          => 'Toggle Single Price',
				'options'       => array(
					'enable'  => 'Enable',
					'disable' => 'Disable'
				)
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
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_social_networks',
				'name'          => 'Toggle Social Networks',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_listing_tag',
				'name'          => 'Toggle Listing Tags',
				'options'       => array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_gallery',
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
				'name'          => 'Maximum Gallery images can be uploaded in a listing. Leave empty means unlimited (*)',
				'default_cb'       => array('WilokeListingTools\MetaBoxes\ListingPlan', 'getMaximumGalleryImagesAllowed')
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
				'default_cb'    => array('WilokeListingTools\MetaBoxes\ListingPlan', 'getMaximumVideosAllowed')
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_restaurant_menu',
				'name'          => 'Toggle Restaurant Menus',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:maximumRestaurantMenus',
				'name'          => 'Maximum Restaurant Menus can be added in a listing. Leave empty means unlimited'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:maximumItemsInMenu',
				'name'          => 'Maximum Items can be added in a menu. Leave empty means unlimited'
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:maximum_restaurant_gallery_images',
				'name'          => 'Maximum images can be used in a Menu Item',
				'default'       => 4
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_google_ads',
				'name'          => 'Showing Google Ads',
				'options'       => array(
					'enable'  => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_bookingcombannercreator',
				'name'          => 'Toggle Booking.com Banner on The Single Sidebar',
				'options'       => array(
					'enable'  => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_admob',
				'name'          => 'Showing AdMob On Mobile',
				'options'       => array(
					'enable'  => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_coupon',
				'name'          => 'Toggle Coupon',
				'options'       => array(
					'enable'  => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'select',
				'id'            => 'add_listing_plan:toggle_promotion',
				'name'          => 'Showing Promotion Listings',
				'options'       => array(
					'enable'  => 'Enable',
					'disable' => 'Disable'
				)
			),
			array(
				'type'          => 'wiloke_field',
				'fieldType'     => 'input',
				'id'            => 'add_listing_plan:menu_order',
				'name'          => 'Listing Order',
				'description'   => 'The the default order to the Listing. The higher order will get higher priority on the Search page'
			)
		)
	),
	'listing_woocommerce_association'  => array(
		'id'         => 'listing_woocommerce_association',
		'title'      => esc_html__('WooCommerce Alias', 'wiloke-listing-tools'),
		'object_types' => array('listing_plan'),
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type' => 'select',
				'id'   => $prefix.'woocommerce_association',
				'name' => esc_html__('Product Alias', 'wiloke-listing-tools'),
				'options_cb' => array('WilokeListingTools\MetaBoxes\ListingPlan', 'renderProductAlias')
			)
		)
	)
);