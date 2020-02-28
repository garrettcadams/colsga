<?php
use WilokeListingTools\Framework\Helpers\Repository;

if ( !function_exists('wilokeListingToolsRepository') ){
	function wilokeListingToolsRepository(){
		return new Repository;
	}
}

if ( !function_exists('wilokeLTEnqueueScripts') ){
	add_action('admin_enqueue_scripts', 'wilokeLTEnqueueScripts');
	function wilokeLTEnqueueScripts(){
		wp_enqueue_style('wiloke-listing-tools-general', WILOKE_LISTING_TOOL_URL . 'admin/source/css/general.css', array(), 'wiloke-listing-tools');
		wp_enqueue_script('wiloke-global', WILOKE_LISTING_TOOL_URL . 'admin/source/js/global.js', array('jquery'), null, true);
	}
}

#Admin Nav Bar#
if ( !function_exists('wilcityAdminBar') ){
	function wilcityAdminBar(){
		global $wp_admin_bar;

		$wp_admin_bar->add_menu(
			array(
				'id'    => 'wilcity-support-forum-url',
				'title' => 'Wilcity Support Forum',
				'href'  => 'https://support.wilcity.com',
				'meta'  => array(
					'target' => '_blank'
				)
			)
		);

		$wp_admin_bar->add_menu(
			array(
				'id'    => 'wilcity-documentation',
				'title' => 'Wilcity Online Docs',
				'href'  => 'http://documentation.wilcity.com/',
				'meta'  => array(
					'target' => '_blank'
				)
			)
		);
	}
	add_action( 'admin_bar_menu', 'wilcityAdminBar', 100 );
}
