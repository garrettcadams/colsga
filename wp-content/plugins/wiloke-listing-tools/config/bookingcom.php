<?php
$prefix = 'wilcity';
use WilokeListingTools\Framework\Helpers\General;
$postTypes = '';
if ( isset($_GET['post']) && !empty($_GET['post']) ){
	$parentID = wp_get_post_parent_id($_GET['post']);
	if ( !empty($parentID) ){
		$postTypes = get_post_type($parentID);
	}
}


if ( empty($postTypes) ){
	$postTypes = implode(',', General::getPostTypeKeys(false));
}

return array(
	'aBannerCreator' => array(
		'id'         => 'banner_creator_settings',
		'title'      => 'Display On',
		'object_types' => array('bdotcom_bm'),
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'      => 'select2_posts',
				'post_types'=> \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false,true),
				'attributes' => array(
					'ajax_action' => 'wiloke_fetch_posts',
					'post_types'  => $postTypes
				),
				'id'        => 'parent_id',
				'name'      => 'Display On',
				'default_cb'=> array('WilokeListingTools\MetaBoxes\BookingComBannerCreator', 'getParentID')
			)
		)
	),
);