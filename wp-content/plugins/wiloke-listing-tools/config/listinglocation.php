<?php
$prefix = 'wilcity_';
return array(
	'listing_location_settings' =>  array(
		'id'            => 'listing_location_settings',
		'title'         => esc_html__('Settings', 'wiloke-listing-tools'),
		'object_types'  => array('term'),
		'taxonomies'    => array('listing_location'),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'text',
				'id'        => $prefix.'tagline',
				'name'      => 'Tagline',
			),
			array(
				'type'      => 'wiloke_multiselect2_ajax',
				'sanitization_cb' => false,
				'taxonomy'  => 'listing_tag',
				'id'        => $prefix.'tags_children',
				'action'    => 'wilcity_get_tags_options',
				'name'      => 'Set Tags belong to this location',
				'desc' => 'Leave empty means belongs to all tags'
			),
			array(
				'type'      => 'text',
				'taxonomy'  => 'icon',
				'id'        => $prefix.'icon',
				'name'      => 'Icon',
				'desc' => 'Warning: You have to use <a href="https://fontawesome.com/v4.7.0/" target="_blank">FontAwesome</a> or <a target="_blank" href="http://nimb.ws/eBfXA5">Line Awesome</a>. If you use another one, it will broken your App'
			),
			array(
				'type'      => 'colorpicker',
				'id'        => $prefix.'icon_color',
				'name'      => 'Icon Color',
			),
			array(
				'type'      => 'file',
				'taxonomy'  => 'icon_img',
				'id'        => $prefix.'icon_img',
				'name'      => 'Icon Image',
				'desc'      => 'This setting will override Term Icon setting'
			),
			array(
				'type'      => 'file',
				'id'        => $prefix.'featured_image',
				'name'      => 'Featured Image'
			),
			array(
				'type'      => 'file_list',
				'taxonomy'  => 'gallery',
				'id'        => $prefix.'gallery',
				'name'      => 'Gallery',
				'desc'      => 'If the gallery is not empty, it be used on this category page'
			),
			array(
				'type'      => 'colorpicker',
				'id'        => $prefix.'left_gradient_bg',
				'name'      => 'Left Gradient Background',
				'desc'      => 'This setting is for Term Boxes shortcode'
			),
			array(
				'type'      => 'colorpicker',
				'id'        => $prefix.'right_gradient_bg',
				'name'      => 'Right Gradient Background',
				'desc'      => 'This setting is for Term Boxes shortcode'
			),
			array(
				'type'      => 'text',
				'id'        => $prefix.'gradient_tilted_degrees',
				'name'      => 'Gradient tilted degrees',
				'desc'      => 'Eg: A gradient tilted 45 degrees, starting Left Background and finishing Right Background',
				'default'   => -10
			),
			array(
				'type'      => 'multicheck_inline',
				'id'        => $prefix.'belongs_to',
				'name'      => esc_html__('Belongs To', 'wiloke-listing-tools'),
				'desc'      => 'Enter in your icon name you want to use. You can find the icon at <a href="https://icons8.com/line-awesome" target="_blank">https://icons8.com</a>',
				'description'       => 'Select Listing Types that this term should belong to. Leave empty to set the category for all',
				'options_cb' => array('WilokeListingTools\MetaBoxes\Listing', 'setListingTypesOptions')
			),
		)
	)
);