<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use WilokeListingTools\Framework\Payment\SuspendInterface;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;

class PayPalSuspendPlan implements SuspendInterface {
	use PayPalConfiguration;
	private $description;
	private $paymentID;
	private $agreementID;

	public function setPaymentID($paymentID){
		$this->paymentID = $paymentID;
	}

	public function suspend() {
		$this->setupConfiguration();
		$this->suspendDescrition = esc_html__('Suspended the Agreement', 'wiloke-listing-tools');
		$status = PaymentModel::getField('status', $this->paymentID);
		$this->description = esc_html__('Suspended the Agreement', 'wiloke-listing-tools');

		if ( $status !== 'active' ){
			return true;
		}

		$this->agreementID = PaymentMetaModel::get($this->paymentID, wilokeListingToolsRepository()->get('addlisting:paypalAgreementID'));

		if ( empty($this->agreementID) ){
			return true;
		}

		$agreementStateDescriptor = new AgreementStateDescriptor();
		$agreementStateDescriptor->setNote($this->description);
		$createdAgreement = null;

		try{
			$oAgreementInfo = Agreement::get($this->agreementID, $this->oApiContext);
		}catch (\Exception $oE){
			return false;
		}
		try{
			$oAgreementInfo->suspend($agreementStateDescriptor, $this->oApiContext);
			$this->oNewAgreement = Agreement::get($this->agreementID, $this->oApiContext);
			PaymentModel::updatePaymentStatus('suspended', $this->paymentID);
			return true;
		}catch (\Exception $oEx){
			return false;
		}
	}
}