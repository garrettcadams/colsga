<?php

namespace WILCITY_APP\Controllers;

use \WilokeListingTools\Controllers\ReviewController as WebReviewController;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;


class ReviewController extends Controller {
	use VerifyToken;
	use ParsePost;
	use JsonSkeleton;
	use Message;

	private $reviewID = '';
	private $postID = '';
	private $discussionID = '';
	private $commentID = '';
	private $oToken;
	private $aReviewData;
	private $oRestData;
	private $isDeleteReview=false;

	public function __construct() {
		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/reviews', array(
				'methods' => 'GET',
				'callback' => array($this, 'getReviewsViaRestAPI'),
			));
		});

		add_filter('wilcity/wilcity-mobile-app/filter/wilcity-reviews', array($this, 'generateReviewsForShortcode'), 10, 2);

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/review-fields/(?P<postID>\w+)', array(
				'methods' => 'GET',
				'callback' => array($this, 'getReviewFields'),
			));
		} );

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/posts/(?P<postID>\w+)/reviews', array(
				'methods' => 'GET',
				'callback' => array($this, 'getReviewsOfPost'),
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/posts/(?P<postID>\w+)/reviews/(?P<reviewID>\w+)', array(
				'methods' => 'GET',
				'callback' => array($this, 'getReview'),
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/posts/(?P<postID>\w+)/reviews/(?P<reviewID>\w+)', array(
				'methods' => 'POST',
				'callback' => array($this, 'updateReview')
			));
		});

        add_action('rest_api_init', function(){
            register_rest_route( WILOKE_PREFIX.'/v2', '/posts/(?P<postID>\w+)/reviews', array(
                'methods' => 'POST',
                'callback' => array($this, 'postReview')
            ));
        });

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/posts/(?P<postID>\w+)/reviews/(?P<reviewID>\w+)', array(
				'methods' => 'DELETE',
				'callback' => array($this, 'deleteReview')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/reviews/(?P<reviewID>\w+)/like', array(
				'methods' => 'POST',
				'callback' => array($this, 'updateLikeReview')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/reviews/(?P<reviewID>\w+)/discussions', array(
				'methods' => 'POST',
				'callback' => array($this, 'postReviewDiscussion')
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/reviews/(?P<reviewID>\d+)/discussions', array(
				'methods' => 'GET',
				'callback' => array($this, 'getDiscussions')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/reviews/(?P<reviewID>\w+)/discussions/(?P<discussionID>\w+)', array(
				'methods' => 'PUT',
				'callback' => array($this, 'updateReviewDiscussion')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/reviews/(?P<reviewID>\w+)/discussions/(?P<discussionID>\w+)', array(
				'methods' => 'DELETE',
				'callback' => array($this, 'deleteReviewDiscussion')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/reviews/(?P<reviewID>\w+)/share', array(
				'methods' => 'POST',
				'callback' => array($this, 'updateReviewCountShares')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/events/(?P<eventID>\w+)/discussions', array(
				'methods' => 'POST',
				'callback' => array($this, 'postEventDiscussion')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/events/(?P<eventID>\w+)/discussions/(?P<discussionID>\w+)', array(
				'methods' => 'PUT',
				'callback' => array($this, 'updateEventDiscussion')
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/events/(?P<eventID>\w+)/discussions', array(
				'methods' => 'GET',
				'callback' => array($this, 'getEventDiscussions'),
			));
		});


		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/events/(?P<eventID>\w+)/discussions/(?P<discussionID>\w+)', array(
				'methods' => 'DELETE',
				'callback' => array($this, 'deleteEventDiscussion'),
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/discussions/(?P<discussionID>\w+)/like', array(
				'methods' => 'POST',
				'callback' => array($this, 'updateEventDiscussionLiked')
			));
		});


		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/discussions/(?P<discussionID>\w+)/comments', array(
				'methods' => 'POST',
				'callback' => array($this, 'postCommentOnEventDiscussion')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/discussions/(?P<discussionID>\w+)/comments/(?P<commentID>\w+)', array(
				'methods' => 'PUT',
				'callback' => array($this, 'updateCommentOnEventDiscussion')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/discussions/(?P<discussionID>\w+)/comments/(?P<commentID>\w+)', array(
				'methods' => 'DELETE',
				'callback' => array($this, 'deleteCommentOnEventDiscussion')
			));
		});

		add_action('rest_api_init', function(){
			register_rest_route( WILOKE_PREFIX.'/v2', '/discussions/(?P<discussionID>\w+)/share', array(
				'methods' => 'POST',
				'callback' => array($this, 'updateDiscussionCountShares')
			));
		});

