<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User;

class GuardController extends Controller {
	public function __construct() {
		add_action('admin_init', array($this, 'preventAccessAdmin'));
	}

	private function preventUser($aRoles){
		$aPreventUsers = apply_filters('wilcity/guard/is-prevent', array('contributor', 'subscriber'));

		foreach ($aRoles as $role){
			if ( in_array($role, $aPreventUsers) ){
				return true;
			}

			if ( get_option('admin_access') == 'off' ){
				if ( in_array($role, array('vendor')) ){
					return true;
				}
			}
		}
		return false;
	}

	public function preventAccessAdmin(){
		if ( defined('DOING_AJAX') && DOING_AJAX ){
			return true;
		}

		if ( is_user_logged_in() ){
			$oUserRoles = new \WP_User(User::getCurrentUserID());
			$aRoles = $oUserRoles->roles;
			$isPrevent = $this->preventUser($aRoles);

			if ( $isPrevent ){
				wp_redirect(home_url('/'));
				exit();
			}
		}
	}
}