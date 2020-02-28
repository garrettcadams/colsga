<?php

namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

class CustomFieldsForPostType {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'registerCustomBoxes'));
		add_action('cmb2_admin_init', array($this, 'registerGroups'));
	}

	private function getUsedSections(){
		$postType = General::detectPostType();
		if ( empty($postType) ){
			return false;
		}

		$aUsedSections = GetSettings::getOptions(General::getUsedSectionKey($postType));
		if ( empty($aUsedSections) ){
			return false;
		}
		return $aUsedSections;
	}

	protected function parseOption($option){
		$option = trim($option);
		if ( strpos($option, ':') !== false ){
			$aOption = General::parseCustomSelectOption($option);
			return array(
				'value' => $aOption['key'],
				'name'  => $aOption['name']
			);
		}

		$aParseOption = explode('|', $option);

		return array(
			'name' => $aParseOption[0],
			'value'=> $option
		);
	}

	public function registerGroups(){
		$postType = General::detectPostType();
		$aUsedSections = $this->getUsedSections();
		if ( !$aUsedSections || !is_array($aUsedSections) ){
			return false;
		}

		$aGroups = array_filter($aUsedSections, function($aSection){
			if ( isset($aSection['type']) && $aSection['type'] == 'group' ){
				return true;
			}
			return false;
		});

		if ( empty($aGroups) ){
			return false;
		}

		$aCommon = array(
			'id'          => 'wilcity_custom_settings',
			'title'       => 'My Group',
			'object_types'=> array($postType), // Post type
			'context'     => 'normal',
			'save_fields' => false,
			'priority'    => 'low',
			'show_names'  => true, // Show field names on the left
		);
		foreach ($aGroups as $aGroup){
			if ( !isset($aGroup['fields']) || empty($aGroup['fields']) ){
				continue;
			}

			$aCommon['id']      = 'wilcity_group_'.$aGroup['key'];
			$aCommon['title']   = $aGroup['heading'];

			$instCMB2 = new_cmb2_box($aCommon);
			foreach ($aGroup['fields'] as $aFieldSettings){
				$aBuildField = array();
				foreach ($aFieldSettings as $aSetting){
					switch ($aSetting['type']){
						case 'label':
							$aBuildField['name'] = trim($aSetting['value']);
							break;
						case 'name':
							$aBuildField['id'] = $aGroup['key'] . ':' . trim($aSetting['value']);
							break;
						case 'customField':
							$aBuildField['type'] = 'wiloke_field';
							$type = $aSetting['value']['type'];

							$type = str_replace('2', '', $type);
							if ( $type == 'select-multiple' ){
								$aBuildField['fieldType'] = 'multicheck_inline';
							}else if ($type == 'upload-img'){
								$aBuildField['fieldType'] = 'file';
							}else{
								$aBuildField['fieldType'] = $type;
							}

							if ( $type == 'select-multiple' || $type == 'select' ){
								$options = trim($aSetting['value']['options']);
								if ( empty($options) ){
									$options = 'Option A, Option B';
								}
								$aRawOptions = explode(',', $options);
								$aRawOptions = array_map('trim', $aRawOptions);

								$aOptions = array(
									'' => '----'
								);
								foreach ($aRawOptions as $order => $rawOption){
									$aParseOptions = $this->parseOption($rawOption);
									$aOptions[$aParseOptions['value']] = $aParseOptions['name'];
								}
								$aBuildField['options'] = $aOptions;
							}
							break;
					}
				}
				$instCMB2->add_field($aBuildField);
			}

		}
	}

	public function registerCustomBoxes(){
		$postType = General::detectPostType();
		$aUsedSections = $this->getUsedSections();
		if ( empty($aUsedSections) || !is_array($aUsedSections) ){
			return false;
		}
		$aCustomSections = array_filter($aUsedSections, function($aSection){
			if ( isset($aSection['isCustomSection']) && $aSection['isCustomSection'] == 'yes' ){
				return true;
			}
			return false;
		});

		if ( !empty($aCustomSections) ){
			$prefix = wilokeListingToolsRepository()->get('addlisting:customMetaBoxPrefix');

			$instCMB2 = new_cmb2_box(array(
				'id'          => 'wilcity_custom_settings',
				'title'       => esc_html__( 'Custom Settings', 'wiloke-listing-tools' ),
				'object_types'=> array($postType), // Post type
				'context'     => 'normal',
				'priority'    => 'low',
				'show_names'  => true, // Show field names on the left
			));

			foreach ($aCustomSections as $aCustomSection){
				switch ($aCustomSection['type']){
					case 'select':
					case 'checkbox':
					case 'checkbox2':
						$aRawOptions = explode(',', $aCustomSection['fields']['settings']['options']);
						$aOptions = array();
						foreach ($aRawOptions as $val){
							$val = trim($val);
							$aParseOptions = $this->parseOption($val);

							$aParseName = explode('|', $aParseOptions['name']);
							$aOptions[$aParseOptions['value']] = $aParseName[0];
						}

						if ( $aCustomSection['type'] == 'select' && $aCustomSection['fields']['settings']['isMultiple'] == 'no' ){
							$instCMB2->add_field(array(
								'type'         => 'select',
								'id'           => $prefix.$aCustomSection['key'],
								'name'         => $aCustomSection['heading'],
								'show_option_none' => true,
								'options'      => $aOptions
							));
						}else{
							$instCMB2->add_field(array(
								'id'           => $prefix.$aCustomSection['key'],
								'name'         => $aCustomSection['heading'],
								'placeholder'  => '',
								'type'         => 'multicheck_inline',
								'options'      => $aOptions
							));
						}
						break;
					case 'date_time':
						$instCMB2->add_field(array(
							'id'           => $prefix.$aCustomSection['key'],
							'name'         => $aCustomSection['heading'],
							'placeholder'  => '',
							'type'         => 'wilcity_date_time'
						));
						break;
					case 'image':
						$instCMB2->add_field(array(
							'id'           => $prefix.$aCustomSection['key'],
							'name'         => $aCustomSection['heading'],
							'placeholder'  => '',
							'type'         => 'file'
						));
						break;
					case 'listing_type_relationships':
						if ( empty($aCustomSection['fields']['listing_type_relationships']['ajaxArgs.post_types']) ){
							break;
						}
						$instCMB2->add_field(array(
							'type'      => 'select2_posts',
							'description'=> $aCustomSection['desc'],
							'post_types'=> array('product'),
							'attributes' => array(
								'ajax_action' => $aCustomSection['fields']['listing_type_relationships']['ajaxAction'],
								'post_types'  => $aCustomSection['fields']['listing_type_relationships']['ajaxArgs.post_types']
							),
							'id'        => $prefix.$aCustomSection['key'],
							'multiple'  => $aCustomSection['fields']['listing_type_relationships']['isMultiple'] == 'yes',
							'name'      => $aCustomSection['heading']
						));
						break;
					default:
						$instCMB2->add_field(array(
							'id'           => $prefix.$aCustomSection['key'],
							'name'         => $aCustomSection['heading'],
							'type'         => $aCustomSection['type']
						));
						break;
				}
			}
		}
	}
}