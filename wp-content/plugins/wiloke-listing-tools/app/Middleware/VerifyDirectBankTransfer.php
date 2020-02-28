<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Models\PaymentModel;

class VerifyDirectBankTransfer implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		if ( !isset($aOptions['userID']) || !isset($aOptions['planID']) ){
			$this->msg = esc_html__('The user id and plan id are required', 'wiloke-listing-tools');
			return false;
		}
		$status = PaymentModel::getLastDirectBankTransferStatus($aOptions['userID'], $aOptions['planID']);

		if ( $status == 'processing' || $status == 'pending' ){
			$paymentID = PaymentModel::getLastDirectBankTransferID($aOptions['userID'], $aOptions['planID']);

			$this->msg = esc_html__(sprintf(esc_html__('Please complete the order number %s to continue submitting', 'wiloke-listing-tools')), $paymentID);
			return false;
		}

		return true;
	}
}