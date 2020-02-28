<?php
use WILCITY_SC\SCHelpers;
use \WilokeListingTools\Framework\Helpers\GetSettings;

function wilcity_sc_render_grid($atts){
	global $wiloke;
    $atts['border'] = isset($atts['border']) ? $atts['border'] : '';
	$aArgs = SCHelpers::parseArgs($atts);

	if ( $atts['orderby'] !== 'nearbyme' ){
		$query = new WP_Query($aArgs);
		if ( !$query->have_posts() ){
			wp_reset_postdata();
			return '';
		}
    }

	$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($atts);
	if ( SCHelpers::isApp($atts) ){
		$aResponse = array();
		$oSkeleton = new \WILCITY_APP\Helpers\AppHelpers();

		if ( $atts['orderby'] !== 'nearbyme' ){
			while ( $query->have_posts() ){
				$query->the_post();
				$aResponse[] = $oSkeleton->listingSkeleton($query->post, array('oGallery', 'oSocialNetworks', 'oVideos'));
			} wp_reset_postdata();
        }

		echo '%SC%' . json_encode(
				array(
					'oSettings' => $atts,
					'oResults'  => $aResponse,
					'TYPE'      => $atts['TYPE']
				)
			) . '%SC%';
		return '';
	}

	$wrap_class	= apply_filters( 'wilcity-el-class', $atts );

	$wrap_class = implode(' ', $wrap_class) . '  ' . $atts['extra_class'];
	$wrap_class .= apply_filters('wilcity/filter/class-prefix', ' wilcity-grid');

	if ( wp_is_mobile() && isset($atts['mobile_img_size']) && !empty($atts['mobile_img_size']) ){
		$atts['img_size'] = $atts['mobile_img_size'];
	}

	?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
        <?php
        if ( !empty($atts['heading']) || !empty($atts['desc']) ){
	        wilcity_render_heading(array(
		        'TYPE'              => 'HEADING',
		        'blur_mark'         => '',
		        'blur_mark_color'   => '',
		        'heading'           => $atts['heading'],
		        'heading_color'     => $atts['heading_color'],
		        'description'       => $atts['desc'],
		        'description_color' => $atts['desc_color'],
		        'alignment'         => $atts['header_desc_text_align'],
		        'extra_class'       => ''
	        ));
        }
        ?>
        <?php if ( $atts['toggle_viewmore'] == 'enable' ) : ?>
            <div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'btn-view-all-wrap clearfix')); ?>">
                <a class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wil-view-all mb-15 btn-view-all wil-float-right')); ?>" href="<?php echo SCHelpers::getViewAllUrl($atts); ?>"><?php echo esc_html($atts['viewmore_btn_name']); ?></a>
            </div>
        <?php endif; ?>
        <?php
        if ( $atts['orderby'] == 'nearbyme' ){
            $tabID = '';
            if ( !empty($atts['tabname']) ){
                $tabID = strtolower($atts['tabname']);
	            $tabID = str_replace(array('&', 'amp;'), array('', ''), $tabID);

                $tabID = preg_replace_callback('/\s+/', function($aMatched){
                    return '-';
                }, $tabID);
            }

            if( isset( $atts['tab_id'] ) ) {
                $tabID = $atts['tab_id'];
            }

            if (is_tax()) {
                $oQueriedObject = get_queried_object();
                $taxonomy = $oQueriedObject->taxonomy;
                $termID   = $oQueriedObject->term_id;

                if ( $atts['post_type'] == 'depends_on_belongs_to' ){
                    $aDirectoryTypes = GetSettings::getTermMeta($termID, 'belongs_to');
                    if ( empty($aDirectoryTypes) ) {
                        $atts['post_type'] = GetSettings::getDefaultPostType(true);
                    }else{
                        $atts['post_type'] = json_encode($aDirectoryTypes);
                    }
                }

                if (!isset($atts[$taxonomy.'s']) || empty($atts[$taxonomy.'s'])) {
                    $atts[$taxonomy.'s'] = $termID.':'.$oQueriedObject->name;
                }
            }

            ?>
            <div id="wilcity-<?php echo esc_attr($tabID); ?>-nearbyme" class="wilcity-grid-nearbyme">
                <keep-alive>
                    <near-by-me :post-type="'<?php echo esc_attr($atts['post_type']); ?>'" :posts-per-page="'<?php echo esc_attr($atts['posts_per_page']); ?>'" :grid-class="'<?php echo esc_attr($atts['maximum_posts_on_md_screen'] . ' ' . $atts['maximum_posts_on_sm_screen'] . ' ' . $atts['maximum_posts_on_lg_screen']); ?>'" :unit="'<?php echo esc_attr($atts['unit']); ?>'" :radius="'<?php echo esc_attr($atts['radius']); ?>'" :tab-id="'<?php echo esc_attr($tabID); ?>'" :o-ajax-data="'<?php echo base64_encode(serialize($atts)); ?>'" :border-class="'<?php echo esc_attr($atts['border']); ?>'"></near-by-me>
                </keep-alive>
            </div>
            <?php
        }else{ ?>
            <div class="row row-clearfix">
                <?php
                    do_action('wilcity/listing-grid/before-loop', $query, $atts);
                    if ( $query->have_posts() ){
                        $atts['item_class'] = 'mb-30';
                        while($query->have_posts()){
                            $query->the_post();
                            wilcity_render_grid_item($query->post, $atts);
                        }
                        wp_reset_postdata();
                    }
                    do_action('wilcity/listing-grid/after-loop', $query, $atts);
                ?>
                </div>
            <?php
        } ?>
	</div>
	<?php
}
