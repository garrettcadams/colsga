<?php
/**
 * @param Array(key=>val)
 * key: key of aConfigs
 * val: a part of file name: config.val.php
 */
return array(
    'configs' => array(
        'themeoptions'      => 'themeoptions',
        'vc'                => 'vc',
        'metaboxes'         => 'metaboxes',
        'install_plugins'   => 'install_plugins',
        'frontend'          => 'frontend',
        'taxonomy'          => 'taxonomy',
        'pagebuilder'       => 'pagebuilder',
        'widgets'           => 'widgets',
        'contactform7'      => 'contactform7',
    ),
    'single_portfolio_info' => array('single_portfolio_info_date', 'single_portfolio_info_created_by'),
    'knowledgebase'         => 'https://blog.wiloke.com/listgo-knowledge-base/',
    'support_forum_url'     => 'http://support.wiloke.com',
    'changelog'             => 'https://wiloke.net/themes/changelog/7',
    'theme_options'          =>  array(
        'id'    => 'wiloke-options',
        'title' => esc_html__('Wiloke Options', 'wilcity'),
        'href'  => admin_url('themes.php?page=Wiloke'),
        'meta'  => ''
    ),
    'redux_extensions' => array('wiloke_repeater'=>'Wiloke_Repeater'),
    'color_picker'     => array(
        'palette' => array(
            array("#000000", "#171717", "#ef6e6e", "#fff")
        )
    ),
    'is_enable_updated' => array(
        'plugins'=> false,
        'theme'  => false
    ),
    'theme_slug' => 'wilcity',
    'rewrite_link'=>array(
        'portfolio' => array(
            'title' => esc_html__('Portfolio Permalinks', 'wilcity'),
            'taxonomy' => 'portfolio_category'
        )
    ),
    'wiloke_design_portfolio' => array(
        'large'     => array(
            'title' => 'Large',
            'desc'  => '≥1200px'
        ),
        'medium'    => array(
            'title' => 'Medium',
            'desc'  => '≥992px'
        ),
        'small'     => array(
            'title' => 'Small',
            'desc'  => '≥768px'
        ),
        'extra_small' => array(
            'title' => 'Extra Small',
            'desc'  => '<768px'
        )
    ),
    'img_sizes' => array(
        'wiloke_xs_thumb' => array(20, false, true)
    ),
	'wiloke_design' => array(
		'cube'  => 'wiloke_listgo_370x370',
		'large' => 'large',
		'rest'  => 'large'
	)
);