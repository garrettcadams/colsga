<?php
function wilcityVcRestaurantListing($atts){
	$atts = wp_parse_args(
		$atts,
		array(
			'type'                      => 'WILCITY_RESTAURANT_LISTINGS',
			'heading_style'             => 'ribbon',
			'ribbon'                    => 'col-md-4',
			'ribbon_color'              => '',
			'heading'                   => '',
			'desc'                      => '',
			'desc_color'                => '',
			'header_desc_text_align'    => 'wil-text-center',
			'toggle_viewmore'           => 'enable',
			'viewmore_btn_name'         => 'View Full Menu',
			'viewmore_icon'             => 'la la-glass',
			'posts_per_page'            => 6,
			'excerpt_length'            => 100,
			'post_type'                 => 'listing',
			'listing_tags'              => '',
			'listing_cats'              => '',
			'listing_locations'         => '',
			'custom_taxonomy_key'       => '',
			'custom_taxonomies_id'      => '',
			'listing_ids'               => '',
			'orderby'                   => '',
			'order'                     => '',
			'extra_class'               => '',
			'css'                       => ''
		)
	);
	ob_start();
	wilcityRenderRestaurantListings($atts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode('wilcity_vc_restaurant_listings', 'wilcityVcRestaurantListing');