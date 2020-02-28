<?php
return array(
	'report_information' => array(
		'id'            => 'report_information',
		'title'         => 'Report Information',
		'object_types'  => array('report'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'    => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'id'        => 'wilcity_listing_name',
				'show_link' => true,
				'name'      => 'Listing Name',
				'attributes' => array(
					'ajax_action' => 'wiloke_fetch_posts',
					'post_types'  => implode(',', \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false))
				)
			)
		)
	),
	'report_my_note' => array(
		'id'            => 'report_my_note',
		'title'         => 'My Note',
		'object_types'  => array('report'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'    => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'textarea',
				'id'        => 'wilcity_my_note',
				'name'      => 'My Note'
			)
		)
	)
);