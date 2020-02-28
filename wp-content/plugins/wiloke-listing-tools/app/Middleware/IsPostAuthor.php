<?php
namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;
use WilokeListingTools\Frontend\User;

class IsPostAuthor implements InterfaceMiddleware {
	public $msg;

	public function handle(array $aOptions) {
		$this->msg = esc_html__('You do not have permission to access this page', 'wiloke-listing-tools');
		if ( isset($aOptions['passedIfAdmin']) && $aOptions['passedIfAdmin'] && User::can('administrator') ){
			return true;
		}

		if ( !isset($aOptions['postID']) ){
			return false;
		}

		$authorID = isset($aOptions['postAuthor']) ? $aOptions['postAuthor'] : User::getCurrentUserID();
		if ( get_post_field('post_author', $aOptions['postID']) != $authorID ){
			return false;
		}

		return true;
	}
}