<?php

namespace WilokeListingTools\MetaBoxes;


class UserMeta {
	public function __construct() {
		add_action( 'cmb2_admin_init', array($this, 'registerMetaBoxes') );
	}

	public function registerMetaBoxes(){
		new_cmb2_box(wilokeListingToolsRepository()->get('usermeta'));
	}
}