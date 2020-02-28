<?php
namespace WilokeListingTools\Framework\Payment\PayPal;

use WilokeListingTools\Framework\Store\Session;

trait PayPalAuthentication{
	private $aSessionStore;

	public function isMatchedToken(){
		if ( !isset($_REQUEST['token']) ){
			return false;
		}

		$this->aSessionStore = Session::getSession(wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData'), false);

		if ( empty($this->aSessionStore) ){
			return false;
		}

		if ( !isset($this->aSessionStore[$_REQUEST['token']]) || empty($this->aSessionStore[$_REQUEST['token']]) ){
			return false;
		}

		return true;
	}
}
