<?php

namespace WilokeListingTools\Framework\Helpers;


use WilokeListingTools\Frontend\User;

class Validation {
	public static function isUrl($url){
		return filter_var($url, FILTER_VALIDATE_URL);
	}

	public static function isEmail($url){
		return filter_var($url, FILTER_VALIDATE_EMAIL);
	}

	public static function isPostAuthor($postID, $isCheckEventAdmin=false){
		if ( !$isCheckEventAdmin ){
			if ( User::can('administrator') ){
				return true;
			}
		}

		return $postID == User::getCurrentUserID();
	}
}