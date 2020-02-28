<?php
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Frontend\PriceRange;
use WilokeListingTools\Framework\Helpers\GetSettings;

add_shortcode('wilcity_sidebar_price_range', 'wilcitySidebarPriceRange');
function wilcitySidebarPriceRange($aArgs){
	global $post;

	if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_price_range') ){
        return '';
    }
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'          => esc_html__('Price Range', WILOKE_LISTING_DOMAIN),
			'icon'          => 'la la-qq',
			'currencyIcon'  => apply_filters('wilcity/price-range/icon', 'la la-dollar')
		)
	);

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/price_range', $post, $aAtts);
	}

	$symbol = PriceRange::getSymbol($post);
	$range = GetSettings::getPostMeta($post->ID,'price_range');

	$symbol = apply_filters('wilcity/price-range/currencySymbol', $symbol, $range);
	if ( !$symbol ){
	    return '';
    }

    $maximumPrice = GetSettings::getPostMeta($post->ID, 'maximum_price');
    $minimumPrice = GetSettings::getPostMeta($post->ID, 'minimum_price');

    if ( empty($maximumPrice) && empty($minimumPrice) ){
        return '';
    }

	$currencyCode   = GetWilokeSubmission::getField('currency_code');
	$currencySymbol =  GetWilokeSubmission::getSymbol($currencyCode);
    $desc = GetSettings::getPostMeta($post->ID, 'price_range_desc');
    $currencyPos = GetWilokeSubmission::getField('currency_position');

	$currencySymbol = apply_filters('wilcity-shortcodes/currency-symbol', $currencySymbol);

	ob_start();
	?>
	<div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-sidebar-item-price-range content-box_module__333d9')); ?>">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
		<div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'content-box_body__3tSRB wilcity-price-range-'.esc_attr($range))); ?>">
			<!-- price-range_module__348HY clearfix -->
			<div class="price-range_module__348HY clearfix">
				<?php if ( !empty($desc) ) : ?>
					<div class="price-range_text__Uwf_4"><?php \Wiloke::ksesHTML($desc); ?></div>
				<?php endif; ?>
				<div class="price-range_sign__3thLa">
                    <?php if (has_action('wilcity/wilcity-shortcodes/wilcity-pricing/render-price/symbol-icon')){
                            do_action('wilcity/wilcity-shortcodes/wilcity-pricing/render-price/symbol-icon');
                          }else{
                        ?>
                        <i class="<?php echo esc_attr($aAtts['currencyIcon']); ?>"></i>
                        <?php
                    }
                      ?>
                </div>
				<div class="price-range_group__23kMb">
					<div class="price-range_sign2__3xNkO"><?php echo esc_html($symbol); ?></div>
					<div class="price-range_range__19Fut color-primary">
						<div class="price-range_from__3iV-6"><sup class="price-range_supSig__1pMDY <?php echo esc_attr($currencyPos); ?>"><?php echo esc_attr($currencySymbol); ?></sup><?php echo esc_html($minimumPrice); ?></div>

                        <?php if ( !empty($maximumPrice) ) : ?>
						<div class="price-range_arrow__3LnNe"><i class="la la-arrow-right"></i></div>
						<div class="price-range_to__2Tf3W"><sup class="price-range_supSig__1pMDY <?php echo esc_attr($currencyPos); ?>"><?php echo esc_attr($currencySymbol); ?></sup><?php echo esc_html($maximumPrice); ?></div>
                        <?php endif; ?>

					</div>
				</div>
			</div><!-- End / price-range_module__348HY clearfix -->
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}