<?php

namespace WilokeListingTools\Controllers;


use WILCITY_APP\Database\FirebaseDB;
use WILCITY_APP\Database\FirebaseUser;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;

class ProfileController extends Controller {
	public function __construct() {
		add_action('wp_ajax_wilcity_fetch_profile_fields', array($this, 'fetchProfileFields'));
		add_action('wp_ajax_wilcity_update_profile', array($this, 'updateProfile'));
	}

	public static function updateBasicInfo($aBasicInfo, $userID){
		$aUserInfo = array();
		foreach ($aBasicInfo as $key => $aField){
			switch ($key){
				case 'first_name':
				case 'last_name':
				case 'display_name':
				case 'description':
					$aUserInfo[sanitize_text_field($key)] = sanitize_text_field($aField['value']);
					break;
				case 'email':
					if ( !empty($aField['value']) ){
						$currentEmail = User::getField('user_email', $userID);
						if ($currentEmail != $aField['value']){
							if ( email_exists($aUserInfo['user_email']) ){
								return array(
									'status' => 'error',
									'msg'    => esc_html__('This email is already registered.', 'wiloke-listing-tools')
								);
							}
							$aUserInfo['user_email'] = sanitize_text_field($aField['value']);
						}

					}
					break;
				case 'send_email_if_reply_message':
					$aUserMeta['send_email_if_reply_message'] = sanitize_text_field($aField['value']);
					break;
				case 'position':
					$aUserMeta['position'] = sanitize_text_field($aField['value']);
					break;
				case 'avatar':
				case 'cover_image':
					if ( !empty($aField['value']) && isset($aField['value'][0]) ){
						if ( isset($aField['value'][0]['id']) ){
							$aUserMeta[sanitize_text_field($key)]  = wp_get_attachment_image_url($aField['value'][0]['id'], 'large');
							$aUserMeta[sanitize_text_field($key).'_id']  = $aField['value'][0]['id'];
						}else{
							if ( strpos($aField['value'][0]['src'], 'http') === false ){
								$instUploadImg = new Upload();
								$instUploadImg->userID = $userID;
								$instUploadImg->aData['imageData']  = $aField['value'][0]['src'];
								$instUploadImg->aData['fileName']   = $aField['value'][0]['fileName'];
								$instUploadImg->aData['fileType']   = $aField['value'][0]['fileType'];
								$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder($userID);
								$id = $instUploadImg->image();
								$aUserMeta[sanitize_text_field($key)]  = wp_get_attachment_image_url($id, 'large');
								$aUserMeta[sanitize_text_field($key).'_id']  = $id;
							}
						}
					}
					break;
			}
		}

		if ( !empty($aUserInfo) ){
			$aUserInfo['ID'] = $userID;
			if ( empty($aUserInfo['display_name']) ){
				$aUserInfo['display_name'] = $aUserInfo['first_name'] . ' ' . $aUserInfo['last_name'];
			}

			$userID = wp_update_user((object)$aUserInfo);

            /**
             * @hooked WILCITY_APP\Controllers\Firebase\MessageController:updateUserAvatarToMessageFirebase 10
             */
			do_action('wilcity/wiloke-listing-tools/save-profile-basic-info', $aBasicInfo, $userID);
		}

		if ( !empty($aUserMeta) ){
			foreach ($aUserMeta as $metaKey => $val){
				SetSettings::setUserMeta($userID, $metaKey, $val);
			}
		}

		if ( is_wp_error($userID) ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('ERROR: Something went wrong, We could not update your profile.', 'wiloke-listing-tools')
			);
		}

