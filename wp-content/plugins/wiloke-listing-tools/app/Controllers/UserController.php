<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;

class UserController extends Controller {
	public function __construct() {
		add_action('wp_ajax_wilcity_fetch_user_profile', array($this, 'fetchUserProfile'));
		add_action('admin_init', array($this, 'addCaps'));
		add_action('ajax_query_attachments_args', array($this, 'mediaAccess'));
		add_action('wp_ajax_signin_firebase', [$this, 'signinFirebase']);
	}

	public function signinFirebase()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error();
        }

        wp_send_json_success(
          [
              'email' => User::getField('user_email', get_current_user_id()),
              'password' => User::getField('user_pass', get_current_user_id())
          ]
        );
    }

	public function addCaps(){
		$oContributor = get_role( 'contributor' );
		$oContributor->add_cap( 'upload_files' );

		if ( class_exists('\WilokeThemeOptions') && \WilokeThemeOptions::getOptionDetail('addlisting_upload_img_via') == 'wp' ){
			$oSubscriber = get_role('subscriber');
			if ( !empty($oSubscriber) ){
				if ( current_user_can('subscriber') ){
					$oSubscriber->add_cap( 'upload_files' );
				}else{
					$oSubscriber->remove_cap( 'upload_files' );
				}
			}
		}
	}

	public function mediaAccess($aArgs){
		$userID = User::getCurrentUserID();
		if ( !empty($userID) && class_exists('\WilokeThemeOptions') ){
			if ( \WilokeThemeOptions::isEnable('user_admin_access_all_media', true) && User::currentUserCan('administrator') ){
				return $aArgs;
			}

			$aArgs['author'] = User::getCurrentUserID();
		}
		return $aArgs;
	}

	public function fetchUserProfile(){
		$this->middleware(array('isUserLoggedIn'), array());
		$userID = get_current_user_id();

		$aThemeOptions = \Wiloke::getThemeOptions();

		wp_send_json_success(array(
			'display_name'  => User::getField('display_name', $userID),
			'avatar'        => User::getAvatar($userID),
			'position'      => User::getPosition($userID),
			'profile_description' => isset($aThemeOptions['dashboard_profile_description']) ? $aThemeOptions['dashboard_profile_description'] : '',
			'author_url'    => get_author_posts_url($userID)
		));
	}
}
