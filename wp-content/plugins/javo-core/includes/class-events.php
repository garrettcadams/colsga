<?php

class jvbpd_core_events {

	Const STR_EVENT_ACTION = 'jvbpd_create_event';
	Const STR_EVENT_DELETE_ACTION = 'jvbpd_delete_event';
	Const STR_EVENT_TYPE = 'tribe_events';
	Const STR_CAT_SLUG = 'tribe_events_cat';

	public $message = false;
	public static $instance = null;

	public function __construct() {
		if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == self::STR_EVENT_ACTION ) {
			$this->createEvent( $_POST );
		}
		if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == self::STR_EVENT_DELETE_ACTION ) {
			$this->deleteEvent( $_POST );
		}
	}

	public function getEventAction( $type='' ) {
		$output = null;
		switch( $type ) {
			case 'delete' : $output = self::STR_EVENT_DELETE_ACTION; break;
			case 'create' : default :
				$output = self::STR_EVENT_ACTION;
		}
		return $output;
	}

	public function getEventPostType() { return self::STR_EVENT_TYPE; }
	public function getEventCategoryName() { return self::STR_CAT_SLUG; }

	public function getItems( $args=Array() ) {
		$arrQuery = shortcode_atts(
			Array(
				'author' => bp_displayed_user_id(),
				'post_type' => jvbpdCore()->getSlug(),
				'post_status' => Array( 'publish' ),
				'posts_per_page' => -1,
			), $args
		);
		$objQuery = new WP_Query;
		return $objQuery->query( $arrQuery );
	}

	public function getEventCategories( $args=Array() ) {
		$param = wp_parse_args(
			Array(
				'taxonomy' => $this->getEventCategoryName(),
				'hide_empty' => false,
				'fields' => 'id=>name',
			), $args
		);
		return get_terms( $param );
	}

	public function createEvent( $args=Array() ) {

		$intEventID = isset( $args[ 'post_id' ] ) ? $args[ 'post_id' ] : 0;
		$is_update = 0 < intVal( $intEventID );

		$post_type = self::STR_EVENT_TYPE;
		$post_title = isset( $args[ 'txtTitle' ] ) ? $args[ 'txtTitle' ] : false;
		$post_content = isset( $args[ 'txtDescription' ] ) ? $args[ 'txtDescription' ] : false;
		$post_parent = isset( $args[ 'selParent' ] ) ? $args[ 'selParent' ] : false;
		$arrCategory = isset( $args[ 'selCategory' ] ) ? $args[ 'selCategory' ] : false;

		$event_args = compact( Array( 'post_type', 'post_title', 'post_content', 'post_parent' ) );

		if( $is_update ) {
			$event_args[ 'ID' ] = $intEventID;
			$intEventID = wp_update_post( $event_args );
		}else{
			$event_args[ 'post_status' ] = 'publish';
			$intEventID = wp_insert_post( $event_args );
		}

		if( class_exists( 'Tribe__Events__API' ) && ! is_wp_error( $intEventID ) ) {
			Tribe__Events__API::saveEventMeta( $intEventID, $_POST, get_post( $intEventID ) );
		}

		if( isset( $_POST[ 'featured_image_id' ] ) && $_POST[ 'featured_image_id' ] != '' ) {
			set_post_thumbnail( $intEventID, $_POST[ 'featured_image_id' ] );
		}

		update_post_meta( $intEventID, 'detail_images', $_POST[ 'gallery_image_ids' ] );
		wp_set_object_terms( $intEventID, intVal( $arrCategory ), $this->getEventCategoryName() );

		$strRedirectBase = trailingslashit( bp_displayed_user_domain() . 'events' );
		$strRedirect = apply_filters( 'jvfrm_spot_new_event_after_redirect', $strRedirectBase, $intEventID, $is_update );
		wp_safe_redirect( $strRedirect );
		die();
	}

	public function deleteEvent() {
		$intEventID = isset( $_POST[ 'event_id' ] ) ? $_POST[ 'event_id' ] : 0;

		if( 0 < $intEventID ) {
			$intRemoveEventID = wp_delete_post( $intEventID );
			if( $intRemoveEventID ) {
				$this->message = Array(
					'state' => 'success',
					'content' => esc_html__( "Has been successfully delete an event.", 'javospot' ),
				);
			}else{
				$this->message = Array(
					'state' => 'danger',
					'content' => esc_html__( "Failed delete an event.", 'javospot' ),
				);
			}
		}else{
			$this->message = Array(
				'state' => 'danger',
				'content' => esc_html__( "Invailed Event.", 'javospot' ),
			);
		}
	}

	public function hasMessage() { return !empty( $this->message ); }

	public function outputMessage() {

		$strMessageClass = null;
		$strMessageState = isset( $this->message[ 'state' ] ) ? $this->message[ 'state' ] : null;
		$strMessage =  isset( $this->message[ 'content' ] ) ? $this->message[ 'content' ] : esc_html__( "Empty Message", 'javospot' );

		switch( $strMessageState ) {
			case 'success' : $strMessageClass = 'alert-success'; break;
			case 'danger' : $strMessageClass = 'alert-danger'; break;
		}
		return sprintf(
			'<div class="alert text-center fade show %1$s"><a class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></a> %2$s</div>',
			$strMessageClass, $strMessage
		);
	}

	public function getEventDate( $event_id=0, $key='', $default=false ) {
		$strDate = get_post_meta( $event_id, $key, true );
		if( !empty( $strDate ) ) {
			$default = date( 'Y-m-d', strtotime( $strDate ) );
		}
		return $default;
	}

	public static function getInstance() {
		if(is_null(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
if(!function_exists('jvbpd_events')) {
	function jvbpd_events() {
		return jvbpd_core_events::getInstance();
	}
	add_action('init', 'jvbpd_events');
}