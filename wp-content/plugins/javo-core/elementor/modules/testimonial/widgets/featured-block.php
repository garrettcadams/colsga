<?php
namespace jvbpdelement\Modules\Testimonial\Widgets;

class featured_block extends Base {
	
	public function get_name() { return parent::FEATURED_BLOCK; }
	public function get_title() { return 'Featured Block'; }
	public function get_icon() { return 'eicon-testimonial'; }	
	public function get_categories() { return [ 'jvbpd-elements' ]; }
	public function get_script_depends() {return [ 'owl-carousel' ];}
	
	protected function _register_controls() {
		parent::_register_controls();
	}

	protected function render() {
		parent::render();
	}
}