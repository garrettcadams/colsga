<?php
namespace WilokeListingTools\MetaBoxes;

use WilokeListingTools\AlterTable\AlterTableBusinessHours;
use WilokeListingTools\AlterTable\AlterTableLatLng;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;

class Listing {
	protected $aSection;
	public static $aDefault = array(
		'lat'=>'',
		'lng'=>'',
		'address'=>''
	);

	public $aPostTypes = array();
	private $aRelationshipValues = array();
    private $countRelationships = 0;

	public function __construct() {
		add_filter('cmb2_sanitize_wiloke_map', array( $this, 'savePWMAP' ), 10, 4);
		add_action('cmb2_admin_init', array($this, 'timezoneBox'), 10);
		add_filter('cmb2_render_wilcity_date_time', array( $this, 'renderDateTimeField' ), 10, 5);
		add_filter('cmb2_sanitize_wilcity_date_time', array( $this, 'sanitizeDateTimeCallBack' ), 10, 2);
		add_filter('cmb2_render_wilcity_social_networks', array( $this, 'renderSocialNetworks' ), 10, 5);
		add_action('cmb2_admin_init', array($this, 'registerMyProductsMetaBox'), 10);
		add_action('cmb2_admin_init', array($this, 'registerMyPosts'), 10);
		add_action('cmb2_admin_init', array($this, 'registerRestaurantMenu'), 10);
		add_action('add_meta_boxes', array($this, 'registerMetaBoxes'), 15);
		add_action('save_post', array($this, 'saveSettings'), 10, 3);

		add_filter('wiloke-listing-tools/map-field-values', array(__CLASS__, 'getListingAddress'));
	    add_action('wp_ajax_wilcity_get_timezone_by_latlng', array($this, 'getTimezoneByLatLng'));

	    add_action('added_post_meta', array($this, 'modifySaveCustomRelationshipViaBackend'), 10, 4);
	    add_action('updated_post_meta', array($this, 'modifySaveCustomRelationshipViaBackend'), 10, 4);
	}

	public function isDisableMetaBlock($blockName){
	    if ( !defined('WILCITY_DISABLE_META_BLOCKS') || empty(WILCITY_DISABLE_META_BLOCKS) ){
	        return false;
        }

        return in_array($blockName, WILCITY_DISABLE_META_BLOCKS);
    }

	public function modifySaveCustomRelationshipViaBackend($metaID, $listingID, $metaKey, $metaVal){
        if ( !General::isAdmin() ){
            return false;
        }

        if ( strpos($metaKey, 'wilcity_custom_') === false || strpos($metaKey, '_relationship') === false ){
            return false;
        }

        global $wpdb;
		$this->countRelationships = $this->countRelationships+1;
        if ( $this->countRelationships != count($_POST[$metaKey]) ){
	        SetSettings::deletePostMeta($listingID, $metaKey);
        }

        if ( !empty($_POST[$metaKey]) && $this->countRelationships == count($_POST[$metaKey]) ){
            $aRelationshipIDs = array_map(function($postID){
                global $wpdb;
                return $wpdb->_real_escape($postID);
            }, $_POST[$metaKey]);

            $wpdb->update(
		        $wpdb->postmeta,
		        array(
			        'meta_value' => implode(',', $aRelationshipIDs)
		        ),
		        array(
			        'meta_key' => $metaKey,
			        'post_id'  => $listingID
		        ),
		        array(
			        '%s'
		        ),
		        array(
			        '%s',
			        '%d'
		        )
	        );
	        $this->countRelationships = 1;
        }
    }

	public static function setListingTypesOptions(){
	    $aListingTypes = General::getPostTypes(false, false);
	    $aOptions = array();
	    foreach ($aListingTypes as $postType => $aPostType){
		    $aOptions[$postType] = $aPostType['singular_name'];
        }
        return $aOptions;
    }

