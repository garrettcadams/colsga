<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Single_Get_Direction extends Widget_Base {

	public function get_name() { return 'jvbpd-single-get-direction'; }
	public function get_title() { return 'Single listing get direction'; }
	public function get_icon() { return 'eicon-button'; }
    public function get_categories() { return [ 'jvbpd-single-listing' ]; }

    protected function _register_controls() {
        $this->start_controls_section( 'section_general', Array(
            'label' => esc_html__("General", 'jvfrmtd'),
        ));
            $this->add_control('display_type', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__("Display Type", 'jvfrmtd'),
                'default' => 'general',
                'options' => Array(
                    'general' => esc_html__("General", 'jvfrmtd'),
                    'modal' => esc_html__("Button + Modal", 'jvfrmtd')
                ),
            ));
            $this->add_control('button_label', Array(
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__("Button Label", 'jvfrmtd'),
                'default' => esc_html__("Get Direction", 'jvfrmtd'),
            ));
        $this->end_controls_section();
    }

    protected function render() {
        if(!function_exists('lava_directory_direction')){
            return;
        }
        $display_type = $this->get_settings('display_type');
        $this->add_render_attribute('wrap', 'class', 'jvbpd-single-listing-custom-field');
        ?>
        <div <?php echo $this->get_render_attribute_string('wrap'); ?>>
            <?php
            if(method_exists($this, 'render_' . $display_type)){
                call_user_func(Array($this, 'render_' . $display_type), lava_directory_direction()->template);
            }; ?>
        </div>
        <?php
    }

    public function render_general($instance) {
        $instance->singleTemplate();
    }

    public function render_modal($instance) {
        $this->add_render_attribute('button', Array(
            'class' => 'btn btn-block',
            'data-toggle' => 'modal',
            'data-target' => '#single-title-line-modal-get-dir',
        )); ?>
        <button <?php echo $this->get_render_attribute_string('button'); ?>>
            <?php echo $this->get_settings('button_label'); ?>
        </button>
        <?php
    }
}