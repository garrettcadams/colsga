<?php
use \WilokeListingTools\Framework\Helpers\GetSettings;

function wilcity_render_rectangle_term_box( $oTerm, $wrapperClass, $aAtts = array() ) {
	$featuredImgId = GetSettings::getTermMeta( $oTerm->term_id, 'featured_image_id' );
	if ( ! empty( $featuredImgId ) ) {
		if ( empty( $aAtts['image_size'] ) ) {
			$aAtts['image_size'] = 'medium';
		}
		$featuredImg = wp_get_attachment_image_url( $featuredImgId, $aAtts['image_size'] );
	} else {
		$featuredImg = GetSettings::getTermMeta( $oTerm->term_id, 'featured_image' );
	}

	$totalChildren = GetSettings::countChildren( $oTerm );
	if ( $totalChildren !== 0 ) {
		$i18 = sprintf(
			_n( '%s Sub category', '%s Sub categories', $totalChildren, 'wilcity-shortcodes' ),
			number_format_i18n($totalChildren)
		);
	} else {
		if ( is_tax() ) {
			$totalListings = get_query_var( 'taxonomy' ) == $oTerm->taxonomy ? $oTerm->count : GetSettings::countTermDependsOnCurrentTerm( $oTerm->term_id );
		} else {
			$totalListings = $oTerm->count;
		}

		if ( $totalListings < 1 ) {
			$i18 = esc_html__( '0 Listing', 'wilcity-shortcodes' );
		} else {
			$i18 = sprintf(
				_n( '%s Listing', '%s Listings', $totalListings, 'wilcity-shortcodes' ),
				number_format_i18n($totalListings)
			);
		}
	}

	?>
    <div class="<?php echo esc_attr( $wrapperClass ); ?>">
        <div class="textbox-4_module__2gJjK">
            <a href="<?php echo esc_url( get_term_link( $oTerm->term_id ) ) ?>">
                <div class="textbox-4_background__3bSqa">
                    <div class="wil-overlay"></div>
					<?php \WILCITY_SC\SCHelpers::renderLazyLoad( $featuredImg, array( 'divClass' => 'textbox-4_img__2_DKb bg-cover' ) ); ?>
                </div>
                <div class="textbox-4_top__1919H"><i
                            class="la la-edit color-primary"></i> <?php echo esc_html( $i18 ); ?></div>
                <div class="textbox-4_content__1B-wJ">
                    <h3 class="textbox-4_title__pVQr7"><?php echo esc_html( $oTerm->name ); ?></h3>
                    <span class="wil-btn wil-btn--primary wil-btn--block wil-btn--lg wil-btn--round "><?php
                        esc_html_e('Discover', 'wilcity-shortcodes');
                        ?></span>
                </div>
            </a>
        </div>
    </div>
	<?php
}
