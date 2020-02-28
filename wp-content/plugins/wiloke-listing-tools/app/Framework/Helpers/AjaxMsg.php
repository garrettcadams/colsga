<?php

namespace WilokeListingTools\Framework\Helpers;


class AjaxMsg {
	public static function error($msg){
		wp_send_json_error(
			array(
				'msg' => $msg
			)
		);
	}

	public static function success($msg){
		wp_send_json_success(
			array(
				'msg' => $msg
			)
		);
	}
}