<?php
function wilcity_render_heading($aAtts){
	$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($aAtts);
	if ( $atts['isApp'] ){
		echo '%SC%'.json_encode(\WILCITY_SC\SCHelpers::removeUnnecessaryParamOnApp($atts)).'%SC%'; return '';
	}

	$wrapper_class = 'heading_module__156eJ ' . $aAtts['alignment'] . ' ' . $aAtts['extra_class'];
    ?>
    <!-- heading_module__156eJ -->
    <div class="<?php echo esc_attr($wrapper_class); ?>">
        <?php if ( !empty($aAtts['blur_mark']) ) : ?>
            <?php if ( !empty($aAtts['blur_mark_color']) ) : ?>
                <h3 class="heading_mask__pcO5T" style="color: <?php echo esc_attr($aAtts['blur_mark_color']); ?>"><?php Wiloke::ksesHTML($aAtts['blur_mark']); ?></h3>
            <?php else: ?>
                <h3 class="heading_mask__pcO5T"><?php Wiloke::ksesHTML($aAtts['blur_mark']); ?></h3>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ( !empty($aAtts['heading']) ) : ?>
        <h2 class="heading_title__1bzno" <?php if ( !empty($aAtts['heading_color']) ) : ?>style="color:<?php echo esc_attr($aAtts['heading_color']); ?>"<?php endif; ?>><?php Wiloke::ksesHTML($aAtts['heading']); ?></h2>
        <?php endif; ?>

        <?php if ( !empty($aAtts['description']) ) : ?>
        <div class="heading_content__2mtYE" <?php if ( !empty($aAtts['description_color']) ) : ?>style="color:<?php echo esc_attr($aAtts['description_color']); ?>"<?php endif; ?>><?php Wiloke::ksesHTML($aAtts['description']); ?></div>
        <?php endif; ?>
    </div><!-- End / heading_module__156eJ -->
    <?php
}