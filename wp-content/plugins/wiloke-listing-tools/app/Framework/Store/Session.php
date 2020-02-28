<?php
namespace WilokeListingTools\Framework\Store;


use WilokeListingTools\Framework\Helpers\DebugStatus;
// 'oauth_token=391151360-vNrREFyYUslmCooUwCpJBxJRNj5tilLr5ryIdQGR&oauth_token_secret=yArwhsWPOLlhJBoU5vXAEub6pfbhby8svKgKz8cBDpJ20&user_id=391151360&screen_name=wilokethemes'
class Session{
	protected static $isSessionStarted=false;
	protected static $expiration = 900;

	protected static function generatePrefix($name){
		return wilokeListingToolsRepository()->get('general:prefix') . $name;
	}

	protected static function sessionStart($sessionID=null){
		if ( !empty($sessionID) ){
			session_id($sessionID);
		}
		session_start();
	}

	public static function getSessionID(){
		session_start();
		var_export(session_id());
	}

	public static function setSession($name, $value, $sessionID=null){
		$value = maybe_serialize($value);
		if ( DebugStatus::status('WILOKE_STORE_WITH_DB') ){
			set_transient(self::generatePrefix($name), $value, self::$expiration);
		}else{
			if (empty(session_id())) {
				self::sessionStart($sessionID);
			}
			$_SESSION[self::generatePrefix($name)] = $value;
		}
	}

	public static function getSession($name, $thenDestroy=false){
		if ( DebugStatus::status('WILOKE_STORE_WITH_DB') ){
			$value = get_transient(self::generatePrefix($name));
		}else{
			if (empty(session_id())) {
				self::sessionStart();
			}

			$value = isset($_SESSION[self::generatePrefix($name)]) ? $_SESSION[self::generatePrefix($name)] : '';
		}

		if ( empty($value) ){
			return false;
		}

		if ( $thenDestroy ){
			self::destroySession($name);
		}

		return maybe_unserialize($value);
	}

	public static function deleteAllSessions(){
		Session::destroySession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'));
		Session::destroySession(wilokeListingToolsRepository()->get('payment:storePlanID'));
		Session::destroySession(wilokeListingToolsRepository()->get('addlisting:isAddingListingSession'));
		Session::destroySession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'));
		Session::destroySession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'));
	}

	public static function destroySession($name=null){
		if ( DebugStatus::status('WILOKE_STORE_WITH_DB') ){
			delete_transient(self::generatePrefix($name));
		}else{
			if ( !empty(self::generatePrefix($name)) ){
				unset($_SESSION[self::generatePrefix($name)]);
			}else{
				session_destroy();
			}
		}
	}
}