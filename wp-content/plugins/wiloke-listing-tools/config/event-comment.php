<?php
$prefix = 'wilcity_';
return array(
	'metaBoxes' => array(
		'id'            => 'event_comment_settings',
		'title'         => 'Settings',
		'object_types'  => array('event_comment'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'          => 'select2_posts',
				'post_types'    => 'event',
				'attributes'    => array(
					'ajax_action' => 'wiloke_fetch_posts',
					'post_types'  => 'event,event_comment'
				),
				'id'        => 'parent_id',
				'name'      => 'Parent ID',
				'default_cb'=> array('WilokeListingTools\MetaBoxes\EventComment', 'getParentID')
			)
		)
	)
);