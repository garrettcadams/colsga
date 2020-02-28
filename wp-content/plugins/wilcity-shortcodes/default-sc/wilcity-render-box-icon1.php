<?php
function wilcityRenderBoxIcon1($aAtts){
	$aAtts = shortcode_atts(
		array(
			'icon'      => 'la la-leaf',
			'margin'    => 'mb-10',
            'icon_style'=> 'rounded-circle',
            'name'      => '',
            'link'      => '',
            'color'     => ''
		),
		$aAtts
	);

	ob_start();
	?>
	<div class="icon-box-1_module__uyg5F <?php echo esc_attr($aAtts['margin']); ?>">
		<div class="icon-box-1_block1__bJ25J"><?php if ( !empty($aAtts['icon']) ) : ?><?php if ( empty($aAtts['color']) ) : ?><div class="icon-box-1_icon__3V5c0 <?php echo esc_attr($aAtts['icon_style']); ?>"><i class="<?php echo esc_attr($aAtts['icon']); ?>"></i></div><?php else: ?><div style="color: <?php echo esc_attr($aAtts['color']); ?>" class="icon-box-1_icon__3V5c0 <?php echo esc_attr($aAtts['icon_style']); ?>"><i class="<?php echo esc_attr($aAtts['icon']); ?>"></i></div><?php endif; ?><?php endif; ?>
            <div class="icon-box-1_text__3R39g">
                <?php if ( !empty($aAtts['link']) ): ?>
                    <a href="<?php echo esc_url($aAtts['link']); ?>"><?php echo esc_html($aAtts['name']); ?></a>
                <?php else: ?>
                    <?php echo esc_html($aAtts['name']); ?>
                <?php endif; ?>
            </div>
        </div>
	</div>

	<?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

add_shortcode('wilcity_render_box_icon1', 'wilcityRenderBoxIcon1');