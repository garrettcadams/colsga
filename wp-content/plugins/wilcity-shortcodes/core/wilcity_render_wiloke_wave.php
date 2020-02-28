<?php
function wilcity_render_wiloke_wave($aAtts){
	$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($aAtts);
	if ( $atts['isApp'] ){
		echo '%SC%' . json_encode($atts) . '%SC%';
		return '';
	}

	$wrapper_class = $atts['extra_class'] . apply_filters('wilcity/filter/class-prefix', ' wilcity-wiloke-wave');
	?>
    <div class="<?php echo esc_attr(trim($wrapper_class)); ?>">
		<?php //wilcity_render_heading($atts); ?>
        <!-- canvas-textbox_module__UVKrB -->
        <div class="canvas-textbox_module__UVKrB">
            <div class="canvas-textbox_content__1WW09">
                <div class="canvas-textbox_textLarge__2JGKN"><?php Wiloke::ksesHTML(str_replace(array('&lt;', '&gt;'), array('<', '>'), $aAtts['heading'])); ?></div>
                <?php if ( !empty($aAtts['description']) ) : ?>
                <div class="canvas-textbox_text__3_SWp"><?php Wiloke::ksesHTML($aAtts['description']); ?></div>
                <?php endif; ?>
                <?php if ( !empty($aAtts['btn_group']) ) : ?>
                <div class="canvas-textbox_btn__ZGIsd">
                    <?php
                    foreach ( $aAtts['btn_group'] as $oBtn ) :
	                    $oBtn = (object)$oBtn;
                    ?>
                    <a class="wil-btn wil-btn--light wil-btn--lg wil-btn--round" target="<?php echo esc_attr($oBtn->open_type); ?>" href="<?php echo esc_url($oBtn->url); ?>">
                        <i class="<?php echo esc_attr($oBtn->icon); ?>"></i> <?php echo esc_html($oBtn->name); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="js-canvas-textbox" data-gradient-left="<?php echo esc_attr($aAtts['left_gradient_color']); ?>" data-gradient-right="<?php echo esc_attr($aAtts['right_gradient_color']); ?>">
                <canvas></canvas>
            </div>
        </div><!-- End / canvas-textbox_module__UVKrB -->
    </div>
    <?php
}