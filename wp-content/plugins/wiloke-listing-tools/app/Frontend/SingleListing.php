<?php
namespace WilokeListingTools\Frontend;

use phpDocumentor\Reflection\File;
use Stripe\Util\Set;
use WILCITY_SC\SCHelpers;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Submission;
use WilokeListingTools\Framework\Helpers\TermSetting;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Models\ViewStatistic;

class SingleListing extends Controller {
    public static $aSidebarOrder;
    public static $aNavOrder;
    protected static $aGroupVal = array();
    protected static $groupKey = '';
	private static $aConvertNavKeysToListingPlanKeys = array(
		'photos' => 'gallery',
		'tags'   => 'listing_tag'
	);

	private static $aDefaultSidebarKeys = array();

	private static $isTCountDownSC = false;

	private static $aAdminOnly = array(
		'google_adsense',
        'google_adsense_2',
        'google_adsense_1'
	);

	private static $aListingPromotionShownUp = array();

	public function __construct() {
//		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wilcity_listing_settings', array($this, 'saveSettings'));
		add_action('wp_ajax_wilcity_save_single_sidebar_settings', array($this, 'saveSidebarSettings'));
		add_action('wp_ajax_wilcity_listing_set_navigation_mode', array($this, 'saveNavigationMode'));
		add_action('wp_ajax_wilcity_fetch_single_gallery', array($this, 'fetchGallery'));
		add_action('wp_ajax_nopriv_wilcity_fetch_single_gallery', array($this, 'fetchGallery'));
		add_action('wp_ajax_wilcity_fetch_single_video', array($this, 'fetchVideoGallery'));
		add_action('wp_ajax_nopriv_wilcity_fetch_single_video', array($this, 'fetchVideoGallery'));
		add_action('wp_ajax_wilcity_fetch_content', array($this, 'fetchContent'));
		add_action('wp_ajax_nopriv_wilcity_fetch_content', array($this, 'fetchContent'));
		add_action('wp_ajax_nopriv_wilcity_get_restaurant_menu', array($this, 'fetchRestaurant'));
		add_action('wp_ajax_wilcity_get_restaurant_menu', array($this, 'fetchRestaurant'));

		add_action('wp_ajax_wilcity_fetch_custom_content', array($this, 'fetchCustomContent'));
		add_action('wp_ajax_nopriv_wilcity_fetch_custom_content', array($this, 'fetchCustomContent'));

		add_action('wp_ajax_wilcity_get_tags', array($this, 'fetchTags'));
		add_action('wp_ajax_nopriv_wilcity_get_tags', array($this, 'fetchTags'));


		add_filter('wilcity/nav-order', array($this, 'removeTagIfPlanIsDisabled'), 10, 1);
		add_filter('wilcity/single-listing/tabs', array($this, 'removeDisableNavigationByListingPlan'), 10, 2);
//		add_action('wp_head', array($this, 'insertGoogleAds'));
		add_filter('the_content', array($this, 'insertGoogleAdsToContent'));
		add_filter('body_class', array($this, 'addPlanClassToSingleListing'));
		add_filter('wilcity/single-listing/fetchCustomContent', array($this, 'modifyColumnRelationshipShortcode'));
	}

	public static function getAdminOnly(){
	    return self::$aAdminOnly;
    }

    public static function renderTabTitle($tabName){
	    global $post;
	    return $tabName . ' - ' . $post->post_title;
    }

	public static function getRestaurantMenu($postID, $wrapperClass = ''){
        if (!function_exists('wilcityRenderRestaurantListMenu')){
			return '';
		}

		$numberOfMenus = GetSettings::getPostMeta($postID, 'number_restaurant_menus');

        $hasZero = GetSettings::getPostMeta($postID, 'restaurant_menu_group_0');
        if (empty($numberOfMenus) && !empty($hasZero)) {
            $numberOfMenus = 1;
        }

		if ( empty($numberOfMenus) ){
			return '';
		}

		$aMenus = array();
		for( $i = 0; $i < $numberOfMenus; $i++ ){
			$aMenu = GetSettings::getPostMeta($postID, 'restaurant_menu_group_'.$i);
			if ( !empty($aMenu) ){
				$aMenus['restaurant_menu_group_'.$i]['items'] = $aMenu;
				$aMenus['restaurant_menu_group_'.$i]['wrapper_class']       = 'wilcity_restaurant_menu_group_'.$i . ' ' . $wrapperClass;
				$aMenus['restaurant_menu_group_'.$i]['group_title']         = GetSettings::getPostMeta($postID, 'group_title_'.$i);
				$aMenus['restaurant_menu_group_'.$i]['group_description']   = GetSettings::getPostMeta($postID, 'group_description_'.$i);
				$aMenus['restaurant_menu_group_'.$i]['group_icon']          = GetSettings::getPostMeta($postID, 'group_icon_'.$i);
			}
		}

		return $aMenus;
	}

