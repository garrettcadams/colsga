<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;

class VerifyPurchaseCode {
	private $wilcityService = 'https://wilcityservice.com/';
	private static $purchaseCodeStatusKey = 'purchase_code_status';
	private static $purchaseCodeKey = 'purchase_code';
	private $invalidPurchaseCodeFrom = 'invalid_purchase_code_from';

	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'verifyPurchaseCodeScripts'));
		add_action('wp_ajax_wiloke_verify_purchase_code', array($this, 'verifyNow'));
		add_action('wilcity_daily_events', array($this, 'reCheckPurchaseDaily'));
		add_action('wp_ajax_wiloke_revoke_purchase_code', array($this, 'revokeLicense'));
	}

	private function isAdmin(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error(
				array(
					'msg' => 'You do not have permission to access this page'
				)
			);
		}
	}

	public function revokeLicense(){
		$this->isAdmin();
		$purchasedCode = GetSettings::getOptions(self::$purchaseCodeKey);

		$oResponse = wp_remote_post( $this->wilcityService, array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => array(
					'action'    => 'revoke_license',
					'website'   => home_url('/'),
					'email'     => get_option('admin_email'),
					'purchasedCode' => $purchasedCode
				),
				'cookies' => array()
			)
		);

		if ( !is_wp_error($oResponse) ){
			SetSettings::deleteOption(self::$purchaseCodeStatusKey);
			SetSettings::deleteOption(self::$purchaseCodeKey);
			SetSettings::setOptions($this->invalidPurchaseCodeFrom, time());
		}

		wp_send_json_success();
	}

	public static function isActivating(){
		return (GetSettings::getOptions(self::$purchaseCodeStatusKey) == 'active');
	}

	public static function showLessPurchaseCode(){
		$purchaseCode = GetSettings::getOptions(self::$purchaseCodeKey);
		return substr($purchaseCode,0, 3) . '***' . substr($purchaseCode, 8, -1);
	}

	private function machineVerifyPurchaseCode($purchasedCode){
		$oResponse = wp_remote_post( $this->wilcityService, array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => array(
					'action'    => 'wilcity_verify_license',
					'website'   => home_url('/'),
					'email'     => get_option('admin_email'),
					'purchasedCode' => $purchasedCode
				),
				'cookies' => array()
			)
		);

		return $oResponse;
	}

	private function setInvalidPurchaseCodeFrom(){
		$from = GetSettings::getOptions($this->invalidPurchaseCodeFrom);
		if ( empty($from) ){
			SetSettings::setOptions($this->invalidPurchaseCodeFrom, time());
		}
	}

	public function reCheckPurchaseDaily(){
		$purchasedCode = GetSettings::getOptions(self::$purchaseCodeKey);
		if ( empty($purchasedCode) ){
			SetSettings::deleteOption(self::$purchaseCodeStatusKey);
			$this->setInvalidPurchaseCodeFrom();
			return false;
		}

		$oResponse = $this->machineVerifyPurchaseCode($purchasedCode);

		if ( is_wp_error($oResponse) ){
			SetSettings::deleteOption(self::$purchaseCodeStatusKey);
			$this->setInvalidPurchaseCodeFrom();
			return false;
		}

		return true;
	}

	public function verifyNow(){
		$this->isAdmin();

		if ( !isset($_POST['purchasedCode']) || empty($_POST['purchasedCode']) ){
			wp_send_json_error(
				array(
					'msg' => 'The purchase code is required'
				)
			);
		}

		$purchasedCode = trim(sanitize_text_field($_POST['purchasedCode']));

		$oResponse = $this->machineVerifyPurchaseCode($purchasedCode);

		if ( is_wp_error( $oResponse ) ) {
			$errMsg = $oResponse->get_error_message();
			$this->setInvalidPurchaseCodeFrom();
			wp_send_json_error(array(
				'msg' => $errMsg
			));
		} else {
			SetSettings::setOptions(self::$purchaseCodeKey, sanitize_text_field($purchasedCode));
			SetSettings::setOptions(self::$purchaseCodeStatusKey, 'active');
			SetSettings::deleteOption($this->invalidPurchaseCodeFrom);

			wp_send_json_success(array(
				'msg' => 'Congrats! Your License has been activated successfully.'
			));
		}
	}

	public function verifyPurchaseCodeScripts(){
		if ( !isset($_GET['page']) == 'wiloke-listing-tools' ){
			return false;
		}

		wp_enqueue_script('wilcity-verify-purchase-code', WILOKE_LISTING_TOOL_URL . 'admin/source/js/verify-purchase-code.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
	}
}