<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class VerifyReview implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$this->msg = esc_html__('The %s is required', 'wiloke-listing-tools');

		if ( empty($aOptions['aData']['title']) ){
			$this->msg = sprintf($this->msg, esc_html__('Review Title', 'wiloke-listing-tools'));
			return false;
		}

		if ( empty($aOptions['aData']['content']) ){
			$this->msg = sprintf($this->msg, esc_html__('Review Title', 'wiloke-listing-tools'));
			return false;
		}

		return true;
	}
}