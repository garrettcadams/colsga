<?php
use \WilokeListingTools\Framework\Helpers\GetSettings;
add_shortcode('wilcity_sidebar_terms_box', 'wilcitySidebarTermsBox');

function wilcitySidebarTermsBox($aArgs){
	$aArgs['atts'] = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);

	$aArgs = shortcode_atts(
		array(
            'name' => isset($aArgs['name']) ? $aArgs['name'] : $aArgs['atts']['name'],
            'atts' => array(
	            'name'      => '',
	            'icon'      => 'la la-sitemap',
	            'taxonomy'  => 'listing_cat',
	            'postID'    => ''
            )
        ),
		$aArgs
	);

	$aAtts = $aArgs['atts'];

	$aTerms = wp_get_post_terms($aAtts['postID'], $aAtts['taxonomy']);
    if ( empty($aTerms) || is_wp_error($aTerms) ){
        return '';
    }

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/terms_box', $aTerms, $aAtts);
	}

	$wrapperClass = 'content-box_module__333d9 wilcity-sidebar-item-term-box wilcity-sidebar-item-'.$aAtts['taxonomy'];
    ob_start();
	?>
    <div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', $wrapperClass)); ?>">
        <?php wilcityRenderSidebarHeader($aArgs['name'], $aAtts['icon']); ?>
        <div class="content-box_body__3tSRB">
            <div class="row">
                <?php
                foreach ($aTerms as $oTerm) :
                    if ( empty($oTerm) || is_wp_error($oTerm) ){
                        continue;
                    }
                ?>
                <div class="col-sm-6 col-sm-6-clear">
                    <div class="icon-box-1_module__uyg5F two-text-ellipsis mt-20 mt-sm-15">
                        <div class="icon-box-1_block1__bJ25J">
                            <?php echo WilokeHelpers::getTermIcon($oTerm, 'icon-box-1_icon__3V5c0 rounded-circle', true); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
	<?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}