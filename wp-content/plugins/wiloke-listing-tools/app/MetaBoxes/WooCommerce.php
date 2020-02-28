<?php

namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\GetSettings;

class WooCommerce {
	public function __construct() {
		add_action( 'cmb2_admin_init', array($this, 'registerMetaBoxes') );
	}

	public static function getIsDokan(){
		$postID = isset($_GET['post']) && !empty($_GET['post']) ?  $_GET['post'] : '';
		if ( empty($postID) ){
			return '';
		}
		return GetSettings::getPostMeta($postID, 'is_dokan');
	}

	public static function getIsSendQRCode(){
		$postID = isset($_GET['post']) && !empty($_GET['post']) ?  $_GET['post'] : '';
		if ( empty($postID) ){
			return '';
		}
		return GetSettings::getPostMeta($postID, 'is_send_qrcode');
	}

	public static function getQRCodeEmailContent(){
		$postID = isset($_GET['post']) && !empty($_GET['post']) ?  $_GET['post'] : '';
		if ( empty($postID) ){
			return '';
		}
		return GetSettings::getPostMeta($postID, 'qrcode_description');
	}

	public function registerMetaBoxes(){
		new_cmb2_box(wilokeListingToolsRepository()->get('woocommerce-metaboxes:metaBoxes'));
		new_cmb2_box(wilokeListingToolsRepository()->get('woocommerce-metaboxes:excludeFromShop'));
	}
}