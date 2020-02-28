<?php

/**
 * WilokeMetaboxes
 * Custom Metabox
 *
 * We want to say thank you so much to WebDevStudios
 *
 * This category extended from their plugin
 *
 * @category Meta box
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
	die();
}

class WilokeMetaboxes
{
	public function __construct()
	{
		add_action( 'cmb2_admin_init', array($this, 'render') );
	}

	/**
	 * Register and render meta boxes
	 */
	public function render()
	{
		global $wiloke;
		if ( isset($wiloke->aConfigs['metaboxes']) && !empty($wiloke->aConfigs['metaboxes']) )
		{
			foreach ($wiloke->aConfigs['metaboxes'] as $aMetabox){
				if ( isset($aMetabox['type']) && ($aMetabox['type'] == 'group') ){
					$aBoxConfiguration = $aMetabox;
					unset($aBoxConfiguration['type']);
					unset($aBoxConfiguration['group_fields']);
					unset($aBoxConfiguration['group_settings']);
					$instMetaBoxGroup = new_cmb2_box($aBoxConfiguration);
					$groupFieldID = $instMetaBoxGroup->add_field($aMetabox['group_settings']);

					foreach ($aMetabox['group_fields'] as $aGroupField){
						$instMetaBoxGroup->add_group_field($groupFieldID, $aGroupField);
					}
				}else{
					new_cmb2_box($aMetabox);
				}
			}
		}
	}
}
