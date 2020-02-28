<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;

class Archive_Meta extends Base {

	private $post = null;

	public function get_name() { return parent::ARCHIVE_META; }
	public function get_title() { return 'Archive Module Meta'; }
	public function get_icon() { return 'eicon-align-left'; }
	public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }

	protected function _register_controls() {
		parent::_register_controls();
		$this->add_label_settings_control();
	}

	private function dispatchPost() {
		if( isset( $GLOBALS[ 'jvbpd_post' ] ) && wp_doing_ajax() ) {
			$this->post = $GLOBALS[ 'post' ];
			$GLOBALS[ 'post' ] = get_post( $GLOBALS[ 'jvbpd_post' ] );
		}
	}

	private function restorePost() {
		if( ! is_null( $this->post ) ) {
			$GLOBALS[ 'post' ] = $this->post;
			$this->post = null;
		}
	}

	protected function render() {
		$this->dispatchPost();
		$this->_render();
		$this->restorePost();
	}

}