<?php
$atts = shortcode_atts(
	array(
		'TYPE'              => 'HEADING',
		'blur_mark'         => '',
		'blur_mark_color'   => '',
		'heading'           => '',
		'heading_color'     => '#252c41',
		'description'       => '',
		'bg_color'          => '#ffffff',
		'description_color' => '#70778b',
		'alignment'         => 'wil-text-center',
		'extra_class'       => ''
	),
	$atts
);
$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($atts);
echo '%SC%'.json_encode(\WILCITY_APP\Helpers\AppHelpers::removeUnnecessaryParamOnApp($atts)).'%SC%';