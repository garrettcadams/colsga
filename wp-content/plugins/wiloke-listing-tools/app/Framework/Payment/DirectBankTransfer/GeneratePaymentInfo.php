<?php
namespace WilokeListingTools\Framework\Payment\DirectBankTransfer;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time;

trait GeneratePaymentInfo {
	public function generateTransactionInfo(){
		$oUserInfo = get_userdata(get_current_user_id());

		return array(
			'couponInfo' => $this->oReceipt->aCouponInfo,
			'discount'   => $this->discountPrice,
			'subTotal'   => $this->subTotal,
			'total'      => $this->total,
			'currency'   => $this->currency,
			'tax'        => $this->tax,
			'plan'   => array(
				'ID'    => $this->oReceipt->planID,
				'name'  => get_the_title($this->oReceipt->planID),
				'type'  => get_post_type($this->oReceipt->planID),
				'info'  => GetSettings::getPlanSettings($this->oReceipt->planID)
			),
			'billingType' => $this->getBillingType(),
			'created_gmt_at'  => Time::timestampUTCNow(),
			'created_at'      => Time::timeStampNow(),
			'sessionID'       => $this->paymentID,
			'userInfo'        => array(
				'ID'          => get_current_user_id(),
				'email'       => $oUserInfo->user_email,
				'user_meta'   => $oUserInfo->user_meta,
			)
		);
	}
}