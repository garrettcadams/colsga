<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use PayPal\Api\ShippingAddress;
use WilokeListingTools\Framework\Helpers\DebugStatus;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Models\PaymentMetaModel;


trait PayPalConfiguration{
	private $aConfiguration;
	private $oApiContext;
	private $paymentDescription;
	private $maxFailedPayments;
	public $gateway = 'paypal';

	public function setShippingAddress(){
		$aUserInfo  = get_userdata($this->oReceipt->userID);
		$shippingAddress = new ShippingAddress();
		$hasShippingAddressInfo = false;

		if ( isset($aUserInfo['meta']['wiloke_address']) && !empty($aUserInfo['meta']['wiloke_address']) ){
			$shippingAddress->setLine1($aUserInfo['meta']['wiloke_address']);
			$hasShippingAddressInfo = true;
		}

		if ( isset($aUserInfo['meta']['wiloke_city']) && !empty($aUserInfo['meta']['wiloke_city']) ){
			$shippingAddress->setCity($aUserInfo['meta']['wiloke_city']);
			$hasShippingAddressInfo = true;
		}

		if ( isset($aUserInfo['meta']['wiloke_state']) && !empty($aUserInfo['meta']['wiloke_state']) ){
			$shippingAddress->setCity($aUserInfo['meta']['wiloke_state']);
			$hasShippingAddressInfo = true;
		}

		if ( isset($aUserInfo['meta']['wiloke_country']) && !empty($aUserInfo['meta']['wiloke_country']) ){
			$shippingAddress->setPostalCode($aUserInfo['meta']['wiloke_country']);
			$hasShippingAddressInfo = true;
		}

		if ( isset($aUserInfo['meta']['wiloke_zipcode']) && !empty($aUserInfo['meta']['wiloke_zipcode']) ){
			$shippingAddress->setPostalCode($aUserInfo['meta']['wiloke_zipcode']);
			$hasShippingAddressInfo = true;
		}

		return $hasShippingAddressInfo ? $shippingAddress : false;
	}

	public function checkPayPalAPI($mode){
		if ( $mode == 'sandbox' ){
			$clientIDKey = 'paypal_sandbox_client_id';
			$secretKey = 'paypal_sandbox_secret';
		}else{
			$clientIDKey = 'paypal_live_client_id';
			$secretKey = 'paypal_live_secret';
		}

		$msg = esc_html__('The PayPal has not configured yet!', 'wiloke-listing-tools');

		if ( empty($this->aConfiguration[$clientIDKey]) || empty($this->aConfiguration[$secretKey]) ){
			Message::error($msg);
		}
	}

	private function setupConfiguration(){
		$this->aConfiguration = GetWilokeSubmission::getAll();
		$msg = esc_html__('The PayPal has not configured yet!', 'wiloke-listing-tools');
		if ( !GetWilokeSubmission::isGatewaySupported($this->gateway) ){
			Message::error($msg);
		}
		$isDebug = $this->aConfiguration['toggle_debug'] == 'enable';
		$this->checkPayPalAPI($this->aConfiguration['mode']);

		if ( !DebugStatus::status('WP_PAYPAL_FOCUS_LIVE') && $this->aConfiguration['mode'] == 'sandbox' ){

			$this->oApiContext = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					$this->aConfiguration['paypal_sandbox_client_id'],
					$this->aConfiguration['paypal_sandbox_secret']
				)
			);

			$aPayPalConfiguration = array(
				'mode' => 'sandbox',
				'http.CURLOPT_SSLVERSION'=>'CURL_SSLVERSION_TLSv2'
			);

			if ( $isDebug ){
				$aPayPalConfiguration = array_merge($aPayPalConfiguration, array(
					'log.LogEnabled' => true,
					'log.LogLevel'   => 'DEBUG',
					'log.FileName'   => Upload::getFolderDir('wilcity') . GetWilokeSubmission::getField('paypal_logfilename')
				));
			}
			$this->oApiContext->setConfig($aPayPalConfiguration);
		}else{
			$this->oApiContext = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					$this->aConfiguration['paypal_live_client_id'],
					$this->aConfiguration['paypal_live_secret']
				)
			);

			$aPayPalConfiguration = array(
				'mode' => 'live',
				'http.CURLOPT_SSLVERSION'=>'CURL_SSLVERSION_TLSv2'
			);

			$aPayPalConfiguration = array_merge($aPayPalConfiguration, array(
				'log.LogEnabled' => true,
				'log.LogLevel'   => 'ERROR',
				'log.FileName'   => Upload::getFolderDir('wilcity') . GetWilokeSubmission::getField('paypal_logfilename')
			));

			$this->oApiContext->setConfig($aPayPalConfiguration);
		}

		$this->paymentDescription = $this->aConfiguration['paypal_agreement_text'];
		$this->maxFailedPayments = $this->aConfiguration['paypal_maximum_failed'];
	}

	public function getConfiguration($field=''){
		if ( !empty($field) ){
			return $this->aConfiguration[$field];
		}
		return $this->aConfiguration;
	}

	protected function storeTokenAndPlanId($paymentID){
		$storeKey = wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData');
		$id = PaymentMetaModel::set($paymentID, $storeKey, $this->token);

		if ( empty($id) ){
			return false;
		}

		Session::setSession($storeKey, array(
			$this->token => $this->oReceipt->aPlan
		));

		return true;
	}
}
