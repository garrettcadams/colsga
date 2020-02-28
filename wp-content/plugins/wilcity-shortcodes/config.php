<?php
use WILCITY_SC\SCHelpers;
use WilokeListingTools\Framework\Helpers\General;

$aPricingOptions = ['flexible' => 'Depends on Listing Type Request'];
$aPostTypes      = General::getPostTypeKeys(false, false);
if (class_exists('WilokeListingTools\Framework\Helpers\General')) {
    //	$aPricingOptions = $aPricingOptions + array_combine($aPostTypes, $aPostTypes);
    if (!empty($aPostTypes)) {
        $aPricingOptions = array_merge($aPricingOptions, array_combine($aPostTypes, $aPostTypes));
    }
}

if (defined('KC_PATH')) {
    $live_tmpl = KC_PATH.KDS.'shortcodes'.KDS.'live_editor'.KDS;
} else {
    $live_tmpl = '';
}

return [
    'shortcodes'  => [
        'kc_tabs'                         => [
            'name'         => 'Tabs - Sliders',
            'description'  => 'Tabbed or Sliders content',
            'category'     => 'Content',
            'icon'         => 'kc-icon-tabs',
            'title'        => 'Tabs - Sliders Settings',
            'is_container' => true,
            'views'        => [
                'type'     => 'views_sections',
                'sections' => 'kc_tab'
            ],
            'priority'     => 120,
            'live_editor'  => $live_tmpl.'kc_tabs.php',
            'params'       => [
                'general' => [
                    [
                        'name'  => 'class',
                        'label' => 'Extra Class',
                        'type'  => 'text'
                    ],
                    [
                        'name'        => 'type',
                        'label'       => 'How Display',
                        'type'        => 'select',
                        'options'     => [
                            'horizontal_tabs' => 'Horizontal Tabs',
                            'vertical_tabs'   => 'Vertical Tabs',
                            'slider_tabs'     => 'Owl Sliders'
                        ],
                        'description' => 'Use sidebar view of your tabs as horizontal, vertical or slider.',
                        'value'       => 'horizontal_tabs'
                    ],
                    [
                        'name'        => 'title_slider',
                        'label'       => 'Display Titles?',
                        'type'        => 'toggle',
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'description' => 'Display tabs title above of the slider',
                    ],
                    [
                        'name'        => 'items',
                        'label'       => 'Number Items?',
                        'type'        => 'number_slider',
                        'options'     => [
                            'min'        => 1,
                            'max'        => 10,
                            'show_input' => true
                        ],
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'description' => 'Display number of items per each slide (Desktop Screen)'
                    ],
                    [
                        'name'        => 'tablet',
                        'label'       => 'Items on tablet?',
                        'type'        => 'number_slider',
                        'options'     => [
                            'min'        => 1,
                            'max'        => 6,
                            'show_input' => true
                        ],
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'description' => 'Display number of items per each slide (Tablet Screen)'
                    ],
                    [
                        'name'        => 'mobile',
                        'label'       => 'Items on smartphone?',
                        'type'        => 'number_slider',
                        'options'     => [
                            'min'        => 1,
                            'max'        => 4,
                            'show_input' => true
                        ],
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'description' => 'Display number of items per each slide (Smartphone Screen)'
                    ],
                    [
                        'name'        => 'speed',
                        'label'       => 'Speed of slider',
                        'type'        => 'number_slider',
                        'options'     => [
                            'min'        => 100,
                            'max'        => 1000,
                            'show_input' => true
                        ],
                        'value'       => 450,
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'description' => 'The speed of sliders in millisecond'
                    ],
                    [
                        'name'        => 'navigation',
                        'label'       => 'Navigation',
                        'type'        => 'toggle',
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'description' => 'Display the "Next" and "Prev" buttons.'
                    ],
                    [
                        'name'        => 'pagination',
                        'label'       => 'Pagination',
                        'type'        => 'toggle',
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'value'       => 'yes',
                        'description' => 'Show the pagination.',
                    ],
                    [
                        'name'        => 'autoplay',
                        'label'       => 'Auto Play',
                        'type'        => 'toggle',
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'description' => 'The slider automatically plays when site loaded'
                    ],
                    [
                        'name'        => 'autoheight',
                        'label'       => 'Auto Height',
                        'type'        => 'toggle',
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => 'slider_tabs'
                        ],
                        'description' => 'The slider height will change automatically'
                    ],
                    [
                        'name'        => 'effect_option',
                        'label'       => 'Enable fadein effect?',
                        'type'        => 'toggle',
                        'relation'    => [
                            'parent'    => 'type',
                            'hide_when' => 'slider_tabs'
                        ],
                        'description' => 'Quickly apply fade in and face out effect when users click on tab.'
                    ],
                    [
                        'name'     => 'tabs_position',
                        'label'    => 'Position',
                        'type'     => 'select',
                        'options'  => [
                            'wil-text-left'   => 'Left',
                            'wil-text-center' => 'Center',
                            'wil-text-right'  => 'Right'
                        ],
                        'relation' => [
                            'parent'    => 'type',
                            'show_when' => ['horizontal_tabs', 'vertical_tabs']
                        ]
                    ],
                    [
                        'name'        => 'nav_item_style',
                        'label'       => 'Nav Item Style',
                        'description' => 'The position of the tab name and icon',
                        'type'        => 'select',
                        'options'     => [
                            ''                     => 'Horizontal',
                            'wilTab_iconLg__2Ibz5' => 'Vertical '
                        ],
                        'relation'    => [
                            'parent'    => 'type',
                            'show_when' => ['horizontal_tabs', 'vertical_tabs']
                        ],
                        'value'       => ''
                    ],
                    [
                        'name'     => 'open_mouseover',
                        'label'    => 'Open on mouseover',
                        'type'     => 'toggle',
                        'relation' => [
                            'parent'    => 'type',
                            'hide_when' => 'slider_tabs'
                        ],
                    ]
                ],
                'styling' => [
                    [
                        'name'    => 'css_custom',
                        'type'    => 'css',
                        'options' => [
                            [
                                "screens" => "any,1024,999,767,479",
                                'Tab'     => [
                                    [
                                        'property' => 'font-family,font-size,line-height,font-weight,text-transform,text-align',
                                        'label'    => 'Font family',
                                        'selector' => '.kc_tabs_nav, .kc_tabs_nav > li a,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li>a'
                                    ],
                                    [
                                        'property' => 'font-size,color,padding',
                                        'label'    => 'Icon Size,Icon Color,Icon Spacing',
                                        'selector' => '.kc_tabs_nav a i,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li>a i'
                                    ],
                                    [
                                        'property' => 'color',
                                        'label'    => 'Text Color',
                                        'selector' => '.kc_tabs_nav a, .kc_tabs_nav,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li>a'
                                    ],
                                    [
                                        'property' => 'background-color',
                                        'label'    => 'Background Color',
                                        'selector' => '.kc_tabs_nav,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav'
                                    ],
                                    [
                                        'property' => 'background-color',
                                        'label'    => 'Background Color tab item',
                                        'selector' => '.kc_tabs_nav li,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav li'
                                    ],
                                    [
                                        'property' => 'border',
                                        'label'    => 'Border',
                                        'selector' => '.kc_tabs_nav > li, .kc_tab.ui-tabs-body-active, .kc_tabs_nav,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav ~ div.kc_tab.ui-tabs-body-active,+.kc_vertical_tabs.tabs_right>.kc_wrapper>ul.ui-tabs-nav ~ div.kc_tab'
                                    ],
                                    [
                                        'property' => 'border-radius',
                                        'label'    => 'Border-radius',
                                        'selector' => '.kc_tabs_nav > li, .kc_tab.ui-tabs-body-active, .kc_tabs_nav,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav ~ div.kc_tab.ui-tabs-body-active,+.kc_vertical_tabs.tabs_right>.kc_wrapper>ul.ui-tabs-nav ~ div.kc_tab'
                                    ],
                                    [
                                        'property' => 'padding',
                                        'label'    => 'Padding',
                                        'selector' => '.kc_tabs_nav > li > a,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li>a'
                                    ],
                                    [
                                        'property' => 'margin',
                                        'label'    => 'Margin',
                                        'selector' => '.kc_tabs_nav > li > a,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li'
                                    ],
                                    [
                                        'property' => 'width',
                                        'label'    => 'Width',
                                        'selector' => '.kc_tabs_nav > li,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li'
                                    ],
                                ],
                                
                                'Tab Hover'  => [
                                    [
                                        'property' => 'color',
                                        'label'    => 'Text Color',
                                        'selector' => '.kc_tabs_nav li:hover a, .kc_tabs_nav li:hover, .kc_tabs_nav > .ui-tabs-active:hover a,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li>a:hover,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li.ui-tabs-active > a'
                                    ],
                                    [
                                        'property' => 'color',
                                        'label'    => 'Icon Color',
                                        'selector' => '.kc_tabs_nav li:hover a i,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li>a:hover i,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li.ui-tabs-active > a i'
                                    ],
                                    [
                                        'property' => 'background-color',
                                        'label'    => 'Background Color',
                                        'selector' => '.kc_tabs_nav > li:hover, .kc_tabs_nav > li:hover a, .kc_tabs_nav > li > a:hover,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li>a:hover,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li.ui-tabs-active > a'
                                    ],
                                ],
                                'Tab Active' => [
                                    [
                                        'property' => 'color',
                                        'label'    => 'Text Color',
                                        'selector' => '.kc_tabs_nav li.ui-tabs-active a,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li.ui-tabs-active > a'
                                    ],
                                    [
                                        'property' => 'color',
                                        'label'    => 'Icon Color',
                                        'selector' => '.kc_tabs_nav li.ui-tabs-active a i, .kc_tabs_nav > .ui-tabs-active:focus a i,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li.ui-tabs-active > a i'
                                    ],
                                    [
                                        'property' => 'background-color',
                                        'label'    => 'Background Color',
                                        'selector' => '.kc_tabs_nav > .ui-tabs-active:focus, .kc_tabs_nav > .ui-tabs-active, .kc_tabs_nav > .ui-tabs-active > a,+.kc_vertical_tabs>.kc_wrapper>ul.ui-tabs-nav>li.ui-tabs-active > a'
                                    ],
                                ],
                                'Tab Body'   => [
                                    [
                                        'property' => 'background-color',
                                        'label'    => 'Background Color',
                                        'selector' => '.kc_tab'
                                    ],
                                    [
                                        'property' => 'padding',
                                        'label'    => 'Spacing',
                                        'selector' => '.kc_tab .kc_tab_content'
                                    ],
                                    ['property' => 'display', 'label' => 'Display'],
                                ],
                            
                            ]
                        ]
                    ]
                ],
                'animate' => [
                    [
                        'name' => 'animate',
                        'type' => 'animate'
                    ]
                ],
            ],
            'content'      => '[kc_tab title="New Tab"][/kc_tab]'
        ],
        'wilcity_kc_heading'              => [
            'name'     => 'Heading',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'type'  => 'text',
                        'name'  => 'blur_mark',
                        'label' => 'Blur Mark',
                        'value' => ''
                    ],
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
        'wilcity_kc_testimonials'         => [
            'name'     => 'Testimonials',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'  => 'icon',
                        'label' => 'Icon',
                        'type'  => 'icon_picker',
                        'value' => 'la la-quote-right'
                    ],
                    [
                        'name'        => 'autoplay',
                        'label'       => 'Auto Play',
                        'description' => 'Leave empty to disable this feature. Or specify auto-play each x seconds',
                        'type'        => 'text',
                        'value'       => ''
                    ],
                    [
                        'name'   => 'testimonials',
                        'label'  => 'Testimonials',
                        'type'   => 'group',
                        'value'  => '',
                        'params' => [
                            [
                                'type'  => 'text',
                                'label' => 'Customer Name',
                                'name'  => 'name'
                            ],
                            [
                                'type'  => 'textarea',
                                'label' => 'Testimonial',
                                'name'  => 'testimonial'
                            ],
                            [
                                'type'  => 'text',
                                'label' => 'Customer Profesional',
                                'name'  => 'profesional'
                            ],
                            [
                                'type'  => 'attach_image_url',
                                'label' => 'Avatar',
                                'name'  => 'avatar'
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'wilcity_kc_wiloke_wave'          => [
            'name'     => 'Wiloke Wave',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Heading',
                        'type'        => 'text',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'description',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'type'  => 'color_picker',
                        'name'  => 'left_gradient_color',
                        'label' => 'Left Gradient Color',
                        'value' => '#f06292',
                    ],
                    [
                        'type'  => 'color_picker',
                        'name'  => 'right_gradient_color',
                        'label' => 'Right Gradient Color',
                        'value' => '#f97f5f'
                    ],
                    [
                        'name'   => 'btn_group',
                        'label'  => 'Buttons Group',
                        'type'   => 'group',
                        'value'  => '',
                        'params' => [
                            [
                                'type'  => 'icon_picker',
                                'label' => 'Icon',
                                'name'  => 'icon'
                            ],
                            [
                                'type'  => 'text',
                                'label' => 'Button name',
                                'name'  => 'name'
                            ],
                            [
                                'type'  => 'text',
                                'label' => 'Button URL',
                                'name'  => 'url'
                            ],
                            [
                                'type'    => 'select',
                                'label'   => 'Open Type',
                                'name'    => 'open_type',
                                'options' => [
                                    '_self'  => 'In the same window',
                                    '_blank' => 'In a New Window'
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        ],
        'wilcity_kc_box_icon'             => [
            'name'     => 'Box Icon',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'        => 'icon',
                        'label'       => 'Icon',
                        'type'        => 'icon_picker',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'heading',
                        'label'       => 'Heading',
                        'type'        => 'text',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'  => 'description',
                        'label' => 'Description',
                        'type'  => 'textarea',
                        'value' => ''
                    ],
                ]
            ]
        ],
        'wilcity_kc_events_grid'          => [
            'name'     => 'Events Grid Layout',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general'         => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Heading',
                        'type'        => 'text',
                        'value'       => 'The Latest Events',
                        'admin_label' => true
                    ],
                    [
                        'type'  => 'color_picker',
                        'name'  => 'heading_color',
                        'label' => 'Heading Color',
                        'value' => ''
                    ],
                    [
                        'name'        => 'desc',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => true
                    ],
                    [
                        'type'  => 'color_picker',
                        'name'  => 'desc_color',
                        'label' => 'Description Color',
                        'value' => ''
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'admin_label' => true
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Toggle View More',
                        'name'    => 'toggle_viewmore',
                        'options' => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ],
                        'std'     => 'enable'
                    ],
                    [
                        'type'     => 'text',
                        'label'    => 'Button Name',
                        'name'     => 'viewmore_btn_name',
                        'relation' => [
                            'parent'    => 'toggle_viewmore',
                            'show_when' => [
                                'enable'
                            ]
                        ],
                        'std'      => 'View more'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Tags',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_tags'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Categories',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_cats'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Locations',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_locations'
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order By',
                        'name'    => 'orderby',
                        'options' => [
                            'post_date'                 => 'Event Date',
                            'post_title'                => 'Event Title',
                            'menu_order'                => 'Premium Listings',
                            'upcoming_event'            => 'Upcoming Events',
                            'happening_event'           => 'Happening Events',
                            'starts_from_ongoing_event' => 'Upcoming + Happening'
                        ]
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order',
                        'name'    => 'order',
                        'options' => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC'
                        ],
                        'value'   => 'ASC'
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
                        'type'  => 'text',
                        'label' => 'Mobile Image Size',
                        'name'  => 'mobile_img_size',
                        'value' => ''
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Toggle Gradient',
                        'name'    => 'toggle_gradient',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'value'   => 'enable'
                    ],
                    [
                        'type'     => 'color_picker',
                        'label'    => 'Left Gradient',
                        'name'     => 'left_gradient',
                        'value'    => '#006bf7',
                        'relation' => [
                            'parent'    => 'toggle_gradient',
                            'show_when' => 'enable'
                        ]
                    ],
                    [
                        'type'     => 'color_picker',
                        'label'    => 'Right Gradient',
                        'name'     => 'right_gradient',
                        'value'    => '#ed6392',
                        'relation' => [
                            'parent'    => 'toggle_gradient',
                            'show_when' => 'enable'
                        ]
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Opacity',
                        'parent'      => [],
                        'description' => 'The value must equal to or smaller than 1',
                        'name'        => 'gradient_opacity',
                        'value'       => '0.3',
                        'relation'    => [
                            'parent'    => 'toggle_gradient',
                            'show_when' => 'enable'
                        ]
                    ]
                ],
                'device settings' => [
                    [
                        'name'        => 'maximum_posts_on_lg_screen',
                        'label'       => 'Items / row on >=1200px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                        'type'        => 'select',
                        'value'       => 'col-lg-4',
                        'options'     => [
                            'col-lg-2'  => '6 Items / row',
                            'col-lg-3'  => '4 Items / row',
                            'col-lg-4'  => '3 Items / row',
                            'col-lg-6'  => '2 Items / row',
                            'col-lg-12' => '1 Item / row'
                        ],
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_md_screen',
                        'label'       => 'Items / row on >=960px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                        'type'        => 'select',
                        'options'     => [
                            'col-md-2'  => '6 Items / row',
                            'col-md-3'  => '4 Items / row',
                            'col-md-4'  => '3 Items / row',
                            'col-md-6'  => '2 Items / row',
                            'col-md-12' => '1 Item / row'
                        ],
                        'value'       => 'col-md-3',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_sm_screen',
                        'label'       => 'Items / row on >=720px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                        'type'        => 'select',
                        'options'     => [
                            'col-sm-2'  => '6 Items / row',
                            'col-sm-3'  => '4 Items / row',
                            'col-sm-4'  => '3 Items / row',
                            'col-sm-6'  => '2 Items / row',
                            'col-sm-12' => '1 Item / row'
                        ],
                        'value'       => 'col-sm-12',
                        'admin_label' => true
                    ]
                ],
                'styling'         => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_events_slider'        => [
            'name'     => 'Events Slider',
            'icon'     => 'sl-paper-plane',
            'category' => WILCITY_SC_CATEGORY,
            'css_box'  => true,
            'params'   => [
                'general'               => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Heading',
                        'type'        => 'text',
                        'value'       => 'The Latest Events',
                        'admin_label' => true
                    ],
                    [
                        'type'  => 'color_picker',
                        'name'  => 'heading_color',
                        'label' => 'Heading Color',
                        'value' => ''
                    ],
                    [
                        'name'        => 'desc',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => true
                    ],
                    [
                        'type'  => 'color_picker',
                        'name'  => 'desc_color',
                        'label' => 'Description Color',
                        'value' => ''
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'admin_label' => true
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Toggle View More',
                        'name'    => 'toggle_viewmore',
                        'options' => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ],
                        'std'     => 'enable'
                    ],
                    [
                        'type'     => 'text',
                        'label'    => 'Button Name',
                        'name'     => 'viewmore_btn_name',
                        'std'      => 'View more',
                        'relation' => [
                            'parent'    => 'toggle_viewmore',
                            'show_when' => [
                                'enable'
                            ]
                        ]
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Tags',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_tags'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Categories',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_cats'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Locations',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_locations'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Taxonomy Key',
                        'description' => 'This feature is useful if you want to use show up your custom taxonomy',
                        'name'        => 'custom_taxonomy_key'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Select Your Custom Taxonomies',
                        'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
                        'name'        => 'custom_taxonomies_id'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Taxonomy Key',
                        'description' => 'This feature is useful if you want to use show up your custom taxonomy',
                        'name'        => 'custom_taxonomy_key'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Select Your Custom Taxonomies',
                        'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
                        'name'        => 'custom_taxonomies_id'
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order By',
                        'name'    => 'orderby',
                        'options' => [
                            'post_date'                 => 'Listing Date',
                            'post_title'                => 'Listing Title',
                            'menu_order'                => 'Premium Events',
                            'upcoming_event'            => 'Upcoming Events',
                            'happening_event'           => 'Happening Events',
                            'starts_from_ongoing_event' => 'Upcoming + Happening'
                        ]
                    ]
                ],
                'listings on screens'   => [
                    [
                        'name'        => 'desktop_image_size',
                        'label'       => 'Desktop Image Size',
                        'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                        'type'        => 'text',
                        'value'       => ''
                    ],
                    [
                        'name'        => 'maximum_posts',
                        'label'       => 'Maximum Listings',
                        'type'        => 'text',
                        'value'       => 8,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_extra_lg_screen',
                        'label'       => 'Items on >=1600px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1600px ',
                        'type'        => 'text',
                        'value'       => 6,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_lg_screen',
                        'label'       => 'Items on >=1400px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                        'type'        => 'text',
                        'value'       => 5,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_md_screen',
                        'label'       => 'Items on >=1200px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                        'type'        => 'text',
                        'value'       => 5,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_sm_screen',
                        'label'       => 'Items on >=992px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 992px ',
                        'type'        => 'text',
                        'value'       => 2,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_extra_sm_screen',
                        'label'       => 'Items on >=640px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                        'type'        => 'text',
                        'value'       => 1,
                        'admin_label' => true
                    ]
                ],
                'slider configurations' => [
                    [
                        'name'        => 'is_auto_play',
                        'label'       => 'Is Auto Play',
                        'type'        => 'select',
                        'options'     => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'value'       => 'disable',
                        'admin_label' => true
                    ]
                ],
                'styling'               => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_grid'                 => [
            'name'     => 'Listings Grid Layout',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general'          => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Heading',
                        'type'        => 'text',
                        'value'       => 'The Latest Listings',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'heading_color',
                        'label'       => 'Heading Color',
                        'type'        => 'color_picker',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'desc',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'desc_color',
                        'label'       => 'Description Color',
                        'type'        => 'color_picker',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'admin_label' => true
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Style',
                        'name'    => 'style',
                        'options' => [
                            'grid'  => 'Grid 1 (Default)',
                            'grid2' => 'Grid 2',
                            'list'  => 'List'
                        ],
                        'std'     => 'style1'
                    ],
                    [
                        'type'        => 'select',
                        'label'       => 'Toggle Grid Border?',
                        'description' => 'Adding a order around grid listing',
                        'name'        => 'border',
                        'options'     => [
                            'border-gray-1' => 'Enable',
                            'border-gray-0' => 'Disable'
                        ],
                        'std'         => 'border-gray-0'
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Toggle View More',
                        'name'    => 'toggle_viewmore',
                        'options' => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ],
                        'std'     => 'disable'
                    ],
                    [
                        'type'     => 'text',
                        'label'    => 'Button Name',
                        'name'     => 'viewmore_btn_name',
                        'relation' => [
                            'parent'    => 'toggle_viewmore',
                            'show_when' => [
                                'enable'
                            ]
                        ],
                        'std'      => 'View more'
                    ],
                    [
                        'name'        => 'post_type',
                        'label'       => 'Post Type',
                        'description' => 'We recommend using Using Belongs To Setting if this is <a href="https://documentation.wilcity.com/knowledgebase/customizing-listing-location-listing-category-page/" target="_blank">Customizing Taxonomy Page</a>',
                        'type'        => 'select',
                        'value'       => 'listing',
                        'admin_label' => true,
                        'options'     => SCHelpers::getPostTypeOptions()
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Categories',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_cats'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Locations',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_locations'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Tags',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_tags'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Taxonomy Key',
                        'description' => 'This feature is useful if you want to use show up your custom taxonomy',
                        'name'        => 'custom_taxonomy_key'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Your Custom Taxonomies IDs',
                        'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
                        'name'        => 'custom_taxonomies_id'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Specify Listings',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_ids'
                    ],
                    [
                        'type'  => 'text',
                        'label' => 'Maximum Items',
                        'name'  => 'posts_per_page',
                        'value' => 6
                    ],
                    [
                        'type'        => 'select',
                        'label'       => 'Order By',
                        'description' => 'In order to use Order by Random, please disable the cache plugin or exclude this page from cache.',
                        'name'        => 'orderby',
                        'options'     => [
                            'post_date'        => 'Listing Date',
                            'post_title'       => 'Listing Title',
                            'menu_order'       => 'Listing Order',
                            'best_viewed'      => 'Popular Viewed',
                            'best_rated'       => 'Popular Rated',
                            'best_shared'      => 'Popular Shared',
                            'post__in'         => 'Like Specify Listing IDs field',
                            'rand'             => 'Random',
                            'nearbyme'         => 'Near By Me',
                            'open_now'         => 'Open now',
                            'premium_listings' => 'Premium Listings'
                        ]
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order',
                        'name'    => 'order',
                        'options' => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC',
                        ]
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Radius',
                        'description' => 'Fetching all listings within x radius',
                        'name'        => 'radius',
                        'value'       => 10,
                        'relation'    => [
                            'parent'    => 'orderby',
                            'show_when' => ['orderby', '=', 'nearbyme']
                        ]
                    ],
                    [
                        'type'     => 'select',
                        'label'    => 'Unit',
                        'name'     => 'unit',
                        'relation' => [
                            'parent'    => 'orderby',
                            'show_when' => ['orderby', '=', 'nearbyme']
                        ],
                        'options'  => [
                            'km' => 'KM',
                            'm'  => 'Miles'
                        ],
                        'value'    => 'km'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Tab Name',
                        'description' => 'If the grid layout is inside of a tab, we recommend putting the Tab ID to this field. If the tab is emptied, the listings will be shown after the browser is loaded. Otherwise, it will be shown after someone clicks on the Tab Name.',
                        'name'        => 'tabname',
                        'value'       => uniqid('nearbyme_'),
                        'relation'    => [
                            'parent'    => 'orderby',
                            'show_when' => ['orderby', '=', 'nearbyme']
                        ]
                    ]
                ],
                'devices settings' => [
                    [
                        'type'        => 'text',
                        'label'       => 'Desktop Image Size',
                        'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                        'name'        => 'img_size',
                        'value'       => 'wilcity_360x200'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Mobile Image Size',
                        'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                        'name'        => 'mobile_img_size',
                        'value'       => 'wilcity_360x200'
                    ],
                    [
                        'name'        => 'maximum_posts_on_lg_screen',
                        'label'       => 'Items / row on >=1200px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                        'type'        => 'select',
                        'value'       => 'col-lg-4',
                        'options'     => [
                            'col-lg-2'  => '6 Items / row',
                            'col-lg-3'  => '4 Items / row',
                            'col-lg-4'  => '3 Items / row',
                            'col-lg-6'  => '2 Items / row',
                            'col-lg-12' => '1 Item / row'
                        ]
                    ],
                    [
                        'name'        => 'maximum_posts_on_md_screen',
                        'label'       => 'Items / row on >=960px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                        'type'        => 'select',
                        'options'     => [
                            'col-md-2'  => '6 Items / row',
                            'col-md-3'  => '4 Items / row',
                            'col-md-4'  => '3 Items / row',
                            'col-md-6'  => '2 Items / row',
                            'col-md-12' => '1 Item / row'
                        ],
                        'value'       => 'col-md-3'
                    ],
                    [
                        'name'        => 'maximum_posts_on_sm_screen',
                        'label'       => 'Items / row on >=720px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                        'type'        => 'select',
                        'options'     => [
                            'col-sm-2'  => '6 Items / row',
                            'col-sm-3'  => '4 Items / row',
                            'col-sm-4'  => '3 Items / row',
                            'col-sm-6'  => '2 Items / row',
                            'col-sm-12' => '1 Item / row'
                        ],
                        'value'       => 'col-sm-12'
                    ],
                ],
                'styling'          => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_restaurant_listings'  => [
            'name'     => 'Wilcity Restaurant List',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general'        => [
                    [
                        'name'        => 'heading_style',
                        'label'       => 'Heading style',
                        'type'        => 'select',
                        'value'       => 'ribbon',
                        'options'     => [
                            'ribbon'  => 'ribbon',
                            'default' => 'default'
                        ],
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'ribbon',
                        'label'       => 'Ribbon',
                        'type'        => 'text',
                        'value'       => 'Menu',
                        'relation'    => [
                            'parent'    => 'heading_style',
                            'show_when' => ['ribbon']
                        ],
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'ribbon_color',
                        'label'       => 'Ribbon Color',
                        'type'        => 'text',
                        'value'       => '#fff',
                        'relation'    => [
                            'parent'    => 'heading_style',
                            'show_when' => ['ribbon']
                        ],
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'heading',
                        'label'       => 'Heading',
                        'type'        => 'text',
                        'value'       => 'Our Special Menu',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'heading_color',
                        'label'       => 'Heading Color',
                        'type'        => 'color_picker',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'desc',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'value'       => 'Explore Delicious Flavour',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'desc_color',
                        'label'       => 'Description Color',
                        'type'        => 'color_picker',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'relation'    => [
                            'parent'    => 'heading_style',
                            'show_when' => ['default']
                        ],
                        'admin_label' => true
                    ],
                    [
                        'name'  => 'excerpt_length',
                        'label' => 'Excerpt Length',
                        'type'  => 'text',
                        'value' => 100
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Toggle View More',
                        'name'    => 'toggle_viewmore',
                        'options' => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ]
                    ],
                    [
                        'type'     => 'text',
                        'label'    => 'Button Name',
                        'name'     => 'viewmore_btn_name',
                        'relation' => [
                            'parent'    => 'toggle_viewmore',
                            'show_when' => [
                                'enable'
                            ]
                        ],
                        'std'      => 'View more'
                    ],
                    [
                        'type'     => 'icon_picker',
                        'label'    => 'View More Icon',
                        'name'     => 'viewmore_icon',
                        'relation' => [
                            'parent'    => 'toggle_viewmore',
                            'show_when' => [
                                'enable'
                            ]
                        ]
                    ]
                ],
                'query settings' => [
                    [
                        'name'        => 'post_type',
                        'label'       => 'Post Type',
                        'description' => 'We recommend using Using Belongs To Setting if this is <a href="https://documentation.wilcity.com/knowledgebase/customizing-listing-location-listing-category-page/" target="_blank">Customizing Taxonomy Page</a>',
                        'type'        => 'select',
                        'value'       => 'listing',
                        'admin_label' => true,
                        'options'     => SCHelpers::getPostTypeOptions()
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Categories',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_cats'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Locations',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_locations'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Tags',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_tags'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Taxonomy Key',
                        'description' => 'This feature is useful if you want to use show up your custom taxonomy',
                        'name'        => 'custom_taxonomy_key'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Your Custom Taxonomies IDs',
                        'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
                        'name'        => 'custom_taxonomies_id'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Specify Listings',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_ids'
                    ],
                    [
                        'type'  => 'text',
                        'label' => 'Maximum Items',
                        'name'  => 'posts_per_page',
                        'value' => 6
                    ],
                    [
                        'type'        => 'select',
                        'label'       => 'Order By',
                        'description' => 'In order to use Order by Random, please disable the cache plugin or exclude this page from cache.',
                        'name'        => 'orderby',
                        'options'     => [
                            'post_date'        => 'Listing Date',
                            'post_title'       => 'Listing Title',
                            'menu_order'       => 'Listing Order',
                            'best_viewed'      => 'Popular Viewed',
                            'best_rated'       => 'Popular Rated',
                            'best_shared'      => 'Popular Shared',
                            'post__in'         => 'Like Specify Listing IDs field',
                            'rand'             => 'Random',
                            'nearbyme'         => 'Near By Me',
                            'open_now'         => 'Open now',
                            'premium_listings' => 'Premium Listings'
                        ]
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order',
                        'name'    => 'order',
                        'options' => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC',
                        ]
                    ]
                ]
            ]
        ],
        'wilcity_kc_hero'                 => [
            'name'         => esc_html__('Hero', 'wilcity-shortcodes'),
            'nested'       => true,
            'icon'         => 'sl-paper-plane',
            'accept_child' => 'wilcity_kc_search_form',
            'css_box'      => true,
            'category'     => WILCITY_SC_CATEGORY,
            'params'       => [
                'general'              => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Title',
                        'type'        => 'text',
                        'value'       => 'Explore This City',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'heading_color',
                        'label' => 'Title Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'heading_font_size',
                        'label'       => 'Title Font Size',
                        'description' => 'Eg: 50px',
                        'type'        => 'text',
                        'value'       => '50px'
                    ],
                    [
                        'name'        => 'description',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'description_color',
                        'label' => 'Description Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'description_font_size',
                        'label'       => 'Description Font Size',
                        'description' => 'Eg: 17px',
                        'type'        => 'text',
                        'value'       => '17px'
                    ],
                    [
                        'name'        => 'toggle_button',
                        'label'       => 'Toggle Button',
                        'type'        => 'select',
                        'admin_label' => false,
                        'value'       => 'enable',
                        'options'     => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'name'        => 'button_icon',
                        'label'       => 'Button Icon',
                        'value'       => 'la la-pencil-square',
                        'type'        => 'icon_picker',
                        'admin_label' => false,
                        'relation'    => [
                            'toggle_button' => 'enable'
                        ]
                    ],
                    [
                        'name'        => 'button_name',
                        'label'       => 'Button Name',
                        'value'       => 'Check out',
                        'type'        => 'text',
                        'admin_label' => false,
                        'relation'    => [
                            'toggle_button' => 'enable'
                        ]
                    ],
                    [
                        'name'        => 'button_link',
                        'label'       => 'Button Link',
                        'type'        => 'text',
                        'value'       => '#',
                        'admin_label' => false,
                        'relation'    => [
                            'toggle_button' => 'enable'
                        ]
                    ],
                    [
                        'name'     => 'button_text_color',
                        'label'    => 'Button Text Color',
                        'type'     => 'color_picker',
                        'value'    => '#fff',
                        'relation' => [
                            'toggle_button' => 'enable'
                        ]
                    ],
                    [
                        'name'     => 'button_background_color',
                        'label'    => 'Button Background Color',
                        'type'     => 'color_picker',
                        'value'    => '',
                        'relation' => [
                            'toggle_button' => 'enable'
                        ]
                    ],
                    [
                        'name'     => 'button_size',
                        'label'    => 'Button Size',
                        'type'     => 'select',
                        'relation' => [
                            'toggle_button' => 'enable'
                        ],
                        'value'    => 'wil-btn--sm',
                        'options'  => [
                            'wil-btn--sm' => 'Small',
                            'wil-btn--md' => 'Medium',
                            'wil-btn--lg' => 'Large'
                        ]
                    ],
                    [
                        'name'    => 'toggle_dark_and_white_background',
                        'label'   => 'Toggle Dark and White Background',
                        'type'    => 'select',
                        'default' => 'disable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'name'    => 'bg_overlay',
                        'label'   => 'Background Overlay',
                        'type'    => 'color_picker',
                        'default' => ''
                    ],
                    [
                        'name'    => 'bg_type',
                        'label'   => 'Is Using Slider Background?',
                        'type'    => 'select',
                        'default' => 'image',
                        'options' => [
                            'image'  => 'Image Background',
                            'slider' => 'Slider Background'
                        ]
                    ],
                    [
                        'name'     => 'image_bg',
                        'label'    => 'Background Image',
                        'type'     => 'attach_image_url',
                        'relation' => [
                            'parent'    => 'bg_type',
                            'show_when' => 'image'
                        ]
                    ],
                    [
                        'name'     => 'slider_bg',
                        'label'    => 'Background Slider',
                        'type'     => 'attach_images',
                        'relation' => [
                            'parent'    => 'bg_type',
                            'show_when' => 'slider'
                        ]
                    ],
                    [
                        'name'        => 'img_size',
                        'label'       => 'Image Size',
                        'type'        => 'text',
                        'value'       => 'large',
                        'description' => 'Entering full keyword to display the original size',
                        'admin_label' => false
                    ]
                ],
                'search form'          => [
                    [
                        'name'        => 'search_form_position',
                        'label'       => 'Search Form Style',
                        'type'        => 'select',
                        'admin_label' => false,
                        'value'       => 'bottom',
                        'options'     => [
                            'right'  => 'Right of Screen',
                            'bottom' => 'Bottom'
                        ]
                    ],
                    [
                        'name'        => 'search_form_background',
                        'label'       => 'Search Form Background',
                        'type'        => 'select',
                        'admin_label' => false,
                        'value'       => 'hero_formDark__3fCkB',
                        'options'     => [
                            'hero_formWhite__3fCkB' => 'White',
                            'hero_formDark__3fCkB'  => 'Black'
                        ]
                    ],
                ],
                'list of suggestions ' => [
                    [
                        'name'        => 'toggle_list_of_suggestions',
                        'label'       => 'Toggle The List Of Suggestions',
                        'description' => 'A list of suggestion locations/categories will be shown on the Hero section if this feature is enabled.',
                        'type'        => 'select',
                        'options'     => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'value'       => 'enable'
                    ],
                    [
                        'name'  => 'maximum_terms_suggestion',
                        'label' => 'Maximum Locations / Categories',
                        'type'  => 'text',
                        'value' => 6
                    ],
                    [
                        'name'    => 'taxonomy',
                        'label'   => 'Get By',
                        'type'    => 'select',
                        'options' => [
                            'listing_cat'      => 'Listing Category',
                            'listing_location' => 'Listing Location'
                        ],
                        'value'   => 'listing_cat'
                    ],
                    [
                        'name'    => 'orderby',
                        'label'   => 'Order By',
                        'type'    => 'select',
                        'options' => [
                            'count'         => 'Number of children',
                            'id'            => 'ID',
                            'slug'          => 'Slug',
                            'specify_terms' => 'Specify Locations/Categories'
                        ],
                        'value'   => 'count'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Categories',
                        'description' => 'This feature is available for Order By Specify Categories',
                        'name'        => 'listing_cats',
                        'relation'    => [
                            'parent'    => 'taxonomy',
                            'show_when' => ['taxonomy', '=', 'listing_cat']
                        ]
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Locations (Optional)',
                        'description' => 'This feature is available for Order By Specify Locations',
                        'name'        => 'listing_locations',
                        'relation'    => [
                            'parent'    => 'taxonomy',
                            'show_when' => ['taxonomy', '=', 'listing_location']
                        ]
                    ]
                ]
            ]
        ],
        'wilcity_kc_search_form'          => [
            'name'     => esc_html__('Search Form', 'wilcity-shortcodes'),
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'   => 'items',
                        'label'  => 'Search Tab',
                        'type'   => 'group',
                        'value'  => '',
                        'params' => [
                            [
                                'name'  => 'name',
                                'label' => 'Tab Name',
                                'type'  => 'text',
                                'value' => 'Listing'
                            ],
                            [
                                'name'    => 'post_type',
                                'label'   => 'Directory Type',
                                'type'    => 'select',
                                'value'   => 'listing',
                                'options' => class_exists('WilokeListingTools\Framework\Helpers\General') ?
                                    General::getPostTypeOptions(false,
                                        false) : [
                                        'listing',
                                        'event'
                                    ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'wilcity_kc_term_boxes'           => [
            'name'     => esc_html__('Term Boxes', 'wilcity-shortcodes'),
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general'   => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Title',
                        'type'        => 'text',
                        'value'       => '',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'heading_color',
                        'label' => 'Title Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'description',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'description_color',
                        'label' => 'Description Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'taxonomy',
                        'label'       => 'Taxonomy',
                        'type'        => 'select',
                        'value'       => 'listing_cat',
                        'options'     => [
                            'listing_cat'      => 'Listing Category',
                            'listing_location' => 'Listing Location',
                            'listing_tag'      => 'Listing Tag',
                            '_self'            => 'Depends on Taxonomy Page'
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
                        'name'        => 'number',
                        'label'       => 'Maximum Terms Can Be Shown',
                        'description' => 'This feature is useful if you do not want to specify what Terms should be shown on the page. If you specify Locations / Categories / Tags, Maximum Terms is equal to number of selected items.',
                        'type'        => 'text',
                        'value'       => ''
                    ],
                    [
                        'name'    => 'items_per_row',
                        'label'   => 'Items Per Row',
                        'type'    => 'select',
                        'value'   => 'col-lg-3',
                        'options' => [
                            'col-lg-2'  => '6 Items / Row',
                            'col-lg-3'  => '4 Items / Row',
                            'col-lg-4'  => '3 Items / Row',
                            'col-lg-6'  => '2 Items / Row',
                            'col-lg-12' => '1 Items / Row'
                        ]
                    ],
                    [
                        'name'    => 'is_show_parent_only',
                        'label'   => 'Show Parent Only',
                        'type'    => 'select',
                        'value'   => 'no',
                        'options' => [
                            'no'  => 'No',
                            'yes' => 'Yes'
                        ]
                    ],
                    [
                        'name'    => 'is_hide_empty',
                        'label'   => 'Hide Empty Term',
                        'type'    => 'select',
                        'value'   => 'no',
                        'options' => [
                            'no'  => 'No',
                            'yes' => 'Yes'
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
                            'none'       => 'None',
                            'include'    => 'Include'
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
                ],
                'box style' => [
                    [
                        'name'        => 'toggle_box_gradient',
                        'label'       => 'Toggle Box Gradient',
                        'description' => 'In order to use this feature, please upload a Featured Image to each Listing Location/Category: Listings -> Listing Locations / Categories -> Your Location/Category -> Featured Image.',
                        'type'        => 'select',
                        'value'       => 'disable',
                        'options'     => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ]
                ],
                'styling'   => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_modern_term_boxes'    => [
            'name'     => esc_html__('Modern Term Boxes', 'wilcity-shortcodes'),
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Title',
                        'type'        => 'text',
                        'value'       => '',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'heading_color',
                        'label' => 'Title Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'description',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'description_color',
                        'label' => 'Description Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'taxonomy',
                        'label'       => 'Taxonomy',
                        'type'        => 'select',
                        'value'       => 'listing_cat',
                        'options'     => [
                            'listing_cat'      => 'Listing Category',
                            'listing_location' => 'Listing Location',
                            'listing_tag'      => 'Listing Tag',
                            '_self'            => 'Depends on Taxonomy Page'
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
                        'name'    => 'items_per_row',
                        'label'   => 'Items Per Row',
                        'type'    => 'select',
                        'value'   => 'col-lg-6',
                        'options' => [
                            'col-lg-2'  => '6 Items / Row',
                            'col-lg-3'  => '4 Items / Row',
                            'col-lg-4'  => '3 Items / Row',
                            'col-lg-6'  => '2 Items / Row',
                            'col-lg-12' => '1 Items / Row'
                        ]
                    ],
                    [
                        'name'  => 'col_gap',
                        'label' => 'Col Gap',
                        'type'  => 'text',
                        'value' => 20
                    ],
                    [
                        'name'  => 'number',
                        'label' => 'Maximum Items',
                        'type'  => 'text',
                        'value' => 6
                    ],
                    [
                        'name'        => 'image_size',
                        'label'       => 'Image Size',
                        'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                        'type'        => 'text',
                        'value'       => 'wilcity_560x300'
                    ],
                    [
                        'name'    => 'is_show_parent_only',
                        'label'   => 'Show Parent Only',
                        'type'    => 'select',
                        'value'   => 'no',
                        'options' => [
                            'no'  => 'No',
                            'yes' => 'Yes'
                        ]
                    ],
                    [
                        'name'    => 'is_hide_empty',
                        'label'   => 'Hide Empty Term',
                        'type'    => 'select',
                        'value'   => 'no',
                        'options' => [
                            'no'  => 'No',
                            'yes' => 'Yes'
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
                            'none'       => 'None',
                            'include'    => 'Include'
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
                ],
                'styling' => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_rectangle_term_boxes' => [
            'name'     => 'Rectangle Term Boxes',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Title',
                        'type'        => 'text',
                        'value'       => '',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'heading_color',
                        'label' => 'Title Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'description',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'description_color',
                        'label' => 'Description Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'taxonomy',
                        'label'       => 'Taxonomy',
                        'type'        => 'select',
                        'value'       => 'listing_cat',
                        'options'     => [
                            'listing_cat'      => 'Listing Category',
                            'listing_location' => 'Listing Location',
                            'listing_tag'      => 'Listing Tag',
                            '_self'            => 'Depends on Taxonomy Page'
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
                        'name'    => 'items_per_row',
                        'label'   => 'Items Per Row',
                        'type'    => 'select',
                        'value'   => 'col-md-3 col-lg-3',
                        'options' => [
                            'col-md-4 col-lg-4' => '3 Items / Row',
                            'col-md-3 col-lg-3' => '4 Items / Row',
                            'col-md-6 col-lg-6' => '2 Items / Row'
                        ]
                    ],
                    [
                        'name'  => 'col_gap',
                        'label' => 'Col Gap',
                        'type'  => 'text',
                        'value' => 20
                    ],
                    [
                        'name'  => 'number',
                        'label' => 'Maximum Items',
                        'type'  => 'text',
                        'value' => 6
                    ],
                    [
                        'name'        => 'image_size',
                        'label'       => 'Image Size',
                        'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                        'type'        => 'text',
                        'value'       => 'wilcity_560x300'
                    ],
                    [
                        'name'    => 'is_show_parent_only',
                        'label'   => 'Show Parent Only',
                        'type'    => 'select',
                        'value'   => 'no',
                        'options' => [
                            'no'  => 'No',
                            'yes' => 'Yes'
                        ]
                    ],
                    [
                        'name'    => 'is_hide_empty',
                        'label'   => 'Hide Empty Term',
                        'type'    => 'select',
                        'value'   => 'no',
                        'options' => [
                            'no'  => 'No',
                            'yes' => 'Yes'
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
                            'none'       => 'None',
                            'include'    => 'Include'
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
                        'name'        => 'image_size',
                        'label'       => 'Image Size',
                        'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                        'type'        => 'text',
                        'value'       => 'medium'
                    ]
                ],
                'styling' => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_listings_tabs'        => [
            'name'     => 'Listings Tabs',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'settings' => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Heading',
                        'type'        => 'text',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'heading_color',
                        'label'       => 'Heading Color',
                        'type'        => 'color_picker',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'taxonomy',
                        'label'       => 'Get Listings in',
                        'type'        => 'select',
                        'value'       => 'listing_cat',
                        'options'     => [
                            'listing_cat'      => 'Listing Category',
                            'listing_location' => 'Listing Location'
                        ],
                        'admin_label' => true
                    ],
                    [
                        'type'        => 'select',
                        'name'        => 'get_term_type',
                        'label'       => 'Get Terms Type',
                        'description' => 'Warning: If you want to use Get Term Children mode, You can use select 1 Listing Location / Listing Category only',
                        'value'       => 'term_children',
                        'options'     => [
                            'term_children' => 'Get Term Children',
                            'specify_terms' => 'Specify Terms'
                        ]
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Listing Location[s]',
                        'description' => 'If you are using Get Term Children mode, you can enter in 1 Listing Location only',
                        'name'        => 'listing_locations',
                        'relation'    => [
                            'parent'    => 'taxonomy',
                            'show_when' => ['taxonomy', '=', 'listing_location']
                        ]
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Listing Category/Categories',
                        'description' => 'If you are using Get Term Children mode, you can enter in 1 Listing Category only',
                        'name'        => 'listing_cats',
                        'relation'    => [
                            'parent'    => 'taxonomy',
                            'show_when' => ['taxonomy', '=', 'listing_cat']
                        ]
                    ],
                    [
                        'type'     => 'text',
                        'label'    => 'Maximum Term Children',
                        'name'     => 'number_of_term_children',
                        'value'    => 6,
                        'relation' => [
                            'parent'    => 'get_term_type',
                            'show_when' => ['get_term_type', '=', 'term_children']
                        ]
                    ],
                    [
                        'type'  => 'text',
                        'name'  => 'terms_tab_id',
                        'label' => 'Wrapper ID',
                        'value' => uniqid('terms_tab_id')
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order By',
                        'name'    => 'orderby',
                        'options' => [
                            'post_date'  => 'Post Date',
                            'post_title' => 'Post Title',
                            'menu_order' => 'Premium Listings',
                            'rand'       => 'Random',
                            'nearbyme'   => 'Near By Me'
                        ]
                    ],
                    [
                        'type'  => 'text',
                        'label' => 'Get listings within X km/m',
                        'name'  => 'radius',
                        'value' => 10,
                        'relation'    => [
                            'parent'    => 'orderby',
                            'show_when' => ['orderby', '=', 'nearbyme']
                        ]
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order',
                        'name'    => 'order',
                        'options' => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC'
                        ],
                        'value'   => 'ASC'
                    ],
                    [
                        'type'  => 'text',
                        'label' => 'Maximum Items',
                        'name'  => 'posts_per_page',
                        'value' => 6
                    ],
                    [
                        'name'    => 'post_types_filter',
                        'label'   => 'Post Types Filter',
                        'type'    => 'multiple',
                        'value'   => '',
                        'options' => SCHelpers::getPostTypeKeys(false, false)
                    ],
                ],
                'general'  => [
                    [
                        'type'        => 'text',
                        'label'       => 'Image Size',
                        'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                        'name'        => 'img_size',
                        'value'       => 'wilcity_360x200'
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Toggle Viewmore',
                        'name'    => 'toggle_viewmore',
                        'value'   => 'enable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Tab Alignment',
                        'name'    => 'tab_alignment',
                        'options' => [
                            'wil-text-center' => 'wil-text-center',
                            'wil-text-right'  => 'wil-text-right'
                        ],
                        'value'   => 'wil-text-right'
                    ],
                    [
                        'name'        => 'maximum_posts_on_lg_screen',
                        'label'       => 'Items / row on >=1200px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                        'type'        => 'select',
                        'value'       => 'col-lg-4',
                        'options'     => [
                            'col-lg-2'  => '6 Items / row',
                            'col-lg-3'  => '4 Items / row',
                            'col-lg-4'  => '3 Items / row',
                            'col-lg-6'  => '2 Items / row',
                            'col-lg-12' => '1 Item / row'
                        ],
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_md_screen',
                        'label'       => 'Items / row on >=960px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                        'type'        => 'select',
                        'options'     => [
                            'col-md-2'  => '6 Items / row',
                            'col-md-3'  => '4 Items / row',
                            'col-md-4'  => '3 Items / row',
                            'col-md-6'  => '2 Items / row',
                            'col-md-12' => '1 Item / row'
                        ],
                        'value'       => 'col-md-3',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_sm_screen',
                        'label'       => 'Items / row on >=720px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                        'type'        => 'select',
                        'options'     => [
                            'col-sm-2'  => '6 Items / row',
                            'col-sm-3'  => '4 Items / row',
                            'col-sm-4'  => '3 Items / row',
                            'col-sm-6'  => '2 Items / row',
                            'col-sm-12' => '1 Item / row'
                        ],
                        'value'       => 'col-sm-12',
                        'admin_label' => true
                    ]
                ]
            ]
        ],
        'wilcity_kc_masonry_term_boxes'   => [
            'name'     => 'Masonry Term Boxes',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Title',
                        'type'        => 'text',
                        'value'       => '',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'heading_color',
                        'label' => 'Title Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'description',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => false
                    ],
                    [
                        'name'  => 'desc_color',
                        'label' => 'Description Color',
                        'type'  => 'color_picker',
                        'value' => ''
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'taxonomy',
                        'label'       => 'Taxonomy',
                        'type'        => 'select',
                        'value'       => 'listing_cat',
                        'options'     => [
                            'listing_cat'      => 'Listing Category',
                            'listing_location' => 'Listing Location',
                            'listing_tag'      => 'Listing Tag',
                            '_self'            => 'Depends on Taxonomy Page'
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
                        'name'  => 'number',
                        'label' => 'Maximum Items',
                        'type'  => 'text',
                        'value' => 6
                    ],
                    [
                        'name'        => 'image_size',
                        'label'       => 'Image Size',
                        'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                        'type'        => 'text',
                        'value'       => 'wilcity_560x300'
                    ],
                    [
                        'name'    => 'is_show_parent_only',
                        'label'   => 'Show Parent Only',
                        'type'    => 'select',
                        'value'   => 'no',
                        'options' => [
                            'no'  => 'No',
                            'yes' => 'Yes'
                        ]
                    ],
                    [
                        'name'    => 'is_hide_empty',
                        'label'   => 'Hide Empty Term',
                        'type'    => 'select',
                        'value'   => 'no',
                        'options' => [
                            'no'  => 'No',
                            'yes' => 'Yes'
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
                            'none'       => 'None',
                            'include'    => 'Include'
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
                ],
                'styling' => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_listings_slider'      => [
            'name'     => 'Listings Slider',
            'icon'     => 'sl-paper-plane',
            'category' => WILCITY_SC_CATEGORY,
            'css_box'  => true,
            'params'   => [
                'general'               => [
                    [
                        'name'        => 'heading',
                        'label'       => 'Heading',
                        'type'        => 'text',
                        'value'       => 'The Latest Listings',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'heading_color',
                        'label'       => 'Heading Color',
                        'type'        => 'color_picker',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'desc',
                        'label'       => 'Description',
                        'type'        => 'textarea',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'desc_color',
                        'label'       => 'Description Color',
                        'type'        => 'color_picker',
                        'value'       => '',
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'header_desc_text_align',
                        'label'       => 'Heading and Description Text Alignment',
                        'type'        => 'select',
                        'options'     => [
                            'wil-text-center' => 'Center',
                            'wil-text-left'   => 'Left',
                            'wil-text-right'  => 'Right'
                        ],
                        'value'       => 'wil-text-center',
                        'admin_label' => true
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Toggle View More',
                        'name'    => 'toggle_viewmore',
                        'options' => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ],
                        'std'     => 'enable'
                    ],
                    [
                        'type'     => 'text',
                        'label'    => 'Button Name',
                        'name'     => 'viewmore_btn_name',
                        'relation' => [
                            'parent'    => 'toggle_viewmore',
                            'show_when' => [
                                'enable'
                            ]
                        ],
                        'std'      => 'View more'
                    ],
                    [
                        'name'        => 'post_type',
                        'description' => 'We recommend using Using Belongs To Setting if this is <a href="https://documentation.wilcity.com/knowledgebase/customizing-listing-location-listing-category-page/" target="_blank">Customizing Taxonomy Page</a>',
                        'label'       => 'Post Type',
                        'type'        => 'select',
                        'options'     => SCHelpers::getPostTypeOptions(),
                        'value'       => 'listing',
                        'admin_label' => true
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Tags',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_tags'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Categories',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_cats'
                    ],
                    [
                        'type'        => 'autocomplete',
                        'label'       => 'Select Locations',
                        'description' => 'Leave empty if you are working on Taxonomy Template',
                        'name'        => 'listing_locations'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Taxonomy Key',
                        'description' => 'This feature is useful if you want to use show up your custom taxonomy',
                        'name'        => 'custom_taxonomy_key'
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Select Your Custom Taxonomies',
                        'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
                        'name'        => 'custom_taxonomies_id'
                    ],
                    [
                        'type'  => 'autocomplete',
                        'label' => 'Specify Listings',
                        'name'  => 'listing_ids'
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order By',
                        'name'    => 'orderby',
                        'options' => [
                            'post_date'        => 'Listing Date',
                            'post_title'       => 'Listing Title',
                            'menu_order'       => 'Listing Order',
                            'best_viewed'      => 'Popular Viewed',
                            'best_rated'       => 'Popular Rated',
                            'best_shared'      => 'Popular Shared',
                            'post__in'         => 'Like Specify Listing IDs field',
                            'premium_listings' => 'Premium Listings'
                        ]
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Order',
                        'name'    => 'order',
                        'options' => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC'
                        ],
                        'value'   => 'DESC'
                    ],
                    [
                        'type'    => 'select',
                        'label'   => 'Toggle Gradient',
                        'name'    => 'toggle_gradient',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'value'   => 'enable'
                    ],
                    [
                        'type'     => 'color_picker',
                        'label'    => 'Left Gradient',
                        'name'     => 'left_gradient',
                        'value'    => '#006bf7',
                        'relation' => [
                            'parent'    => 'toggle_gradient',
                            'show_when' => 'enable'
                        ]
                    ],
                    [
                        'type'     => 'color_picker',
                        'label'    => 'Right Gradient',
                        'name'     => 'right_gradient',
                        'value'    => '#ed6392',
                        'relation' => [
                            'parent'    => 'toggle_gradient',
                            'show_when' => 'enable'
                        ]
                    ],
                    [
                        'type'        => 'text',
                        'label'       => 'Opacity',
                        'parent'      => [],
                        'description' => 'The value must equal to or smaller than 1',
                        'name'        => 'gradient_opacity',
                        'value'       => '0.3',
                        'relation'    => [
                            'parent'    => 'toggle_gradient',
                            'show_when' => 'enable'
                        ]
                    ]
                ],
                'listings on screens'   => [
                    [
                        'name'        => 'desktop_image_size',
                        'label'       => 'Desktop Image Size',
                        'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                        'type'        => 'text',
                        'value'       => ''
                    ],
                    [
                        'name'  => 'mobile_image_size',
                        'label' => 'Mobile Image Size',
                        'type'  => 'text',
                        'value' => ''
                    ],
                    [
                        'name'        => 'maximum_posts',
                        'label'       => 'Maximum Listings',
                        'type'        => 'text',
                        'value'       => 8,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_extra_lg_screen',
                        'label'       => 'Items on >=1600px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1600px ',
                        'type'        => 'text',
                        'value'       => 6,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_lg_screen',
                        'label'       => 'Items on >=1400px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
                        'type'        => 'text',
                        'value'       => 5,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_md_screen',
                        'label'       => 'Items on >=1200px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                        'type'        => 'text',
                        'value'       => 5,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_sm_screen',
                        'label'       => 'Items on >=992px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 992px ',
                        'type'        => 'text',
                        'value'       => 2,
                        'admin_label' => true
                    ],
                    [
                        'name'        => 'maximum_posts_on_extra_sm_screen',
                        'label'       => 'Items on >=640px',
                        'description' => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
                        'type'        => 'text',
                        'value'       => 1,
                        'admin_label' => true
                    ]
                ],
                'slider configurations' => [
                    [
                        'name'        => 'autoplay',
                        'label'       => 'Auto Play (In ms)',
                        'type'        => 'text',
                        'value'       => 100000,
                        'admin_label' => true
                    ]
                ],
                'styling'               => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_pricing'              => [
            'name'        => esc_html__('Pricing Table', 'wilcity-shortcodes'),
            'description' => esc_html__('Display single icon', 'wilcity-shortcodes'),
            'icon'        => 'sl-paper-plane',
            'category'    => WILCITY_SC_CATEGORY,
            'params'      => [
                'general' => [
                    [
                        'name'        => 'items_per_row',
                        'label'       => esc_html__('Items / Row', 'wilcity-shortcodes'),
                        'type'        => 'select',
                        'admin_label' => true,
                        'options'     => [  // THIS FIELD REQUIRED THE PARAM OPTIONS
                            'col-md-4 col-lg-4'   => '3 Items / Row',
                            'col-md-3 col-lg-3'   => '4 Items / Row',
                            'col-md-6 col-lg-6'   => '2 Items / Row',
                            'col-md-12 col-lg-12' => '1 Item / Row'
                        ]
                    ],
                    [
                        'name'        => 'listing_type',
                        'label'       => 'Directory Type',
                        'type'        => 'select',
                        'admin_label' => true,
                        'options'     => $aPricingOptions
                    ],
                    [
                        'name'        => 'toggle_nofollow',
                        'label'       => 'Add rel="nofollow" to Plan URL',
                        'type'        => 'select',
                        'admin_label' => true,
                        'options'     => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ],
                        'default'     => 'disable'
                    ]
                ],
                'styling' => [
                    [
                        'name' => 'css_custom',
                        'type' => 'css'
                    ]
                ]
            ]
        ],
        'wilcity_kc_contact_us'           => [
            'name'     => 'Contact Us',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general'      => [
                    [
                        'name'  => 'contact_info_heading',
                        'label' => 'Heading',
                        'type'  => 'text',
                        'value' => 'Contact Info'
                    ],
                    [
                        'name'   => 'contact_info',
                        'label'  => 'Contact Info',
                        'type'   => 'group',
                        'value'  => '',
                        'params' => [
                            [
                                'type'  => 'icon_picker',
                                'label' => 'Icon',
                                'name'  => 'icon'
                            ],
                            [
                                'type'  => 'textarea',
                                'label' => 'Info',
                                'name'  => 'info'
                            ],
                            [
                                'type'        => 'text',
                                'label'       => 'link',
                                'description' => 'Enter in # if it is not a real link.',
                                'name'        => 'link'
                            ],
                            [
                                'type'    => 'select',
                                'label'   => 'Type',
                                'name'    => 'type',
                                'value'   => 'default',
                                'options' => [
                                    'default' => 'Default',
                                    'phone'   => 'Phone',
                                    'mail'    => 'mail'
                                ]
                            ],
                            [
                                'type'        => 'select',
                                'label'       => 'Open Type',
                                'description' => 'After clicking on this link, it will be opened in',
                                'name'        => 'target',
                                'value'       => '_self',
                                'options'     => [
                                    '_self'  => 'Self page',
                                    '_blank' => 'New Window'
                                ]
                            ]
                        ]
                    ]
                ],
                'Contact Form' => [
                    [
                        'name'  => 'contact_form_heading',
                        'label' => 'Heading',
                        'type'  => 'text',
                        'value' => 'Contact Us'
                    ],
                    [
                        'type'    => 'autocomplete',
                        'name'    => 'contact_form_7',
                        'label'   => 'Contact Form 7',
                        'options' => [
                            'post_type' => 'wpcf7_contact_form',
                        ]
                    ],
                    [
                        'type'        => 'text',
                        'name'        => 'contact_form_shortcode',
                        'label'       => 'Contact Form Shortcode',
                        'description' => 'If you are using another contact form plugin, please enter its own shortcode here.'
                    ]
                ]
            ]
        ],
        'wilcity_kc_intro_box'            => [
            'name'     => 'Intro Box',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'  => 'bg_img',
                        'label' => 'Background Image',
                        'type'  => 'attach_image_url',
                        'value' => ''
                    ],
                    [
                        'name'  => 'video_intro',
                        'label' => 'Video Intro',
                        'type'  => 'text',
                        'value' => ''
                    ],
                    [
                        'name'  => 'intro',
                        'label' => 'Intro',
                        'type'  => 'editor',
                        'value' => ''
                    ]
                ]
            ]
        ],
        'wilcity_kc_team_intro_slider'    => [
            'name'     => 'Team Intro Slider',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'    => 'get_by',
                        'label'   => 'Get users who are',
                        'type'    => 'select',
                        'value'   => 'administrator',
                        'options' => [
                            'administrator' => 'Administrator',
                            'editor'        => 'Editor',
                            'contributor'   => 'Contributor',
                            'custom'        => 'Custom',
                        ]
                    ],
                    [
                        'name'        => 'members',
                        'label'       => 'Members',
                        'type'        => 'group',
                        'description' => 'Eg: facebook:https://facebook.com,google-plus:https://googleplus.com',
                        'params'      => [
                            [
                                'type'  => 'attach_image_url',
                                'label' => 'Avatar',
                                'name'  => 'avatar'
                            ],
                            [
                                'type'  => 'attach_image_url',
                                'label' => 'Picture',
                                'name'  => 'picture'
                            ],
                            [
                                'type'  => 'text',
                                'label' => 'Name',
                                'name'  => 'display_name'
                            ],
                            [
                                'type'  => 'text',
                                'label' => 'Position',
                                'name'  => 'position'
                            ],
                            [
                                'type'  => 'textarea',
                                'label' => 'Intro',
                                'name'  => 'intro'
                            ],
                            [
                                'name'  => 'social_networks',
                                'label' => 'Social Networks',
                                'type'  => 'textarea'
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'wilcity_kc_author_slider'        => [
            'name'     => 'Author Slider',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'        => 'role__in',
                        'label'       => 'Role in',
                        'description' => 'Limit the returned users that have one of the specified roles',
                        'type'        => 'multiple',
                        'is_multiple' => true,
                        'value'       => 'administrator,contributor',
                        'options'     => [
                            'administrator' => 'Administrator',
                            'editor'        => 'Editor',
                            'contributor'   => 'Contributor',
                            'subscriber'    => 'Subscriber',
                            'seller'        => 'Vendor',
                            'author'        => 'Author'
                        ]
                    ],
                    [
                        'name'    => 'orderby',
                        'label'   => 'Order by',
                        'type'    => 'select',
                        'value'   => 'post_count',
                        'options' => [
                            'registered' => 'Registered',
                            'post_count' => 'Post Count',
                            'ID'         => 'ID'
                        ]
                    ],
                    [
                        'name'  => 'number',
                        'label' => 'Maximum Users',
                        'type'  => 'text',
                        'value' => 8
                    ]
                ]
            ]
        ],
        'wilcity_kc_custom_login'         => [
            'name'     => 'Custom Login',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general'       => [
                    [
                        'name'  => 'login_section_title',
                        'label' => 'Login Title',
                        'type'  => 'text',
                        'value' => 'Welcome back, please login to your account'
                    ],
                    [
                        'name'  => 'register_section_title',
                        'label' => 'Register Title',
                        'type'  => 'text',
                        'value' => 'Create an account! It\'s free and always will be.'
                    ],
                    [
                        'name'  => 'rp_section_title',
                        'label' => 'Reset Password Title',
                        'type'  => 'text',
                        'value' => 'Find Your Account'
                    ],
                    [
                        'name'    => 'social_login_type',
                        'label'   => 'Social Login',
                        'type'    => 'select',
                        'options' => [
                            'fb_default'       => 'Using Facebook Login as Default',
                            'custom_shortcode' => 'Inserting External Shortcode',
                            'off'              => 'I do not want to use this feature'
                        ],
                        'value'   => 'fb_default'
                    ],
                    [
                        'name'     => 'social_login_shortcode',
                        'label'    => 'Social Login Shortcode',
                        'type'     => 'textarea',
                        'relation' => [
                            'parent'    => 'social_login_type',
                            'show_when' => 'custom_shortcode'
                        ],
                        'value'    => ''
                    ]
                ],
                'intro_section' => [
                    [
                        'name'  => 'login_bg_img',
                        'label' => 'Background Image',
                        'type'  => 'attach_image_url',
                        'value' => ''
                    ],
                    [
                        'name'  => 'login_bg_color',
                        'label' => 'Background Color',
                        'type'  => 'color_picker',
                        'value' => 'rgba(216, 35, 112, 0.1)'
                    ],
                    [
                        'name'   => 'login_boxes',
                        'label'  => 'Intro Box',
                        'type'   => 'group',
                        'value'  => '',
                        'params' => [
                            [
                                'type'  => 'icon_picker',
                                'label' => 'Icon',
                                'name'  => 'icon'
                            ],
                            [
                                'type'  => 'textarea',
                                'label' => 'Description',
                                'name'  => 'description'
                            ],
                            [
                                'type'  => 'color_picker',
                                'label' => 'Icon Color',
                                'name'  => 'icon_color',
                                'value' => '#fff'
                            ],
                            [
                                'type'  => 'color_picker',
                                'label' => 'Text Color',
                                'name'  => 'text_color',
                                'value' => '#fff'
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'wilcity_kc_map'                  => [
            'name'     => 'Wilcity Map',
            'icon'     => 'sl-paper-plane',
            'css_box'  => true,
            'category' => WILCITY_SC_CATEGORY,
            'params'   => [
                'general' => [
                    [
                        'name'    => 'type',
                        'label'   => 'Default Listing Type',
                        'type'    => 'select',
                        'value'   => '',
                        'options' => General::getPostTypeOptions(false, false)
                    ],
                    [
                        'name'    => 'style',
                        'label'   => 'Listing Style',
                        'type'    => 'select',
                        'value'   => 'grid',
                        'options' => [
                            'grid'  => 'Grid',
                            'list'  => 'List',
                            'grid2' => 'Grid2'
                        ]
                    ],
                    [
                        'name'        => 'latlng',
                        'label'       => 'Set Map Center',
                        'type'        => 'text',
                        'description' => 'Enter in the Latitude & Longitude value. EG: 123,456. 123 is latitude, 456 is longitude',
                        'value'       => ''
                    ],
                    [
                        'name'        => 'img_size',
                        'label'       => 'Image Size',
                        'type'        => 'text',
                        'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                        'value'       => 'wilcity_360x200'
                    ],
                    [
                        'name'        => 'img_size',
                        'label'       => 'Image Size',
                        'type'        => 'text',
                        'description' => 'For example: 200x300. 200: Image width. 300: Image height',
                        'value'       => 'wilcity_360x200'
                    ],
                    [
                        'name'        => 'max_zoom',
                        'type'        => 'text',
                        'label'       => 'Maximum Zoom Value',
                        'description' => 'If you are using a cache plugin, please flush cache to this setting take effect on your site.',
                        'default'     => 21
                    ],
                    [
                        'name'    => 'min_zoom',
                        'type'    => 'text',
                        'label'   => 'Minimum Zoom Value',
                        'default' => 1
                    ],
                    [
                        'name'    => 'default_zoom',
                        'type'    => 'text',
                        'label'   => 'Default Zoom Value',
                        'default' => 2
                    ],
                    [
                        'name'    => 'orderby',
                        'label'   => 'Order By',
                        'type'    => 'select',
                        'value'   => 'menu_order',
                        'options' => [
                            'menu_order'  => 'Premium Listings',
                            'post_date'   => 'Listing Date',
                            'post_title'  => 'Listing Title',
                            'best_viewed' => 'Popular Viewed',
                            'best_rated'  => 'Popular Rated',
                            'best_shared' => 'Popular Shared',
                            'rand'        => 'Random',
                            'nearbyme'    => 'Near By Me',
                            'open_now'    => 'Open now'
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
        ]
    ],
    'aDaysOfWeek' => [
        'monday'    => esc_html__('Monday', 'wilcity-shortcodes'),
        'tuesday'   => esc_html__('Tuesday', 'wilcity-shortcodes'),
        'wednesday' => esc_html__('Wednesday', 'wilcity-shortcodes'),
        'thursday'  => esc_html__('Thursday', 'wilcity-shortcodes'),
        'friday'    => esc_html__('Friday', 'wilcity-shortcodes'),
        'saturday'  => esc_html__('Saturday', 'wilcity-shortcodes'),
        'sunday'    => esc_html__('Sunday', 'wilcity-shortcodes'),
    ]
];
