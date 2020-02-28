<?php

namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class ValidateGoogleReCaptcha implements InterfaceMiddleware {
	public $msg;

	private function checkAnswer($responsenKey, $secretKey){
		$userIP = $_SERVER['REMOTE_ADDR'];
		$url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responsenKey&remoteip=$userIP";
		$response = file_get_contents($url);
		$response = json_decode($response);
		return $response->success;
	}

	public function handle( array $aOptions ) {
		if ( \WilokeThemeOptions::isEnable('toggle_google_recaptcha') ){
			if ( isset($aOptions['action']) && $aOptions['action'] == 'wilcity_login' ){
				if ( \WilokeThemeOptions::getOptionDetail('using_google_recaptcha_on') != 'both' ){
					return true;
				}
			}

			$siteKey = \WilokeThemeOptions::getOptionDetail('recaptcha_site_key');
			$secretKey = \WilokeThemeOptions::getOptionDetail('recaptcha_secret_key');
			if ( empty($siteKey) || empty($secretKey) ){
				$this->msg = esc_html__('Recaptcha Key is required, please go to Appearance -> Theme Options -> Register And Login to complete this setting', 'wiloke-listing-tools');
				return false;
			}else{
				$this->msg = esc_html__('The reCAPTCHA wasn\'t entered correctly. Please try it again.', 'wiloke-listing-tools');
				if ( !isset($aOptions['g-recaptcha-response']) || empty($aOptions['g-recaptcha-response']) ){
					return false;
				}
				$isvalid = $this->checkAnswer($aOptions['g-recaptcha-response'], $secretKey);
				if ( !$isvalid ){
					return false;
				}
			}
		}

		return true;
	}
}