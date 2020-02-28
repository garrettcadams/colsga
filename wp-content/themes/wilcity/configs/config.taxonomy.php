<?php
// Configure Taxonomy here
return array(
	'category' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		)
	),
	'post_tag' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		)
	),
	'listing_location' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		),
		array(
			'type'          => 'media',
			'name'          => 'Gallery',
			'description'   => 'The gallery will be shown on the this category page',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'gallery'
		),
		array(
			'type'          => 'text',
			'save_type'     => 'term_meta',
			'name'          => esc_html__('Place ID', 'wilcity'),
			'description'   => Wiloke::ksesHTML( __('This feature available in ListGo 1.0.8 and higher. You can find the place here <a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank">PlaceID finder</a>', 'wilcity'), true),
			'is_add_to_column' => true,
			'id'            => 'placeid'
		),
	),
	'listing_cat' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'id'            => 'header_overlay'
		),
		array(
			'type'          => 'media',
			'name'          => 'Gallery',
			'description'   => 'The gallery will be shown on the this category page',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'gallery'
		),
		array(
			'type'          => 'media',
			'name'          => esc_html__('Map Marker', 'wilcity'),
			'description'   => Wiloke::ksesHTML(__('You can download Wiloke Map Icons here <a href="https://www.dropbox.com/s/l67lf2t135j1ns0/map-icons.zip?dl=0" target="_blank">Download Map Icons</a>', 'wilcity'), true),
			'return'        => 'url',
			'is_add_to_column' => true,
			'id'            => 'map_marker_image'
		)
	),
	'product_cat' => array(
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		)
	),
	'product_tag' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'wilcity'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		)
	)
);