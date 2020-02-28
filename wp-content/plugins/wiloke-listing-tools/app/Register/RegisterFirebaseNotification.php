<?php

namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\Firebase;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Upload\Upload;


class RegisterFirebaseNotification {
	use ListingToolsGeneralConfig;
	use GetAvailableSections;
	use ParseSection;

	private $slug = 'wiloke-firebase-notifications';
	private $aListOfChatConfiguration = array(
		'apiKey' => '',
		'authDomain' => '',
		'databaseURL' => '',
		'projectID' => '',
		'messagingSenderId' => ''
	);

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_saving_customer_receive_notification', array($this, 'saveCustomerReceiveNotifications'));
		add_action('wp_ajax_saving_admin_receive_notification', array($this, 'saveAdminReceiveNotifications'));
		add_action('wp_ajax_wilcity_upload_firebase', array($this, 'uploadFirebase'));
		add_action('wp_ajax_wilcity_firease_chat_configuration', array($this, 'saveFirebaseChatConfiguration'));
	}

	public function saveFirebaseChatConfiguration(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error(array(
				'msg' => 'You do not have permission to upload this file'
			));
		}

		if ( empty($_POST['aConfigurations']) ){
			wp_send_json_error(
				array(
					'msg' => 'The configuration is required'
				)
			);
		}

		$aConfiguration = array();
		foreach ($this->aListOfChatConfiguration as $key => $nothing ){
			if ( !isset($_POST['aConfigurations'][$key]) || empty($_POST['aConfigurations'][$key]) ){
				wp_send_json_error(
					array(
						'msg' => 'The '.$key.' is required'
					)
				);
			}
			$aConfiguration[$key] = sanitize_text_field(trim($_POST['aConfigurations'][$key]));
		}

		SetSettings::setOptions('firebase_chat_configuration', $aConfiguration);
		wp_send_json_success(array(
			'msg' => 'Congratulations! The firebase chat configuration has been saved successfully'
		));
	}

	public function uploadFirebase(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error(array(
				'msg' => 'You do not have permission to upload this file'
			));
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		if ( $_FILES['wilcity_upload_filebase']['type'] != 'application/json' ){
			wp_send_json_error(array(
				'msg' => 'The file must be json format'
			));
		}
		$status = move_uploaded_file($_FILES['wilcity_upload_filebase']['tmp_name'], Upload::getFolderDir('wilcity') . 'firebaseConfig.json');

		if ( !$status ){
			wp_send_json_error(array(
				'msg' => 'Oops! We could not upload this file. Please rename this file to firebaseConfig.json then upload it manually to Your WordPress folder -> wp-content -> uploads -> wilcity folder.'
			));
		}

		if ( function_exists('chmod') ){
			chmod(Upload::getFolderDir('wilcity') . 'firebaseConfig.json', 0644);
		}

//		SetSettings::setOptions('is_uploaded_firebasefile', current_time('timestamp'));

		wp_send_json_success(array(
			'msg' => 'The file uploaded successfully'
		));
	}

	public function saveCustomerReceiveNotifications(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error();
		}

		$toggle = sanitize_text_field($_POST['toggle']);
		$aSettings = General::unSlashDeep($_POST['aSettings']);

		SetSettings::setOptions('toggle_customers_receive_notifications', $toggle);
		SetSettings::setOptions('customers_receive_notifications_settings', $aSettings);
		wp_send_json_success();
	}

	public function saveAdminReceiveNotifications(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error();
		}

		$toggle = sanitize_text_field($_POST['toggle']);
		$aSettings = General::unSlashDeep($_POST['aSettings']);

		SetSettings::setOptions('toggle_admin_receive_notifications', $toggle);
		SetSettings::setOptions('admin_receive_notifications_settings', $aSettings);
		wp_send_json_success();
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) === false ){
			return false;
		}
		$this->requiredScripts();
		$this->generalScripts();

		wp_enqueue_script('push-notifications', WILOKE_LISTING_TOOL_URL . 'admin/source/js/push-notifications.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);

		$aCustomerNotificationSettings = GetSettings::getOptions('customers_receive_notifications_settings');

		if ( empty($aCustomerNotificationSettings) ){
			$aCustomerNotificationSettings = wilokeListingToolsRepository()->get('push-notifications:customers');
		}else{
			$aCustomerNotificationSettings = array_merge(
				wilokeListingToolsRepository()->get('push-notifications:customers'),
				$aCustomerNotificationSettings
			);
		}

		$aAdminNotifications = GetSettings::getOptions('admin_receive_notifications_settings');

		if ( empty($aAdminNotifications) ){
			$aAdminNotifications = wilokeListingToolsRepository()->get('push-notifications:customers');
		}else{
			$aAdminNotifications = array_merge(
				wilokeListingToolsRepository()->get('push-notifications:admin'),
				$aAdminNotifications
			);
		}

		$oChatConfiguration = GetSettings::getOptions('firebase_chat_configuration');

		wp_localize_script('push-notifications', 'WILOKE_PUSH_NOTIFICATIONS',
			array(
				'toggle_admin_receive_notifications' => empty(GetSettings::getOptions('toggle_admin_receive_notifications')) ? 'disable' : GetSettings::getOptions('toggle_admin_receive_notifications'),
				'toggle_customers_receive_notifications' => empty(GetSettings::getOptions('toggle_customers_receive_notifications')) ? 'disable' : GetSettings::getOptions('toggle_customers_receive_notifications'),
				'oCustomerReceive',
				'oCustomerNotifications' => $aCustomerNotificationSettings,
				'oAdminNotifications'   => $aAdminNotifications,
				'isFirebaseFileUploaded' => Firebase::getFirebaseFile() ? 'yes' : 'no',
				'oFirebaseChatConfiguration' => empty($oChatConfiguration) || !is_array($oChatConfiguration) ? $this->aListOfChatConfiguration : $oChatConfiguration,
			)
		);
	}

	public function register(){
		add_submenu_page($this->parentSlug, 'Notification Settings', 'Notification Settings', 'administrator', $this->slug, array($this, 'pushNotificationSettings'));
	}

	public function pushNotificationSettings(){
		Inc::file('push-notifications:index');
	}
}