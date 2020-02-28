<?php

namespace WILCITY_APP\Database;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;

class FirebaseDeviceToken {
	private static function buildKey($userID){
		return 'deviceTokens/___'.$userID.'___';
	}

	public static function getDeviceToken($userID, $firebaseID){
		try{
			$aValue = FirebaseDB::getDB()->getReference(self::buildKey($userID))->getSnapshot()->getValue();
			return isset($aValue['firebaseID']) && $aValue['firebaseID'] == $firebaseID ? $aValue['token'] : false;
		}catch (\Exception $oE){
			return false;
		}
	}

	public static function getNotificationStatus($userID, $notificationKey){
		return FirebaseDB::getDB()->getReference(self::buildKey($userID).'/pushNotificationSettings/'.$notificationKey)->getSnapshot()->getValue() == true;
	}
}
