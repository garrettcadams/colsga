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


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_single_service extends Widget_Base {

	public function get_name() { return 'jvbpd-single-service'; }

	public function get_title() { return 'Lava Service'; }

	public function get_icon() { return 'eicon-time-line'; }

	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Group', 'jvfrmtd' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'field_one_h_arrange',
			[
				'label' => __( 'Horizontal arragne', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'jvfrmtd' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .lava-service .nav-tabs' => 'text-align:{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'group_title_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		/** Color **/
		$this->start_controls_tabs( 'group_style' );

		$this->start_controls_tab(
			'group_style_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text & Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.nav-item' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'group_style_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label' => __( 'Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.nav-item:hover' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'group_style_selected',
			[
				'label' => __( 'Selected', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'group_color_selected',
			[
				'label' => __( 'Selected Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.nav-item.active' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'Group_typography',
				'label' => __( 'Typography', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .lava-service .nav.nav-tabs a',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		//jvbpd_elements_tools()->add_button_control( $this );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
		jvbpd_elements_tools()->switch_preview_post();
        echo do_shortcode(sprintf( '[lava-service post_id=%s]', get_the_ID() ) );
        jvbpd_elements_tools()->restore_preview_post();
    }

}