<?php
function wilcity_render_term_masonry_items($atts){
	$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($atts);
	$aArgs = \WILCITY_SC\SCHelpers::parseTermQuery($atts);

	$aTerms = get_terms($aArgs);
	if ( empty($aTerms) || is_wp_error($aTerms) ){
		if ( isset($aArgs['include']) ){
			$aArgs['number'] = count($aArgs['include']);
			unset($aArgs['include']);
			$aTerms = get_terms($aArgs);
		}
		if ( empty($aTerms) || is_wp_error($aTerms) ){
			return '';
		}
	}

	if ( $atts['isApp'] ){
		$aResponse = array();
		foreach ($aTerms as $oTerm){
			$aPostFeaturedImgs = \WilokeListingTools\Framework\Helpers\GetSettings::getPostFeaturedImgsByTerm($oTerm->term_id, $atts['taxonomy']);

			$aResponse[] = array(
				'oTerm' => $oTerm,
				'aPostFeaturedImg' => $aPostFeaturedImgs,
				'oCount' => array(
					'number' => $oTerm->count,
					'text'   => $oTerm->count > 1 ? esc_html__('Listings', 'wilcity-shortcodes') : esc_html__('Listing', 'wilcity-shortcodes')
				),
				'oIcon' => WilokeHelpers::getTermOriginalIcon($oTerm)
			);
		}

		echo '%SC%' . json_encode(array(
				'oSettings' => $atts,
				'oResults'  => $aResponse,
				'TYPE'      => $atts['TYPE']
			)) . '%SC%';
		return '';
	}

	$wrapper_class	= apply_filters( 'wilcity-el-class', $atts );
	$wrapper_class  = implode(' ', $wrapper_class) . '  ' . $atts['extra_class'] . ' wil-masonry_module__hEqFd';
	?>
	<div class="<?php echo esc_attr($wrapper_class); ?>">
		<?php
		if ( !empty($atts['heading']) || !empty($atts['desc']) ){
			wilcity_render_heading(array(
				'TYPE'              => 'HEADING',
				'blur_mark'         => '',
				'blur_mark_color'   => '',
				'heading'           => $atts['heading'],
				'heading_color'     => $atts['heading_color'],
				'description'       => $atts['description'],
				'description_color' => $atts['description_color'],
				'alignment'         => $atts['header_desc_text_align'],
				'extra_class'       => ''
			));
		}
		?>

		<?php
			$termsCount = 11;
			if ( count($aTerms) < 11 ) {
				$termsCount = count($aTerms);
			}
		?>

		<div class="row" data-col-xs-gap="<?php echo esc_attr($atts['col_gap']); ?>" data-count="<?php echo esc_attr($termsCount); ?>">
			<?php
			foreach ($aTerms as $oTerm) {
				wilcity_render_term_masonry_item($oTerm, $atts);
			} ?>
		</div>
	</div>
	<?php
}