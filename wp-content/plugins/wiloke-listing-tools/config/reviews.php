<?php
use WilokeListingTools\Framework\Helpers\General;

if ( isset($_GET['post']) && !empty($_GET['post']) ){
	$parentID = wp_get_post_parent_id($_GET['post']);
	if ( !empty($parentID) ){
		$postTypes = get_post_type($parentID);
	}
}

if ( !isset($postTypes) ){
	$postTypes = implode(',', General::getPostTypeKeys(false));
}

return [
	'toggle'            => 'toggle_review',
	'toggle_gallery'    => 'toggle_review_upload_gallery',
	'toggle_review_discussion' => 'toggle_review_discussion',
	'is_immediately_approved' => 'review_is_immediately_approved',
	'review_sticky'     => 'review_sticky_',
	'mode'              => 'review_mode',
	'details'           => 'review_details',
	'review_qualities'  => array(
		5 => array(
			5  => esc_html__('Excellent', 'wiloke-listing-tools'),
			4   => esc_html__('Very Good', 'wiloke-listing-tools'),
			3   => esc_html__('Average', 'wiloke-listing-tools'),
			2   => esc_html__('Terrible', 'wiloke-listing-tools'),
			1   => esc_html__('Poor', 'wiloke-listing-tools')
		),
		10 => array(
			10  => esc_html__('Excellent', 'wiloke-listing-tools'),
			9   => esc_html__('Very Good', 'wiloke-listing-tools'),
			8   => esc_html__('Average', 'wiloke-listing-tools'),
			5   => esc_html__('Poor', 'wiloke-listing-tools'),
			3   => esc_html__('Terrible', 'wiloke-listing-tools')
		)
	),
	'liked' => 'liked',
	'metaBoxes' => array(
		'gallery' => array(
			'id'            => 'review_gallery',
			'title'         => 'Reviews',
			'object_types'  => array('review'),
			'context'       => 'normal',
			'priority'      => 'low',
			'show_names'    => true, // Show field names on the left
			'fields'        => array(
				array(
					'type'      => 'file_list',
					'id'        => 'wilcity_gallery',
					'name'      => 'Gallery'
				)
			)
		),
		'parent' => array(
			'id'            => 'review_parent',
			'title'         => 'Parent ID',
			'object_types'  => array('review'),
			'context'       => 'normal',
			'priority'      => 'low',
			'save_fields'   => false,
			'show_names'    => true, // Show field names on the left
			'fields'        => array(
				array(
					'type'      => 'select2_posts',
					'description'      => 'The parent id is required. If you have not selected a parent id yet, please Select one and then click Publish button, the Review Category will be displayed after that. You can not change the Parent ID to another Directory Type. Eg: If this Parent ID belongs to Listing Type, You can not replace with a Parent ID that belongs to Travel Directory Type.',
					'post_types'=> General::getPostTypeKeys(false,true),
					'attributes' => array(
						'ajax_action' => 'wiloke_fetch_posts',
						'post_types'  => $postTypes
					),
					'id'        => 'parent_id',
					'name'      => 'Parent ID',
					'default_cb'=> array('WilokeListingTools\MetaBoxes\Review', 'getParentID')
				)
			)
		)
	)
];