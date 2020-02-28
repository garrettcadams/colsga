<?php

namespace WilokeListingTools\Framework\Helpers;


use WILCITY_APP\Database\FirebaseDeviceToken;
use WILCITY_APP\Database\FirebaseMsgDB;
use WILCITY_APP\Database\FirebaseUser;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;

class Firebase {
	private static $filebaseFileDir = null;
	private static $firebaseDBURL = null;
	private static $aConfiguration = null;
	private static $isAdminEnablingNotifications = null;
	private static $aCustomerSettings = null;
	private static $firebaseID;
	private static $deviceToken;
	private static $msg;
	private static $aAdminSettings = null;

	public static function resetInfo(){
		self::$msg = '';
		self::$firebaseID = '';
		self::$deviceToken = '';
	}

	public static function focusGetDeviceToken($userID){
		self::$firebaseID = User::getFirebaseID($userID);

		if ( empty(self::$firebaseID) ){
			return false;
		}

		self::$deviceToken = FirebaseDeviceToken::getDeviceToken($userID, self::$firebaseID);
		return empty(self::$deviceToken) ? false : self::$deviceToken;
	}

	public static function getMessage(){
		return self::$msg;
	}

	public static function getDeviceToken(){
		return self::$deviceToken;
	}

	public static function getFirebaseID(){
		return self::$firebaseID;
	}

	private static function isCustomersReceiveNotifications(){
		if ( self::$isAdminEnablingNotifications === null ){
			self::$isAdminEnablingNotifications = GetSettings::getOptions('toggle_customers_receive_notifications') == 'enable';
		}
		return self::$isAdminEnablingNotifications;
	}

	private static function isEnableNotificationStatusOnDevice($userID, $key){
		if ( !self::toggleReceiveNotifications($userID) ){
			return false;
		}
		return FirebaseMsgDB::getNotificationStatus($userID, $key);
	}

	private static function getAdminSettings(){
		if ( self::$aAdminSettings !== null ){
			return self::$aAdminSettings;
		}

		if ( empty(self::$aAdminSettings) ){
			self::$aAdminSettings = GetSettings::getOptions('admin_receive_notifications_settings');
			return self::$aAdminSettings;
		}

		if ( empty(self::$aAdminSettings) ){
			self::$aAdminSettings = array();
		}

		return self::$aAdminSettings;
	}

	private static function getCustomerSettings(){
		if ( self::$aCustomerSettings !== null ){
			return self::$aCustomerSettings;
		}

		if ( empty(self::$aCustomerSettings) ){
			self::$aCustomerSettings = GetSettings::getOptions('customers_receive_notifications_settings');
			return self::$aCustomerSettings;
		}

		if ( empty(self::$aCustomerSettings) ){
			return false;
		}
		return self::$aCustomerSettings;
	}

	private static function isAdminReceiveNotifications(){
		return GetSettings::getOptions('toggle_admin_receive_notifications') == 'enable';
	}

	public static function getAdminMsg($key){
		self::getAdminSettings();
		return self::$aAdminSettings[$key] && isset(self::$aAdminSettings[$key]['msg']) ? self::$aAdminSettings[$key]['msg'] : '';
	}

	public static function isAdminEnable($key){
		if ( !self::isAdminReceiveNotifications() ){
			return false;
		}
		self::getAdminSettings();

		$status = isset(self::$aAdminSettings[$key]) && isset(self::$aAdminSettings[$key]['status']) && self::$aAdminSettings[$key]['status'] == 'on';

		if ( !$status ){
			return false;
		}

		self::$msg = self::getAdminMsg($key);
		if ( empty(self::$msg) ){
			return false;
		}

		return true;
	}

	private static function toggleReceiveNotifications($userID){
		return FirebaseMsgDB::getNotificationStatus($userID, 'toggleAll');
	}

	public static function getCustomerMsg($key){
		self::getCustomerSettings();
		return self::$aCustomerSettings[$key] && isset(self::$aCustomerSettings[$key]['msg']) ? self::$aCustomerSettings[$key]['msg'] : '';
	}

	public static function isCustomerEnable($key, $userID=null){
		if ( !self::isCustomersReceiveNotifications() ){
			return false;
		}
		self::getCustomerSettings();
		$status = isset(self::$aCustomerSettings[$key]) && isset(self::$aCustomerSettings[$key]['status']) && self::$aCustomerSettings[$key]['status'] == 'on';

		if ( !$status ){
			return false;
		}

		if ( !class_exists('WILCITY_APP\Database\FirebaseDeviceToken') ){
			return true;
		}

		self::$msg = self::getCustomerMsg($key);
		if ( empty(self::$msg) ){
			return false;
		}

		self::$firebaseID = User::getFirebaseID($userID);

		if ( empty(self::$firebaseID) ){
			return true;
		}

		self::$deviceToken = FirebaseDeviceToken::getDeviceToken($userID, self::$firebaseID);

		if ( empty(self::$deviceToken) ){
			return true;
		}

		if ( !self::isEnableNotificationStatusOnDevice($userID, $key) ){
			return false;
		}

		return true;
	}


	public static function getFirebaseChatConfiguration(){
		if ( self::$aConfiguration !== null ){
			return self::$aConfiguration;
		}

		self::$aConfiguration = GetSettings::getOptions('firebase_chat_configuration');
		if ( empty(self::$aConfiguration) || !is_array(self::$aConfiguration) ){
			return false;
		}

		foreach (self::$aConfiguration as $key => $val){
			self::$aConfiguration[$key] = trim($val);
		}

		return empty(self::$aConfiguration) ? false : self::$aConfiguration;
	}

	public static function getFirebaseField($key){
		if ( self::$aConfiguration == null ){
			self::getFirebaseChatConfiguration();
		}

		if ( empty(self::$aConfiguration) ){
			return '';
		}

		return isset(self::$aConfiguration[$key]) ? trim(self::$aConfiguration[$key]) : '';
	}

	public static function getFirebaseFile(){
		if ( self::$filebaseFileDir !== null ){
			return self::$filebaseFileDir;
		}

		self::$filebaseFileDir = is_file(Upload::getFolderDir('wilcity') . 'firebaseConfig.json') ? Upload::getFolderDir('wilcity') . 'firebaseConfig.json' : false;
		return self::$filebaseFileDir;
	}

	public static function getFirebaseDBURL(){
		if ( self::$firebaseDBURL !== null ){
			return self::$firebaseDBURL;
		}


		self::$firebaseDBURL = GetSettings::getOptions('firebase_db_url');
		return empty(self::$firebaseDBURL) ? false : self::$firebaseDBURL;
	}

	public static function isFirebaseEnable(){
		if ( (defined('WILCITY_FOCUS_DISABLE_FIREBASE') && WILCITY_FOCUS_DISABLE_FIREBASE) || version_compare(PHP_VERSION, '7.0.0', '<') ){
			return false;
		}

		if ( !self::getFirebaseFile() || !self::getFirebaseChatConfiguration() ){
			return false;
		}
		return true;
	}
}
