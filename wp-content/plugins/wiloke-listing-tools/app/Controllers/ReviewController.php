<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\DebugStatus;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PostModel;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Models\SharesStatistic;

class ReviewController extends Controller {
	public $aReviewSettings;
	public static $foundSticky = false;
	public static $stickyID = 0;
	public static $postType = 'review';

	public function __construct() {
		add_action('wp_ajax_wilcity_submit_review', array($this, 'submitReview'));
		add_action('wilcity/footer/vue-popup-wrapper', array($this, 'printFooter'));
		add_action('wp_ajax_wilcity_fetch_user_reviewed_data', array($this, 'fetchUserReviewedData'));
		add_action('wp_ajax_wilcity_review_is_update_like', array($this, 'updateLike'));
		add_action('wp_ajax_nopriv_wilcity_review_is_update_like', array($this, 'updateLike'));
		add_action('wp_ajax_wilcity_review_discussion', array($this, 'ajaxBeforeSetReviewDiscussion'));

		add_action('wp_ajax_wilcity_delete_discussion', array($this, 'deleteDiscussion'));
		add_filter('wilcity/single-listing/tabs', array($this, 'checkReviewStatus'), 10, 2);
		add_action('wp_ajax_wilcity_delete_comment', array($this, 'deleteReview'));
		add_action('wp_ajax_wilcity_like_review', array($this, 'likeReview'));
		add_action('wp_ajax_wilcity_update_discussion', array($this, 'updateDiscussion'));
//		add_action('wp_ajax_nopriv_wilcity_like_review', array($this, 'likeReview'));
		add_action('wp_enqueue_scripts', array($this, 'printReviewSettings'));
		add_action('wp_ajax_wilcity_fetch_single_review', array($this, 'fetchReviewsTab'));
		add_action('wp_ajax_nopriv_wilcity_fetch_single_review', array($this, 'fetchReviewsTab'));
		add_action('wp_ajax_wilcity_fetch_review_general', array($this, 'fetchReviewGeneral'));
		add_action('wp_ajax_nopriv_wilcity_fetch_review_general', array($this, 'fetchReviewGeneral'));
		add_action('wp_ajax_wilcity_pin_review_to_top', array($this, 'pinReviewToTop'));
		add_action('wp_ajax_wilcity_post_comment', array($this, 'postComment'));
		add_action('wp_ajax_nopriv_wilcity_post_comment', array($this, 'postComment'));

		add_filter('wilcity/addMiddlewareToReview/of/listing', array($this, 'addMiddleWareToReviewHandler'));

		add_action('wp_ajax_wilcity_fetch_ratings_general', array($this, 'fetchGeneralRatings'));
		add_action('wp_ajax_wilcity_ratings_latest_week', array($this, 'fetchRatingLastWeek'));

		add_action('wp_ajax_wilcity_fetch_discussions', array($this, 'fetchDiscussions'));
		add_action('wp_ajax_nopriv_wilcity_fetch_discussions', array($this, 'fetchDiscussions'));

		add_action('post_updated', array($this, 'updateAverageReviews'), 10, 3);
		add_action('wilcity/footer/vue-popup-wrapper', array($this, 'deleteReviewPopup'));
		add_action('wp_ajax_wilcity_delete_review', array($this, 'deleteReviews'));
		add_filter('wilcity/wilcity-mobile-app/submit-review', array($this, 'handleSubmitReview'), 10, 4);

		add_filter('wilcity/wilcity-mobile-app/like-a-review', array($this, 'updateLikeReviewViaApp'), 10, 2);
		add_filter('wilcity/wilcity-mobile-app/post-review-discussion', array($this, 'appSetReviewDiscussion'), 10, 2);
		add_filter('wilcity/wilcity-mobile-app/put-review-discussion', array($this, 'appUpdateReviewDiscussion'), 10, 2);
	}

	private function updateListingAverageReview($parentID){
		$averageReview  = ReviewMetaModel::getAverageReviews($parentID);
		SetSettings::setPostMeta($parentID, 'average_reviews', $averageReview);
	}

	public function afterDeleteReview($postID){
        if ( get_post_type($postID) != 'review' ){
            return false;
        }
    }

	public function deleteReviews(){
	    if ( !isset($_POST['reviewID']) || empty($_POST['reviewID']) ){
	        wp_send_json_error(array(
	           'msg' => esc_html__('The review id is required.', 'wiloke-listingt-tools')
            ));
        }

        $this->middleware(['isPostAuthor'], ['postID'=>$_POST['reviewID'], 'passedIfAdmin'=>true]);
		$parentID = wp_get_post_parent_id($_POST['reviewID']);
		wp_delete_post($_POST['reviewID'], true);
	    $this->updateListingAverageReview($parentID);
	    wp_send_json_success(array('msg'=>esc_html__('Congratulations! The review has been deleted.', 'wiloke-listing-tools')));
    }

