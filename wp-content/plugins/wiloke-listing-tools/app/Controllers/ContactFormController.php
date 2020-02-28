<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User;

class ContactFormController extends Controller {
	public function __construct() {
		add_filter('wpcf7_form_hidden_fields', array($this, 'addHiddenFilterForContactForm7OnListingPage'), 10, 1);
		add_filter('wpcf7_mail_components', array($this, 'filterCF7Components'), 10, 1);
	}

	public function addHiddenFilterForContactForm7OnListingPage($aFields){
		global $post;
		if ( !is_single() ){
			return $aFields;
		}
		$aListingTypes = General::getPostTypeKeys(false, false);
		if ( in_array($post->post_type, $aListingTypes) ){
			$aFields = array(
				'_wiloke_post_author_email' => $post->ID
			);
		}
		return $aFields;
	}

	public function filterCF7Components($components){
		if ( isset($_POST['_wiloke_post_author_email']) && !empty($_POST['_wiloke_post_author_email']) ){
			$post = get_post(absint($_POST['_wiloke_post_author_email']));
			if ( !is_wp_error($post) && !empty($post) ){
				$email = GetSettings::getPostMeta($post->ID, 'email');

				if ( !empty($email) ){
					$components['recipient'] = $email;
				}else{
					$email = User::getField('user_email', $post->post_author);
					$components['recipient'] = $email;
				}

			}
		}
		return $components;
	}
}