<?php
use \WILCITY_SC\SCHelpers;

$atts = shortcode_atts(
    [
        'TYPE'          => 'WOOCOMMERCE_BOOKINGS',
        'post_type'     => 'product',
        'style'         => 'grid',
        'maximum_posts' => 4,
        'items_per_row' => 2,
        'orderby'       => 'recent_products',
        'product_cats'  => '',
        'order'         => 'DESC',
        'product_ids'   => '',
        'bg_color'      => '#ffffff'
    ],
    $atts
);
if (!trait_exists('WILCITY_APP\Controllers\JsonSkeleton')) {
    return '';
}

$aArgs = SCHelpers::parseArgs($atts);
switch ($aArgs['orderby']) {
    case 'recent_products':
        $aArgs['orderby'] = 'post_date';
        break;
    case 'featured_products':
        $aArgs['meta_key'] = '_featured';
        $aArgs['orderby']  = 'meta_value_num';
        break;
    case 'specify_product_ids':
        $aArgs['post__in']       = SCHelpers::getAutoCompleteVal($atts['product_ids']);
        $aArgs['posts_per_page'] = count($aArgs['post__in']);
        $aArgs['orderby']        = 'post__in';
        break;
}

$aArgs['meta_query'][] = [
    'relation' => 'OR',
    [
        'key'     => 'wilcity_exclude_from_shop',
        'compare' => 'NOT EXISTS'
    ],
    [
        'key'     => 'wilcity_exclude_from_shop',
        'value'   => 'yes',
        'compare' => '!='
    ]
];

$aArgs['tax_query'] = [
    [
        [
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => ['booking']
        ],
    ]
];

if ($aArgs['orderby'] == 'top_rated_products') {
    add_filter('posts_clauses', ['WC_Shortcodes', 'order_by_rating_post_clauses']);
    $query = new WP_Query($aArgs);
    remove_filter('posts_clauses', ['WC_Shortcodes', 'order_by_rating_post_clauses']);
} else {
    $query = new WP_Query($aArgs);
}

if (!$query->have_posts()) {
    wp_reset_postdata();

    return '';
}

$aResponse = [];
while ($query->have_posts()) {
    $query->the_post();
    global $product;
    $aProduct    = apply_filters('wilcity/wilcity-mobile-app/filter/wilcity_bookings_on_mobile', $product, $query->post,
        $atts);
    $aResponse[] = $aProduct;
}
wp_reset_postdata();

echo '%SC%'.json_encode(
        [
            'oSettings' => \WILCITY_APP\Helpers\AppHelpers::removeUnnecessaryParamOnApp($atts),
            'TYPE'      => $atts['TYPE'],
            'oResults'  => $aResponse
        ]
    ).'%SC%';
return '';
