<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Site_Logo extends Widget_Base {

	public function get_name() { return 'jvbpd-site-logo'; }
	public function get_title() { return 'Javo Site Logo'; }
	public function get_icon() { return 'eicon-image'; }
    public function get_categories() { return [ 'jvbpd-elements' ]; }

    protected function _register_controls() {
        $this->start_controls_section('section_general', Array(
            'label' => esc_html__( 'General', 'jvfrmtd' ),
        ));

            $this->start_controls_tabs('image_type');
                $this->start_controls_tab( 'image_type_normal', Array(
                    'label' => esc_html__( 'Normal', 'jvfrmtd' ),
                ));
                    $this->add_control('image', Array(
                        'type' => Controls_Manager::MEDIA,
                        'label' => esc_html__( 'Image', 'jvfrmtd' ),
                        'default' => Array(
                            'url' => \Elementor\Utils::get_placeholder_image_src(),
                        ),
                    ));
                    $this->add_control('image_width', Array(
                        'type' => Controls_Manager::SLIDER,
                        'label' => esc_html__( 'Width', 'jvfrmtd' ),
                        'default' => Array(
                            'size' => '',
                            'unit' => 'px',
                        ),
                        'range' => Array(
                            'px' => Array(
                                'min' => 8,
                                'max' => 800,
                                'step' => 1,
                            ),
                            '%' => Array(
                                'min' => 0,
                                'max' => 100,
                            ),
                        ),
                        'size_units' => Array('px', '%'),
                        'selectors' => Array(
                            '{{WRAPPER}} img.brand-image' => 'width:{{SIZE}}{{UNIT}};',
                        ),
                    ));
                $this->end_controls_tab();
                $this->start_controls_tab( 'image_type_sticky', Array(
                    'label' => esc_html__( 'Sticky', 'jvfrmtd' ),
                ));
                    $this->add_control('sticky_image', Array(
                        'type' => Controls_Manager::MEDIA,
                        'label' => esc_html__( 'Image', 'jvfrmtd' ),
                        'default' => Array(
                            'url' => \Elementor\Utils::get_placeholder_image_src(),
                        ),
                    ));
                    $this->add_control('sticky_image_width', Array(
                        'type' => Controls_Manager::SLIDER,
                        'label' => esc_html__( 'Width', 'jvfrmtd' ),
                        'default' => Array(
                            'size' => '',
                            'unit' => 'px',
                        ),
                        'range' => Array(
                            'px' => Array(
                                'min' => 8,
                                'max' => 800,
                                'step' => 1,
                            ),
                            '%' => Array(
                                'min' => 0,
                                'max' => 100,
                            ),
                        ),
                        'size_units' => Array('px', '%'),
                        'selectors' => Array(
                            '{{WRAPPER}} img.brand-sticky-image' => 'width:{{SIZE}}{{UNIT}};',
                        ),
                    ));
                $this->end_controls_tab();
            $this->end_controls_tabs();
            $this->add_control('permalink_type', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( 'Link', 'jvfrmtd' ),
                'options' => Array(
                    '' => esc_html__( 'Site URL', 'jvfrmtd' ),
                    'custom' => esc_html__( 'Custom URL', 'jvfrmtd' ),
                ),
                'separator' => 'before',
            ));
            $this->add_control('permalink', Array(
                'type' => Controls_Manager::URL,
                'show_label' => false,
                'condition' => Array(
                    'permalink_type' => 'custom',
                ),
            ));
        $this->end_controls_section();
    }

    protected function render() {
        $this->add_render_attribute('wrap', 'class', 'jvbpd-brand');
        $this->add_render_attribute('anchor', Array('class' =>'brand-link', 'href'=>home_url()));
        $this->add_render_attribute('image', Array(
            'class' => Array('brand-image', 'sticky-hidden'),
            'alt' => esc_html__("Brand Image", 'jvfrmtd'),
        ));
        $this->add_render_attribute('sticky_image', Array(
            'class' => Array('brand-sticky-image', 'sticky-visible'),
            'alt' => esc_html__("Brand Sticky Image", 'jvfrmtd'),
        ));

        // Image
        foreach(Array('image', 'sticky_image') as $image ) {
            $imageArgs = $this->get_settings($image);
            $imageURL = $imageArgs['url'];
            $this->add_render_attribute($image, 'src', $imageURL);
        }
        // Permalink
        if('custom' == $this->get_settings('permalink_type')) {
            $permalinkArgs = $this->get_settings('permalink');
            if($permalinkArgs['url']){
                $this->add_render_attribute('anchor', 'href', esc_url($permalinkArgs['url']), true);
            }
            if($permalinkArgs['is_external']){
                $this->add_render_attribute('anchor', 'target', '_blank');
            }
            if($permalinkArgs['nofollow']){
                $this->add_render_attribute('anchor', 'rel', 'nofollow');
            }
        }
        ?>
        <div <?php echo $this->get_render_attribute_string('wrap'); ?>>
            <a <?php echo $this->get_render_attribute_string('anchor'); ?>>
                <img <?php echo $this->get_render_attribute_string('image'); ?>>
                <img <?php echo $this->get_render_attribute_string('sticky_image'); ?>>
            </a>
        </div>
        <?php
    }

}