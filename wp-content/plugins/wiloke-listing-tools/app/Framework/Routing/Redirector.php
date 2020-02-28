<?php
namespace WilokeListingTools\Framework\Routing;


class Redirector{
	public static function to($url=null, $status=302){
		if ( empty($url) ){
			$url = home_url('/');
		}else if ( is_numeric($url) ){
			$url = get_permalink($url);
		}

		header('Location: ' .$url, false, $status);
		exit();
	}
}