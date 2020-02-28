<?php
namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsGatewaySupported implements InterfaceMiddleware {
	public $msg;
	public function handle( array $aOptions ) {
		$this->msg = sprintf(esc_html__('OOps! %s gateway is not support by our service', 'wiloke-listing-tools'), $aOptions['gateway']);

		if ( !isset($aOptions['gateway']) || empty($aOptions['gateway']) ){
			return false;
		}

		$aGateways = GetWilokeSubmission::getAllGateways();

		if ( empty($aGateways) ){
			return false;
		}

		if ( !in_array($aOptions['gateway'], $aGateways) ){
			return false;
		}

		return true;
	}
}