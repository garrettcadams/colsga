<?php

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Background;


if (!defined('ABSPATH')) exit;

class jvbpd_modal_popup extends Widget_Base
{
    public function get_name()
    {
        return 'jvbpd-modal-popup';
    }

    public function get_title()
    {
        return 'JV Modal Popup';
    }

    public function get_icon()
    {
        return 'eicon-rating';
    }

    public function get_categories()
    {
        return ['jvbpd-single-listing'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section('section_general', array(
            'label' => esc_html__('General', 'jvfrmtd'),
        ));
        $this->add_control(
			'des_title',
			[
				'label' => __( 'Title', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Learn More', 'jvfrmtd' ),
				'placeholder' => __( 'Type your title here', 'jvfrmtd' ),
			]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __( 'Text Alignment', 'jvfrmtd' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'jvfrmtd' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'jvfrmtd' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'jvfrmtd' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'review_description',
            [
                'label' => __('Description', 'jvfrmtd'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Default description', 'jvfrmtd'),
                'placeholder' => __('Type your description here', 'jvfrmtd'),
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section('section_field', array(
            'label' => __('Field Style', 'jvfrmtd'),
            'tab' => Controls_Manager::TAB_STYLE,
        ));
        $this->add_group_control(Group_Control_Typography::get_type(), array(
            'name' => 'learn_more_style',
            'label' => __('Title Typhography', 'jvfrmtd'),
            'selector' => '{{WRAPPER}} a.jvbpd-modal-popup-opener',
            'scheme' => Scheme_Typography::TYPOGRAPHY_3,
        ));
        $this->add_control('learn_more_color', array(
            'type' => Controls_Manager::COLOR,
            'label' => esc_html__('Title Color', 'jvfrmtd'),
            'selectors' => array(
                '{{WRAPPER}} a.jvbpd-modal-popup-opener' => 'color:{{VALUE}};',
            ),
            'default' => '#000000',
        ));
        $this->end_controls_section();
    }

    protected function render()
    {
        jvbpd_elements_tools()->switch_preview_post();

        $settings = $this->get_settings();
        $des_title = $this->get_settings('des_title');

        //$isModuleMode = 'module' == $this->get_settings('display_mode');

        // $this->add_render_attribute('wrap', 'class', array('lvar-wrap', 'text-center'));
        // $this->add_render_attribute('icon-wrap', 'class', 'author-review-icon-wrap');
        // $this->add_render_attribute('review_label', 'class', 'review-label-wrap');
        // $this->add_render_attribute('on_icon', array(
        //     'class' => array('author-review-icon', 'lvar-on'),
        //     'alt' => esc_html__("Review On Image", 'jvfrmtd'),
        // ));
        // $this->add_render_attribute('off_icon', array(
        //     'class' => array('author-review-icon', 'lvar-off'),
        //     'alt' => esc_html__("Review Off Image", 'jvfrmtd'),
        // ));

        $this->add_render_attribute('modal-opener', array(
            'class' => array('jvbpd-modal-popup-opener'),
            'href' => 'javascript:',
        )); ?>
    <div <?php echo $this->get_render_attribute_string('wrap'); ?>>

        <a <?php echo $this->get_render_attribute_string('modal-opener'); ?>>
            <?php echo $des_title; ?>
        </a>
        <script type="text/html">
            <?php echo $this->get_settings('review_description'); ?>
        </script>
        <?php

        ?>
    </div>
    <?php
    jvbpd_elements_tools()->restore_preview_post();
}
}
