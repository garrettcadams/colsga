<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IAgreeToPrivacyPolicy implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$aThemeOptions = \Wiloke::getThemeOptions(true);
		if ( !isset($aThemeOptions['toggle_privacy_policy']) || $aThemeOptions['toggle_privacy_policy'] == 'disable' ){
			return true;
		}

		$this->msg = esc_html__('Sorry, You have to agree to privacy policy.', 'wiloke-listing-tools');

		if ( !isset($aOptions['agreeToPrivacyPolicy']) || $aOptions['agreeToPrivacyPolicy'] == 'no' ){
			return false;
		}

		return true;
	}
}