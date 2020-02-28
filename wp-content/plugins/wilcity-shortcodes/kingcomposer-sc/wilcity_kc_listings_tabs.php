<?php
use \WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Framework\Helpers\TermSetting;

$aAtts = shortcode_atts(
    [
        'heading'                    => '',
        'heading_color'              => '',
        'style'                      => 'grid',
        'taxonomy'                   => 'listing_cat',
        'get_term_type'              => 'term_children',
        'order'                      => 'DESC',
        'orderby'                    => 'post_title',
        'posts_per_page'             => 6,
        'img_size'                   => 'wilcity_360x200',
        'number_of_term_children'    => 6,
        'radius'                     => 10,
        'listing_locations'          => '',
        'toggle_viewmore'            => 'enable',
        'listing_cats'               => '',
        'terms_tab_id'               => '',
        'tab_alignment'              => 'wil-text-right',
        'maximum_posts_on_lg_screen' => 'col-lg-4',
        'maximum_posts_on_md_screen' => 'col-md-4',
        'maximum_posts_on_sm_screen' => 'col-md-12',
        'post_types_filter'          => ''
    ],
    $atts
);

wilcityRenderListingsTabsSC($aAtts);