	public function getTimezoneByLatLng(){
	    if ( !isset($_POST['latLng']) || empty($_POST['latLng']) ){
	        wp_send_json_error();
        }

	    $aThemeOptions = \Wiloke::getThemeOptions();
		$url = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$_POST['latLng'].'&timestamp='.time().'&key='.$aThemeOptions['general_google_api'];
		$aTimeZone = wp_remote_get(esc_url_raw($url));
		if ( is_wp_error($aTimeZone)  ){
			wp_send_json_error();
		}else{
			$oTimeZone = json_decode($aTimeZone['body']);
			wp_send_json_success($oTimeZone->timeZoneId);
		}
    }

	public function getPostTypes(){
	    if ( !empty($this->aPostTypes) ){
	        return $this->aPostTypes;
        }

		$this->aPostTypes = General::getPostTypeKeys(false, true);

		return $this->aPostTypes;
    }

	public function sanitizeDateTimeCallBack($override_value, $value){
        return sanitize_text_field($value);
    }

	public function renderDateTimeField($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object){
	    $name = str_replace('wilcity_', '',  $field->args('_name'));

	    $val = GetSettings::getPostMeta($field_object_id, $name);

		$field_type_object->_desc( true, true );
		?>
        <input type="datetime-local" name="<?php echo esc_attr($field->args('_name')); ?>" class="regular-text" id="<?php echo esc_attr($field->args('_name')); ?>" value="<?php echo esc_attr($val); ?>">
        <?php
    }

