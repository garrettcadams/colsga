<?php
return [
	'claim_status' => array(
		'id'            => 'claim_status',
		'title'         => esc_html__('Claim Status', 'wiloke-listing-tools'),
		'object_types'  => array('claim_listing'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select',
				'id'        => 'wilcity_claim_status',
				'name'      => 'Claimer Status',
				'options'   => array(
					'pending'   => 'Pending',
					'cancelled' => 'Cancelled',
					'approved'  => 'Approved'
				)
			)
		)
	),
	'claimer_id' => array(
		'id'            => 'wilcity_claimer_id',
		'title'         => esc_html__('Claimer', 'wiloke-listing-tools'),
		'object_types'  => array('claim_listing'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_user',
				'id'        => 'wilcity_claimer_id',
				'name'      => 'Claimer Username',
				'attributes' => array(
					'ajax_action' => 'wiloke_select_user'
				)
			)
		)
	),
	'claimed_listing_id' => array(
		'id'            => 'claimed_listing_id',
		'title'         => esc_html__('Listing Name', 'wiloke-listing-tools'),
		'object_types'  => array('claim_listing'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'id'        => 'wilcity_claimed_listing_id',
				'name'      => 'Listing Name',
				'attributes' => array(
					'ajax_action' => 'wiloke_fetch_posts',
					'post_types'   => implode(',', \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false))
				)
			)
		)
	),
	'claim_plan_id' => array(
		'id'            => 'claim_plan_id',
		'title'         => 'Claim Plan',
		'object_types'  => array('claim_listing'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'id'        => 'wilcity_claim_plan_id',
				'name'      => 'Claim Plan',
				'attributes' => array(
					'ajax_action' => 'wiloke_fetch_posts',
					'post_types'   => 'listing_plan'
				)
			)
		)
	),
	'attribute_post_author' => array(
		'id'            => 'attribute_post_author',
		'title'         => esc_html__('Attribute this listing to', 'wiloke-listing-tools'),
		'object_types'  => array('claim_listing'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'          => 'select2_user',
				'id'            => 'attribute_post_author',
				'name'          => 'Attribute this listing to',
				'desc'      => 'This setting is required if you want to switch this claim from Approved to another status.',
				'attributes' => array(
					'ajax_action' => 'wiloke_select_user'
				)
			)
		)
	)
];