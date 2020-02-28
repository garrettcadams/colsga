<?php
namespace WilokeListingTools\Controllers;


use WilokeListingTools\AlterTable\AlterTableEventsData;
use WilokeListingTools\Framework\Helpers\AjaxMsg;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\HTML;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Submission;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\EventModel;
use WilokeListingTools\Models\UserModel;

class EventController extends Controller {
	use SingleJsonSkeleton;
	use SetListingBelongsToPlanID;
	use Validation;
	use GetSingleImage;
	use SetPlanRelationship;
	use PrintAddListingSettings;
	use SetPostDuration;
	use GetWilokeToolSettings;
	use MergingSettingValues;
	use BelongsToCategories;
	use BelongsToLocation;
	use BelongsToTags;
	use SetCustomSections;
	use SetGeneral;
	use InsertImg;
	use SetContactInfo;
	use InsertAddress;
	use InsertGallery;
	use SetVideo;
	use SetProductsToListing;
	use SetPriceRange;
	use SetSinglePrice;
	use HandleSubmit;
	use SetSocialNetworks;
	use SetGroupData;
	use InsertFeaturedImg;

	protected $aPlanSettings = array();
	public $aData = array();
	public $postType    = 'event';
	public $postStatus;
	public $eventPlanPostType  = 'event_plan';
	public $parentListingID;
	protected $postID   = null;
	protected $listingID;
	protected $planID;
	protected $isNewListing=true;
	public static $aEventsData = array();
	public static $aEventSkeleton = array(
		'name'          => '',
		'content'       => '',
		'img'           => '',
		'video'         => '',
		'weekly'        => '',
		'daily'         => '',
		'occurs_once'   => '',
		'frequency'     => '',
		'address'       => array(
			'address' => '',
			'lat'     => '',
			'lng'     => ''
		)
	);

	public $aEventsDataAndPrepares = array(
		'parentID' => '%d',
		'frequency'=> '%s',
		'starts'   => '%s',
		'endsOn'   => '%s',
		'openingAt'=> '%s',
		'closedAt' => '%s',
		'timezone' => '%s',
		'specifyDays' => '%s',
		'weekly'   => array(
			'specifyDays' => '%s'
		),
		'googleAddress' => array(
			'address' => '%s',
			'lat' => '%s',
			'lng' => '%s'
		),
		'address' => array(
			'address' => '%s',
			'lat' => '%s',
			'lng' => '%s'
		)
	);

	public function __construct() {
		add_action('wp_ajax_wilcity_edit_event', array($this, 'editEvent'));
		add_action('wp_ajax_wilcity_get_event_data', array($this, 'getEventItemData'));
		add_action('save_post_event', array($this, 'updatedEventDataViaBackend'), 10, 3);
		add_action('wiloke-listing-tools/before-payment-process/listing_plan', array($this, 'setPlanRelationshipBeforePayment'), 10, 1);
		add_action('wiloke-listing-tools/before-payment-process/event_plan', array($this, 'setPlanRelationshipBeforePayment'), 10, 1);
		add_action('wp_ajax_wilcity_fetch_events', array($this, 'fetchEvents'));
		add_action('wp_ajax_nopriv_wilcity_fetch_events', array($this, 'fetchEvents'));
		add_filter('wilcity/ajax/post-comment/event', array($this, 'ajaxBeforePostComment'));
		add_filter('wilcity/determining/reviewPostType/of/event', array($this, 'setReviewPostType'));
		add_filter('wilcity/addMiddlewareToReview/of/event', array($this, 'addMiddleWareToReviewHandler'));
		add_filter('wilcity/addMiddlewareOptionsToReview/of/event', array($this, 'addMiddleWareOptionsToReviewHandler'));
		add_action('wiloke/wilcity/addlisting/print-fields/event', array($this, 'printAddEventFields'));
		add_action('wiloke/wilcity/addlisting/print-sidebar-items/event', array($this, 'printAddEventSidebars'));
		add_action('wp_ajax_wilcity_handle_review_event', array($this, 'handlePreview'));
		add_action('wilcity/addlisting/validation/event_calendar', array($this, 'validateEventCalendar'), 10, 2);
		add_action('wilcity/addlisting/validation/event_belongs_to_listing', array($this, 'validateEventBelongsToListing'), 10, 2);
		add_action('wp_ajax_wilcity_fetch_events_json', array($this, 'fetchEventsJson'));
		add_action('wp_ajax_wilcity_load_more_events', array($this, 'loadMoreListings'));
		add_action('wp_ajax_nopriv_wilcity_load_more_events', array($this, 'loadMoreListings'));
		add_action('wp_ajax_wilcity_fetch_count_author_event_types', array($this, 'countAuthorEventTypes'));

		add_action('wilcity/single-event/calendar', array(__CLASS__, 'renderEventCalendar'), 10, 2);
		add_action('wilcity/single-event/meta-data', array($this, 'renderEventMetaData'), 10, 1);
		add_filter('wilcity/dashboard/navigation', array($this, 'profileNavigation'));

//        add_action('wilcity_check_event_status', array($this, 'moveExpiryEventToTrash'));
//        add_action('init', array($this, 'moveExpiryEventToTrash'));
        add_filter('wilcity/filter-listing-slider/meta-data', array($this, 'addDataToEventGrid'), 10, 3);
        add_filter('wilcity/wilcity-mobile-app/post-event-discussion', array($this, 'appBeforePostComment'), 10, 2);
        add_filter('wilcity/wilcity-mobile-app/put-event-discussion', array($this, 'appBeforeUpdateComment'), 10, 3);
	}

