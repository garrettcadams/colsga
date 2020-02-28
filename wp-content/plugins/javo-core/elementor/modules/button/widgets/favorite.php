<?php
namespace jvbpdelement\Modules\Button\Widgets;

use Elementor\Controls_Manager;
class Favorite extends Base {
	public function get_name() { return parent::FAVORITE_BUTTON; }
	public function get_title() { return 'Favorites Button'; }
	public function get_icon() { return 'eicon-sidebar'; }

	protected function _register_controls() {
		parent::_register_controls();
	}

	public function getFavoritesLink() {
		$user_domain = Array();
		if( function_exists( 'buddypress' ) ) {

			$user_domain[] = untrailingslashit( buddypress()->loggedin_user->domain );
			$user_domain[] = 'favorites';
		}
		return join( '/', $user_domain );
	}

	public function linkAttributes( $args=Array() ) {

		if( is_user_logged_in() ) {
			$args[ 'href' ] = $this->getFavoritesLink();
		}else{
			$args[ 'data-toggle' ] = 'modal';
			$args[ 'data-target' ] = '#login_panel';
		}

		$output = '';
		foreach( $args as $key => $value ) {
			$output .= sprintf( '%1$s="%2$s"', $key, esc_attr( $value ) );
		}
		echo $output;
	}

	protected function render() {
		$this->button_render();
	}

}