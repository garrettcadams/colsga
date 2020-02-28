<?php
$atts = shortcode_atts(
	array(
		'TYPE'              => 'HERO',
		'heading'           => '',
		'heading_color'     => '',
		'description'       => '',
		'description_color' => '',
		'bg_type'           => 'image',
		'overlay_color'     => '',
		'image_bg'          => '',
		'slider_bg'         => '',
		'bg_color'          => '#ffffff',
		'extra_class'       => ''
	),
	$atts
);

$aAtts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($atts);

if ( empty($aAtts['overlay_color']) ){
	unset($aAtts['overlay_color']);
}

if ( empty($aAtts['heading_color']) ){
	unset($aAtts['heading_color']);
}

if ( empty($aAtts['description_color']) ){
	unset($aAtts['description_color']);
}

echo '%SC%' . json_encode(\WILCITY_APP\Helpers\AppHelpers::removeUnnecessaryParamOnApp($aAtts)) . '%SC%';