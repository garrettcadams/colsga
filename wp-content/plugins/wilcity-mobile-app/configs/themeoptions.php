<?php
return [
    'title'            => 'Mobile General Settings',
    'id'               => 'mobile_general_settings',
    'icon'             => 'dashicons dashicons-smartphone',
    'subsection'       => false,
    'customizer_width' => '500px',
    'fields'           => [
        [
            'id'          => 'mobile_app_page',
            'type'        => 'select',
            'data'        => 'posts',
            'args'        => [
                'post_type'      => 'page',
                'posts_per_page' => 100
            ],
            'title'       => 'Mobile App Home page',
            'description' => 'Building your App Home Page by following <a href="https://documentation.wilcity.com/knowledgebase/design-my-app/" target="_blank">this tutorial</a>, the assign this page to this setting.'
        ],
        [
            'id'          => 'wilcity_security_authentication_key',
            'type'        => 'password',
            'title'       => 'SECURE AUTH KEY',
            'description' => ' The SECURE AUTH KEY you provided must contain some special characters (*&!@%^#$) and 12 characters at least. You can generate a token by clicking on <a href="https://api.wordpress.org/secret-key/1.1/salt/" target="_blank">WordPress API</a>.',
            'default'     => ''
        ],
        [
            'id'          => 'wilcity_token_expired_after',
            'type'        => 'text',
            'title'       => 'Token Expiration after (in day)',
            'description' => 'After a customer logged into your site, the app will keep logged status in x days',
            'default'     => 30
        ],
        [
            'id'      => 'app_listings_orderby',
            'type'    => 'select',
            'title'   => 'Listings Order By',
            'options' => [
                'post_date'            => 'Listing Date',
                'post_title'           => 'Listing Title',
                'menu_order post_date' => 'Listing Order',
                'best_viewed'          => 'Popular Viewed',
                'best_rated'           => 'Popular Rated',
                'best_shared'          => 'Popular Shared'
            ],
            'default' => 'menu_order post_date'
        ],
        [
            'id'      => 'app_listings_order',
            'type'    => 'select',
            'title'   => 'ORDER',
            'default' => 'DESC',
            'options' => [
                'DESC' => 'DESC',
                'ASC'  => 'ASC'
            ]
        ],
        [
            'id'      => 'content_position',
            'type'    => 'select',
            'title'   => 'Content Position',
            'default' => 'below_sidebar',
            'options' => [
                'above_sidebar' => 'Above Sidebar',
                'below_sidebar' => 'Below Sidebar'
            ]
        ],
        [
            'id'     => 'app_google_admob_section_open',
            'title'  => 'Google Admob',
            'type'   => 'section',
            'indent' => true
        ],
        [
            'id'          => 'app_google_banner_unit_id',
            'type'        => 'text',
            'title'       => 'Banner Unit ID',
            'description' => '<a href="https://documentation.wilcity.com/knowledgebase/setting-up-google-admob/" target="_blank">Setting up AdMob</a>',
            'default'     => ''
        ],
        [
            'id'      => 'app_google_fullwidth_admob_type',
            'type'    => 'select',
            'title'   => 'Fullwidth Admob Type',
            'default' => 'banner',
            'options' => [
                ''             => 'I do not use this feature',
                'rewarded'     => 'Rewarded',
                'interstitial' => 'Interstitial'
            ]
        ],
        [
            'id'       => 'app_google_fullwidth_unit_id',
            'type'     => 'text',
            'title'    => 'Full-Width Unit ID',
            'required' => ['app_google_fullwidth_admob_type', '!=', ''],
            'default'  => ''
        ],
        [
            'id'      => 'app_google_banner_size',
            'type'    => 'select',
            'title'   => 'Banner Size',
            'default' => 'banner',
            'options' => [
                'banner'          => 'banner',
                'largeBanner'     => 'largeBanner',
                'mediumRectangle' => 'mediumRectangle',
                'fullBanner'      => 'fullBanner',
                'leaderboard'     => 'leaderboard'
            ]
        ],
        [
            'id'      => 'app_google_admob_homepage',
            'type'    => 'select',
            'title'   => 'Toggle Admob On Home page',
            'options' => [
                'disable' => 'Disable',
                'enable'  => 'Enable'
            ],
            'default' => 'disable'
        ],
        [
            'id'      => 'app_google_admob_taxonomy_page',
            'type'    => 'select',
            'title'   => 'Toggle on Taxonomy page',
            'options' => [
                'disable' => 'Disable',
                'enable'  => 'Enable'
            ],
            'default' => 'disable'
        ],
        [
            'id'      => 'app_google_admob_single_listing',
            'type'    => 'select',
            'title'   => 'Toggle Admob On Single page',
            'options' => [
                'disable' => 'Disable',
                'enable'  => 'Enable'
            ],
            'default' => 'disable'
        ],
        [
            'id'     => 'app_admob_section_close',
            'title'  => '',
            'type'   => 'section',
            'indent' => false
        ],
        [
            'id'     => 'app_woocommerce_section_open',
            'title'  => 'WooCommerce Settings',
            'type'   => 'section',
            'indent' => true
        ],
        [
            'id'          => 'app_woocommerce_consumer_key',
            'type'        => 'password',
            'title'       => 'Consumer Key',
            'description' => 'In order to setup this feature, please read and follow<a href="https://documentation.wilcity.com/knowledgebase/setting-up-woocommerce-on-wilcity-app/">this tutorial</a>',
            'default'     => ''
        ],
        [
            'id'      => 'app_woocommerce_consumer_secret',
            'type'    => 'password',
            'title'   => 'Consumer Secret',
            'default' => ''
        ],
        [
            'id'     => 'app_woocommerce_section_close',
            'title'  => '',
            'type'   => 'section',
            'indent' => false
        ],
    ]
];
