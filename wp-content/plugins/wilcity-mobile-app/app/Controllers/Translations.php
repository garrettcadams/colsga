<?php

namespace WILCITY_APP\Controllers;


class Translations {
	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', 'translations', array(
				'methods'   => 'GET',
				'callback'  => array($this, 'getTranslations')
			));
		});
	}

	public function getTranslations(){
		global $wiloke;

		return array(
			'status' => 'success',
			'oResults' => $wiloke->aConfigs['translation']
		);
	}
}