	public function updateAverageReviews($postID, $oPostAfter, $oPostBefore){
		
	    if ( $oPostAfter->post_type !== 'review' ){
	        return false;
		}
		
        if ( isset( $_POST['action'] ) ){
	        $action = $_POST['action'];
        }else if ( isset($_GET['action']) ){
	        $action = $_GET['action'];
		}

        if ( !isset($action) || !in_array($action, array('edit', 'editpost', 'trash', 'inline-save') ) ){
	        return false;
		}
		
        $parentID = isset($_POST['post_parent']) && !empty($_POST['post_parent']) ? abs($_POST['post_parent']) : wp_get_post_parent_id($postID);
		$this->updateListingAverageReview($parentID);
    }

	public function fetchDiscussions(){
        $aRawDiscussions = ReviewModel::getReviews($_POST['parentID'], array(
            'postsPerPage' => WILCITY_NUMBER_OF_DISCUSSIONS,
            'page' => $_POST['page']
        ));

        if ( !$aRawDiscussions ){
            wp_send_json_error();
        }

        $aDiscussions = array();
        while ($aRawDiscussions->have_posts()){
            $aRawDiscussions->the_post();
	        $aDiscussions[] = self::getReviewInfo($aRawDiscussions->post, $_POST['parentID'], true);
        }

        wp_send_json_success(array(
            'discussions' => $aDiscussions
        ));
    }

	public function fetchRatingLastWeek(){
		$this->middleware(['isUserLoggedIn'], array());
		$userID = get_current_user_id();

		$aDateInThisWeek = Time::getAllDaysInThis();
		$aCountAverageRatingOfWeek = array();

		foreach ($aDateInThisWeek as $date){
			$aCountAverageRatingOfWeek[] = ReviewModel::getAuthorAverageRatingsInDay($userID, $date);
		}

		$start = date(get_option('date_format'), strtotime($aDateInThisWeek['monday']));
		$end = date(get_option('date_format'), strtotime(end($aDateInThisWeek)));

		wp_send_json_success(array(
			'data'  => $aCountAverageRatingOfWeek,
			'range' => $start . ' - ' . $end
		));
    }

	public function fetchGeneralRatings(){
		$this->middleware(['isUserLoggedIn'], array());
	    $userID = get_current_user_id();
	    $averageRating  = ReviewModel::getAuthorAverageRatings($userID);
	    $mondayThisWeek = Time::mysqlDate(strtotime('monday this week'));
	    $sundayThisWeek = Time::mysqlDate(strtotime('sunday this week'));

		$mondayLastWeek = Time::mysqlDate(strtotime('monday last week'));
		$sundayLastWeek = Time::mysqlDate(strtotime('sunday last week'));

		$averageRatingThisWeek = ReviewModel::getAuthorAverageRatingsInRange($userID, $mondayThisWeek, $sundayThisWeek);
		$averageRatingLastWeek = ReviewModel::getAuthorAverageRatingsInRange($userID, $mondayLastWeek, $sundayLastWeek);

		$is = 'up';
		if ( $averageRatingThisWeek == $averageRatingLastWeek ){
			$status = '';
			$percentage = 0;
        }else{
			$percentage = empty($averageRatingLastWeek) ? round($averageRatingThisWeek*100, 2) : round(($averageRatingThisWeek/$averageRatingLastWeek)*100, 2);

			if ( $averageRatingThisWeek < $averageRatingLastWeek ){
				$percentage = -$percentage;
				$status = 'red';
				$is = 'down';
			}else{
				$status = 'green';
            }
        }

        wp_send_json_success(
            array(
                'averageRating' => round($averageRating, 2),
                'mode'          => GetSettings::getOptions(General::getReviewKey('mode', 'listing')),
                'oChanging' => array(
                    'percentage' => $percentage.'%',
                    'status'     => $status,
                    'is'         => $is
                )
            )
        );
    }

	public function addMiddleWareToReviewHandler($aMiddleware){
	    return array_merge($aMiddleware, ['isPublishedPost']);
    }

    public static function isEnableGallery($postType){
	    return GetSettings::getOptions(General::getReviewKey('toggle_gallery', $postType));
    }

	public static function isEnableRating($post=null){
	    if ( empty($post) ){
	        global $post;
        }

        if ( !isset($post->post_type) ){
	        return false;
        }

        $toggle = GetSettings::getOptions(General::getReviewKey('toggle', $post->post_type));

		return apply_filters('wilcity/is_enable_rating', $toggle);
	}

