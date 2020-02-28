<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Elementor\Group_control_Base' ) ) {
	return false;
}

class jvbpd_group_block_style extends Elementor\Group_Control_Base {

	protected static $fields;

	public static function get_type() {
		return 'jvbpd-group-block-style';
	}

	public function module( &$output=Array(), $name='' ) {
		$output[ $name ] = Array(
			'label' => esc_html__( "Block (cards)", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'multiple' => false,
			'options' => Array(
				'' => esc_html__( "Select a module", 'jvfrmtd' ),
				'module1' => __( 'Module 1', 'jvfrmtd' ),
				'module12' => __( 'Module 12', 'jvfrmtd' ),
				'module15' => __( 'Module 15', 'jvfrmtd' ),
				'module4' => __( 'Module 4', 'jvfrmtd' ),
			),
		);
	}

	public function name( &$output=Array(), $name='' ) {
		$output[ $name ] = Array(
			'label' => esc_html__( "Block", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'multiple' => false,
			'default' => 'block2',
			'options' => jvbpd_elements_tools()->getActivateBlocks(),
			'description' => esc_html__( 'Note : "Block 8" does not support "4 Columns".', 'jvfrmtd' ),
		);
	}

	public function columns( &$output=Array(), $name='' ) {
		$output[ $name ] = Array(
			'label' => esc_html__( "Columns", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'multiple' => false,
			'default' => 1,
			'options' => jvbpd_elements_tools()->getColumnsOption( 1, 4 ) +
			Array( 6 => sprintf( _nx( '%s Column', '%s Columns', 6, 'Columns count', 'jvfrmtd' ), 6 ) ),
		);
	}

	public function carousel( &$output=Array(), $name='' ) {

		$output[ $name . '_navigation' ] = Array(
			'label' => esc_html__( 'Carousel Navigation', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Yes', 'jvfrmtd' ),
			'label_off' => __( 'No', 'jvfrmtd' ),
			'return_value' => '1',
		);

		$output[ $name . '_navi_position' ] = Array(
			'label' => __( 'Carousel Navigation Position', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'condition' => Array(
				$name . '_navigation' => '1',
			),
			'default' => 'bottom',
			'options' => Array(
				'top' => __( 'Top', 'jvfrmtd' ),
				'middle' => __( 'Side', 'jvfrmtd' ),
				'bottom'  => __( 'Bottom', 'jvfrmtd' ),
			),
			'separator' => 'none',
		);

		$output[ $name . '_dots' ] = Array(
			'label' => esc_html__( 'Carousel Dots Navigation', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Yes', 'jvfrmtd' ),
			'label_off' => __( 'No', 'jvfrmtd' ),
			'return_value' => '1',
		);

		/*

		$output[ $name . '_autoplay' ] = Array(
			'label' => esc_html__( 'Carousel Autoplay', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'condition' => [
			'jv_bpd_block2_display_type' => 'carousel',
			],
			'label_on' => __( 'Yes', 'jvfrmtd' ),
			'label_off' => __( 'No', 'jvfrmtd' ),
			'return_value' => '1',
		);
		// Carousel
		$this->add_control(
            'carousel_autoplay', [

            ]
        );

		$this->add_control(
            'carousel_loop', [
                'label' => esc_html__( 'Carousel Infinity Loop', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_navigation', [
                'label' => esc_html__( 'Carousel Navigation', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_navi_position', [
				'label' => __( 'Carousel Navigation Position', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'carousel_navigation' => '1',
				],
				'default' => 'bottom',
				'options' => [
					'top' => __( 'Top', 'jvfrmtd' ),
					'middle' => __( 'Side', 'jvfrmtd' ),
					'bottom'  => __( 'Bottom', 'jvfrmtd' ),
				],
				'separator' => 'none',
            ]
        );

		$this->add_control(
            'carousel_dots', [
                'label' => esc_html__( 'Carousel Dots Navigation', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_mouse_wheel', [
                'label' => esc_html__( 'Carousel Enable MouseWheel', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_items_per_slide', [
                'label' => esc_html__( 'Carousel Items Per Slide', 'jvfrmtd' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
            ]
        );
		*/

		/**

		$this->add_control(
            'carousel_lazyload', [
                'label' => esc_html__( 'Carousel Lazy Load', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );
		*/
	}

	public function filter_fields() {
		$args = $this->get_args();
		if( isset( $args[ 'fields' ] ) && is_array( $args[ 'fields' ] ) ) {
			foreach( $args[ 'fields' ] as $field ) {
				if( method_exists( $this, $field ) ) {
					call_user_func_array( Array( $this, $field ), Array( &$fields, $field ) );
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
