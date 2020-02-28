<?php
namespace WilokeListingTools\Framework\Helpers;


class DebugStatus {
	/*
	 * Determining the status of the specified definition key
	 *
	 * @param string $definition
	 */
	public static function status($definition){
		if ( !defined($definition) ){
			return false;
		}

		return constant($definition);
	}
}