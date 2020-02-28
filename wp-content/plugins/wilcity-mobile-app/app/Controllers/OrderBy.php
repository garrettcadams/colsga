<?php

namespace WILCITY_APP\Controllers;


class OrderBy {
	use JsonSkeleton;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2/', 'get-orderby',  array(
				'methods'   => 'GET',
				'callback'  => array($this, 'response')
			));
		});
	}

	public function response(){
		return array(
			'status'    => 'success',
			'oResults'  => $this->getOrderBy()
		);
	}
}