	public function addDataToEventGrid($aListing, $post){
		$aEventCalendarSettings = GetSettings::getEventSettings($post->ID);
		$aListing['interestedClass'] = UserModel::isMyFavorite($post->ID) ? 'la la-star color-primary' : 'la la-star-o';
		$aListing['hostedByName'] = GetSettings::getEventHostedByName($post);
		$aListing['hostedByURL'] = GetSettings::getEventHostedByUrl($post);
		$aListing['hostedByTarget'] = GetSettings::getEventHostedByTarget($aListing['hostedByURL']);

		$aListing['startAt'] = date_i18n('d', strtotime($aEventCalendarSettings['startsOn']));
		$aListing['startsOn'] = date_i18n('M', strtotime($aEventCalendarSettings['startsOn']));
		return $aListing;
    }

	public static function getEventStatuses($isKey=true){
	    $aTranslation = GetSettings::getTranslation();
	    if ( !$isKey ){
	        return $aTranslation['aEventStatus'];
        }

        return array_map(function($aEventStatus){
            return array($aEventStatus['post_status']);
        }, $aTranslation['aEventStatus']);
    }

	public function moveExpiryEventToTrash(){
        global $wpdb;
        $postsTbl = $wpdb->posts;
        $eventsTbl = $wpdb->prefix . AlterTableEventsData::$tblName;
        $utcNow = current_time('timestamp', 1);
        $dateTime = Time::mysqlDateTime($utcNow);

        $aEvents = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT $eventsTbl.objectID, $postsTbl.post_title FROM $eventsTbl LEFT JOIN $postsTbl ON ($postsTbl.ID = $eventsTbl.objectID) WHERE $postsTbl.post_status=%s AND $postsTbl.post_type=%s AND $eventsTbl.endsOnUTC!='' AND $eventsTbl.endsOnUTC < %s ORDER BY $eventsTbl.objectID LIMIT 100",
                'publish', 'event', $dateTime
            )
        );

        if ( empty($aEvents) ){
            return true;
        }
        if ( !empty($aEvents) ){
            foreach ($aEvents as $oEvent){
                wp_update_post(
                    array(
                        'ID' => $oEvent->objectID,
                        'post_status' => 'draft'
                    )
                );
            }
        }
    }

	public function profileNavigation($aNavigation){
		$aEventSettings = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'));

		if ( isset($aEventSettings['toggle_event']) && $aEventSettings['toggle_event'] == 'disable'  ){
			unset($aNavigation['events']);
		}

		return $aNavigation;
	}

	public static function getEventMetaData($post){
		$aMapInformation = GetSettings::getListingMapInfo($post->ID);
		$aMetaData = array();
		if ( !empty($aMapInformation) && !empty($aMapInformation['address']) && !empty($aMapInformation['lat']) && !empty($aMapInformation['lng']) ){
			$aMetaData[] = array(
				'icon'   => 'la la-map-marker',
				'type'   => 'map',
				'value'  => $aMapInformation
			);
		}

		$oTerm = wp_get_post_terms($post->ID, 'listing_location');
		if ( $oTerm ){
			$aMetaData[] = array(
				'icon'   => '',
				'type'   => 'term',
				'value'  => $oTerm
			);
		}

		$oTerm = wp_get_post_terms($post->ID, 'listing_cat');
		if ( $oTerm ){
			$aMetaData[] = array(
				'icon'   => '',
				'type'   => 'term',
				'value'  => $oTerm
			);
		}

		$email = GetSettings::getPostMeta($post->ID, 'email');
		if ( $email ){
			$aMetaData[] = array(
				'icon'   => 'la la-envelope',
				'type'   => 'email',
				'value'  => $email
			);
		}

		$phone = GetSettings::getPostMeta($post->ID, 'phone');
		if ( $phone ){
			$aMetaData[] = array(
				'icon'   => 'la la-phone',
				'type'   => 'phone',
				'value'  => $phone
			);
		}

		$website = GetSettings::getPostMeta($post->ID, 'website');
		if ( $website ){
			$aMetaData[] = array(
				'icon'   => 'la la-link',
				'type'   => 'website',
				'value'  => $website
			);
		}

		$aPriceRange = GetSettings::getPriceRange($post->ID, true);
		if ( $aPriceRange ){
			$aMetaData[] = array(
				'icon'   => 'la la-money',
				'type'   => 'price_range',
				'value'  => $aPriceRange
			);
		}

		$singlePrice = GetSettings::getPostMeta($post->ID, 'single_price');
		if ( !empty($singlePrice) ){
			$aMetaData[] = array(
				'icon'   => 'la la-money',
				'type'   => 'single_price',
				'value'  => $singlePrice
			);
		}
		return apply_filters('wiloke-listing-tools/single-event/meta-data', $aMetaData, $post);
	}

	public function renderEventMetaData($post){
		global $wiloke;
		$aMetaData = self::getEventMetaData($post);
		if ( empty($aMetaData) ){
			return '';
		}
		foreach ($aMetaData as $aItem){
			?>
            <div class="event-detail-content_firstItem__3vz2x">
				<?php
				switch ($aItem['type']){
					case 'map':
						?>
                        <div class="icon-box-1_module__uyg5F event-detail-content_location__1UYZY">
                            <div class="icon-box-1_block1__bJ25J">
                                <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <i class="<?php echo esc_html($aItem['icon']); ?>"></i>
                                </div>
                                <div class="icon-box-1_text__3R39g"><?php echo esc_html(stripslashes($aItem['value']['address'])); ?></div>
                            </div>
                        </div>
                        <a class="event-detail-content_showMap__3psSs color-primary js-toggle-map" href="#" data-mapid="wilcity-event-map-<?php echo esc_attr($post->ID); ?>" data-toggle-button="showmap" data-toggle-effect="slide" style="user-select: none;"><?php esc_html_e('Show map', 'wiloke-listing-tools'); ?> <i class="la la-angle-down"></i></a>
                        <div class="event-detail-content_map__1LrJO mt-15" data-toggle-content="showmap">
                            <div style="height: 200px; background-color: #e7e7ed;" id="wilcity-event-map-<?php echo esc_attr($post->ID); ?>" class="js-single-map has-toggle wil-single-map" data-zoom="<?php echo esc_attr($wiloke->aThemeOptions['single_event_map_default_zoom']); ?>" data-max-zoom="<?php echo esc_attr($wiloke->aThemeOptions['single_event_map_max_zoom']); ?>" data-min-zoom="<?php echo esc_attr($wiloke->aThemeOptions['single_event_map_minimum_zoom']); ?>" data-latlng="<?php echo esc_attr($aItem['value']['lat'] . ',' . $aItem['value']['lng']); ?>" data-marker="<?php echo esc_url(\WilokeListingTools\Frontend\SingleListing::getMapIcon($post)); ?>" data-google-map-url="<?php echo esc_url(GetSettings::getAddress($post->ID, true)); ?>"></div>
                        </div>
						<?php
						break;
					case 'term':
					    if ( empty($aItem['value']) ){
					        break;
                        }
						?>
                        <div class="icon-box-1_module__uyg5F event-detail-content__cat">
							<?php foreach ($aItem['value'] as $oTerm) : ?>
                                <div class="icon-box-1_block1__bJ25J mr-20">
									<?php
									echo \WilokeHelpers::getTermIcon($oTerm, 'icon-box-1_icon__3V5c0 rounded-circle', true);
									?>
                                </div>
							<?php endforeach; ?>
                        </div>
						<?php
						break;
					case 'email':
						?>
                        <div class="icon-box-1_module__uyg5F event-detail-content_location__1UYZY">
                            <div class="icon-box-1_block1__bJ25J">
                                <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <i class="<?php echo esc_html($aItem['icon']); ?>"></i>
                                </div>
                            </div>
                            <div class="icon-box-1_text__3R39g"><a href="mailto:<?php echo esc_attr($aItem['value']); ?>" target="_self"><?php echo esc_html($aItem['value']); ?></a></div>
                        </div>
						<?php
						break;
					case 'phone':
						?>
                        <div class="icon-box-1_module__uyg5F event-detail-content_location__1UYZY">
                            <div class="icon-box-1_block1__bJ25J">
                                <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <i class="<?php echo esc_html($aItem['icon']); ?>"></i>
                                </div>
                            </div>
                            <div class="icon-box-1_text__3R39g"><a href="tel:<?php echo esc_attr($aItem['value']); ?>"><?php echo esc_html($aItem['value']); ?></a></div>
                        </div>
						<?php
						break;
					case 'website':
						?>
                        <div class="icon-box-1_module__uyg5F event-detail-content_location__1UYZY">
                            <div class="icon-box-1_block1__bJ25J">
                                <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <i class="<?php echo esc_html($aItem['icon']); ?>"></i>
                                </div>
                            </div>
                            <div class="icon-box-1_text__3R39g"><a href="<?php echo esc_attr($aItem['value']); ?>" target="_blank"><?php echo esc_html($aItem['value']); ?></a></div>
                        </div>
						<?php
						break;
					case 'price_range':
						?>
                        <div class="icon-box-1_module__uyg5F event-detail-content_location__1UYZY">
                            <div class="icon-box-1_block1__bJ25J">
                                <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <i class="<?php echo esc_html($aItem['icon']); ?>"></i>
                                </div>
                            </div>
                            <div class="icon-box-1_text__3R39g"><a href="#"><?php echo esc_attr($aItem['value']['minimumPrice']) . ' - ' . ($aItem['value']['maximumPrice']); ?></a></div>
                        </div>
						<?php
						break;
					case 'single_price':
						?>
                        <div class="icon-box-1_module__uyg5F event-detail-content_location__1UYZY">
                            <div class="icon-box-1_block1__bJ25J">
                                <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <i class="<?php echo esc_html($aItem['icon']); ?>"></i>
                                </div>
                            </div>
                            <div class="icon-box-1_text__3R39g"><a href="#" class="wilcity-single-event-price"><?php echo GetWilokeSubmission::renderPrice($aItem['value']); ?></a></div>
                        </div>
						<?php
						break;
					default:
						?>
                        <div class="icon-box-1_module__uyg5F event-detail-content_location__1UYZY">
                            <div class="icon-box-1_block1__bJ25J">
                                <div class="icon-box-1_icon__3V5c0 rounded-circle">
                                    <i class="<?php echo esc_html($aItem['icon']); ?>"></i>
                                </div>
                            </div>
                            <div class="icon-box-1_text__3R39g"><a href="<?php echo esc_attr($aItem['value']); ?>" target="<?php echo isset($aItem['target']) ? esc_attr($aItem['target']) : '_self'; ?>"><?php echo esc_html($aItem['value']); ?></a></div>
                        </div>
						<?php
						break;
				}
				?>
            </div>
			<?php
		}


		$planID = GetSettings::getPostMeta($post->ID, 'belongs_to');
		if ( empty($planID) || GetSettings::isPlanAvailableInListing($planID, 'toggle_social_networks') ) {
			$socialNetworks = do_shortcode('[wilcity_listing_social_networks post_id="'.$post->ID.'"]');

			if ( !empty($socialNetworks) ) {
				echo '<div class="event-detail-content_firstItem__3vz2x">' . $socialNetworks . '</div>';
			}
		}
	}

	public function countAuthorEventTypes(){
		$userID = get_current_user_id();
		$this->middleware(['isUserLoggedIn'], array('userID'=>$userID));

		$aResponse = array();
		$aResponse['unpaid_events']     = EventModel::countUnpaidEvents($userID);
		$aResponse['up_coming_events']  = EventModel::countUpcomingEventsOfAuthor($userID);
		$aResponse['on_going_events']   = EventModel::countOnGoingEventsOfAuthor($userID);
		$aResponse['expired_events']    = EventModel::countExpiredEventsOfAuthor($userID);
		$aResponse['temporary_close']   = User::countPostsByPostStatus('temporary_close', 'event');
		$aResponse['pending'] = User::countPostsByPostStatus('pending', 'event');

		wp_send_json_success($aResponse);
	}

	public function loadMoreListings(){
		$aData = $_POST['data'];
		$page  = isset($_POST['page']) ? abs($_POST['page']) : 2;

		foreach ($aData as $key => $val){
			$aData[$key] = sanitize_text_field($val);
		}

		$query = new \WP_Query(
			array(
				'post_type'         => 'event',
				'posts_per_page'    => $aData['postsPerPage'],
				'paged'             => $page,
				'post_status'       => 'publish'
			)
		);

		if ( $query->have_posts() ){
			ob_start();
			while ($query->have_posts()){
				$query->the_post();
				wilcity_render_event_item($query->post, array(
					'img_size'                   => $aData['img_size'],
					'maximum_posts_on_lg_screen' => $aData['maximum_posts_on_lg_screen'],
					'maximum_posts_on_md_screen' => $aData['maximum_posts_on_md_screen'],
					'maximum_posts_on_sm_screen' => $aData['maximum_posts_on_sm_screen'],
				));
			}
			$contents = ob_get_contents();
			ob_end_clean();
			wp_send_json_success(array('msg'=>$contents));
		}else{
			wp_send_json_error(
				array(
					'msg' => sprintf(esc_html__('Oops! Sorry, We found no %s', 'wiloke-listing-tools'), $aData['postType'])
				)
			);
		}
	}

	public function fetchEventsJson(){
		$aArgs = array(
			'post_type'         => 'event',
			'posts_per_page'    => 10,
			'post_status'       => 'publish',
			'order'             => 'ASC',
			'isDashboard' => true,
			'author'            => User::getCurrentUserID()
		);

		if ( isset($_POST['s']) && !empty($_POST['s']) ){
		    $aArgs['s'] = trim($_POST['s']);
        }

		if ( isset($_POST['paged']) ){
			$aArgs['paged'] = $_POST['paged'];
		}

		if ( isset($_POST['orderby']) ){
			$aArgs['orderby'] = $_POST['orderby'];
			$aArgs['post_status'] = array('publish', 'draft', 'expired', 'unpaid');
		}

		if ( isset($_POST['post_status']) ){
			$aArgs['post_status'] = $_POST['post_status'];
			if ($aArgs['post_status'] == 'pending' || $aArgs['post_status'] == 'unpaid') {
			    $aArgs['aIgnoreModifyPostTypes'] = array('event');
			    $aArgs['isFocusExcludeEventExpired'] = false;
            }
		}

		if ( isset($_POST['parentID']) ){
			$aArgs['post_parent'] = $_POST['parentID'];
		}

		$aEventsData = array();
		$query = new \WP_Query($aArgs);
		$aFrequencies = wilokeListingToolsRepository()->get('event-settings:aFrequencies');

		$dateFormat = get_option('date_format') . ' ' . get_option('time_format');
		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$aEventData = $this->json($query->post);
				$aEventDate = GetSettings::getEventSettings($query->post->ID);
				$aEventData['frequency'] = $aFrequencies[$aEventDate['frequency']];
				$aEventData['starts'] = date_i18n($dateFormat, strtotime($aEventDate['startsOn']));
				$aEventData['ends']   = date_i18n($dateFormat, strtotime($aEventDate['endsOn']));
				$aEventsData[] = $aEventData;
			}
			wp_reset_postdata();
		}else{
			wp_send_json_error(array(
				'msg' => esc_html__('There are no events', 'wiloke-listing-tools'),
				'maxPages' => 0,
				'maxPosts' => 0
			));
		}

		wp_send_json_success(array(
			'info' => $aEventsData,
			'maxPages' => abs($query->max_num_pages),
			'maxPosts' => abs($query->found_posts)
		));
	}

	public function printEventFields(){
		global $post;
		$aSupportedPostType = Submission::getSupportedPostTypes();
		if ( !is_user_logged_in() || !is_singular($aSupportedPostType) || ( !current_user_can('edit_theme_options') && (get_current_user_id() != $post->post_author) )  ){
			return false;
		}

		$aEventFields = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:designFields', true)->sub('usedSectionKey'));
		$this->aSections = $this->getAvailableFields();

		wp_localize_script('jquery-migrate', 'WILCITY_EVENT_FIELDS', $aEventFields);
	}

	public function printAddEventSidebars(){

	}

	public function addMiddleWareOptionsToReviewHandler($aOptions){
		return array_merge($aOptions, array(
			'postType' => $this->postType
		));
	}

	public function addMiddleWareToReviewHandler($aMiddleware){
		return array_merge($aMiddleware, ['isPublishedPost', 'isPostType']);
	}

	public function setReviewPostType(){
		return 'event_comment';
	}

	public static function isEnabledDiscussion(){
		$aGeneralSettings = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'));

		return $aGeneralSettings['toggle_comment_discussion'] == 'enable';
	}

	public static function isEnableComment(){
		$aGeneralSettings = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'));

		return $aGeneralSettings['toggle'] == 'enable';
	}

	protected function determineCommentStatus(){
		$aGeneralSettings = GetSettings::getOptions(wilokeListingToolsRepository()->get('event-settings:keys', true)->sub('general'));
		if ( $aGeneralSettings['immediately_approved'] == 'enable' ){
			return 'publish';
		}
		return 'draft';
	}

	private function postComment($parentID, $comment, $commentID=null){
		$userID = User::getCurrentUserID();
		$displayName = User::getField('display_name', $userID);
		$post_title = $displayName . ' ' . esc_html__('Left a Comment ', 'wiloke-listing-tools') . ' ' . get_the_title($parentID);

		if ( empty($commentID) ){
			$commentID = wp_insert_post(
				array(
					'post_type'     => 'event_comment',
					'post_status'   => $this->determineCommentStatus(),
					'post_title'    => apply_filters('wilcity/wiloke-listing-tools/post-comment/title', $post_title, $displayName, $parentID),
					'post_content'  => $comment,
					'post_parent'   => $parentID,
					'post_author'   => $userID
				)
			);
        }else{
			wp_update_post(
				array(
                    'ID'            => $commentID,
					'post_status'   => $this->determineCommentStatus(),
					'post_content'  => $comment
				)
			);
        }

		global $wiloke;
		$wiloke->aThemeOptions = \Wiloke::getThemeOptions();
		$wiloke->aConfigs['translation'] = wilcityGetConfig('translation');
		$oComment = get_post($commentID);

		do_action('wilcity/event/after-inserted-comment', $commentID, $userID, $parentID);

		return $oComment;
    }

    public function appBeforePostComment($parentID, $comment){
	    return $this->postComment($parentID, $comment);
    }

	public function appBeforeUpdateComment($parentID, $comment, $commentID){
		return $this->postComment($parentID, $comment, $commentID);
	}

	public function ajaxBeforePostComment($aData){
		$this->middleware(['isPostType'], array(
			'postID'   => $aData['postID'],
			'postType' => 'event'
		));
		global $wilcityoReview;
		$wilcityoReview = $this->postComment($aData['postID'], $_POST['content']);
		ob_start();
		get_template_part('reviews/item');
		$html = ob_get_contents();
		ob_end_clean();

		wp_send_json_success(array(
			'html' => $html,
			'commentID' => $wilcityoReview->ID
		));
	}

	public static function fetchEvents($aData=array()){
		$aData = $_GET;

		$aArgs =  array(
			'post_type'         => 'event',
			'post_status'       => 'publish',
			'order'             => 'ASC',
			'orderby'           => 'starts_from_ongoing_event',
			'posts_per_page'    => 10,
		);

		if ( isset($aData['parentID']) ){
			$aArgs['post_parent'] = $aData['parentID'];
		}

		if ( isset($aData['postNotIn']) && !empty($aData['postNotIn']) ){
			$aParseIDs = explode(',', $aData['postNotIn']);
			$aParseIDs = array_map(function($id){
				return abs($id);
			}, $aParseIDs);
			$aArgs['post__not_in'] = $aParseIDs;
		}
		$isAjax = false;
		if ( wp_doing_ajax() ){
			$isAjax = true;
		}

		$query = new \WP_Query($aArgs);

		if ( $isAjax ){
			ob_start();
		}
		global $wilcityWrapperClass;
		$wilcityWrapperClass = 'col-sm-6 col-md-4';
		$aPostIDs = array();

		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$aPostIDs[] = $query->post->ID;
				get_template_part('single-listing/partials/event');
			}
		}else{
			if ( isset($aArgs['post__not_in']) ){
				wp_send_json_error(array(
					'isLoaded'=>'yes'
				));
			}else{
				wp_send_json_error(array(
					'msg' =>  esc_html__('There are no events', 'wiloke-listing-tools')
				));
			}
		}
		wp_reset_postdata();

		if ( $isAjax ){
			$content = ob_get_contents();
			ob_end_clean();
			wp_send_json_success(array(
				'content' => $content,
				'postIDs' => $aPostIDs
			));
		}
	}

	/*
	 * $aResponse: $paymentID, $planID, $gateway
	 */
	public function setPlanRelationshipBeforePayment($aResponse){
		if ( !isset($aResponse['planID']) || empty($aResponse['planID']) ){
			return false;
		}

		$userID = get_current_user_id();
		$aInfo = array(
			'paymentID' => $aResponse['paymentID'],
			'planID'    => $aResponse['planID'],
			'userID'    => $userID,
			'objectID'  => Session::getSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'))
		);

		$aUserPlan = UserModel::getSpecifyUserPlanID($aResponse['planID'], $userID, true);

		$this->setPlanRelationship($aUserPlan, $aInfo);
	}

	public function updateVideo($aData){
		if ( isset($aData['video']) && !empty($aData['video']) ){
			SetSettings::setPostMeta($this->postID, 'video', sanitize_text_field($aData['video']));
		}else{
			SetSettings::deletePostMeta($this->postID, 'video');
		}
	}

	public function updateTimeFormat($aData){
		if ( isset($aData['timeFormat']) && !empty($aData['timeFormat']) ){
			SetSettings::setPostMeta($this->postID, 'timeFormat', sanitize_text_field($aData['timeFormat']));
		}else{
			SetSettings::deletePostMeta($this->postID, 'timeFormat');
		}
	}

	public function validatingEventData($aInput, $aRawEventData){
		$aEventData = array();
		$aPrepares  = array();
		foreach ($aInput as $dataKey => $val) {
			if ( ! isset( $aRawEventData[ $dataKey ] ) ) {
				continue;
			}

			if ( ! is_array( $val ) ) {
				$aEventData[ $dataKey ] = $aRawEventData[$dataKey];
				$aPrepares[]            = sanitize_text_field( $val );
			} else {
				foreach ( $val as $subKey => $subVal ) {
					$aEventData[ $subKey ] = sanitize_text_field( $aRawEventData[$dataKey][$subKey] );
					$aPrepares[]           = $subVal;
				}
			}
		}

		return array(
			'data' => $aEventData,
			'prepares' => $aPrepares
		);
	}

	public function editEvent(){
		if ( !isset($_POST['eventID']) || empty($_POST['eventID']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
				)
			);
		}
		$eventID = $_POST['eventID'];
		$this->postID = $eventID;
		$aRawEventData = $_POST['data'];

		if ( !current_user_can('edit_theme_options') ){
			$this->middleware(['isPostAuthor'], array(
				'postID' => $eventID
			));
		}
		// Updating Events
		$this->uploadFeaturedImg($aRawEventData['img']);

		$aAfterValidation = $this->validatingEventData($this->aEventsDataAndPrepares, $aRawEventData);
		$aPrepares  = $aAfterValidation['prepares'];
		$aEventData = $aAfterValidation['data'];
		$aEventData['objectID'] = $this->postID;
		$aPrepares[] = '%d';

		if ( $_POST['isAddressChanged'] ){
			$aEventData['timezone'] = GetSettings::getTimeZoneByGeocode($aRawEventData['address']['lat'] . ',' . $aRawEventData['address']['lng']);
			$aPrepares[] = '%s';
		}

		wp_update_post(
			array(
				'ID'            => $this->postID,
				'post_title'    => $aRawEventData['listing_title'],
				'post_name'     => sanitize_title( $aRawEventData['listing_title']),
				'post_content'  => $aRawEventData['listing_content']
			)
		);

		$this->updateVideo($_POST['data']);
		$this->updateTimeFormat($_POST['data']);

		EventModel::updateEventData($this->postID, array(
			'values'    => $aEventData,
			'prepares'  => $aPrepares
		));

		$aResponse['msg'] = esc_html__('Congrats! The event has been updated successfully', 'wiloke-listing-tools');
		if ( !empty($aRawEventData['img']) ){
			$aResponse['img'] = $this->getFeaturedImageData($this->postID);
		}

		wp_send_json_success($aResponse);
	}

	public function updatedEventDataViaBackend($postID, $post, $update){
		if ( !is_admin() ){
			return false;
		}

		if ( ($post->post_type != $this->postType) || ( isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == 'all' ) ){
			return false;
		}

		if ( !isset($_POST['isFormChanged']) || empty($_POST['isFormChanged']) ){
			return false;
		}

		$this->postID = $postID;
		$aPrepares = array();
		$aEventData = array();
		$aEventData['objectID'] = $postID;
		$aPrepares[] = '%d';

		$aAfterValidation = $this->validatingEventData($this->aEventsDataAndPrepares, $_POST);
		$aPrepares = array_merge($aPrepares, $aAfterValidation['prepares']);
		$aEventData = array_merge($aEventData, $aAfterValidation['data']);

		$this->updateVideo($_POST);

		EventModel::updateEventData($postID, array(
			'values'    => $aEventData,
			'prepares'  => $aPrepares
		));
	}

	public function getEventItemData(){
		if ( !current_user_can('edit_theme_options') ){
			$this->middleware(['isPostAuthor'], array(
				'postID' => $_POST['eventID']
			));
		}

		$eventID = $_POST['eventID'];
		$aData = array(
			'listing_title' => '',
			'listing_content' => ''
		);

		if ( !empty($eventID) ){
			$aData['listing_title']     = get_post_field('post_title', $eventID);
			$aData['listing_content']   = get_post_field('post_content', $eventID);
			if (  has_post_thumbnail($eventID) ){
				$aData['img'][0]['src'] = get_the_post_thumbnail_url($eventID);
				$aData['img'][0]['fileName'] = get_the_title($eventID);
				$aData['img'][0]['imgID'] = get_post_thumbnail_id($eventID);
			}
		}


		$aEventData = EventModel::getEventData($eventID);
		self::$aEventsData[$eventID] = $aEventData;

		if ( !empty($aEventData) ){
			$aData['video']                 = $aEventData['video'];
			$aData['address']['address']    = $aEventData['address'];
			$aData['address']['lat']        = $aEventData['lat'];
			$aData['address']['lng']        = $aEventData['lng'];
			$aData['frequency']             = $aEventData['frequency'];
			$aData['endsOn']                = date('Y/m/d', strtotime($aEventData['endsOn']));
			$aData['starts']                = date('Y/m/d', strtotime($aEventData['starts']));
			$aData['openingAt']             = $aEventData['openingAt'];
			$aData['closedAt']              = $aEventData['closedAt'];

			if ( $aData['frequency'] == 'weekly' ){
				$aData['weekly']['specifyDays'] = isset($aEventData['specifyDays']) ? explode(',', $aEventData['specifyDays']) : array();
			}
		}

		wp_send_json_success($aData);
	}

	public function eventItems(){
		?>

		<?php
	}

	public function uploadFeaturedImg($aImg){
		if ( empty($aImg) ){
			delete_post_thumbnail($this->listingID);
		}

		if ( empty($aImg) || (isset($aImg[0]['imgID']) && !empty($aImg[0]['imgID'])) ){
			return false;
		}

		$instUploadImg = new Upload();

		$instUploadImg->userID = get_current_user_id();
		$instUploadImg->aData['imageData']  = $aImg[0]['src'];
		$instUploadImg->aData['fileName']   = $aImg[0]['fileName'];
		$instUploadImg->aData['fileType']   = $aImg[0]['fileType'];
		$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder();

		$id = $instUploadImg->image();
		set_post_thumbnail($this->listingID, $id);
		return true;
	}

	protected function isMustPayForEvent(){
		return array(
			'redirectTo' => GetWilokeSubmission::getField('checkout', true)
		);
	}

	public function detectPostStatus(){
		if ( User::can('edit_theme_options') ){
			return 'publish';
		}

		return 'unpaid';
	}

	public function validateEventBelongsToListing($that, $aFieldData){
		$that->parentListingID = $aFieldData['value'];
		if ( !empty($that->parentListingID) ){
			$this->middleware(['isUserLoggedIn', 'isPostAuthor', 'isPublishedPost'], array(
				'postID'         => $that->parentListingID,
				'postAuthor'     => get_current_user_id(),
				'passedIfAdmin'  => true
			));
		}
	}

	public function validateEventCalendar($that, $aFieldData){
		if ( empty($aFieldData['value']) ){
			$that->aEventCalendar = $aFieldData['value'];
		}else{
			foreach ($aFieldData['value'] as $key => $val){
				$that->aEventCalendar[sanitize_text_field($key)] = sanitize_text_field($val);
			}

			if ( empty($that->aEventCalendar['starts']) || empty($that->aEventCalendar['endsOn']) ){
				wp_send_json_error(
					array(
						'msg' => esc_html__('The event start date and end date are required', 'wiloke-listing-tools')
					)
				);
			}

			$start  = strtotime($that->aEventCalendar['starts']);
			$end    = strtotime($that->aEventCalendar['endsOn']);
			$wrongDateMsg = esc_html__('The event start date must be smaller than event end date', 'wiloke-listing-tools');

			if ( $start > $end ){
				wp_send_json_error(
					array(
						'msg' => $wrongDateMsg
					)
				);
			}else if ( $start ==  $end ){
				$openingAt =  strtotime($that->aEventCalendar['openingAt']);
				$closedAt  =  strtotime($that->aEventCalendar['closedAt']);

				if ( $openingAt > $closedAt ){
					wp_send_json_error(
						array(
							'msg' => $wrongDateMsg
						)
					);
				}
			}
		}
	}

	protected function addressIsRequired(){
		if ( empty($this->aGoogleAddress) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Google Address is required', 'wiloke-listing-tools')
				)
			);
		}

		if ( empty($this->aGoogleAddress['latLng']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('We could not get Geocode of the address, please try to enter it again', 'wiloke-listing-tools')
				)
			);
		}
	}

	private function _handlePreview() {
		$this->aData = $_POST['data'];
		$aEventData = array();
		$this->planID  = $_POST['planID'];

		$this->middleware(['isPlanExists'], array(
			'planID'        => $this->planID,
			'listingType'   => $this->postType
		));

		$this->aListingData['post_type'] = $this->postType;
        $this->aPlanSettings = GetSettings::getPlanSettings($this->planID);

        $this->validation();
		if ( isset($this->parentListingID) ){
			$this->aListingData['post_parent'] = $this->parentListingID;
		}

		if ( isset($_POST['listingID']) && !empty($_POST['listingID']) ){
			$this->listingID = abs($_POST['listingID']);
			$this->middleware(['isPostType', 'isPostAuthor'], array(
				'postType'  => 'event',
				'postAuthor'=> get_current_user_id(),
				'postID'    => $this->listingID
			));

			$this->postStatus = get_post_status($this->listingID);
			$this->middleware(['isListingBeingReviewed'], array(
				'postStatus'  => $this->postStatus
			));
			$this->aListingData['ID'] = $this->listingID;

			if ( $this->postStatus == 'publish' ){
				$this->aListingData['post_status'] = 'editing';
			}

			if ( isset($this->aListingData['post_title']) && !empty($this->aListingData['post_title']) ){
				$this->aListingData['post_name'] = sanitize_text_field($this->aListingData['post_title']);
			}

			wp_update_post($this->aListingData);

			$this->isNewListing = false;
		}else{
			$this->listingID = wp_insert_post($this->aListingData);
		}

		if ( empty($this->listingID) ){
			wp_send_json_error(array(
				'msg' => esc_html__('We could not insert the event. Please make sure that all required fields are filled up', 'wiloke-listing-tools')
			));
		}

		$aPrepares = array(
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s'
		);

		$aParseAddress = explode(',', $this->aGoogleAddress['latLng']);
		$aEventData['objectID']         = $this->listingID;
		$aEventData['parentID']         = $this->parentListingID;
		$aEventData['frequency']        = $this->aEventCalendar['frequency'];
		$aEventData['address']          = $this->aGoogleAddress['address'];
		$aEventData['lat']              = sanitize_text_field($aParseAddress[0]);
		$aEventData['lng']              = sanitize_text_field($aParseAddress[1]);
		$aEventData['starts']           = $this->aEventCalendar['starts'];
		$aEventData['endsOn']           = $this->aEventCalendar['endsOn'];
		$aEventData['openingAt']        = $this->aEventCalendar['openingAt'];
		$aEventData['closedAt']         = $this->aEventCalendar['closedAt'];

		if ( $this->aEventCalendar['frequency'] == 'weekly' ){
			$aEventData['specifyDays'] = $this->aEventCalendar['specifyDays'];
			$aPrepares[] = '%s';
		}

		$status = EventModel::updateEventData($this->listingID, array(
			'values'    => $aEventData,
			'prepares'  => $aPrepares
		));

		if ( !$status ){
			AjaxMsg::error(esc_html__('ERROR: We could not insert the event Calendar.', 'wiloke-listing-tools'));
		}

		$this->belongsToCategories();
		$this->belongsToLocation();
		$this->belongsToTags();
		$this->setVideos();
		$this->setPriceRange();
		$this->setSinglePrice();
		$this->insertGallery();
		$this->insertFeaturedImg();

		$this->setGeneralSettings();
		$this->setSocialNetworks();
		$this->setContactInfo();
		$this->setCustomSections();
		$this->setGroup();
		$this->insertAddress();

		$this->setProductsToListing();
		$this->setListingBelongsTo();

		Session::setSession(wilokeListingToolsRepository()->get('payment:storePlanID'), $this->planID);
		Session::setSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'), $this->listingID);
		Session::setSession(wilokeListingToolsRepository()->get('addlisting:isAddingListingSession'), $this->listingID);

		// Maybe Skip Preview Step
		if ( \WilokeThemeOptions::isEnable('addlisting_skip_preview_step', false) ){
			$this->_handleSubmit();
		}

		wp_send_json_success(array(
			'redirectTo' => add_query_arg(
				array(
					'mode' => 'preview'
				),
				get_permalink($this->listingID)
			)
		));
	}

	public function handlePreview(){
		$this->_handlePreview();
	}

	public static function eventStart($objectID, $isReturn=true){
		if ( isset(self::$aEventsData[$objectID]) ){
			$aData = self::$aEventsData[$objectID];
		}else{
			$aData = EventModel::getEventData($objectID);
			self::$aEventsData[$objectID] = $aData;
		}

		if ( $isReturn ){
			ob_start();
		}

		$date  = date('M/d', strtotime($aData['startsOn']));
		$aDate = explode('/', $date);
		?>
        <div class="event_calendar__2x4Hv"><span class="event_month__S8D_o color-primary"><?php echo esc_html($aDate[0]); ?></span><span class="event_date__2Z7TH"><?php echo esc_html($aDate[1]); ?></span></div>
		<?php
		if ( $isReturn ){
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}

	public static function renderEventCalendar($post, $isReturn=false){
		
		$aEventData = EventModel::getEventData($post->ID);
		$frequency  = $aEventData['frequency'];
		$timezone   = GetSettings::getPostMeta($post->ID, 'timezone');
		$timeFormat = GetSettings::getPostMeta($post->ID, 'event_time_format');

		$aTimeInformation = array();
		$aDetails = array();
		$aAjaxInfo = array();

		switch ($frequency){
			case 'occurs_once':
				Time::findUTCOffsetByTimezoneID($timezone);
				$aTimeInformation['general'] = esc_html(Time::toDateFormat($aEventData['startsOn'])) . ' - ' . esc_html(Time::toDateFormat($aEventData['endsOn']));

				$aDetails[0]['heading'] = esc_html__('Opening at', 'wiloke-listing-tools');
				$aDetails[0]['time']    = esc_html(Time::toTimeFormat($aEventData['startsOn'], $timeFormat));

				$aDetails[1]['heading'] = esc_html__('Closed at', 'wiloke-listing-tools');
				$aDetails[1]['time']    = esc_html(Time::toTimeFormat($aEventData['endsOn'], $timeFormat));
				
				break;
			case 'daily':
				$aTimeInformation['general'] = esc_html__('Daily', 'wiloke-listing-tools') . ', ' . esc_html(Time::toDateFormat($aEventData['startsOn'])) . ' - ' . esc_html(Time::toDateFormat($aEventData['endsOn']));

				$aDetails[0]['heading'] = esc_html__('Opening at', 'wiloke-listing-tools');
				$aDetails[0]['time']    = Time::toTimeFormat($aEventData['startsOn'], $timeFormat);

				$aDetails[1]['heading'] = esc_html__('Closed at', 'wiloke-listing-tools');
				$aDetails[1]['time']    = Time::toTimeFormat($aEventData['endsOn'], $timeFormat);
				
				break;

			case 'weekly':
				$specifyDay = $aEventData['specifyDays'];
				$dayName = wilokeListingToolsRepository()->get('general:aDayOfWeek', true)->sub($specifyDay);

				$aTimeInformation['general'] = sprintf( esc_html__('Every %s', 'wiloke-listing-tools'),  $dayName) . ', ' . esc_html(Time::toDateFormat($aEventData['startsOn'])) . ' - ' . esc_html(Time::toDateFormat($aEventData['endsOn']));

				$aDetails[0]['heading'] = esc_html__('Opening at', 'wiloke-listing-tools');
				$aDetails[0]['time']    = Time::toTimeFormat($aEventData['startsOn'], $timeFormat);

				$aDetails[1]['heading'] = esc_html__('Closed at', 'wiloke-listing-tools');
				$aDetails[1]['time']    = Time::toTimeFormat($aEventData['endsOn'], $timeFormat);
				
				break;
		}

		if ( !empty($aTimeInformation) ):
			if ( $isReturn ):
				$aAjaxInfo['heading'] = esc_html__('Calendar', 'wiloke-listing-tools');
				$aAjaxInfo['general'] = $aTimeInformation['general'];

				$newTimeFormat = 'D ' . Time::getTimeFormat($timeFormat);
				$aAjaxInfo['oStarts'] = array(
					'date' => Time::toDateFormat($aEventData['startsOn']),
					'hour' => date_i18n($newTimeFormat, strtotime($aEventData['startsOn']))
				);

				$aAjaxInfo['oEnds'] = array(
					'date' => Time::toDateFormat($aEventData['endsOn']),
					'hour' => date_i18n($newTimeFormat, strtotime($aEventData['endsOn']))
				);
				$aAjaxInfo['oOccur'] = array(
					'frequency' => $frequency,
					'text'      => $aTimeInformation['general']
				);

				return $aAjaxInfo;
			else: ?>
                <div class="event-detail-content_firstItem__3vz2x">
                    <div class="icon-box-1_module__uyg5F">
                        <div class="icon-box-1_block1__bJ25J">
                            <div class="icon-box-1_icon__3V5c0 rounded-circle"><i class="la la-clock-o"></i></div>
                            <div class="icon-box-1_text__3R39g"><?php esc_html_e('Calendar', 'wiloke-listing-tools'); ?></div>
                        </div>
                        <div class="icon-box-1_block2__1y3h0">
                            <span class="color-secondary"><?php echo esc_html($aTimeInformation['general']); ?></span>
                        </div>
                    </div>
					<?php
					
					if ( !empty($aDetails) ):
						
						foreach ($aDetails as $aDetail) : ?>
                            <div class="date-item_module__2wyHG mt-10 mr-10">
                                <div class="date-item_date__3OIqD"><?php echo esc_html($aDetail['heading']); ?></div>
                                <div class="date-item_hours__3w6Rw"><?php echo esc_html($aDetail['time']); ?></div>
                            </div>
							<?php
						endforeach;
					endif;
					?>
                </div>
				<?php
			endif;
		endif;
	}
}
