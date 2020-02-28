<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;

function wilcityRenderCheckboxField($aAtts){
	$aAtts = shortcode_atts(
		array(
			'key'         => '',
			'is_grid'     => 'no',
			'is_mobile'   => '',
			'post_id'     => '',
			'description' => '',
			'extra_class' => '',
			'title'       => '',
			'print_unchecked' => 'yes'
		),
		$aAtts
	);

	if ( !empty($aAtts['post_id']) ){
		$post = get_post($aAtts['post_id']);
	}else{
		$post = \WILCITY_SC\SCHelpers::getPost();
	}

	if ( !GetSettings::isPlanAvailableInListing($post->ID, $aAtts['key']) ){
		return '';
	}

	if ( empty($aAtts['key']) || !class_exists('WilokeListingTools\Framework\Helpers\GetSettings') || empty($post)){
		return '';
	}

	$aSettings = General::findField($post->post_type, $aAtts['key']);

	if ( empty($aSettings) ){
		return '';
	}
	$options = $aSettings['fields']['settings']['options'];
	
	if ( !empty($options) ){
		$aOptions = explode(',', $options);
		$aOptions = array_map(function($option){
			return trim($option);
		}, $aOptions);
	}
	if ( empty($aOptions) ){
		return '';
	}
	$aRawValues = GetSettings::getPostMeta($post->ID, 'custom_'.$aAtts['key']);

	if ( empty($aRawValues) ){
		return '';
	}

	$aRawValues = is_array($aRawValues) ? $aRawValues : explode( ',' ,$aRawValues);
	
	$aValues = array();
	
	foreach ($aOptions as $key => $val){
		$aParseValue = explode('|', $val);
		$rawName = $aParseValue[0];
		if ( strpos($rawName, ':') !== false ){
			$aParsed = General::parseCustomSelectOption($val);
			$name = $aParsed['name'];
			$key = $aParsed['key'];
		}else{
			$name = $rawName;
			$key = $rawName;
		}

		if ( (empty($aRawValues) || ( !in_array($key, $aRawValues) && !in_array($val, $aRawValues))) && $aAtts['print_unchecked'] == 'no' ){
			continue;
		}

		$icon = $iconColor = '';
		unset($aParseValue[0]);

		foreach ($aParseValue as $data){
			if ( strpos($data, 'icon_color_') !== false ){
				$iconColor = str_replace('icon_color_', '',$data);
			}else if ( strpos($data, 'icon_') !== false ){
				$icon = str_replace('icon_', '',$data);
				$icon = str_replace('_', ' ', $icon);
			}
		}

		if ( $aAtts['is_mobile'] == 'yes' ){
			$aItem = array(
				'name'  => $name,
				'type'  => 'icon',
				'color' => $iconColor,
				'icon'  => $icon
			);
		}else{
			$aItem = array(
				'name'  => $name,
				'oIcon' => array(
					'type'  => 'icon',
					'color' => $iconColor,
					'icon'  => $icon
				)
			);
		}

		if ( !empty($aRawValues) && ( in_array($key, $aRawValues) || in_array($val, $aRawValues) ) ){
			$aItem['unChecked'] = 'no';
		}else{
			$aItem['unChecked'] = 'yes';
		}

		$aValues[] = $aItem;
	}
	if ( empty($aValues) ){
		return false;
	}

	if ( $aAtts['is_mobile'] == 'yes' || $aAtts['is_grid'] == 'yes' ){
		return json_encode($aValues);
	}

	$class = $aAtts['key'];
	if ( !empty($aAtts['extra_class']) ){
		$class .= ' ' . $aAtts['extra_class'];
	}

	ob_start();
	wilcityListFeaturesSC(array(
		'options' => json_encode($aValues),
		'extra_class' => $class
	));
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

add_shortcode('wilcity_render_checkbox2_field', 'wilcityRenderCheckboxField');