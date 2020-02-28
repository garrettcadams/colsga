<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Single_Custom_Field extends Widget_Base {

	public function get_name() { return 'jvbpd-single-custom-field'; }
	public function get_title() { return 'Single listing custom field'; }
	public function get_icon() { return 'eicon-button'; }
    public function get_categories() { return [ 'jvbpd-single-listing' ]; }

    protected function _register_controls() {}

    protected function render() {
        if(!function_exists('lv_directory_customfield')){
            return;
        }
        jvbpd_elements_tools()->switch_preview_post();
        $this->add_render_attribute('wrap', 'class', 'jvbpd-single-listing-custom-field');
        ?>
        <div <?php echo $this->get_render_attribute_string('wrap'); ?>>
            <?php lv_directory_customfield()->template->append_to_single_page(); ?>
        </div>
        <?php
        jvbpd_elements_tools()->restore_preview_post();
    }
}