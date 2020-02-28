<?php

namespace WILCITY_APP\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Frontend\User;

class IsOwnerOfReview implements InterfaceMiddleware {
	public $msg;

	public function handle( array $aOptions ) {
		if ( !isset($aOptions['reviewAuthorID']) || empty($aOptions['reviewAuthorID']) || !isset($aOptions['reviewID']) || empty($aOptions['reviewID']) ){
			$this->msg = wilcityAppGetLanguageFiles(403);
			return false;
		}

		$aRoles = User::getField('roles', $aOptions['reviewAuthorID']);
		if ( !in_array('administrator', $aRoles) &&  $aOptions['reviewAuthorID'] != get_post_field('post_author', $aOptions['reviewID']) ){
			$this->msg = wilcityAppGetLanguageFiles(403);
			return false;
		}

		return true;
	}
}