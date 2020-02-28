<?php
namespace WILCITY_APP\Database;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use WilokeListingTools\Framework\Helpers\Firebase;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;

class FirebaseDB {
	private static $db;
	private static $auth;
	private static $serviceAccount;
	private static $firebaseIDKey = 'firebase_id';

	public static function getDB(){
		if ( self::$db ){
			return self::$db;
		}

		self::_connect();
		return self::$db;
	}

	private static function _auth(){
		$serviceAccount = ServiceAccount::fromJsonFile(Upload::getFolderDir('wilcity') . 'firebaseConfig.json');

		$firebase = (new Factory)
			->withServiceAccount($serviceAccount)
			->create();
		self::$auth = $firebase->getAuth();
	}

	private static function _connect(){
		$serviceAccount = ServiceAccount::fromJsonFile(Upload::getFolderDir('wilcity') . 'firebaseConfig.json');
		$firebase = (new Factory)
			->withServiceAccount($serviceAccount)
			// The following line is optional if the project id in your credentials file
			// is identical to the subdomain of your Firebase project. If you need it,
			// make sure to replace the URL with the URL of your project.
			->withDatabaseUri(Firebase::getFirebaseField('databaseURL'))
			->create();
		self::$db = $firebase->getDatabase();
	}

	public static function getFirebaseID($userID=null){
		$userID = empty($userID) ? User::getCurrentUserID() : $userID;
		return GetSettings::getUserMeta($userID, self::$firebaseIDKey);
	}

	public static function setFirebaseID($firebaseID, $userID=null){
		$userID = empty($userID) ? User::getCurrentUserID() : $userID;
		SetSettings::setUserMeta($userID, self::$firebaseIDKey, $firebaseID);
	}

	public static function getAuth(){
		if ( self::$auth ){
			return self::$auth;
		}

		self::_auth();
		return self::$auth;
	}
}

