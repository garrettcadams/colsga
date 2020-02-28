<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Models\PaymentModel;

class IsMyPaymentSession implements InterfaceMiddleware {
	public $msg = '';
	public function handle( array $aOptions ) {
		$this->msg = esc_html__('The payment does not exist', 'wiloke-listing-tools');
		if ( !isset($aOptions['paymentID']) || empty($aOptions['paymentID']) ){
			return false;
		}

		return PaymentModel::isMyPaymentSession($aOptions['userID'], $aOptions['paymentID']);
	}
}