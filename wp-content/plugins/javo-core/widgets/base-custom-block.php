<?php
//namespace jvbpdelement\Modules\Slides\Widgets;
namespace jvbpdelement\Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Typography;
use jvbpdelement\Base\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Base_Custom_Block extends Base_Widget {

	public function get_icon() { return 'eicon-slideshow'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }

	abstract public function getPostType();

	// public function get_script_depends() {
	// 	return [ 'imagesloaded', 'jquery-slick' ];
	// }

	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'jvfrmtd' ),
			'sm' => __( 'Small', 'jvfrmtd' ),
			'md' => __( 'Medium', 'jvfrmtd' ),
			'lg' => __( 'Large', 'jvfrmtd' ),
			'xl' => __( 'Extra Large', 'jvfrmtd' ),
		];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'header_options',
			[
				'label' => __( 'Loading Options', 'jvfrmtd' ),
				'type' => Controls_Manager::SECTION,
			]
		);

		$this->add_responsive_control(
			'col_amount',
			[
				'label' => __( 'Columns', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'options' => [
					'1' => __( '1', 'jvfrmtd' ),
					'2' => __( '2', 'jvfrmtd' ),
					'3' => __( '3', 'jvfrmtd' ),
					'4' => __( '4', 'jvfrmtd' ),
				]
			]
		);

		$this->add_responsive_control(
			'load_amount',
			[
				'label' => __( 'Load Amount', 'jvfrmtd' ),
	 	    'type'    => Controls_Manager::NUMBER,
	 	    'default' => 1,
	 	    'min'     => 1,
	 	    'max'     => 100,
	 	    'step'    => 1,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'block_layout',
			[
				'label' => __( 'Layout Order', 'jvfrmtd' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'slides_repeater' );

		$repeater->start_controls_tab( 'filelds', [ 'label' => __( 'Fields', 'jvfrmtd' ) ] );

		$repeater->add_control(
			'layout_type',
			[
				'label' => __( 'Layout Type', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'options' => [
					'header' => __( 'Header', 'jvfrmtd' ),
					'media' => __( 'Media', 'jvfrmtd' ),
					'meta' => __( 'Meta', 'jvfrmtd' ),
					'footer' => __( 'Footer', 'jvfrmtd' )
				]
			]
		);

		$repeater->add_control(
			'item_image',
			[
				'label' => _x( 'Image', 'Background Control', 'jvfrmtd' ),
				'type' => Controls_Manager::MEDIA,
				'condition'	=> [
					'layout_type' => 'media',
				],
			]
		);

		$repeater->add_control(
			'image_top_left',
			[
				'label' => __( 'Top Left', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				'condition'	=> [
					'layout_type' => 'media',
				],
			]
		);

		$postFields = jvbpd_elements_tools()->get_available_post_fields( $this->getPostType() );

		$repeater->add_control(
			'image_top_left_field',
			[
				'label' => __( '', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'separator' => 'after',
				'options' => $postFields,
				'condition'	=> [
					'image_top_left' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cblock-media-top-left' => 'position: absolute; z-index: 3;',
				],
			]
		);

		$repeater->add_control(
			'image_top_right',
			[
				'label' => __( 'Top Right', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'condition'	=> [
					'layout_type' => 'media',
				],
			]
		);

		$repeater->add_control(
			'image_top_right_field',
			[
				'label' => __( 'Field', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'separator' => 'after',
				'options' => $postFields,
				'condition'	=> [
					'image_top_right' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cblock-media-top-right' => 'position: absolute; z-index: 3;',
				],
			]
		);

		$repeater->add_control(
			'image_bottom_left',
			[
				'label' => __( 'Bottom Left', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				'condition'	=> [
					'layout_type' => 'media',
				],
			]
		);

		$repeater->add_control(
			'image_bottom_left_field',
			[
				'label' => __( 'Field', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'separator' => 'after',
				'options' => $postFields,
				'condition'	=> [
					'image_bottom_left' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cblock-media-bottom-left' => 'position: absolute; z-index: 3;',
				],
			]
		);

		$repeater->add_control(
			'image_bottom_right',
			[
				'label' => __( 'Image Bottom Right', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'condition'	=> [
					'layout_type' => 'media',
				],
			]
		);

		$repeater->add_control(
			'image_bottom_right_field',
			[
				'label' => __( 'Bottom Right Field', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'separator' => 'after',
				'options' => $postFields,
				'condition'	=> [
					'image_bottom_right' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cblock-media-bottom-right' => 'position: absolute; z-index: 3;',
				],
			]
		);

		$repeater->add_control(
			'image_center',
			[
				'label' => __( 'Image Center', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				'condition'	=> [
					'layout_type' => 'media',
				],
			]
		);

		$repeater->add_control(
			'image_center_field',
			[
				'label' => __( 'Center Field', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'options' => $postFields,
				'condition'	=> [
					'image_center' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cblock-media-center' => 'position: absolute; z-index: 3;',
				],
			]
		);


		$repeater->add_control(
			'cols',
			[
				'label' => __( 'Columns', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'one_col',
				'options' => [
					'one_col' => __( '1 Column', 'jvfrmtd' ),
					'two_cols' => __( '2 Columns', 'jvfrmtd' ),
					'three_cols' => __( '3 Columns', 'jvfrmtd' ),
					'four_cols' => __( '4 Columns', 'jvfrmtd'),
					'five_cols' => __( '5 Columns', 'jvfrmtd'),
				],
				'condition'	=> [
					'layout_type!' => 'media',
				],
			]
		);

		$repeater->add_control(
			'field_one',
			[
				'label' => __( 'Field', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'options' => $postFields,
				'condition' =>[
					'cols' => [
						'one_col', 'two_cols','three_cols', 'four_cols',
					],
					'layout_type!' => [
						'media',
					],
				]
			]
		);

		$repeater->add_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .block-cols.one_col' => 'text-align:{{VALUE}}',
				],
				'condition' => [
					'cols' => [
						'one_col', 'two_cols','three_cols', 'four_cols',
					],
					'layout_type!' => [
						'media',
					],
				]
			]
		);

		$repeater->add_control(
			'field_two',
			[
				'label' => __( 'Field 2', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'options' => $postFields,
				'condition' =>[
					'cols' => [
						'two_cols','three_cols', 'four_cols',
					],
					'layout_type!' => [
						'media',
					],
				]
			]
		);

		$repeater->add_control(
			'field_two_h_arrange',
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .block-cols.two_col' => 'text-align:{{VALUE}}',
				],
				'condition' =>[
					'cols' => [
						'two_cols','three_cols', 'four_cols',
					],
					'layout_type!' => [
						'media',
					],
				]
			]
		);


		$repeater->add_control(
			'field_three',
			[
				'label' => __( 'Field 3', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'header',
				'options' => $postFields,
				'condition' =>[
					'cols' => [
						'three_cols', 'four_cols',
					],
					'layout_type!' => [
						'media',
					],
				]
			]
		);

		$repeater->add_control(
			'field_three_h_arrange',
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .block-cols.three_col' => 'text-align:{{VALUE}}',
				],
				'condition' =>[
					'cols' => [
						'three_cols', 'four_cols',
					],
					'layout_type!' => [
						'media',
					],
				]
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'style', [ 'label' => __( 'Style', 'jvfrmtd' ) ] );

		$repeater->add_control(
			'item_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cblock-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'field_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .block-cols' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'fields_typo',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .block-cols',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$repeater->add_control( '_fields_icon', [
			'label' => __( 'Social Icon', 'jvfrmtd' ),
			'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'fields_icon',
				'default' => Array(
					'value' => '',
					'library' => '',
				),
		] );

		$repeater->add_responsive_control(
			'item_padding',
			[
				'label' => __( 'Item Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '10',
					'right' => '10',
					'bottom' => '10',
					'left' => '10',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cblock-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$repeater->add_responsive_control(
			'item_margin',
			[
				'label' => __( 'Item Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cblock-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);




		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'advanced', [ 'label' => __( 'Advanced', 'jvfrmtd' ) ] );

		$repeater->add_control(
			'custom_style',
			[
				'label' => __( 'Custom', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description' => __( 'Set custom style that will only affect this specific slide.', 'jvfrmtd' ),
			]
		);

		$repeater->add_control(
			'horizontal_position',
			[
				'label' => __( 'Horizontal Position', 'jvfrmtd' ),
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .jv-custom-block-inner .elementor-slide-content' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => 'margin-right: auto',
					'center' => 'margin: 0 auto',
					'right' => 'margin-left: auto',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'vertical_position',
			[
				'label' => __( 'Vertical Position', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => __( 'Top', 'jvfrmtd' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'jvfrmtd' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'jvfrmtd' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .jv-custom-block-inner' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'text_align',
			[
				'label' => __( 'Text Align', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .jv-custom-block-inner' => 'text-align: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'content_color',
			[
				'label' => __( 'Content Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .jv-custom-block-inner .elementor-slide-heading' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .jv-custom-block-inner .elementor-slide-description' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .jv-custom-block-inner .elementor-slide-button' => 'color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'custom_style',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'block_items',
			[
				'label' => __( 'Block Items', 'jvfrmtd' ),
				'type' => Controls_Manager::REPEATER,
				'show_label' => true,
				'default' => [
					[
						'layout_type' => 'Header',
						'field_one' => 'title',
						'cols' => 'one_col',
					],
					[
						'layout_type' => 'Media',
						'field_one' => 'media',
						'cols' => 'one_col',
					],
					[
						'layout_type' => 'Footer',
						'field_one' => 'created_date',
						'cols' => 'one_col',
					],
				],
				'fields' => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ layout_type }}}',
			]
		);


		$this->add_responsive_control(
			'slides_width',
			[
				'label' => __( 'Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 400,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .cblock_wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'slides_height',
			[
				'label' => __( 'Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 400,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .cblock_wrap' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_media_options',
			[
				'label' => __( 'Media Options', 'jvfrmtd' ),
				'type' => Controls_Manager::SECTION,
			]
		);

		$this->add_control(
			'image_overlay',
			[
				'label' => __( 'Image Overlay', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				/*'condition'	=> [
					'layout_type' => 'media',
				],*/
			]
		);

		$this->add_control(
			'image_overlay_color',
			[
				'label' => __( 'Overlay Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.3)',
				'conditions' => [
					'terms' => [
						[
							'name' => 'image_overlay',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cblock-media a.thumb' => 'position:relative; display:block;',
					'{{WRAPPER}} .cblock-media a.thumb::before' => 'background-color: {{VALUE}}; content: ""; display: block; position: absolute; left: 0; top: 0; opacity: 1; filter: alpha(opacity=0); width: 100%; height: 100%; z-index: 2;',
				],
			]
		);


		$this->add_control(
			'image_hover_overlay_color',
			[
				'label' => __( 'Hover Overlay Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.7)',
				 'conditions' => [
				 	'terms' => [
				 		[
				 			'name' => 'image_overlay',
				 			'operator' => '==',
				 			'value' => 'yes',
				 		],
				 	],
				 ],
				'selectors' => [
					'{{WRAPPER}} .cblock-media a.thumb' => 'position:relative; display:block;',
					'{{WRAPPER}} .cblock-media a.thumb:hover::before' => 'position:relative; display:block; background-color: {{VALUE}}; content: ""; display: block; position: absolute; left: 0; top: 0; opacity: 1; filter: alpha(opacity=0); width: 100%; height: 100%; z-index: 2;',
				],
			]
		);

		$this->add_image_top_control( $this, 'top_left', esc_html__( "Top Left", 'jvfrmtd' ) );
		$this->add_image_top_control( $this, 'top_right', esc_html__( "Top Right", 'jvfrmtd' ) );
		$this->add_image_top_control( $this, 'bottom_left', esc_html__( "Bottom Left", 'jvfrmtd' ) );
		$this->add_image_top_control( $this, 'bottom_right', esc_html__( "Bottom Right", 'jvfrmtd' ) );
		$this->add_image_top_control( $this, 'center', esc_html__( "Center", 'jvfrmtd' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_slides',
			[
				'label' => __( 'Slides', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_max_width',
			[
				'label' => __( 'Content Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'default' => [
					'size' => '66',
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-slide-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}


	public function add_image_top_control( $instance, $direction='', $dirLabel='' ) {

		$controls = Array(
			'image_%s_setting' => Array(
				'label' => __( 'Image %s Setting', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				 'condition'	=> Array(
				 	//'image_%s' =>'yes',
				 	//'image_%s_field!' => '',
				 ),
			),
			'image_%s_field_position_right' => Array(
				'label' => __( 'From Left', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 12,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => ' left: {{SIZE}}{{UNIT}};',
				],
				 'condition' => [
				 	'image_%s_setting' => 'yes',
				 	//'image_%s'=>'yes',
				 	//'image_%s_field!' => '',
				 ]
			),
			'image_%s_field_position_top' => Array(
				'label' => __( 'From Top', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 12,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => ' top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'image_%s_setting' => 'yes',
					//'image_%s'=>'yes',
					//'image_%s_field!' => '',
				]
			),
			'image_%s_text_color' => Array(
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'color: {{VALUE}};',
				],
				'condition' => [
					'image_%s_setting' => 'yes',
					//'image_%s'=>'yes',
					//'image_%s_field!' => '',
				]
			),
			'image_%s_text_bgcolor' => Array(
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#000',
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'image_%s_setting' => 'yes',
					//'image_%s'=>'yes',
					//'image_%s_field!' => '',
				]
			),
			'image_%s_typo' => Array(
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .cblock-media-%s',
				'condition' => [
					'image_%s_setting' => 'yes',
					//'image_%s'=>'yes',
					//'image_%s_field!' => '',
				],
				'group_ctl' => Group_Control_Typography::get_type(),
			),
			'image_%s_padding' => Array(
				'label' => __( '%s Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '10',
					'right' => '12',
					'bottom' => '10',
					'left' => '12',
					'unit' => 'px',
				],
				'condition' => [
					'image_%s_setting' => 'yes',
					//'image_%s'=>'yes',
					//'image_%s_field!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'responsive_ctl' => true,
			),
			'image_%s_radius' => Array(
				'label' => __( '%s Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '8',
					'right' => '8',
					'bottom' => '8',
					'left' => '8',
					'unit' => 'px',
				],
				'condition' => [
					'image_%s_setting' => 'yes',
					//'image_%s'=>'yes',
					//'image_%s_field!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'responsive_ctl' => true,
			),
		);

		foreach( $controls as $control_key => $control_meta ){
			$controlName = sprintf( $control_key, $direction );
			if( isset( $control_meta['label'] ) ) {
				$control_meta['label'] = sprintf( $control_meta['label'], $dirLabel );
			}

			if( isset( $control_meta['condition'] ) && is_array( $control_meta['condition'] ) ) {
				foreach( $control_meta['condition'] as $conditionKey => $conditionValue ) {
					unset( $control_meta['condition'][ $conditionKey ] );
					$newKey = sprintf( $conditionKey, $direction );
					$control_meta['condition'][ $newKey ] = $conditionValue;
				}
			}

			if( isset( $control_meta['selector'] ) ) {
				$control_meta['selector'] = sprintf( $control_meta['selector'], str_replace( '_', '-', $direction ) );
			}

			if( isset( $control_meta['selectors'] ) && is_array( $control_meta['selectors'] ) ) {
				foreach( $control_meta['selectors'] as $conditionKey => $conditionValue ) {
					unset( $control_meta['selectors'][ $conditionKey ] );
					$newKey = sprintf( $conditionKey, str_replace( '_', '-', $direction ) );
					$control_meta['selectors'][ $newKey ] = $conditionValue;
				}
			}

			if( isset( $control_meta[ 'group_ctl' ] ) && $control_meta[ 'group_ctl' ] ) {
				$control_meta[ 'name' ] = $controlName;
				$instance->add_group_control( $control_meta[ 'group_ctl' ], $control_meta );
			}elseif( isset( $control_meta[ 'responsive_ctl' ] ) && $control_meta[ 'responsive_ctl' ] ) {
				$instance->add_responsive_control( $controlName, $control_meta );
			}else{
				$instance->add_control( $controlName, $control_meta );
			}
		}
	}

	protected function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['block_items'] ) ) {
			return;
		}





		echo "<div class='row'>";
		$query = new \WP_Query( array( 'post_type' => $this->getPostType(), 'posts_per_page'=> $settings['load_amount'], 'no_found_rows'=> true ) );
		if ( $query->have_posts() ) :
				while ( $query->have_posts() ) : $query->the_post();
				$post_id = get_the_ID();

				if (has_post_thumbnail( $post_id ) ){
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );
					$media_image=$image[0];
				}else {
					$media_image = '';
				}



				$buffer='';
				foreach ( $settings['block_items'] as $block_item ) {
					$block_item_html='';
					$field_icon='';

					switch ($block_item['layout_type']) {

						case 'header':
						case 'meta':
						case 'footer':

							if (!empty($block_item['_fields_icon']['value'])){
								$icon = $block_item['_fields_icon']['value'];
								ob_start();
								if(
									isset($block_item['__fa4_migrated']['_fields_icon']) ||
									(empty($block_item['fields_icon']) && Icons_Manager::is_migration_allowed())
								) {
									Icons_Manager::render_icon( $block_item['_fields_icon'], Array('aria-hidden' => 'true') );
								}else{
									printf('<i %s></i>', esc_attr( $block_item['fields_icon']));
								}
								$field_icon = ob_get_clean();
								// $field_icon = '<i class="' . esc_attr( $icon ) . '"></i>';
							}


								$block_item_html .="<div class='cblock-item cblock-". $block_item['layout_type'] ."'>";
								if ($block_item['cols'] == 'one_col'){
									$block_item_html .="<div class='block-cols one_col ". $block_item['cols'] ."'>". $field_icon ." ". $this->getFieldValue( get_post(), $block_item['field_one'] ) ."</div>";
								}elseif ($block_item['cols'] == 'two_cols'){
									$block_item_html .="<div class='block-cols one_col ". $block_item['cols'] ."'>". $field_icon ." ". $this->getFieldValue( get_post(), $block_item['field_one'] ) ."</div>";
									$block_item_html .="<div class='block-cols two_col ". $block_item['cols'] ."'>". $field_icon ." ". $this->getFieldValue( get_post(), $block_item['field_two'] ) ."</div>";
								}elseif ($block_item['cols'] == 'three_cols'){
									$block_item_html .="<div class='block-cols one_col ". $block_item['cols'] ."'>". $field_icon ." ". $this->getFieldValue( get_post(), $block_item['field_one'] ) ."</div>";
									$block_item_html .="<div class='block-cols two_col ". $block_item['cols'] ."'>". $field_icon ." ". $this->getFieldValue( get_post(), $block_item['field_two'] ) ."</div>";
									$block_item_html .="<div class='block-cols three_col ". $block_item['cols'] ."'>". $field_icon ." ". $this->getFieldValue( get_post(), $block_item['field_three'] ) ."</div>";
								}
							break;

						case 'media':
								$block_item_html .="<div class='cblock-item cblock-media'>";
								if ($block_item['image_top_left'] !== '' && $block_item['image_top_left_field'] !== ''){
									$block_item_html .="<span class='cblock-bedge cblock-media-top-left'>" . $this->getFieldValue( get_post(), $block_item['image_top_left_field'] ) . "</span>";
								}
								if ($block_item['image_top_right'] !== '' && $block_item['image_top_right_field'] !== ''){
									$block_item_html .="<span class='cblock-bedge cblock-media-top-right'>" . $this->getFieldValue( get_post(), $block_item['image_top_right_field'] ) . "</span>";
								}
								if ($block_item['image_bottom_left'] !== '' && $block_item['image_bottom_left_field'] !== ''){
									$block_item_html .="<span class='cblock-bedge cblock-media-bottom-left'>" . $this->getFieldValue( get_post(), $block_item['image_bottom_left_field'] ) . "</span>";
								}
								if ($block_item['image_bottom_right'] !== '' && $block_item['image_bottom_right_field'] !== ''){
									$block_item_html .="<span class='cblock-bedge cblock-media-bottom-right'>" . $this->getFieldValue( get_post(),  $block_item['image_bottom_right_field'] ) . "</span>";
								}
								if ($block_item['image_center'] !== '' && $block_item['image_center_field'] !== ''){
									$block_item_html .="<span class='cblock-bedge cblock-media-center'>" . $this->getFieldValue( get_post(), $block_item['image_center_field'] ) . "</span>";
								}
								$block_item_html .="<a href='#' class='thumb'><img src='". $media_image ."'></a>";
							break;

						default:
							break;
					}
					$block_item_html	.="</div>";

					$buffer .= '<div class="elementor-repeater-item-' . $block_item['_id'] . ' jv-custom-block ">' . $block_item_html . '</div>';
					//echo $block_item_html;
				} //foreach

				$cols_amt = $settings['col_amount'];
				$cols_num='';
				switch ($settings['col_amount']) {
					case '1':
							$cols_num = 'col-md-12 col-sm-6';
						break;
					case '2':
							$cols_num = 'col-md-6 col-sm-6';
						break;
					case '3':
							$cols_num = 'col-md-4 col-sm-6';
						break;
					case '4':
							$cols_num = 'col-md-3 col-sm-6';
						break;

					default:
						# code...
						break;
				}


				$block_wrap = '<div class="cblock_wrap '. $cols_num .'">' . $buffer . '</div>';

		// 	endwhile; wp_reset_postdata();
		// else :
		// endif;
		// echo "</div>";

		echo $block_wrap;
		endwhile; wp_reset_postdata();
		else :
		endif;
		echo "</div>";


			//echo $settings['jv_bpd_block2_post_type'];
			//echo $settings['jv_bpd_block2_custom_term_listing'];
			//echo $settings['post_taxonomy'];
			//echo $settings['lv_listing_taxonomy'];
	}

	public function getPostTerms( $post, $taxonomy=false, $separator=false, $index=false ) {
		$output = false;
		if( $post instanceof \WP_Post && taxonomy_exists( $taxonomy ) ) {
			$output = wp_get_object_terms( $post->ID, $taxonomy, Array( 'fields' => 'names' ) );
			if( $separator && is_array( $output ) ) {
				$output = implode( $separator, $output );
			}else{
				if( is_numeric( $index ) ) {
					$output = isset( $output[ $index ] ) ? $output[ $index ] : false;
				}
			}
		}
		return $output;
	}

	public function field_title( $post ) { return get_the_title( $post ); }
	public function field_content( $post ) { return get_the_excerpt( $post ); }
	public function field_category( $post ) { return $this->getPostTerms( $post, 'category', false, 0 ); }
	public function field_tags( $post ) { return $this->getPostTerms( $post, 'post_tag', false, 0 ); }
	public function field_format( $post ) { return $this->getPostTerms( $post, 'post_format', false, 0 ); }
	public function field_author( $post ) { return "Author"; }
	public function field_date( $post ) { return "1 day ago"; }

	public function field_button( $post ) {
		return '<button type="button">Button</button>';
	}

	public function field_share_icons( $post ) {
		return '<button type="button">Social</button>';
	}

	public function field_listing_category( $post ) { return $this->getPostTerms( $post, 'listing_category', false, 0 ); }
	public function field_listing_location( $post ) { return $this->getPostTerms( $post, 'listing_location', false, 0 ); }
	public function field_address( $post ) { return intVal( get_post_meta( $post->ID, '_address', true ) ); }

	public function field_bedrooms( $post ) { return intVal( get_post_meta( $post->ID, 'lvac_bedrooms', true ) ); }
	public function field_bathrooms( $post ) { return intVal( get_post_meta( $post->ID, 'lvac_bathrooms', true ) ); }
	public function field_garages( $post ) { return intVal( get_post_meta( $post->ID, 'lvac_garages', true ) ); }

	public function field_price( $post ) {
		$type = 'preffix';
		$format = '%1$s %2$s';
		if( $type == 'suffix' ) {
			$format = '%2$s %1$s';
		}
		return sprintf( $format,
			get_post_meta( $post->ID, 'lvac_price_prefix', true ),
			get_post_meta( $post->ID, 'lvac_default_price', true )
		);
	}

	public function field_area( $post ) {
		$type = 'suffix';
		$format = '%1$s %2$s';
		if( $type == 'suffix' ) {
			$format = '%2$s %1$s';
		}
		return sprintf( $format,
			get_post_meta( $post->ID, 'lvac_area_size_prefix', true ),
			get_post_meta( $post->ID, 'lvac_areaNumber', true )
		);
	}

	public function field_property_id( $post ) { return get_post_meta( $post->ID, 'lvac_garages', true ); }

	public function getFieldValue( $post, $field='' ) {
		$output = false;
		$fieldName = 'field_' . $field;
		if( method_exists( $this, $fieldName ) ) {
			$output = call_user_func_array( Array( $this, $fieldName ), Array( $post ) );
		}
		return $output;
	}

}
