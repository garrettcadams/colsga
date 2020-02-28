<?php

namespace WilokeListingTools\Framework\Helpers;


class Cookie {
	private static $aCookie = array();

	public static function getCookie($key, $clearAfterThat=false){
		$val = get_transient(WILOKE_LISTING_PREFIX.$key);
		if ( $clearAfterThat ){
			delete_transient(WILOKE_LISTING_PREFIX.$key);
		}
		return $val;
	}

	public static function setCookie($key, $val, $expiration=''){
		$expiration = empty($expiration) ? 5*60 : abs($expiration);
		set_transient(WILOKE_LISTING_PREFIX.$key, $val, $expiration);
	}

	public static function fixSetCookie(){

	}
}