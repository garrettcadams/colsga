<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Payment\FreePlan\FreePlan;
use WilokeListingTools\Framework\Routing\Controller;

class FreePlanController extends Controller {
	public function __construct() {
		add_action('wilcity/wiloke-listing-tools/downgrade-to-free-plan', array($this, 'downgrade'), 10, 2);
	}

	public function downgrade(){
		$aPlanInfo = GetSettings::getPlanSettings($_POST['newPlanID']);
		if ( !empty($aPlanInfo['regular_price']) ){
			return false;
		}

		$this->middleware(['isExceededFreePlan'], array(
			'planID' => $_POST['newPlanID']
		));

		$oFree = new FreePlan($_POST['newPlanID']);
		$oFree->proceedPayment();

		wp_send_json_success(array(
			'status'    => 'success',
			'msg'       => esc_html__('Congratulations! Your plan has been updated successfully.', 'wiloke-listing-tools')
		));
	}
}