//		add_action('rest_api_init', function(){
//			register_rest_route( WILOKE_PREFIX.'/v2', '/posts/(?P<postID>\d+)/reviews1/(?P<reviewID>\d+)', array(
//				'methods' => 'POST',
//				'callback' => array($this, 'testReview'),
//			));
//		});
	}

	/*
	 * @since 1.4.4
	 *
	 * @var $orderBy. You can filter Reviews by Order By
	 */
	public function getReviews($aData){
		if ( !isset($aData['number_of_reviews']) ){
			$numberOfReviews = 3;
		}else if ( $aData['number_of_reviews'] > 100 ){
			$numberOfReviews = 100;
		}else{
			$numberOfReviews = abs($aData['number_of_reviews']);
		}

		$orderBy = isset($aData['orderby']) ? $aData['orderby'] : 'top_liked';
		$offset = isset($aData['offset']) ? $aData['offset'] : 0;

		if ( $orderBy == 'specify_review_ids' ){
			if ( !isset($aData['review_ids']) ){
				$orderBy = 'latest_reviews';
			}
		}

		switch ($orderBy){
			case 'top_liked':
				$aReviewIDs = ReviewModel::getTopReviewsByLiked($numberOfReviews, $offset);
				break;
			case 'top_discussions':
				$aReviewIDs = ReviewModel::getTopReviewsByDiscussion($numberOfReviews, $offset);
				break;
			case 'specify_review_ids':
				$aReviewIDs = explode(',', $aData['review_ids']);
				break;
			default:
				$aReviewIDs = ReviewModel::getLatestReviews($numberOfReviews, $offset);
				break;
		}

		if ( empty($aReviewIDs) ){
			return array(
				'status' => 'error'
			);
		}

		$query = new \WP_Query(
			array(
				'post_type' => 'review',
				'post__in'  => $aReviewIDs,
				'orderby'   => 'post__in'
			)
		);

		if ( !$query->have_posts() ){
			return array(
				'status' => 'error'
			);
		}

		$aResponse = array();
		$aReviewDetails = array();
		while ($query->have_posts()){
			$query->the_post();
			$postParentType = get_post_type($query->post->post_parent);
			if ( !isset($aReviewDetails[$postParentType]) ){
				$aReviewDetails[$postParentType] = GetSettings::getOptions(General::getReviewKey('details', $postParentType));
			}

			$aReview['oReview'] = $this->getReviewItem($query->post, $query->post->post_parent, $aReviewDetails[$postParentType]);
			$aReview['oParent'] = array(
				'id'        => abs($query->post->post_parent),
				'title'     => get_the_title($query->post->post_parent),
				'tagline'   => GetSettings::getTagLine($query->post->post_parent),
				'link'      => get_permalink($query->post->post_parent),
				'author'    => $this->getUserInfo(get_post_field('post_author', $query->post->post_parent)) ,
				'image'     => $this->getFeaturedImg($query->post->post_parent) ,
				'logo'      => GetSettings::getLogo($query->post->post_parent)
			);

			$aResponse[] = $aReview;
		}
		wp_reset_postdata();

		return array(
			'status'    => 'success',
			'aResults'  => $aResponse
		);
	}

	public function getReviewsViaRestAPI(\WP_REST_Request $oRequest){
		$aData = $oRequest->get_params();
		return $this->getReviews($aData);
	}

	public function generateReviewsForShortcode($aResponse, $aData){
		return $this->getReviews($aData);
	}

	public function getReviewsOfPost(\WP_REST_Request $oRequest){
		$aData = $oRequest->get_params();
		if ( !isset($aData['page']) ){
			$aData['page'] = 1;
		}
		$aData['metaKey'] = 'reviews';
		$aData['target'] = $aData['postID'];
		return $this->getPostMeta($aData);
	}

	private function responseEventDiscussion($oDiscussion){
		$aResponse = array(
			'status'    => 'success',
			'ID'        => abs($oDiscussion->ID),
			'postDate'  => Time::getPostDate($oDiscussion->post_date),
			'msg'       => wilcityAppGetLanguageFiles('discussionUpdatedSuccessfully')
		);
		return $aResponse;
	}

	public function deleteEventDiscussion($oData){
		$this->discussionID = $oData->get_param('discussionID');
		$this->postID = $oData->get_param('eventID');
		$aStatus = $this->middleware(['isPostAuthor'], array(
			'postID' => $this->discussionID,
			'isApp'  => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		wp_delete_post($this->discussionID, true);
		$aResponse['status'] = 'success';
		return $aResponse;
	}

	public function updateEventDiscussion($oData){
		$this->discussionID = $oData->get_param('discussionID');
		$this->postID = $oData->get_param('eventID');
		$aStatus = $this->middleware(['isPostAuthor'], array(
			'postID' => $this->discussionID,
			'isApp'  => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		$content = $oData->get_param('content');
		if ( empty($content) ){
			return $this->error(wilcityAppGetLanguageFiles('discussionEmpty'));
		}

		apply_filters('wilcity/wilcity-mobile-app/put-event-discussion', $this->postID, $content, $this->discussionID);

		return $this->responseEventDiscussion(get_post($this->discussionID));
	}

	public function deleteCommentOnEventDiscussion($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}
		$this->oToken->getUserID();
		$this->commentID = $oData->get_param('commentID');

		$aStatus = $this->middleware(['isPostAuthor'], array(
			'postID'   => $this->commentID,
			'isApp' => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		wp_delete_post($this->commentID, true);

		return array('status'=>'success');
	}

	public function updateCommentOnEventDiscussion($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}
		$this->oToken->getUserID();
		$this->discussionID = $oData->get_param('discussionID');
		$this->commentID = $oData->get_param('commentID');

		$aStatus = $this->middleware(['isPostAuthor'], array(
			'postID'   => $this->commentID,
			'isApp' => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		$content = $oData->get_param('content');
		if ( empty($content) ){
			return $this->error(wilcityAppGetLanguageFiles('discussionEmpty'));
		}

		$oComment = apply_filters('wilcity/wilcity-mobile-app/put-event-discussion', $this->discussionID, $content, $this->commentID);

		return $this->responseEventDiscussion($oComment);
	}

	public function postCommentOnEventDiscussion($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();

		$this->discussionID = $oData->get_param('discussionID');

		if ( get_post_type($this->discussionID) != 'event_comment' ){
			return $this->error(403);
		}

		$content = $oData->get_param('content');

		if ( empty($content) ){
			return $this->error(wilcityAppGetLanguageFiles('discussionEmpty'));
		}

		$oDiscussion = apply_filters('wilcity/wilcity-mobile-app/post-event-discussion', $this->discussionID, $content);
		$aResponse = $this->responseEventDiscussion($oDiscussion);
		if ( $oDiscussion->post_status == 'publish' ){
			$aResponse['msg'] = wilcityAppGetLanguageFiles('discussionUpdatedSuccessfully');
		}else{
			$aResponse['msg'] = wilcityAppGetLanguageFiles('discussionBeingReviewed');
		}
		return $aResponse;
	}

	public function getEventDiscussions($aData){
		if ( !isset($aData['eventID']) || empty($aData['eventID']) ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('There are no discussions', 'wilcity-mobile-app')
			);
		}
		$eventID = abs($aData['eventID']);
		$paged = isset($aData['page']) ? abs($aData['page']) : 1;
		if ( !isset($aData['postsPerPage']) || $aData['postsPerPage'] == -1  ){
			$postsPerPage = -1;
		}else{
			$postsPerPage = isset($aData['postsPerPage']) ? abs($aData['postsPerPage']) : 6;
		}

		$query = new \WP_Query(
			$aArgs = array(
				'post_type'     => 'event_comment',
				'post_status'   => 'publish',
				'post_parent'   => $eventID,
				'paged'         => $paged,
				'posts_per_page'=> $postsPerPage
			)
		);

		if ( !$query->have_posts() ){
			return array(
				'status' => 'error',
				'msg'    => $paged > 1 ? esc_html__('All discussions have been loaded', 'wilcity-listing-tools') : esc_html__('We found no discussions', 'wilcity-listing-tools')
			);
		}
		$aComments = array();

		while ($query->have_posts()){
			$query->the_post();
			$aComments[] = $this->eventCommentItem($query->post);
		}
		wp_reset_postdata();

		$basedOnPostType = get_post_field('post_type', $eventID);
		if ( $basedOnPostType  == 'event_comment' ){
			$authorID = get_post_field('post_author', $eventID);
			$displayName = User::getField('display_name', $authorID);
			$repliedOn = sprintf(esc_html__('Replied on %s discussion', 'wiloke-mobile-app'), $displayName);
		}else{
			$title = get_the_title($eventID);
			$repliedOn = sprintf(esc_html__('All discussions on %s', 'wiloke-mobile-app'), $title);
		}

		return array(
			'status'           => 'success',
			'discussionsOn'    => $repliedOn,
			'countDiscussions' => GetSettings::countNumberOfChildrenReviews($eventID),
			'next'             => $query->max_num_pages > $paged ? $paged+1 : false,
			'oResults'         => $aComments
		);
	}

	public function postEventDiscussion($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();

		$this->postID = $oData->get_param('eventID');
		$aStatus = $this->middleware(['isPublishedPost'], array(
			'postID' => $this->postID,
			'isApp'  => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		$content = $oData->get_param('content');
		if ( empty($content) ){
			return $this->error(wilcityAppGetLanguageFiles('discussionEmpty'));
		}

		$oDiscussion = apply_filters('wilcity/wilcity-mobile-app/post-event-discussion', $this->postID, $content);
		$aResponse = $this->responseEventDiscussion($oDiscussion);
		if ( $oDiscussion->post_status == 'publish' ){
			$aResponse['msg'] = wilcityAppGetLanguageFiles('discussionUpdatedSuccessfully');
		}else{
			$aResponse['msg'] = wilcityAppGetLanguageFiles('discussionBeingReviewed');
		}
		return $aResponse;
	}

	public function updateEventDiscussionLiked($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();
		$this->discussionID = $oData->get_param('discussionID');

		$parent = wp_get_post_parent_id($this->discussionID);
		if ( get_post_type($parent) != 'event' ){
			return $this->error(403);
		}

		$aStatus = apply_filters('wilcity/wilcity-mobile-app/like-a-review', $this->discussionID, $this->oToken->userID);
		return $aStatus;
	}

	private function updateCountShares($postID){
		$aStatus = $this->middleware(['isPublishedPost'], array(
			'isApp'  => true,
			'postID' => $postID
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		$total = GetSettings::getPostMeta($postID, 'count_shared');
		$total = empty($total) ? 0 : abs($total);
		$total = $total + 1;
		SetSettings::setPostMeta($postID, 'count_shared', $total);
		return array(
			'status' => 'success',
			'countShares' => abs($total)
		);
	}

	public function updateDiscussionCountShares($oData){
		$this->discussionID = $oData->get_param('discussionID');
		$eventID = wp_get_post_parent_id($this->discussionID);

		$aStatus = $this->middleware(['isPostType'], array(
			'isApp'  => 'yes',
			'postID' => $eventID,
			'postType' => 'event'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		return $this->updateCountShares($this->discussionID);
	}

	public function updateReviewCountShares($oData){
		$this->reviewID = $oData->get_param('reviewID');

		$aStatus = $this->middleware(['isPostType'], array(
			'isApp'  => 'yes',
			'postID' => $this->reviewID,
			'postType' => 'review'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		return $this->updateCountShares($this->reviewID);
	}

	public function getDiscussions($aData){
		$page = isset($aData['page']) ? abs($aData['page']) : 1;
		if ( isset($aData['postsPerPage']) ){
			$postsPerPage = $aData['postsPerPage'] == -1 ? -1 : abs($aData['postsPerPage']);
		}else{
			$postsPerPage = 10;
		}

		$query = new \WP_Query(array(
			'post_type'      => 'review',
			'post_status'    => 'publish',
			'post_parent'    => $aData['reviewID'],
			'page'           => $page,
			'posts_per_page' => $postsPerPage
		));

		if ( $query->have_posts() ){
			global $post;

			$aResponse['total'] = $query->found_posts;
			$aResponse['maxPages'] = $query->max_num_pages;

			if ( $page < $query->max_num_pages ){
				$aResponse['next'] = $page+1;
			}else{
				$aResponse['next'] = false;
			}

			while ($query->have_posts()){
				$query->the_post();
				$aDiscussion['ID']   = abs($post->ID);
				$aDiscussion['postTitle']   = get_the_title($post->ID);
				$aDiscussion['postContent'] = strip_tags(get_post_field('post_content', $post->ID));
				$aDiscussion['postDate'] = get_the_date(get_option('date_format'), $post->ID);
				$aDiscussion['oUserInfo']       = $this->getUserInfo($post->post_author);
				$aResponse['aDiscussion'][] = $aDiscussion;
			}

			wp_reset_postdata();
		}else{
			$aResponse = false;
		}

		if ( empty($aResponse) ){
			return $this->error(wilcityAppStripTags('noDiscussion'));
		}

		return array(
			'status'   => 'success',
			'oResults' => $aResponse
		);
	}

	private function responseDiscussionInfo($discussionID){
		$reviewID = wp_get_post_parent_id($discussionID);

		return array(
			'oUserInfo' => array(
				'userID'        => abs($this->oToken->userID),
				'avatar'        => User::getAvatar($this->oToken->userID),
				'displayName'   => User::getField('display_name', $this->oToken->userID),
				'position'      => User::getField('position', $this->oToken->userID)
			),
			'ID'     => abs($discussionID),
			'postTitle'     => get_the_title($discussionID),
			'postContent'   => get_post_field('post_content', $discussionID),
			'postDate'      => Time::getPostDate(get_post_field('post_date', $discussionID)),
			'oReview' => array(
				'countComments' => ReviewMetaModel::countDiscussion($reviewID)
			)
		);
	}

	public function postReviewDiscussion($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();
		$this->reviewID = $oData->get_param('reviewID');

		$aData = $this->parsePost();

		$aStatus = $this->middleware(['isReviewExists'], array(
			'reviewID' => $this->reviewID,
			'isApp' => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		$discussionID = apply_filters('wilcity/wilcity-mobile-app/post-review-discussion', $this->reviewID, $aData['content']);
		$aResponse = $this->responseDiscussionInfo($discussionID);
		$aResponse['status'] = 'success';
		return $aResponse;
	}

	public function updateReviewDiscussion($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();
		$this->reviewID = $oData->get_param('reviewID');
		$this->discussionID = $oData->get_param('discussionID');
		$content = $oData->get_param('content');

		$aStatus = $this->middleware(['isPostAuthor'], array(
			'isApp'         => 'yes',
			'postID'        => $this->discussionID,
			'passedIfAdmin' => true
		));

		if ( empty($content) ){
			$this->error('discussionContentRequired');
		}

		if ( $aStatus !== true ){
			return $aStatus;
		}

		$aStatus = apply_filters('wilcity/wilcity-mobile-app/put-review-discussion', $this->discussionID, $content);
		$aStatus['status'] = 'success';
		return $aStatus;
	}

	public function deleteReviewDiscussion($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();
		$this->reviewID = $oData->get_param('reviewID');
		$this->discussionID = $oData->get_param('discussionID');

		$aStatus = $this->middleware(['isPostAuthor'], array(
			'postID' => $this->discussionID,
			'isApp' => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		wp_delete_post($this->discussionID, true);
		return array(
			'status' => 'success',
			'countDiscussions'  => abs(ReviewModel::countDiscussion($this->reviewID))
		);
	}

	public function updateLikeReview($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();
		$this->reviewID = $oData->get_param('reviewID');

		$this->middleware(['isReviewExists'], array(
			'reviewID' => $this->reviewID,
			'isApp' => 'yes'
		));

		$aStatus = apply_filters('wilcity/wilcity-mobile-app/like-a-review', $this->reviewID, $this->oToken->userID);
		return $aStatus;
	}

	private function validateReview(){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();
		$this->aReviewData = $this->parsePost();

		$aStatus = $this->middleware(['isUserLoggedIn', 'isPublishedPost', 'verifyReview', 'isReviewEnabled', 'isAccountConfirmed'], array(
			'postID' => $this->postID,
			'userID' => $this->oToken->userID,
			'aData'  => $this->aReviewData,
			'isApp'  => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		if ( !empty($this->reviewID) ){
			$aStatus = $this->middleware(['isOwnerOfReview'], array(
				'reviewID' => $this->reviewID,
				'reviewAuthorID'  => $this->oToken->userID,
				'isApp'  => 'yes'
			));
		}

		if ( $aStatus !== true ){
			return $aStatus;
		}

		return true;
	}

	private function afterSubmittingReview($aStatus, $isDelReview=false){
		$postType = get_post_type($this->postID);
		$aDetails = GetSettings::getOptions(General::getReviewKey('details', $postType));

		if ( $aStatus['status'] == 'success' ){
			$aReturn['msg'] = $aStatus['reviewStatus'] == 'publish' ? wilcityAppGetLanguageFiles('reviewSubmittedSuccessfully') : wilcityAppGetLanguageFiles('reviewBeingReviewed');

			if ( !$isDelReview ){
				$aStatus['oItem'] = $this->getReviewItem(get_post($aStatus['reviewID']), $this->postID, $aDetails);
			}

			$averageReviews = GetSettings::getPostMeta($this->postID, 'average_reviews');
			$aGeneralReviewsInfo = $this->getGeneralReviewInfo($this->postID, $postType);

			$aStatus['oGeneral'] = array(
				'mode'    => abs(GetSettings::getOptions(General::getReviewKey('mode', $postType))),
				'average' => floatval($averageReviews),
				'quality' => ReviewMetaModel::getReviewQualityString($averageReviews, $postType)
			);

			$aStatus['oGeneral'] = array_merge($aStatus['oGeneral'], $aGeneralReviewsInfo);
		}

		if ( !empty($this->reviewID) ){
			$aStatus['reviewID'] = $this->reviewID;
		}

		return $aStatus;
	}

	private function returnReviewStatus(){
		$aResponse = array('status' => 'success');
		$postType = get_post_type($this->postID);
		$aResponse['oGeneral'] = $this->getGeneralReviewInfo($this->postID, $postType);
		if ( !$this->isDeleteReview ){
			$aDetails = GetSettings::getOptions(General::getReviewKey('details', $postType));
			$aResponse['oItem'] = $this->getReviewItem(get_post($this->reviewID), $this->postID, $aDetails);
		}

		if ( !empty($this->reviewID) ){
			$aResponse['reviewID'] = abs($this->reviewID);
		}

		return $aResponse;
	}

	private function handleSubmitReview(){
		foreach ($this->aReviewData as $key => $value) {
			$this->aReviewData[$key] = stripslashes($value);
		}

		$aGallery = array();
		if ( isset($this->aReviewData['gallery']) && !empty($this->aReviewData['gallery']) ){
			$this->aReviewData['gallery'] = json_decode($this->aReviewData['gallery'], true);

			if ( is_array($this->aReviewData['gallery']) ){
				$aUserRoles = User::getField('roles', $this->oToken->userID);
				foreach ($this->aReviewData['gallery'] as $galleryKey => $galleryID){
					if ( !in_array('administrator', $aUserRoles) &&  get_post_field('post_author', $galleryID) != $this->oToken->userID ){
						unset($this->aReviewData['gallery'][$galleryKey]);
					}else{
						$aGallery[] = array(
							'id' => $galleryID,
							'src' => wp_get_attachment_image_url($galleryID, 'full')
						);
					}
				}
			}else{
				$this->aReviewData['gallery'] = array();
			}
		}

		$aReviewDetailKeys  = WebReviewController::getDetailsSettings(get_post_type($this->postID));
		$this->aReviewData['details'] = array();
		if ( !empty($aReviewDetailKeys) ){
			foreach ($aReviewDetailKeys as $aDetail){
				$score = isset($this->aReviewData[$aDetail['key']]) ? absint($this->aReviewData[$aDetail['key']]) : 5;
				if ( empty($score) ){
					continue;
				}

				$this->aReviewData['details'][$aDetail['key']]['value'] = $score;
				unset($this->aReviewData[$aDetail['key']]);
			}
		}

		$aGallery = array_merge($aGallery, $this->oRestData->get_file_params());
		if ( !empty($aGallery) ){
			$this->aReviewData['gallery'] = $aGallery;
		}

		$this->aReviewData['isFakeGallery'] = true;

		$aAddReviewStatus = apply_filters('wilcity/wilcity-mobile-app/submit-review', $this->postID, $this->aReviewData, $this->reviewID, true);

		if ( $aAddReviewStatus['status'] == 'error' ){
			return $aAddReviewStatus;
		}

		$this->reviewID = $aAddReviewStatus['reviewID'];

		$aResponse = $this->returnReviewStatus();
		if ( $aAddReviewStatus['reviewStatus'] == 'publish' ){
			$aResponse = array_merge($aResponse, array('msg'=>wilcityAppStripTags('reviewSubmittedSuccessfully')));
		}else{
			$aResponse = array_merge($aResponse, array('msg'=>wilcityAppStripTags('reviewBeingReviewed')));
		}

		$aResponse['shareURL'] = GetSettings::getShareReviewURL(get_permalink($this->postID), $this->reviewID);
		return $aResponse;
	}

	public function deleteReview($oData){
		$this->oToken = $this->verifyPermanentToken();
		if ( !$this->oToken ){
			return $this->tokenExpiration();
		}

		$this->oToken->getUserID();
		$this->reviewID = $oData->get_param('reviewID');
		$this->postID = $oData->get_param('postID');
		$this->aReviewData = $this->parsePost();

		$aStatus = $this->middleware(['isOwnerOfReview'], array(
			'reviewID'          => $this->reviewID,
			'reviewAuthorID'    => $this->oToken->userID,
			'isApp'             => 'yes'
		));

		if ( $aStatus !== true ){
			return $aStatus;
		}

		wp_delete_post($oData->get_param('reviewID'), true);
		$this->isDeleteReview = true;
		return $this->returnReviewStatus();
	}

	public function updateReview($oData){
		$this->reviewID = abs($oData->get_param('reviewID'));
		$this->postID = abs($oData->get_param('postID'));
		$aValidated = $this->validateReview();

		if ( $aValidated !== true ){
			return $aValidated;
		}

		if ( ReviewModel::isUserReviewed($this->postID) ){
			return $this->error('youLeftAReviewBefore');
		}

		$this->oRestData = $oData;
		return $this->handleSubmitReview();
	}

    // public function postReview(\WP_REST_Request $oData){
    //     $this->postID = abs($oData->get_param('postID'));
    //     $aValidated = $this->validateReview();

    //     if ( $aValidated !== true ){
    //         return $aValidated;
    //     }

    //     if (ReviewModel::isUserReviewed($this->postID)){
    //         return $this->error('youLeftAReviewBefore');
    //     }

    //     $this->oRestData = $oData;
    //     return $this->handleSubmitReview();
    // }

	public function testReview($oData){
		$this->oRestData = $oData;

		$this->reviewID = abs($oData->get_param('reviewID'));
		$this->postID = abs($oData->get_param('postID'));
		$this->aReviewData = $this->parsePost();

		$aReviewDetailKeys  = WebReviewController::getDetailsSettings(get_post_type($this->postID));
		$this->aReviewData['details'] = array();
		if ( !empty($aReviewDetailKeys) ){
			foreach ($aReviewDetailKeys as $aDetail){
				$score = isset($this->aReviewData[$aDetail['key']]) ? absint($this->aReviewData[$aDetail['key']]) : 5;
				if ( empty($score) ){
					continue;
				}

				$this->aReviewData['details'][$aDetail['key']]['value'] = $score;
				unset($this->aReviewData[$aDetail['key']]);
			}
		}

		return $this->aReviewData;
	}

	public function getReview($oData){

	}

	public function getReviewFields($oData){
		$aData = $oData->get_params();
		if ( !isset($aData['postID']) || empty($aData['postID']) ){
			return array(
				'status' => 'error'
			);
		}

		$postType = get_post_type($aData['postID']);

		if ( !WebReviewController::isEnabledReview($postType) ){
			return array(
				'status' => 'error'
			);
		}

		$aFields = WebReviewController::getDetailsSettings($postType);
		if ( empty($aFields) ){
			return array(
				'status' => 'error'
			);
		}
		$aReturn = array();
		foreach ($aFields as $aField){
			if ( !isset($aField['key']) || empty($aField['key']) ){
				continue;
			}

			$aReturn[trim($aField['key'])] = array(
				'type' => 'inputRange',
				'name' => $aField['name']
			);
		}

		$aReturn['title'] = array(
			'placeholder'   => wilcityAppGetLanguageFiles('reviewTitle'),
			'type'          =>  'inputText',
			'required'      => true
		);

		$aReturn['content'] = array(
			'placeholder'   => wilcityAppGetLanguageFiles('yourReview'),
			'type'          =>  'inputText',
			'required'      => true,
			'multiline'     => true
		);

		if ( WebReviewController::isEnableGallery($postType) !== 'disable' ){
			$aReturn['gallery'] = array(
				'type' => 'gallery'
			);
		}

		return array(
			'status' => 'success',
			'oFields' => $aReturn
		);
	}
}
