<?php
use \WILCITY_SC\SCHelpers;

function wilcityVCListingsTabs($atts)
{
    $atts = shortcode_atts(
        [
            'TYPE'                       => 'LISTINGS_TABS',
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
            'listing_cats'               => '',
            'terms_tab_id'               => '',
            'toggle_viewmore'            => 'enable',
            'tab_alignment'              => 'wil-text-right',
            'maximum_posts_on_lg_screen' => 'col-lg-4',
            'maximum_posts_on_md_screen' => 'col-md-4',
            'maximum_posts_on_sm_screen' => 'col-md-12',
            'post_types_filter'          => ''
        ],
        $atts
    );
    
    $atts = apply_filters('wilcity/vc/parse_sc_atts', $atts);
    ob_start();
    wilcityRenderListingsTabsSC($atts);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

add_shortcode('wilcity_vc_listings_tabs', 'wilcityVCListingsTabs');
