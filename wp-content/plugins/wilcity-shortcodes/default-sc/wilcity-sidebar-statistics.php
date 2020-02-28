<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Models\ReviewModel;

add_shortcode('wilcity_sidebar_statistics', 'wilcitySidebarStatistics');
function wilcitySidebarStatistics($aArgs){
	global $post;
	if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_sidebar_statistics') ){
	    return '';
    }

	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/statistics', $post, $aAtts);
	}

	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'  => esc_html__('Statistic', WILOKE_LISTING_DOMAIN),
			'icon'  => 'la la-bar-chart'
		)
	);

	$views = GetSettings::getPostMeta($post->ID, 'count_viewed');
	$views = empty($views) ? 0 : $views;
	$totalReviews = ReviewModel::countAllReviewedOfListing($post->ID);
	$totalFavorites = GetSettings::getPostMeta($post->ID, 'count_favorites');
	$totalShares = GetSettings::getPostMeta($post->ID, 'count_shared');

	?>
	<div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-sidebar-item-statistic')); ?> content-box_module__333d9">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
		<div class="content-box_body__3tSRB">
			<div class="row">
				<div class="col-sm-6 col-sm-6-clear">
					<div class="icon-box-1_module__uyg5F two-text-ellipsis mt-20 mt-sm-15">
						<div class="icon-box-1_block1__bJ25J">
							<div class="icon-box-1_icon__3V5c0 rounded-circle"><i class="la la-eye"></i></div>
							<div class="icon-box-1_text__3R39g"><?php echo sprintf(__('%s Views', 'wilcity-shortcodes'), $views); ?></div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-sm-6-clear">
					<div class="icon-box-1_module__uyg5F two-text-ellipsis mt-20 mt-sm-15">
						<div class="icon-box-1_block1__bJ25J">
							<div class="icon-box-1_icon__3V5c0 rounded-circle"><i class="la la-star-o"></i></div>
							<div class="icon-box-1_text__3R39g"><?php echo empty($totalReviews) ? $totalReviews . ' ' . esc_html__('Rating', 'wilcity-shortcodes') :  sprintf(_n('%d Rating', '%d Ratings', $totalReviews,  'wilcity-shortcodes'), $totalReviews); ?></div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-sm-6-clear">
					<div class="icon-box-1_module__uyg5F two-text-ellipsis mt-20 mt-sm-15">
						<div class="icon-box-1_block1__bJ25J">
							<div class="icon-box-1_icon__3V5c0 rounded-circle"><i class="la la-heart-o"></i></div>
							<div class="icon-box-1_text__3R39g"><?php echo empty($totalFavorites) ? 0 . ' ' . esc_html__('Favorite', 'wilcity-shortcodes') : sprintf(_n('%d Favorite', '%d Favorites', $totalFavorites,  'wilcity-shortcodes'), $totalFavorites); ?></div>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-sm-6-clear">
					<div class="icon-box-1_module__uyg5F two-text-ellipsis mt-20 mt-sm-15">
						<div class="icon-box-1_block1__bJ25J">
							<div class="icon-box-1_icon__3V5c0 rounded-circle"><i class="la la-share"></i></div>
							<div class="icon-box-1_text__3R39g"><?php echo  empty($totalShares) ? $totalShares . ' ' . esc_html__('Share', 'wilcity-shortcodes') : sprintf(_n('%d Share', '%d Shares', $totalShares,  'wilcity-shortcodes'), $totalShares); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}