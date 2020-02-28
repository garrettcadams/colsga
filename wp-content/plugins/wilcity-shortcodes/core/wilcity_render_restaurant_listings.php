<?php
function wilcityRenderRestaurantListings($aAtts){
	switch ($aAtts['heading_style']){
		case 'ribbon':
			wilcityRenderHeadingRibbon($aAtts);
			break;
		default:
			wilcity_render_heading($aAtts);
			break;
	}

	$wrapperClass = 'row wil-text-left ' . $aAtts['extra_class'];
	$aArgs = $aArgs = \WILCITY_SC\SCHelpers::parseArgs($aAtts);

	if ( \WILCITY_SC\SCHelpers::isApp($aAtts) ){
		if ( $aAtts['isApp'] ){
			echo '%SC%' . json_encode($aAtts) . '%SC%';
			return '';
		}
	}

	$query = new WP_Query($aArgs);
	if ( !$query->have_posts() ){
		wp_reset_postdata();
		return '';
	}
	?>
	<div class="<?php echo esc_attr($wrapperClass); ?>">
		<?php
		$aAtts['item_wrapper'] = 'col-md-6 col-lg-6 ';
		while ($query->have_posts()):
			$query->the_post();
			wilcityRenderRestaurantListing($query->post, $aAtts);
		endwhile; wp_reset_postdata();
		if ( $aAtts['toggle_viewmore'] == 'enable' ):
		?>
			<div class="col-md-12 col-lg-12">
				<div class="wil-text-center">
					<a class="wil-btn wil-btn--primary wil-btn--round wil-btn--md" href="<?php echo esc_url(\WILCITY_SC\SCHelpers::getViewAllUrl($aAtts)); ?>"><i class="<?php echo esc_attr($aAtts['viewmore_icon']); ?>"></i> <?php echo esc_html($aAtts['viewmore_btn_name']); ?></a>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
