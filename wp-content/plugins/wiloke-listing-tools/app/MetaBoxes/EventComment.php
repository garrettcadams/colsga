<?php

namespace WilokeListingTools\MetaBoxes;


class EventComment {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
	}

	public function renderMetaboxFields(){
		$aAllSettings = wilokeListingToolsRepository()->get('event-comment');
		$postID = isset($_GET['post']) && !empty($_GET['post']) ? $_GET['post'] : '';

		if ( is_array($postID) ){
			return false;
		}

		new_cmb2_box($aAllSettings['metaBoxes']);
	}

	public static function getParentID(){
		if ( isset($_GET['post']) ){
			return wp_get_post_parent_id($_GET['post']);
		}

		return '';
	}
}