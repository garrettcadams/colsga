<?php

namespace WilokeListingTools\MetaBoxes;


class ListingTag {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
	}

	public function renderMetaboxFields(){
		foreach (wilokeListingToolsRepository()->get('listingtag') as $aSettings){
			new_cmb2_box($aSettings);
		}
	}
}