<?php

namespace WILCITY_APP\Controllers;


use ReallySimpleJWT\TokenBuilder;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;

trait BuildToken {
	private function buildToken($oUser, $expiration=''){
		$builder = new TokenBuilder();

		if ( empty($expiration) ){
			$expiration = $this->getOptionField('wilcity_token_expired_after');
			$expiration = !empty($expiration) ? '+' . $expiration . ' day' : '+30 day';
		}
		try{
			$token = $builder->addPayload(['key' => 'userID', 'value' => $oUser->ID])
			                 ->setSecret($this->getSecurityAuthKey())
			                 ->setExpiration(strtotime($expiration))
			                 ->setIssuer(get_option('siteurl'))
			                 ->build();
			do_action('wilcity/wilcity-mobile-app/app-signed-up', $oUser->ID, $token);
			return $token;
		}catch (\Exception $oE){
			return array(
				'status' => 'error',
				'msg'    => $oE->getMessage() . '. Please go to Appearance -> Theme Options -> Mobile General Settings -> SECURE AUTH KEY to complete this setting'
			);
		}
	}

	private function buildPermanentLoginToken($oUser) {
	    $token = $this->buildToken($oUser);
	    if (is_array($token)) {
	        return $token;
        }
        
        SetSettings::setUserMeta($oUser->ID, 'app_token', $token);
	    return $token;
    }

	/**
     * Build temporary login token (It will be expired after 10 minutes)
     * This is useful for WooCommerce login
     */
	private function buildTemporaryLoginToken($oUser) {
        $token = $this->buildToken($oUser, '+10 minutes');
        if (is_array($token)) {
            return $token;
        }

        SetSettings::setUserMeta($oUser->ID, 'temporary_app_token', $token);
        SetSettings::setUserMeta($oUser->ID, 'temporary_user_ip', General::clientIP());
        return $token;
    }
}
