<?php
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Frontend\PriceRange;
use WilokeListingTools\Framework\Helpers\GetSettings;

add_shortcode('wilcity_sidebar_single_price', 'wilcitySidebarSinglePrice');
function wilcitySidebarSinglePrice($aArgs){
	global $post;
	if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_single_price') ){
		return '';
	}
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'          => esc_html__('Single Price', WILOKE_LISTING_DOMAIN),
			'icon'          => 'la la-qq',
			'currencyIcon'  => apply_filters('wilcity/price-range/icon', 'la la-dollar')
		)
	);

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/price_range', $post, $aAtts);
	}

	$symbol = GetWilokeSubmission::getSymbol(GetWilokeSubmission::getField('currency_code'));
	$symbol = apply_filters('wilcity/price-range/currencySymbol', $symbol);

	if ( !$symbol ){
		return '';
	}

	$price = GetSettings::getPostMeta($post->ID, 'single_price');

	if ( empty($price) ){
		return '';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-sidebar-item-single-price content-box_module__333d9')); ?>">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
		<div class="content-box_body__3tSRB">
			<div class="price-range_module__348HY clearfix">
				<div class="price-range_sign__3thLa"><i class="<?php echo esc_attr($symbol); ?>"></i></div>
				<div class="price-range_group__23kMb">
					<div class="price-range_range__19Fut color-primary">
						<div class="price-range_from__3iV-6"><sup class="price-range_supSig__1pMDY <?php echo esc_attr($symbol); ?>"><?php echo esc_attr($symbol); ?></sup><?php echo esc_html($price); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}