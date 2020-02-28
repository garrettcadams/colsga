<?php
global $wiloke;
$prefix = 'wilcity_';

return array(
	'wilcity_page_general_settings'  => array(
		'id'          => 'wilcity_page_general_settings',
		'title'       => esc_html__( 'General Settings', 'wilcity' ),
		'object_types'=> array('page'),
		'context'     => 'normal',
		'priority'    => 'low',
		'show_names'  => true, // Show field names on the left
		'fields'      => array(
			array(
				'type'         => 'file',
				'id'           => $prefix.'logo',
				'name'         => 'Logo',
				'description'  => 'This setting will override Theme Options setting'
			),
			array(
				'type'         => 'file',
				'id'           => $prefix.'retina_logo',
				'name'         => 'Rentina Logo',
				'description'  => 'This setting will override Theme Options setting'
			),
			array(
				'type'         => 'select',
				'id'           => $prefix.'menu_background',
				'name'         => esc_html__('Menu background', 'wilcity'),
				'default'      => 'inherit',
				'options'      => array(
					'inherit' => 'Inherit',
					'transparent' => 'Transparent',
					'dark' => 'Dark',
					'light' => 'Light',
					'custom' => 'Custom'
				)
			),
			array(
				'type'         => 'colorpicker',
				'id'           => $prefix.'custom_menu_background',
				'name'         => 'Custom Menu background'
			),
			array(
				'type'         => 'select',
				'id'           => $prefix.'toggle_menu_sticky',
				'name'         => 'Toggle Menu Sticky',
				'default'      => 'inherit',
				'options'      => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable'
				)
			)
		)
	),
	'wilcity_reset_password_settings'  => array(
		'id'          => 'wilcity_reset_password_settings',
		'title'       => 'Reset Password Settings',
		'show_on'     => array( 'key' => 'page_template', 'value' => 'templates/reset-password.php' ),
		'object_types'=> array('page'),
		'context'     => 'normal',
		'priority'    => 'low',
		'show_names'  => true, // Show field names on the left
		'fields'      => array(
			array(
				'type'         => 'file',
				'id'           => $prefix.'background_image',
				'name'         => 'Background Image'
			)
		)
	),
	'wilcity_search_without_map_settings'  => array(
		'id'          => 'wilcity_search_without_map_settings',
		'title'       => 'Map / Search Without Map Settings',
		'object_types'=> array('page'),
		'show_on'     => array( 'key' => 'page_template', 'value' => 'templates/search-without-map.php' ),
		'context'     => 'normal',
		'priority'    => 'low',
		'show_names'  => true, // Show field names on the left
		'fields'      => array(
			array(
				'type'         => 'text',
				'id'           => $prefix.'search_img_size',
				'name'         => 'Image Size',
				'description'  => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
			),
			array(
				'type'         => 'select',
				'id'           => $prefix.'style',
				'name'         => 'Style',
				'options' => array(
					'grid'  => 'Grid',
					'grid2' => 'Grid 2',
					'list'  => 'List'
				)
			)
		)
	),
    'wilcity_general_settings'  => array(
        'id'          => 'wilcity_general_settings',
        'title'       => esc_html__( 'General Settings', 'wilcity' ),
        'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false) : array('listing'),
        'context'     => 'normal',
        'priority'    => 'low',
        'show_names'  => true, // Show field names on the left
        'fields'      => apply_filters(
            'wilcity/general-settings/fields',
            [
                array(
                    'type'         => 'text',
                    'id'           => $prefix.'tagline',
                    'name'         => esc_html__('Tagline', 'wilcity'),
                    'placeholder'  => '',
                    'default'      => '',
                ),
                array(
                    'type'         => 'file',
                    'id'           => $prefix.'logo',
                    'name'         => esc_html__('Logo', 'wilcity'),
                    'placeholder'  => '',
                    'default'      => ''
                ),
                array(
                    'type'         => 'file',
                    'id'           => $prefix.'cover_image',
                    'name'         => esc_html__('Cover Image', 'wilcity'),
                    'placeholder'  => '',
                    'default'      => ''
                ),
                array(
                    'type'         => 'text',
                    'id'           => $prefix.'timezone',
                    'name'         => esc_html__('Timezone', 'wilcity'),
                    'placeholder'  => '',
                    'default'      => ''
                )
            ]
        )
    ),
    'wilcity_video'  => array(
	    'id'          => 'wilcity_video',
	    'title'       => esc_html__( 'Video', 'wilcity' ),
	    'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false) : array('listing'),
	    'context'     => 'normal',
	    'priority'    => 'low',
	    'type'        => 'group',
	    'show_names'  => true, // Show field names on the left
	    'group_settings' => array(
		    'id'          => 'wilcity_video_srcs',
		    'type'        => 'group',
		    'options'     => array(
			    'group_title'   => esc_html__( 'Video URL', 'wilcity' ), // since version 1.1.4, {#} gets replaced by row number
			    'add_button'    => esc_html__( 'Add Video', 'wilcity' ),
			    'remove_button' => esc_html__( 'Remove Video', 'wilcity' ),
			    'sortable'      => true,
			    'closed'        => true
		    )
	    ),
	    'group_fields' => array(
		    array(
		    	'name' => 'Source',
			    'id'    => 'src',
			    'type'  => 'text',
		    ),
		    array(
			    'name' => 'Thumbnail',
			    'id'   => 'thumbnail',
			    'type' => 'file',
		    )
	    )
    ),
    'wilcity_gallery_settings' => array(
	    'id'          => 'wilcity_gallery_settings',
	    'title'       => esc_html__( 'Gallery', 'wilcity' ),
	    'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false) : array('listing'),
	    'context'     => 'normal',
	    'priority'    => 'low',
	    'show_names'  => true, // Show field names on the left
	    'fields'      => array(
		    array(
			    'name' => esc_html__('Upload Images', 'wilcity'),
			    'id'   => $prefix . 'gallery',
			    'type' => 'file_list',
			    'preview_size'  => 'thumbnail',
			    'query_args'    => array('type' => 'image')
		    )
	    )
    ),
    'wilcity_google_address'  => array(
	    'id'          => 'wilcity_google_address',
	    'title'       => esc_html__( 'Google Address', 'wilcity' ),
	    'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, false) : array('listing'),
	    'context'     => 'normal',
	    'priority'    => 'low',
	    'show_names'  => true, // Show field names on the left
	    'fields'      => array(
		    array(
			    'name' => esc_html__('Location', 'wilcity'),
			    'id' => $prefix . 'location',
			    'type' => 'wiloke_map',
				'split_values' => true, // Save latitude and longitude as two separate fields
		    )
	    )
    ),
	'wilcity_contact_info' => array(
		'id'          => 'wilcity_contact_info',
		'title'       => esc_html__( 'Contact Information', 'wilcity' ),
		'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false) : array('listing'),
		'context'     => 'normal',
		'priority'    => 'low',
		'save_field'  => false,
		'show_names'  => true, // Show field names on the left
		'fields'      => array(
			array(
				'name' => esc_html__('Email', 'wilcity'),
				'id' => $prefix . 'email',
				'type' => 'text_email'
			),
			array(
				'name' => esc_html__('Phone', 'wilcity'),
				'id' => $prefix . 'phone',
				'type' => 'text'
			),
			array(
				'name' => esc_html__('Website', 'wilcity'),
				'id' => $prefix . 'website',
				'type' => 'text_url'
			)
		)
	),
    'wilcity_social_networks' => array(
	    'id'          => 'wilcity_social_networks',
	    'title'       => esc_html__( 'Social Networks', 'wilcity' ),
	    'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false) : array('listing'), // Post type
	    'context'     => 'normal',
	    'priority'    => 'low',
	    'show_names'  => true, // Show field names on the left
	    'fields'      => array(
		    array(
			    'name'  => esc_html__('Social Networks', 'wilcity'),
			    'id'    => 'wilcity_social_networks',
			    'type'  => 'wilcity_social_networks'
		    )
	    )
    ),
	'wilcity_single_price' => array(
		'id'          => 'wilcity_single_price',
		'title'       => 'Single Price',
		'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, false) : array('listing'),
		'context'     => 'normal',
		'priority'    => 'low',
		'show_names'  => true, // Show field names on the left
		'fields'      => array(
			array(
				'type' => 'text',
				'id'   => $prefix . 'single_price',
				'name' => 'Price',
				'description' => 'It is suitable for Fixed Price purpose like Real Stable, Rent House',
			)
		)
	),
    'wilcity_price_range' => array(
	    'id'          => 'wilcity_price_range',
	    'title'       => esc_html__( 'Price Range', 'wilcity' ),
	    'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, false) : array('listing'),
	    'context'     => 'normal',
	    'priority'    => 'low',
	    'show_names'  => true, // Show field names on the left
	    'fields'      => array(
	    	array(
	    		'type'      => 'select',
			    'id'        => $prefix . 'price_range',
			    'name'      => esc_html__('Price Range', 'wilcity'),
			    'description' => 'Eg: You can set Price Range for a Restaurant listing',
			    'options'   => apply_filters('wilcity/filter/price-range-options', array(
				    'nottosay'      => esc_html__('Not to say', 'wilcity'),
				    'cheap'         => esc_html__('Cheap', 'wilcity'),
				    'moderate'      => esc_html__('Moderate', 'wilcity'),
				    'expensive'     => esc_html__('Expensive', 'wilcity'),
				    'ultra_high'    => esc_html__('Ultra High', 'wilcity'),
			    ))
		    ),
			array(
				'type' => 'text',
				'id'   => $prefix . 'price_range_desc',
				'name' => esc_html__('Price Range Description', 'wilcity')
			),
		    array(
			    'type' => 'text',
			    'id'   => $prefix . 'minimum_price',
			    'name' => esc_html__('Minimum Price', 'wilcity')
		    ),
		    array(
			    'type' => 'text',
			    'id'   => $prefix . 'maximum_price',
			    'name' => esc_html__('Maximum Price', 'wilcity')
		    )
	    )
    ),
    'wilcity_expiry' => array(
	    'id'          => 'wilcity_post_expiry',
	    'title'       => esc_html__( 'Expiration', 'wilcity' ),
	    'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false) : array('listing'),
	    'context'     => 'normal',
	    'priority'    => 'low',
	    'show_names'  => true, // Show field names on the left
	    'fields'      => array(
		    array(
			    'type'          => 'text_datetime_timestamp',
			    'id'            => $prefix . 'post_expiry',
			    'default_cb'    => array('WilokeListingTools\Controllers\PostController', 'setDefaultExpiration'),
			    'name'          => esc_html__('Listing Expiry', 'wilcity'),
			    'description'   => esc_html__('Empty means forever. Date Format: Month/Day/Year', 'wilcity')
		    )
	    )
    ),
    'wilcity_belongs_to' => array(
	    'id'          => 'wilcity_belongs_to',
	    'title'       => esc_html__( 'Belongs To', 'wilcity' ),
	    'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false) : array('listing'),
	    'context'     => 'normal',
	    'priority'    => 'low',
	    'show_names'  => true, // Show field names on the left
	    'fields'      => array(
		    array(
			    'type'      => 'select2_posts',
			    'id'        => $prefix.'belongs_to',
			    'name'      => 'Belongs To Plan',
			    'attributes' => array(
				    'ajax_action' => 'wiloke_fetch_posts',
				    'post_types'  => 'listing_plan'
			    )
		    )
	    )
    ),
	'wilcity_coupon' => array(
		'id'          => 'wilcity_coupon',
		'title'       => 'Coupon Settings',
		'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true) : array('listing'),
		'context'     => 'normal',
		'priority'    => 'low',
		'show_names'  => true, // Show field names on the left
		'save_field'  => false,
		'fields'      => array(
			array(
				'type' => 'text',
				'id'   => 'wilcity_coupon[highlight]',
				'name' => 'Highlight',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Coupon', 'getHighlight'),
			),
			array(
				'type' => 'text',
				'id'   => 'wilcity_coupon[title]',
				'name' => 'Title',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Coupon', 'getTitle'),
			),
			array(
				'type' => 'textarea',
				'id'   => 'wilcity_coupon[description]',
				'name' => 'Description',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Coupon', 'getDescription'),
			),
			array(
				'type' => 'text',
				'id'   => 'wilcity_coupon[code]',
				'name' => 'Code',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Coupon', 'getCode')
			),
			array(
				'type' => 'file',
				'id'   => 'wilcity_coupon[popup_image]',
				'name' => 'Popup Image',
				'options' => array(
					'url' => false, // Hide the text input for the url
				),
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Coupon', 'getPopupImage'),
			),
			array(
				'type' => 'textarea',
				'id'   => 'wilcity_coupon[popup_description]',
				'name' => 'Popup Description',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Coupon', 'getPopupDescription'),
			),
			array(
				'type' => 'text',
				'id'   => 'wilcity_coupon[redirect_to]',
				'name' => 'Redirect To',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Coupon', 'getRedirectTo'),
				'description' => 'The popup won\'t show if the coupon is not empty'
			),
			array(
				'type' => 'text_datetime_timestamp',
				'id'   => 'wilcity_coupon[expiry_date]',
				'name' => 'Expiry Date',
				'default_cb'   => array('WilokeListingTools\MetaBoxes\Coupon', 'getExpiry'),
				'description' => 'The popup won\'t show if the coupon is not empty'
			)
		)
	),
    'wilcity_claim' => array(
	    'id'          => 'wilcity_claim',
	    'title'       => esc_html__( 'Claim Listing', 'wilcity' ),
	    'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true) : array('listing'),
	    'context'     => 'normal',
	    'priority'    => 'low',
	    'show_names'  => true, // Show field names on the left
	    'fields'      => array(
		    array(
			    'type' => 'select',
			    'id'   => $prefix . 'claim_status',
			    'name' => esc_html__('Listing Status', 'wilcity'),
			    'options' => array(
			    	'not_claim' => 'Not Claim',
				    'claimed'   => 'Claimed'
			    )
		    )
	    )
    ),
	'wilcity_custom_button' => array(
		'id'          => 'wilcity_custom_button',
		'title'       => esc_html__( 'Add a button to your Page', 'wilcity' ),
		'object_types'=> class_exists('\WilokeListingTools\Framework\Helpers\General') ? \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true) : array('listing'),
		'context'     => 'normal',
		'priority'    => 'low',
		'show_names'  => true, // Show field names on the left
		'fields'      => array(
			array(
				'type' => 'text',
				'id'   => $prefix . 'button_name',
				'name' => esc_html__('Button Name', 'wilcity'),
				'default' => '+ Add a Button'
			),
			array(
				'type' => 'text',
				'id'   => $prefix . 'button_link',
				'name' => esc_html__('Button Link', 'wilcity'),
				'default' => ''
			),
			array(
				'type' => 'text',
				'id'   => $prefix . 'button_icon',
				'name' => esc_html__('Button Icon', 'wilcity'),
				'description' => Wiloke::ksesHTML(__('Go to <a href="https://icons8.com/line-awesome" target="_blank">LineIcon</a> to find your icon', 'wilcity'), true),
				'default' => ''
			)
		)
	),
	'wilcity_event_template' => array(
		'id'          => 'wilcity_event_template',
		'title'       => esc_html__( 'Event Template Settings', 'wilcity' ),
		'object_types'=> array('page'),
		'show_on'     => array( 'key' => 'page-template', 'value' => 'templates/event-template.php' ),
		'context'     => 'normal',
		'priority'    => 'low',
		'show_names'  => true, // Show field names on the left
		'fields'      => array(
			array(
				'type' => 'select',
				'id'   => $prefix . 'sidebar',
				'name' => esc_html__('Sidebar Position', 'wilcity'),
				'default'=>'right',
				'options' => array(
					'right' => 'Right Sidebar',
					'left'  => 'Left Sidebar',
					'no'    => 'No Sidebar'
				)
			),
			array(
				'type'   => 'text',
				'id'     => $prefix . 'events_per_page',
				'name'   => esc_html__('Events Per Page', 'wilcity'),
				'default'=>8
			),
			array(
				'type' => 'select',
				'id'   => $prefix . 'maximum_posts_on_lg_screen',
				'name' => esc_html__('Events / Row (Screen >= 1200px)', 'wilcity'),
				'default'=>'col-lg-6',
				'options' => array(
					'col-lg-6' => '2 events / row',
					'col-lg-4' => '3 events / row',
					'col-lg-3' => '4 events / row',
				)
			),
			array(
				'type' => 'select',
				'id'   => $prefix . 'maximum_posts_on_md_screen',
				'name' => esc_html__('Events / Row (Screen >= 992px)', 'wilcity'),
				'default'=>'col-md-6',
				'options' => array(
					'col-md-6' => '2 events / row',
					'col-md-4' => '3 events / row',
					'col-md-3' => '4 events / row',
				)
			),
			array(
				'type' => 'select',
				'id'   => $prefix . 'maximum_posts_on_sm_screen',
				'name' => esc_html__('Events / Row (Screen < 992px)', 'wilcity'),
				'default'=>'col-sm-6',
				'options' => array(
					'col-sm-6' => '2 events / row',
					'col-sm-4' => '3 events / row',
					'col-sm-3' => '4 events / row',
				)
			)
		),
	)
);
