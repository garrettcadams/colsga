<?php
$prefix = 'wilcity_';
return array(
	'listing_tag_settings' =>  array(
		'id'            => 'listing_tag_settings',
		'title'         => esc_html__('Settings', 'wiloke-listing-tools'),
		'object_types'  => array('term'),
		'taxonomies'    => array('listing_tag'),
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
				'type'      => 'text',
				'id'        => $prefix.'icon',
				'name'      => 'Icon',
				'desc' => 'Warning: You have to use <a href="https://fontawesome.com/v4.7.0/" target="_blank">FontAwesome</a> or <a target="_blank" href="http://nimb.ws/eBfXA5">Line Awesome</a>. If you use another one, it will broken your App'
			),
			array(
				'type'      => 'colorpicker',
				'id'        => $prefix.'icon_color',
				'name'      => 'Icon Color'
			),
			array(
				'type'      => 'file',
				'id'        => $prefix.'icon_img',
				'name'      => esc_html__('Upload Your Icon', 'wiloke-listing-tools'),
				'desc'      => esc_html__('The icon image will get higher priority than LineAwesome Icon', 'wiloke-listing-tools'),
				'preview_size' => 'full'
			),
			array(
				'type'      => 'file',
				'taxonomy'  => 'featured_image',
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
				'type'      => 'multicheck_inline',
				'id'        => $prefix.'belongs_to',
				'name'      => esc_html__('Belongs To', 'wiloke-listing-tools'),
				'description'       => 'Select Listing Types that this term should belong to. Leave empty to set the tag for all',
				'options_cb' => array('WilokeListingTools\MetaBoxes\Listing', 'setListingTypesOptions')
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
			)
		)
	)
);