<?php

namespace WILCITY_APP\Controllers;


trait Message {
	public function error($msg){
		return array(
			'status' => 'error',
			'msg' => $msg
		);
	}
}