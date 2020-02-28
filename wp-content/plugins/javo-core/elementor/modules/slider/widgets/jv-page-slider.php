<?php
namespace jvbpdelement\Modules\Slider\Widgets;

use Elementor\Controls_Manager;

class JV_Page_Slider extends Base {
	public function get_name() { return parent::PAGE_SLIDER; }
	public function get_title() { return 'JV Slider (Page)'; }
	public function get_icon() { return 'eicon-slideshow'; }
	public function get_script_depends() {
		return [ 'imagesloaded', 'jquery-slick' ];
	}

}