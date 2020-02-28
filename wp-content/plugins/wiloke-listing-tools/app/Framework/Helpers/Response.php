<?php

namespace WilokeListingTools\Framework\Helpers;


class Response {
	private static function response($status, $aInfo){
		if ( wp_doing_ajax() ){
			if ( $status == 'success' ){
				wp_send_json_success($aInfo);
			}else{
				wp_send_json_error($aInfo);
			}
		}else{
			return $aInfo;
		}
	}

	public static function responseError($msg, $aAdditionalInfo=array()){
		$aResponse = array(
			'msg'    =>  $msg,
			'status' => 'error'
		);
		return self::response('error', $aResponse);
	}

	public static function responseSuccess($msg, $aAdditionalInfo=array()){
		$aResponse = array(
			'msg'    =>  $msg,
			'status' => 'success'
		);
		return self::response('error', $aResponse);
	}
}