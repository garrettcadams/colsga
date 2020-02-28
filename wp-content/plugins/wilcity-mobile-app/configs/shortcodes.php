<?php
use \WILCITY_SC\SCHelpers;
use \WilokeListingTools\Framework\Helpers\General;


$aOptionPostTypes = array();
$getPostTypeKeys = General::getPostTypes(false);
$directoryTypeKeys = '';
foreach ($getPostTypeKeys as $directoryType => $aType){
	$aOptionPostTypes[$directoryType]  = $aType['name'];
    $directoryTypeKeys .= empty($directoryTypeKeys) ? $directoryTypeKeys : '|' . $directoryTypeKeys;
}

return [
    'wilcity_app_hero'               => [
        'name'     => 'App Hero',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'name'        => 'heading',
                    'label'       => 'Title',
                    'type'        => 'text',
                    'value'       => 'Explore This City',
                    'admin_label' => false
                ],
                [
                    'name'        => 'heading_color',
                    'label'       => 'Title Color',
                    'type'        => 'color_picker',
                    'admin_label' => false
                ],
                [
                    'name'        => 'description',
                    'label'       => 'Description',
                    'type'        => 'textarea',
                    'admin_label' => false
                ],
                [
                    'name'        => 'description_color',
                    'label'       => 'Description Color',
                    'type'        => 'color_picker',
                    'admin_label' => false
                ],
                [
                    'name'  => 'image_bg',
                    'label' => 'Background Image',
                    'type'  => 'attach_image_url'
                ],
                [
                    'name'        => 'overlay_color',
                    'label'       => 'Overlay Color',
                    'type'        => 'color_picker',
                    'admin_label' => false
                ]
            ]
        ]
    ],
    'wilcity_app_heading'            => [
        'name'     => 'App Heading',
        'icon'     => 'sl-paper-plane',
        'css_box'  => true,
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'type'  => 'color_picker',
                    'name'  => 'blur_mark_color',
                    'label' => 'Blur Mark Color',
                    'value' => ''
                ],
                [
                    'type'  => 'text',
                    'name'  => 'heading',
                    'label' => 'Heading',
                    'value' => ''
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'heading_color',
                    'label' => 'Heading Color',
                    'value' => '#252c41'
                ],
                [
                    'type'  => 'textarea',
                    'name'  => 'description',
                    'label' => 'Description',
                    'value' => ''
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'description_color',
                    'label' => 'Description Color',
                    'value' => '#70778b'
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ],
                [
                    'type'    => 'select',
                    'name'    => 'alignment',
                    'label'   => 'Alignment',
                    'value'   => 'wil-text-center',
                    'options' => [
                        'wil-text-center' => 'Center',
                        'wil-text-right'  => 'Right',
                        'wil-text-left'   => 'Left'
                    ]
                ]
            ]
        ]
    ],
    'wilcity_app_term_boxes'         => [
        'name'     => 'App Term Boxes',
        'icon'     => 'sl-paper-plane',
        'css_box'  => true,
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'name'        => 'taxonomy',
                    'label'       => 'Taxonomy',
                    'type'        => 'select',
                    'value'       => 'listing_cat',
                    'options'     => [
                        'listing_cat'      => 'Listing Category',
                        'listing_location' => 'Listing Location',
                        'listing_tag'      => 'Listing Tag'
                    ],
                    'admin_label' => true
                ],
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Categories (Optional)',
                    'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                    'name'        => 'listing_cats',
                    'relation'    => [
                        'parent'    => 'taxonomy',
                        'show_when' => ['taxonomy', '=', 'listing_cat']
                    ]
                ],
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Locations (Optional)',
                    'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                    'name'        => 'listing_locations',
                    'relation'    => [
                        'parent'    => 'taxonomy',
                        'show_when' => ['taxonomy', '=', 'listing_location']
                    ]
                ],
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Tags (Optional)',
                    'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                    'name'        => 'listing_tags',
                    'relation'    => [
                        'parent'    => 'taxonomy',
                        'show_when' => ['taxonomy', '=', 'listing_tag']
                    ]
                ],
                [
                    'name'        => 'orderby',
                    'label'       => 'Order By',
                    'description' => 'This feature is not available if the "Select Locations/Select Tags/Select Categories" is not empty',
                    'type'        => 'select',
                    'value'       => 'count',
                    'options'     => [
                        'count'      => 'Number of children',
                        'name'       => 'Term Name',
                        'term_order' => 'Term Order',
                        'id'         => 'Term ID',
                        'slug'       => 'Term Slug',
                        'none'       => 'None'
                    ]
                ],
                [
                    'name'    => 'order',
                    'label'   => 'Order',
                    'type'    => 'select',
                    'value'   => 'DESC',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC'
                    ]
                ],
                [
                    'name'    => 'style',
                    'label'   => 'Style',
                    'type'    => 'select',
                    'value'   => 'modern_slider',
                    'options' => [
                        'modern_slider' => 'Modern Slider',
                        'grid'          => 'Grid'
                    ]
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ]
            ]
        ]
    ],
    'wilcity_app_listings_on_mobile' => [
        'name'     => 'App Listings',
        'icon'     => 'sl-paper-plane',
        'css_box'  => true,
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'name'        => 'post_type',
                    'label'       => 'Directory Type',
                    'type'        => 'select',
                    'value'       => 'listing',
                    'admin_label' => true,
                    'options'     => SCHelpers::getPostTypeKeys(true)
                ],
                [
                    'type'  => 'autocomplete',
                    'label' => 'Select Categories',
                    'name'  => 'listing_cats'
                ],
                [
                    'type'  => 'autocomplete',
                    'label' => 'Select Locations',
                    'name'  => 'listing_locations'
                ],
                [
                    'type'  => 'autocomplete',
                    'label' => 'Select Tags',
                    'name'  => 'listing_tags'
                ],
                [
                    'type'  => 'text',
                    'label' => 'Maximum Items',
                    'name'  => 'posts_per_page',
                    'value' => 6
                ],
                [
                    'type'        => 'text',
                    'label'       => 'Image Size',
                    'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                    'name'        => 'img_size',
                    'value'       => 'wilcity_360x200'
                ],
                [
                    'type'    => 'select',
                    'label'   => 'Layout',
                    'name'    => 'style',
                    'options' => [
                        'grid'          => 'Grid',
                        'modern_slider' => 'Modern Slider',
                        'simple_slider' => 'Simple Slider'
                    ],
                    'value'   => 'grid'
                ],
                [
                    'type'    => 'select',
                    'label'   => 'Order By',
                    'name'    => 'orderby',
                    'options' => [
                        'post_date'   => 'Listing Date',
                        'post_title'  => 'Listing Title',
                        'menu_order'  => 'Listing Order',
                        'best_viewed' => 'Popular Viewed',
                        'best_rated'  => 'Popular Rated',
                        'best_shared' => 'Popular Shared',
                        'rand'        => 'Random'
                    ]
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ]
            ]
        ]
    ],
    'wilcity_app_listing_blocks'     => [
        'name'     => 'App Listing Blocks',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'name'        => 'heading',
                    'label'       => 'Heading',
                    'type'        => 'text',
                    'value'       => '',
                    'admin_label' => true
                ],
                [
                    'name'        => 'listing_type',
                    'label'       => 'Listing Type',
                    'type'        => 'select',
                    'is_multiple' => true,
                    'value'       => '',
                    'admin_label' => true,
                    'options'     => SCHelpers::getPostTypeKeys(true)
                ],
                [
                    'name'  => 'items_per_column',
                    'label' => 'Items per column',
                    'type'  => 'text',
                    'value' => 3
                ],
                [
                    'name'  => 'number_of_blocks',
                    'label' => 'Number of blocks',
                    'type'  => 'text',
                    'value' => 3
                ],
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Categories',
                    'description' => 'it accepts 1 category only',
                    'name'        => 'listing_cats'
                ],
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Locations',
                    'description' => 'it accepts 1 location only',
                    'name'        => 'listing_locations'
                ],
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Tags',
                    'description' => 'it accepts 1 tag only',
                    'name'        => 'listing_tags'
                ],
                [
                    'type'    => 'select',
                    'label'   => 'Order By',
                    'name'    => 'orderby',
                    'options' => [
                        'post_date'   => 'Listing Date',
                        'post_title'  => 'Listing Title',
                        'menu_order'  => 'Listing Order',
                        'best_viewed' => 'Popular Viewed',
                        'best_rated'  => 'Popular Rated',
                        'best_shared' => 'Popular Shared',
                        'rand'        => 'Random'
                    ]
                ],
                [
                    'type'    => 'select',
                    'label'   => 'Order',
                    'name'    => 'order',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC'
                    ]
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ]
            ]
        ]
    ],
    'wilcity_kc_events_mobile'       => [
        'name'     => 'App Events',
        'icon'     => 'sl-paper-plane',
        'css_box'  => true,
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Tags',
                    'description' => 'Get Listings from the specified Tags. Leave empty to set all Tags.',
                    'name'        => 'listing_tags'
                ],
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Categories',
                    'description' => 'Get Categories from the specified Categories. Leave empty to set all Categories.',
                    'name'        => 'listing_cats'
                ],
                [
                    'type'        => 'autocomplete',
                    'label'       => 'Select Locations',
                    'description' => 'Get Locations from the specified Locations. Leave empty to set all Locations.',
                    'name'        => 'listing_locations'
                ],
                [
                    'type'    => 'select',
                    'label'   => 'Order By',
                    'name'    => 'orderby',
                    'options' => [
                        'post_date'       => 'Listing Date',
                        'post_title'      => 'Listing Title',
                        'menu_order'      => 'Listing Order',
                        'upcoming_event'  => 'Upcoming Events',
                        'happening_event' => 'Happening Events',
                        'rand'            => 'Random'
                    ]
                ],
                [
                    'type'    => 'select',
                    'label'   => 'Order',
                    'name'    => 'order',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC'
                    ]
                ],
                [
                    'type'  => 'text',
                    'label' => 'Maximum Items',
                    'name'  => 'posts_per_page',
                    'value' => 6
                ],
                [
                    'type'        => 'text',
                    'label'       => 'Image Size',
                    'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                    'name'        => 'img_size',
                    'value'       => 'wilcity_360x200'
                ],
                [
                    'type'    => 'select',
                    'label'   => 'Layout',
                    'name'    => 'style',
                    'options' => [
                        'grid'          => 'Grid',
                        'simple_slider' => 'Simple Slider'
                    ],
                    'value'   => 'grid'
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ]
            ]
        ]
    ],
    'wilcity_google_admobs'          => [
        'name'     => 'App AdMob',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            [
                'type'    => 'select',
                'label'   => 'Custom Banner Size',
                'name'    => 'banner_size_type',
                'default' => 'default',
                'options' => [
                    'default' => 'Inherit Theme Options Settings',
                    'custom'  => 'Custom'
                ]
            ],
            [
                'type'     => 'text',
                'name'     => 'custom_banner_size',
                'label'    => 'Custom Banner Size',
                'relation' => [
                    'parent'    => 'banner_size_type',
                    'show_when' => [
                        'custom'
                    ]
                ],
                'value'    => ''
            ]
        ]
    ],
    'wilcity_listing_type_boxes'     => [
        'name'     => 'App Listing Type Boxes',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'name'        => 'except_directory_types',
                    'label'       => 'Except Directory Type',
                    'type'        => 'multiple',
                    'is_multiple' => true,
                    'value'       => '',
                    'admin_label' => true,
                    'options'     => General::getPostTypeKeys(false, false)
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ],
                [
                    'name'    => 'items_per_row',
                    'label'   => 'Directory Types / Row',
                    'type'    => 'select',
                    'value'   => 2,
                    'options' => [
                        '1' => 1 / 1,
                        '2' => 2 / 1,
                        '3' => 3 / 1
                    ]
                ]
            ]
        ]
    ],
    'wilcity_reviews'                => [
        'name'     => 'App Reviews',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'name'    => 'style',
                    'label'   => 'Style',
                    'type'    => 'select',
                    'value'   => 'grid',
                    'options' => [
                        'grid'   => 'Grid',
                        'slider' => 'Slider'
                    ]
                ],
                [
                    'name'  => 'number_of_reviews',
                    'label' => 'Number Of reviews',
                    'type'  => 'text',
                    'value' => 4
                ],
                [
                    'name'     => 'items_per_row',
                    'label'    => 'Items per row',
                    'type'     => 'text',
                    'value'    => 2,
                    'relation' => [
                        'parent'    => 'style',
                        'show_when' => ['style', '=', 'grid']
                    ]
                ],
                [
                    'name'    => 'orderby',
                    'label'   => 'Order By',
                    'type'    => 'select',
                    'value'   => 'top_liked',
                    'options' => [
                        'top_liked'          => 'Number of Likes',
                        'top_discussions'    => 'Number of Discussions',
                        'latest'             => 'Latest Reviews',
                        'specify_review_ids' => 'Specify Review IDs'
                    ]
                ],
                [
                    'name'     => 'review_ids',
                    'label'    => 'Review IDs',
                    'type'     => 'autocomplete',
                    'value'    => '',
                    'relation' => [
                        'parent'    => 'orderby',
                        'show_when' => ['orderby', '=', 'specify_review_ids']
                    ]
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ]
            ]
        ]
    ],
    'wilcity_app_products'           => [
        'name'        => 'App Productions',
        'description' => 'Warning: It will not show up Booking Product on this shortcode',
        'icon'        => 'sl-paper-plane',
        'category'    => WILCITY_MOBILE_CAT,
        'params'      => [
            'general' => [
                [
                    'name'    => 'style',
                    'label'   => 'Style',
                    'type'    => 'select',
                    'value'   => 'grid',
                    'options' => [
                        'grid'   => 'Grid',
                        'slider' => 'Slider'
                    ]
                ],
                [
                    'name'     => 'maximum_posts',
                    'label'    => 'Number of products',
                    'type'     => 'text',
                    'value'    => 4,
                    'relation' => [
                        'parent'    => 'orderby',
                        'hide_when' => ['orderby', '=', 'specify_product_ids']
                    ]
                ],
                [
                    'name'     => 'items_per_row',
                    'label'    => 'Items per row',
                    'type'     => 'text',
                    'value'    => 2,
                    'relation' => [
                        'parent'    => 'style',
                        'show_when' => ['style', '=', 'grid']
                    ]
                ],
                [
                    'type'  => 'autocomplete',
                    'label' => 'Get Products in the following Categories',
                    'name'  => 'product_cats'
                ],
                [
                    'name'    => 'orderby',
                    'label'   => 'Order By',
                    'type'    => 'select',
                    'value'   => 'recent_products',
                    'options' => [
                        'recent_products'     => 'Latest Products',
                        'top_sales'           => 'Best Selling Products',
                        'featured_products'   => 'Featured products',
                        //                        'top_rated_products'  => 'Top Rated Products',
                        'sale_products'       => 'Sale Rated Products',
                        'title'               => 'Title',
                        'specify_product_ids' => 'Specify Product IDs'
                    ]
                ],
                [
                    'type'     => 'autocomplete',
                    'label'    => 'Specify Products',
                    'name'     => 'product_ids',
                    'relation' => [
                        'parent'    => 'orderby',
                        'show_when' => ['orderby', '=', 'specify_product_ids']
                    ]
                ],
                [
                    'name'    => 'order',
                    'label'   => 'Order',
                    'type'    => 'select',
                    'value'   => 'DESC',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC'
                    ]
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ]
            ]
        ]
    ],
    'wilcity_app_product_blocks'     => [
        'name'        => 'App Product Blocks',
        'description' => 'Warning: It will not show up Booking Product on this shortcode',
        'icon'        => 'sl-paper-plane',
        'category'    => WILCITY_MOBILE_CAT,
        'params'      => [
            'general' => [
                [
                    'name'  => 'heading',
                    'label' => 'Heading',
                    'type'  => 'text',
                    'value' => ''
                ],
                [
                    'name'  => 'items_per_column',
                    'label' => 'Items per column',
                    'type'  => 'text',
                    'value' => 3
                ],
                [
                    'name'  => 'number_of_blocks',
                    'label' => 'Number of blocks',
                    'type'  => 'text',
                    'value' => 3
                ],
                [
                    'type'  => 'autocomplete',
                    'label' => 'Get Products in the following Categories',
                    'name'  => 'product_cats'
                ],
                [
                    'name'    => 'orderby',
                    'label'   => 'Order By',
                    'type'    => 'select',
                    'value'   => 'recent_products',
                    'options' => [
                        'recent_products'     => 'Latest Products',
                        'top_sales'           => 'Best Selling Products',
                        'featured_products'   => 'Featured products',
                        'sale_products'       => 'Sale Rated Products',
                        'title'               => 'Title',
                        'specify_product_ids' => 'Specify Product IDs'
                    ]
                ],
                [
                    'type'     => 'autocomplete',
                    'label'    => 'Specify Products',
                    'name'     => 'product_ids',
                    'relation' => [
                        'parent'    => 'orderby',
                        'show_when' => ['orderby', '=', 'specify_product_ids']
                    ]
                ],
                [
                    'name'    => 'order',
                    'label'   => 'Order',
                    'type'    => 'select',
                    'value'   => 'DESC',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC'
                    ]
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ]
            ]
        ]
    ],
    'wilcity_external_banner'        => [
        'name'     => 'External Banner',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                'name'  => 'image_bg',
                'label' => 'Image Banner',
                'type'  => 'attach_image_url'
            ],
            [
                'type'  => 'text',
                'name'  => 'link_to',
                'label' => 'Link To',
                'value' => ''
            ],
            [
                'type'  => 'color_picker',
                'name'  => 'bg_color',
                'label' => 'Background Color',
                'value' => ''
            ]
        ]
    ],
    'wilcity_app_bookings'           => [
        'name'     => 'App Booking',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            'general' => [
                [
                    'name'    => 'style',
                    'label'   => 'Style',
                    'type'    => 'select',
                    'value'   => 'grid',
                    'options' => [
                        'grid'   => 'Grid',
                        'slider' => 'Slider'
                    ]
                ],
                [
                    'name'     => 'maximum_posts',
                    'label'    => 'Number of products',
                    'type'     => 'text',
                    'value'    => 4,
                    'relation' => [
                        'parent'    => 'orderby',
                        'hide_when' => ['orderby', '=', 'specify_product_ids']
                    ]
                ],
                [
                    'name'     => 'items_per_row',
                    'label'    => 'Items per row',
                    'type'     => 'text',
                    'value'    => 2,
                    'relation' => [
                        'parent'    => 'style',
                        'show_when' => ['style', '=', 'grid']
                    ]
                ],
                [
                    'type'  => 'autocomplete',
                    'label' => 'Get Products in the following Categories',
                    'name'  => 'product_cats'
                ],
                [
                    'name'    => 'orderby',
                    'label'   => 'Order By',
                    'type'    => 'select',
                    'value'   => 'recent_products',
                    'options' => [
                        'recent_products'     => 'Latest Products',
                        'featured_products'   => 'Featured Products',
                        'top_rated_products'  => 'Top Rated Products',
                        'title'               => 'Title',
                        'specify_product_ids' => 'Specify Product IDs'
                    ]
                ],
                [
                    'type'     => 'autocomplete',
                    'label'    => 'Specify Products',
                    'name'     => 'product_ids',
                    'relation' => [
                        'parent'    => 'orderby',
                        'show_when' => ['orderby', '=', 'specify_product_ids']
                    ]
                ],
                [
                    'name'    => 'order',
                    'label'   => 'Order',
                    'type'    => 'select',
                    'value'   => 'DESC',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC'
                    ]
                ]
            ]
        ]
    ],
    'wilcity_app_booking_blocks'     => [
        'name'        => 'App Booking Blocks',
        'icon'        => 'sl-paper-plane',
        'category'    => WILCITY_MOBILE_CAT,
        'params'      => [
            'general' => [
                [
                    'name'  => 'heading',
                    'label' => 'Heading',
                    'type'  => 'text',
                    'value' => ''
                ],
                [
                    'name'  => 'items_per_column',
                    'label' => 'Items per column',
                    'type'  => 'text',
                    'value' => 3
                ],
                [
                    'name'  => 'number_of_blocks',
                    'label' => 'Number of blocks',
                    'type'  => 'text',
                    'value' => 3
                ],
                [
                    'type'  => 'autocomplete',
                    'label' => 'Get Products in the following Categories',
                    'name'  => 'product_cats'
                ],
                [
                    'name'    => 'orderby',
                    'label'   => 'Order By',
                    'type'    => 'select',
                    'value'   => 'recent_products',
                    'options' => [
                        'recent_products'     => 'Latest Products',
                        'featured_products'   => 'Featured Products',
                        'top_rated_products'  => 'Top Rated Products',
                        'title'               => 'Title',
                        'specify_product_ids' => 'Specify Product IDs'
                    ]
                ],
                [
                    'type'     => 'autocomplete',
                    'label'    => 'Specify Products',
                    'name'     => 'product_ids',
                    'relation' => [
                        'parent'    => 'orderby',
                        'show_when' => ['orderby', '=', 'specify_product_ids']
                    ]
                ],
                [
                    'name'    => 'order',
                    'label'   => 'Order',
                    'type'    => 'select',
                    'value'   => 'DESC',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC'
                    ]
                ],
                [
                    'type'  => 'color_picker',
                    'name'  => 'bg_color',
                    'label' => 'Background Color',
                    'value' => ''
                ]
            ]
        ]
    ],
  'wilcity_external_banners'       => [
        'name'     => 'External Banners',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            [
                'type'   => 'group',
                'label'  => 'Banner',
                'name'   => 'banners',
                'params' => [
                    [
                        'name'        => 'image',
                        'label'       => 'Image Banner',
                        'type'        => 'attach_image_url',
                        'admin_label' => false
                    ],
                    [
                        'type'  => 'text',
                        'name'  => 'link_to',
                        'label' => 'Link To',
                        'value' => ''
                    ],
                ]
            ],
            [
                'type'  => 'text',
                'label' => 'Slide Interval (milliseconds)',
                'name'  => 'slide_interval',
                'value' => 3000
            ]
        ]
    ],
    'wilcity_listing_banners'        => [
        'name'     => 'Listing Banners',
        'icon'     => 'sl-paper-plane',
        'category' => WILCITY_MOBILE_CAT,
        'params'   => [
            [
                'type'   => 'group',
                'label'  => 'Banner',
                'name'   => 'banners',
                'params' => [
                    [
                        'name'        => 'image',
                        'label'       => 'Image Banner',
                        'type'        => 'attach_image_url',
                        'admin_label' => false
                    ],
                    [
                        'type'      => 'autocomplete',
                        'name'      => 'postID',
                        'label'     => 'Select Listing',
                        'post_type' => $directoryTypeKeys,
                        'value'     => ''
                    ],
                ]
            ],
            [
                'type'  => 'text',
                'label' => 'Slide Interval (milliseconds)',
                'name'  => 'slide_interval',
                'value' => 3000
            ]
        ]
    ]
];
