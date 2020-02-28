<?php

namespace WilokeListingTools\MetaBoxes;


use WilcityPaidClaim\Register\RegisterClaimSubMenu;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;

class ClaimListing {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
		add_action('save_post_claim_listing', array($this, 'saveClaimListing'), 10, 2);
	}

	public function getClaimedListingID($postID){
		return GetSettings::getPostMeta($postID, 'claimed_listing_id');
	}

	public function saveClaimListing($postID, $post){
		if ( !current_user_can('edit_theme_options') ){
			return false;
		}

		if ( isset($_POST['wilcity_claimed_listing_id']) && !empty($_POST['wilcity_claimed_listing_id']) ){
			SetSettings::setPostMeta($postID, 'claimed_listing_id', $_POST['wilcity_claimed_listing_id']);
		}
	}

	public function renderMetaboxFields($post){
		$aClaimFields = GetSettings::getOptions('claim_settings');
		$aFields = array();

		if ( empty($aClaimFields) ){
			return false;
		}

		foreach ($aClaimFields as $aClaimField){
			switch ($aClaimField['type']){
				case 'text':
					$aFields[] = array(
						'type'      => 'wiloke_field',
						'fieldType' => 'input',
						'save_fields'   => false,
						'id'        => 'wilcity_claimer_info:'.$aClaimField['key'],
						'name'      => $aClaimField['label']
					);
					break;
				case 'checkbox':
					$parseOptions = explode(',', $aClaimField['options']);

					$aOptions = array();
					foreach ($parseOptions as $option){
						$option = trim($option);
						$aOptions[$option] = $option;
					}

					$aFields[] = array(
						'type'      => 'wiloke_field',
						'fieldType' => 'multicheck',
						'save_fields'   => false,
						'id'        => 'wilcity_claimer_info:'.$aClaimField['key'],
						'name'      => $aClaimField['label'],
						'options'   => $aOptions
					);
					break;
				case 'claimPackage':
					$oPosts = get_posts(
						array(
							'post_type'     => 'listing_plan',
							'posts_per_page'=> -1,
							'post_status'   => 'publish'
						)
					);

					$msg = '';
					if ( !class_exists('WilcityPaidClaim\Register\RegisterClaimSubMenu') ){
						$msg = 'Wilcity Paid Claim plugin is disabled, You have to activate this plugin to use Paid Claim feature';
					}else{
						$aSettings = GetSettings::getOptions(RegisterClaimSubMenu::$optionKey);
						if ( !isset($aSettings['toggle']) || ($aSettings['toggle'] == 'disable') ){
							$msg = 'Paid Claim feature is disabled, please go to Wiloke Submission -> Claim Settings to enable this feature';
						}
					}

					$aOptions = array();
					if ( empty($oPosts) || is_wp_error($oPosts) ){
						$aOptions[] = 'You do not have any plan yet. Please click on Listing Plans and create one';
					}else{
						$aOptions[-1] = '----';
						foreach ($oPosts as $oPost){
							$aOptions[$oPost->ID] = $oPost->post_title;
						}
					}

					$aFields[] = array(
						'type'      => 'wiloke_field',
						'fieldType' => 'select',
						'id'        => 'wilcity_claimer_info:'.$aClaimField['key'],
						'name'      => $aClaimField['label'],
						'options'   => $aOptions,
						'save_fields'   => false,
						'desc'      => $msg
					);
					break;
				default:
					$aFields[] = array(
						'type'      => 'wiloke_field',
						'fieldType' => $aClaimField['type'],
						'save_fields'   => false,
						'id'        => 'wilcity_claimer_info:'.$aClaimField['key'],
						'name'      => $aClaimField['label']
					);
					break;
			}
		}

		new_cmb2_box(
			array(
				'id'            => 'wilcity_claimer_info',
				'title'         => esc_html__('Claimer Info', 'wiloke-listing-tools'),
				'object_types'  => array('claim_listing'),
				'context'       => 'normal',
				'priority'      => 'low',
				'save_fields'   => false,
				'fields'        => $aFields
			)
		);
		new_cmb2_box(wilokeListingToolsRepository()->get('claim-settings:claimed_listing_id'));
		new_cmb2_box(wilokeListingToolsRepository()->get('claim-settings:claimer_id'));
		new_cmb2_box(wilokeListingToolsRepository()->get('claim-settings:claim_status'));
		new_cmb2_box(wilokeListingToolsRepository()->get('claim-settings:claim_plan_id'));
		new_cmb2_box(wilokeListingToolsRepository()->get('claim-settings:attribute_post_author'));
	}
}