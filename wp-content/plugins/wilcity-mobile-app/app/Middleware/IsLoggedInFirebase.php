<?php
namespace WILCITY_APP\Middleware;

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Frontend\User;

class IsLoggedInFirebase implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		$isEnabled = \WilokeThemeOptions::isEnable('mobile_is_using_firebase_message', false);
		if ( !$isEnabled ){
			return true;
		}
		$firebaseID = User::getFirebaseUserID();

		if ( empty($firebaseID) ){
			$this->msg = esc_html__('There is something wrong! Please try logout and the re-login to the website again', 'wilcity-mobile-app');
			return false;
		}

		return true;
	}
}