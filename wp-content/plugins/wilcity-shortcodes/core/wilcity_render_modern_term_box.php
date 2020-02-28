<?php
use WilokeListingTools\Framework\Helpers\GetSettings;

function wilcity_render_modern_term_box($oTerm, $taxonomy, $wrapperClass, $aAtts=array()){
    if ( isset($aAtts['image_size']) ){
        if ( strpos($aAtts['image_size'], ',') !== false ){
            $imgSize = explode(',', $aAtts['image_size']);
        }else{
	        $imgSize = $aAtts['image_size'];
        }
    }else{
        $imgSize = 'wilcity_560x300';
    }

	$leftBg = GetSettings::getTermMeta($oTerm->term_id, 'left_gradient_bg');
	$rightBg  = GetSettings::getTermMeta($oTerm->term_id, 'right_gradient_bg');
	$tiltedDegrees  = GetSettings::getTermMeta($oTerm->term_id, 'gradient_tilted_degrees');

	$leftBg = empty($leftBg) ? '#006bf7' : $leftBg;
	$rightBg = empty($rightBg) ? '#f06292' : $rightBg;
	$tiltedDegrees = empty($tiltedDegrees) ? 45 : $tiltedDegrees;
	$totalChildren = GetSettings::countChildren($oTerm);

	if ( $totalChildren !== 0 ){
		$i18 = sprintf(
			_n( '%d Sub category', '%d Sub categories', $totalChildren, 'wilcity-shortcodes' ),
			number_format_i18n($totalChildren)
		);
	}else{
		if ( is_tax() ){
			$totalChildren = get_query_var('taxonomy') == $oTerm->taxonomy ? $oTerm->count : GetSettings::countTermDependsOnCurrentTerm($oTerm->term_id);
		}else{
			$totalChildren = $oTerm->count;
		}

		if ( $totalChildren < 1 ){
			$i18 = esc_html__('0 Listing', 'wilcity-shortcodes');
		}else{
			$i18 = sprintf(
				_n( '%d Listing', '%d Listings', $totalChildren, 'wilcity-shortcodes' ),
				number_format_i18n($totalChildren)
			);
		}
	}
	?>
	<div class="<?php echo esc_attr($wrapperClass); ?>">
		<div class="image-box_module__G53mA">
			<a href="<?php echo esc_url(GetSettings::getTermLink($oTerm)); ?>">
				<header class="image-box_header__1bT-m">
                    <?php if ( !empty($leftBg) && !empty($rightBg) ) : ?>
                    <div class="wil-overlay" style="background-image: linear-gradient(<?php echo esc_attr($tiltedDegrees); ?>deg, <?php echo esc_attr($leftBg) ?> 0%, <?php echo esc_attr($rightBg) ?> 100%)"></div>
                    <?php endif; ?>
                    <?php \WILCITY_SC\SCHelpers::renderLazyLoad(WilokeHelpers::getTermFeaturedImage($oTerm, $imgSize), array(
                        'divClass' => 'image-box_img__mh3A- bg-cover',
                        'imgClass' => 'hidden',
                        'alt' => $oTerm->name
                    )); ?>
				</header>
                <div class="image-box_body__Je8Uw">
                    <h2 class="image-box_title__1PnHo"><?php echo esc_html($oTerm->name); ?></h2><span class="image-box_text__1K_bA"><i class="la la-edit color-primary"></i>
                        <?php echo esc_html($i18); ?>
                    </span>
                </div>
                <?php
                $aPostFeaturedImgs = GetSettings::getPostFeaturedImgsByTerm($oTerm->term_id, $taxonomy);
                if ( !empty($aPostFeaturedImgs) ) :
                ?>
                <div class="image-box_right__17b8t">
                    <?php if ( $totalChildren > 4 ) : ?>
                        <div class="image-box_item__3T3KI"><span class="image-box_count__2ILGP bg-color-primary">+<?php echo esc_attr($totalChildren); ?></span></div>
                    <?php endif; ?>
                    <?php foreach ($aPostFeaturedImgs as $featuredImg) : ?>
                    <div class="image-box_item__3T3KI">
                        <div class="image-box_logo__3NG5m bg-cover" style="background-image: url(<?php echo esc_url($featuredImg); ?>)"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
			</a>
		</div>
	</div>
	<?php
}