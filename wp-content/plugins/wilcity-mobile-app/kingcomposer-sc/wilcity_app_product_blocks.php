<?php
use \WILCITY_SC\SCHelpers;

$atts = shortcode_atts(
    [
        'TYPE'             => 'WOOCOMMERCE_PRODUCT_BLOCKS',
        'heading'          => '',
        'post_type'        => 'product',
        'style'            => 'grid',
        'number_of_blocks' => 3,
        'items_per_column' => 3,
        'orderby'          => 'recent_products',
        'product_cats'     => '',
        'order'            => 'DESC',
        'product_ids'      => '',
        'bg_color'         => '#ffffff'
    ],
    $atts
);
if (!trait_exists('WILCITY_APP\Controllers\JsonSkeleton')) {
    return '';
}

if (empty($atts['items_per_column']) || empty($atts['number_of_blocks'])) {
    echo '';
    
    return false;
}

$aArgs = SCHelpers::parseArgs($atts);
switch ($aArgs['orderby']) {
    case 'recent_products':
        $aArgs['orderby'] = 'post_date';
        break;
    case 'top_sales':
        $aArgs['meta_key'] = 'total_sales';
        $aArgs['orderby']  = 'meta_value_num';
        break;
    case 'featured_products':
        $aArgs['meta_key'] = '_featured';
        $aArgs['orderby']  = 'meta_value_num';
        break;
    case 'top_rated_products':
        break;
    case 'sale_products':
        $aMetaQuery   = [];
        $aMetaQuery[] = WC()->query->visibility_meta_query();
        $aMetaQuery[] = WC()->query->stock_status_meta_query();
        $aMetaQuery   = array_filter($aMetaQuery);
        
        $aProductIDsOnSale   = wc_get_product_ids_on_sale();
        $aArgs['post__in']   = array_merge([0], $aProductIDsOnSale);
        $aArgs['meta_query'] = $aMetaQuery;
        $aArgs['orderby']    = 'title';
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
            'terms'    => ['simple', 'external', 'variable']
        ],
    ]
];

$atts['items_per_column'] = abs($atts['items_per_column']);
$atts['number_of_blocks'] = abs($atts['number_of_blocks']);
$aArgs['posts_per_page']  = $atts['items_per_column'] * $atts['number_of_blocks'];

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
    $aProduct    = apply_filters('wilcity/wilcity-mobile-app/wilcity_app_product_blocks', $product, $query->post,
        $atts);
    $aResponse[] = $aProduct;
}
wp_reset_postdata();

echo '%SC%'.json_encode(
        [
            'oSettings' => \WILCITY_APP\Helpers\AppHelpers::removeUnnecessaryParamOnApp($atts),
            'TYPE'      => $atts['TYPE'],
            'oResults'  => array_chunk($aResponse, $atts['number_of_blocks'])
        ]
    ).'%SC%';
return '';
