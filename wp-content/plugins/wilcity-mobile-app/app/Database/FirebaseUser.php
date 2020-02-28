<?php

namespace WILCITY_APP\Database;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;

class FirebaseUser {
	private static $firebaseIDKey = 'firebase_id';

	private static function buildKey($userID){
		return  'connections/___'.$userID.'___';
	}

	public static function isUserOnlineOnApp($userID){
		$status = FirebaseDB::getDB()->getReference(self::buildKey($userID))->getSnapshot()->getValue();
		return $status ? true : false;
	}

	public static function updateConnectionStatus($userID, $isOnline){
		FirebaseDB::getDB()->getReference('connections')
		          ->update([
			          '___'.$userID.'___' => $isOnline ? true : null
		          ]);
	}

	public static function getFirebaseID($userID=null){
		$userID = empty($userID) ? User::getCurrentUserID() : $userID;
		return GetSettings::getUserMeta($userID, self::$firebaseIDKey);
	}
}