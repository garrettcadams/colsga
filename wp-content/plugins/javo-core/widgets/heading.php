<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_heading extends Widget_Base
{
    public function get_name() {
        return 'jv-heading';
    }

    public function get_title() {
        return esc_html__('JV Heading', 'jvfrmtd');
    }

    public function get_icon() {
        return 'eicon-type-tool';
    }

    public function get_categories() {
        return [ 'jvbpd-elements' ];
    }


    protected function _register_controls() {

        $this->start_controls_section('heading_content',
                [
                    'label'         => esc_html__('Heading Title', 'jvfrmtd'),
                ]
         );

        $this->add_control('heading_text',
                [
                    'label'         => esc_html__('Heading Title', 'jvfrmtd'),
                    'type'          => Controls_Manager::TEXT,
                    'default'       => esc_html__('Heading Title','jvfrmtd'),
                    'label_block'   => true,
                ]
         );

        /*Title Style*/
        $this->add_control('heading_style',
                [
                    'label'         => esc_html__('Style', 'jvfrmtd'),
                    'type'          => Controls_Manager::SELECT,
                    'default'       => 'style1',
                    'options'       => [
                        'style1'        => esc_html__('Style1'),
                        'style2'        => esc_html__('Style2'),
                        'style3'        => esc_html__('Style3'),
                        'style4'        => esc_html__('Style4'),
                        'style5'        => esc_html__('Style5'),
                        'style6'        => esc_html__('Style6'),
                        'style7'        => esc_html__('Style7'),
                        ],
                    'label_block'   => true,
                    ]
                );

        /*Icon Switcher*/
        $this->add_control('heading_icon_switcher',
                [
                    'label'         => esc_html__('Icon', 'jvfrmtd'),
                    'type'          => Controls_Manager::SWITCHER,
                ]
                );

		$this->add_control( '_heading_icon', [
				'label' => __( 'Icon', 'jvfrmtd' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'heading_icon',
                'default' => Array(
                    'value' => 'fas fa-plus',
                    'library' => 'solid',
                ),
                /*
				'default' => '',
				'options' => get_jv_icons_options( $icons ),
				'include' => get_jv_icons( $icons ), */
				'description' => __('It may take time to load all icons', 'jvfrmtd'),
                // 'label_block'   => true,
	            'condition'     => [
                    'heading_icon_switcher'   => 'yes',
                ]
			]
        );

        /*Title HTML TAG*/
        $this->add_control('heading_tag',
                [
                    'label'         => esc_html__('HTML Tag', 'jvfrmtd'),
                    'type'          => Controls_Manager::SELECT,
                    'default'       => esc_html__('h2','jvfrmtd'),
                    'options'       => [
                        'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
                        'div'    => 'div',
                        ],
                    ]
                );
		$this->add_control('heading_link_switcher',
                [
                    'label'         => esc_html__('Use Link', 'jvfrmtd'),
                    'type'          => Controls_Manager::SWITCHER,
                ]
         );

		$this->add_control(
				  'heading_link',
				  [
					 'label' => __( 'Heading Link', 'jvfrmtd' ),
					 'type' => Controls_Manager::URL,
					 'default' => [
						'url' => 'http://',
						'is_external' => '',
					 ],
					'condition'	=>[
						'heading_link_switcher'	=> 'yes',
					],
					 'show_external' => true, // Show the 'open in new tab' button.
				  ]
		 );

        /*Title Align*/
        $this->add_responsive_control('heading_align',
                [
                    'label'         => esc_html__( 'Alignment', 'jvfrmtd' ),
                    'type'          => Controls_Manager::CHOOSE,
                    'options'       => [
                        'left'      => [
                            'title'=> esc_html__( 'Left', 'jvfrmtd' ),
                            'icon' => 'fa fa-align-left',
                            ],
                        'center'    => [
                            'title'=> esc_html__( 'Center', 'jvfrmtd' ),
                            'icon' => 'fa fa-align-center',
                            ],
                        'right'     => [
                            'title'=> esc_html__( 'Right', 'jvfrmtd' ),
                            'icon' => 'fa fa-align-right',
                            ],
                        ],
                    'default'       => 'left',
                    'selectors'     => [
                        '{{WRAPPER}} .heading-header' => 'display:block; text-align: {{VALUE}};',
                        '{{WRAPPER}} .sub-heading-text' => 'display:block; text-align: {{VALUE}};',
                        '{{WRAPPER}} .des-heading-text' => 'display:block; text-align: {{VALUE}};',
                        ],
                    ]
                );



        /*Style 7*/

		$this->add_control(
			'heading_style7_strip_heading',
			[
				'label' => __( 'Strip Options', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'     => [
					'heading_style'   => 'style7',
				],
			]
		);

		$this->add_control(
			'heading_style7_strip_position',
			[
				'label' => __( 'Strip Position', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'bottom',
				'options' => [
					'top' => [
						'title' => __( 'Top', 'jvfrmtd' ),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'jvfrmtd' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'condition'     => [
					'heading_style'   => 'style7',
				],
			]
		);

        /*Strip Width*/
        $this->add_control('heading_style7_strip_width',
                [
                    'label'         => esc_html__('Strip Width (PX)', 'jvfrmtd'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', '%', 'em'],
                    'default'       => [
                        'unit'  => 'px',
                        'size'  => '120',
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style7-strip:before' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                    'label_block'   => true,
                    'condition'     => [
                        'heading_style'   => 'style7',
                    ],
                ]
                );

        /*Strip Height*/
        $this->add_control('heading_style7_strip_height',
                [
                    'label'         => esc_html__('Strip Height (PX)', 'jvfrmtd'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', 'em'],
                    'default'       => [
                        'unit'  => 'px',
                        'size'  => '5',
                    ],
                    'label_block'   => true,
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style7-strip,{{WRAPPER}} .jv-heading-style7-strip:before ' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                    'condition'     => [
                        'heading_style'   => 'style7',
                    ],
                ]
                );

        /*Strip Top Spacing*/
        $this->add_control('heading_style7_strip_top_spacing',
                [
                    'label'         => esc_html__('Strip Top Spacing (PX)', 'jvfrmtd'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', '%', 'em'],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style7-strip' => 'margin-top: {{SIZE}}{{UNIT}};',
                    ],
                    'label_block'   => true,
                    'condition'     => [
                        'heading_style'   => 'style7',
                    ],
                ]
                );

        /*Strip Bottom Spacing*/
        $this->add_control('heading_style7_strip_bottom_spacing',
                [
                    'label'         => esc_html__('Strip Bottom Spacing (PX)', 'jvfrmtd'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', '%', 'em'],
                    'label_block'   => true,
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style7-strip' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition'     => [
                        'heading_style'   => 'style7',
                    ],
                ]
                );

        /*Title Align*/
        $this->add_responsive_control('heading_style7_strip_align',
                [
                    'label'         => esc_html__( 'Align', 'elementor' ),
                    'type'          => Controls_Manager::CHOOSE,
                    'options'       => [
                        'left'      => [
                            'title'=> esc_html__( 'Left', 'elementor' ),
                            'icon' => 'fa fa-align-left',
                            ],
                        'none'    => [
                            'title'=> esc_html__( 'Center', 'elementor' ),
                            'icon' => 'fa fa-align-center',
                            ],
                        'right'     => [
                            'title'=> esc_html__( 'Right', 'elementor' ),
                            'icon' => 'fa fa-align-right',
                            ],
                        ],
                    'default'       => 'none',
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style7-strip:before' => 'float: {{VALUE}};',
                        ],
                    'condition'     => [
                        'heading_style'   => 'style7',
                    ],
                    ]
                );

        $this->end_controls_section();

		$this->start_controls_section('sub_heading_content',
                [
                    'label'         => esc_html__('Sub Heading Title', 'jvfrmtd'),
                ]
        );

        $this->add_control('sub_heading_switcher',
                [
                    'label'         => esc_html__('Sub Heading', 'jvfrmtd'),
                    'type'          => Controls_Manager::SWITCHER,
                ]
         );

		 $this->add_control('sub_heading_text',
                [
                    'label'         => esc_html__('Sub Heading Text', 'jvfrmtd'),
                    'type'          => Controls_Manager::TEXT,
                    'default'       => esc_html__('Sub Heading Text','jvfrmtd'),
                    'label_block'   => true,
					'condition'		=>[
						'sub_heading_switcher'	=>	'yes',
					],
                ]
         );


        $this->add_control('sub_heading_color',
                [
                    'label'         => esc_html__('Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
    				'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_1,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .sub-heading-text' => 'color: {{VALUE}};',
                        ],
                    ]
        );


        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'          => 'sub_heading_typography',
                    'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
                    'selector'      => '{{WRAPPER}} .sub-heading-text',
                ]
        );

		$this->add_responsive_control('sub_heading_padding',
                [
                    'label'         => esc_html__('Padding', 'jvfrmtd'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => ['px', 'em', '%'],
					'default' => Array(
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 10,
						'unit' => 'px'
					),

                    'selectors'     => [
                        '{{WRAPPER}} .sub-heading-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ]
                    ]
         );

       $this->end_controls_section();

		$this->start_controls_section('des_heading_content',
                [
                    'label'         => esc_html__('Short Description', 'jvfrmtd'),
                ]
        );

        $this->add_control('des_heading_switcher',
                [
                    'label'         => esc_html__('Short Description', 'jvfrmtd'),
                    'type'          => Controls_Manager::SWITCHER,
                ]
         );

		 $this->add_control('des_heading_text',
                [
                    'label'         => esc_html__('Description Text', 'jvfrmtd'),
                    'type'          => Controls_Manager::TEXTAREA,
	 				'rows'		  => 10,
                    'default'       => esc_html__('Short description here','jvfrmtd'),
                    'label_block'   => true,
					'condition'		=>[
						'des_heading_switcher'	=>	'yes',
					],
                ]
         );

        $this->add_control('des_heading_color',
                [
                    'label'         => esc_html__('Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
    				'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_1,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .des-heading-text' => 'color: {{VALUE}};',
                    ],
					'condition'		=>[
						'des_heading_switcher'	=>	'yes',
					],
                ]
        );

		 $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'          => 'des_heading_typography',
                    'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
                    'selector'      => '{{WRAPPER}} .des-heading-text',
					'condition'		=>[
						'des_heading_switcher'	=>	'yes',
					],
                ]
        );

		$this->add_responsive_control('des_heading_padding',
                [
                    'label'         => esc_html__('Padding', 'jvfrmtd'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => ['px', 'em', '%'],
					'default' => Array(
						'top' => 10,
						'right' => 10,
						'bottom' => 10,
						'left' => 10,
						'unit' => 'px'
					),

                    'selectors'     => [
                        '{{WRAPPER}} .des-heading-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ]
                    ]
         );

       $this->end_controls_section();


        /*Start Styling Section*/
        $this->start_controls_section('heading_style_section',
                [
                    'label'         => esc_html__('Heading Style', 'jvfrmtd'),
                    'tab'           => Controls_Manager::TAB_STYLE,
                ]
            );

        /*Title Color*/
        $this->add_control('heading_color',
                [
                    'label'         => esc_html__('Heading Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
    				'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_1,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .heading-header' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .heading-header a span'=> 'color: {{VALUE}};',
                        ],
                    ]
                );


        /*Title Typography*/
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'          => 'title_typography',
                    'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
                    'selector'      => '{{WRAPPER}} .heading-header',
                ]
                );

        /*Style 1*/
        /*Style 1 Border*/
        $this->add_group_control(
            Group_Control_Border::get_type(),
                [
                    'name'          => 'style_one_border',
                    'selector'      => '{{WRAPPER}} .jv-heading-style1',
                    'condition'     => [
                        'heading_style'   => 'style1',
                        ],
                ]
        );

        /*Style 2*/
        /*Background Color*/
        $this->add_control('heading_style2_background_color',
                [
                    'label'         => esc_html__('Background Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
                    'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_2,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style2' => 'background-color: {{VALUE}};',
                        ],
                    'condition'     => [
                        'heading_style'   => 'style2',
                        ],
                    ]
                );

        /*Style 2*/


        /*Style 3*/
        /*Background Color*/
        $this->add_control('heading_style3_background_color',
                [
                    'label'         => esc_html__('Background Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
                    'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_2,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style3' => 'background-color: {{VALUE}};',
                        ],
                    'condition'     => [
                        'heading_style'   => 'style3',
                        ],
                    ]
                );


        /*Style 5*/
        /*Header Line Color*/
        $this->add_control('heading_style5_header_line_color',
                [
                    'label'         => esc_html__('Line Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
                    'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_1,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style5' => 'border-bottom: 2px solid {{VALUE}};',
                        ],
                    'condition'     => [
                        'heading_style'   => 'style5',
                        ],
                    ]
                );

        /*Container Line Color*/
        $this->add_group_control(
            Group_Control_Border::get_type(),
                [
                    'name'          => 'style_five_border',
                    'selector'      => '{{WRAPPER}} .heading-wapper',
                    'condition'     => [
                        'heading_style'   => ['style2','style4','style5','style6'],
                        ],
                ]
                );

        /*Style 7*/
        /*Header Line Color*/
        $this->add_control('heading_style6_header_line_color',
                [
                    'label'         => esc_html__('Line Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
                    'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_1,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style6' => 'border-bottom: 2px solid {{VALUE}};',
                        ],
                    'condition'     => [
                        'heading_style'   => 'style6',
                        ],
                    ]
                );

        /*Triangle Color*/
        $this->add_control('heading_style6_triangle_color',
                [
                    'label'         => esc_html__('Triangle Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
                    'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_1,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style6:before' => 'border-bottom-color: {{VALUE}};',
                        ],
                    'condition'     => [
                        'heading_style'   => 'style6',
                        ],
                    ]
                );



        /*Strip Color*/
        $this->add_control('heading_style7_strip_color',
                [
                    'label'         => esc_html__('Strip Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
                    'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_1,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-style7-strip:before' => 'background-color: {{VALUE}};',
                        ],
                    'condition'     => [
                        'heading_style'   => 'style7',
                        ],
                    ]
                );

        $this->add_responsive_control('heading_padding',
                [
                    'label'         => esc_html__('Padding', 'jvfrmtd'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => ['px', 'em', '%'],
					'default' => Array(
						'top' => 10,
						'right' => 10,
						'bottom' => 10,
						'left' => 10,
						'unit' => 'px'
					),

                    'selectors'     => [
                        '{{WRAPPER}} .heading-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ]
                    ]
         );

        /*Title Margin*/
        $this->add_responsive_control('heading_margin',
                [
                    'label'         => esc_html__('Margin', 'jvfrmtd'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => ['px', 'em', '%'],
                    'selectors'     => [
                        '{{WRAPPER}} .heading-wapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ]
                    ]
                );

        /*Title Text Shadow*/
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'label'             => esc_html__('Shadow','jvfrmtd'),
                'name'              => 'heading_text_shadow',
                'selector'          => '{{WRAPPER}} .heading-header',
            ]
            );

        /*End Title Style Section*/
        $this->end_controls_section();

        $this->start_controls_section('heading_icon_style_section',
                [
                    'label'         => esc_html__('Icon Style', 'jvfrmtd'),
                    'tab'           => Controls_Manager::TAB_STYLE,
                    'condition'     => [
                        'heading_icon_switcher'   => 'yes',
                    ]
                ]
            );

         /*Icon Position*/

         $this->add_control(
			'heading_icon_block',
			[
				'label' => __( 'Upper Position', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
                'selectors'=> [
                    '{{WRAPPER}} .jv-heading-icon'=>'display:block;',
                ]
			]
        );

        /*Icon Bottom Margin*/
        $this->add_control('heading_icon_bottom_margin',
                [
                    'label'         => esc_html__('Icon Spacing', 'jvfrmtd'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', 'em', '%'],
                    'condition'     => [
                        'heading_icon_block'   => 'yes',
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-icon' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                        ]
                    ]
                );


        /*Icon Color*/
        $this->add_control('heading_icon_color',
                [
                    'label'         => esc_html__('Icon Color', 'jvfrmtd'),
                    'type'          => Controls_Manager::COLOR,
    				'scheme' => [
					    'type'  => Scheme_Color::get_type(),
					    'value' => Scheme_Color::COLOR_1,
					    ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-icon' => 'color: {{VALUE}};',
                        ],
                    ]
                );

        /*Icon Size*/
        $this->add_control('heading_icon_size',
                [
                    'label'         => esc_html__('Icon Size', 'jvfrmtd'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', 'em', '%'],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                        ]
                    ]
                );

        /*Icon Background*/
        $this->add_group_control(
            Group_Control_Background::get_type(),
                [
                    'name'              => 'heading_icon_background',
                    'types'             => [ 'classic' , 'gradient' ],
                    'selector'          => '{{WRAPPER}} .jv-heading-icon',
                    ]
                );

        /*Icon Border*/
        $this->add_group_control(
            Group_Control_Border::get_type(),
                [
                    'name'              => 'heading_icon_border',
                    'selector'          => '{{WRAPPER}} .jv-heading-icon',
                    ]
                );

        /*Icon Border Radius*/
        $this->add_control('heading_icon_border_radius',
                [
                    'label'         => esc_html__('Border Radius', 'jvfrmtd'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', '%', 'em'],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-icon' => 'border-radius: {{SIZE}}{{UNIT}};'
                        ]
                    ]
                );

        /*Icon Margin*/
        $this->add_responsive_control('heading_icon_margin',
                [
                    'label'         => esc_html__('Margin', 'jvfrmtd'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => [ 'px', 'em', '%' ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                        ]
                    ]
                );

        /*Icon Padding*/
        $this->add_responsive_control('heading_icon_padding',
                [
                    'label'         => esc_html__('Padding', 'jvfrmtd'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => [ 'px', 'em', '%' ],
                    'selectors'     => [
                        '{{WRAPPER}} .jv-heading-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                        ]
                    ]
                );

        /*Icon Text Shadow*/
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'label'             => esc_html__('Icon Shadow', 'jvfrmtd'),
                'name'              => 'heading_icon_text_shadow',
                'selector'          => '{{WRAPPER}} .jv-heading-icon',
            ]
            );

        /*End Progress Bar Section*/
        $this->end_controls_section();

    }

    protected function render($instance = [])
    {
        // get our input from the widget settings.
        $settings = $this->get_settings();

        $this->add_inline_editing_attributes('heading_text', 'none');

        $title_tag = $settings['heading_tag'];
        $selected_style = $settings['heading_style'];

		//Link
		$website_link = $this->get_settings( 'heading_link' );
		$url = $website_link['url'];
		$target = $website_link['is_external'] ? 'target="_blank"' : '';


?>
<?php if ($settings['sub_heading_switcher']=='yes'){ echo "<div class='sub-heading-text'>". $settings['sub_heading_text'] . "</div>"; } ?>
<div class="heading-wapper <?php echo $selected_style; ?>">
    <<?php echo $title_tag ; ?> class="heading-header jv-heading-<?php echo $selected_style; ?>">
		<?php if( $settings['heading_link_switcher']=='yes'):
			echo '<a href="' . $url . '" ' . $target .'>';
		endif; ?>
        <?php //if ( $settings['heading_style'] === 'style7') : ?>
        <?php if ( $settings['heading_style7_strip_position'] === 'top') : ?>
        <span class="jv-heading-style7-strip <?php echo $settings['heading_style7_strip_position']; ?>"></span>
        <?php endif; ?>
        <?php //endif; ?>

        <?php
        /* if( !empty( $settings['heading_icon'] ) && $settings['heading_icon_switcher'] ) : ?>
        <i class="jv-heading-icon <?php echo $settings['heading_icon'];?>"></i>
        <?php endif; */
        if('yes' == $this->get_settings('heading_icon_switcher')) {
            if(
                isset($settings['__fa4_migrated']['_heading_icon']) ||
                (empty($settings['heading_icon']) && Icons_Manager::is_migration_allowed())
            ) {
                Icons_Manager::render_icon( $settings['_heading_icon'], Array(
                    'aria-hidden' => 'true',
                    'class' => 'jv-heading-icon',
                ) );
            }else{
                printf('<i class="jv-heading-icon %s"></i>', esc_attr( $settings['heading_icon']));
            }
        } ?>
        <span class="jv-heading-text" <?php echo $this->get_render_attribute_string('heading_text'); ?>><?php echo esc_html($settings['heading_text']); ?></span>

        <?php //if ( $settings['heading_style'] === 'style7') : ?>
        <?php if ( $settings['heading_style7_strip_position'] === 'bottom') : ?>
        <span class="jv-heading-style7-strip <?php echo $settings['heading_style7_strip_position']; ?>"></span>
        <?php endif; ?>
        <?php //endif; ?>

		<?php if($settings['heading_link_switcher']=='yes'):
			echo '</a><!-- if link -->';
		endif; ?>
    </<?php echo $title_tag; ?>>
</div>
<?php if ($settings['des_heading_switcher']=='yes'){ echo "<p class='des-heading-text'>". $settings['des_heading_text'] . "</p>"; } ?>


    <?php
    }
}