	public function postComment(){
	    $this->middleware(['isPublishedPost'], array(
            'postID' => $_POST['postID']
        ));

		if ( !isset($_POST['content']) || empty($_POST['content']) ){
			wp_send_json_error(array(
				'msg' => esc_html__('We need your comment.', 'wiloke-listing-tools')
            ));
		}

	    if ( !is_user_logged_in() ){
            if ( !get_option('comment_registration') ){
                wp_send_json_error(array(
                    'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
                ));
            }else{
                if ( !is_email($_POST['email']) ){
	                wp_send_json_error(array(
                        'type'=>'email',
		                'msg' => esc_html__('You entered an invalid email.', 'wiloke-listing-tools')
	                ));
                }

	            if ( email_exists($_POST['email']) ){
		            wp_send_json_error(array(
			            'type'=>'email',
			            'msg' => esc_html__('This email is existed', 'wiloke-listing-tools')
		            ));
	            }

	            if ( username_exists($_POST['email']) ){
		            wp_send_json_error(array(
			            'type'=>'email',
			            'msg' => esc_html__('This email is existed', 'wiloke-listing-tools')
		            ));
	            }

	            $userName = sanitize_text_field($_POST['email']);
	            $password = uniqid();
	            $userID = wp_insert_user(array(
		            'user_login'  =>  $userName,
		            'user_url'    =>  '',
		            'display_name'=>  sanitize_text_field($_POST['name']),
		            'user_pass'   =>  md5($password)
	            ));

	            if ( $userID && !is_wp_error($userID) ){
		            wp_set_current_user($userID, $userName);
		            wp_set_auth_cookie($userID);
                }else{
		            wp_send_json_error(array(
			            'type'=>'email',
			            'msg' => esc_html__('We could not insert a new account.', 'wiloke-listing-tools')
		            ));
                }
            }
        }

	    $postType = get_post_type($_POST['postID']);
	    $html = apply_filters('wilcity/ajax/post-comment/'.$postType, $_POST);
	    if ( is_array($html) ){
	        wp_send_json_error(
                array(
	                'type'=>'general',
                    'msg' => esc_html__('Oops! There are no handler', 'wiloke-listing-tools')
                )
            );
        }
    }

	public function pinReviewToTop(){
	    $this->middleware(['isPublishedPost', 'isPostAuthor', 'isReviewExists'], array(
            'postType' => 'listing',
            'reviewID'   => $_POST['reviewID'],
            'postID'   => $_POST['postID'],
            'passedIfAdmin' => true
        ));

        $stickyID = GetSettings::getPostMeta($_POST['postID'], 'sticky_review');
        if ( !empty($stickyID) ){
            PostModel::setMenuOrder($stickyID, 0);
        }

        if ( $stickyID != $_POST['reviewID'] ){
	        PostModel::setMenuOrder($_POST['reviewID'], 100);
	        SetSettings::setPostMeta($_POST['postID'], 'sticky_review', $_POST['reviewID']);
	        $is = 'added';
        }else{
            SetSettings::deletePostMeta($_POST['postID'], 'sticky_review');
	        PostModel::setMenuOrder($_POST['reviewID'], 0);
	        $is = 'removed';
        }
        wp_send_json_success(array(
            'is' => $is
        ));
    }

	public function fetchReviewGeneral(){
	    if ( empty($_GET['postID']) ){
	        wp_send_json_error();
        }

        $aData = ReviewMetaModel::getGeneralReviewData($_GET['postID']);
	    wp_send_json_success($aData);
    }

    public static function getReviewInfo($oReview, $parentID, $isFetchingDiscussion=false){
	    $aReview['ID']       = $oReview->ID;
	    $aReview['avatar']   = User::getAvatar($oReview->post_author);
	    $aReview['position'] = User::getPosition($oReview->post_author);
	    $aReview['displayName'] = User::getField('display_name', $oReview->post_author);
	   $parentPostType = get_post_type($parentID);

	    $average = ReviewMetaModel::getAverageReviewsItem($oReview->ID);
	    if ( !empty($average) ){
		    $aReview['oRating']['average'] = $average;
		    $aReview['oRating']['mode'] = ReviewModel::getReviewMode($parentPostType);
		    $aReview['oRating']['quality'] = ReviewMetaModel::getReviewQualityString($average, $parentPostType);
	    }else{
		    $aReview['oRating'] = false;
	    }

	    $aReview['postDate'] = Time::getPostDate($oReview->post_date);
	    $aReview['postContent'] = nl2br(get_post_field('post_content', $oReview->ID));
	    $aReview['gallery'] = self::parseGallery($oReview->ID);
	    $aReview['postTitle'] = get_the_title($oReview->ID);
	    $aReview['postLink'] = add_query_arg(
            array(
                '#tab'=>'reviews',
                'reviewID' => $oReview->ID
            ),
		    get_permalink($parentID)
		);
		$aReview['authorLink'] = get_author_posts_url($oReview->post_author);
	    $aReview['countLiked'] = ReviewMetaModel::countLiked($oReview->ID);
	    $aReview['countDiscussion'] = ReviewMetaModel::countDiscussion($oReview->ID);
	    $aReview['countShared'] = abs(GetSettings::getPostMeta($oReview->ID, 'count_shared'));
	    $aReview['isLiked'] = self::isLikedReview($oReview->ID, true);
	    $aReview['isAuthor'] = 'no';
	    $aReview['isAdmin'] = 'no';
	    $aReview['parentID'] = $parentID;
	    $aReview['isPintToTop'] = !empty($oReview->menu_order) ? 'yes' : 'no';

	    if ( User::isUserLoggedIn() ){
	        $userID = get_current_user_id();
	        if ( $userID == $oReview->post_author ){
		        $aReview['isAuthor'] = 'yes';
            }

            if ( current_user_can('edit_theme_options') ){
	            $aReview['isAdmin'] = 'yes';
            }

		    if ( get_current_user_id() == $parentID ){
			    $aReview['isParentAuthor'] = 'yes';
		    }
        }

	    if ( !$isFetchingDiscussion ){
		    $aReview['isEnabledDiscussion'] = self::isEnabledDiscussion(get_post_type($parentID)) ? 'yes' : 'no';
		    $oRawDiscussions = ReviewModel::getReviews($oReview->ID,  array('postsPerPage'=>WILCITY_NUMBER_OF_DISCUSSIONS));
		    if ( !$oRawDiscussions ){
			    $aReview['aDiscussions'] = false;
		    }else{
			    $aReview['aDiscussions'] = array();
			    $aReview['maxDiscussions'] = $oRawDiscussions->found_posts;
			    while ($oRawDiscussions->have_posts()){
				    $oRawDiscussions->the_post();
				    $aReview['aDiscussions'][] = self::getReviewInfo($oRawDiscussions->post, $oReview->ID, true);
			    }
		    }
        }

        return $aReview;
	}

