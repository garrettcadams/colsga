<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WILCITY_SC\SCHelpers;
use WilokeListingTools\Controllers\ReviewController;

$atts = shortcode_atts(
    [
        'TYPE'                       => 'GRID',
        '_id'                        => '',
        'heading'                    => 'The Latest Listings',
        'heading_color'              => '',
        'desc'                       => '',
        'desc_color'                 => '',
        'header_desc_text_align'     => '',
        'border'                     => '',
        'post_type'                  => 'listing',
        'from'                       => 'all',
        'maximum_posts_on_lg_screen' => 'col-lg-3',
        'maximum_posts_on_md_screen' => 'col-md-4',
        'maximum_posts_on_sm_screen' => 'col-sm-6',
        'img_size'                   => 'wilcity_img_360x200',
        'mobile_img_size'            => '',
        'orderby'                    => '',
        'order'                      => '',
        'unit'                       => 'km',
        'radius'                     => 10,
        'tabname'                    => '',
        'posts_per_page'             => 6,
        'listing_cats'               => '',
        'toggle_viewmore'            => 'disable',
        'viewmore_btn_name'          => 'View more',
        'style'                      => 'style1',
        'custom_taxonomy_key'        => '',
        'custom_taxonomies_id'       => '',
        'listing_locations'          => '',
        'listing_tags'               => '',
        'listing_ids'                => '',
        'extra_class'                => '',
        'class'                      => '',
        'css_custom'                 => ''
    ],
    $atts
);
wilcity_sc_render_grid($atts);
