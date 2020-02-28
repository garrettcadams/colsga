<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsWilokeShortcodeActivated implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		if ( !function_exists('wilcity_render_grid_item') ){
			$this->msg = esc_html__('The Wiloke Shortcode plugin is required. From the admin sidbear, click on Appearance -> Install Plugins to activate it', 'wiloke-listing-tools');
			return false;
		}

		return true;
	}
}