	public function getUserInfo($userID){
		$aUser['avatar'] = User::getAvatar($userID);
		$aUser['position'] = User::getPosition($userID);
		$aUser['displayName'] = User::getField('display_name', $userID);
		return $aUser;
    }

	public function fetchReviewsTab(){
	    $parentID = abs($_POST['postID']);
	    $aArgs['page'] = $_POST['page'];
	    if ( isset($_POST['postsPerPage']) && !empty($_POST['postsPerPage']) ){
	        $aArgs['postsPerPage'] = abs($_POST['postsPerPage']);
        }
        $oReviews = ReviewModel::getReviews($parentID, $aArgs);
        $aReviews = array();

        if ( $oReviews ){
	        $aUser = $this->getUserInfo(User::getCurrentUserID());
	        while ($oReviews->have_posts()){
	            $oReviews->the_post();
                $aReviews[] = self::getReviewInfo($oReviews->post, $parentID, false);
			}
			wp_reset_postdata();

            wp_send_json_success(
                array(
                    'reviews'   => $aReviews,
                    'maxPosts'  => $oReviews->found_posts,
                    'maxPages'  => $oReviews->max_num_pages,
                    'user'      => $aUser
                )
            );
        }else{
            if ( $_POST['page'] != 1 ){
                wp_send_json_success(
                    array(
                        'isFinished' => 'yes'
                    )
                );
            }
            wp_send_json_error(array(
                'msg' => esc_html__('There are no reviews!', 'wiloke-listing-tools')
            ));
        }
    }

	public static function getMode($postType){
		$mode = GetSettings::getOptions(General::getReviewKey('mode', $postType));
		$mode = empty($mode) ? 5 : absint($mode);
		return $mode;
    }

	public static function getNewReviewStatus($postType){
	    if ( User::currentUserCan('administrator') ){
	        return 'publish';
        }
		$isImmediatelyApproved = GetSettings::getOptions(General::getReviewKey('is_immediately_approved', $postType));
		$isImmediatelyApproved = !empty($isImmediatelyApproved) ? $isImmediatelyApproved : 'no';

		if ( $isImmediatelyApproved == 'yes' ){
		    return 'publish';
        }
		return 'pending';
	}

	public static function getDetailsSettings($postType){
		return GetSettings::getOptions(General::getReviewKey('details', $postType));
	}

	public function printReviewSettings(){
	    $aPostTypeKeys = General::getPostTypeKeys(false);

	    if ( !is_user_logged_in() || !is_singular($aPostTypeKeys) ){
	        return false;
        }

        global $post;
	    if ( !isset($post->post_type) ){
	        return false;
        }

        $toggle = GetSettings::getOptions(General::getReviewKey('toggle', $post->post_type));
	    if ( empty($toggle) || $toggle == 'disable' ){
	        return false;
        }

        $aDetails = GetSettings::getOptions(General::getReviewKey('details', $post->post_type));
	    $mode = GetSettings::getOptions(General::getReviewKey('mode', $post->post_type));

	    wp_localize_script('jquery-migrate', strtoupper(WILCITY_WHITE_LABEL).'_REVIEW_SETTINGS', array(
            'mode'     => $mode,
            'details'  => $aDetails
        ));
    }

	public function deleteReview(){
	    $this->middleware(['isPostAuthor'], array(
            'postID' => $_POST['postID'],
            'passedIfAdmin' => true
        ));

	    wp_delete_post($_POST['postID']);
    }

	public static function isLikedReview($reviewID, $returnYesNoOnly=false){
        $status = ReviewMetaModel::isLiked($reviewID);
        if ( $returnYesNoOnly ){
	        return $status ? 'yes' : 'no';
        }

		if ( $status ){
		    return array(
                'is'    => esc_html__('Liked', 'wiloke-listing-tools'),
                'class' => 'liked color-primary'
            );
        }

        return array(
	        'is'    => esc_html__('Like', 'wiloke-listing-tools'),
	        'class' => ''
        );
    }

