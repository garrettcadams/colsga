<?php
$atts = shortcode_atts(
	array(
        'heading'                       => '',
        '_id'      => '',
        'heading_color'                 => '',
        'desc'                          => '',
        'desc_color'                    => '',
        'header_desc_text_align'        => '',
        'post_type'                     => 'event',
        'listing_tags'                  => '',
        'listing_cats'                  => '',
        'listing_locations'             => '',
        'maximum_posts_on_lg_screen'    => 'col-lg-3',
        'maximum_posts_on_md_screen'    => 'col-md-4',
        'maximum_posts_on_sm_screen'    => 'col-sm-6',
        'from'                          => 'all',
        'toggle_viewmore'               => 'disable',
        'viewmore_btn_name'             => 'View more',
        'orderby'                       => 'post_date',
        'order'                         => 'DESC',
        'img_size'                      => 'wilcity_img_360x200',
        'mobile_img_size'               => '',
        'posts_per_page'                => 6,
        'custom_taxonomy_key'           => '',
        'custom_taxonomies_id'          => '',
		'extra_class'                   => '',
        'css_custom'                => ''
	),
	$atts
);

wilcity_sc_render_events_grid($atts);