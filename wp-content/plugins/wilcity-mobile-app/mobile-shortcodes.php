<?php
/*
 * Register Shortcode
 */
if ( !function_exists('wilcityAppRegisterKCSC') ){
	function wilcityAppRegisterKCSC() {
		if (function_exists('kc_add_map'))
		{
			global $kc;
			$aConfigurations = include WILCITY_APP_PATH . 'configs/shortcodes.php';
			$kc->add_map($aConfigurations);
		}
	}
	add_action('init', 'wilcityAppRegisterKCSC', 99 );
}

if ( !function_exists('wilcityAppRegisterTemplatePath') ){
	function wilcityAppRegisterTemplatePath(){
		global $kc;
		if (!function_exists('kc_add_map')){
			return false;
		}
		$kc->set_template_path( WILCITY_APP_PATH . 'kingcomposer-sc/' );
	}
	add_action('init', 'wilcityAppRegisterTemplatePath', 99 );
}