		return true;
	}

	public static function updateFollowAndContact($aFollowAndContact, $userID){
		$aUserMeta = array();
		$aUserInfo = array();
		foreach ($aFollowAndContact as $key => $aField){
			switch ($key){
				case 'social_networks':
					foreach ($aField['value'] as $aSocial){
						if ( !empty($aSocial['url']) && strpos($aSocial['url'], 'http') !== false ){
							$aUserMeta['social_networks'][$aSocial['name']] = sanitize_text_field($aSocial['url']);
						}
					}
					break;
				case 'address':
				case 'phone':
					$aUserMeta[sanitize_text_field($key)] = sanitize_text_field($aField['value']);
					break;
				case 'website':
					$aUserInfo['user_url'] = sanitize_text_field($aField['value']);
					break;
			}
		}

		if ( !empty($aUserMeta) ){
			foreach ($aUserMeta as $key => $val){
				SetSettings::setUserMeta($userID, $key, $val);
			}
		}

		if ( !empty($aUserInfo) ){
			$aUserInfo['ID'] = $userID;
			$aUserInfo = (object)$aUserInfo;

			wp_update_user($aUserInfo);
		}
	}

	public static function updatePassword($aPassword, $userID){
		$oUserData = new \WP_User($userID);
		if ( empty($aPassword['currentPassword']) || !wp_check_password($aPassword['currentPassword'], $oUserData->data->user_pass, $userID) ){
			return array(
				'status' => 'error',
				'msg' => esc_html__('ERROR: Invalid Password.', 'wiloke-listing-tools')
			);
		}

		if ( $aPassword['newPassword'] !== $aPassword['confirmNewPassword'] ){
			return array(
				'status' => 'error',
				'msg' => esc_html__('ERROR: The password confirmation must be matched the new password.', 'wiloke-listing-tools')
			);
		}

		reset_password($oUserData, $aPassword['newPassword']);
		do_action('wilcity/user/after_reset_password', $oUserData);
		return true;
	}

	public function updateProfile(){
		$userID = get_current_user_id();
		$this->middleware(['isUserLoggedIn'], array(
			'userID' => $userID
		));

		if ( $_POST['aPassword'] !== 'no' && is_array($_POST['aPassword']) ){
			$aPassword = $_POST['aPassword'];
			$aStatus = self::updatePassword($aPassword, $userID);
			if ( $aStatus !== true ){
				wp_send_json_error(
					array(
						'msg' => $aStatus['msg']
					)
				);
			}
		}

		if ( $_POST['aBasicInfo'] !== 'no' ){
			$aStatus = $this->updateBasicInfo($_POST['aBasicInfo'], $userID);
			if ( $aStatus !== true ){
				wp_send_json_error(
					array(
						'msg' => $aStatus['msg']
					)
				);
			}
		}

		if ( $_POST['aFollowAndContact'] !== 'no' ){
			self::updateFollowAndContact($_POST['aFollowAndContact'], $userID);
		}

		wp_send_json_success(array(
			'msg' => esc_html__('Congratulations! Your profile have been updated', 'wiloke-listing-tools'),
			'oNewProfileInfo' => array(
				'display_name'  => User::getField('display_name', $userID),
				'avatar'        => User::getAvatar($userID),
				'position'      => User::getPosition($userID),
				'author_url'    => get_author_posts_url($userID)
			)
		));

	}

	public static function fetchProfileFields(){
		$userID = get_current_user_id();

		$aBasicInfo = array();
		$oUserData = get_userdata($userID);

		$aBasicInfo['first_name'] = array(
			'value' => $oUserData->first_name,
			'type'  => 'input',
			'label' => esc_html__('First Name', 'wiloke-listing-tools')
		);

		$aBasicInfo['last_name'] = array(
			'value' => $oUserData->last_name,
			'type'  => 'input',
			'label' => esc_html__('Last Name', 'wiloke-listing-tools')
		);

		$aBasicInfo['display_name']  = array(
			'value' => $oUserData->display_name,
			'type'  => 'input',
			'label' => esc_html__('Display Name', 'wiloke-listing-tools')
		);

		$avatar = GetSettings::getUserMeta($userID, 'avatar');
		$aBasicInfo['avatar'] = array(
			'value' => !empty($avatar) ? array(array('src'=>$avatar, 'fileName'=>esc_html__('Avatar', 'wiloke-listing-tools'))) : array(),
			'type'  => 'upload_img',
			'label' => esc_html__('Avatar', 'wiloke-listing-tools')
		);

		$coverImg = GetSettings::getUserMeta($userID, 'cover_image');
		$aBasicInfo['cover_image'] = array(
			'value' => empty($coverImg) ? array() : array(array('src'=>$coverImg, 'fileName'=>esc_html__('Cover Image', 'wiloke-listing-tools'))),
			'type'  => 'upload_img',
			'label' => esc_html__('Cover Image', 'wiloke-listing-tools')
		);

		$aBasicInfo['email'] = array(
			'type' => 'email',
			'value'=> $oUserData->user_email,
			'label' => esc_html__('Email', 'wiloke-listing-tools')
		);

		$aBasicInfo['position'] = array(
			'type' => 'input',
			'value'=> GetSettings::getUserMeta($userID, 'position'),
			'label' => esc_html__('Position', 'wiloke-listing-tools')
		);

		$aBasicInfo['description']  = array(
			'value' => get_the_author_meta('user_description', $userID),
			'type'  => 'textarea',
			'label' => esc_html__('Introduce your self', 'wiloke-listing-tools')
		);

		$aBasicInfo['send_email_if_reply_message']  = array(
			'value' => GetSettings::getUserMeta($userID, 'send_email_if_reply_message'),
			'type'  => 'checkbox',
			'label' => esc_html__('Receive message through email.', 'wiloke-listing-tools')
		);

		$aBasicInfo = apply_filters('wilcity/wiloke-listing-tools/filter/profile-controllers/basic-info', $aBasicInfo);

		$aFollowContact = array();

		$aFollowContact['address'] = array(
			'type' => 'input',
			'value'=> GetSettings::getUserMeta($userID, 'address'),
			'label' => esc_html__('Address', 'wiloke-listing-tools')
		);

		$aFollowContact['phone'] = array(
			'type' => 'input',
			'value'=> GetSettings::getUserMeta($userID, 'phone'),
			'label' => esc_html__('Phone', 'wiloke-listing-tools')
		);

		$aFollowContact['website'] = array(
			'type' => 'input',
			'value'=> User::getWebsite($userID),
			'label' => esc_html__('Website', 'wiloke-listing-tools')
		);

		$aRawSocialNetworks = GetSettings::getUserMeta($userID, 'social_networks');
		$aSocialNetworks = array();

		if ( !empty($aRawSocialNetworks) ){
			$aParsedSocialNetworks = array();
			foreach ($aRawSocialNetworks as $socialName => $socialUrl){
				if ( !empty($socialUrl) ){
					$aParsedSocialNetworks[] = array(
						'name' => $socialName,
						'url'  => $socialUrl
					);
				}
			}
			$aSocialNetworks = $aParsedSocialNetworks;
		}

		$aFollowContact['social_networks'] = array(
			'type'  => 'social_networks',
			'value' => $aSocialNetworks,
			'label' => esc_html__('Social Networks', 'wiloke-listing-tools')
		);

		$aFollowContact = apply_filters('wilcity/wiloke-listing-tools/filter/profile-controllers/follow-contact', $aFollowContact);

		wp_send_json_success(array(
			'oBasicInfo' => $aBasicInfo,
			'oFollowContact' => $aFollowContact
		));
	}
}
