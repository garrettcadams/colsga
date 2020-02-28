<?php

namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;

class RegisterReportSubmenu{
	use ListingToolsGeneralConfig;

	public $slug = 'report';

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_save_report_settings', array($this, 'saveReportSettings'));
	}

	public function saveReportSettings(){
		if ( !current_user_can('edit_theme_options') ){
			wp_send_json_error();
		}

		SetSettings::setOptions('toggle_report', $_POST['toggle']);
		SetSettings::setOptions('report_description', $_POST['description']);
		SetSettings::setOptions('report_fields', $_POST['fields']);
		SetSettings::setOptions('report_thankyou', $_POST['thankyou']);

		wp_send_json_success();
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) === false ){
			return false;
		}
		$this->requiredScripts();
		$this->generalScripts();

		wp_enqueue_script('wiloke-report-script', WILOKE_LISTING_TOOL_URL . 'admin/source/js/report-script.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);

		$aFields = GetSettings::getOptions('report_fields');

		wp_localize_script('wiloke-report-script', 'WILOKE_REPORT',
			array(
				'fields'    => empty($aFields) || !is_array($aFields) ? array() : $aFields,
				'toggle'    => empty(GetSettings::getOptions('toggle_report')) ? 'disable' : GetSettings::getOptions('toggle_report'),
				'description'    => empty(GetSettings::getOptions('report_description')) ? 'If you think this content inappropriate and should be removed from our website, please let us know by submitting your reason at the form below.' : GetSettings::getOptions('report_description'),
				'thankyou' => empty(GetSettings::getOptions('report_thankyou')) ? 'Thank for reporting the issue. We value your feedback. We will try to deal with your issue as quickly as possible' : GetSettings::getOptions('report_thankyou')
			)
		);
	}

	public function showReports(){
		Inc::file('report-settings:index');
	}

	public function register(){
		add_submenu_page($this->parentSlug, 'Reports', 'Reports', 'administrator', $this->slug, array($this, 'showReports'));
	}
}