    static protected function isDataExisting($listingID, $dayOfWeek){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTableBusinessHours::$tblName;

		return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT ID FROM $tbl WHERE objectID=%d AND dayOfWeek=%s",
	            $listingID, $dayOfWeek
            )
        );
    }

    static protected function updateBusinessHourTbl($listingID, $dayOfWeek, $aBusinessHour, $timezone=''){
        global $wpdb;
        $tbl = $wpdb->prefix . AlterTableBusinessHours::$tblName;
        $fromTimezone = !empty($timezone) ? $timezone : GetSettings::getPostMeta($listingID, 'timezone');

	    if ( empty($fromTimezone) ){
	        $fromTimezone = Time::getDefaultTimezoneString();
            if ( !$fromTimezone && wp_doing_ajax() ){
                wp_send_json_error(array(
                    'msg' => esc_html__('Please use Timezone String instead of UTC timezone offset: Settings &gt;  General', 'wiloke-listing-tools')
                ));
            }
        }

        if ( $id = self::isDataExisting($listingID, $dayOfWeek) ){
	        unset($aBusinessHour['objectID']);

            if ( empty($aBusinessHour['firstCloseHour']) || empty($aBusinessHour['firstOpenHour']) ){
	            $aBusinessHour['firstOpenHour']     = NULL;
	            $aBusinessHour['firstCloseHour']    = NULL;
	            $aBusinessHour['firstOpenHourUTC']  = NULL;
	            $aBusinessHour['firstCloseHourUTC'] = NULL;
            }else{
	            $aBusinessHour['firstOpenHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['firstOpenHour'], $fromTimezone, 'H:i:s');

	            $aBusinessHour['firstCloseHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['firstCloseHour'], $fromTimezone, 'H:i:s');
            }

	        if ( empty($aBusinessHour['secondOpenHour']) || empty($aBusinessHour['secondCloseHour']) || ($aBusinessHour['secondOpenHour'] == $aBusinessHour['secondCloseHour'])  ){
		        $aBusinessHour['secondOpenHour']        = NULL;
		        $aBusinessHour['secondCloseHour']       = NULL;
		        $aBusinessHour['secondOpenHourUTC']     = NULL;
		        $aBusinessHour['secondCloseHourUTC']    = NULL;
	        }else{
		        $aBusinessHour['secondOpenHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['secondOpenHour'], $fromTimezone, 'H:i:s');
		        $aBusinessHour['secondCloseHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['secondCloseHour'], $fromTimezone, 'H:i:s');
            }

	        $wpdb->update(
		        $tbl,
		        $aBusinessHour,
		        array(
			        'ID' => $id
		        ),
		        array(
			        '%s',
			        '%s',
			        '%s',
			        '%s',
			        '%s',
			        '%s',
			        '%s',
			        '%s',
			        '%s'
		        ),
		        array(
			        '%d'
		        )
	        );
        }else{
	        if ( (empty($aBusinessHour['firstOpenHour']) || empty($aBusinessHour['firstCloseHour'])) ){
	            return false;
            }

	        $aBusinessHour = array_merge(array(
                'objectID' => $listingID,
                'dayOfWeek' => $dayOfWeek
            ), $aBusinessHour);

	        if ( !isset($aBusinessHour['secondOpenHour']) || !isset($aBusinessHour['secondCloseHour']) || empty($aBusinessHour['secondOpenHour']) || empty($aBusinessHour['secondCloseHour']) ) {
                unset($aBusinessHour['secondOpenHour']);
                unset($aBusinessHour['secondCloseHour']);

		        $aBusinessHour['firstOpenHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['firstOpenHour'], $fromTimezone, 'H:i:s');
		        $aBusinessHour['firstCloseHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['firstCloseHour'], $fromTimezone, 'H:i:s');

	            $wpdb->insert(
			        $tbl,
		            $aBusinessHour,
			        array(
				        '%d',
				        '%s',
				        '%s',
				        '%s',
				        '%s',
				        '%s',
				        '%s',
				        '%s',
				        '%s'
			        )
		        );
	        }else{
		        $aBusinessHour['firstOpenHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['firstOpenHour'], $fromTimezone, 'H:i:s');

		        $aBusinessHour['firstCloseHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['firstCloseHour'], $fromTimezone, 'H:i:s');

		        $aBusinessHour['secondOpenHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['secondOpenHour'], $fromTimezone, 'H:i:s');
		        $aBusinessHour['secondCloseHourUTC'] = Time::convertToTimezoneUTC($aBusinessHour['secondCloseHour'], $fromTimezone, 'H:i:s');

                $wpdb->insert(
                    $tbl,
	                $aBusinessHour,
                    array(
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                    )
                );
            }
        }
    }

    private function setDefaults($listingID){
	    $averageRating = GetSettings::getPostMeta($listingID, 'average_reviews');
	    if ( !$averageRating ){
		    SetSettings::setPostMeta($listingID, 'average_reviews', 0);
	    }

	    $countViewed = GetSettings::getPostMeta($listingID, 'count_viewed');
	    if ( !$countViewed ){
		    SetSettings::setPostMeta($listingID, 'count_viewed', 0);
	    }

	    $countShared = GetSettings::getPostMeta($listingID, 'count_shared');
	    if ( !$countShared ){
		    SetSettings::setPostMeta($listingID, 'count_shared', 0);
	    }

	    $countFavorites = GetSettings::getPostMeta($listingID, 'count_favorites');
	    if ( !$countFavorites ){
		    SetSettings::setPostMeta($listingID, 'count_favorites', 0);
	    }
    }

	public function saveSettings($listingID, $post, $updated){
		if ( !current_user_can('edit_theme_options') ){
			return false;
		}

		$aPostTypeKeys = General::getPostTypeKeys(true, true);
		if ( !in_array($post->post_type, $aPostTypeKeys) ){
		    return false;
        }

        $this->setDefaults($listingID);

		if ( isset($_POST['wilcity_business_hours']) && !empty($_POST['wilcity_business_hours']) ){
			$aData = $_POST['wilcity_business_hours'];
            $timezone = isset($_POST['wilcity_timezone']) ? $_POST['wilcity_timezone'] : '';
			self::saveBusinessHours($listingID, $aData, $timezone);
		}

		if( isset( $_POST['wilcity_belongs_to'] ) && !empty( $_POST['wilcity_belongs_to'] ) ) {
			$new_belong_id = absint( $_POST['wilcity_belongs_to'] );
			$old_belong_id = absint( GetSettings::getPostMeta($listingID, 'belongs_to') );
			if( $new_belong_id != $old_belong_id ) {
				$plan = GetSettings::getPlanSettings($new_belong_id);
				$menu_order = isset( $plan['menu_order'] ) ? absint($plan['menu_order']) : 0;
				self::saveMenuOrder($listingID, $menu_order);
			}
		}
		
		if ( isset($_POST['wilcity_my_posts']) && !empty($_POST['wilcity_my_posts']) ){
			$aData = $_POST['wilcity_my_posts'];
			
			foreach ($aData as $postOrder => $postID){
			    $postType = get_post_type($postID);
			    if ( empty($postType) || $postType != 'post' ){
			        unset($aData[$postOrder]);
                }
            }
			SetSettings::setPostMeta($listingID, 'my_posts', $aData);
		}
	}

	public static function saveMenuOrder($post_id, $menu_order) {
		global $wpdb;
		$table = $wpdb->prefix . 'posts';
		$status = $wpdb->update( 
			$table, 
			array( 
				'menu_order' => $menu_order
			), 
			array( 'ID' => $post_id ), 
			array( 
				'%d'
			), 
			array( '%d' ) 
		);

		return $status;
	}

	public static function saveBusinessHours($listingID, $aData, $timezone=''){
		SetSettings::setPostMeta($listingID, 'hourMode', $aData['hourMode']);
		SetSettings::setPostMeta($listingID, 'timeFormat', $aData['timeFormat']);

		if ( $aData['hourMode'] == 'open_for_selected_hours' ){
            foreach (wilokeListingToolsRepository()->get('general:aDayOfWeek') as $dayOfWeek => $name){
                $aBusinessHour = array();

                foreach ($aData['businessHours'][$dayOfWeek]['operating_times'] as $key => $val){
                    $aBusinessHour[sanitize_text_field($key)] = sanitize_text_field($val);
                }

                $aBusinessHour['isOpen'] = isset($aData['businessHours'][$dayOfWeek]['isOpen']) ? sanitize_text_field($aData['businessHours'][$dayOfWeek]['isOpen']) : 'no';
                self::updateBusinessHourTbl($listingID, $dayOfWeek, $aBusinessHour, $timezone);
            }
		}
	}

	public static function getBusinessHoursOfDay($listingID, $dayOfWeek){
	    if ( empty($listingID) ){
	        return false;
        }

        global $wpdb;
	    $tbl = $wpdb->prefix . AlterTableBusinessHours::$tblName;

	    $aBusinessHours = $wpdb->get_row(
            $wpdb->prepare(
                    "SELECT * FROM $tbl WHERE objectID=%d AND dayOfWeek=%s",
                $listingID, $dayOfWeek
            ),
            ARRAY_A
        );

        if ( empty($aBusinessHours) ){
            return false;
        }

        return $aBusinessHours;
    }

	public static function getBusinessHoursOfListing($listingID){
		if ( empty($listingID) ){
			return false;
		}

		global $wpdb;
		$tbl = $wpdb->prefix . AlterTableBusinessHours::$tblName;

		$aBusinessHours = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $tbl WHERE objectID=%d ORDER BY FIELD(dayOfWeek, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')",
				$listingID
			),
			ARRAY_A
		);

		if ( empty($aBusinessHours) ){
			return false;
		}

		return $aBusinessHours;
	}

	public function timezoneBox(){
		$postID = isset($_GET['post']) && !empty($_GET['post']) ? $_GET['post'] : '';

		if ( is_array($postID) ){
			return false;
		}

		$aListingTimezone = wilokeListingToolsRepository()->get('listing-settings:timezone');
        $aPostTypes = General::getPostTypeKeys(false, true);
        $aListingTimezone['object_types'] = $aPostTypes;
        new_cmb2_box($aListingTimezone);
    }

	public function registerMyProductsMetaBox(){
	    if ( !class_exists('WooCommerce') ){
	        return false;
        }

        if ( !$this->isDisableMetaBlock('myProducts')  ){
	        $aMyProducts = wilokeListingToolsRepository()->get('listing-settings:myProducts');
	        new_cmb2_box($aMyProducts);
        }


		if ( !$this->isDisableMetaBlock('myRoom')  ){
			$aMyRoom = wilokeListingToolsRepository()->get('listing-settings:myRoom');
			new_cmb2_box($aMyRoom);
		}

	}

	public function registerMyPosts(){
		if ( !$this->isDisableMetaBlock('myPosts')  ){
			$aMyPosts = wilokeListingToolsRepository()->get('listing-settings:myPosts');
			new_cmb2_box($aMyPosts);
		}
	}

	public function saveRestaurantMenu($postID, $post){
        if ( !isset($_POST['wilcity_added_fields']) || empty($_POST['wilcity_added_fields']) ){
            return false;
        }

        $aRestaurantMenuPrefixKeys = array('wilcity_group_title_', 'wilcity_group_description_', 'wilcity_group_icon_', 'wilcity_restaurant_menu_group_');
        $aNewGroupOrders = explode(',', $_POST['wilcity_added_fields']);
        foreach ($aNewGroupOrders as $order){
            foreach ($aRestaurantMenuPrefixKeys as $prefixKey){
                if ( isset($_POST[$prefixKey.$order]) ){
                    SetSettings::setPostMeta($postID, $prefixKey.$order, $_POST[$prefixKey.$order]);
                }
            }
        }
    }

	public function registerRestaurantMenu($addNew=false){
		if ( !$this->isDisableMetaBlock('myRestaurantMenu')  ){
		    if ( !$addNew ){
			    new_cmb2_box(wilokeListingToolsRepository()->get('listing-settings:myNumberRestaurantMenus'));
            }
			$aRestaurantMenu = wilokeListingToolsRepository()->get('listing-settings:myRestaurantMenu');

			$aGeneralSettings = $aRestaurantMenu['general_settings'];
			unset($aRestaurantMenu['general_settings']);

			$aGroupFields = $aRestaurantMenu['group_fields'];
			unset($aRestaurantMenu['group_fields']);

			$aGeneralGroupInfoSettings = $aGeneralSettings['general_settings'];
			$aGeneralGroupSettings     = $aGeneralSettings['group_settings'];

			if ( isset($_GET['post']) && !empty($_GET['post']) ){
				$numberOfMenus = GetSettings::getPostMeta($_GET['post'], 'number_restaurant_menus');
            }

            if ( empty($numberOfMenus) ){
	            $numberOfMenus = 1;
            }

            $originalFieldID = $aGeneralGroupSettings['id'];

            if ( $addNew ){
                $start = $numberOfMenus;
                $numberOfMenus = $numberOfMenus+1;
            }else{
                $start = 0;
            }
			for ($i = $start; $i < $numberOfMenus; $i++ ){
                $aMenuGroup = $aRestaurantMenu;

	            $aMenuGroup['id']    = $aRestaurantMenu['id'] . '_' . $i;
	            $aMenuGroup['title'] = $aRestaurantMenu['title'] . ' ' . $i;
	            $oCmbRepeat = new_cmb2_box($aMenuGroup);

	            foreach ($aGeneralGroupInfoSettings as $aGeneralField){
		            $aGeneralField['id'] = $aGeneralField['id'] . '_' . $i;
		            $oCmbRepeat->add_field($aGeneralField);
                }

                $aGroupFieldsSetting       = $aGeneralGroupSettings;
	            $aGroupFieldsSetting['id'] = $originalFieldID . '_' . $i;
	            $oGroupFieldInit = $oCmbRepeat->add_field($aGroupFieldsSetting);

	            foreach ($aGroupFields as $groupField){
		            $oCmbRepeat->add_group_field($oGroupFieldInit, $groupField);
	            }

            }
		}
	}

	public static function getMyProducts(){
        if ( !isset($_GET['post']) || empty($_GET['post']) ){
			return false;
		}

		return GetSettings::getPostMeta($_GET['post'], 'my_products');
	}

	public static function getMyPosts(){
		if ( !isset($_GET['post']) || empty($_GET['post']) ){
			return false;
		}
		return GetSettings::getPostMeta($_GET['post'], 'my_posts');
	}

	public static function getMyRoom(){
		if ( !isset($_GET['post']) || empty($_GET['post']) ){
			return false;
		}

		return GetSettings::getPostMeta($_GET['post'], 'my_room');
    }

	public function registerMetaBoxesUseCMBTwo(){
		$this->getPostTypes();
		$aSettings = GetSettings::getPromotionPlans();
		if ( empty($aSettings) ){
			return false;
		}

		$aPositions = array();
		foreach ($aSettings as $planKey => $aSetting){
			$aPositions[] = array(
				'type'      => 'text_datetime_timestamp',
				'id'        => 'wilcity_promote_'.$planKey,
				'name'      => $aSetting['name'] . ' (Expiration)'
			);
		}

		new_cmb2_box(array(
			'id'            => 'listing_ads_box',
			'title'         => 'Ads Positions',
			'context'       => 'normal',
			'object_types'  => $this->getPostTypes(),
			'priority'      => 'low',
			'show_names'    => true, // Show field names on the left
			'fields'        => $aPositions
		));
    }

	public function registerMetaBoxes(){
		$this->getPostTypes();

		if ( !$this->isDisableMetaBlock('businessHours')  ){
			add_meta_box( 'wilcity-business-hours', 'Business Hours', array($this, 'renderBusinessHourSettings'), $this->aPostTypes, 'normal' );
		}
	}

	public function listingScripts() {
		if ( General::isPostType( 'listing' ) ) {
			wp_enqueue_script( 'vuejs', WILOKE_LISTING_TOOL_URL . 'admin/assets/vue/vue.js', array(), '2.5.13', true );
		}
	}

	public function renderBusinessHourSettings($post){
        $aHours = General::generateBusinessHours();
		$timeFormat = GetSettings::getPostMeta($post->ID, 'timeFormat');
		$hourMode = GetSettings::getPostMeta($post->ID, 'hourMode');
		?>
		<div class="cmb2-wrap form-table">
			<div class="cmb2-metabox cmb-field-list">
				<div class="cmb-row cmb-type-select">
					<div class="cmb-th">
						<label for="wilcity_business_hourMode"><?php esc_html_e('Hour Mode', 'wiloke-listing-tools'); ?></label>
					</div>
					<div class="cmb-td">
						<select name="wilcity_business_hours[hourMode]" id="wilcity_business_hourMode" class="cmb2_select">
                            <option value="no_hours_available" <?php selected($hourMode, 'no_hours_available'); ?>><?php esc_html_e('No Hours Available', 'wiloke-listing-tools'); ?></option>
							<option value="open_for_selected_hours" <?php selected($hourMode, 'open_for_selected_hours'); ?>><?php esc_html_e('Open For Selected Hours', 'wiloke-listing-tools'); ?></option>
							<option value="always_open" <?php selected($hourMode, 'always_open'); ?>><?php esc_html_e('Always Open', 'wiloke-listing-tools'); ?></option>
						</select>
					</div>
				</div>
			</div>

			<div class="cmb2-metabox cmb-field-list">
				<div class="cmb-row cmb-type-select">
					<div class="cmb-th">
						<label for="wilcity_business_timeFormat"><?php esc_html_e('Time Format', 'wiloke-listing-tools'); ?></label>
					</div>
					<div class="cmb-td">
						<select name="wilcity_business_hours[timeFormat]" id="wilcity_business_timeFormat" class="cmb2_select">
							<option value="inherit" <?php selected($timeFormat, 'inherit'); ?>><?php esc_html_e('Inherit Theme Options', 'wiloke-listing-tools'); ?></option>
							<option value="12" <?php selected($timeFormat, 12); ?>><?php esc_html_e('12-Hour Format', 'wiloke-listing-tools'); ?></option>
							<option value="24" <?php selected($timeFormat, 24); ?>><?php esc_html_e('24-Hour Format', 'wiloke-listing-tools'); ?></option>
						</select>
					</div>
				</div>
			</div>

            <?php
            $wrapperBHClass = $hourMode != 'open_for_selected_hours' ? 'hidden cmb2-metabox cmb-field-list wilcity-bh-settings' : 'wilcity-bh-settings cmb2-metabox cmb-field-list';
            ?>
			<div class="<?php echo esc_attr($wrapperBHClass); ?>">
				<div class="cmb-row cmb-type-table">
					<div class="cmb-th">
						<label for="wilcity_business_hours_mode"><?php esc_html_e('Business Hours', 'wiloke-listing-tools'); ?></label>
                        <p>Warning: The timezone value is required. In case, you want to inherit the Timezone setting from General -> Settings, make sure that it's GMT format, please do not use UTC format.</p>
                        <p><i style="font-weight: normal">You can set the default Business Hours at Appearance -> Theme Options -> Listing</i></p>
					</div>
					<div class="cmb-td">
                        <div class="table-responsive">
                            <table class="table table-bordered profile-hour">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Business Hours</th>
                                        <th>Is Settings Available?</th>
                                    </tr>
                                </thead>

                                <?php
                                $aThemeOptions = \Wiloke::getThemeOptions(true);
                                $aDefaultBusinessHours = array(
                                    'firstOpenHour'     =>  isset($aThemeOptions['listing_default_opening_hour']) ? $aThemeOptions['listing_default_opening_hour'] : '',
                                    'firstCloseHour'    =>  isset($aThemeOptions['listing_default_closed_hour']) ? $aThemeOptions['listing_default_closed_hour'] : '',
                                    'secondOpenHour'    =>  isset($aThemeOptions['listing_default_second_opening_hour']) ? $aThemeOptions['listing_default_second_opening_hour'] : '',
                                    'secondCloseHour'   =>  isset($aThemeOptions['listing_default_second_closed_hour']) ? $aThemeOptions['listing_default_second_closed_hour'] : '',
                                    'isOpen'            => 'yes'
                                );

                                foreach ( wilokeListingToolsRepository()->get('general:aDayOfWeek') as $key => $day ) :
                                    if ( isset($_GET['post']) && !empty($_GET['post']) ){
                                        $aBusinessHours = self::getBusinessHoursOfDay($_GET['post'], $key);
                                    }

                                    if ( !isset($aBusinessHours) || empty($aBusinessHours) ){
                                        $aBusinessHours = $aDefaultBusinessHours;
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo esc_html($day); ?></td>
                                        <td>
                                            <div>
                                                <select name="wilcity_business_hours[businessHours][<?php echo esc_attr($key) ?>][operating_times][firstOpenHour]">
                                                    <option value="">---</option>
                                                    <?php foreach ($aHours as $aHour): ?>
                                                        <option value="<?php echo esc_attr($aHour['value']); ?>" <?php selected($aBusinessHours['firstOpenHour'], $aHour['value']); ?>><?php echo esc_attr($aHour['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <select name="wilcity_business_hours[businessHours][<?php echo esc_attr($key) ?>][operating_times][firstCloseHour]">
                                                    <option value="">---</option>
                                                    <?php foreach ($aHours as $aHour): ?>
                                                        <option value="<?php echo esc_attr($aHour['value']); ?>" <?php selected($aBusinessHours['firstCloseHour'], $aHour['value']); ?>><?php echo esc_attr($aHour['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div>
                                                <select name="wilcity_business_hours[businessHours][<?php echo esc_attr($key) ?>][operating_times][secondOpenHour]">
                                                    <option value="">---</option>
                                                    <?php foreach ($aHours as $aHour): ?>
                                                        <option value="<?php echo esc_attr($aHour['value']); ?>" <?php selected($aBusinessHours['secondOpenHour'], $aHour['value']); ?>><?php echo esc_attr($aHour['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <select name="wilcity_business_hours[businessHours][<?php echo esc_attr($key) ?>][operating_times][secondCloseHour]">
                                                    <option value="">---</option>
                                                    <?php foreach ($aHours as $aHour): ?>
                                                        <option value="<?php echo esc_attr($aHour['value']); ?>" <?php selected($aBusinessHours['secondCloseHour'], $aHour['value']); ?>><?php echo esc_attr($aHour['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <label for="bh-available-<?php echo esc_attr($key); ?>" class="input-checkbox">
                                                <input id="bh-available-<?php echo esc_attr($key); ?>" type="checkbox" name="wilcity_business_hours[businessHours][<?php echo esc_attr($key) ?>][isOpen]" value="yes" <?php echo isset($aBusinessHours['isOpen']) && $aBusinessHours['isOpen'] == 'yes' ? 'checked' : ''; ?> value="yes">
                                                <span></span>
                                            </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function getListingAddress($postID){
		if ( empty($postID) ){
			return self::$aDefault;
		}

		global $wpdb;
		$tbl = $wpdb->prefix . AlterTableLatLng::$tblName;

		$aResult = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM '.$tbl . ' WHERE objectID=%d',
				$postID
			),
			ARRAY_A
		);

		if ( empty($aResult) ){
			return self::$aDefault;
		}

		$aResult['googleMapUrl'] = esc_url('https://www.google.com/maps/search/' . urlencode($aResult['address']));
		return $aResult;
	}

	public static function isUpdate($objectID){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTableLatLng::$tblName;

		$id = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT ID FROM '.$tbl . ' WHERE objectID=%d',
				$objectID
			)
		);

		return !empty($id);
	}

	public static function removeGoogleAddress($objectID) {
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTableLatLng::$tblName;
		$status = $wpdb->delete(
			$tbl,
			array(
				'objectID' => $objectID
			),
			array(
				'%d'
			)
		);

		return $status;
	}
	public static function saveData($objectID, $aGoogleAddress){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTableLatLng::$tblName;

		if ( self::isUpdate($objectID) ){
			$wpdb->update(
				$tbl,
				$aGoogleAddress,
				array(
					'objectID' => $objectID
				),
				array(
					'%s',
					'%s',
					'%s'
				),
				array(
					'%d'
				)
			);
		}else{
			$aGoogleAddress['objectID'] = $objectID;
			$wpdb->insert(
				$tbl,
				$aGoogleAddress,
				array(
					'%s',
					'%s',
					'%s',
					'%d'
				)
			);
		}
	}

	public function savePWMAP($override_value, $value, $object_id, $field_args){
	    if ( empty($value['lat']) || empty($value['lng']) || empty($value['address']) ){
	        return false;
        }

		$aGoogleAddress['lat'] = floatval($value['lat']);
		$aGoogleAddress['lng'] = floatval($value['lng']);
		$aGoogleAddress['address'] = $value['address'];

		self::saveData($object_id, $aGoogleAddress);
	}

	public function renderSocialNetworks($field, $fieldEscapedValue, $fieldObjectID, $fieldObjectType, $oFieldType){

	    switch ($field->args('is')){
            case 'usermeta':
	            $aSocialNetworks = GetSettings::getUserMeta($fieldObjectID, 'social_networks');
                break;
            default:
	            $aSocialNetworks = GetSettings::getPostMeta($fieldObjectID, 'social_networks');
                break;
        }

	    foreach (\WilokeSocialNetworks::$aSocialNetworks as $socialKey){
	        ?>
            <div>
                <label><strong><?php echo ucfirst($socialKey); ?></strong></label>
                <?php
                echo $oFieldType->input( array(
                    'title'      => ucfirst($socialKey),
                    'type'       => 'text',
                    'name'       => $field->args('_name') . '['.$socialKey.']',
                    'value'      => isset($aSocialNetworks[$socialKey]) ? $aSocialNetworks[$socialKey] : '',
                    'class'      => 'large-text',
                    'desc'       => '',
                ) );
                ?>
            </div>
            <?php
        }
	}
}