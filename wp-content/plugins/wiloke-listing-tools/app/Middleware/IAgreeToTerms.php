<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IAgreeToTerms implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$aThemeOptions = \Wiloke::getThemeOptions(true);
		if ( !isset($aThemeOptions['toggle_terms_and_conditionals']) || $aThemeOptions['toggle_terms_and_conditionals'] == 'disable' ){
			return true;
		}

		$this->msg = esc_html__('Sorry, You have to agree to our terms.', 'wiloke-listing-tools');

		if ( !isset($aOptions['agreeToTerms']) || $aOptions['agreeToTerms'] == 'no' ){
			return false;
		}

		return true;
	}
}