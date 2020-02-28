<?php
use WILCITY_SC\SCHelpers;
use WilokeListingTools\Framework\Helpers\General;

$aPricingOptions = ['Depends on Listing Type Request' => 'flexible'];
$aPostTypes      = ['listing' => 'listing'];
$aAllPostTypes   = ['post' => 'post', 'listing' => 'listing'];

if (class_exists('WilokeListingTools\Framework\Helpers\General')) {
    $aPostTypes      = General::getPostTypeKeys(false, false);
    $aPostTypes      = array_combine($aPostTypes, $aPostTypes);
    $aPricingOptions = $aPricingOptions + $aPostTypes;
    
    $aAllPostTypes = SCHelpers::getPostTypeKeys(true);
    $aAllPostTypes = array_combine($aAllPostTypes, $aAllPostTypes);
}

$aContactForm['No contact forms found'] = 0;
if (defined('WPCF7_VERSION')) {
    $cf7          = get_posts('post_type="wpcf7_contact_form"&numberposts=-1');
    $aContactForm = [];
    if ($cf7) {
        foreach ($cf7 as $cform) {
            $aContactForm[$cform->post_title] = $cform->ID;
        }
    }
}

return [
    [
        'name'                    => 'Heading',
        'base'                    => 'wilcity_vc_heading',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'        => 'textfield',
                'param_name'  => 'blur_mark',
                'heading'     => 'Blur Mark',
                'std'         => '',
                'save_always' => true
            ],
            [
                'type'        => 'colorpicker',
                'param_name'  => 'blur_mark_color',
                'heading'     => 'Blur Mark Color',
                'std'         => '',
                'save_always' => true
            ],
            [
                'type'        => 'textfield',
                'param_name'  => 'heading',
                'heading'     => 'Heading',
                'std'         => '',
                'save_always' => true
            ],
            [
                'type'        => 'colorpicker',
                'param_name'  => 'heading_color',
                'heading'     => 'Heading Color',
                'std'         => '#252c41',
                'save_always' => true
            ],
            [
                'type'        => 'textarea',
                'param_name'  => 'description',
                'heading'     => 'Description',
                'std'         => '',
                'save_always' => true
            ],
            [
                'type'        => 'colorpicker',
                'param_name'  => 'description_color',
                'heading'     => 'Description Color',
                'std'         => '#70778b',
                'save_always' => true
            ],
            [
                'type'        => 'dropdown',
                'param_name'  => 'alignment',
                'heading'     => 'Alignment',
                'std'         => 'wil-text-center',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Right'  => 'wil-text-right',
                    'Left'   => 'wil-text-left'
                ],
                'save_always' => true
            ]
        ]
    ],
    [
        'name'                    => 'Testimonials',
        'base'                    => 'wilcity_vc_testimonials',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name'  => 'autoplay',
                'heading'     => 'Auto Play',
                'description' => 'Leave empty to disable this feature. Or specify auto-play each x seconds',
                'type'        => 'textfield',
                'std'         => ''
            ],
            [
                'param_name' => 'testimonials',
                'heading'    => 'Testimonials',
                'type'       => 'param_group',
                'std'        => '',
                'params'     => [
                    [
                        'type'       => 'textfield',
                        'heading'    => 'Customer Name',
                        'param_name' => 'name'
                    ],
                    [
                        'type'       => 'textarea',
                        'heading'    => 'Testimonial',
                        'param_name' => 'testimonial'
                    ],
                    [
                        'type'       => 'textfield',
                        'heading'    => 'Customer Profesional',
                        'param_name' => 'profesional'
                    ],
                    [
                        'type'       => 'attach_image',
                        'heading'    => 'Avatar',
                        'param_name' => 'avatar'
                    ]
                ]
            ]
        ]
    ],
    [
        'name'                    => 'Wiloke Wave',
        'base'                    => 'wilcity_vc_wiloke_wave',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name' => 'heading',
                'heading'    => 'Heading',
                'type'       => 'textfield'
            ],
            [
                'param_name' => 'description',
                'heading'    => 'Description',
                'type'       => 'textarea',
                'value'      => ''
            ],
            [
                'type'        => 'colorpicker',
                'param_name'  => 'left_gradient_color',
                'heading'     => 'Left Gradient Color',
                'save_always' => true,
                'std'         => '#f06292'
            ],
            [
                'type'        => 'colorpicker',
                'param_name'  => 'right_gradient_color',
                'heading'     => 'Right Gradient Color',
                'std'         => '#f97f5f',
                'save_always' => true,
            ],
            [
                'param_name' => 'btn_group',
                'heading'    => 'Buttons Group',
                'type'       => 'param_group',
                'value'      => '',
                'params'     => [
                    [
                        'type'       => 'iconpicker',
                        'heading'    => 'Icon',
                        'param_name' => 'icon'
                    ],
                    [
                        'type'       => 'textfield',
                        'heading'    => 'Button name',
                        'param_name' => 'name'
                    ],
                    [
                        'type'       => 'textfield',
                        'heading'    => 'Button URL',
                        'param_name' => 'url'
                    ],
                    [
                        'type'        => 'dropdown',
                        'heading'     => 'Open Type',
                        'param_name'  => 'open_type',
                        'value'       => [
                            'In the same window' => '_self',
                            'In a New Window'    => '_blank'
                        ],
                        'std'         => '_self',
                        'save_always' => true
                    ]
                ]
            ]
        ]
    ],
    [
        'name'                    => 'Wiloke Icon',
        'base'                    => 'wilcity_vc_box_icon',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name' => 'icon',
                'heading'    => 'Icon',
                'type'       => 'iconpicker',
                'value'      => ''
            ],
            [
                'param_name' => 'heading',
                'heading'    => 'Heading',
                'type'       => 'textfield',
                'value'      => ''
            ],
            [
                'param_name' => 'description',
                'heading'    => 'Description',
                'type'       => 'textarea',
                'value'      => ''
            ],
        ]
    ],
    [
        'name'                    => 'Listings Grid Layout',
        'base'                    => 'wilcity_vc_listing_grip_layout',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Description',
                'param_name' => 'desc'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Description Color',
                'param_name' => 'desc_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Heading and Description Alignment',
                'param_name'  => 'header_desc_text_align',
                'std'         => '',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Toggle Viewmore',
                'param_name'  => 'toggle_viewmore',
                'std'         => '',
                'value'       => [
                    'Disable' => 'disable',
                    'Enable'  => 'enable'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Button Name',
                'param_name'  => 'viewmore_btn_name',
                'std'         => 'View more',
                'dependency'  => [
                    'element' => 'toggle_viewmore',
                    'value'   => ['enable']
                ],
                'save_always' => true
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Style',
                'param_name'  => 'style',
                'std'         => '',
                'value'       => [
                    'Grid'   => 'grid',
                    'Grid 2' => 'grid2',
                    'List'   => 'list'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Border',
                'description' => 'Adding a border around Listing Grid',
                'param_name'  => 'border',
                'std'         => '',
                'value'       => [
                    'Enable'  => 'border-gray-1',
                    'Disable' => 'border-gray-0'
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'post_type',
                'heading'     => 'Post Type',
                'type'        => 'dropdown',
                'std'         => 'listing',
                'save_always' => true,
                'admin_label' => true,
                'value'       => $aAllPostTypes
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Tags',
                'param_name' => 'listing_tags',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Categories',
                'param_name' => 'listing_cats',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Locations',
                'param_name' => 'listing_locations',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Specify Listing IDs',
                'param_name' => 'listing_ids',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Order By',
                'param_name'  => 'orderby',
                'std'         => 'post_date',
                'value'       => [
                    'Listing Date'                   => 'post_date',
                    'Listing Title'                  => 'post_title',
                    'Popular Viewed'                 => 'best_viewed',
                    'Popular Rated'                  => 'best_rated',
                    'best_shared'                    => 'best_shared',
                    'Random'                         => 'rand',
                    'Near By Me'                     => 'nearbyme',
                    'Open now'                       => 'open_now',
                    'Like Specify Listing IDs field' => 'post__in',
                    'Premium Listings'               => 'premium_listings'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Radius',
                'description' => 'Fetching all listings within x radius',
                'param_name'  => 'radius',
                'std'         => 10,
                'save_always' => true,
                'dependency'  => [
                    'element' => 'orderby',
                    'value'   => ['nearbyme']
                ]
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Unit',
                'param_name'  => 'unit',
                'dependency'  => [
                    'element' => 'orderby',
                    'value'   => ['orderby', '=', 'nearbyme']
                ],
                'value'       => [
                    'KM'    => 'km',
                    'Miles' => 'm'
                ],
                'std'         => 'km',
                'save_always' => true
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Tab Name',
                'description' => 'If the grid layout is inside of a tab, we recommend putting the Tab ID to this field. If the tab is emptied, the listings will be shown after the browser is loaded. Otherwise, it will be shown after someone clicks on the Tab Name.',
                'param_name'  => 'tabname',
                'value'       => '',
                'element'     => [
                    'element' => 'orderby',
                    'value'   => ['orderby', '=', 'nearbyme']
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Maximum Items',
                'param_name'  => 'posts_per_page',
                'value'       => 6,
                'save_always' => true
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Image Size',
                'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                'param_name'  => 'img_size',
                'std'         => 'wilcity_360x200',
                'save_always' => true
            ],
            [
                'param_name'  => 'maximum_posts_on_lg_screen',
                'heading'     => 'Items / row on >=1200px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                'type'        => 'dropdown',
                'std'         => 'col-lg-4',
                'save_always' => true,
                'value'       => [
                    '6 Items / row' => 'col-lg-2',
                    '4 Items / row' => 'col-lg-3',
                    '3 Items / row' => 'col-lg-4',
                    '2 Items / row' => 'col-lg-6',
                    '1 Items / row' => 'col-lg-12'
                ],
                'group'       => 'Device Settings'
            ],
            [
                'param_name'  => 'maximum_posts_on_md_screen',
                'heading'     => 'Items / row on >=960px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                'type'        => 'dropdown',
                'value'       => [
                    '6 Items / row' => 'col-md-2',
                    '4 Items / row' => 'col-md-3',
                    '3 Items / row' => 'col-md-4',
                    '2 Items / row' => 'col-md-6',
                    '1 Items / row' => 'col-md-12'
                ],
                'std'         => 'col-md-3',
                'save_always' => true,
                'group'       => 'Device Settings'
            ],
            [
                'param_name'  => 'maximum_posts_on_sm_screen',
                'heading'     => 'Items / row on >=720px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                'type'        => 'dropdown',
                'value'       => [
                    '6 Items / row' => 'col-sm-2',
                    '4 Items / row' => 'col-sm-3',
                    '3 Items / row' => 'col-sm-4',
                    '2 Items / row' => 'col-sm-6',
                    '1 Items / row' => 'col-sm-12'
                ],
                'std'         => 'col-sm-12',
                'group'       => 'Device Settings',
                'save_always' => true
            ],
            [
                'type'       => 'css_editor',
                'heading'    => 'CSS',
                'param_name' => 'css',
                'group'      => 'Design Options'
            ]
        ]
    ],
    [
        'name'                    => 'Restaurant Listings',
        'base'                    => 'wilcity_vc_restaurant_listings',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name' => 'heading_style',
                'heading'    => 'Heading style',
                'type'       => 'dropdown',
                'std'        => 'ribbon',
                'value'      => [
                    'ribbon'  => 'ribbon',
                    'default' => 'default'
                ]
            ],
            [
                'param_name'  => 'ribbon',
                'heading'     => 'Ribbon',
                'type'        => 'textfield',
                'value'       => 'Menu',
                'dependency'  => [
                    'element' => 'heading_style',
                    'value'   => ['ribbon']
                ],
                'admin_label' => true
            ],
            [
                'param_name'  => 'ribbon_color',
                'heading'     => 'Ribbon Color',
                'type'        => 'textfield',
                'value'       => '#fff',
                'dependency'  => [
                    'element' => 'heading_style',
                    'value'   => ['ribbon']
                ],
                'admin_label' => true
            ],
            [
                'param_name'  => 'heading',
                'heading'     => 'Heading',
                'type'        => 'textfield',
                'value'       => 'Our Special Menu',
                'admin_label' => true
            ],
            [
                'param_name'  => 'heading_color',
                'heading'     => 'Heading Color',
                'type'        => 'colorpicker',
                'value'       => '',
                'admin_label' => true
            ],
            [
                'param_name'  => 'desc',
                'heading'     => 'Description',
                'type'        => 'textarea',
                'value'       => 'Explore Delicious Flavour',
                'admin_label' => true
            ],
            [
                'param_name'  => 'desc_color',
                'heading'     => 'Description Color',
                'type'        => 'colorpicker',
                'value'       => '',
                'admin_label' => true
            ],
            [
                'param_name'  => 'header_desc_text_align',
                'heading'     => 'Heading and Description Text Alignment',
                'type'        => 'dropdown',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'std'         => 'wil-text-center',
                'dependency'  => [
                    'element' => 'heading_style',
                    'value'   => ['default']
                ],
                'admin_label' => true
            ],
            [
                'param_name' => 'excerpt_length',
                'heading'    => 'Excerpt Length',
                'type'       => 'textfield',
                'value'      => 100
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Toggle View More',
                'param_name' => 'toggle_viewmore',
                'value'      => [
                    'Disable' => 'disable',
                    'Enable'  => 'enable'
                ]
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Button Name',
                'param_name' => 'viewmore_btn_name',
                'dependency' => [
                    'element' => 'toggle_viewmore',
                    'value'   => [
                        'enable'
                    ]
                ],
                'std'        => 'View more'
            ],
            [
                'type'       => 'icon_picker',
                'heading'    => 'View More Icon',
                'param_name' => 'viewmore_icon',
                'dependency' => [
                    'element' => 'toggle_viewmore',
                    'value'   => [
                        'enable'
                    ]
                ]
            ],
            [
                'param_name'  => 'post_type',
                'heading'     => 'Post Type',
                'description' => 'We recommend using Using Belongs To Setting if this is <a href="https://documentation.wilcity.com/knowledgebase/customizing-listing-location-listing-category-page/" target="_blank">Customizing Taxonomy Page</a>',
                'type'        => 'dropdown',
                'std'         => 'listing',
                'admin_label' => true,
                'value'       => $aPostTypes
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Categories',
                'description' => 'Leave empty if you are working on Taxonomy Template',
                'param_name'  => 'listing_cats'
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Locations',
                'description' => 'Leave empty if you are working on Taxonomy Template',
                'param_name'  => 'listing_locations'
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Tags',
                'description' => 'Leave empty if you are working on Taxonomy Template',
                'param_name'  => 'listing_tags'
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Taxonomy Key',
                'description' => 'This feature is useful if you want to use show up your custom taxonomy',
                'param_name'  => 'custom_taxonomy_key'
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Your Custom Taxonomies IDs',
                'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
                'param_name'  => 'custom_taxonomies_id'
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Specify Listings',
                'description' => 'Leave empty if you are working on Taxonomy Template',
                'param_name'  => 'listing_ids'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Maximum Items',
                'param_name' => 'posts_per_page',
                'value'      => 6
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Order By',
                'description' => 'In order to use Order by Random, please disable the cache plugin or exclude this page from cache.',
                'param_name'  => 'orderby',
                'value'       => [
                    'Listing Order'                  => 'menu_order',
                    'Listing Date'                   => 'post_date',
                    'Listing Title'                  => 'post_title',
                    'Popular Viewed'                 => 'best_viewed',
                    'Popular Rated'                  => 'best_rated',
                    'Best Shared'                    => 'best_shared',
                    'Premium Listings'               => 'premium_listings',
                    'Like Specify Listing IDs field' => 'post__in',
                    'Random'                         => 'rand'
                ]
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Order',
                'param_name' => 'order',
                'value'      => [
                    'DESC' => 'DESC',
                    'ASC'  => 'ASC',
                ]
            ]
        ]
    ],
    [
        'name'                    => 'Hero Search Form',
        'base'                    => 'wilcity_vc_search_form',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name' => 'items',
                'heading'    => 'Search Tab',
                'type'       => 'param_group',
                'value'      => '',
                'params'     => [
                    [
                        'param_name'  => 'name',
                        'heading'     => 'Tab Name',
                        'type'        => 'textfield',
                        'std'         => 'Listing',
                        'save_always' => true,
                    ],
                    [
                        'param_name'  => 'post_type',
                        'heading'     => 'Directory Type',
                        'type'        => 'dropdown',
                        'std'         => 'listing',
                        'save_always' => true,
                        'value'       => $aPostTypes
                    ]
                ]
            ]
        ]
    ],
    [
        'name'                    => 'Hero',
        'base'                    => 'wilcity_vc_hero',
        'is_container'            => true,
        'as_parent'               => ['only' => 'wilcity_vc_search_form'],
        'content_element'         => true,
        'icon'                    => '',
        'show_settings_on_create' => false,
        'category'                => WILCITY_VC_SC,
        'params'                  => [
            [
                'param_name'  => 'heading',
                'heading'     => 'Title',
                'type'        => 'textfield',
                'std'         => 'Explore This City',
                'save_always' => true,
                'admin_label' => false
            ],
            [
                'param_name'  => 'heading_color',
                'heading'     => 'Heading Color',
                'type'        => 'colorpicker',
                'save_always' => true
            ],
            [
                'param_name'  => 'heading_font_size',
                'description' => 'Eg: 100px',
                'heading'     => 'Heading Font Size',
                'type'        => 'textfield',
                'save_always' => true
            ],
            [
                'param_name'  => 'description',
                'heading'     => 'Description',
                'type'        => 'textarea',
                'admin_label' => false
            ],
            [
                'param_name'  => 'description_color',
                'heading'     => 'Description Color',
                'type'        => 'colorpicker',
                'save_always' => true
            ],
            [
                'param_name'  => 'description_font_size',
                'description' => 'Eg: 17px',
                'heading'     => 'Description Font Size',
                'type'        => 'textfield',
                'save_always' => true
            ],
            [
                'param_name'  => 'toggle_button',
                'heading'     => 'Toggle Button',
                'type'        => 'dropdown',
                'admin_label' => false,
                'std'         => 'enable',
                'save_always' => true,
                'value'       => [
                    'Enable'  => 'enable',
                    'Disable' => 'disable'
                ]
            ],
            [
                'param_name'  => 'button_icon',
                'heading'     => 'Button Icon',
                'std'         => 'la la-pencil-square',
                'type'        => 'iconpicker',
                'admin_label' => false,
                'save_always' => true,
                'dependency'  => [
                    'element' => 'toggle_button',
                    'value'   => ['enable']
                ]
            ],
            [
                'param_name'  => 'button_name',
                'heading'     => 'Button Name',
                'std'         => 'Check out',
                'type'        => 'textfield',
                'admin_label' => false,
                'save_always' => true,
                'dependency'  => [
                    'element' => 'toggle_button',
                    'value'   => ['enable']
                ]
            ],
            [
                'param_name'  => 'button_link',
                'heading'     => 'Button Link',
                'type'        => 'textfield',
                'std'         => '#',
                'admin_label' => false,
                'save_always' => true,
                'dependency'  => [
                    'element' => 'toggle_button',
                    'value'   => ['enable']
                ]
            ],
            [
                'param_name'  => 'button_background_color',
                'heading'     => 'Button Background Color',
                'type'        => 'colorpicker',
                'save_always' => true
            ],
            [
                'param_name'  => 'button_text_color',
                'heading'     => 'Button Text Color',
                'type'        => 'colorpicker',
                'std'         => '#fff',
                'save_always' => true
            ],
            [
                'param_name'  => 'button_size',
                'heading'     => 'Button Size',
                'type'        => 'dropdown',
                'default'     => 'wil-btn--sm',
                'save_always' => true,
                'value'       => [
                    'Small'  => 'wil-btn--sm',
                    'Medium' => 'wil-btn--md',
                    'Large'  => 'wil-btn--lg',
                ]
            ],
            [
                'param_name'  => 'toggle_dark_and_white_background',
                'heading'     => 'Toggle Dark and White Background',
                'type'        => 'dropdown',
                'default'     => 'disable',
                'save_always' => true,
                'value'       => [
                    'Enable'  => 'enable',
                    'Disable' => 'disable'
                ]
            ],
            [
                'param_name' => 'bg_overlay',
                'heading'    => 'Background Overlay',
                'type'       => 'colorpicker',
                'default'    => ''
            ],
            [
                'param_name' => 'bg_type',
                'heading'    => 'Is Using Slider Background?',
                'type'       => 'dropdown',
                'default'    => 'image',
                'value'      => [
                    'Image Background'  => 'image',
                    'Slider Background' => 'slider'
                ]
            ],
            [
                'param_name'  => 'image_bg',
                'heading'     => 'Background Image',
                'type'        => 'attach_image',
                'save_always' => true,
                'dependency'  => [
                    'element' => 'bg_type',
                    'value'   => ['image']
                ]
            ],
            [
                'param_name'  => 'slider_bg',
                'heading'     => 'Background Slider',
                'type'        => 'attach_images',
                'save_always' => true,
                'dependency'  => [
                    'element' => 'bg_type',
                    'value'   => ['slider']
                ]
            ],
            [
                'param_name' => 'img_size',
                'heading'    => 'Image Size',
                'type'       => 'textfield',
                'default'    => 'large'
            ],
            [
                'param_name'  => 'search_form_position',
                'heading'     => 'Search Form Style',
                'type'        => 'dropdown',
                'admin_label' => false,
                'std'         => 'bottom',
                'save_always' => true,
                'value'       => [
                    'Right of Screen' => 'right',
                    'Bottom'          => 'bottom'
                ],
                'group'       => 'Search Form'
            ],
            [
                'param_name'  => 'search_form_background',
                'heading'     => 'Search Form Background',
                'type'        => 'dropdown',
                'admin_label' => false,
                'std'         => 'hero_formDark__3fCkB',
                'save_always' => true,
                'value'       => [
                    'White' => 'hero_formWhite__3fCkB',
                    'Black' => 'hero_formDark__3fCkB'
                ],
                'group'       => 'Search Form'
            ],
            [
                'param_name'  => 'toggle_list_of_suggestions',
                'heading'     => 'Toggle The List Of Suggestions',
                'description' => 'A list of suggestion locations/categories will be shown on the Hero section if this feature is enabled.',
                'type'        => 'dropdown',
                'save_always' => true,
                'value'       => [
                    'Enable'  => 'enable',
                    'Disable' => 'disable'
                ],
                'std'         => 'enable'
            ],
            [
                'param_name' => 'maximum_terms_suggestion',
                'heading'    => 'Maximum Locations / Categories',
                'type'       => 'textfield',
                'std'        => 6
            ],
            [
                'param_name'  => 'taxonomy',
                'heading'     => 'Get By',
                'type'        => 'dropdown',
                'save_always' => true,
                'value'       => [
                    'Listing Category' => 'listing_cat',
                    'Listing Location' => 'listing_location'
                ],
                'std'         => 'listing_cat'
            ],
            [
                'param_name'  => 'orderby',
                'heading'     => 'Order By',
                'type'        => 'dropdown',
                'save_always' => true,
                'value'       => [
                    'Number of children'           => 'count',
                    'ID'                           => 'id',
                    'Slug'                         => 'slug',
                    'Specify Locations/Categories' => 'specify_terms'
                ],
                'std'         => 'count'
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Categories',
                'description' => 'This feature is available for Order By Specify Categories',
                'param_name'  => 'listing_cats',
                'save_always' => true,
                'dependence'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_cat']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Locations (Optional)',
                'description' => 'This feature is available for Order By Specify Locations',
                'param_name'  => 'listing_locations',
                'save_always' => true,
                'dependence'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_locations']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ]
        ],
        'js_view'                 => 'VcColumnView'
    ],
    [
        'name'                    => 'Rectangle Term Boxes',
        'base'                    => 'wilcity_vc_rectangle_term_boxes',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Description',
                'param_name' => 'description'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Description Color',
                'param_name' => 'description_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Heading and Description Alignment',
                'param_name'  => 'header_desc_text_align',
                'std'         => '',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'taxonomy',
                'heading'     => 'Taxonomy',
                'type'        => 'dropdown',
                'std'         => 'listing_cat',
                'save_always' => true,
                'value'       => [
                    'Listing Category'         => 'listing_cat',
                    'Listing Location'         => 'listing_location',
                    'Listing Tag'              => 'listing_tag',
                    'Depends on Taxonomy Page' => '_self'
                ],
                'admin_label' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Categories (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_cats',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_cat']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Locations (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_locations',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_location']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Tags (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_tags',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_tag']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'param_name'  => 'items_per_row',
                'heading'     => 'Items Per Row',
                'type'        => 'dropdown',
                'std'         => 'col-lg-3',
                'save_always' => true,
                'value'       => [
                    '6 Items / row' => 'col-lg-2',
                    '4 Items / row' => 'col-lg-3',
                    '3 Items / row' => 'col-lg-4'
                ]
            ],
            [
                'param_name'  => 'is_show_parent_only',
                'heading'     => 'Show Parent Only',
                'type'        => 'dropdown',
                'std'         => 'no',
                'value'       => [
                    'No'  => 'no',
                    'Yes' => 'yes'
                ],
                'save_always' => true
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Maximum Items',
                'param_name' => 'number',
                'value'      => 6
            ],
            [
                'param_name'  => 'orderby',
                'heading'     => 'Order By',
                'description' => 'This feature is not available if the "Select Locations/Select Tags/Select Categories" is not empty',
                'type'        => 'dropdown',
                'std'         => 'count',
                'value'       => [
                    'Number of children' => 'count',
                    'Term Name'          => 'name',
                    'Term Order'         => 'term_order',
                    'Term ID'            => 'id',
                    'Term Slug'          => 'slug',
                    'Terms Included'     => 'include',
                    'None'               => 'none'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'order',
                'heading'     => 'Order',
                'type'        => 'dropdown',
                'std'         => 'DESC',
                'value'       => [
                    'DESC' => 'DESC',
                    'ASC'  => 'ASC'
                ],
                'save_always' => true,
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Image Size',
                'param_name' => 'image_size',
                'value'      => 'medium'
            ]
        ]
    ],
    [
        'name'                    => 'Term Boxes',
        'base'                    => 'wilcity_vc_term_boxes',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Description',
                'param_name' => 'description'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Description Color',
                'param_name' => 'description_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Heading and Description Alignment',
                'param_name'  => 'header_desc_text_align',
                'std'         => '',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'taxonomy',
                'heading'     => 'Taxonomy',
                'type'        => 'dropdown',
                'std'         => 'listing_cat',
                'save_always' => true,
                'value'       => [
                    'Listing Category'         => 'listing_cat',
                    'Listing Location'         => 'listing_location',
                    'Listing Tag'              => 'listing_tag',
                    'Depends on Taxonomy Page' => '_self'
                ],
                'admin_label' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Categories (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_cats',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_cat']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Locations (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_locations',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_location']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Tags (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_tags',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_tag']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'param_name'  => 'items_per_row',
                'heading'     => 'Items Per Row',
                'type'        => 'dropdown',
                'std'         => 'col-lg-3',
                'save_always' => true,
                'value'       => [
                    '6 Items / row' => 'col-lg-2',
                    '4 Items / row' => 'col-lg-3',
                    '3 Items / row' => 'col-lg-4',
                    '2 Items / row' => 'col-lg-6',
                    '1 Items / row' => 'col-lg-12'
                ]
            ],
            [
                'param_name'  => 'is_show_parent_only',
                'heading'     => 'Show Parent Only',
                'type'        => 'dropdown',
                'std'         => 'no',
                'value'       => [
                    'No'  => 'no',
                    'Yes' => 'yes'
                ],
                'save_always' => true
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Maximum Items',
                'param_name' => 'number',
                'value'      => 6
            ],
            [
                'param_name'  => 'orderby',
                'heading'     => 'Order By',
                'description' => 'This feature is not available if the "Select Locations/Select Tags/Select Categories" is not empty',
                'type'        => 'dropdown',
                'std'         => 'count',
                'value'       => [
                    'Number of children' => 'count',
                    'Term Name'          => 'name',
                    'Term Order'         => 'term_order',
                    'Term ID'            => 'id',
                    'Term Slug'          => 'slug',
                    'Terms Included'     => 'include',
                    'None'               => 'none'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'order',
                'heading'     => 'Order',
                'type'        => 'dropdown',
                'std'         => 'DESC',
                'value'       => [
                    'DESC' => 'DESC',
                    'ASC'  => 'ASC'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'toggle_box_gradient',
                'heading'     => 'Toggle Box Gradient',
                'description' => 'In order to use this feature, please upload a Featured Image to each Listing Location/Category: Listings -> Listing Locations / Categories -> Your Location/Category -> Featured Image.',
                'type'        => 'dropdown',
                'std'         => 'disable',
                'value'       => [
                    'Enable'  => 'enable',
                    'Disable' => 'disable'
                ],
                'group'       => 'Box Style',
                'save_always' => true,
            ],
            [
                'type'       => 'css_editor',
                'heading'    => 'CSS',
                'param_name' => 'css',
                'group'      => 'Design Options'
            ]
        ]
    ],
    [
        'name'                    => 'Modern Term Boxes',
        'base'                    => 'wilcity_vc_modern_term_boxes',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Description',
                'param_name' => 'description'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Description Color',
                'param_name' => 'description_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Heading and Description Alignment',
                'param_name'  => 'header_desc_text_align',
                'std'         => '',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'taxonomy',
                'heading'     => 'Taxonomy',
                'type'        => 'dropdown',
                'std'         => 'listing_cat',
                'value'       => [
                    'Listing Category'         => 'listing_cat',
                    'Listing Location'         => 'listing_location',
                    'Listing Tag'              => 'listing_tag',
                    'Depends on Taxonomy Page' => '_self'
                ],
                'admin_label' => true,
                'save_always' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Categories (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_cats',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_cat']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
                'save_always' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Locations (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_locations',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_location']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
                'save_always' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Tags (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_tags',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_tag']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'items_per_row',
                'heading'     => 'Items Per Row',
                'type'        => 'dropdown',
                'std'         => 'col-lg-3',
                'value'       => [
                    '6 Items / row' => 'col-lg-2',
                    '4 Items / row' => 'col-lg-3',
                    '3 Items / row' => 'col-lg-4',
                    '1 Items / row' => 'col-lg-12'
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'col_gap',
                'heading'     => 'Col Gap',
                'type'        => 'textfield',
                'std'         => 20,
                'save_always' => true
            ],
            [
                'param_name'  => 'image_size',
                'heading'     => 'Image Size',
                'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                'type'        => 'textfield',
                'std'         => 'wilcity_560x300',
                'save_always' => true
            ],
            [
                'param_name'  => 'is_show_parent_only',
                'heading'     => 'Show Parent Only',
                'type'        => 'dropdown',
                'std'         => 'no',
                'value'       => [
                    'No'  => 'no',
                    'Yes' => 'yes'
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'is_hide_empty',
                'heading'     => 'Hide Empty Term',
                'type'        => 'dropdown',
                'std'         => 'no',
                'value'       => [
                    'No'  => 'no',
                    'Yes' => 'yes'
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'is_hide_empty',
                'heading'     => 'Hide Empty Term',
                'type'        => 'dropdown',
                'std'         => 'no',
                'value'       => [
                    'No'  => 'no',
                    'Yes' => 'yes'
                ],
                'save_always' => true
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Maximum Items',
                'param_name' => 'number',
                'value'      => 6
            ],
            [
                'param_name'  => 'orderby',
                'heading'     => 'Order By',
                'description' => 'This feature is not available if the "Select Locations/Select Tags/Select Categories" is not empty',
                'type'        => 'dropdown',
                'std'         => 'count',
                'value'       => [
                    'Number of children' => 'count',
                    'Term Name'          => 'name',
                    'Term Order'         => 'term_order',
                    'Term ID'            => 'id',
                    'Term Slug'          => 'slug',
                    'Terms Included'     => 'include',
                    'None'               => 'none'
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'order',
                'heading'     => 'Order',
                'type'        => 'dropdown',
                'std'         => 'DESC',
                'value'       => [
                    'DESC' => 'DESC',
                    'ASC'  => 'ASC'
                ],
                'save_always' => true
            ],
            [
                'type'       => 'css_editor',
                'heading'    => 'CSS',
                'param_name' => 'css',
                'group'      => 'Design Options'
            ]
        ]
    ],
    [
        'name'                    => 'Masonry Term Boxes',
        'base'                    => 'wilcity_vc_masonry_term_boxes',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Description',
                'param_name' => 'description'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Description Color',
                'param_name' => 'description_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Heading and Description Alignment',
                'param_name'  => 'header_desc_text_align',
                'std'         => '',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'taxonomy',
                'heading'     => 'Taxonomy',
                'type'        => 'dropdown',
                'std'         => 'listing_cat',
                'value'       => [
                    'Listing Category'         => 'listing_cat',
                    'Listing Location'         => 'listing_location',
                    'Listing Tag'              => 'listing_tag',
                    'Depends on Taxonomy Page' => '_self'
                ],
                'admin_label' => true,
                'save_always' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Categories (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_cats',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_cat']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
                'save_always' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Locations (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_locations',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_location']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
                'save_always' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Tags (Optional)',
                'description' => 'If this setting is empty, it will get terms by "Order By" setting',
                'param_name'  => 'listing_tags',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_tag']
                ],
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'image_size',
                'heading'     => 'Image Size',
                'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                'type'        => 'textfield',
                'std'         => 'wilcity_560x300',
                'save_always' => true
            ],
            [
                'param_name'  => 'is_show_parent_only',
                'heading'     => 'Show Parent Only',
                'type'        => 'dropdown',
                'std'         => 'no',
                'value'       => [
                    'No'  => 'no',
                    'Yes' => 'yes'
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'is_hide_empty',
                'heading'     => 'Hide Empty Term',
                'type'        => 'dropdown',
                'std'         => 'no',
                'value'       => [
                    'No'  => 'no',
                    'Yes' => 'yes'
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'is_hide_empty',
                'heading'     => 'Hide Empty Term',
                'type'        => 'dropdown',
                'std'         => 'no',
                'value'       => [
                    'No'  => 'no',
                    'Yes' => 'yes'
                ],
                'save_always' => true
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Maximum Items',
                'param_name' => 'number',
                'value'      => 6
            ],
            [
                'param_name'  => 'orderby',
                'heading'     => 'Order By',
                'description' => 'This feature is not available if the "Select Locations/Select Tags/Select Categories" is not empty',
                'type'        => 'dropdown',
                'std'         => 'count',
                'value'       => [
                    'Number of children' => 'count',
                    'Term Name'          => 'name',
                    'Term Order'         => 'term_order',
                    'Term ID'            => 'id',
                    'Term Slug'          => 'slug',
                    'Terms Included'     => 'include',
                    'None'               => 'none'
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'order',
                'heading'     => 'Order',
                'type'        => 'dropdown',
                'std'         => 'DESC',
                'value'       => [
                    'DESC' => 'DESC',
                    'ASC'  => 'ASC'
                ],
                'save_always' => true
            ],
            [
                'type'       => 'css_editor',
                'heading'    => 'CSS',
                'param_name' => 'css',
                'group'      => 'Design Options'
            ]
        ]
    ],
    [
        'name'                    => 'Listings Slider',
        'base'                    => 'wilcity_vc_listings_slider',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Description',
                'param_name' => 'desc'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Description Color',
                'param_name' => 'desc_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Heading and Description Alignment',
                'param_name'  => 'header_desc_text_align',
                'std'         => '',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Toggle Viewmore',
                'param_name'  => 'toggle_viewmore',
                'std'         => '',
                'value'       => [
                    'Disable' => 'disable',
                    'Enable'  => 'enable'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Button Name',
                'param_name'  => 'viewmore_btn_name',
                'std'         => 'View more',
                'dependency'  => [
                    'element' => 'toggle_viewmore',
                    'value'   => ['enable']
                ],
                'save_always' => true
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Style',
                'param_name'  => 'style',
                'std'         => '',
                'value'       => [
                    'Grid'   => 'grid',
                    'Grid 2' => 'grid2',
                    'List'   => 'list'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'post_type',
                'heading'     => 'Post Type',
                'type'        => 'dropdown',
                'value'       => $aPostTypes,
                'std'         => 'listing',
                'admin_label' => true
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Tags',
                'param_name' => 'listing_tags',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Categories',
                'param_name' => 'listing_cats',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Locations',
                'param_name' => 'listing_locations',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Specify Listing IDs',
                'param_name' => 'listing_ids',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Maximum Items',
                'param_name' => 'posts_per_page',
                'value'      => 6
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Order By',
                'param_name' => 'orderby',
                'value'      => [
                    'Listing Date'                   => 'post_date',
                    'Listing Title'                  => 'post_title',
                    'Popular Viewed'                 => 'best_viewed',
                    'Popular Rated'                  => 'best_rated',
                    'Best Shared'                    => 'best_shared',
                    'Premium Listings'               => 'premium_listings',
                    'Like Specify Listing IDs field' => 'post__in',
                    'Random'                         => 'rand'
                ]
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Toggle Gradient',
                'param_name' => 'toggle_gradient',
                'value'      => [
                    'Enable'  => 'enable',
                    'Disable' => 'disable'
                ],
                'std'        => 'enable'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Left Gradient',
                'param_name' => 'left_gradient',
                'std'        => '#006bf7',
                'dependency' => [
                    'element' => 'toggle_gradient',
                    'value'   => ['enable']
                ]
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Right Gradient',
                'param_name' => 'right_gradient',
                'std'        => '#ed6392',
                'dependency' => [
                    'element' => 'toggle_gradient',
                    'value'   => ['enable']
                ]
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Opacity',
                'description' => 'The value must equal to or smaller than 1',
                'param_name'  => 'gradient_opacity',
                'std'         => '0.3',
                'dependency'  => [
                    'element' => 'toggle_gradient',
                    'value'   => ['enable']
                ]
            ],
            [
                'param_name'  => 'maximum_posts',
                'heading'     => 'Maximum Listings',
                'type'        => 'textfield',
                'std'         => 8,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'desktop_image_size',
                'heading'     => 'Desktop Image Size',
                'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                'type'        => 'textfield',
                'std'         => '',
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_extra_lg_screen',
                'heading'     => 'Items on >=1600px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1600px ',
                'type'        => 'textfield',
                'std'         => 6,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_lg_screen',
                'heading'     => 'Items on >=1400px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                'type'        => 'textfield',
                'std'         => 5,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_md_screen',
                'heading'     => 'Items on >=1200px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                'type'        => 'textfield',
                'std'         => 5,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_sm_screen',
                'heading'     => 'Items on >=992px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 992px ',
                'type'        => 'textfield',
                'std'         => 2,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_extra_sm_screen',
                'heading'     => 'Items on >=640px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                'type'        => 'textfield',
                'std'         => 1,
                'save_always' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'autoplay',
                'heading'     => 'Auto Play (in ms)',
                'type'        => 'textfield',
                'std'         => 3000,
                'save_always' => true,
                'group'       => 'Slider Configuration'
            ],
            [
                'type'       => 'css_editor',
                'heading'    => 'CSS',
                'param_name' => 'css',
                'group'      => 'Design Options'
            ]
        ]
    ],
    [
        'name'                    => 'Listings Tabs',
        'base'                    => 'wilcity_vc_listings_tabs',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Toggle Viewmore',
                'param_name'  => 'toggle_viewmore',
                'std'         => '',
                'value'       => [
                    'Disable' => 'disable',
                    'Enable'  => 'enable'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Get Listings in',
                'param_name'  => 'taxonomy',
                'std'         => 'listing_cat',
                'value'       => [
                    'Listing Categories' => 'listing_cat',
                    'Listing Locations'  => 'listing_location',
                    'Custom Taxonomy'    => 'custom'
                ],
                'save_always' => true,
            ],
            [
                'param_name'  => 'get_term_type',
                'heading'     => 'Get Terms Type',
                'type'        => 'dropdown',
                'value'       => [
                    'Get Term Children' => 'term_children',
                    'Specify Terms'     => 'specify_terms'
                ],
                'std'         => 'term_children',
                'admin_label' => true
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Listing Category/Categories',
                'description' => 'If you are using Get Term Children mode, you can enter in 1 Listing Category only',
                'param_name'  => 'listing_cats',
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_cat']
                ]
            ],
            [
                'type'        => 'autocomplete',
                'heading'     => 'Select Listing Location[s]',
                'description' => 'If you are using Get Term Children mode, you can enter in 1 Listing Location only',
                'param_name'  => 'listing_locations',
                'settings'    => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['listing_location']
                ]
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Taxonomy Key',
                'param_name' => 'custom_taxonomy_key',
                'value'      => '',
                'dependency' => [
                    'element' => 'taxonomy',
                    'value'   => ['custom']
                ]
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Taxonomy IDs',
                'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
                'param_name'  => 'custom_taxonomies_id',
                'value'       => '',
                'dependency'  => [
                    'element' => 'taxonomy',
                    'value'   => ['custom']
                ]
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Maximum Term Children',
                'param_name' => 'number_of_term_children',
                'std'        => 6
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Maximum Items',
                'param_name' => 'posts_per_page',
                'std'        => 6
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Order By',
                'param_name' => 'orderby',
                'value'      => [
                    'Listing Date'     => 'post_date',
                    'Listing Title'    => 'post_title',
                    'Popular Viewed'   => 'best_viewed',
                    'Popular Rated'    => 'best_rated',
                    'Best Shared'      => 'best_shared',
                    'Premium Listings' => 'premium_listings',
                    'Near By Me'       => 'nearbyme'
                ]
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Radius',
                'description' => 'Fetching all listings within x radius',
                'param_name'  => 'radius',
                'std'         => 10,
                'save_always' => true,
                'dependency'  => [
                    'element' => 'orderby',
                    'value'   => ['nearbyme']
                ]
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Order',
                'param_name' => 'order',
                'std'        => 'DESC',
                'value'      => [
                    'ASC'  => 'ASC',
                    'DESC' => 'DESC'
                ]
            ],
            [
                'type'        => 'checkbox',
                'heading'     => 'Post Type Filters',
                'param_name'  => 'post_types_filter',
                'std'         => '',
                'value'       => SCHelpers::getPostTypeKeys(false, false),
                'is_multiple' => true,
            ],
            [
                'param_name' => 'terms_tab_id',
                'heading'    => 'Wrapper ID (*)',
                'type'       => 'textfield',
                'std'        => uniqid('terms_tab_id')
            ],
            [
                'param_name' => 'image_size',
                'heading'    => 'Image Size',
                'type'       => 'textfield',
                'std'        => 'wilcity_360x200'
            ],
            [
                'param_name' => 'tab_alignment',
                'heading'    => 'Tab Alignment',
                'type'       => 'textfield',
                'std'        => 'wil-text-right',
                'value'      => [
                    'wil-text-center' => 'wil-text-center',
                    'wil-text-right'  => 'wil-text-right'
                ]
            ],
            [
                'param_name'  => 'maximum_posts_on_lg_screen',
                'heading'     => 'Items on >=1400px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                'type'        => 'dropdown',
                'std'         => 'col-lg-3',
                'admin_label' => true,
                'group'       => 'Listings On Screen',
                'value'       => [
                    '6 Items / row' => 'col-lg-2',
                    '4 Items / row' => 'col-lg-3',
                    '3 Items / row' => 'col-lg-4',
                    '2 Items / row' => 'col-lg-6',
                    '1 Items / row' => 'col-lg-12'
                ]
            ],
            [
                'param_name'  => 'maximum_posts_on_md_screen',
                'heading'     => 'Items on >=1200px',
                'type'        => 'dropdown',
                'std'         => 'col-md-3',
                'admin_label' => true,
                'value'       => [
                    '6 Items / row' => 'col-md-2',
                    '4 Items / row' => 'col-md-3',
                    '3 Items / row' => 'col-md-4',
                    '2 Items / row' => 'col-md-6',
                    '1 Items / row' => 'col-md-12'
                ],
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_sm_screen',
                'heading'     => 'Items on >=992px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 992px ',
                'type'        => 'textfield',
                'std'         => 'col-sm-12',
                'admin_label' => true,
                'value'       => [
                    '6 Items / row' => 'col-sm-2',
                    '4 Items / row' => 'col-sm-3',
                    '3 Items / row' => 'col-sm-4',
                    '2 Items / row' => 'col-sm-6',
                    '1 Items / row' => 'col-sm-12'
                ],
                'group'       => 'Listings On Screen'
            ],
            [
                'type'       => 'css_editor',
                'heading'    => 'CSS',
                'param_name' => 'css',
                'group'      => 'Design Options'
            ]
        ]
    ],
    [
        'name'                    => 'Events Slider',
        'base'                    => 'wilcity_vc_events_slider',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Description',
                'param_name' => 'desc'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Description Color',
                'param_name' => 'desc_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Heading and Description Alignment',
                'param_name'  => 'header_desc_text_align',
                'std'         => '',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Toggle Viewmore',
                'param_name'  => 'toggle_viewmore',
                'std'         => '',
                'value'       => [
                    'Disable' => 'disable',
                    'Enable'  => 'enable'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Button Name',
                'param_name'  => 'viewmore_btn_name',
                'std'         => 'View more',
                'dependency'  => [
                    'elemement' => 'toggle_viewmore',
                    'value'     => ['enable']
                ],
                'save_always' => true
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Tags',
                'param_name' => 'listing_tags',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Categories',
                'param_name' => 'listing_cats',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Locations',
                'param_name' => 'listing_locations',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Specify Listing IDs',
                'param_name' => 'listing_ids',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Order By',
                'param_name' => 'orderby',
                'value'      => [
                    'Like Specify Event IDs field' => 'post__in',
                    'Event Date'                   => 'post_date',
                    'Event Title'                  => 'post_title',
                    'Menu Order'                   => 'menu_order',
                    'Upcoming Event'               => 'upcoming_event',
                    'Happening Event'              => 'happening_event',
                    'Upcoming + Happening'         => 'starts_from_ongoing_event'
                ]
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Toggle Gradient',
                'param_name' => 'toggle_gradient',
                'value'      => [
                    'Enable'  => 'enable',
                    'Disable' => 'disable'
                ],
                'std'        => 'enable'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Left Gradient',
                'param_name' => 'left_gradient',
                'std'        => '#006bf7',
                'dependency' => [
                    'element' => 'toggle_gradient',
                    'value'   => ['enable']
                ]
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Right Gradient',
                'param_name' => 'right_gradient',
                'std'        => '#ed6392',
                'dependency' => [
                    'element' => 'toggle_gradient',
                    'value'   => ['enable']
                ]
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Opacity',
                'description' => 'The value must equal to or smaller than 1',
                'param_name'  => 'gradient_opacity',
                'std'         => '0.3',
                'dependency'  => [
                    'element' => 'toggle_gradient',
                    'value'   => ['enable']
                ]
            ],
            [
                'param_name'  => 'maximum_posts',
                'heading'     => 'Maximum Listings',
                'type'        => 'textfield',
                'std'         => 8,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'desktop_image_size',
                'heading'     => 'Desktop Image Size',
                'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                'type'        => 'textfield',
                'std'         => '',
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_extra_lg_screen',
                'heading'     => 'Items on >=1600px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1600px ',
                'type'        => 'textfield',
                'std'         => 6,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_lg_screen',
                'heading'     => 'Items on >=1400px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                'type'        => 'textfield',
                'std'         => 5,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_md_screen',
                'heading'     => 'Items on >=1200px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                'type'        => 'textfield',
                'std'         => 5,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_sm_screen',
                'heading'     => 'Items on >=992px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 992px ',
                'type'        => 'textfield',
                'std'         => 2,
                'admin_label' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'maximum_posts_on_extra_sm_screen',
                'heading'     => 'Items on >=640px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                'type'        => 'textfield',
                'std'         => 1,
                'save_always' => true,
                'group'       => 'Listings On Screen'
            ],
            [
                'param_name'  => 'is_auto_play',
                'heading'     => 'Is Auto Play',
                'type'        => 'dropdown',
                'value'       => [
                    'Enable'  => 'enable',
                    'Disable' => 'disable'
                ],
                'std'         => 'disable',
                'save_always' => true,
                'group'       => 'Slider Configuration'
            ],
            [
                'type'       => 'css_editor',
                'heading'    => 'CSS',
                'param_name' => 'css',
                'group'      => 'Design Options'
            ]
        ]
    ],
    [
        'name'                    => 'Pricing Table (Listing Packages)',
        'base'                    => 'wilcity_vc_pricing',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name'  => 'row',
                'heading'     => 'Items / Row',
                'type'        => 'dropdown',
                'admin_label' => true,
                'value'       => [  // THIS FIELD REQUIRED THE PARAM OPTIONS
                    '3 Items / Row' => 'col-md-4 col-lg-4',
                    '4 Items / Row' => 'col-md-3 col-lg-3',
                    '2 Items / Row' => 'col-md-6 col-lg-6',
                    '1 Item / Row'  => 'col-md-12 col-lg-12'
                ],
                'std'         => 'col-md-4 col-lg-4',
                'save_always' => true
            ],
            [
                'param_name'  => 'listing_type',
                'heading'     => 'Post Type',
                'type'        => 'dropdown',
                'admin_label' => true,
                'value'       => $aPricingOptions,
                'save_always' => true
            ],
            [
                'param_name' => 'toggle_nofollow',
                'heading'    => 'Add rel="nofollow" to Plan URL',
                'type'       => 'dropdown',
                'value'      => [
                    'disable' => 'Disable',
                    'enable'  => 'Enable'
                ],
                'std'        => 'disable'
            ]
        ]
    ],
    [
        'name'                    => 'Contact Us',
        'base'                    => 'wilcity_vc_contact_us',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name'  => 'contact_info_heading',
                'heading'     => 'Heading',
                'type'        => 'textfield',
                'std'         => 'Contact Info',
                'save_always' => true
            ],
            [
                'param_name'  => 'contact_info',
                'heading'     => 'Contact Info',
                'type'        => 'param_group',
                'std'         => '',
                'params'      => [
                    [
                        'type'        => 'iconpicker',
                        'heading'     => 'Icon',
                        'admin_label' => true,
                        'param_name'  => 'icon'
                    ],
                    [
                        'type'       => 'textarea',
                        'heading'    => 'Info',
                        'param_name' => 'info'
                    ],
                    [
                        'type'        => 'textfield',
                        'heading'     => 'link',
                        'description' => 'Enter in # if it is not a real link.',
                        'param_name'  => 'link'
                    ],
                    [
                        'type'       => 'dropdown',
                        'heading'    => 'Type',
                        'param_name' => 'type',
                        'std'        => 'default',
                        'value'      => [
                            'Default' => 'default',
                            'Phone'   => 'phone',
                            'Email'   => 'mail'
                        ]
                    ],
                    [
                        'type'        => 'dropdown',
                        'heading'     => 'Open Type',
                        'description' => 'After clicking on this link, it will be opened in',
                        'param_name'  => 'target',
                        'std'         => '_self',
                        'value'       => [
                            'Self page'  => '_self',
                            'New Window' => '_blank'
                        ]
                    ]
                ],
                'save_always' => true
            ],
            [
                'param_name'  => 'contact_form_heading',
                'heading'     => 'Heading',
                'type'        => 'textfield',
                'std'         => 'Contact Us',
                'group'       => 'Contact Form',
                'save_always' => true
            ],
            [
                'type'        => 'dropdown',
                'param_name'  => 'contact_form_7',
                'heading'     => 'Contact Form 7',
                'value'       => $aContactForm,
                'group'       => 'Contact Form',
                'save_always' => true
            ],
            [
                'type'        => 'textfield',
                'param_name'  => 'contact_form_shortcode',
                'heading'     => 'Contact Form Shortcode',
                'description' => 'If you are using another contact form plugin, please enter its own shortcode here.',
                'value'       => '',
                'group'       => 'Contact Form',
                'save_always' => true
            ]
        ]
    ],
    [
        'name'                    => 'Intro Box',
        'base'                    => 'wilcity_vc_intro_box',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name' => 'bg_img',
                'heading'    => 'Background Image',
                'type'       => 'attach_image',
                'value'      => ''
            ],
            [
                'param_name' => 'video_intro',
                'heading'    => 'Video Intro',
                'type'       => 'textfield',
                'value'      => ''
            ],
            [
                'param_name' => 'content',
                'heading'    => 'Intro',
                'type'       => 'textarea_html',
                'value'      => ''
            ]
        ]
    ],
    [
        'name'                    => 'Team Intro Slider',
        'base'                    => 'wilcity_vc_team_intro_slider',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name'  => 'get_by',
                'heading'     => 'Get users who are',
                'type'        => 'dropdown',
                'std'         => 'administrator',
                'save_always' => true,
                'value'       => [
                    'Administrator' => 'administrator',
                    'Editor'        => 'editor',
                    'Contributor'   => 'contributor',
                    'Custom'        => 'custom'
                ]
            ],
            [
                'param_name' => 'members',
                'heading'    => 'Members',
                'dependency' => [
                    'element' => 'get_by',
                    'value'   => ['custom']
                ],
                'type'       => 'param_group',
                'std'        => '',
                'params'     => [
                    [
                        'type'       => 'attach_image',
                        'heading'    => 'Avatar',
                        'param_name' => 'avatar'
                    ],
                    [
                        'type'       => 'attach_image',
                        'heading'    => 'Picture',
                        'param_name' => 'picture'
                    ],
                    [
                        'type'       => 'textfield',
                        'heading'    => 'Name',
                        'param_name' => 'display_name'
                    ],
                    [
                        'type'       => 'textfield',
                        'heading'    => 'Position',
                        'param_name' => 'position'
                    ],
                    [
                        'type'       => 'textarea',
                        'heading'    => 'Intro',
                        'param_name' => 'intro'
                    ],
                    [
                        'param_name'  => 'social_networks',
                        'heading'     => 'Social Networks',
                        'description' => 'Eg: facebook:https://facebook.com,google-plus:https://googleplus.com',
                        'type'        => 'textarea'
                    ]
                ]
            ]
        ]
    ],
    [
        'name'                    => 'Author Slider',
        'base'                    => 'wilcity_vc_author_slider',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name'  => 'role__in',
                'heading'     => 'Role in',
                'description' => 'Limit the returned users that have one of the specified roles',
                'type'        => 'checkbox',
                'is_multiple' => true,
                'save_always' => true,
                'std'         => 'administrator,contributor',
                'value'       => [
                    'Administrator' => 'administrator',
                    'Editor'        => 'editor',
                    'Contributor'   => 'contributor',
                    'Subscriber'    => 'subscriber',
                    'Vendor'        => 'vendor',
                    'author'        => 'Author'
                ]
            ],
            [
                'param_name'  => 'orderby',
                'heading'     => 'Order by',
                'type'        => 'dropdown',
                'save_always' => true,
                'std'         => 'post_count',
                'value'       => [
                    'Registered' => 'registered',
                    'Post Count' => 'post_count',
                    'ID'         => 'ID'
                ]
            ],
            [
                'param_name'  => 'number',
                'heading'     => 'Maximum Users',
                'save_always' => true,
                'type'        => 'textfield',
                'std'         => 8
            ]
        ]
    ],
    [
        'name'                    => 'Event Grid Layout',
        'base'                    => 'wilcity_vc_events_grid',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'type'       => 'textfield',
                'heading'    => 'Heading',
                'param_name' => 'heading'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Heading Color',
                'param_name' => 'heading_color'
            ],
            [
                'type'       => 'textfield',
                'heading'    => 'Description',
                'param_name' => 'desc'
            ],
            [
                'type'       => 'colorpicker',
                'heading'    => 'Description Color',
                'param_name' => 'desc_color'
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Heading and Description Alignment',
                'param_name'  => 'header_desc_text_align',
                'std'         => '',
                'value'       => [
                    'Center' => 'wil-text-center',
                    'Left'   => 'wil-text-left',
                    'Right'  => 'wil-text-right'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'dropdown',
                'heading'     => 'Toggle Viewmore',
                'param_name'  => 'toggle_viewmore',
                'std'         => '',
                'value'       => [
                    'Disable' => 'disable',
                    'Enable'  => 'enable'
                ],
                'save_always' => true,
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Button Name',
                'param_name'  => 'viewmore_btn_name',
                'std'         => 'View more',
                'dependency'  => [
                    'element' => 'toggle_viewmore',
                    'value'   => ['enable']
                ],
                'save_always' => true
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Tags',
                'param_name' => 'listing_tags',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Categories',
                'param_name' => 'listing_cats',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Select Locations',
                'param_name' => 'listing_locations',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ]
            ],
            [
                'type'       => 'autocomplete',
                'heading'    => 'Specify Listing IDs',
                'param_name' => 'listing_ids',
                'settings'   => [
                    'multiple' => true,
                    'sortable' => true,
                    'groups'   => true,
                ],
            ],
            [
                'type'       => 'dropdown',
                'heading'    => 'Order By',
                'param_name' => 'orderby',
                'value'      => [
                    'Like Specify Event IDs field' => 'post__in',
                    'Event Date'                   => 'post_date',
                    'Event Title'                  => 'post_title',
                    'Menu Order'                   => 'menu_order',
                    'Upcoming Event'               => 'upcoming_event',
                    'Happening Event'              => 'happening_event',
                    'Upcoming + Happening'         => 'starts_from_ongoing_event'
                ]
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Maximum Items',
                'param_name'  => 'posts_per_page',
                'value'       => 6,
                'save_always' => true
            ],
            [
                'type'        => 'textfield',
                'heading'     => 'Image Size',
                'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                'param_name'  => 'img_size',
                'std'         => 'wilcity_360x200',
                'save_always' => true
            ],
            [
                'param_name'  => 'maximum_posts_on_lg_screen',
                'heading'     => 'Items / row on >=1200px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                'type'        => 'dropdown',
                'std'         => 'col-lg-4',
                'save_always' => true,
                'value'       => [
                    '6 Items / row' => 'col-lg-2',
                    '4 Items / row' => 'col-lg-3',
                    '3 Items / row' => 'col-lg-4',
                    '2 Items / row' => 'col-lg-6',
                    '1 Items / row' => 'col-lg-12'
                ],
                'group'       => 'Device Settings'
            ],
            [
                'param_name'  => 'maximum_posts_on_md_screen',
                'heading'     => 'Items / row on >=960px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                'type'        => 'dropdown',
                'value'       => [
                    '6 Items / row' => 'col-md-2',
                    '4 Items / row' => 'col-md-3',
                    '3 Items / row' => 'col-md-4',
                    '2 Items / row' => 'col-md-6',
                    '1 Items / row' => 'col-md-12'
                ],
                'std'         => 'col-md-3',
                'save_always' => true,
                'group'       => 'Device Settings'
            ],
            [
                'param_name'  => 'maximum_posts_on_sm_screen',
                'heading'     => 'Items / row on >=720px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                'type'        => 'dropdown',
                'value'       => [
                    '6 Items / row' => 'col-sm-2',
                    '4 Items / row' => 'col-sm-3',
                    '3 Items / row' => 'col-sm-4',
                    '2 Items / row' => 'col-sm-6',
                    '1 Items / row' => 'col-sm-12'
                ],
                'std'         => 'col-sm-12',
                'group'       => 'Device Settings',
                'save_always' => true
            ],
            [
                'type'       => 'css_editor',
                'heading'    => 'CSS',
                'param_name' => 'css',
                'group'      => 'Design Options'
            ]
        ]
    ],
    [
        'name'                    => 'Custom Login',
        'base'                    => 'wilcity_vc_custom_login',
        'icon'                    => '',
        'show_settings_on_create' => true,
        'category'                => WILCITY_VC_SC,
        'controls'                => true,
        'params'                  => [
            [
                'param_name'  => 'login_section_title',
                'heading'     => 'Login Title',
                'type'        => 'textfield',
                'std'         => 'Welcome back, please login to your account',
                'save_always' => true
            ],
            [
                'param_name'  => 'register_section_title',
                'heading'     => 'Register Title',
                'type'        => 'textfield',
                'std'         => 'Create an account! It is free and always will be.',
                'save_always' => true
            ],
            [
                'param_name' => 'rp_section_title',
                'heading'    => 'Reset Password Title',
                'type'       => 'textfield',
                'value'      => 'Find Your Account'
            ],
            [
                'param_name'  => 'social_login_type',
                'heading'     => 'Social Login',
                'type'        => 'dropdown',
                'value'       => [
                    'Using Facebook Login as Default'   => 'fb_default',
                    'Inserting External Shortcode'      => 'custom_shortcode',
                    'I do not want to use this feature' => 'off'
                ],
                'std'         => 'fb_default',
                'save_always' => true
            ],
            [
                'param_name' => 'social_login_shortcode',
                'heading'    => 'Social Login Shortcode',
                'type'       => 'textfield',
                'dependency' => [
                    'element' => 'social_login_type',
                    'value'   => ['custom_shortcode']
                ],
                'std'        => ''
            ],
            [
                'param_name'  => 'login_bg_img',
                'heading'     => 'Background Image',
                'type'        => 'attach_image',
                'std'         => '',
                'save_always' => true
            ],
            [
                'param_name'  => 'login_bg_color',
                'heading'     => 'Background Color',
                'type'        => 'colorpicker',
                'std'         => 'rgba(216, 35, 112, 0.1)',
                'save_always' => true
            ],
            [
                'param_name' => 'login_boxes',
                'heading'    => 'Intro Box',
                'type'       => 'param_group',
                'value'      => '',
                'params'     => [
                    [
                        'type'       => 'iconpicker',
                        'heading'    => 'Icon',
                        'param_name' => 'icon'
                    ],
                    [
                        'type'       => 'textarea',
                        'heading'    => 'Description',
                        'param_name' => 'description'
                    ],
                    [
                        'type'        => 'colorpicker',
                        'heading'     => 'Icon Color',
                        'param_name'  => 'icon_color',
                        'std'         => '#fff',
                        'save_always' => true
                    ],
                    [
                        'type'        => 'colorpicker',
                        'heading'     => 'Text Color',
                        'param_name'  => 'text_color',
                        'std'         => '#fff',
                        'save_always' => true
                    ]
                ]
            ]
        ]
    ]
];
