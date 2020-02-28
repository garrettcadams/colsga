<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsThemeOptionSupport implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$aThemeOptions = \Wiloke::getThemeOptions();

		if ( !isset($aOptions['feature']) || !isset($aThemeOptions[$aOptions['feature']]) || $aThemeOptions[$aOptions['feature']] == 'disable' ){
			$this->msg = sprintf(esc_html__('The %s is not supported by the theme', 'wiloke-listing-tools'), $aOptions['feature']);
			return false;
		}
		return true;
	}
}