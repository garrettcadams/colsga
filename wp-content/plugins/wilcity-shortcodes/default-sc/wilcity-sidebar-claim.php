<?php
add_shortcode('wilcity_sidebar_claim', 'wilcitySidebarClaim');

function wilcitySidebarClaim($aArgs){
    global $post;

    $status = \WilokeListingTools\Framework\Helpers\GetSettings::getPostMeta($post->ID, 'claim_status');

	$aSupportedPostTypes = \WilokeListingTools\Framework\Helpers\GetSettings::getFrontendPostTypes(true);
    if (  !in_array($post->post_type, $aSupportedPostTypes) ){
        return '';
    }

    if ( $status == 'claimed' ){
        return '';
    }

	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'          => esc_html__('Claim Listing', 'wilcity-shortcodes'),
			'boxTitle'      => esc_html__('Is this your business?', 'wilcity-shortcodes'),
			'boxDesc'       => esc_html__('Claim listing is the best way to manage and protect your business.', 'wilcity-shortcodes'),
			'icon'          => 'la la-qq',
			'desc'          => '',
			'currencyIcon'  => 'la la-dollar'
		)
	);

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/claim', $post, $aAtts);
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-sidebar-item-claim')); ?> content-box_module__333d9">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
		<div class="content-box_body__3tSRB">
			<div class="promo-item_module__24ZhT">
				<div class="promo-item_group__2ZJhC">
					<h3 class="promo-item_title__3hfHG"><?php echo esc_html($aAtts['boxTitle']); ?></h3>
					<p class="promo-item_description__2nc26"><?php Wiloke::ksesHTML($aAtts['boxDesc'], false); ?></p>
				</div>
				<div class="promo-item_action__pd8hZ">
                    <claim-popup-btn target-id="<?php echo esc_attr($post->ID); ?>"><template slot="insideBtn"><?php esc_html_e('Claim Now', 'wilcity-shortcodes'); ?></template></claim-popup-btn>
				</div>
			</div>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
