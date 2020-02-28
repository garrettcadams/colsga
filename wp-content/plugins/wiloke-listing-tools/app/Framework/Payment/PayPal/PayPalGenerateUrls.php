<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

trait PayPalGenerateUrls{
	public function thankyouUrl(){
		if ( empty($this->oReceipt->thankyouUrl) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The thankyou page is required.', 'wiloke-listing-tools')
				)
			);
		}

		$this->thankyouUrl = add_query_arg(
			array(
				'billingType' => $this->getBillingType(),
				'planID'      => $this->oReceipt->aPlan['ID']
			),
			$this->oReceipt->thankyouUrl
		);
		return urlencode($this->thankyouUrl);
	}

	public function cancelUrl(){
		if ( empty($this->oReceipt->cancelUrl) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The cancel page is required. Please go to Wiloke Submission -> Cancel Page to setup it.', 'wiloke-listing-tools')
				)
			);
		}
		$this->cancelUrl = add_query_arg(
			array(
				'billingType' => $this->getBillingType(),
				'planID'      => $this->oReceipt->aPlan['ID']
			),
			$this->oReceipt->cancelUrl
		);
	}

}