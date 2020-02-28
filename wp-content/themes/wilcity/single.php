<?php
$aDefaultDirectoryTypes = \WilokeListingTools\Framework\Helpers\General::getPostTypes(false);
$aKeys = array_keys($aDefaultDirectoryTypes);

if ( is_singular('elementor_library') ){
	get_template_part('templates/page-builder');
}else if ( is_singular($aKeys) ){
	get_template_part('post-types/listing');
}else{
	get_template_part('post-types/post');
}