<?php

namespace WilokeListingTools\Framework\Helpers;


use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class General {
	public static $aBusinessHours = null;
	private static $aFieldSettings = array();
	public static $isBookingFormOnSidebar = false;
	protected static $aThemeOptions = array();

	public static function getCurrentLanguage(){
		return defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : '';
	}

	public static function renderRel($url){
		$homeurl = home_url();
		if ( strpos($url, $homeurl) === false ){
			return 'nofollow';
		}

		return 'dofollow';
	}

	public static function loginRedirectTo()
    {
        $type = \WilokeThemeOptions::getOptionDetail('login_redirect_type');
        if ($type == 'self_page') {
            global $wp;
            return add_query_arg( $wp->query_vars, home_url() );
        }

        return get_permalink(\WilokeThemeOptions::getOptionDetail('login_redirect_to'));
    }

	public static function isAdmin(){
		if ( !wp_doing_ajax() && is_admin() ){
			if ( !isset($_POST['template']) || $_POST['template'] != 'templates/mobile-app-homepage.php' ){
				return true;
			}

			return false;
		}

		if ( wp_doing_ajax() ){
			if ( isset($_POST['action']) ){
				$action = $_POST['action'];
			}else if ( isset($_GET['action']) ){
				$action = $_GET['action'];
			}

			if( isset($action) ) {
				if( $action == 'wilcity_handle_submit_listing' || $action == 'wilcity_handle_submit_listing') {
					return false;
				}
            }

			if ( !isset($action) || empty($action) || ( isset($action) && strpos($action, 'wilcity') === false || strpos($action, 'wiloke') === false ) ){
				return true;
			}
		}

		return false;
	}
	public static function isElementorPreview(){
		if ( class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode() || (isset($_REQUEST['elementor-preview']) && !empty($_REQUEST['elementor-preview'])) ) {
			return true;
		}

		return false;
	}

	public static function unSlashDeep($aVal){
		if ( !is_array($aVal) ){
			return stripslashes($aVal);
		}
		return array_map(array(__CLASS__, 'unSlashDeep'), $aVal);
	}

	public static function getOptionField($key=''){
		if ( !empty(self::$aThemeOptions) ){
			return isset(self::$aThemeOptions[$key]) ? self::$aThemeOptions[$key] : '';
		}

		self::$aThemeOptions = \Wiloke::getThemeOptions(true);
		return isset(self::$aThemeOptions[$key]) ? self::$aThemeOptions[$key] : '';
	}

	public static function getSecurityAuthKey(){
		return self::getOptionField('wilcity_security_authentication_key');
	}

	/**
	 * Get Client IP
	 * @since 1.0.1
	 */
	public static function clientIP(){
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
		}else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ipaddress = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
		}else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = sanitize_text_field($_SERVER['HTTP_X_FORWARDED']);
		}else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = sanitize_text_field($_SERVER['HTTP_FORWARDED_FOR']);
		}else if(isset($_SERVER['HTTP_FORWARDED'])){
			$ipaddress = sanitize_text_field($_SERVER['HTTP_FORWARDED']);
		}else if(isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = sanitize_text_field($_SERVER['REMOTE_ADDR']);
		}else {
			$ipaddress = false;
		}
		return $ipaddress;
	}

	public static function getCustomFieldsOfPostType($postType){
		if ( empty($postType) ){
			return false;
		}

		$aUsedSections = GetSettings::getOptions(self::getUsedSectionKey($postType));
		if ( empty($aUsedSections) ){
			return false;
		}

		$aCustomSections = array_filter($aUsedSections, function($aSection){
			if ( isset($aSection['isCustomSection']) && $aSection['isCustomSection'] == 'yes' ){
				return true;
			}
			return false;
		});

		return empty($aCustomSections) ? false : $aCustomSections;
	}

	public static function getCustomGroupsOfPostType($postType){
		if ( empty($postType) ){
			return false;
		}

		$aUsedSections = GetSettings::getOptions(self::getUsedSectionKey($postType));
		if ( empty($aUsedSections) ){
			return false;
		}

		$aGroups = array_filter($aUsedSections, function($aSection){
			if ( isset($aSection['type']) && $aSection['type'] == 'group' ){
				return true;
			}
			return false;
		});

		return empty($aGroups) ? false : $aGroups;
	}

	public static function convertToNiceNumber($number, $evenZero=false){
		if ( empty($number) && !$evenZero ){
			return 0;
		}

		if ( $number < 10 ){
			return 0 . $number;
		}else if ( $number >= 1000 ){
			$prefix = floor($number/1000);
			$subFix = $number - $prefix*10000;
			return $prefix . $subFix;
		}

		return $number;
	}

	public static function ksesHtml($content, $isReturn=false){
		$allowed_html = array(
			'a' => array(
				'href'  => array(),
				'style' => array(
					'color' => array()
				),
				'title' => array(),
				'target'=> array(),
				'class' => array(),
				'data-msg' => array()
			),
			'div'    => array('class'=>array()),
			'h1'     => array('class'=>array()),
			'h2'     => array('class'=>array()),
			'h3'     => array('class'=>array()),
			'h4'     => array('class'=>array()),
			'h5'     => array('class'=>array()),
			'h6'     => array('class'=>array()),
			'br'     => array('class' => array()),
			'p'      => array('class' => array(), 'style'=>array()),
			'em'     => array('class' => array()),
			'strong' => array('class' => array()),
			'span'   => array('data-typer-targets'=>array(), 'class' => array()),
			'i'      => array('class' => array()),
			'ul'     => array('class' => array()),
			'ol'     => array('class' => array()),
			'li'     => array('class' => array()),
			'code'   => array('class'=>array()),
			'pre'    => array('class' => array()),
			'iframe' => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class'=>array('embed-responsive-item')),
			'img'    => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class'=>array(), 'alt'=>array()),
			'embed'  => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class' => array()),
		);

		if ( !$isReturn ) {
			echo wp_kses(wp_unslash($content), $allowed_html);
		}else{
			return wp_kses(wp_unslash($content), $allowed_html);
		}
	}

	public static function detectPostType(){
		if ( isset($_REQUEST['post']) ){
			return get_post_field('post_type', $_REQUEST['post']);
		}else if ( isset($_REQUEST['post_type']) ){
			return $_REQUEST['post_type'];
		}else if ( isset($_REQUEST['page']) ){
			if ( $_REQUEST['page'] == 'wiloke-event-settings' ){
				return 'event';
			}

			return $_REQUEST['page'];
		}
	}

	public static function wpmlIsLangDuplicate($postID, $originalPostID){
		if ( !defined('ICL_LANGUAGE_CODE') ){
			return false;
		}

		$parentID = get_post_meta( $postID, '_icl_lang_duplicate_of', true );
		return $parentID == $originalPostID;
	}

	public static function getUsedSectionKey($postType, $isCheckWPML=false){
		$key = wilokeListingToolsRepository()->get('addlisting:usedSectionKey');

//		if ( $isCheckWPML && defined('ICL_LANGUAGE_CODE') ){
//			$key .= '_' . ICL_LANGUAGE_CODE;
//		}
		return str_replace('add_listing', 'add_'.$postType, $key);
	}

    public static function getUsedSectionSavedAt($postType, $isCheckWPML=false){
        $key = wilokeListingToolsRepository()->get('addlisting:usedSectionSavedAtKey');

//		if ( $isCheckWPML && defined('ICL_LANGUAGE_CODE') ){
//			$key .= '_' . ICL_LANGUAGE_CODE;
//		}
        return str_replace('add_listing', 'add_'.$postType, $key);
    }

	public static function getClaimKey($postType){
		return $postType . '_claim_settings';
	}

	public static function getSchemaMarkupKey($postType){
		return $postType . '_schema_markup';
	}

	public static function getSchemaMarkupSavedAtKey($postType){
		return $postType . '_schema_markup_saved_at';
	}

	public static function getSearchFieldsKey($postType){
		return $postType . '_search_fields';
	}

	public static function mainSearchFormSavedAtKey($postType){
		return $postType . 'main_search_form_'.$postType.'_saved_at';
	}

	public static function getHeroSearchFieldsKey($postType){
		return $postType . '_hero_search_fields';
	}

	public static function heroSearchFormSavedAt($postType){
		return $postType . 'hero_search_form_'.$postType.'_saved_at';
	}

	public static function getReviewKey($type, $postType){
		$aReviews = wilokeListingToolsRepository()->get('reviews');
		return $postType . '_' . $aReviews[$type];
	}

	public static function numberFormat($number, $decimals){
		return number_format($number, $decimals);
	}

	/*
	 * @settingType: navigation or sidebar
	 * @postType: Post Type
	 */
	public static function getSingleListingSettingKey($settingType, $postType){
		$key = wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub($settingType);
		return $postType . '_' . $key;
	}

	public static function isPostType($postType){
		if ( !is_admin() ){
			return false;
		}

		return self::detectPostType() == $postType;
	}

	public static function getPostTypes($isIncludedDefaults=true, $exceptEvents=false){
		$aDefaults = array(
			'post' => array(
				'key'               => 'post',
				'slug'              => 'post',
				'singular_name'     => esc_html__('Post', 'wiloke-listing-tools'),
				'name'              => esc_html__('Posts', 'wiloke-listing-tools'),
				'icon'              => ''
			),
			'page' => array(
				'key'               => 'page',
				'slug'              => 'page',
				'singular_name'     => esc_html__('Page', 'wiloke-listing-tools'),
				'name'              => esc_html__('Pages', 'wiloke-listing-tools'),
				'icon'              => ''
			)
		);

		$aCustomPostTypes = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));

		if ( $isIncludedDefaults ){
			$aPostTypes =  !empty($aCustomPostTypes) && is_array($aCustomPostTypes) ? $aCustomPostTypes + $aDefaults : $aDefaults;
		}else{
			$aPostTypes = $aCustomPostTypes;
		}

		if ( empty($aPostTypes) ){
			$aPostTypes = array(
				array(
					'key'               => 'listing',
					'slug'              => 'listing',
					'singular_name'     => 'Listing',
					'name'              => 'Listings',
					'icon'              => ''
				),
				array(
					'key'               => 'event',
					'slug'              => 'event',
					'singular_name'     => 'Event',
					'name'              => 'Events',
					'icon'              => ''
				)
			);
		}

		$aPostTypesWithKey = array();
		foreach ($aPostTypes as $aInfo){
			if ( $exceptEvents && $aInfo['key'] == 'event' ){
				continue;
			}

			$aPostTypesWithKey[$aInfo['key']] = array();
			$aPostTypesWithKey[$aInfo['key']]['name'] = isset($aInfo['name']) ? $aInfo['name'] : 'My Custom Post Type';
			$aPostTypesWithKey[$aInfo['key']]['singular_name'] = isset($aInfo['singular_name']) ? $aInfo['singular_name'] : 'My Custom Post Type';
			$aPostTypesWithKey[$aInfo['key']]['icon'] = isset($aInfo['icon']) ? $aInfo['icon'] : 'la la-rocket';
			$aPostTypesWithKey[$aInfo['key']]['bgColor'] = isset($aInfo['addListingLabelBg']) ? $aInfo['addListingLabelBg'] : '';
		}

		$aEventSettings = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'));

		if ( isset($aEventSettings['toggle_event']) && $aEventSettings['toggle_event'] == 'disable'  ){
			unset($aPostTypesWithKey['event']);
		}

		$aPostTypesWithKey = apply_filters('wilcity/filter/directory-types', $aPostTypesWithKey);

		return $aPostTypesWithKey;
	}

	public static function getPostTypeKeys($isIncludedDefaults, $exceptEvents=false){
		$aPostTypes = self::getPostTypes($isIncludedDefaults, $exceptEvents);
		return $aPostTypes ? array_keys($aPostTypes) : false;
	}

	public static function getDefaultPostTypeKey($exceptEvent=false, $isAddListing=false){
		$aDirectoryType = $isAddListing ? GetSettings::getFrontendPostTypes(true) : self::getPostTypeKeys(false, $exceptEvent);
		return array_shift($aDirectoryType);
	}

	public static function getDefaultPostType($exceptEvent=false){
		$aDirectoryType = self::getPostTypes(false, $exceptEvent);
		return array_shift($aDirectoryType);
	}

	public static function getFirstPostTypeKey($isIncludedDefaults, $exceptEvents=false){
		$aPostTypes = self::getPostTypeKeys($isIncludedDefaults, $exceptEvents);
		if ( $aPostTypes ){
			return array_shift($aPostTypes);
		}
		return false;
	}

	public static function getPostTypeOptions($isIncludedDefaults, $exceptEvents=false){
		$aPostTypes = self::getPostTypes($isIncludedDefaults, $exceptEvents);
		$aOptions = array();
		foreach ($aPostTypes as $postType => $aInfo){
			$aOptions[$postType] = $aInfo['singular_name'];
		}
		return $aOptions;
	}

	public static function generateBusinessHours(){
		if ( self::$aBusinessHours !== null ){
			return self::$aBusinessHours;
		}

		$aCreatingAM = array();
		$aForm = apply_filters('wilcity/filter/business-hours-skeleton', wilokeListingToolsRepository()->get('addlisting:aFormBusinessHour'));

		for ($i=0;  $i<=11; $i=$i+1){
			$aGenerated = array();
			foreach ( $aForm as $key => $aItem ){
				if ( $i > 9 ){
					$newHour = $i;
				}else{
					$newHour = '0'.$i;
				}
				$aGenerated['value'] = str_replace('00::', $newHour.':', $aItem['value']);
				$twentyFormat = $newHour == '00' ? 12 : $newHour;
				$aGenerated['name'] = str_replace('00:', $twentyFormat.':', $aItem['name']);
				$aGenerated['name24'] = date('H:i', strtotime($aGenerated['value']));

				$aCreatingAM[] = $aGenerated;
			}
		}

		self::$aBusinessHours = $aCreatingAM;

		$aCreatingPM = array();

		for ($i=12; $i<24; $i++){
			$aGenerated = array();
			foreach ( $aForm as $key => $aItem ){
				if ( $i > 9 ){
					$newHour = $i-12;
				}else{
					$newHour = '0'.$i-12;
				}
				$aGenerated['value'] = str_replace('00::', $i.':', $aItem['value']);
				$aGenerated['name'] = str_replace(array('00:', 'AM'), array($newHour.':', 'PM'), $aItem['name']);
				$aGenerated['name24'] = date('H:i', strtotime($aGenerated['value']));

				$aCreatingPM[] = $aGenerated;
			}
		}

		self::$aBusinessHours = array_merge(self::$aBusinessHours, $aCreatingPM);

		return self::$aBusinessHours;
	}

	public static function getDayOfWeek($day){
		$aDaysOfWeek = wilokeListingToolsRepository()->get('general:aDayOfWeek');
		return $aDaysOfWeek[$day];
	}

	public static function getPostsStatus(){
		$aCustom = wilokeListingToolsRepository()->get('posttypes:post_statuses');

		$aPostStatus = array();

		$aPostStatus['publish'] = array(
			'label' => esc_html__('Published', 'wiloke-listing-tools'),
			'icon'  => 'la la-share-alt'
		);

		$aPostStatus['pending'] = array(
			'label' => esc_html__('In Review', 'wiloke-listing-tools'),
			'icon'  => 'la la-refresh'
		);

		foreach ($aCustom  as $postType => $aInfo){
			$aPostStatus[$postType] = array(
				'label' => $aInfo['label'],
				'icon'  => $aInfo['icon']
			);
		}

		return $aPostStatus;
	}

	public static function generateMetaKey($name){
		return wilokeListingToolsRepository()->get('general:metaboxPrefix') . $name;
	}

	public static function addPrefixToPromotionPosition($position){
		return 'wilcity_promote_' . $position;
	}

	public static function findField($postType, $fieldKey){
		if ( isset(self::$aFieldSettings[$postType]) && isset(self::$aFieldSettings[$postType][$fieldKey]) ){
			return self::$aFieldSettings[$postType][$fieldKey];
		}

		if ( !isset(self::$aFieldSettings[$postType]) ){
			self::$aFieldSettings[$postType] = array();
		}

		$aSettings = GetSettings::getOptions(General::getUsedSectionKey($postType));
		foreach ($aSettings as $aField){
			if ( $aField['key'] == $fieldKey ){
				self::$aFieldSettings[$postType][$fieldKey] = $aField;
				return self::$aFieldSettings[$postType][$fieldKey];
			}
		}
	}

	public static function buildSelect2OptionForm($post){
		$aTemporary['id'] = $post->ID;
		$aTemporary['text']  = $post->post_title;
		return $aTemporary;
	}

	public static function printVal($aFieldSettings){
		echo '<pre>';
		var_export($aFieldSettings);
		echo '</pre>';
		die();

	}

	public static function taxonomyPostTypeCacheKey(){
		return  'get_taxonomy_saved_at';
	}

	public static function renderWhatsApp($link){
		if ( strpos($link, 'http') === false ){
			if ( preg_match('/[0-9]/', $link) ){
				$link = 'https://api.whatsapp.com/send?phone='.$link;
			}else{
				$link = 'https://api.whatsapp.com/send?text='.$link;
			}
		}

		return $link;
	}

	public static function parseCustomSelectOption($option){
		$aRawOption = explode('|', $option);
		if ( strpos($option, ':') !== false ){
			$aParse = explode(':', $aRawOption[0]);
			return array(
				'key' => trim($aParse[0]),
				'name' => trim($aParse[1])
			);
		}else{
			return array(
				'key' => $aRawOption[0],
				'name' => isset($aRawOption[1]) ? $aRawOption[1] :  $aRawOption[0]
			);
		}
	}

	public static function getFBID($url) {
		if ( empty($url) ){
			return false;
		}

		$aThemeOptions = \Wiloke::getThemeOptions(true);
		if ( empty($aThemeOptions['fb_api_id']) || empty($aThemeOptions['fb_app_secret']) || empty($aThemeOptions['fb_access_token']) ){
			return false;
		}

		/* PHP SDK v5.0.0 */
		/* make the API call */
		$fb = new Facebook([
			'app_id' => $aThemeOptions['fb_api_id'],
			'app_secret' => $aThemeOptions['fb_app_secret'],
			'default_graph_version' => 'v3.1',
		]);

		$aUrl = explode('/', $url);

		try {
			// Returns a `Facebook\FacebookResponse` object
			$response = $fb->get(
				'/', end($aUrl),
				$aThemeOptions['fb_access_token']
			);
		} catch(FacebookResponseException $e) {
			return false;
		} catch(FacebookSDKException $e) {
			return false;
		}
		$aStatus = $response->getGraphNode();
		if ( is_array($aStatus) && isset($aStatus['id']) ){
			return $aStatus['id'];
		}
		return $aStatus;
		/* handle the result */
	}


	public static function isRemoveWooCommerceSection() {
		return General::$isBookingFormOnSidebar || (isset($_REQUEST['iswebview']) && $_REQUEST['iswebview'] == 'yes');
	}
}
