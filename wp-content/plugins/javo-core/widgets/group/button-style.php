<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'Elementor\Group_control_Base' ) ) {
	return false;
}

class jvbpd_group_button_style extends Elementor\Group_Control_Base {

	protected static $fields;

	public static function get_type() { return 'jvbpd-group-button-style'; }

	public function button_txt( &$fields=Array(), $name='' ) {
		$fields[ 'button_label_normal' ] = array(
			'label'       => esc_html__( 'Button Label Text', 'jvfrmtd' ),
			'type'        => Controls_Manager::TEXT,
			'label_block'	=> true,
			'default'     => esc_html__( 'Button', 'jvfrmtd' ),
		);

		$fields[ 'button_icon_normal' ] = array(
			'label'       => esc_html__( 'Button Icon', 'jvfrmtd' ),
			'type'        => Controls_Manager::ICON,
			'label_block' => true,
			'file'        => '',
		);
	}

	public function button_txt_hover( &$fields=Array(), $name='' ) {

		$fields[ 'button_label_hover' ] = array(
			'label'       => esc_html__( 'Button Hover Text', 'jvfrmtd' ),
			'type'        => Controls_Manager::TEXT,
			'label_block'	=>	true,
			'default'     => esc_html__( 'Button', 'jvfrmtd' ),
		);

		$fields[ 'button_icon_hover' ] = array(
			'label'       => esc_html__( 'Button Hover Icon', 'jvfrmtd' ),
			'type'        => Controls_Manager::ICON,
			'label_block' => true,
			'file'        => '',
		);

		$fields[ 'button_label_hover' ] = array(
			'label'       => esc_html__( 'Button Hover Text', 'jvfrmtd' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Button', 'jvfrmtd' ),
		);
	}

	public function settings( &$fields=Array(), $name='' ) {
		$fields[ 'hover_effect' ] = array(
			'label'   => esc_html__( 'Hover Effect', 'jvfrmtd' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'effect-0',
			'options' => array(
				'effect-0'  => esc_html__( 'None', 'jvfrmtd' ),
				'effect-1'  => esc_html__( 'Fade', 'jvfrmtd' ),
				'effect-2'  => esc_html__( 'Down Slide', 'jvfrmtd' ),
				'effect-3'  => esc_html__( 'Up Slide', 'jvfrmtd' ),
				'effect-4'  => esc_html__( 'Right Slide', 'jvfrmtd' ),
				'effect-5'  => esc_html__( 'Left Slide', 'jvfrmtd' ),
				'effect-6'  => esc_html__( 'Up Scale', 'jvfrmtd' ),
				'effect-7'  => esc_html__( 'Down Scale', 'jvfrmtd' ),
				'effect-8'  => esc_html__( 'Top Diagonal Slide', 'jvfrmtd' ),
				'effect-9'  => esc_html__( 'Bottom Diagonal Slide', 'jvfrmtd' ),
				'effect-10' => esc_html__( 'Right Rayen', 'jvfrmtd' ),
				'effect-11' => esc_html__( 'Left Rayen', 'jvfrmtd' ),
			),
		);
	}

	public function btn_size( &$fields=Array(), $name='' ) {
		$fields[ 'button_custom_width' ] = array(
			'label'      => esc_html__( 'Button Width', 'jvfrmtd' ),
			'type'       => Controls_Manager::SLIDER,
			'default'     => array(
				'size' => 150,
			),
			'size_units' => array(
				'px', 'em', '%',
			),
			'range'      => array(
				'px' => array(
					'min' => 40,
					'max' => 1000,
				),
				'%' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-advanced-button' => 'width: {{SIZE}}{{UNIT}};',
			),
		);
		$fields[ 'button_custom_height' ] = array(
			'label'      => esc_html__( 'Button Height', 'jvfrmtd' ),
			'type'       => Controls_Manager::SLIDER,
			'default'     => array(
				'size' => 44,
			),
			'size_units' => array(
				'px', 'em', '%',
			),
			'range'      => array(
				'px' => array(
					'min' => 10,
					'max' => 1000,
				),
				'%' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-advanced-button' => 'height: {{SIZE}}{{UNIT}};',
			),
		);
		$fields[ 'button_alignment' ] = array(
			'label'   => esc_html__( 'Button Alignment', 'jvfrmtd' ),
			'type'    => Controls_Manager::CHOOSE,
			'default' => 'flex-start',
			'options' => array(
				'flex-start'    => array(
					'title' => esc_html__( 'Left', 'jvfrmtd' ),
					'icon'  => 'fa fa-align-left',
				),
				'center' => array(
					'title' => esc_html__( 'Center', 'jvfrmtd' ),
					'icon'  => 'fa fa-align-center',
				),
				'flex-end' => array(
					'title' => esc_html__( 'Right', 'jvfrmtd' ),
					'icon'  => 'fa fa-align-right',
				),
			),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-button__container' => 'justify-content: {{VALUE}};',
			),
		);
		$fields[ 'button_margin' ] = array(
			'label'      => __( 'Button Margin', 'jvfrmtd' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-advanced-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		);
	}

	public function normal_style( &$fields=Array(), $name='' ) {
		$fields[ 'normal_border_radius' ] = array(
			'label'      => __( 'Button Radius', 'jvfrmtd' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-advanced-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		);
		$fields[ 'button_padding' ] =array(
			'label'      => __( 'Button Padding', 'jvfrmtd' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-normal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		);
	}

	public function hover_style( &$fields=Array(), $name='' ) {

		$fields[ 'hover_border_radius' ] = array(
			'label'      => __( 'Button Hover Radius', 'jvfrmtd' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-advanced-button' . ':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		);
		$fields[ 'hover_button_padding' ] = array(
			'label'      => __( 'Button Hover Padding', 'jvfrmtd' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		);
	}
	public function btn_bg_normal( &$fields=Array(), $name='' ) {
		$fields[ 'normal_btn_bg' ] = array(
			'label' => __( 'Button Background', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#3a3a50',
			'selectors' => Array(
				'{{WRAPPER}} ' . '.jvbpd-button_wrapper-normal' => 'background-color: {{VALUE}};',
			),
		);
	}
	public function btn_border_color_normal( &$fields=Array(), $name='' ) {
		$fields[ 'btn_border_color_normal' ] = array(
			'label' => __( 'Button Border', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			 'scheme' => [
              'type' => Scheme_Color::get_type(),
              'value' => Scheme_Color::COLOR_1,
          ],
			'selectors' => Array(
				'{{WRAPPER}} ' . '.jvbpd-advanced-button' => 'border-color: {{VALUE}}; border-style: solid;',
			),
		);
	}

	public function btn_border_width_normal( &$fields=Array(), $name='' ) {
		$fields[ 'btn_border_width_normal' ] = array(
			'label' => __( 'Border Size', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'default' => [
				'top' => 0,
				'right' => 0,
				'bottom' => 0,
				'left' => 0,
				'unit' => 'px'
			],
			'selectors' => Array(
				'{{WRAPPER}} ' . '.jvbpd-advanced-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			),
		);
	}

	public function btn_bg_hover( &$fields=Array(), $name='' ) {
		$fields[ 'hover_btn_bg' ] = array(
			'label' => __( 'Hover Background', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#121212',
			'selectors' => Array(
				'{{WRAPPER}} ' . '.jvbpd-button_wrapper-hover' => 'background-color: {{VALUE}};',
			),
		);
	}
	public function btn_border_color_hover( &$fields=Array(), $name='' ) {
		$fields[ 'btn_border_color_hover' ] = array(
			'label' => __( 'Button Border', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			 'scheme' => [
              'type' => Scheme_Color::get_type(),
              'value' => Scheme_Color::COLOR_1,
          ],
			'selectors' => Array(
				'{{WRAPPER}} ' . '.jvbpd-advanced-button:hover' => 'border-color: {{VALUE}}; border-style: solid;',
			),
		);
	}

	public function btn_border_width_hover( &$fields=Array(), $name='' ) {
		$fields[ 'btn_border_width_hover' ] = array(
			'label' => __( 'Border Size', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'default' => [
				'top' => 0,
				'right' => 0,
				'bottom' => 0,
				'left' => 0,
				'unit' => 'px'
			],
			'selectors' => Array(
					'{{WRAPPER}} ' . '.jvbpd-advanced-button:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			),
		);
	}


	public function icon_normal_style( &$fields=Array(), $name='' ) {
		$fields[ 'use_btn_icon' ] = array(
			'label'        => esc_html__( 'Use Icon?', 'jvfrmtd' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'Yes', 'jvfrmtd' ),
			'label_off'    => esc_html__( 'No', 'jvfrmtd' ),
			'return_value' => 'yes',
			'default'      => 'no',
		);

		$fields[ 'btn_icon_arrange' ] = array(
			'label'   => esc_html__( 'Icon Arrange', 'jvfrmtd' ),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				'left'   => esc_html__( 'Left', 'jvfrmtd' ),
				'top'    => esc_html__( 'Top', 'jvfrmtd' ),
				'right'  => esc_html__( 'Right', 'jvfrmtd' ),
				'bottom' => esc_html__( 'Bottom', 'jvfrmtd' ),
			),
			'default'     => 'left',
			'render_type' => 'template',
			'condition' => array(
				'use_btn_icon' => 'yes',
			),
		);

		$fields[ 'normal_icon_color' ] = array(
			'label' => esc_html__( 'Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#fff',
			'condition' => array(
				'use_btn_icon' => 'yes',
			),
			'selectors' => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-normal .jvbpd-button_icon' . ' i' => 'color: {{VALUE}};',
			),
		);

		$fields[ 'normal_icon_font_size' ] = array(
			'label'      => esc_html__( 'Font Size', 'jvfrmtd' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array(
				'px',
			),
			'range'      => array(
				'px' => array(
					'min' => 1,
					'max' => 150,
				),
			),
			'default' => array(
				'size'	 => 11,
				'unit'	=> 'px',
			),
			'condition' => array(
				'use_btn_icon' => 'yes',
			),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-normal .jvbpd-button_icon' . ' i' => 'font-size: {{SIZE}}{{UNIT}};',
			),
		);

		$fields[ 'normal_icon_box_width' ] = array(
			'label'      => esc_html__( 'Icon Box Width', 'jvfrmtd' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array(
				'px',
			),
			'range'      => array(
				'px' => array(
					'min' => 10,
					'max' => 200,
				),
			),
			'default' => array(
				'size' => 16,
				'unit' => 'px',
			),
			'condition' => array(
				'use_btn_icon' => 'yes',
			),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-normal .jvbpd-button_icon' => 'width: {{SIZE}}{{UNIT}};',
			),
		);

	}
	public function icon_hover_style( &$fields=Array(), $name='' ) {
		$fields[ 'hover_icon_color' ] = array(
			'label' => esc_html__( 'Hover Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#fff',
			'selectors' => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-hover .jvbpd-button_icon' => 'color: {{VALUE}};',
			),
		);

		$fields[ 'hover_icon_font_size' ] = array(
			'label'      => esc_html__( 'Icon Hover Size', 'jvfrmtd' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array(
				'px', 'em', 'rem',
			),
			'range'      => array(
				'px' => array(
					'min' => 1,
					'max' => 100,
				),
			),
			'default' => array(
				'size'	 => 11,
				'unit'	=> 'px',
			),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-hover .jvbpd-button_icon' => 'font-size: {{SIZE}}{{UNIT}};',
			),
		);

		$fields[ 'hover_icon_box_width' ] = array(
			'label'      => esc_html__( 'Icon Hover Width', 'jvfrmtd' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array(
				'px', 'em', '%',
			),
			'range'      => array(
				'px' => array(
					'min' => 10,
					'max' => 200,
				),
			),
			'default' => array(
				'size'	 => 16,
				'unit'	=> 'px',
			),
			'selectors'  => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-hover .jvbpd-button_icon' => 'width: {{SIZE}}{{UNIT}};',
			),
		);


	}
	public function label_normal_style( &$fields=Array(), $name='' ) {
		$fields[ 'normal_txt_color' ] = array(
			'label' => esc_html__( 'Text Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' =>'#fff',
			'selectors' => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-normal .jvbpd-button_txt' => 'color: {{VALUE}};',
			),
		);

	}
	public function label_hover_style( &$fields=Array(), $name='' ) {
		$fields[ 'hover_txt_color' ] = array(
			'label' => esc_html__( 'Hover Text Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#fff',
			'selectors' => array(
				'{{WRAPPER}} ' . '.jvbpd-button_inner-hover .jvbpd-button_txt:hover' => 'color: {{VALUE}};',
				'{{WRAPPER}} ' . '.jvbpd-button_inner-hover .jvbpd-button_txt' => 'color: {{VALUE}};',
			),
		);
	}

	public function filter_fields() {
		$args = $this->get_args();
		if( isset( $args[ 'fields' ] ) && is_array( $args[ 'fields' ] ) ) {
			foreach( $args[ 'fields' ] as $field ) {
				if( method_exists( $this, $field ) ) {
					call_user_func_array( Array( $this, $field ), Array( &$fields, $field, $args[ 'params' ] ) );
				}
			}
		}
		return $fields;
	}
	protected function init_fields() { return Array(); }

	protected function get_default_options() {
		return Array( 'popover' => false, );
	}
}