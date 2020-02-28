<?php
global $kc;
$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($atts);

if ( !$atts['isApp'] ){
	include $kc->get_template_path() . 'kc_row.php';
}else{
	echo do_shortcode($content);
}