	/*
	 * If this shortcode is under Listing Tab, We will modify its column
	 *
	 * @since 1.2.0
	 */
	public function modifyColumnRelationshipShortcode($sc){
        if ( strpos($sc, 'wilcity_render_listing_type_relationships') !== false ){
            $scType = SCHelpers::getCustomSCClass($sc);
            $sc = str_replace(']', ' maximum_posts_on_lg_screen="col-md-4" maximum_posts_on_md_screen="col-md-4" extra_class="'.$scType.' clearfix"]', $sc);
        }
        return $sc;
    }

	public static function isClaimedListing($listingID, $isFocus=false){
		if ( !$isFocus && !\WilokeThemeOptions::isEnable('listing_toggle_contact_info_on_unclaim') ){
		    return true;
        }

        return GetSettings::getPostMeta($listingID, 'claim_status') == 'claimed';
    }

	public static function setListingPromotionShownUp($listingID){
	    self::$aListingPromotionShownUp[] = $listingID;
    }

	public static function getListingsPromotionShownUp($isIncludedCurrentPostID=true){
	    global $post;
	    if ( $isIncludedCurrentPostID ){
		    self::$aListingPromotionShownUp[] = $post->ID;
        }
		return defined('WILCITY_IGNORE_DUPLICATE_PROMOTION') ? array() : self::$aListingPromotionShownUp;
	}

	public function addPlanClassToSingleListing($aClasses){
        if ( is_singular() ){
            global $post;
	        $belongsToPlanClass = GetSettings::getSingleListingBelongsToPlanClass($post->ID);
	        if ( !empty($belongsToPlanClass) ){
		        $aClasses[] = $belongsToPlanClass;
	        }
        }
        return $aClasses;
    }

	public function insertGoogleAdsToContent($content){
		global $wiloke, $post;
		$aPostTypes = General::getPostTypeKeys(false, false);
		if ( !is_singular($aPostTypes) ){
			return $content;
		}

		if ( !isset($wiloke->aThemeOptions['google_adsense_client_id']) || empty($wiloke->aThemeOptions['google_adsense_client_id']) || empty($wiloke->aThemeOptions['google_adsense_slot_id']) || ($wiloke->aThemeOptions['google_adsense_directory_content_position'] == 'disable') ){
			return $content;
		}

		if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_google_ads') ){
			return $content;
		}

