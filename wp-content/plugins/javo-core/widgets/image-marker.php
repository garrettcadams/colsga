<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Color;


if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Image_Marker extends Widget_Base {

	public function get_name() { return 'jvbpd-image-marker'; }
	public function get_title() { return 'Hotspot Image marker'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'jvfrmtd' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],				
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'large',
				'separator' => 'none',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
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
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption',
			[
				'label' => __( 'Caption', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter your image caption', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'link_to',
			[
				'label' => __( 'Link to', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __( 'None', 'jvfrmtd' ),
					'file' => __( 'Media File', 'jvfrmtd' ),
					'custom' => __( 'Custom URL', 'jvfrmtd' ),
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link to', 'jvfrmtd' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'jvfrmtd' ),
				'condition' => [
					'link_to' => 'custom',
				],
				'show_label' => false,
			]
		);

		$this->add_control(
			'open_lightbox',
			[
				'label' => __( 'Lightbox', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'jvfrmtd' ),
					'yes' => __( 'Yes', 'jvfrmtd' ),
					'no' => __( 'No', 'jvfrmtd' ),
				],
				'condition' => [
					'link_to' => 'file',
				],
			]
		);

		$this->add_control(
			'attachment_new_window',
			[
				'label' => __( 'New window', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'your-plugin' ),
				'label_off' => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'open_lightbox' => 'no',
				]
			]
		);


		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'jvfrmtd' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();


		/*
		Repeater
		 */
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Hotspot markers', 'jvfrmtd' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'use_image',
				[
					'label' => __( 'Use Image', 'jvfrmtd' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Able', 'jvfrmtd' ),
					'label_off' => __( 'Disable', 'jvfrmtd' ),
					'return_value' => 'yes',
					'default' => 'no',
				]
			);

			$repeater->add_control(
				'image',
				[
					'label' => __( 'Choose Image', 'jvfrmtd' ),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => [
						'active' => true,
					],	
					'condition' => [
						'use_image' => 'yes',
					],					]
			);

			$repeater->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
					'default' => 'large',
					'separator' => 'none',
					'condition' => [
						'use_image' => 'yes',
					],		
				]
			);

			$repeater->add_responsive_control(
				'align',
				[
					'label' => __( 'Alignment', 'jvfrmtd' ),
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
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
					'condition' => [
						'use_image' => 'yes',
					],		
				]
			);

			$repeater->add_control(
				'caption',
				[
					'label' => __( 'Caption', 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'placeholder' => __( 'Enter your image caption', 'jvfrmtd' ),
					'condition' => [
						'use_image' => 'yes',
					],		
				]
			);

			$repeater->add_control(
				'link_to',
				[
					'label' => __( 'Link to', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => [
						'none' => __( 'None', 'jvfrmtd' ),
						'file' => __( 'Media File', 'jvfrmtd' ),
						'custom' => __( 'Custom URL', 'jvfrmtd' ),
					],
					'condition' => [
						'use_image' => 'yes',
					],		
				]
			);

			$repeater->add_control(
				'link',
				[
					'label' => __( 'Link to', 'jvfrmtd' ),
					'type' => Controls_Manager::URL,
					'dynamic' => [
						'active' => true,
					],
					'placeholder' => __( 'https://your-link.com', 'jvfrmtd' ),
					'condition' => [
						'link_to' => 'custom',
					],
					'show_label' => false,
					'condition' => [
						'use_image' => 'yes',
					],		
				]
			);

			$repeater->add_control(
				'open_lightbox',
				[
					'label' => __( 'Lightbox', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => __( 'Default', 'jvfrmtd' ),
						'yes' => __( 'Yes', 'jvfrmtd' ),
						'no' => __( 'No', 'jvfrmtd' ),
					],
					'condition' => [
						'link_to' => 'file',
					],
				]
			);

			$repeater->add_control(
				'attachment_new_window',
				[
					'label' => __( 'New window', 'jvfrmtd' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'your-plugin' ),
					'label_off' => __( 'No', 'your-plugin' ),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => [
						'open_lightbox' => 'no',
					]
				]
			);


			$repeater->add_control(
				'view',
				[
					'label' => __( 'View', 'jvfrmtd' ),
					'type' => Controls_Manager::HIDDEN,
					'default' => 'traditional',
				]
			);

			$repeater->end_controls_section();




			$repeater->start_controls_tabs( 'slides_repeater' );
			$repeater->start_controls_tab( 'content', [ 'label' => __( 'Content', 'jvfrmtd' ) ] );

				$repeater->add_control(
					'list_title', [
						'label' => __( 'Title', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => __( 'List Title' , 'jvfrmtd' ),
						'label_block' => true,
					]
				);

				$repeater->add_control(
					'list_content', [
						'label' => __( 'Content', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => __( 'List Content' , 'jvfrmtd' ),
						'show_label' => false,
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'position', [ 'label' => __( 'Position', 'jvfrmtd' ) ] );
				$repeater->add_control(
					'hotspot_top',
					[
						'label' => __( 'Top positoin', 'jvfrmtd' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ '%', 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1000,
								'step' => 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'default' => [
							'unit' => '%',
							'size' => 50,
						],
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}}.hotspot_item' => 'position:absolute; top:{{SIZE}}{{UNIT}}',
						]
					]
				);

				$repeater->add_control(
					'hotspot_left',
					[
						'label' => __( 'Left position', 'jvfrmtd' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ '%', 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1000,
								'step' => 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'default' => [
							'unit' => '%',
							'size' => 50,
						],
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}}.hotspot_item' => 'left:{{SIZE}}{{UNIT}}',
						]
					]
				);

				$repeater->add_control(
					'tooltip-detail',
					[
						'label' => __( 'Margin', 'jvfrmtd' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em' ],
						'default' => [
							'top'	=>	'35',
							'right'	=>	'0',
							'bottom'=>	'0',
							'lett'	=>	'0',
							'isLinked'	=> false,
						],
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .detail-tooltip' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'style', [ 'label' => __( 'Style', 'jvfrmtd' ) ] );

				$repeater->add_control(
					'hot_spot_color',
					[
						'label' => __( 'Hot spot color', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '#ff1c1c',
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .heartbeat' => 'background: {{VALUE}}'
						],
					]
				);

				$repeater->add_control(
					'des_text_color',
					[
						'label' => __( 'Text color', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .detail-tooltip h3' => 'color: {{VALUE}}', 
							'{{WRAPPER}} {{CURRENT_ITEM}} .detail-tooltip div' => 'color: {{VALUE}}',
						],
					]
				);

				$repeater->add_control(
					'des_bg',
					[
						'label' => __( 'Background color', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .detail-tooltip' => 'background-color: {{VALUE}}'
						],
					]
				);

			$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		


		$this->add_control(
			'list',
			[
				'label' => __( 'Repeater List', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_title' => __( 'Hotspot #1', 'jvfrmtd' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'jvfrmtd' ),
					],
					[
						'list_title' => __( 'Hotspot #2', 'jvfrmtd' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'jvfrmtd' ),
					],
				],
				'title_field' => '{{{ list_title }}}',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_image_marker_border',
			[
				'label' => __( 'Image Marker', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tooltip_border',
				'selector' => '{{WRAPPER}} .detail-tooltip',
			]
		);

			$this->add_control(
			'_tooltip_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .detail-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tooltip_box_shadow',
				'selector' => '{{WRAPPER}} .detail-tooltip',
			]
		);
	

		$this->add_control(
			'init_hot_spot_color',
			[
				'label' => __( 'Hot spot color', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ff1c1c',
				'selectors' => [
					'{{WRAPPER}} .heartbeat' => 'background: {{VALUE}}'
				],
			]
		);

		$this->add_control(
			'des_init_text_color',
			[
				'label' => __( 'Tooltip Text color', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#454545',
				'selectors' => [
					'{{WRAPPER}} .detail-tooltip h3' => 'color: {{VALUE}}',
					'{{WRAPPER}} .detail-tooltip div' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'des_init_bg',
			[
				'label' => __( 'Tooltip Background color', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .detail-tooltip' => 'background-color: {{VALUE}}',
				],
			]
		);




		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __( 'Image', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => __( 'Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label' => __( 'Max Width', 'jvfrmtd' ) . ' (%)',
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_panel_style',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab( 'normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => __( 'Opacity', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-image img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label' => __( 'Opacity', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .elementor-image:hover img',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __( 'Transition Duration', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'jvfrmtd' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .elementor-image img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elementor-image img',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_caption',
			[
				'label' => __( 'Caption', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'caption!' => '',
				],
			]
		);

		$this->add_control(
			'caption_align',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
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
					'justify' => [
						'title' => __( 'Justified', 'jvfrmtd' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'caption_typography',
				'selector' => '{{WRAPPER}} .widget-image-caption',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_responsive_control(
			'caption_space',
			[
				'label' => __( 'Spacing', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['image']['url'] ) ) {
			return;
		}

		$has_caption = ! empty( $settings['caption'] );

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-image' );

		if ( ! empty( $settings['shape'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-image-shape-' . $settings['shape'] );
		}

		$link = $this->get_link_url( $settings );

		if ( $link ) {
			$this->add_render_attribute( 'link', [
				'href' => $link['url'],
				'data-elementor-open-lightbox' => $settings['open_lightbox'],
			] );

			// if ( Plugin::$instance->editor->is_edit_mode() ) {
			// 	$this->add_render_attribute( 'link', [
			// 		'class' => 'elementor-clickable',
			// 	] );
			// }
			// 
			if ( $settings['attachment_new_window'] == 'yes' ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( ! empty( $link['is_external'] ) ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( ! empty( $link['nofollow'] ) ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		} ?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $has_caption ) : ?>
				<figure class="wp-caption">
			<?php endif; ?>
			<?php if ( $link ) : ?>
					<a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
			<?php endif; ?>
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings ); ?>
			<?php if ( $link ) : ?>
					</a>
			<?php endif; ?>
			<?php if ( $has_caption ) : ?>
					<figcaption class="widget-image-caption wp-caption-text"><?php echo $settings['caption']; ?></figcaption>
			<?php endif; ?>
			<?php if ( $has_caption ) : ?>
				</figure>
			<?php endif; ?>
		</div>
		<?php

		if ( $settings['list'] ) {
			echo '<div class="hotspot_wrap">';
			foreach (  $settings['list'] as $item ) {
				
				if (!$item['use_image']==='yes'){
					$hotspot_type = "type-heart";
				}else{
					$hotspot_type = "type-img";
				}



				echo '<div class="hotspot_item elementor-repeater-item-' . $item['_id'] . ' ' . $hotspot_type .'">';
					if ($item['image'])
				
						if (!$item['use_image']==='yes'){
							echo '<div class="heartbeat"></div>';
						}else{
						
							if ($item['image']['url']):
								echo '<div class="item-img"><img src="' . $item['image']['url'] . '" ></div>';
							endif;					
								echo '<div class="detail-tooltip">';
								echo '<h3>' . $item['list_title'] . '</h3>';
								echo '<div class="hotpost_content">' . $item['list_content'] . '</div>';
								echo '</div>';
					}
				echo '</div>';
			}
			echo '</div>';
		}
	}

	protected function _content_template() {
		?>
		<#
		var image = {
			id: settings.image.id,
			url: settings.image.url,
			size: settings.thumbnail_size,
			dimension: settings.thumbnail_custom_dimension,
			model: view.getEditModel()
		};
		var image_url = elementor.imagesManager.getImageUrl( image );
		#>
		<img src="{{{ image_url }}}" />
		<# if ( settings.list.length ) { #>
			<div class="hotspot_wrap">
			<# _.each( settings.list, function( item ) { #>
				<div class="hotspot_item elementor-repeater-item-{{ item._id }}">
					<div class="heartbeat"></div>
					<div class="detail-tooltip">
						<h3>{{{ item.list_title }}}</h3>
						<div class="hotspot_content">{{{ item.list_content }}}</div>
					</div>
				</div>
			<# }); #>
			</div>
		<# } #>
		<?php
	}

	/**
	 * Retrieve image widget link URL.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $settings
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	private function get_link_url( $settings ) {
		if ( 'none' === $settings['link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['link_to'] ) {
			if ( empty( $settings['link']['url'] ) ) {
				return false;
			}
			return $settings['link'];
		}

		return [
			'url' => $settings['image']['url'],
		];
	}
}