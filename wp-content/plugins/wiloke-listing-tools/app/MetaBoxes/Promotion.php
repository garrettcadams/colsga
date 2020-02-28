<?php

namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\GetSettings;

class Promotion {
	protected $aPromotionPlans;

	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
	}

	public function renderMetaboxFields(){
		$aMetaBoxes = wilokeListingToolsRepository()->get('promotion-metaboxes');
		$aPlanSettings = GetSettings::getPromotionPlans();

		$aAdditionalFields = array();

		if ( empty($aPlanSettings) ){
			return false;
		}

		foreach ($aPlanSettings as $key => $aPlan){
			$aAdditionalFields[] = array(
				'type' => 'text_datetime_timestamp',
				'id'   => 'wilcity_promote_'.$key,
				'name' => 'Position ' . $aPlan['name'] . ' Until'
			);
		}

		$aMetaBoxes['promotion_information']['fields'] = array_merge($aMetaBoxes['promotion_information']['fields'], $aAdditionalFields);

		foreach ($aMetaBoxes as $aMetaBox){
			new_cmb2_box($aMetaBox);
		}
	}
}