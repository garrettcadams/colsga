<?php

namespace WILCITY_APP\Controllers;


trait GetSecurityKey {
	private $aMobileOptions;

	protected function getMobileOption($key){
		if ( !empty($this->aMobileOptions) ){
			return isset($this->aMobileOptions[$key]) ? $this->aMobileOptions[$key] : '';
		}

//		$this->aMobileOptions = \Wiloke::getThemeOptions(true);
		$this->aMobileOptions = get_option('wiloke_themeoptions');;

		return isset($this->aMobileOptions[$key]) ? $this->aMobileOptions[$key] : '';
	}

	protected function getSecurityAuthKey(){
		return $this->getMobileOption('wilcity_security_authentication_key');
	}
}
