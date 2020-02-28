<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Controllers\EventController;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Controllers\ShareController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;

class Event {
	use BuildQuery;
	use JsonSkeleton;
	private $aUnnecessaryItems = array('businessStatus', 'oTerm', 'oIcon');

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/events/(?P<target>\w+)', array(
				'methods' => 'GET',
				'callback' => array($this, 'getEvent')
			));
		});

		add_filter('wilcity/mobile/render_event_on_mobile', array($this, 'buildListingEventData'), 5, 2);
		add_filter('wilcity/app/single-skeletons/event', array($this, 'addAdditionalEventDataToSkeleton'), 10, 2);
		add_filter('wilcity/app/single-skeletons/event', array($this, 'buildEventSingleContent'), 15, 2);
//		add_filter('wilcity/app/single-skeletons/event', array($this, 'getEventCommented'), 20, 2);
	}

	public function getEventCommented($aResponse, $post){
		$query = new \WP_Query(
			array(
				'post_type'     => 'event_comment',
				'post_status'   => 'publish',
				'parent__in'    => array($post->ID),
				'posts_per_page'=> 6
			)
		);

		if ( !$query->have_posts() ){
			$aResponse['oDiscussions'] = false;
		}

		$aComments = array();

		while ($query->have_posts()){
			$query->the_post();
			$aComments[] = $this->eventCommentItem($query->post);
		}
		wp_reset_postdata();

		$aResponse['oDiscussions'] = array(
			'oContent' => $aComments,
			'next'     => $query->post->max_number_pages > 1 ? 2 : false
		);
		return $aResponse;
	}

	public function buildEventSingleContent($aResponse, $post){
		$aSettings = GetSettings::getOptions('event_content_fields');
		if ( empty($aSettings) ){
			return $aResponse;
		}
		$aSections = array();
		foreach ($aSettings as $aSetting){
			$content = $this->getListingData($aSetting['key'], $post);
			if ( !empty($content) ){
				$aSections[] = array(
					'text'      => $aSetting['name'],
					'type'      => $aSetting['key'],
					'content'   => is_array($this->getListingData($aSetting['key'], $post)) ? $this->getListingData($aSetting['key'], $post) : strip_tags($this->getListingData($aSetting['key'], $post))
				);
			}
		}
		$aResponse['aSections'] = $aSections;
		return $aResponse;
	}

	public function addAdditionalEventDataToSkeleton($aResponse, $post){
		$aCalendar = EventController::renderEventCalendar($post, true);
		$aEventMetaData = EventController::getEventMetaData($post);

		$aResponse['oCalendar'] = $aCalendar;
		$aResponse['aMetaData'] = $aEventMetaData;
		$aResponse['hostedBy']  = GetSettings::getEventHostedByName($post);

		foreach ($this->aUnnecessaryItems as $item){
			unset($aResponse[$item]);
		}

		return $aResponse;
	}

	public function buildListingEventData($aAtts, $post){
		return $this->listingSkeleton($post);
	}

	public function getEvent($aData){
		$aArgs = $this->buildSingleQuery($aData);

		$query = new \WP_Query($aArgs);

		if ( $query->have_posts() ){
			global $post;
			$aPost = array();
			while ($query->have_posts()){
				$query->the_post();
				$aPost = $this->listingSkeleton($post);
				$postID = $query->post->ID;
			}

			return apply_filters('wilcity/wilcity-mobile-app/filter/listing-detail', array(
				'status'   => 'success',
				'oResults' => $aPost
			), $aData, $postID);
		}else{
			return array(
				'status' => 'error',
				'msg'    => esc_html__('No Post Found',  'wilcity-mobile-app')
			);
		}
	}
}