    public static function isEnabledDiscussion($postType){
	    $toggleDiscussion = GetSettings::getOptions(General::getReviewKey('toggle_review_discussion', $postType));
	    return $toggleDiscussion == 'enable';
    }

	public static function isEnabledReview($postType){
		$toggleDiscussion = GetSettings::getOptions(General::getReviewKey('toggle', $postType));
		return $toggleDiscussion == 'enable';
	}

	public function likeReview(){
	    $this->middleware(['isPublishedPost', 'isPostType'], array(
            'postID'    => $_POST['reviewID'],
            'postType'  => 'review'
        ));

		$likedID = is_user_logged_in() ? get_current_user_id() : General::clientIP();
		$aLikedReview = GetSettings::getPostMeta($_POST['reviewID'], wilokeListingToolsRepository()->get('reviews:liked'));

	    if ( empty($aLikedReview)  ){
            $aNewLikedReview = array($likedID);
        }else if ( !in_array($likedID, $aLikedReview) ){
		    $aNewLikedReview = array_push($aLikedReview, $likedID);
        }else{
		    $aLikedReview = array_flip($aLikedReview);
            unset($aLikedReview[$likedID]);
		    $aNewLikedReview = array_flip($aLikedReview);
        }

		SetSettings::setPostMeta($_POST['reviewID'], 'liked', $aNewLikedReview);

        wp_send_json_success(
            array(
                'countLiked' => count($aNewLikedReview) . ' ' . esc_html__('Liked', 'wiloke-listing-tools')
            )
        );
    }

	public function checkReviewStatus($aTabs, $post){
	    if ( empty($aTabs) ){
	        return $aTabs;
        }

	    $status = GetSettings::getOptions(General::getReviewKey('toggle', $post->post_type));

        if ( $status == 'disable' || !$status ){
            unset($aTabs['reviews']);
        }
        return $aTabs;
    }

	public function deleteDiscussion(){
		$this->middleware(['isReviewExists', 'isUserLoggedIn', 'isPostAuthor'], array(
			'reviewID'  => $_POST['reviewID'],
			'postID'    => $_POST['reviewID'],
            'passedIfAdmin' => 'yes'
		));
        wp_delete_post($_POST['reviewID'], true);
    }

	private function updateReviewDiscussion($discussionID, $content){
		wp_update_post(array(
			'ID'           => $discussionID,
			'post_content' => $content
		));
	}

	public function appSetReviewDiscussion($reviewID, $content){
		return $this->setReviewDiscussion($reviewID, $content);
	}

	public function appUpdateReviewDiscussion($discussionID, $content){
		return $this->updateReviewDiscussion($discussionID, $content);
	}

	public function updateDiscussion(){
		$this->middleware(['isPostAuthor'], array(
			'postID'        => $_POST['discussionID'],
			'passedIfAdmin' => true
		));

		$this->updateReviewDiscussion($_POST['discussionID'], $_POST['content']);
		wp_send_json_success();
	}

	private function getDiscussionInfo($reviewID){
		$parentID = wp_get_post_parent_id($reviewID);
		$oDiscussion = get_post($reviewID);
		$aDiscussionInfo = self::getReviewInfo($oDiscussion, $parentID, true);
		do_action('wilcity/review/discussion', $parentID, $parentID);
		return $aDiscussionInfo;
    }

    private function setReviewDiscussion($parentID, $content){
	    $discussionPostType = get_post_type($parentID) == 'event_comment' ? 'event_comment' : 'review';
	    $commentID = ReviewModel::setDiscussion($parentID, $discussionPostType, $content);
        return $commentID;
    }

	public function ajaxBeforeSetReviewDiscussion(){
	    $aMiddleware = array('isReviewExists', 'isUserLoggedIn');
	    $postType = get_post_type($_POST['parentID']);

		$aMiddleware = apply_filters('wilcity/addMiddlewareToReview/of/'.$postType, $aMiddleware);
		$aMiddlewareOptions = apply_filters('wilcity/addMiddlewareOptionsToReview/of/'.$postType, array(
			'reviewID' => $_POST['parentID']
		));
		$this->middleware($aMiddleware, $aMiddlewareOptions);
		$discussionID = $this->setReviewDiscussion($_POST['parentID'], $_POST['content']);
		$aDiscussionInfo = $this->getDiscussionInfo($discussionID);
        wp_send_json_success($aDiscussionInfo);
    }

