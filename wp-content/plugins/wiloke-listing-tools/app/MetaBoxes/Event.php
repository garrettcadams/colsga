<?php
namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Models\EventModel;

class Event {
	use CustomFieldTools;
	public static $aEventData = null;

	public static $aDefault = array(
		'lat'=>'',
		'lng'=>'',
		'address'=>''
	);

	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('save_post', array($this, 'saveSettings'), 10, 3);
//		add_filter('wiloke-listing-tools/event/map-field-values', array($this, 'getGoogleAddress'));
	}

	public function saveSettings($listingID, $post, $updated){
		if ( !current_user_can('administrator') ){
			return false;
		}

		if ( !General::isAdmin() ){
			return false;
		}

		$aPostTypeKeys = General::getPostTypeKeys(false, false);

		if ( !in_array($post->post_type, $aPostTypeKeys) ){
			return false;
		}

		if ( isset($_POST['wilcity_my_products']) ){
			$aMyProducts = array_map('absint', $_POST['wilcity_my_products']);
			SetSettings::setPostMeta($listingID, 'my_products', $aMyProducts);
		}else{
			SetSettings::deletePostMeta($listingID, 'my_products');
		}

		if ( isset($_POST['wilcity_my_room']) ){
			$aMyProducts = absint($_POST['wilcity_my_room']);
			SetSettings::setPostMeta($listingID, 'my_room', $aMyProducts);
		}else{
			SetSettings::deletePostMeta($listingID, 'my_room');
		}
	}

	public static function getMyProducts(){
		if ( !isset($_GET['post']) || empty($_GET['post']) ){
			return false;
		}

		return GetSettings::getPostMeta($_GET['post'], 'my_products');
	}

	public static function getParentID(){
		if ( isset($_GET['post']) ){
			return wp_get_post_parent_id($_GET['post']);
		}

		return '';
	}

	public static function getEventData($postID=null){
		if ( empty($postID) ){
			self::$aEventData = array();
			return false;
		}

		if ( self::$aEventData === null ){
			self::$aEventData = EventModel::getEventData($postID);
		}
	}

	public static function parseData($date){
		$startsOn = date('Y/m/d h:i:s A', strtotime($date));
		$aData['date'] = '';
		$aData['time'] = '';

		if ( !empty($startsOn) ){
			$aParseStarts = explode(' ', $startsOn);
			$aData['date'] = $aParseStarts[0];
			$aData['time'] = $aParseStarts[1] . ' ' . $aParseStarts[2];
		}

		return $aData;
	}

	public static function getPostID(){
		return isset($_GET['post']) && !empty($_GET['post']) ? $_GET['post'] : '';
	}

	public static function getVideo(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}

		return GetSettings::getPostMeta($postID, 'video');
	}

	public static function getTimeFormat(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}

		return GetSettings::getPostMeta($postID, 'timeFormat');
	}

	public static function getSpecifyDay(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}
		return self::$aEventData['specifyDays'];
	}

	public static function getFrequency(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}
		return self::$aEventData['frequency'];
	}

	public static function startsOn(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}

		$aParseDate = self::parseData(self::$aEventData['startsOn']);
		return $aParseDate['date'];
	}

	public static function timezone(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}

		return self::$aEventData['timezone'];
	}

	public static function endsOn(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}

		$aParseDate = self::parseData(self::$aEventData['endsOn']);
		return $aParseDate['date'];
	}

	public static function closedAt(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}

		$aParseDate = self::parseData(self::$aEventData['endsOn']);
		return $aParseDate['time'];
	}

	public static function openingAt(){
		$postID = self::getPostID();
		if ( empty($postID) ){
			return false;
		}
		self::getEventData($postID);

		if ( empty(self::$aEventData) ){
			return '';
		}

		$aParseDate = self::parseData(self::$aEventData['startsOn']);
		return $aParseDate['time'];
	}

	public function renderMetaboxFields(){
		$aAllSettings = wilokeListingToolsRepository()->get('event-metaboxes');
		new_cmb2_box($aAllSettings['my_tickets']);
		new_cmb2_box($aAllSettings['hosted_by']);
		new_cmb2_box($aAllSettings['event_time_format']);
		new_cmb2_box($aAllSettings['event_settings']);
		new_cmb2_box($aAllSettings['event_parent']);
	}

	public function getGoogleAddress($postID){
		self::getEventData($postID);

		if ( empty(self::$aEventData) || (count( self::$aEventData) == 1) ){
			return array(
				'address' => '',
				'lat' => '',
				'lng' => ''
			);
		}
		return array(
			'address' => self::$aEventData['address'],
			'lat'     => self::$aEventData['lat'],
			'lng'     => self::$aEventData['lng']
		);
	}

	public function registerMetaBoxes(){
		add_meta_box('event-meta-boxes', 'Event Settings', array($this, 'settings'), 'event');
	}

	public function enqueueScripts($hook){
		if (!General::isPostType('event')){
			return false;
		}

		wp_enqueue_script('event-metabox', plugin_dir_url(__FILE__) . 'assets/js/event.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
	}

}