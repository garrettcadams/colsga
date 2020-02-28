<?php
$prefix = 'wilcity_';

return array(
	'postParent' => array(
		'id'          => $prefix.'posts_belongs_to',
		'title'       => 'Posts Belongs To Listing',
		'object_types'=> array('post'),
		'context'     => 'normal',
//		'save_fields' => false,
		'priority'    => 'low',
		'show_names'  => true, // Show field names on the left
		'fields'      => array(
			array(
				'type'      => 'select2_posts',
				'id'        => 'post_parent',
				'name'      => 'Belongs To Listings',
				'description'      => 'This setting has been deprecated, We recommend using to Listings -> Your Listing -> My Posts instead.',
				'attributes' => array(
					'ajax_action' => 'wiloke_fetch_posts',
					'post_types'  => implode(',', class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true) : array('listing'))
				),
				'default_cb'=> array('WilokeListingTools\MetaBoxes\Post', 'getPostParent')
			)
		)
	)
);