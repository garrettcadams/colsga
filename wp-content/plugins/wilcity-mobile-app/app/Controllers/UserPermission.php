<?php

namespace WILCITY_APP\Controllers;


trait UserPermission {
	protected static $aSubmitListings =  array('administrator', 'contributor', 'seller');

	protected static function canSubmitListing($aRoles){
		if ( !empty(array_intersect($aRoles, self::$aSubmitListings)) ){
			return true;
		}

		return false;
	}
}