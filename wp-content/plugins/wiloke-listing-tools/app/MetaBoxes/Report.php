<?php

namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

class Report {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
	}

	public function renderMetaboxFields(){
		$aReports = wilokeListingToolsRepository()->get('report');

		$aAdditionalFields = GetSettings::getOptions('report_fields');

		if ( !empty($aAdditionalFields) ){
			foreach ($aAdditionalFields as $aField){
				switch ($aField['type']){
					case 'text':
						$aReports['report_information']['fields'][] = array(
							'type'      => 'text',
							'id'        => 'wilcity_'.$aField['key'],
							'name'      => $aField['label']
						);
						break;
					case 'textarea':
						$aReports['report_information']['fields'][] = array(
							'type'      => 'textarea',
							'id'        => 'wilcity_'.$aField['key'],
							'name'      => $aField['label']
						);
						break;
					case 'select':
						if ( !empty($aField['options']) ){
							$aRawOptions = explode(',', $aField['options']);
							$aSelectField = array(
								'type'      => 'select',
								'id'        => 'wilcity_'.$aField['key'],
								'name'      => $aField['label']
							);
							foreach ($aRawOptions as $val){
								$aOptions = General::parseCustomSelectOption($val);
								$aSelectField['options'][$aOptions['key']] = trim($aOptions['name']);
							}
							$aReports['report_information']['fields'][] = $aSelectField;
						}

						break;
				}
			}
		}


		new_cmb2_box($aReports['report_information']);
		new_cmb2_box($aReports['report_my_note']);
	}
}