<?php
function wilcity_render_box_icon($aAtts){
	$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($aAtts);

	if ( $atts['isApp'] ){
		echo '%SC%'.json_encode($atts).'%SC%'; return '';
	}

//	$wrapper_class = $atts['extra_class'] . ' wilcity-box-icon';
    ?>
    <div class="textbox-2_module__15Zpj">
        <div class="textbox-2_icon__1xt9q color-primary"><i class="<?php echo esc_attr($aAtts['icon']); ?>"></i></div>
        <h3 class="textbox-2_title__301U3"><?php Wiloke::ksesHTML($aAtts['heading']); ?></h3>
        <div class="textbox-2_content__qS8li"><?php Wiloke::ksesHTML($aAtts['description']); ?></div>
    </div>
    <?php
}