<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Routing\Controller;

class ReportController extends Controller {
	public function __construct() {
		add_action('wilcity/footer/vue-popup-wrapper', array($this, 'printReportPopup'));
//		add_action('wp_ajax_wilcity_fetch_report_fields', array($this, 'fetchReportFields'));
//		add_action('wp_ajax_nopriv_wilcity_fetch_report_fields', array($this, 'fetchReportFields'));
		add_action('wp_ajax_wilcity_submit_report', array($this, 'submitReport'));
		add_action('wp_ajax_nopriv_wilcity_submit_report', array($this, 'submitReport'));

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/listings/fields/reports', array(
				'methods' => 'GET',
				'callback' => array($this, 'getReportFields')
			));
		});
	}

	public static function addReport($aData){
		$title = isset($aData['data']['post_title']) && !empty($aData['data']['post_title']) ? $aData['data']['post_title'] . ' - ' . get_the_title($aData['postID'])  : sprintf(esc_html__('Report an issue of %s', 'wiloke-listing-tools'), get_the_title($aData['postID']));

		$reportID = wp_insert_post(
			array(
				'post_type'  => 'report',
				'post_status'=> 'draft',
				'post_title' => $title
			)
		);
		SetSettings::setPostMeta($reportID, 'listing_name', $aData['postID']);
		foreach ($aData['data'] as $key => $val){
		    if ( $key == 'post_title' ){
		        continue;
            }
			SetSettings::setPostMeta($reportID, sanitize_text_field($key), sanitize_text_field($val));
		}

		do_action('wilcity/submitted-report', $aData['postID'], $reportID);
        return true;
    }

	public function submitReport($isApp=false){
	    $this->middleware(['isPublishedPost'], array(
            'postID' => $_POST['postID'],
            'isApp'  => $isApp ? 'yes' : 'no'
        ));

        if ( empty($_POST['data']) ){
            $aMsg = array(
	            'msg' => esc_html__('Please give us your reason', 'wiloke-listing-tools')
            );
            if ( !$isApp ){
	            wp_send_json_error($aMsg);
            }else{
	            $aMsg['status'] = 'error';
	            return $aMsg;
            }
        }

        self::addReport($_POST);

        $aResponse = array(
	        'msg' => GetSettings::getOptions('report_thankyou')
        );

        if ( !$isApp ){
            wp_send_json_success($aResponse);
        }else{
            $aResponse['status'] = 'success';
            return $aResponse;
        }
    }

	public static function isAllowReport(){
		$toggle = GetSettings::getOptions('toggle_report');
		if ( empty($toggle) || $toggle == 'disable' ){
			return false;
		}
		return true;
	}

	public function getReportFields(){
		if ( !self::isAllowReport() ){
			return array(
				'error' => array(
					'userMessage' => esc_html__('Oops! You do not have permission to access this area.', 'wiloke-listing-tools'),
					'code' => 404
				)
			);
		}

		$aRawFields = GetSettings::getOptions('report_fields');

		if ( empty($aRawFields) ){
			wp_send_json_error(array(
				'msg' => esc_html__('There are no report fields. Please go to Wiloke Listing Tools -> Reports to create one.', 'wiloke-listing-tools')
			));
		}

		$aFields = array();
		foreach ($aRawFields as $key => $aField){
			switch ($aField['type']){
				case 'text':
				case 'textarea':
					$aFields[$key]['key'] = $aField['key'];
					$aFields[$key]['label'] = $aField['label'];
					$aFields[$key]['type'] = $aField['type'];
					break;
				case 'select':
					$aFields[$key]['type'] = $aField['type'];
					if ( empty($aField['options']) ){
						break;
					}
					$parseOptions = explode(',', $aField['options']);
					$aFields[$key]['key'] = $aField['key'];
					$aFields[$key]['label'] = $aField['label'];

					$aFields[$key]['options'][] = '---';
					foreach ($parseOptions as $option){
					    $aParsedOption = General::parseCustomSelectOption($option);
                        $aFields[$key]['options'][$aParsedOption['key']] = trim($aParsedOption['name']);
					}
					break;
			}
		}

		return array(
            'data' => array(
	            'fields' => $aFields,
	            'description' => GetSettings::getOptions('report_description')
            )
        );
	}

	public function printReportPopup(){
		if ( !self::isAllowReport() ){
			return false;
		}
		?>
		<report-popup></report-popup>
		<?php
	}
}
