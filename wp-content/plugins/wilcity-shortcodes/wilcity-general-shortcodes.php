<?php
add_shortcode('wilcity_general_sc_hero_search_form', 'wilcity_general_sc_hero_search_form');

function wilcity_general_sc_hero_search_form($aAtts){
	$aAtts = shortcode_atts(
		array(
			'items'        => '',
			'extra_class'  => ''
		),
		$aAtts
	);

	if ( empty($aAtts['items']) ){
		return '';
	}

	$aAtts['items'] = json_decode(base64_decode($aAtts['items']), true);
	
	foreach($aAtts['items'] as $index => $item) {
		$aAtts['items'][$index]['icon'] = WilokeListingTools\Framework\Helpers\GetSettings::getPostTypeField('icon', $item['post_type']);
	}

	ob_start();
	wilcity_sc_render_hero_search_form($aAtts);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}