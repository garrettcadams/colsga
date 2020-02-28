<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Header_Search extends Widget_Base {

    public $instance;

	public function get_name() { return 'jvbpd-header-search'; }
	public function get_title() { return 'Header Search'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }

    protected function _register_controls() {}

    public function header_search_filter() {
        return 'lava_get_selbox_child_term_lists';
    }

    public function render() {
        if(!function_exists('lv_directory_header_search')){
            return;
        }
        add_filter( 'Lava/HeaderSearch/CategoryFilter', Array( $this, 'header_search_filter') );
        $this->instance = lv_directory_header_search()->template;
        $this->instance->load_template();
        remove_filter( 'Lava/HeaderSearch/CategoryFilter', Array( $this, 'header_search_filter') );
    }
}