<?php
namespace jvbpdelement\Modules\Block\Widgets;

class Archive_Block extends Base {
	public function get_name() { return parent::ARCHIVE_BLOCK; }
	public function get_title() { return 'Archive Blcok'; }
	public function get_icon() { return 'eicon-sidebar'; }

	protected function _register_controls() {
		parent::_register_controls();
	}

	protected function render() {
		$settings = $this->get_settings();
	}
}