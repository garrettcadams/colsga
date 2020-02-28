<?php
namespace WILCITY_APP\Controllers;

use WilokeListingTools\Controllers\ReportController as ThemeReportController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

class ReportController {
	use VerifyToken;
	use JsonSkeleton;
	use ParsePost;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', 'get-report-fields', array(
				'methods'   => 'GET',
				'callback'  => array($this, 'getReportField')
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', 'post-report', array(
				'methods'   => 'POST',
				'callback'  => array($this, 'postReport')
			));
		});
	}

	public function getReportField(){
		$toggleReport = GetSettings::getOptions('toggle_report');

		if ( $toggleReport != 'enable' ){
			wp_send_json_error(
				array(
					'msg' => 'Report Disabled'
				)
			);
		}
		$aFields = GetSettings::getOptions('report_fields');
		if ( empty($aFields) ){
			wp_send_json_error(
				array(
					'msg' => 'Report Disabled'
				)
			);
		}

		$description = GetSettings::getOptions('report_description');
		foreach ($aFields as $key => $aField){
			if ( $aField['type'] == 'select' ){
				$aRawOptions = explode(',', $aField['options']);
				$aOptions = array_map(function($val){
					$aParsedOptions = General::parseCustomSelectOption($val);
					return array(
						'id'    => $aParsedOptions['key'],
						'name'  => $aParsedOptions['name'],
						'selected' => false
					);
				}, $aRawOptions);
				$aFields[$key]['options'] = $aOptions;
			}else{
				unset($aFields[$key]['options']);
			}
		}

		$aResults = array(
			'aFields'     => $aFields
		);

		if ( !empty($description) ){
			$aResults['description'] = $description;
		}

		return array(
			'status'    => 'success',
			'oResults'  => $aResults
		);
	}

	public function postReport(){
		$aData = $this->parsePost();
		if ( !isset($aData['postID']) || empty($aData['postID']) || get_post_status($aData['postID']) !== 'publish' ){
			return array(
				'status' => 'error',
				'msg'    => 403
			);
		}

		if ( !isset($aData['data']) || empty($aData['data']) ){
			return array(
				'status' => 'error',
				'msg'    => 'weNeedYourReportMsg'
			);
		}

		ThemeReportController::addReport($aData);
		return array(
			'status' => 'success',
			'msg'    => GetSettings::getOptions('report_thankyou')
		);
	}
}