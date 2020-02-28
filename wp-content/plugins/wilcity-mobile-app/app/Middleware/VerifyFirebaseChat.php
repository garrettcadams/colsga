<?php

namespace WILCITY_APP\Middleware;


use WILCITY_APP\Database\FirebaseDB;
use WILCITY_APP\Database\FirebaseUser;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Frontend\User;

class VerifyFirebaseChat implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		if ( !isset($aOptions['receiveID']) || empty($aOptions['receiveID']) ){
			$this->msg = esc_html__('The receiver ID is required.', 'wilcity-mobile-app');
			return false;
		}

		if ( !isset($aOptions['oChatRef']) || !is_object($aOptions['oChatRef']) ){
			$this->msg = esc_html__('You need to build Chat reference object first.', 'wilcity-mobile-app');
			return false;
		}

		$receiverFirebaseID = FirebaseDB::getFirebaseID($aOptions['receiveID']);
		if ( empty($receiverFirebaseID) ){
			$oReceiver = new \WP_User($aOptions['receiveID']);
			do_action('wilcity/create-firebase-account', $oReceiver->data->user_email, $oReceiver->data->user_pass);

			$receiverFirebaseID = User::getFirebaseID($aOptions['receiveID']);
			if ( empty($receiverFirebaseID) ){
				$this->msg = esc_html__('We could not create your Firebase account', 'wilcity-mobile-app');
				return false;
			}
		}

		try{
			$firstVal = $aOptions['oChatRef']->getChild('fUser')->getSnapshot()->getValue();
			if ( !empty($firstVal) ){
				$firebaseID = FirebaseDB::getFirebaseID();
				if ( $firebaseID == $firstVal ){
					return true;
				}else{
					$secondVal = $aOptions['oChatRef']->getChild('sUser')->getSnapshot()->getValue();
					if ( $secondVal == $firebaseID ){
						return true;
					}
				}

				$this->msg = esc_html__('You do not have permission to access this chat room', 'wilcity-mobile-app');
				return false;
			}
			return true;
		}catch (\Exception $oE){
			return true;
		}
	}
}