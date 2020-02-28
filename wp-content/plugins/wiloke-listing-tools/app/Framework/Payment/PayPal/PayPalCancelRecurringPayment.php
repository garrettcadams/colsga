<?php
namespace WilokeListingTools\Framework\Payment\PayPal;


use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class PayPalCancelRecurringPayment{
	use PayPalGenerateUrls;
	use PayPalConfiguration;

	protected $agreementID;
	protected $setNote;

	public function getAgreementIDByPaymentID($paymentID){
		$this->agreementID = PaymentMetaModel::get($paymentID, wilokeListingToolsRepository()->get('payment:paypalAgreementID'));

		if ( empty($this->agreementID) ){
			Message::error(esc_html__('The payment id does not exist', 'wiloke-listing-tools'));
		}

		return $this;
	}


	public function execute($paymentID){

		$this->getAgreementIDByPaymentID($paymentID);
		$this->setupConfiguration();

		$oAgreement = new Agreement();
		$oAgreement->setId($this->agreementID);

		$oAgreementStateDescriptor = new AgreementStateDescriptor();
		$oAgreementStateDescriptor->setNote(esc_html__('Cancel the agreement', 'wiloke-listing-tools'));

		try{
			$oAgreement->cancel($oAgreementStateDescriptor, $this->oApiContext);
			$cancelAgreementDetails = Agreement::get($oAgreement->getId(), $this->oApiContext);

			PaymentModel::updatePaymentStatus('cancelled', $paymentID);

			return array(
				'status' => 'success',
				'msg'    => $cancelAgreementDetails
			);
		}catch (\Exception $oE){
			return array(
				'status' => 'error',
				'msg'    => $oE->getMessage()
			);
		}
	}
}