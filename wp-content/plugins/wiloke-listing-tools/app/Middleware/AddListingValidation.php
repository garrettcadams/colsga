<?php
namespace WilokeListingTools\Middleware;

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class AddListingValidation implements InterfaceMiddleware{
	public $msg;

	/**
	 * Make sure that all required fields are filled up
	 * $aOptions: Required $planID and $aSubmitData
	 */
	public function handle(array $aOptions = []){

//		if ( !isset($aOptions['planID']) || empty($aOptions['planID']) ){
//			$this->msg = esc_html__('You have to select a plan first', 'wiloke-listing-tools');
//			return false;
//		}

		if ( !isset($aOptions['aSubmitData']) || empty($aOptions['aSubmitData']) ){
			$this->msg = esc_html__('Please fill up all requirement fields.', 'wiloke-listing-tools');
			return false;
		}

		$msg = esc_html__('The %s is required', 'wiloke-listing-tools');
		foreach ($aOptions['aSubmitData'] as $index => $aSection){
			foreach ($aSection['fields'] as $fieldKey => $aFieldData){
				if ( isset($aFieldData['isRequired']) && ($aFieldData['isRequired'] == 'yes') ){
					if ( empty($aFieldData['value']) ){
						$msg = sprintf($msg, $fieldKey);
						if ( isset($aOptions['isReturnDepthMsg']) && $aOptions['isReturnDepthMsg'] ){
							$this->msg['index']     = $index;
							$this->msg['fieldKey']  = $fieldKey;
							$this->msg['msg']       = $msg;
						}else{
							$this->msg = $msg;
						}
						return false;
					}
				}
			}
		}
	}
}