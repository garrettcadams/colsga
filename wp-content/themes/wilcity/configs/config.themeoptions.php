<?php
$aConfigureMailchimp = [];

if (!function_exists('wilcitylistBusinessHours')) {
    function wilcitylistBusinessHours()
    {
        if (function_exists('wilokeListingToolsRepository')) {
            $aRawBusinessHours = \WilokeListingTools\Framework\Helpers\General::generateBusinessHours();
            $aBusinessHours    = ['' => '---'];
            foreach ($aRawBusinessHours as $aData) {
                $aBusinessHours[$aData['value']] = $aData['name'];
            }
            
            return $aBusinessHours;
        } else {
            return [
                '' => 'Please activate Wiloke Listing Tools: Appearance -> Install Plugins'
            ];
        }
    }
}

return [
    'menu_name' => esc_html__('Theme Options', 'wilcity'),
    'menu_slug' => 'wiloke',
    'redux'     => [
        'args'     => [
            // TYPICAL -> Change these values as you need/desire
            'opt_name'             => 'wiloke_options',
            // This is where your data is stored in the database and also becomes your global variable name.
            'display_name'         => 'wiloke',
            // Name that appears at the top of your panel
            'display_version'      => WILOKE_THEMEVERSION,
            // Version that appears at the top of your panel
            'menu_type'            => 'submenu',
            //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
            'allow_sub_menu'       => false,
            // Show the sections below the admin menu item or not
            'menu_title'           => esc_html__('Theme Options', 'wilcity'),
            'page_title'           => esc_html__('Theme Options', 'wilcity'),
            // You will need to generate a Google API key to use this feature.
            // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
            'google_api_key'       => '',
            // Set it you want google fonts to update weekly. A google_api_key value is required.
            'google_update_weekly' => false,
            // Must be defined to add google fonts to the typography module
            'async_typography'     => true,
            // Use a asynchronous font on the front end or font string
            //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
            'admin_bar'            => true,
            // Show the panel pages on the admin bar
            'admin_bar_icon'       => 'dashicons-portfolio',
            // Choose an icon for the admin bar menu
            'admin_bar_priority'   => 50,
            // Choose an priority for the admin bar menu
            'global_variable'      => '',
            // Set a different name for your global variable other than the opt_name
            'dev_mode'             => false,
            // Show the time the page took to load, etc
            'update_notice'        => false,
            // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
            'customizer'           => false,
            // Enable basic customizer support
            //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
            //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field
            
            // OPTIONAL -> Give you extra features
            'page_priority'        => null,
            // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
            'page_parent'          => 'themes.php',
            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
            'page_permissions'     => 'manage_options',
            // Permissions needed to access the options panel.
            'menu_icon'            => '',
            // Specify a custom URL to an icon
            'last_tab'             => '',
            // Force your panel to always open to a specific tab (by id)
            'page_icon'            => 'icon-themes',
            // Icon displayed in the admin panel next to your menu_title
            'page_slug'            => '',
            // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
            'save_defaults'        => true,
            // On load save the defaults to DB before user clicks save or not
            'default_show'         => false,
            // If true, shows the default value next to each field that is not the default value.
            'default_mark'         => '',
            // What to print by the field's title if the value shown is default. Suggested: *
            'show_import_export'   => true,
            // Shows the Import/Export panel when not used as a field.
            
            // CAREFUL -> These options are for advanced use only
            'transient_time'       => 60 * MINUTE_IN_SECONDS,
            'output'               => true,
            // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
            'output_tag'           => true,
            // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
            // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
            
            // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
            'database'             => '',
            // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
            'system_info'          => false,
            // REMOVE
            
            // HINTS
            'hints'                => [
                'icon'          => 'el el-question-sign',
                'icon_position' => 'right',
                'icon_color'    => 'lightgray',
                'icon_size'     => 'normal',
                'tip_style'     => [
                    'color'   => 'light',
                    'shadow'  => true,
                    'rounded' => false,
                    'style'   => '',
                ],
                'tip_position'  => [
                    'my' => 'top left',
                    'at' => 'bottom right',
                ],
                'tip_effect'    => [
                    'show' => [
                        'effect'   => 'slide',
                        'duration' => '500',
                        'event'    => 'mouseover',
                    ],
                    'hide' => [
                        'effect'   => 'slide',
                        'duration' => '500',
                        'event'    => 'click mouseleave',
                    ],
                ],
            ]
        ],
        'sections' => apply_filters('wilcity/theme-options/configurations', [
            [
                'title'            => esc_html__('General Settings', 'wilcity'),
                'id'               => 'general_settings',
                'subsection'       => false,
                'icon'             => 'dashicons dashicons-admin-generic',
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'          => 'general_favicon',
                        'description' => 'You should upload PNG format',
                        'type'        => 'media',
                        'title'       => 'Favicon',
                        'default'     => ''
                    ],
                    [
                        'id'      => 'general_logo',
                        'type'    => 'media',
                        'title'   => esc_html__('Logo', 'wilcity'),
                        'default' => ''
                    ],
                    [
                        'id'      => 'general_retina_logo',
                        'type'    => 'media',
                        'title'   => esc_html__('Retina Logo', 'wilcity'),
                        'default' => ''
                    ],
                    [
                        'id'          => 'general_listing_logo',
                        'type'        => 'media',
                        'title'       => 'Listing Logo',
                        'description' => 'If a listing does not have a logo, this logo will be used.',
                    ],
                    [
                        'id'    => 'general_menu_color',
                        'title' => 'Menu Color',
                        'type'  => 'color_rgba',
                    ],
                    [
                        'id'      => 'general_author_menu_background',
                        'type'    => 'select',
                        'title'   => 'Author Menu Background',
                        'default' => 'transparent',
                        'options' => [
                            'transparent' => 'Transparent',
                            'dark'        => 'Dark',
                            'light'       => 'Light',
                            'custom'      => 'Custom Background Color'
                        ]
                    ],
                    [
                        'id'       => 'general_author_custom_menu_background',
                        'type'     => 'color_rgba',
                        'title'    => 'Author Custom Background Color',
                        'default'  => '',
                        'required' => ['general_author_menu_background', '=', 'custom']
                    ],
                    [
                        'id'      => 'general_listing_menu_background',
                        'type'    => 'select',
                        'title'   => 'Listing Details Menu Background',
                        'default' => 'transparent',
                        'options' => [
                            'transparent' => 'Transparent',
                            'dark'        => 'Dark',
                            'light'       => 'Light',
                            'custom'      => 'Custom'
                        ]
                    ],
                    [
                        'id'       => 'general_custom_listing_menu_background',
                        'type'     => 'color_rgba',
                        'title'    => 'Listing Custom Background Color',
                        'default'  => '',
                        'required' => ['general_listing_menu_background', '=', 'custom']
                    ],
                    [
                        'id'      => 'general_menu_background',
                        'type'    => 'select',
                        'title'   => 'Menu Background (Excluding Listing Details)',
                        'default' => 'dark',
                        'options' => [
                            'dark'        => 'Dark',
                            'light'       => 'Light',
                            'transparent' => 'Transparent',
                            'custom'      => 'Custom'
                        ]
                    ],
                    [
                        'id'       => 'general_custom_menu_background',
                        'type'     => 'color_rgba',
                        'title'    => 'Menu Custom Background Color (Excluding Listing Details)',
                        'default'  => '',
                        'required' => ['general_menu_background', '=', 'custom']
                    ],
                    [
                        'id'      => 'general_toggle_follow',
                        'type'    => 'select',
                        'title'   => 'Toggle Follow Feature',
                        'default' => 'enable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'id'          => 'general_toggle_show_full_text',
                        'type'        => 'select',
                        'title'       => 'Always Show Full Text',
                        'description' => 'For instance, we have this text "I want to show full text". If this feature is disabled, it will show "I want to ...." on the small screen.',
                        'default'     => 'disable',
                        'options'     => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'id'      => 'general_toggle_lazyload',
                        'type'    => 'select',
                        'title'   => 'Toggle Lazy Load Image',
                        'default' => 'disable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ]
                ]
            ],
            [
                'title'            => 'SEO',
                'id'               => 'seo_settings',
                'subsection'       => false,
                'icon'             => 'dashicons dashicons-search',
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'toggle_fb_ogg_tag_to_listing',
                        'type'    => 'select',
                        'title'   => 'Added \'og:image\' property to Listing Page.',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'default' => 'enable'
                    ],
                ]
            ],
            [
                'title'            => esc_html__('Front-end Dashboard', 'wilcity'),
                'id'               => 'frontend_dashboard',
                'subsection'       => false,
                'icon'             => 'dashicons dashicons-feedback',
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'     => 'dashboard_profile_section',
                        'type'   => 'section',
                        'title'  => 'Profile Section',
                        'indent' => true
                    ],
                    [
                        'id'      => 'dashboard_profile_description',
                        'type'    => 'textarea',
                        'title'   => 'Description',
                        'default' => 'We do not sell or share your details without your permission. Find out more in our <a href="#">Privacy Policy</a>.'
                    ],
                    [
                        'id'     => 'dashboard_profile_section_end',
                        'type'   => 'section',
                        'indent' => false
                    ]
                ]
            ],
            [
                'title'            => esc_html__('Register And Login', 'wilcity'),
                'id'               => 'register_login',
                'subsection'       => false,
                'icon'             => 'dashicons dashicons-feedback',
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'toggle_register',
                        'type'    => 'select',
                        'title'   => esc_html__('Toggle Register', 'wilcity'),
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'default' => 'enable'
                    ],
                    [
                        'id'       => 'toggle_custom_login_page',
                        'required' => ['toggle_register', '=', 'enable'],
                        'type'     => 'select',
                        'title'    => 'Toggle Custom Login Page',
                        'options'  => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'default'  => 'disable'
                    ],
                    [
                        'id'       => 'custom_login_page',
                        'type'     => 'select',
                        'required' => ['toggle_custom_login_page', '=', 'enable'],
                        'data'     => 'posts',
                        'args'     => [
                            'post_type'      => 'page',
                            'posts_per_page' => 100
                        ],
                        'title'    => 'Custom Login Page'
                    ],
                    [
                        'id'       => 'toggle_google_recaptcha',
                        'required' => ['toggle_register', '=', 'enable'],
                        'options'  => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'title'    => 'Toggle Google reCaptcha',
                        'default'  => 'disable',
                        'type'     => 'select'
                    ],
                    [
                        'id'       => 'using_google_recaptcha_on',
                        'required' => ['toggle_register', '=', 'enable'],
                        'options'  => [
                            'register_page' => 'Register Page Only',
                            'both'          => 'Register page and Login page'
                        ],
                        'title'    => 'Using Google reCaptcha On',
                        'default'  => 'register_page',
                        'type'     => 'select'
                    ],
                    [
                        'id'       => 'recaptcha_site_key',
                        'required' => ['toggle_google_recaptcha', '=', 'enable'],
                        'title'    => 'Google reCAPTCHA - Site Key',
                        'default'  => '',
                        'type'     => 'text'
                    ],
                    [
                        'id'       => 'recaptcha_secret_key',
                        'required' => ['toggle_google_recaptcha', '=', 'enable'],
                        'title'    => 'Google reCAPTCHA - Secret Key',
                        'default'  => '',
                        'type'     => 'text'
                    ],
                    [
                        'id'       => 'toggle_privacy_policy',
                        'required' => ['toggle_register', '=', 'enable'],
                        'options'  => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'title'    => esc_html__('Toggle agree To the Privacy Policy', 'wilcity'),
                        'default'  => 'enable',
                        'type'     => 'select'
                    ],
                    [
                        'id'       => 'privacy_policy_desc',
                        'title'    => esc_html__('Privacy Policy Description', 'wilcity'),
                        'type'     => 'textarea',
                        'required' => ['toggle_privacy_policy', '=', 'enable'],
                        'default'  => 'I agree to the <a href="#" target="_blank">Privacy Policy</a>'
                    ],
                    [
                        'id'       => 'toggle_terms_and_conditionals',
                        'required' => ['toggle_register', '=', 'enable'],
                        'options'  => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ],
                        'title'    => esc_html__('Toggle Terms and conditionals', 'wilcity'),
                        'default'  => 'enable',
                        'type'     => 'select'
                    ],
                    [
                        'id'       => 'terms_and_conditionals_desc',
                        'type'     => 'textarea',
                        'title'    => esc_html__('Terms and conditionals description', 'wilcity'),
                        'required' => ['toggle_terms_and_conditionals', '=', 'enable'],
                        'default'  => 'I agree to the <a href="#" target="_blank">Terms and Conditions</a>'
                    ],
                    [
                        'id'          => 'login_redirect_type',
                        'type'        => 'select',
                        'options'     => [
                            'specify_page' => esc_html__('Specify page', 'wilcity'),
                            'self_page'    => esc_html__('Self page', 'wilcity')
                        ],
                        'default'     => 'self_page',
                        'title'       => esc_html__('Login Redirect Type', 'wilcity'),
                        'description' => esc_html__('Leave empty to refresh the self page', 'wilcity')
                    ],
                    [
                        'id'          => 'login_redirect_to',
                        'type'        => 'select',
                        'required'    => ['login_redirect_type', '=', 'specify_page'],
                        'data'        => 'posts',
                        'args'        => [
                            'post_type'      => 'page',
                            'posts_per_page' => 100
                        ],
                        'title'       => esc_html__('Login Redirect To', 'wilcity'),
                        'description' => esc_html__('Leave empty to refresh the self page', 'wilcity')
                    ],
                    [
                        'id'       => 'toggle_confirmation',
                        'type'     => 'select',
                        'title'    => 'Confirm Users Email Address',
                        'required' => ['toggle_terms_and_conditionals', '=', 'enable'],
                        'default'  => 'disable',
                        'options'  => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ]
                    ],
                    [
                        'id'       => 'confirmation_notification',
                        'type'     => 'textarea',
                        'title'    => 'Confirmation Notification',
                        'required' => ['toggle_confirmation', '=', 'enable'],
                        'default'  => 'Wait ... It is almost done! We sent a confirmation link to your email address. Check your mailbox now, then click on the link to activate your account.'
                    ],
                    [
                        'id'          => 'confirmation_page',
                        'type'        => 'select',
                        'required'    => ['toggle_confirmation', '=', 'enable'],
                        'data'        => 'posts',
                        'args'        => [
                            'post_type'      => 'page',
                            'posts_per_page' => 100
                        ],
                        'title'       => 'Confirmation Page',
                        'description' => 'Go to page -> Create a new page and then set this page to Wilcity Confirm Account template -> Assign the page to this field. When customer clicks on the confirmation link on his/her email, it will redirect to this page.'
                    ],
                    [
                        'id'    => 'created_account_redirect_to',
                        'type'  => 'select',
                        'data'  => 'posts',
                        'args'  => [
                            'post_type'      => 'page',
                            'posts_per_page' => 100
                        ],
                        'title' => esc_html__('Redirect to this page after creating an account', 'wilcity')
                    ],
                    [
                        'id'          => 'toggle_allow_customer_delete_account',
                        'type'        => 'select',
                        'title'       => 'Toggle Allow customer can delete account',
                        'description' => 'The customers can delete their account on Fontend Dashboard',
                        'options'     => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ]
                    ],
                    [
                        'id'      => 'customer_delete_account_warning',
                        'type'    => 'textarea',
                        'title'   => 'Customer Delete Account Warning',
                        'default' => 'Before you delete the account from Wilcity, remember that all your article will be deleted permanent.'
                    ],
                    [
                        'id'          => 'welcome_message',
                        'type'        => 'textarea',
                        'title'       => 'Internal Welcome Message',
                        'description' => 'This is the welcome message to new users using the WilCity onsite messaging system',
                        'default'     => 'Thank You for joining us today! Wilcity is a WordPress theme that helps you easily build any type of directory website. To learn more about Wilcity, please watch <a href="https://www.youtube.com/channel/UCFcStj2m0N7YOkuP0bmCmfA" target="_blank" style="color: red;">Wilcity Tutorial</a>'
                    ],
                    [
                        'id'          => 'reset_password_page',
                        'type'        => 'select',
                        'data'        => 'posts',
                        'args'        => [
                            'post_type'      => 'page',
                            'posts_per_page' => 100
                        ],
                        'title'       => 'Front-end Reset Password Page',
                        'description' => 'The users will reset their password on this page instead of the default WordPress page. To create a Reset Password, please click on Pages -> Add New -> Set this page to Reset Password template.'
                    ],
                    [
                        'id'     => 'general_fb_info',
                        'title'  => 'Facebook Login',
                        'type'   => 'section',
                        'indent' => true
                    ],
                    [
                        'id'      => 'fb_toggle_login',
                        'type'    => 'select',
                        'title'   => 'Toggle Facebook Login',
                        'default' => 'disable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'id'          => 'fb_api_language',
                        'type'        => 'text',
                        'title'       => 'Language',
                        'description' => 'Please read <a href="https://developers.facebook.com/docs/internationalization/" target="_blank">Facebook Localization</a> to know more',
                        'default'     => 'en_US'
                    ],
                    [
                        'id'      => 'fb_api_id',
                        'type'    => 'text',
                        'title'   => 'Api ID',
                        'default' => ''
                    ],
                    [
                        'id'      => 'fb_app_secret',
                        'type'    => 'password',
                        'title'   => 'Api Secret',
                        'default' => ''
                    ],
                    [
                        'id'     => 'close_general_fb_info',
                        'title'  => '',
                        'type'   => 'section',
                        'indent' => false
                    ],
                    [
                        'id'     => 'mobile_term_section',
                        'title'  => 'Mobile Settings',
                        'type'   => 'section',
                        'indent' => true
                    ],
                    [
                        'id'      => 'mobile_term_label',
                        'type'    => 'text',
                        'title'   => 'Term Term Label',
                        'default' => 'Agree to our terms and conditional'
                    ],
                    [
                        'id'    => 'mobile_term_page',
                        'type'  => 'select',
                        'data'  => 'posts',
                        'args'  => [
                            'post_type'      => 'page',
                            'posts_per_page' => 100
                        ],
                        'title' => 'Term Page'
                    ],
                    [
                        'id'      => 'mobile_policy_label',
                        'type'    => 'text',
                        'title'   => 'Policy Label',
                        'default' => 'Agree to our Policy Privacy'
                    ],
                    [
                        'id'    => 'mobile_policy_page',
                        'type'  => 'select',
                        'data'  => 'posts',
                        'args'  => [
                            'post_type'      => 'page',
                            'posts_per_page' => 100
                        ],
                        'title' => 'Policy Page'
                    ],
                    [
                        'id'     => 'close_mobile_term_section',
                        'title'  => '',
                        'type'   => 'section',
                        'indent' => false
                    ]
                ]
            ],
            [
                'title'            => esc_html__('Users', 'wilcity'),
                'id'               => 'users_settings',
                'subsection'       => false,
                'icon'             => 'dashicons dashicons-admin-users',
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'cover_image',
                        'type'    => 'media',
                        'title'   => esc_html__('Default Cover Image', 'wilcity'),
                        'default' => ''
                    ],
                    [
                        'id'      => 'user_avatar',
                        'type'    => 'media',
                        'title'   => esc_html__('Default User Avatar', 'wilcity'),
                        'default' => ''
                    ],
                    [
                        'id'      => 'user_toggle_follow',
                        'type'    => 'select',
                        'title'   => esc_html__('Toggle Follow Feature', 'wilcity'),
                        'default' => 'enable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'id'      => 'user_admin_access_all_media',
                        'type'    => 'select',
                        'title'   => 'Admin will access all images',
                        'default' => 'enable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ]
                ]
            ],
            [
                'title'            => 'Customize Taxonomies',
                'id'               => 'customize_taxonomies_slug',
                'icon'             => 'dashicons dashicons-admin-links',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'listing_location_featured_image',
                        'type'    => 'media',
                        'title'   => 'Default Location Featured Image',
                        'default' => ''
                    ],
                    [
                        'id'      => 'listing_cat_featured_image',
                        'type'    => 'media',
                        'title'   => 'Default Category Featured Image',
                        'default' => ''
                    ],
                    [
                        'id'      => 'listing_tag_featured_image',
                        'type'    => 'media',
                        'title'   => 'Default Tag Featured Image',
                        'default' => ''
                    ],
                    [
                        'id'          => 'listing_location_slug',
                        'type'        => 'text',
                        'description' => 'After changing the slug, please click on Settings -> Permalinks -> Re-save Post Name',
                        'title'       => 'Listing Location Slug',
                        'default'     => 'listing-location'
                    ],
                    [
                        'id'      => 'listing_cat_slug',
                        'type'    => 'text',
                        'title'   => 'Listing Category Slug',
                        'default' => 'listing-cat'
                    ],
                    [
                        'id'      => 'listing_tag_slug',
                        'type'    => 'text',
                        'title'   => 'Listing Tag Slug',
                        'default' => 'listing-tag'
                    ],
                    [
                        'id'      => 'taxonomy_image_size',
                        'type'    => 'text',
                        'title'   => 'Custom Taxonomy Image Size',
                        'desc'    => 'Set the image size for listing on Listing Location, Listing Category and Listing Tag Page',
                        'default' => ''
                    ],
                    [
                        'id'      => 'listing_taxonomy_page_type',
                        'type'    => 'select',
                        'title'   => 'Listing Taxonomy Page Style',
                        'options' => [
                            'default' => 'Default',
                            'custom'  => 'I create it myself'
                        ],
                        'default' => 'default'
                    ],
                    [
                        'id'       => 'custom_taxonomies_section',
                        'type'     => 'section',
                        'title'    => 'Custom Taxonomy Pages',
                        'required' => ['listing_taxonomy_page_type', '=', 'custom'],
                        'indent'   => true
                    ],
                    [
                        'id'         => 'listing_location_page',
                        'type'       => 'select',
                        'data'       => 'posts',
                        'post_types' => ['page'],
                        'title'      => 'Listing Location Page',
                        'default'    => 12,
                        'args'       => [
                            'post_type'      => 'page',
                            'posts_per_page' => -1,
                            'orderby'        => 'post_date',
                            'templates'      => 'templates/taxonomy-template.php',
                            'post_status'    => 'publish'
                        ],
                    ],
                    [
                        'id'         => 'listing_cat_page',
                        'type'       => 'select',
                        'data'       => 'posts',
                        'post_types' => ['page'],
                        'title'      => 'Listing Category Page',
                        'default'    => 12,
                        'args'       => [
                            'post_type'      => 'page',
                            'posts_per_page' => -1,
                            'orderby'        => 'post_date',
                            'templates'      => 'templates/taxonomy-template.php',
                            'post_status'    => 'publish'
                        ],
                    ],
                    [
                        'id'         => 'listing_tag_page',
                        'type'       => 'select',
                        'data'       => 'posts',
                        'post_types' => ['page'],
                        'title'      => 'Listing Tag Page',
                        'default'    => 12,
                        'args'       => [
                            'post_type'      => 'page',
                            'posts_per_page' => -1,
                            'orderby'        => 'post_date',
                            'templates'      => 'templates/taxonomy-template.php',
                            'post_status'    => 'publish'
                        ],
                    ],
                    [
                        'id'     => 'close_custom_taxonomies_section',
                        'type'   => 'section',
                        'title'  => '',
                        'indent' => false
                    ],
                    [
                        'id'       => 'open_sub_taxonomies_section',
                        'type'     => 'section',
                        'title'    => 'Sub Categories Settings',
                        'required' => ['listing_taxonomy_page_type', '=', 'default'],
                        'indent'   => true
                    ],
                    [
                        'id'          => 'sub_taxonomies_columns',
                        'type'        => 'select',
                        'title'       => 'Number of Columns',
                        'description' => 'Set the image size for listing on Listing Location, Listing Category and Listing Tag Page',
                        'default'     => 'col-md-6 col-lg-6',
                        'options'     => [
                            'col-md-6 col-lg-6' => '2 Columns',
                            'col-md-4 col-lg-4' => '3 Columns',
                            'col-md-3 col-lg-3' => '4 Columns'
                        ]
                    ],
                    [
                        'id'      => 'sub_taxonomies_maximum_can_be_shown',
                        'type'    => 'text',
                        'title'   => 'Maximum Taxonomies Can Be Shown',
                        'default' => 1000
                    ],
                    [
                        'id'      => 'sub_taxonomies_orderby',
                        'type'    => 'select',
                        'title'   => 'Sub-Categories Order By',
                        'default' => 'count',
                        'options' => [
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
                        'id'      => 'sub_taxonomies_order',
                        'type'    => 'select',
                        'title'   => 'Sub-Categories Order',
                        'default' => 'DESC',
                        'options' => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC'
                        ]
                    ],
                    [
                        'id'      => 'sub_taxonomies_toggle_show_some_listings',
                        'type'    => 'select',
                        'title'   => 'Toggle Show Some Listings Belongs To This Category',
                        'default' => 'enable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'id'      => 'sub_taxonomies_listings_title',
                        'type'    => 'text',
                        'title'   => 'Listings Title',
                        'default' => 'Popular Listings'
                    ],
                    [
                        'id'       => 'sub_taxonomies_listings_columns',
                        'type'     => 'select',
                        'title'    => 'Number of Sub-Locations/Categories Columns',
                        'default'  => 'col-md-6 col-lg-6',
                        'options'  => [
                            'col-md-6 col-lg-6' => '2 Columns',
                            'col-md-4 col-lg-4' => '3 Columns',
                            'col-md-3 col-lg-3' => '4 Columns'
                        ],
                        'required' => ['sub_taxonomies_toggle_show_some_listings', '=', 'enable'],
                    ],
                    [
                        'id'       => 'sub_taxonomies_maximum_listings_can_be_shown',
                        'type'     => 'text',
                        'title'    => 'Maximum Listings Can Be Shown',
                        'default'  => 8,
                        'required' => ['sub_taxonomies_toggle_show_some_listings', '=', 'enable'],
                    ],
                    [
                        'id'       => 'sub_taxonomies_maximum_listings_orderby',
                        'type'     => 'select',
                        'title'    => 'Listings Order By',
                        'default'  => 'menu_order post_date',
                        'options'  => [
                            'post_date'            => 'Listing Date',
                            'post_title'           => 'Listing Title',
                            'menu_order post_date' => 'Listing Order',
                            'best_viewed'          => 'Popular Viewed',
                            'best_rated'           => 'Popular Rated',
                            'best_shared'          => 'Popular Shared',
                            'rand'                 => 'Random'
                        ],
                        'required' => ['sub_taxonomies_toggle_show_some_listings', '=', 'enable'],
                    ],
                    [
                        'id'       => 'sub_taxonomies_maximum_listings_order',
                        'type'     => 'select',
                        'title'    => 'ORDER',
                        'default'  => 'DESC',
                        'required' => ['sub_taxonomies_toggle_show_some_listings', '=', 'enable'],
                        'options'  => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC'
                        ]
                    ],
                    [
                        'id'     => 'close_sub_taxonomies_section',
                        'type'   => 'section',
                        'title'  => '',
                        'indent' => false
                    ]
                ]
            ],
            [
                'title'            => 'Map Settings',
                'id'               => 'google_map_settings',
                'icon'             => 'dashicons dashicons-location',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'          => 'map_type',
                        'type'        => 'select',
                        'default'     => 'google_map',
                        'title'       => 'Map Type',
                        'description' => '<a href="https://documentation.wilcity.com/knowledgebase/how-can-i-setup-mapbox-in-wilcity/" target="_blank">Setting up Mapbox</a> | <a href="https://documentation.wilcity.com/knowledgebase/how-can-i-setup-google-map-in-wilcity/" target="_blank">Setting up Google Map</a>',
                        'options'     => [
                            'google_map' => 'Google Map',
                            'mapbox'     => 'Mapbox'
                        ]
                    ],
                    [
                        'id'       => 'mapbox_api',
                        'type'     => 'text',
                        'title'    => 'Mapbox API',
                        'default'  => '',
                        'required' => ['map_type', '=', 'mapbox']
                    ],
                    [
                        'id'          => 'mapbox_iconsize',
                        'type'        => 'text',
                        'title'       => 'Mapbox Icon Size',
                        'description' => 'EG: If Image Width is 100px and Image Height is 200px, please enter 100x200',
                        'default'     => '40x40',
                        'required'    => ['map_type', '=', 'mapbox']
                    ],
                    [
                        'id'          => 'mapbox_style',
                        'type'        => 'text',
                        'title'       => 'Mapbox Style',
                        'description' => '<a target="_blank" href="https://docs.mapbox.com/studio-manual/reference/styles/">How to get my Map style?</a>',
                        'default'     => 'mapbox://styles/mapbox/streets-v9',
                        'required'    => ['map_type', '=', 'mapbox']
                    ],
                    [
                        'id'          => 'general_google_api',
                        'type'        => 'text',
                        'title'       => esc_html__('Google Map API / Mapbox Token', 'wilcity'),
                        'description' => 'Need helps? Please click <a href="https://documentation.wilcity.com/knowledgebase/how-can-i-setup-google-map-in-wilcity/" target="_blank">on me</a>',
                        'default'     => '',
                        'required'    => ['map_type', '=', 'google_map']
                    ],
                    [
                        'id'          => 'general_search_restriction',
                        'description' => 'In order to use this feature, you have to enable Google Places service on your <a target="_blank" href="https://console.developers.google.com/apis/credential">Google Map API</a>. Each country is separated by a comma. Eg: us,pr. You can find your Country Code on <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank">Wiki ISO 3166-1 alpha-2</a>',
                        'type'        => 'text',
                        'title'       => 'Autocomplete (Where to looks? field) country restrictions.',
                        'default'     => ''
                    ],
                    [
                        'id'          => 'general_locale_code',
                        'description' => 'This setting will be used to render Message Date format. You can find your Locale Code on <a href="http://www.lingoes.net/en/translator/langcode.htm" target="_blank">Locale Code</a>',
                        'type'        => 'text',
                        'title'       => 'Locale Code',
                        'default'     => 'en-US',
                        'required'    => ['map_type', '=', 'google_map']
                    ],
                    [
                        'id'          => 'general_google_language',
                        'description' => 'You can find your Country Code on <a href="https://developers.google.com/maps/faq#languagesupport" target="_blank">Language Support</a>',
                        'type'        => 'text',
                        'title'       => 'Google Language',
                        'default'     => ''
                    ],
                    [
                        'id'          => 'map_grid_size',
                        'type'        => 'text',
                        'title'       => 'Grid Size',
                        'description' => 'The grid size of a cluster in pixels.',
                        'default'     => 10,
                        'required'    => ['map_type', '=', 'google_map']
                    ],
                    [
                        'id'          => 'map_max_zoom',
                        'type'        => 'text',
                        'title'       => 'Maximum Zoom Value',
                        'description' => 'If you are using a cache plugin, please flush cache to this setting take effect on your site.',
                        'default'     => 21
                    ],
                    [
                        'id'      => 'map_minimum_zoom',
                        'type'    => 'text',
                        'title'   => 'Minimum Zoom Value',
                        'default' => 1
                    ],
                    [
                        'id'      => 'map_default_zoom',
                        'type'    => 'text',
                        'title'   => 'Default Zoom Value',
                        'default' => 2
                    ],
                    [
                        'id'          => 'map_center',
                        'type'        => 'text',
                        'title'       => esc_html__('Default Map Center', 'wilcity'),
                        'description' => esc_html__('Enter in the default latitude and longitude. For example: -33.866,151.196',
                            'wilcity')
                    ],
                    [
                        'id'       => 'map_bound_toggle',
                        'type'     => 'select',
                        'default'  => 'disable',
                        'title'    => esc_html__('Enable Map Bound Feature', 'wilcity'),
                        'subtitle' => esc_html__('This setting is useful for local business.', 'wilcity'),
                        'options'  => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'id'          => 'map_bound_start',
                        'type'        => 'text',
                        'required'    => ['map_bound_toggle', '=', 'enable'],
                        'title'       => 'Google Map Bound Start - Southwest Coordinate.',
                        'subtitle'    => esc_html__('This setting is useful for local business.', 'wilcity'),
                        'description' => 'Enter in the Latitude and Longitude of the start position. For example: 12356,7890. 12356 is Latitude. 7890 is Longitude. You can easily find the Latitude and Longitude at https://www.latlong.net/'
                    ],
                    [
                        'id'          => 'map_bound_end',
                        'required'    => ['map_bound_toggle', '=', 'enable'],
                        'type'        => 'text',
                        'title'       => 'Google Map Bound End - Northeast Coordinate​',
                        'description' => 'Enter in the Latitude and Longitude of the end position.'
                    ],
                    [
                        'id'       => 'map_theme',
                        'type'     => 'select',
                        'title'    => 'Map Style',
                        'required' => ['map_type', '=', 'google_map'],
                        'default'  => 'black',
                        'options'  => [
                            'black'        => 'black',
                            'blurWater'    => 'blurWater',
                            'ultraLight'   => 'ultraLight',
                            'shadesOfGrey' => 'shadesOfGrey',
                            'sergey'       => 'Sergey',
                            'custom'       => 'custom'
                        ]
                    ],
                    [
                        'id'          => 'map_custom_theme',
                        'type'        => 'textarea',
                        'title'       => 'Map Theme',
                        'description' => 'You can get the map theme at <a href="https://snazzymaps.com/" target="_blank">www.snazzymaps.com</a>',
                        'required'    => ['map_theme', '=', 'custom']
                    ],
                    [
                        'id'     => 'google_map_single_map_open',
                        'type'   => 'section',
                        'title'  => 'Google Map On Single Listing Page',
                        'indent' => true
                    ],
                    [
                        'id'          => 'single_map_max_zoom',
                        'type'        => 'text',
                        'title'       => 'Maximum Zoom Value',
                        'description' => 'If you are using a cache plugin, please flush cache to this setting take effect on your site.',
                        'default'     => 21
                    ],
                    [
                        'id'      => 'single_map_minimum_zoom',
                        'type'    => 'text',
                        'title'   => 'Minimum Zoom Value',
                        'default' => 1
                    ],
                    [
                        'id'      => 'single_map_default_zoom',
                        'type'    => 'text',
                        'title'   => 'Default Zoom Value',
                        'default' => 3
                    ],
                    [
                        'id'     => 'google_map_section_close',
                        'type'   => 'section',
                        'indent' => false
                    ],
                    [
                        'id'     => 'google_map_single_event_open',
                        'type'   => 'section',
                        'title'  => 'Google Map On Single Event Page',
                        'indent' => true
                    ],
                    [
                        'id'          => 'single_event_map_max_zoom',
                        'type'        => 'text',
                        'title'       => 'Maximum Zoom Value',
                        'description' => 'If you are using a cache plugin, please flush cache to this setting take effect on your site.',
                        'default'     => 21
                    ],
                    [
                        'id'      => 'single_event_map_minimum_zoom',
                        'type'    => 'text',
                        'title'   => 'Minimum Zoom Value',
                        'default' => 1
                    ],
                    [
                        'id'      => 'single_event_map_default_zoom',
                        'type'    => 'text',
                        'title'   => 'Default Zoom Value',
                        'default' => 5
                    ],
                    [
                        'id'     => 'google_map_single_event_open_close',
                        'type'   => 'section',
                        'indent' => false
                    ],
                ]
            ],
            [
                'title'            => esc_html__('Directory Type', 'wilcity'),
                'id'               => 'listing_settings',
                'icon'             => 'dashicons dashicons-palmtree',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'listing_template',
                        'type'    => 'select',
                        'title'   => 'Listing Template',
                        'default' => 'featured_image_fullwidth',
                        'options' => [
                            'featured_image_fullwidth' => 'Featured Image Full-Width',
                            'slider'                   => 'Slider'
                        ]
                    ],
                    [
                        'id'       => 'listing_slider_img_size',
                        'type'     => 'text',
                        'title'    => 'Listing Slider Image Size',
                        'required' => ['listing_template', '=', 'slider'],
                        'default'  => 'medium'
                    ],
                    [
                        'id'       => 'listing_slider_autoplay',
                        'type'     => 'text',
                        'title'    => 'Delay between transitions (in ms). Leave empty to means Auto Play feature.',
                        'required' => ['listing_template', '=', 'slider'],
                        'default'  => 5000
                    ],
                    [
                        'id'      => 'listing_overlay_color',
                        'type'    => 'color_rgba',
                        'title'   => 'Overlay Color',
                        'default' => ''
                    ],
                    [
                        'id'      => 'listing_posts_per_page',
                        'type'    => 'text',
                        'title'   => esc_html__('Posts Per Page', 'wilcity'),
                        'default' => 10
                    ],
                    [
                        'id'          => 'listing_excerpt_length',
                        'type'        => 'text',
                        'title'       => esc_html__('Excerpt Length', 'wilcity'),
                        'description' => esc_html__('If the tagline is empty, the excerpt length will be used.',
                            'wilcity'),
                        'default'     => 40
                    ],
                    [
                        'id'          => 'listing_featured_image_type',
                        'type'        => 'select',
                        'title'       => 'Featured Image Type',
                        'default'     => 'general',
                        'description' => 'Inherit Category Featured Image: If a Listing does not have Featured Image, it will use Featured Image of it\'s Category Parent Featured Image. If the Category Parent Featured Image is still empty, The Featured Image setting below will be used.',
                        'options'     => [
                            'general'  => 'Using a General Featured Image',
                            'category' => 'Inherit Category Featured Image'
                        ]
                    ],
                    [
                        'id'      => 'listing_featured_image',
                        'type'    => 'media',
                        'title'   => esc_html__('Featured Image', 'wilcity'),
                        'desc'    => esc_html__('If the featured image is emptied, this image will be used', 'wilcity'),
                        'default' => ''
                    ],
                    [
                        'id'      => 'listing_video_thumbnail',
                        'type'    => 'media',
                        'title'   => esc_html__('Video Thumbnail', 'wilcity'),
                        'desc'    => esc_html__('If the video does not come from Youtube / Vimeo, the default thumbnail will used.',
                            'wilcity'),
                        'default' => ''
                    ],
                    [
                        'id'      => 'listing_coupon_popup_img',
                        'type'    => 'media',
                        'title'   => 'General Coupon Popup Image',
                        'default' => ''
                    ],
                    [
                        'id'      => 'timeformat',
                        'type'    => 'select',
                        'title'   => esc_html__('Time Format', 'wilcity'),
                        'desc'    => esc_html__('You can override this setting in each listing', 'wilcity'),
                        'default' => 12,
                        'options' => [
                            12 => '12h Format',
                            24 => '24h Format'
                        ]
                    ],
                    [
                        'id'         => 'map_page',
                        'type'       => 'select',
                        'data'       => 'posts',
                        'post_types' => ['page'],
                        'title'      => esc_html__('Map page', 'wilcity'),
                        'default'    => 12,
                        'args'       => [
                            'post_type'      => 'page',
                            'posts_per_page' => -1,
                            'orderby'        => 'post_date',
                            'post_status'    => 'publish'
                        ],
                    ],
                    [
                        'id'         => 'search_page',
                        'type'       => 'select',
                        'data'       => 'posts',
                        'post_types' => ['page'],
                        'title'      => esc_html__('Search page', 'wilcity'),
                        'default'    => 12,
                        'args'       => [
                            'post_type'      => 'page',
                            'posts_per_page' => -1,
                            'orderby'        => 'post_date',
                            'post_status'    => 'publish'
                        ],
                    ],
                    [
                        'id'      => 'search_page_layout',
                        'type'    => 'select',
                        'title'   => 'Search page layout',
                        'default' => 'container-fullwidth',
                        'options' => [
                            'container-fullwidth' => 'Fullwidth',
                            'container-default'   => 'Default'
                        ]
                    ],
                    [
                        'id'      => 'listing_search_page_order_by',
                        'type'    => 'select',
                        'title'   => 'Listing Search Page Order By',
                        'default' => 'menu_order',
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
                        ],
                    ],
                    [
                        'id'          => 'listing_search_page_order_by_fallback',
                        'type'        => 'select',
                        'title'       => 'Listing Search Page Order By Fallback',
                        'description' => 'EG: If we show 10 listings / page, and We have 4 premium listings only. The 4 premium listings will show first and 6 listings will be got by Order By Fallback',
                        'default'     => 'post_date',
                        'required'    => ['listing_search_page_order_by', '=', 'menu_order'],
                        'options'     => [
                            'post_date'   => 'Listing Date',
                            'post_title'  => 'Listing Title',
                            'best_shared' => 'Popular Shared',
                            'rand'        => 'Random',
                            'nearbyme'    => 'Near By Me',
                            'open_now'    => 'Open now'
                        ],
                    ],
                    [
                        'id'      => 'listing_search_page_order',
                        'type'    => 'select',
                        'title'   => 'Listing Search Page Order',
                        'default' => 'DESC',
                        'options' => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC'
                        ],
                    ],
                    [
                        'id'      => 'event_search_page_order_by',
                        'type'    => 'select',
                        'title'   => 'Event Search Page Order By',
                        'default' => 'menu_order',
                        'options' => [
                            'post_date'                 => 'Event Date',
                            'post_title'                => 'Event Title',
                            'menu_order'                => 'Premium Events',
                            'upcoming_event'            => 'Upcoming Events',
                            'ongoing_event'             => 'Happening Events',
                            'starts_from_ongoing_event' => 'Ongoing Events Then Upcoming Events'
                        ],
                    ],
                    [
                        'id'          => 'event_search_page_order_by_fallback',
                        'type'        => 'select',
                        'title'       => 'Event Search Page Order By Fallback',
                        'description' => 'EG: If we show 10 events / page, and We have 4 premium listings only. The 4 premium listings will show first and 6 events will be got by Order By Fallback',
                        'default'     => 'post_date',
                        'required'    => ['event_search_page_order_by', '=', 'menu_order'],
                        'options'     => [
                            'post_date'                 => 'Listing Date',
                            'post_title'                => 'Listing Title',
                            'upcoming_event'            => 'Upcoming Events',
                            'ongoing_event'             => 'Happening Events',
                            'starts_from_ongoing_event' => 'Ongoing Events Then Upcoming Events'
                        ],
                    ],
                    [
                        'id'      => 'event_search_page_order',
                        'type'    => 'select',
                        'title'   => 'Event Search Page Order',
                        'default' => 'DESC',
                        'options' => [
                            'DESC' => 'DESC',
                            'ASC'  => 'ASC'
                        ],
                    ],
                    [
                        'id'      => 'unit_of_distance',
                        'type'    => 'select',
                        'title'   => 'Unit of distance',
                        'default' => 'km',
                        'options' => [
                            'km' => 'KM',
                            'mi' => 'Mile'
                        ],
                    ],
                    [
                        'id'          => 'default_radius',
                        'type'        => 'text',
                        'title'       => 'Default Radius',
                        'description' => 'This setting will be used when customer clicks on Near By Me button on App',
                        'default'     => 10
                    ],
                    [
                        'id'      => 'listing_toggle_contact_info_on_unclaim',
                        'type'    => 'select',
                        'title'   => 'Hiding Contact Info on Un-Claim Listing',
                        'default' => 'disable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disable' => 'Disable'
                        ]
                    ],
                    [
                        'id'      => 'listing_toggle_favorite',
                        'type'    => 'select',
                        'title'   => esc_html__('Toggle Favorite Feature', 'wilcity'),
                        'default' => 'enable',
                        'options' => [
                            'enable'  => 'Enable',
                            'disbale' => 'Disable'
                        ]
                    ],
                    [
                        'id'     => 'listing_open_bh_section',
                        'type'   => 'section',
                        'title'  => esc_html__('Business Hours', 'wilcity'),
                        'indent' => true
                    ],
                    [
                        'id'      => 'listing_default_opening_hour',
                        'type'    => 'select',
                        'title'   => esc_html__('Default Opening Hour', 'wilcity'),
                        'default' => 'select',
                        'options' => wilcitylistBusinessHours()
                    ],
                    [
                        'id'      => 'listing_default_closed_hour',
                        'type'    => 'select',
                        'title'   => esc_html__('Default Closed Hour', 'wilcity'),
                        'default' => 'select',
                        'options' => wilcitylistBusinessHours()
                    ],
                    [
                        'id'      => 'listing_default_second_opening_hour',
                        'type'    => 'select',
                        'title'   => esc_html__('Default Second Opening Hour', 'wilcity'),
                        'default' => 'select',
                        'options' => wilcitylistBusinessHours()
                    ],
                    [
                        'id'      => 'listing_default_second_closed_hour',
                        'type'    => 'select',
                        'title'   => esc_html__('Default Second Closed Hour', 'wilcity'),
                        'default' => 'select',
                        'options' => wilcitylistBusinessHours()
                    ],
                    [
                        'id'     => 'listing_close_bh_section',
                        'type'   => 'section',
                        'title'  => '',
                        'indent' => false
                    ]
                ]
            ],
            [
                'title'            => 'Add Listing General Settings',
                'id'               => 'addlisting_general_settings',
                'icon'             => 'dashicons dashicons-edit',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'addlisting_unchecked_features_type',
                        'type'    => 'select',
                        'title'   => 'Unchecked features will',
                        'default' => 'disable',
                        'options' => [
                            'disable' => 'Show on Add Listing page, but it will be Disabled',
                            'hidden'  => 'It should not shown on Add Listing page'
                        ]
                    ],
                    [
                        'id'      => 'addlisting_skip_preview_step',
                        'type'    => 'select',
                        'title'   => 'Skip Preview Step',
                        'default' => 'disable',
                        'options' => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ]
                    ],
                    [
                        'id'      => 'addlisting_upload_img_via',
                        'type'    => 'select',
                        'title'   => 'Upload Image Via',
                        'default' => 'wp',
                        'options' => [
                            'wp'      => 'WordPress Media',
                            'ajax'    => 'Ajax Upload',
                            'default' => 'Default'
                        ]
                    ]
                ]
            ],
            [
                'title'            => 'Booking Settings',
                'id'               => 'booking_settings',
                'icon'             => 'dashicons dashicons-book',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'     => 'bookingcom_section_open',
                        'type'   => 'section',
                        'title'  => 'Booking.com Settings',
                        'indent' => true
                    ],
                    [
                        'id'          => 'bookingcom_affiliate_id',
                        'type'        => 'text',
                        'description' => 'Your affiliate ID is a unique number that allows Booking.com to track commission. If you are not an affiliate yet, check <a href="https://www.booking.com/affiliate-program/v2/index.html" target="_blank">Booking.com affiliate programme</a> and get an affiliate ID. It\'s easy and fast. Start earning money, <a href="https://www.booking.com/affiliate-program/v2/index.html" target="_blank">sign up now!</a>',
                        'title'       => 'Your affiliate ID'
                    ],
                    [
                        'id'     => 'bookingcom_section_close',
                        'type'   => 'section',
                        'indent' => false
                    ]
                ]
            ],
            [
                'title'            => 'Google AdSense',
                'id'               => 'google_adsense_settings',
                'icon'             => 'dashicons dashicons-megaphone',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'          => 'google_adsense_client_id',
                        'type'        => 'text',
                        'title'       => 'Client ID',
                        'description' => 'Please read <a href="https://documentation.wilcity.com/knowledgebase/how-to-insert-google-adsense-code-to-wilcity/" target="_blank">How to Insert Google AdSense code to Wilcity?</a> to know how to embed your Google AdsSense code to this field.',
                        'default'     => ''
                    ],
                    [
                        'id'          => 'google_adsense_slot_id',
                        'type'        => 'text',
                        'title'       => 'Slot ID',
                        'description' => '',
                        'default'     => ''
                    ],
                    [
                        'id'          => 'google_adsense_directory_content_position',
                        'type'        => 'select',
                        'title'       => 'Content Position',
                        'description' => 'The Google Ads will not show if the listing/event belongs to a Plan that does not allow showing Ads.',
                        'options'     => [
                            'above'   => 'Above Listing Content',
                            'below'   => 'Below Listing Content',
                            'disable' => 'Do not show'
                        ],
                        'default'     => 'above'
                    ],
                    [
                        'id'     => 'google_adsense_directory_type',
                        'type'   => 'section',
                        'title'  => '',
                        'indent' => false
                    ]
                ]
            ],
            [
                'title'            => 'Blog',
                'id'               => 'blog_settings',
                'icon'             => 'dashicons dashicons-welcome-write-blog',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'blog_excerpt_length',
                        'type'    => 'text',
                        'title'   => 'Excerpt Length',
                        'default' => 100
                    ],
                    [
                        'id'      => 'blog_featured_image',
                        'type'    => 'media',
                        'title'   => 'Default Featured Image',
                        'default' => ''
                    ]
                ]
            ],
            [
                'title'            => 'Sidebar',
                'id'               => 'sidebar_settings',
                'icon'             => 'dashicons dashicons-editor-table',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'blog_sidebar_layout',
                        'type'    => 'select',
                        'title'   => 'Blog Sidebar Layout',
                        'options' => [
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar'
                        ],
                        'default' => 'right'
                    ],
                    [
                        'id'      => 'single_post_sidebar_layout',
                        'type'    => 'select',
                        'title'   => 'Single Post Sidebar Layout',
                        'options' => [
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar'
                        ],
                        'default' => 'right'
                    ],
                    [
                        'id'      => 'single_event_sidebar',
                        'type'    => 'select',
                        'title'   => 'Single Event Sidebar Layout',
                        'options' => [
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar'
                        ],
                        'default' => 'right'
                    ],
                    [
                        'id'      => 'single_page_sidebar_layout',
                        'type'    => 'select',
                        'title'   => 'Single Page Sidebar Layout',
                        'options' => [
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar'
                        ],
                        'default' => 'right'
                    ],
                    [
                        'id'      => 'single_listing_sidebar_layout',
                        'type'    => 'select',
                        'title'   => 'Single Listing Sidebar Layout',
                        'options' => [
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar'
                        ],
                        'default' => 'right'
                    ],
                    [
                        'id'      => 'woocommerce_sidebar',
                        'type'    => 'select',
                        'title'   => 'WooCommerce Sidebar Layout',
                        'options' => [
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar'
                        ],
                        'default' => 'no'
                    ],
                ]
            ],
            // Social networks
            [
                'title'            => esc_html__('Social Networks', 'wilcity'),
                'id'               => 'social_network_settings',
                'subsection'       => false,
                'icon'             => 'dashicons dashicons-share',
                'customizer_width' => '500px',
                'fields'           => WilokeSocialNetworks::render_setting_field()
            ],
            [
                'title'            => esc_html__('404', 'wilcity'),
                'id'               => 'page_not_found',
                'icon'             => 'dashicons dashicons-hidden',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'    => '404_bg',
                        'type'  => 'media',
                        'title' => esc_html__('Image Background', 'wilcity')
                    ],
                    [
                        'id'      => '404_heading',
                        'type'    => 'textarea',
                        'title'   => esc_html__('Heading', 'wilcity'),
                        'default' => '404'
                    ],
                    [
                        'id'      => '404_description',
                        'type'    => 'textarea',
                        'title'   => esc_html__('Description', 'wilcity'),
                        'default' => 'Sorry, We couldn\'t find what you were looking for. Maybe try searching for an alternative?'
                    ]
                ]
            ],
            [
                'title'            => 'Footer',
                'id'               => 'footer_settings',
                'icon'             => 'dashicons dashicons-editor-kitchensink',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'footer_items',
                        'type'    => 'select',
                        'title'   => 'Number Of Footer Items',
                        'options' => [
                            4 => '4 Items',
                            3 => '3 Items',
                            2 => '2 Items'
                        ],
                        'default' => 4
                    ],
                    [
                        'id'      => 'copyright',
                        'type'    => 'textarea',
                        'title'   => 'Copyright',
                        'default' => 'Copyright © 2018 Wiloke.com.'
                    ]
                ]
            ],
            [
                'title'            => 'Customize URL',
                'id'               => 'custom_url_settings',
                'icon'             => 'dashicons dashicons-admin-links',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'taxonomy_add_parent_to_permalinks',
                        'type'    => 'select',
                        'title'   => 'Add Parent Location To Permalink',
                        'options' => [
                            'disable' => 'Disable',
                            'enable'  => 'Enable'
                        ],
                        'default' => 'disable'
                    ],
                    [
                        'id'     => 'listing_permalink_settings_open',
                        'type'   => 'section',
                        'title'  => 'Single Listing Settings',
                        'indent' => true
                    ],
                    [
                        'id'          => 'listing_permalink_settings',
                        'type'        => 'text',
                        'title'       => 'Listings Permalink Settings',
                        'default'     => '',
                        'subtitle'    => 'Leave empty to use the default setting. After adding your customize permalink, please go to <a href="'.
                                         admin_url('options-permalink.php').
                                         '" target="_blank">Settings -> Permalinks</a> -> Re-save Post Name.',
                        'description' => 'Please read <a href="https://tinyurl.com/y7cr8z3w" target="_blank">this tutorial</a> to learn how to customize your single listing url.'
                    ],
                    [
                        'id'     => 'listing_mobile_section_close',
                        'type'   => 'section',
                        'title'  => '',
                        'indent' => false
                    ]
                ]
            ],
            [
                'title'            => esc_html__('Advanced Settings', 'wilcity'),
                'id'               => 'advanced_settings',
                'icon'             => 'dashicons dashicons-dashboard',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => [
                    [
                        'id'      => 'advanced_google_fonts',
                        'type'    => 'select',
                        'title'   => esc_html__('Google Fonts', 'wilcity'),
                        'options' => [
                            'default' => esc_html__('Default', 'wilcity'),
                            'general' => esc_html__('Custom', 'wilcity'),
                            // 'detail'    => esc_html__('Detail Custom', 'wilcity')
                        ],
                        'default' => 'default'
                    ],
                    [
                        'id'          => 'advanced_general_google_fonts',
                        'type'        => 'text',
                        'title'       => esc_html__('Google Fonts', 'wilcity'),
                        'required'    => ['advanced_google_fonts', '=', 'general'],
                        'description' => esc_html__('The theme allows replace current Google Fonts with another Google Fonts. Go to https://fonts.google.com/specimen to get a the Font that you want. For example: https://fonts.googleapis.com/css?family=Prompt',
                            'wilcity')
                    ],
                    [
                        'id'          => 'advanced_general_google_fonts_css_rules',
                        'type'        => 'text',
                        'required'    => ['advanced_google_fonts', '=', 'general'],
                        'title'       => esc_html__('Css Rules', 'wilcity'),
                        'description' => esc_html__('This code shoule be under Google Font link. For example: font-family: \'Prompt\', sans-serif;',
                            'wilcity')
                    ],
                    [
                        'id'      => 'advanced_main_color',
                        'type'    => 'select',
                        'title'   => esc_html__('Theme Color', 'wilcity'),
                        'options' => [
                            ''       => 'Default',
                            'cyan'   => 'Cyan',
                            'blue'   => 'Blue',
                            'pink'   => 'Pink',
                            'red'    => 'red',
                            'custom' => 'Custom'
                        ],
                        'default' => ''
                    ],
                    [
                        'id'       => 'advanced_custom_main_color',
                        'type'     => 'color_rgba',
                        'title'    => esc_html__('Custom Color', 'wilcity'),
                        'required' => ['advanced_main_color', '=', 'custom']
                    ],
                    [
                        'id'          => 'sidebar_additional',
                        'type'        => 'text',
                        'title'       => esc_html__('Add More Sidebar', 'wilcity'),
                        'description' => esc_html__('You can add more sidebar by entering in your sidebar id here. For example: my_custom_sidebar_1,my_custom_sidebar_2',
                            'wilcity'),
                        'default'     => ''
                    ],
                    [
                        'id'    => 'advanced_css_code',
                        'type'  => 'ace_editor',
                        'title' => esc_html__('Custom CSS Code', 'wilcity'),
                        'mode'  => 'css',
                        'theme' => 'monokai'
                    ],
                    [
                        'id'          => 'advanced_js_code',
                        'type'        => 'ace_editor',
                        'title'       => esc_html__('Custom Javascript Code', 'wilcity'),
                        'description' => 'The code should not contain &lt;script> tag. If you want to add a Custom code to website, We recommend using <a href="https://wordpress.org/plugins/insert-headers-and-footers/">Insert Headers and Footers</a> plugin',
                        'mode'        => 'javascript',
                        'default'     => ''
                    ]
                ]
            ]
        ])
    ]
];
