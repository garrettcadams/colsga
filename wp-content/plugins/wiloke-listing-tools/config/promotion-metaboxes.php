<?php
use WilokeListingTools\Framework\Helpers\General;

return array(
	'promotion_information' => array(
		'id'            => 'promotion_information',
		'title'         => 'Promotion Information',
		'object_types'  => array('promotion'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'id'        => 'wilcity_listing_id',
				'show_link' => true,
				'name'      => 'Listing Name',
				'attributes' => array(
					'ajax_action' => 'wiloke_fetch_posts',
					'post_types'  => implode(',', General::getPostTypeKeys(false))
				)
			)
		)
	),
);