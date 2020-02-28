<?php

namespace WILCITY_APP\SidebarOnApp;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Models\ReviewModel;

class Statistic {
	public function __construct() {
		add_filter('wilcity/mobile/sidebar/statistics', array($this, 'render'));
	}

	public function render($post){
		$views = GetSettings::getPostMeta($post->ID, 'count_viewed');
		$reviews = ReviewModel::countAllReviewedOfListing($post->ID);
		$favorites = GetSettings::getPostMeta($post->ID, 'count_favorites');
		$shares = GetSettings::getPostMeta($post->ID, 'count_shared');


		$views = empty($views) ? 0 : abs($views);
		$reviews = empty($reviews) ? 0 : abs($reviews);
		$favorites = empty($favorites) ? 0 : abs($favorites);
		$shares = empty($shares) ? 0 : abs($shares);

		return json_encode(array(
			array(
				'name' => $views > 1 ? esc_html__('Views', WILCITY_SC_DOMAIN) : esc_html__('View', WILCITY_SC_DOMAIN),
				'count'=> $views,
				'key'  => 'views'
			),
			array(
				'name' => $reviews > 1 ? esc_html__('Reviews', WILCITY_SC_DOMAIN) : esc_html__('Reviews', WILCITY_SC_DOMAIN),
				'count'=> $reviews,
				'key'  => 'reviews'
			),
			array(
				'name' => $favorites > 1 ? esc_html__('Favorites', WILCITY_SC_DOMAIN) : esc_html__('Favorite', WILCITY_SC_DOMAIN),
				'count'=> $favorites,
				'key'  => 'favorites'
			),
			array(
				'name' => $shares > 1 ? esc_html__('Shares', WILCITY_SC_DOMAIN) : esc_html__('Share', WILCITY_SC_DOMAIN),
				'count'=> $shares,
				'key'  => 'shares'
			)
		));
	}
}