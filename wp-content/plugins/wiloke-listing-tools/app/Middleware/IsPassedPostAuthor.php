<?php
namespace WilokeListingTools\Middleware;


use WilokeListingTools\Framework\Routing\InterfaceMiddleware;

class IsPassedPostAuthor implements InterfaceMiddleware{
	public $msg;

	public function handle(array $aOptions = []){
		if ( !isset($aOptions['postID']) || empty($aOptions['postID']) ){
			$this->msg = esc_html__('The id is required', 'wiloke-listing-tools');
			return false;
		}

		if ( !current_user_can('edit_posts') || ( get_post_field('post_author', $aOptions['postID']) != get_current_user_id() ) ){
			$this->msg = wilokeListingToolsRepository()->get('translation:403');
			return false;
		}

		return true;
	}
}