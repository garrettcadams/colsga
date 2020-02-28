<?php
$atts = shortcode_atts(
	array(
		'except_directory_types' => '',
		'items_per_row'          => 3,
		'bg_color'               => '#ffffff'
	),
	$atts
);
$aDirectoryTypeKeys = \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, false);

if ( !empty($atts['except_directory_types']) ){
	$aExceptDirectoryTypes = explode(',', $atts['except_directory_types']);
	$aDirectoryTypeKeys = array_diff($aDirectoryTypeKeys, $aExceptDirectoryTypes);
}

$aDirectoryTypes = \WilokeListingTools\Framework\Helpers\General::getPostTypes(false, false);

$aResponse = array();
foreach ($aDirectoryTypeKeys as $postType){
	$aResponse[] = array(
		'label'             => $aDirectoryTypes[$postType]['name'],
		'iconName'          => $aDirectoryTypes[$postType]['icon'],
		'postType'          => $postType,
		'backgroundColor'   => $aDirectoryTypes[$postType]['bgColor']
	);
}
$atts['items_per_row'] = abs($atts['items_per_row']);

echo '%SC%' . json_encode(
		array(
			'oSettings' => $atts,
			'TYPE'      => 'DIRECTORY_TYPE_BOXES',
			'oResults'  => $aResponse
		)
	) . '%SC%';
return '';
