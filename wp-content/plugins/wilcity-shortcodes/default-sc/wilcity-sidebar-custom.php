<?php
add_shortcode('wilcity_sidebar_custom_content', 'wilcitySidebarCustomContent');
function wilcitySidebarCustomContent($aArgs){
	global $post;
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'icon'          => 'la la-clock-o',
			'desc'          => '',
			'name'          => 'My Custom Sidebar',
			'content'       => '',
			'key'           => ''
		)
	);
	if ( empty($aAtts['content']) ){
		return '';
	}

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/custom_content', $post, $aAtts);
	}

	ob_start();
	if ( has_action('wilcity/sidebar/custom/'.$aAtts['key']) ){
		do_action('wilcity/sidebar/custom/'.$aAtts['key'], $aAtts);
	}else{
		$wrapperClass = apply_filters('wilcity/filter/class-prefix', 'wilcity-sidebar-item-custom content-box_module__333d9');
		?>
		<div class="<?php echo esc_attr($wrapperClass); ?>">
			<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
			<div class="content-box_body__3tSRB">
				<?php echo do_shortcode($aAtts['content']); ?>
			</div>
		</div>
		<?php
	}

	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}