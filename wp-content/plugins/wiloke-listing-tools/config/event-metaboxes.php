<?php
use WilokeListingTools\Framework\Helpers\General;

$timeFormat = 'h:i:s A';
$dateFormat = apply_filters('wilcity_date_picker_format', 'mm/dd/yy');
$dateFormat = \WilokeListingTools\Framework\Helpers\Time::convertJSDateFormatToPHPDateFormat($dateFormat);
if ( class_exists('\WilokeListingTools\Framework\Helpers\Time') ){
	$timeFormat = \WilokeListingTools\Framework\Helpers\Time::getTimeFormat();
}

$prefix = 'wilcity_';
return array(
	'hosted_by' => array(
		'id'            => 'hosted_by',
		'title'         => 'Hosted By',
		'object_types'  => array('event'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'          => 'text',
				'id'            => 'wilcity_hosted_by',
				'description'   => 'If this field is emptied, the event author will be used.',
				'name'          => 'Name'
			),
			array(
				'type'      => 'text',
				'id'        => 'wilcity_hosted_by_profile_url',
				'name'      => 'Profile URL'
			)
		)
	),
	'event_time_format' => array(
		'id'            => 'event_time_format',
		'title'         => 'Time Format',
		'object_types'  => array('event'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'name'      => 'Time Format',
				'type'      => 'select',
				'id'        => 'wilcity_event_time_format',
				'options'   => array(
					'inherit'   => 'Inherit General Settings',
					12     => '12h Format',
					24     => '24h Format',
				)
			)
		)
	),
	'event_settings' => array(
		'id'            => 'event_settings',
		'title'         => 'Event Settings',
		'object_types'  => array('event'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'name'      => 'Frequency',
				'type'      => 'select',
				'id'        => 'frequency',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Event', 'getFrequency'),
				'options'   => array(
					'occurs_once'   => 'Occurs Once',
					'daily'         => 'Daily',
					'weekly'        => 'Weekly'
				)
			),
			array(
				'name'      => 'Day',
				'type'      => 'select',
				'id'        => 'specifyDays',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Event', 'getSpecifyDay'),
				'options'   => array(
					'sunday'    => 'Sunday',
					'monday'    => 'Monday',
					'tuesday'   => 'Tuesday',
					'wednesday' => 'Wednesday',
					'thursday'  => 'Thursday',
					'friday'    => 'Friday',
					'saturday'  => 'Saturday'
				)
			),
			array(
				'name' => 'Starts',
				'id'   => 'starts',
				'type' => 'text_date',
				'default_cb'    => array('WilokeListingTools\MetaBoxes\Event', 'startsOn'),
				'date_format' => $dateFormat
			),
			array(
				'name' => 'Ends On',
				'id'   => 'endsOn',
				'type'        => 'text_date',
				'default_cb'    => array('WilokeListingTools\MetaBoxes\Event', 'endsOn'),
				'date_format' => $dateFormat
			),
			array(
				'name'          => 'Opening At',
				'id'            => 'openingAt',
				'type'          => 'text_time',
				'default_cb'    => array('WilokeListingTools\MetaBoxes\Event', 'openingAt'),
				'time_format'   => $timeFormat
			),
			array(
				'name'          => 'Closed At',
				'id'            => 'closedAt',
				'type'          => 'text_time',
				'default_cb'    => array('WilokeListingTools\MetaBoxes\Event', 'closedAt'),
				'time_format'   => $timeFormat
			),
			array(
				'name'          => 'isFormChanged',
				'id'            => 'isFormChanged',
				'type'          => 'hidden'
			)
		)
	),
	'event_parent' => array(
		'id'            => 'event_parent',
		'title'         => 'Event Parent',
		'object_types'  => array('event'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'description'      => 'The parent id is required. If you have not selected a parent id yet, please Select one and then click Publish button. The Review Category will be displayed after that.',
				'post_types'=> General::getPostTypeKeys(false,true),
				'attributes' => array(
					'ajax_action' => 'wiloke_fetch_posts',
					'post_types'  => implode(',', General::getPostTypeKeys(false))
				),
				'id'        => 'parent_id',
				'name'      => 'Parent ID',
				'default_cb'=> array('WilokeListingTools\MetaBoxes\Event', 'getParentID')
			)
		)
	),
	'my_tickets' => array(
		'id'            => 'my_tickets',
		'title'         => 'Event Tickets',
		'object_types'  => array('event'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'description'      => 'Showing WooCommerce Products on this Event page',
				'post_types'=> array('product'),
				'attributes' => array(
					'ajax_action' => 'wilcity_fetch_dokan_products',
					'post_types'  => 'product'
				),
				'id'        => 'wilcity_my_products',
				'multiple'  => true,
				'name'      => 'My Tickets',
				'default_cb'=> array('WilokeListingTools\MetaBoxes\Event', 'getMyProducts')
			)
		)
	),
	'tickets' => array(
		'id'            => 'tickets',
		'title'         => 'Tickets',
		'object_types'  => array('event'),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select',
				'id'        => 'ticket_url',
				'name'      => 'Ticket url'
			)
		)
	)
);