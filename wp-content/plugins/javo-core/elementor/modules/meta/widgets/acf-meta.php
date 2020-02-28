<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;
class jvbpd_acf_detail extends Base {
	public function get_name() { return parent::ACF_META; }
	public function get_title() { return 'ACF Detail'; }
	public function get_icon() { return 'fa fa-user-o'; }
	public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }

	protected function render() {

		$this->_render();
	}
}