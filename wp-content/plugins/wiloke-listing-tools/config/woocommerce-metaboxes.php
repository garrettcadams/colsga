<?php
$prefix = 'wilcity_';
return array(
	'metaBoxes' => array(
		'id'            => 'wilcity_dokan_settings',
		'title'         => 'Dokan Settings',
		'object_types'  => array('product'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'name'          => 'Is Multi-Vendor?',
				'description'   => 'Choose No if you just want to sell this product as the default.',
				'type'          => 'select',
				'id'            => 'wilcity_is_dokan',
				'options'       => array(
					'no'   => 'No',
					'yes'  => 'Yes'
				),
				'default_cb'=> array('WilokeListingTools\MetaBoxes\WooCommerce', 'getIsDokan')
			),
			array(
				'name'          => 'Sending QRCode to customer',
				'description'   => 'After purchasing this product, We will send QRCode to the customer. You should use this feature if this product is an Event Ticket.',
				'type'          => 'select',
				'id'            => 'wilcity_is_send_qrcode',
				'options'       => array(
					'no'   => 'No',
					'yes'  => 'Yes'
				),
				'default_cb'=> array('WilokeListingTools\MetaBoxes\WooCommerce', 'getIsSendQRCode')
			),
			array(
				'name'          => 'Email Content',
				'description'   => 'Leave empty to use your setting under Appearance -> Theme Options -> Email Settings -> QRCode Email',
				'type'          => 'textarea',
				'id'            => 'wilcity_qrcode_description',
				'default_cb'=> array('WilokeListingTools\MetaBoxes\WooCommerce', 'getQRCodeEmailContent')
			)
		)
	),
	'excludeFromShop' => array(
		'id'            => 'wilcity_exclude_products_from_shop',
		'title'         => 'Exclude Product From Shop Page',
		'object_types'  => array('product'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'name'          => 'Exclude?',
				'type'          => 'select',
				'id'            => 'wilcity_exclude_from_shop',
				'options'       => array(
					'no'   => 'No',
					'yes'  => 'Yes'
				)
			)
		)
	)
);