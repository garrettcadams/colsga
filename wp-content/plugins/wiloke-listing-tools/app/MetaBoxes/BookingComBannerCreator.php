<?php

namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\General;

class BookingComBannerCreator {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
	}

	public static function getParentID(){
		if ( isset($_GET['post']) ){
			return wp_get_post_parent_id($_GET['post']);
		}

		return '';
	}

	public function renderMetaboxFields() {
		if ( !General::isPostType( 'bdotcom_bm' ) || ( isset( $_GET['post'] ) && is_array( $_GET['post'] ) ) ) {
			return false;
		}

		$aSettings = wilokeListingToolsRepository()->get( 'bookingcom:aBannerCreator');
		new_cmb2_box( $aSettings );
	}
}