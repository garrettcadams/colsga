<?php
use  \WilokeListingTools\Framework\Helpers\GetSettings;

function wilcity_render_term_box($oTerm, $wrapperClass, $aAtts=array()){
    $aTermIcon = WilokeHelpers::getTermOriginalIcon($oTerm);
    $innerClass = $aAtts['toggle_box_gradient']  == 'enable' ?  'textbox-1_style2__cPkly textbox-1_module__bn5-O' : 'textbox-1_module__bn5-O bg-color-primary--hover';

	$featuredImgId = GetSettings::getTermMeta($oTerm->term_id, 'featured_image_id');
	if (!empty($featuredImgId)){
	    if (  $aAtts['toggle_box_gradient'] == 'enable' ){
	        $imgSize = 'wilcity_360x300';
        }else{
	        $imgSize = 'medium';
        }
		$featuredImg = wp_get_attachment_image_url($featuredImgId, $imgSize);
	}else{
		$featuredImg = GetSettings::getTermMeta($oTerm->term_id, 'featured_image');
	}
 	?>
	<div class="<?php echo esc_attr($wrapperClass); ?>">
		<div class="<?php echo esc_attr($innerClass); ?>">
            <?php if ( $aAtts['toggle_box_gradient'] == 'enable' ):
                $leftBg = GetSettings::getTermMeta($oTerm->term_id, 'left_gradient_bg');
                $rightBg  = GetSettings::getTermMeta($oTerm->term_id, 'right_gradient_bg');
                $tiltedDegrees  = GetSettings::getTermMeta($oTerm->term_id, 'gradient_tilted_degrees');

                $leftBg = empty($leftBg) ? '#006bf7' : $leftBg;
                $rightBg = empty($rightBg) ? '#f06292' : $rightBg;
                $tiltedDegrees = empty($tiltedDegrees) ? -10 : $tiltedDegrees;
            ?>
                <div class="wil-overlay" style="background-image: linear-gradient(<?php echo esc_attr($tiltedDegrees); ?>deg, <?php echo esc_attr($leftBg) ?> 0%, <?php echo esc_attr($rightBg) ?> 100%)"></div>
                <a href="<?php echo esc_url(GetSettings::getTermLink($oTerm)); ?>" class="bg-cover" style="background-image: url(<?php echo esc_url($featuredImg); ?>)">
	                <?php if ( empty($aTermIcon) ) : ?>
                        <div class="textbox-1_icon__3wBDQ" style="color: #e45b5b"><i class="la la-heart-o"></i></div>
	                <?php else: ?>
		                <?php if ( $aTermIcon['type'] == 'image' ) : ?>
                            <div class="textbox-1_icon__3wBDQ">
                                <img src="<?php echo esc_url($aTermIcon['url']); ?>" alt="<?php echo esc_attr($oTerm->name); ?>"></div>
		                <?php else: ?>
                            <div class="textbox-1_icon__3wBDQ" style="color: <?php echo esc_attr($aTermIcon['color']); ?>;"><i class="<?php echo esc_attr($aTermIcon['icon']); ?>"></i></div>
		                <?php endif; ?>
	                <?php endif; ?>
            <?php else: ?>
                <a href="<?php echo esc_url(GetSettings::getTermLink($oTerm)); ?>">
                    <div class="textbox-1_icon__3wBDQ bg-cover" style="background-image: url('<?php echo esc_url($featuredImg); ?>')">
                        <div class="wil-overlay"></div>
	                    <?php if ( $aTermIcon['type'] == 'image' ) : ?>
                            <img src="<?php echo esc_url($aTermIcon['url']); ?>" alt="<?php echo esc_attr($oTerm->name); ?>">
	                    <?php else: ?>
                            <i class="<?php echo esc_attr($aTermIcon['icon']); ?>"></i>
	                    <?php endif; ?>
                    </div>
            <?php endif; ?>
                <?php
                $totalChildren = GetSettings::countChildren($oTerm);
                if ( $totalChildren !== 0 ){
	                $i18 = sprintf(
		                _n( '%d Sub category', '%d Sub categories', $totalChildren, 'wilcity-shortcodes' ),
		                $totalChildren
	                );
                }else{
	                if ( is_tax() ){
		                $totalListings = get_query_var('taxonomy') == $oTerm->taxonomy ? $oTerm->count : GetSettings::countTermDependsOnCurrentTerm($oTerm->term_id);
	                }else{
		                $totalListings = $oTerm->count;
	                }

	                if ( $totalListings < 1 ){
		                $i18 = esc_html__('0 Listing', 'wilcity-shortcodes');
	                }else{
		                $i18 = sprintf(
			                _n( '%d Listing', '%d Listings', $totalListings, 'wilcity-shortcodes' ),
			                $totalListings
		                );
	                }
                }
                ?>
				<div class="textbox-1_content__3IRq1">
					<span class="textbox-1_text__5g4er"><?php echo esc_html($oTerm->name); ?></span>
					<h3 class="textbox-1_title__Tf1Gy"><?php echo esc_html($i18); ?></h3>
					<span class="textbox-1_arrow__38itC"><i class="la la-long-arrow-right"></i></span>
				</div>
			</a>
		</div>
	</div>
	<?php
}