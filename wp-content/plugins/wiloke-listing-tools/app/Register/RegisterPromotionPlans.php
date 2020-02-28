<?php

namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;

class RegisterPromotionPlans {
	use ListingToolsGeneralConfig;

	public $slug = 'promotion';

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_save_promotion_settings', array($this, 'savePromotionSettings'));
	}

	public function savePromotionSettings(){
		if ( !current_user_can('edit_theme_options') ){
			wp_send_json_error();
		}

		$aPlans = array();

		foreach ($_POST['plans'] as $key => $aValue){
			$aPlans[$key] = $aValue;

			if ( isset($aValue['productAssociation']) && !empty($aValue['productAssociation']) ){
				if ( is_array($aValue['productAssociation']) ){
					$aPlans[$key]['productAssociation'] = isset($aValue['productAssociation']['id']) ? $aValue['productAssociation']['id'] : $aValue['productAssociation'][0]['id'];
				}
			}
		}

		global $wpdb;
		$description = $wpdb->_real_escape($_POST['description']);

		SetSettings::setOptions('toggle_promotion', sanitize_text_field($_POST['toggle']));
		SetSettings::setOptions('promotion_description', $description);
		SetSettings::setOptions('promotion_plans', $aPlans);

		wp_send_json_success();
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) === false ){
			return false;
		}
		$this->requiredScripts();
		$this->generalScripts();

		wp_enqueue_script('wiloke-promotion-script', WILOKE_LISTING_TOOL_URL . 'admin/source/js/promotion-script.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);

		$aPlans = GetSettings::getPromotionPlans();
		$aPositions = wilokeListingToolsRepository()->get('promotion-settings:aPositions');

		$aAvailablePositions = $aPositions;
		$aNewPlans = array();
		if ( !empty($aPlans) ){
			$order = 0;
			foreach ($aPlans as $key => $aPlan){
				$foundKey = array_search($key, $aAvailablePositions);
				$aNewPlans[$order] = $aPlan;
				if ( isset($aPlan['productAssociation']) ){
					$productID = $aPlan['productAssociation'];
					unset($aPlan['productAssociation']);
					$aNewPlans[$order]['productAssociation'] = array(
						'name'  => get_the_title($productID),
						'id'    => $productID
					);
				}else{
					$aNewPlans[$order]['productAssociation'] = '';
				}
				unset($aAvailablePositions[$foundKey]);
				$order++;
			}
		}

		wp_localize_script('wiloke-promotion-script', 'WILOKE_PROMOTIONS',
			array(
				'plans'                 => empty($aNewPlans) ? array() : $aNewPlans,
				'toggle'                => empty(GetSettings::getOptions('toggle_promotion')) ? 'disable' : GetSettings::getOptions('toggle_promotion'),
				'description'           => self::unSlashDeep(GetSettings::getOptions('promotion_description')),
				'positions'             => $aPositions,
				'availablePositions'    => $aAvailablePositions,
				'aSingleLayouts'        => wilokeListingToolsRepository()->get('promotion-settings:aSingleLayouts'),
				'aSingleListingConditionals' => wilokeListingToolsRepository()->get('promotion-settings:aSingleListingConditionals')
			)
		);
	}

	public function showPromotions(){
		Inc::file('promotion-settings:index');
	}

	public function register(){
		add_submenu_page($this->parentSlug, 'Promotions', 'Promotions', 'administrator', $this->slug, array($this, 'showPromotions'));
	}
}