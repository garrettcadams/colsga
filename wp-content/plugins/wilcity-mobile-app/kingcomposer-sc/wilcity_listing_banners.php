<?php
$atts = shortcode_atts(
    [
        'TYPE'           => 'LISTING_BANNERS',
        'banners'        => '',
        'bg_color'       => '#ffffff',
        'slide_interval' => 3000
    ],
    $atts
);

if (empty($atts['banners'])) {
    return '';
}

$aBanners = $atts['banners'];
unset($atts['banners']);

foreach ($aBanners as $oBanner) {
    $aParsePostID = explode(':', $oBanner->postID);
    $oBanner->postID = absint($aParsePostID[0]);
    $oBanner->oListing = apply_filters('wilcity/mobile/render_listings_on_mobile', $atts, get_post($oBanner->postID));
    $atts['banners'][] = $oBanner;
}

$atts['slider_interval'] = empty($atts['slider_interval']) ? 3000 : abs($atts['slider_interval']);

$aAtts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($atts);
echo '%SC%'.json_encode(\WILCITY_APP\Helpers\AppHelpers::removeUnnecessaryParamOnApp($aAtts)).'%SC%';
