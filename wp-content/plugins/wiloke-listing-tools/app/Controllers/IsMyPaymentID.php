<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsMyPaymentID implements InterfaceMiddleware {
	public $msg = '';

	public function handle( array $aOptions ) {
		if ( !isset($aOptions['userID']) || !isset($aOptions['paymentID']) || empty($aOptions['userID']) || empty($aOptions['userID']) ){
			$this->msg = esc_html__('The payment might have been deleted.', 'wiloke-listing-tools');
			return false;
		}

		$paymentID = PaymentModel::getField('ID', $aOptions['paymentID']);
		if ( empty($paymentID) ){
			$this->msg = esc_html__('The payment session does not exists.', 'wiloke-listing-tools');
			return false;
		}
		return true;
	}
}