	private function handleUpdateLike($reviewID, $userID, $isApp=false){
        $aLiked = GetSettings::getPostMeta($reviewID, wilokeListingToolsRepository()->get('reviews:liked'));
        if ( empty($aLiked) || !in_array($userID, $aLiked) ){
            if ( empty($aLiked) ){
                $aLiked = array($userID);
            }else{
	            array_push($aLiked, $userID);
            }
	        $isLiked = true;
        }else{
            $key = array_search($userID, $aLiked);
            unset($aLiked[$key]);
	        $isLiked = false;
        }
        $countLiked = count($aLiked);

		SetSettings::setPostMeta($reviewID, wilokeListingToolsRepository()->get('reviews:liked'), $aLiked);
        SetSettings::setPostMeta($reviewID, 'total_liked', count($aLiked));

		if ( !$isApp ){
			wp_send_json_success(
				array(
					'numberOfLiked' => $countLiked
				)
			);
        }

        return array(
            'status'    => 'success',
            'isLiked'   => $isLiked,
            'countLiked'=> $countLiked
        );
    }

    public function updateLikeReviewViaApp($reviewID, $userID){
	    return $this->handleUpdateLike($reviewID, $userID, true);
    }

    public function updateLike(){
	    $this->middleware(['isReviewExists'], array(
		    'reviewID' => $_POST['reviewID']
	    ));

	    $userID = is_user_logged_in() ? User::getCurrentUserID() : General::clientIP();
        $this->handleUpdateLike($_POST['reviewID'], $userID);
    }

    public static function getReviewDetails($reviewID, $isEditing=false){
	    $parentID = wp_get_post_parent_id($reviewID);
        $aCategoriesSettings = GetSettings::getOptions(General::getReviewKey('details', get_post_type($parentID)));

        if ( empty($aCategoriesSettings) ){
            return false;
        }
        
        $aDetails = array();
        if ( $isEditing ){
	        foreach ($aCategoriesSettings as $aCategorySetting){
		        $aDetails[$aCategorySetting['key']]['name']  = $aCategorySetting['name'];
		        $aDetails[$aCategorySetting['key']]['value'] = ReviewMetaModel::getReviewMeta($reviewID, $aCategorySetting['key']);
	        }
        }else{
	        foreach ($aCategoriesSettings as $aCategorySetting){
		        $aDetails[$aCategorySetting['key']] = ReviewMetaModel::getReviewMeta($reviewID, $aCategorySetting['key']);
	        }
        }

        return $aDetails;
	}

	public static function parseGallery($postID){
		$aGallery = GetSettings::getPostMeta($postID, 'gallery');
		$aParsedGallery = array();

		if ( !empty($aGallery) ){
			foreach ($aGallery as $galleryID => $source){
				$aSetupGallery['medium'] = wp_get_attachment_image_url($galleryID, 'medium');
				$aSetupGallery['src'] = $source;
				$aSetupGallery['link'] = $source;
				$aSetupGallery['full'] = $source;
				$aParsedGallery[] = $aSetupGallery;
			}
		}

		return $aParsedGallery;
    }

	public static function getAllReviewMeta($reviewID, $userID, $aDetails, $aReviewQualities=array()){
        $aReviewMeta = array();
		$score = 0;
	    if ( !empty($aDetails) ){
	        $score = 0;
            foreach ($aDetails as $aDetail){
	            $score += absint(ReviewMetaModel::getReviewMeta($reviewID, $aDetail['key']));
            }
		    $score = round($score/count($aDetails), 2);
        }

		$aGallery = GetSettings::getPostMeta($reviewID, 'gallery');

	    if ( !empty($aGallery) ){
	        foreach ($aGallery as $galleryID => $source){
                $aSetupGallery['medium'] = wp_get_attachment_image_url($galleryID, 'medium');
                $aSetupGallery['src'] = $source;
                $aSetupGallery['link'] = $source;
                $aReviewMeta['gallery'][] = $aSetupGallery;
            }
        }else{
		    $aReviewMeta['gallery'] = array();
        }

        if ( !empty($aReviewQualities) ){
	        foreach ($aReviewQualities as $approachScore => $name){
	            if ( empty($score) ){
		            $aReviewMeta['quality']   = '';
                }else{
		            $approachScore = abs($approachScore);

		            if ( $score < $approachScore ){
			            $aReviewMeta['quality']   = $name;
		            }else{
			            break;
		            }
                }
	        }
        }

	    $aReviewMeta['user']    = GetSettings::getUserData($userID);
	    $aReviewMeta['score']   = $score;

	    return $aReviewMeta;
    }

	public static function fetchSomeReviews($aArgs){
	    global $post;
        return ReviewModel::getReviews($post->ID, $aArgs);
    }

	public function fetchUserReviewedData(){
	    $reviewID = $_POST['reviewID'];

	    $this->middleware(['isPostAuthor'], array(
            'postID'            => $reviewID,
            'passedIfAdmin'     => true
        ));

	    $oReview = get_post($reviewID);
	    if ( empty($oReview) || is_wp_error($oReview) ){
	        wp_send_json_error(array(
                'msg' => esc_html__('You do not permission to access this review', 'wiloke-listing-tools')
            ));
        }

	    $aReview['title']   = $oReview->post_title;
	    $aReview['content'] = $oReview->post_content;
		$aReview['details'] = self::getReviewDetails($reviewID, true);

        $aGallery = GetSettings::getPostMeta($reviewID, 'gallery');
        if ( empty($aGallery) ){
            $aReview['gallery'] = '';
        }else{
            foreach ($aGallery as $imgID => $src){
                $aX['imgID'] = $imgID;
                $aX['src'] = $src;
                $aReview['gallery'][] = $aX;
            }
        }

		wp_send_json_success($aReview);
    }

