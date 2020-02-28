<?php
use WILCITY_SC\SCHelpers;
function wilcity_sc_render_events_grid($atts){
	$atts  = SCHelpers::mergeIsAppRenderingAttr($atts);
	$aArgs = SCHelpers::parseArgs($atts);

	$query = new WP_Query($aArgs);
	if ( !$query->have_posts() ){
		wp_reset_postdata();
		return '';
	}

	$wrap_class	= apply_filters( 'wilcity-el-class', $atts );
	$wrap_class = implode(' ', $wrap_class) . '  ' . $atts['extra_class'];
	$wrap_class .= apply_filters('wilcity/filter/class-prefix', ' wilcity-event-grid wilcity-grid');

	if ( wp_is_mobile() && isset($atts['mobile_img_size']) && !empty($atts['mobile_img_size']) ){
		$atts['img_size'] = $atts['mobile_img_size'];
	}
?>
	<div class="<?php echo esc_attr($wrap_class); ?>" data-col-xs-gap="15" data-col-sm-gap="15" data-col-md-gap="15">
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
				<a class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wil-view-all btn-view-all wil-float-right mb-15')); ?>" href="<?php echo SCHelpers::getViewAllUrl($atts); ?>"><?php echo esc_html($atts['viewmore_btn_name']); ?></a>
			</div>
		<?php endif; ?>
		<div class="row">
            
            <?php
				while($query->have_posts()){
					$query->the_post();
					wilcity_render_event_item($query->post, $atts);
				}
			wp_reset_postdata();
			?>
		</div>
	</div>
<?php
}
