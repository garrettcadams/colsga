<?php
namespace WilokeListingTools\Framework\Payment\DirectBankTransfer;


use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Message;

trait DirectBankTransferConfiguration {
	protected $aConfiguration;
	public $gateway = 'banktransfer';
	public $aBankAccounts = array();

	public function getBankAccount(){
		$this->aConfiguration = GetWilokeSubmission::getAll();

		for ($i=1; $i<=4; $i++){
			if ( !empty($this->aConfiguration['bank_transfer_account_name_'.$i]) && !empty($this->aConfiguration['bank_transfer_account_number_'.$i]) && !empty($this->aConfiguration['bank_transfer_name_'.$i]) ){
				foreach (array('bank_transfer_account_name', 'bank_transfer_account_number', 'bank_transfer_name', 'bank_transfer_short_code', 'bank_transfer_iban', 'bank_transfer_swift') as $bankInfo){
					$this->aBankAccounts[$i][$bankInfo] = $this->aConfiguration[$bankInfo.'_'.$i];
				}
			}
		}
	}

	private function setupConfiguration(){
		$this->aConfiguration = GetWilokeSubmission::getAll();
		$msg = esc_html__('The Direct Bank Transfer has not configured yet!', 'wiloke-listing-tools');
		if ( !GetWilokeSubmission::isGatewaySupported($this->gateway) ){
			Message::error($msg);
		}

		$this->getBankAccount();
		if ( empty($this->aBankAccounts) ){
			Message::error(esc_html__('You need provide one bank account at least.', 'wiloke-listing-tools'));
		}
	}

	public function getConfiguration($field=''){
		if ( !empty($field) ){
			return $this->aConfiguration[$field];
		}
		return $this->aConfiguration;
	}
}