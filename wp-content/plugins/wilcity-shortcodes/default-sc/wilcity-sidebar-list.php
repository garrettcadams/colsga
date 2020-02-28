<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Controllers\SearchFormController;

add_shortcode('wilcity_sidebar_list', 'wilcityRenderSidebarList');
function wilcityRenderSidebarList($aArgs){
	global $post;
	$aAtts = is_array($aArgs['atts']) ? $aArgs['atts'] : \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'      => '',
			'style'     => 'list',
			'icon'      => 'la la-clock-o',
			'desc'      => '',
			'aArgs'     => '',
			'aMetaData' => array('rating', 'address')
		)
	);

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/list', '', $post, $aAtts);
	}

	$query = new WP_Query($aAtts['aArgs']);

	if ( !$query->have_posts() ){
		wp_reset_postdata();
		return '';
	}
	$size = apply_filters('wilcity/listing-sidebar-list/image/size', 'thumbnail');
	ob_start();
	?>
	<div class="wil-single-sidebar-list-wrapper content-box_module__333d9">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
		<div class="content-box_body__3tSRB">
			<div class="row row-fix-10">
                <?php
                    while ($query->have_posts()){
                        $query->the_post();
	                    SingleListing::setListingPromotionShownUp($query->post->ID);
                        ?>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="widget-listing2_module__2KKG0 widget-listing2_list__1y7xK">
                                <a href="<?php echo esc_url(get_permalink($query->post->ID)); ?>">
                                    <div class="widget-listing2_container__2auAC">
                                        <div class="widget-listing2_thumb__1GXhh bg-cover" style="background-image: url(<?php echo esc_url(GetSettings::getFeaturedImg($query->post->ID, $size)); ?>)"></div>
                                        <div class="widget-listing2_content__3pCvW">
                                            <h3 class="widget-listing2_title__2UZx9"><?php echo get_the_title($query->post->ID); ?></h3>
                                            <div class="widget-listing2_metaData__2XG_K">
                                                <?php
                                                \WILCITY_SC\SCHelpers::renderSidebarMetaData($query->post, $aAtts);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                ?>
			</div>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}