	public function printFooter(){
	    $aListings = General::getPostTypeKeys(false, true);

	    if ( !is_singular($aListings) ){
	        return '';
        }

	    global $post;
		$toggleGallery = GetSettings::getOptions(General::getReviewKey('toggle_gallery', $post->post_type));
		?>
		<review-popup popup-title="<?php esc_html_e('Write a review', 'wiloke-listing-tools'); ?>" btn-name="<?php esc_html_e('Submit Review', 'wiloke-listing-tools'); ?>" icon="la la-star-o" review-title-label="<?php esc_html_e('Title of review', 'wiloke-listing-tools'); ?>" review-content-label="<?php esc_html_e('Your review', 'wiloke-listing-tools'); ?>" toggle-gallery="<?php echo esc_attr($toggleGallery); ?>"></review-popup>
		<?php
	}

	public function handleSubmitReview($listingID, $aData, $reviewID='', $isApp=false){
		$this->middleware(['isUserLoggedIn', 'isPublishedPost', 'verifyReview', 'isReviewEnabled', 'isAccountConfirmed'], array(
			'postID' => $listingID,
			'aData'  => $aData
		));


		$parentID = abs($listingID);
		$postType = get_post_type($parentID);
		$isNewSubmitted = false;

		if ( isset($reviewID) && !empty($reviewID) ){
			$this->middleware(['isPostAuthor'], array(
				'postID' => $reviewID
			));
			wp_update_post(
				array(
					'ID'            => $reviewID,
					'post_type'     => 'review',
					'post_title'    => $aData['title'],
					'post_content'  => $aData['content']
				)
			);
		}else{
			$isNewSubmitted = true;
			if ( ReviewModel::isUserReviewed($parentID) ){
			    if ( !$isApp ){
				    wp_send_json_error(
					    array(
						    'msg' => esc_html__('You already left a review before.', 'wiloke-listing-tools')
					    )
				    );
                }else{
                    return array(
                        'status' => 'error',
                        'msg'    => 'youLeftAReviewBefore'
                    );
                }
			}

			$reviewID = wp_insert_post(
				array(
					'post_type'     => 'review',
					'post_title'    => $aData['title'],
					'post_content'  => $aData['content'],
					'post_author'   => User::getCurrentUserID(),
					'post_parent'   => $parentID,
					'post_status'   => self::getNewReviewStatus($postType)
				)
			);
		}

		if ( !$reviewID ){
			if ( !$isApp ){
				wp_send_json_error(
					array(
						'msg' => esc_html__('Oops! We could not insert the review', 'wiloke-listing-tools')
					)
				);
			}else{
				return array(
					'status' => 'error',
					'msg'    => 'couldNotInsertReview'
				);
            }
		}

		$this->aReviewSettings['toggle_gallery'] = GetSettings::getOptions(General::getReviewKey('toggle_gallery', $postType));
		$isNothingChange = true;
		$aOldGalleryIDs = array();

		if ( $this->aReviewSettings['toggle_gallery'] == 'enable' ){
			$userID = User::getCurrentUserID();
			$aRawGallery = isset($aData['gallery']) ? $aData['gallery'] : '';
			$isFakeFile = isset($aData['isFakeGallery']) ? $aData['isFakeGallery'] : false;
			$aOldGallery = GetSettings::getPostMeta($reviewID, 'gallery' );
			if ( !empty($aOldGallery) ){
				$aOldGalleryIDs = array_keys($aOldGallery);
			}else{
				$isNothingChange = false;
			}

			if ( empty($aRawGallery) ){
				SetSettings::deletePostMeta($reviewID, 'gallery');
			}else{
				$aNewGalleryIDs = array();
				$aGallery = array();
				$aRawGallery = !is_array($aRawGallery) && isJson($aRawGallery) ? json_decode($aRawGallery, true) : $aRawGallery;

				foreach ($aRawGallery as $order => $aItem){
					if ( isset($aItem['imgID']) && !empty($aItem['imgID']) ){
						$aGallery[$aItem['imgID']] = $aItem['src'];
						$aNewGalleryIDs[] = abs($aItem['imgID']);
					}else if(isset($aItem['id']) && !empty($aItem['id'])){
						$aGallery[$aItem['id']] = $aItem['src'];
						$aNewGalleryIDs[] = abs($aItem['id']);
					}else{
						$instUploadImg = new Upload();
						$instUploadImg->userID = $userID;

						if ( !$isFakeFile ){
							$instUploadImg->aData['imageData']  = $aItem['src'];
							$instUploadImg->aData['fileName']   = $aItem['fileName'];
							$instUploadImg->aData['fileType']   = $aItem['fileType'];
							$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder();
							$imgID = $instUploadImg->image();
                        }else{
							$instUploadImg->aData['aFile'] = $aItem;
							$imgID = $instUploadImg->uploadFakeFile();
						}

						if ( !empty($imgID) && is_numeric($imgID) ){
							$aGallery[$imgID] = wp_get_attachment_image_url($imgID, 'large');
							$aNewGalleryIDs[] = $imgID;
							$isNothingChange = false;
                        }
					}
				}

				if ( $isNothingChange ){
					$aDifferent = array_diff($aNewGalleryIDs, $aOldGalleryIDs);

					if ( empty($aDifferent) && (count($aNewGalleryIDs) == count($aOldGalleryIDs)) ){
						$isNothingChange = true;
					}else{
						$isNothingChange = false;
					}
				}

				if ( !$isNothingChange ){
					if ( !empty($aOldGalleryIDs) ){
						foreach ($aOldGalleryIDs as $oldID){
							if ( !in_array($oldID, $aNewGalleryIDs) ){
								Upload::deleteImg($oldID);
							}
						}
					}
					if ( !empty($aGallery) ){
						SetSettings::setPostMeta($reviewID, 'gallery', $aGallery);
					}
				}
			}
		}

		$this->aReviewSettings['details']   = self::getDetailsSettings($postType);
		$this->aReviewSettings['mode']      = self::getMode($postType);

		if ( !empty($this->aReviewSettings['details']) ){
			foreach ($this->aReviewSettings['details'] as $aDetail){
				$score = isset($aData['details'][$aDetail['key']]['value']) ? absint($aData['details'][$aDetail['key']]['value']) : 5;
				if ( empty($score) ){
					continue;
				}
				if ( $score > $this->aReviewSettings['mode']){
					$score = $this->aReviewSettings['mode'];
				}
				ReviewMetaModel::setReviewMeta($reviewID, $aDetail['key'], $score);
			}
		}

		$aResponse['reviewID'] = $reviewID;
		$aResponse['isNewSubmitted'] = $isNewSubmitted;
		if ( !$isNewSubmitted ){
			$aResponse['averageReviewScore'] = ReviewMetaModel::getAverageReviews($parentID);
			$aResponse['reviewQuality']      = ReviewMetaModel::getReviewQualityString($aResponse['averageReviewScore'], $postType);
			if ( !$isNothingChange ){
				$aRawGallery = GetSettings::getPostMeta($reviewID, 'gallery');
				$aNewGallery = array();
				if ( !empty($aRawGallery) ){
					foreach ($aRawGallery as $galleryID => $src){
						$aX['imgID']   = $galleryID;
						$aX['src']     = $src;
						$aNewGallery[] = $aX;
					}
                }
				$aResponse['gallery'] = $aNewGallery;
			}
		}

		$oReview = get_post($reviewID);
		$aReviewInfo = self::getReviewInfo($oReview, $parentID, true);
		$averageReview = ReviewMetaModel::getAverageReviews($parentID);

		SetSettings::setPostMeta($parentID, 'average_reviews', $averageReview);

		do_action('wilcity/submitted-new-review', $reviewID, $parentID, User::getCurrentUserID());

		$isRefresh = 'yes';
		$msg = '';
		if ( get_post_status($reviewID) !== 'publish' ){
			$msg = __('Your comment has been received and is being reviewed by our team staff. It will be published after approval.', 'wiloke-listing-tools');
			$isRefresh = 'no';
		}
		if ( !$isApp ){
			wp_send_json_success(array(
				'reviews' => array($aReviewInfo),
				'reviewID' => $reviewID,
				'isRefresh' => $isRefresh,
				'msg'   => $msg
			));
        }else{
            return array(
                'status'        => 'success',
                'reviewID'      => $reviewID,
                'reviewStatus'  => get_post_status($reviewID)
            );
        }
    }

	public function submitReview(){
        $this->handleSubmitReview($_POST['listingID'], $_POST['data'], $_POST['reviewID']);
	}

	public static function isSticky($oReview){
        if ( self::$foundSticky && $oReview->ID !== self::$stickyID ){
            return false;
        }

	    if ( !empty($oReview->menu_order) ){
		    self::$foundSticky = true;
		    self::$stickyID = $oReview->ID;
	        return true;
        }
    }

    public function deleteReviewPopup(){
	    if ( !is_user_logged_in() ){
	        return '';
        }
	    ?>
        <delete-review-popup title="<?php esc_html_e('Are you sure that want to delete this author?', 'wiloke-listing-tools'); ?>" yes="<?php esc_html_e('Yes', 'wiloke-listing-tools'); ?>" cancel="<?php esc_html_e('Cancel', 'wiloke-listing-tools'); ?>"></delete-review-popup>
        <?php
    }

    public static function toTenMode($score, $mode){
	    if ( $mode == 10 ){
	        return $score;
        }

        return floor($score)*2;
    }
}