		if ( $wiloke->aThemeOptions['google_adsense_directory_content_position'] == 'above' ){
			$content = '[wilcity_in_article_google_adsense]' . $content;
		}else{
			$content .= '[wilcity_in_article_google_adsense]';
		}
		return $content;
	}

	public function removeDisableNavigationByListingPlan($aTabs, $post){
		$planID = GetSettings::getPostMeta($post->ID, 'belongs_to');
		if ( empty($planID) ){
			return $aTabs;
		}
		foreach ($aTabs as $order => $aTab){
			$tabKey = isset(self::$aConvertNavKeysToListingPlanKeys[$aTab['key']]) ? self::$aConvertNavKeysToListingPlanKeys[$aTab['key']] : $aTab['key'];
			$tabKey = str_replace('wilcity_single_navigation_', '', $tabKey);
			if ( !GetSettings::isPlanAvailableInListing($post->ID, $tabKey) ){
				unset($aTabs[$order]);
			}
		}
		return $aTabs;
	}

	public static function isElementorEditing(){
		if ( !current_user_can('edit_theme_options') || !is_admin() ){
			return false;
		}

		if ( !isset($_REQUEST['action']) || ($_REQUEST['action'] != 'elementor') ){
			return false;
		}

		return true;
	}

	public static function parseCustomFieldSC($content, $key='', $postID=''){
		if ( strpos($content, 'wilcity') === false ) {
			preg_match( '/(?:group_key={{)([^}]*)(?:}})/', $content, $aMatches );

			if ( !empty($postID) ){
				$post = get_post($postID);
            }else{
				global $post;
            }
			$isGroupField = false;

			if ( isset( $aMatches[1] ) ) {
				self::$groupKey = $aMatches[1];
				$isGroupField   = true;
				if ( ! GetSettings::isPlanAvailableInListing( $post->ID, self::$groupKey ) ) {
					return '';
				}
			}

			if ( ! empty( $key ) ) {
				if ( !GetSettings::isPlanAvailableInListing( $post->ID, $key ) ) {
					return '';
				}
			}
			self::$isTCountDownSC = strpos( $content, 'countdown' ) !== false;

			preg_match_all( '/(?:{{)([^}]*)(?:}})/', $content, $aMatches );

			if ( isset( $aMatches[1] ) && ! empty( $aMatches[1] ) ) {
				if ( $isGroupField ) {
					if ( empty( $aGroupVal ) ) {
						$aGroupVal = GetSettings::getPostMeta( $post->ID, self::$groupKey );
					}

					foreach ($aMatches[1] as $keyVal){
					    if ( strpos($keyVal, 'is_content') !== false ){
						    $isContent = true;
					        $realKey = str_replace('is_content_', '', $keyVal);
                        }else{
						    $isContent = false;
					        $realKey = $keyVal;
                        }

						if ( isset( $aGroupVal[$realKey] ) ) {
							$getVal = is_array( $aGroupVal[$realKey] ) ? implode( ',', $aGroupVal[$realKey] ) : $aGroupVal[$realKey];
							if ( !$isContent ) {
								$getVal = '"' . $getVal . '"';
							}
							$content = str_replace('{{'.$keyVal.'}}', $getVal, $content);
						}
                    }
				} else {
					$getVal = GetSettings::getPostMeta( $post->ID, 'custom_' . $aMatches[1][0] );
					$getVal = is_array( $getVal ) ? implode( ',', $getVal ) : $getVal;

					if ( strpos($aMatches[1][0], 'is_content') !== false ){
						$isContent = true;
					}else{
						$isContent = false;
					}

					if ( self::$isTCountDownSC ) {
						$getVal = date( 'Y-d-m H:i:s', strtotime( $getVal ) );
					}

					if ( $isContent ){
					    $getVal = '"' . $getVal . '"';
                    }
					$content = str_replace($aMatches[1][0], $getVal, $content);
				}
			}
			$content = str_replace(array('{{', '}}'), array('"', '" post_id="'. $postID .'"'), $content);

			self::$groupKey       = '';
			self::$aGroupVal      = '';
			self::$isTCountDownSC = false;

			return trim( $content );
		}

		$content = str_replace(array('{{', '}}'), array('"', '" post_id="'. $postID .'"'), $content);

		return $content;
	}

	public function removeTagIfPlanIsDisabled($aTabs){
		if ( !is_single() ){
			return $aTabs;
		}
		global $post;

		if ( empty($aTabs) || !is_array($aTabs) ){
		    return $aTabs;
        }

		foreach ($aTabs as $tab => $aSetting){
			if ( $tab == 'photos' ){
				$key = 'gallery';
			}else{
				$key = $tab;
			}
			if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_'.$key) ){
				unset($aTabs[$tab]);
			}
		}

		return $aTabs;
	}

	public static function getMapIcon($post){
	    $primaryCatID = get_post_meta($post->id,'_yoast_wpseo_primary_product_cat',true);
		$iconURL = apply_filters('wiloke-listing-tools/map-icon-url-default', get_template_directory_uri() . '/assets/img/map-icon.png');

		if($primaryCatID){
			$oListingCat = get_term($primaryCatID, 'listing_cat');
			if ( !empty($oListingCat) && is_wp_error($oListingCat) ){
			    $iconURL = TermSetting::getTermIcon($oListingCat);
            }
		}else{
		    $aTerms = GetSettings::getPostTerms($post->ID, 'listing_cat');
			if ( !empty($aTerms) && !is_wp_error($aTerms) ){
				$iconURL = TermSetting::getTermIcon($aTerms[0]);
			}
        }

        return $iconURL;
    }

	public function fetchTags(){
		$listingID = $_GET['postID'];
		$post_type = get_post_field('post_type', $listingID);
		$aTags = GetSettings::getPostTerms($listingID, 'listing_tag');
		if ( $aTags ){
			ob_start();
			?>
			<div class="row">
				<?php
				foreach ($aTags as $oTag) :
					if ( !empty($oTag) && !is_wp_error($oTag) ) :
						?>
						<div class="col-sm-3 fix">
							<div class="icon-box-1_module__uyg5F three-text-ellipsis mt-20 mt-sm-15">
								<div class="icon-box-1_block1__bJ25J">
									<?php echo \WilokeHelpers::getTermIcon($oTag, 'icon-box-1_icon__3V5c0 rounded-circle', true, array( 'type'=>$post_type) ); ?>
								</div>
							</div>
						</div>
						<?php
					endif;
				endforeach;
				?>
			</div>
			<?php
			$content = ob_get_contents();
			ob_end_clean();
			wp_send_json_success(array(
				'content' => $content
			));
		}else{
			wp_send_json_error(array(
				'msg' => esc_html__('Whoops! We found no features of this listing', 'wiloke-listing-tools')
			));
		}
	}

	public function fetchCustomContent(){
		$aDefaultNavOrder = GetSettings::getOptions(General::getSingleListingSettingKey('navigation', get_post_type($_POST['postID'])));
		$errMsg = esc_html__('Whoops! We do not found this tab content', 'wiloke-listing-tools');
		if ( empty($aDefaultNavOrder) || !isset($aDefaultNavOrder[$_POST['sectionID']]) ){
			wp_send_json_error(array(
				'msg' => $errMsg
			));
		}
		$content = self::parseCustomFieldSC($aDefaultNavOrder[$_POST['sectionID']]['content'], $_POST['sectionID'], $_POST['postID']);
		if ( empty($content) ){
			wp_send_json_error(array(
				'msg' => $errMsg
			));
		}

		$content = apply_filters('wilcity/single-listing/fetchCustomContent', $content);

		ob_start();
		echo do_shortcode(stripslashes($content));
		$content = ob_get_contents();
		ob_end_clean();
		if ( empty($content) ){
			wp_send_json_error(array(
				'msg' => $errMsg
			));
		}
		wp_send_json_success($content);
	}

	public function fetchRestaurant(){
	    $aMenus = self::getRestaurantMenu($_GET['postID']);
	    if ( !is_array($aMenus) ){
		    wp_send_json_error(array(
			    'msg' => esc_html__('There are no menus', 'wiloke-listing-tools')
		    ));
        }

        $aResponse = array();
		foreach ($aMenus as $key => $aMenu){
			$aResponse[] = wilcityRenderRestaurantListMenu($aMenu, $_GET['postID'], true);
		}

        wp_send_json_success(array('aMenus'=>$aResponse));
    }

	public function fetchContent(){
		$content = get_post_field('post_content', $_GET['postID']);
		$content = apply_filters('the_content', $content);

		if ( empty($content) ){
			wp_send_json_error(array(
				'msg' => esc_html__('The content is empty', 'wiloke-listing-tools')
			));
		}
		wp_send_json_success($content);
	}

	public function successMsg(){
	    wp_send_json_success(array('msg'=>esc_html__('The settings have been updated', 'wiloke-listing-tools')));
    }

	public function saveNavigationMode(){
		$this->middleware(['isPostAuthor'], array(
			'postAuthor' => get_current_user_id(),
			'postID' => $_POST['postID'],
			'passedIfAdmin' => true
		));

		SetSettings::setPostMeta($_POST['postID'], wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('isUsedDefaultNav'), sanitize_text_field($_POST['isUsedDefault']));
		$this->successMsg();
    }

	public function saveSidebarSettings(){
	    $this->middleware(['isPostAuthor'], array(
            'postAuthor' => get_current_user_id(),
            'postID' => $_POST['postID'],
            'passedIfAdmin' => true
        ));

	    if ($_POST['isUsedDefaultSidebar']  == 'yes'){
            SetSettings::setPostMeta($_POST['postID'], wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('isUsedDefaultSidebar'), 'yes');
        }else{
		    SetSettings::setPostMeta($_POST['postID'], wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('isUsedDefaultSidebar'), 'no');

		    $aSidebarSections = array();
		    foreach ($_POST['data'] as $aRawSection){
			    $aSection = array();
		        foreach ($aRawSection as $key => $val){
		            $aSection[sanitize_text_field($key)] = sanitize_text_field($val);
                }
			    $aSidebarSections[sanitize_text_field($aSection['key'])] = $aSection;
            }

		    SetSettings::setPostMeta($_POST['postID'], wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('sidebar', true)->sub('settings'), $aSidebarSections);
        }
		$this->successMsg();
    }

	public static function parseVideos($aRawVideos, $postID){
	    global $wiloke;

		$defaultThumb = isset($wiloke->aThemeOptions['listing_video_thumbnail']['src']) ? $wiloke->aThemeOptions['listing_video_thumbnail']['src'] : '';
		if ( empty($aRawVideos) ){
			return array();
		}

		foreach ($aRawVideos as $order => $aVideo){
			if ( !isset($aVideo['thumbnail']) || empty($aVideo['thumbnail']) ){
				if ( strpos($aVideo['src'], 'youtube') !== false ){
					$aParseVideo = explode('?v=', $aVideo['src']);
					$videoID = end($aParseVideo);
					$aRawVideos[$order]['thumbnail'] = 'https://img.youtube.com/vi/'.$videoID.'/hqdefault.jpg';
				}else if ( strpos($aVideo['src'], 'vimeo') !== false ){
					$aParseVideo = explode('/', $aVideo['src']);
					$videoID = end($aParseVideo);
					$aThumbnails = \WilokeHelpers::getVimeoThumbnail($videoID);
					$aRawVideos[$order] = array_merge($aRawVideos[$order], $aThumbnails);
				}else{
					$aRawVideos[$order]['thumbnail'] = $defaultThumb;
				}
				\WilokeListingTools\Framework\Helpers\SetSettings::setPostMeta($postID, 'video_srcs', $aRawVideos);
			}
		}
		return $aRawVideos;
    }

	public function fetchVideoGallery(){
		$msg = esc_html__('There are no Videos', 'wiloke-listing-tools');
		if ( empty($_GET['postID']) ){
			wp_send_json_success(array(
				'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
			));
		}

		$aVideoGallery = GetSettings::getPostMeta($_GET['postID'], 'video_srcs');

		if ( empty($aVideoGallery) ){
			wp_send_json_error(array(
				'msg' => $msg
			));
		}
		$aVideoGallery = \WilokeListingTools\Frontend\SingleListing::parseVideos($aVideoGallery, $_GET['postID']);
		wp_send_json_success($aVideoGallery);
    }

	public function fetchGallery(){
		$msg = esc_html__('There are no photos', 'wiloke-listing-tools');
		if ( empty($_GET['postID']) ){
			wp_send_json_error(array(
				'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
			));
		}

		$aGalleryIDs = GetSettings::getPostMeta($_GET['postID'], 'gallery');
		if ( empty($aGalleryIDs) ){
			wp_send_json_error(array(
				'msg' => $msg
			));
		}

		$aGallery = array();

		foreach ($aGalleryIDs as $galleryID => $link){
			$aItem['title']      = get_the_title($galleryID);
			$aItem['medium']     = wp_get_attachment_image_url($galleryID, 'medium');
			$aItem['full']       = wp_get_attachment_image_url($galleryID, 'full');
			$aGallery[] = $aItem;
		}

		wp_send_json_success($aGallery);
	}

	public function printContent(){
		Inc::file('single-listing:content');
    }

	public function printNavigation($post){
        $aNavigationSettings = GetSettings::getPostMeta($post->ID, wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('navigation'));

        Inc::file('single-listing:navtop');
    }

	public function saveSettings(){
        $this->middleware(['designListingRoles'], array(
            'postID' => $_POST['postID']
        ));

        switch ($_POST['mode']){
            case 'navigation':
                $aNavOrder = array();
                foreach ($_POST['value'] as $aItem){
                    if ( !current_user_can('administrator') && in_array($aItem['key'], self::$aAdminOnly) ){
                        continue;
                    }
	                $aNavOrder[$aItem['key']] = $aItem;
                }
                SetSettings::setPostMeta($_POST['postID'], wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('navigation'), $aNavOrder);
                break;
	        case 'general':
		        SetSettings::setPostMeta($_POST['postID'], wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('general'), $_POST['value']);
		        break;
        }

        wp_send_json_success(array(
        	'msg' => esc_html__('Congratulations! Your setting have been updated successfully', 'wiloke-listing-tools')
        ));
    }

    public static function getNavOrder($post=null){
	    if ( empty($post) ){
		    global $post;
		}

	    if ( !empty(self::$aNavOrder) ){
	        return self::$aNavOrder;
        }

        $usingCustomSettings = GetSettings::getPostMeta($post->ID, wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('isUsedDefaultNav'));
        if ( $usingCustomSettings == 'no' ){
	        $aIndividualNavOrder = GetSettings::getPostMeta($post->ID, wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('navigation'));
        }

	    $aDefaultNavOrder = GetSettings::getOptions(General::getSingleListingSettingKey('navigation', $post->post_type));

	    if ( empty($aIndividualNavOrder) ){
	        self::$aNavOrder =  apply_filters('wilcity/nav-order', $aDefaultNavOrder);
	        return self::$aNavOrder;
        }

	    self::$aNavOrder = $aIndividualNavOrder + $aDefaultNavOrder;

	    foreach (self::$aNavOrder as $key => $aVal) {

			if( isset( $aDefaultNavOrder[$key]['name'] ) ) {
				self::$aNavOrder[$key]['name'] = $aDefaultNavOrder[$key]['name'];
				if ( !isset( $aVal['isCustomSection'] ) || $aVal['isCustomSection'] == 'no' ) {
					continue;
				}
				if ( !isset($aDefaultNavOrder[$key]) ){
					unset(self::$aNavOrder[$key]);
				}else{
					self::$aNavOrder[$key]['content'] = $aDefaultNavOrder[$key]['content'];
				}
			}
	    }

		self::$aNavOrder = apply_filters('wilcity/nav-order', self::$aNavOrder);

	    return self::$aNavOrder;
    }

    public static function getSidebarOrder($post=null){
    	if ( empty($post) ){
		    global $post;
	    }

	    $isUsedDefaultSidebar = GetSettings::getPostMeta($post->ID, wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('isUsedDefaultSidebar'));

	    if ( !empty(self::$aSidebarOrder) ){
	        return self::$aSidebarOrder;
		}
		
	    $aGeneralSidebarSettings = GetSettings::getOptions(General::getSingleListingSettingKey('sidebar', $post->post_type));
		
	    if ( empty($isUsedDefaultSidebar) || $isUsedDefaultSidebar == 'yes' ){
	        self::$aSidebarOrder = $aGeneralSidebarSettings;
			self::$aSidebarOrder = apply_filters('wilcity/sidebar-order', self::$aSidebarOrder);
		    return self::$aSidebarOrder;
        }

	    $aIndividualSidebarSettings = GetSettings::getPostMeta($post->ID, wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('sidebar', true)->sub('settings'));
		
	    if ( !empty($aIndividualSidebarSettings) ){
			
		    self::$aSidebarOrder = $aIndividualSidebarSettings;
			
		    $aCustomSectionKeys = array_keys($aIndividualSidebarSettings);

		    foreach ($aGeneralSidebarSettings as $key => $aSection){
		    	if ( !in_array($key, $aCustomSectionKeys) ){
				    self::$aSidebarOrder[$key] = $aSection;
			    }else {
					self::$aSidebarOrder[$key] = $aSection;
					if( isset($aIndividualSidebarSettings['status'] ) ) {
						self::$aSidebarOrder[$key]['isTemporaryDisable'] = $aIndividualSidebarSettings['status'];
					}
			    }
			    self::$aSidebarOrder[$key]['name'] = $aSection['name'];
		    }

		    self::$aDefaultSidebarKeys = array_keys($aGeneralSidebarSettings);

		    self::$aSidebarOrder = array_filter(self::$aSidebarOrder, function($aSidebar){
                return in_array($aSidebar['key'], self::$aDefaultSidebarKeys);
            });
	    }else{
		    self::$aSidebarOrder = $aGeneralSidebarSettings;
	    }

	    self::$aSidebarOrder = apply_filters('wilcity/sidebar-order', self::$aSidebarOrder);
	    return self::$aSidebarOrder;
    }

    public static function getDefaultNavKeys(){
    	$aNavigation = wilokeListingToolsRepository()->get('listing-settings:navigation');
    	$aFixedKeys = array_keys($aNavigation['fixed']);
    	$aRest = array_keys($aNavigation['draggable']);

    	$aRest[] = 'listing-settings';

    	return array_merge($aFixedKeys, $aRest);
    }
}
