<?php
/*
 * Plugin Name: Wilcity Elementor Addon
 * Plugin URI: https://wilcity.com
 * Author: Wiloke
 * Author URI: https://wiloke.com
 * Version: 1.3.6
 */

add_action('wiloke-listing-tools/run-extension', function(){
	define('WILCITY_EL_PREFIX', 'WILCITY ');
	if ( !class_exists('\WilokeListingTools\Controllers\GuardController') ){
		return false;
	}

	require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
	new \WILCITY_ELEMENTOR\Registers\Init();
});

add_filter('get_comments_number', function($count, $postID){
	$aDirectoryPostTypes = \WilokeListingTools\Framework\Helpers\GetSettings::getAllDirectoryTypes(true);
	$postType = get_post_type($postID);
	if ( $postType == 'event' || !in_array($postType, $aDirectoryPostTypes) ){
		return $count;
	}

	return \WilokeListingTools\Models\ReviewModel::countAllReviewedOfListing($postID);
}, 10, 2);
