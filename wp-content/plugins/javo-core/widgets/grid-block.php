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

class jvbpd_gridblock extends Widget_Base
{
    public function get_name() {
        return 'jv-gridblock';
    }

    public function get_title() {
        return esc_html__('JV Grid Block', 'jvfrmtd');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
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

          /*Skin*/
        $this->add_control('skins',
        [
            'label'         => esc_html__('Skin', 'jvfrmtd'),
            'type'          => Controls_Manager::SELECT,
            'default'       => esc_html__('skin1','jvfrmtd'),
            'options'       => [
                'skin1'    => 'Skin1',
                'skin2'    => 'Skin2',
                'skin3'    => 'Skin3',
                'skin4'    => 'Skin4',
                'skin5'    => 'Skin5',
                'skin6'    => 'Skin6',
                'skin7'    => 'Skin7',
                'skin8'    => 'Skin8',
                'skin9'    => 'Skin9',
                'skin10'    => 'Skin10',                
                ],
            ]
        );

        /* Overlay */
        $this->add_control('overlay',
        [
            'label'         => esc_html__('Overlay', 'jvfrmtd'),
            'type'          => Controls_Manager::SELECT,
            'default'       => esc_html__('overlay-gradient','jvfrmtd'),
            'options'       => [
                'overlay-gradient'    => 'Gradient',
                'overlay-radial-gradient'    => 'Radial Gradient',
                'overlay-dark'    => 'Dark'          
                ],
            ]
        );

        /* Effect */
        $this->add_control('hover_effect',
        [
            'label'         => esc_html__('Hover Effect', 'jvfrmtd'),
            'type'          => Controls_Manager::SELECT,
            'default'       => esc_html__('hover-zoom-in','jvfrmtd'),
            'options'       => [
                'hover-zoom-in'    => 'Zoom In',
                'content-slide-up'    => 'Content Slide Up',         
                'slide-up-right'    => 'Content Slide Right',          
                'slide-up-background'    => 'Content Slide Color'
                ],
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
                        '{{WRAPPER}} .heading-wapper' => 'text-align: {{VALUE}};',
                        '{{WRAPPER}} .sub-heading-text' => 'text-align: {{VALUE}};',
                        '{{WRAPPER}} .des-heading-text' => 'text-align: {{VALUE}};',
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
        <div class="jv-grid-block jvgrid-<?php echo $settings['skins']; ?> <?php echo $settings['overlay']; ?> <?php echo $settings['hover_effect']; ?>">
            <?php for ($i=0 ; $i <= 6 ; $i++) { ?>
            <div class="item">
                <div class="module-thumb">
                    <a href="#" class="imagewrap">
                        <div class="image-thumb"></div>
                    </a>
                </div>
                <div class="meta-info-container">
                    <div class="post-meta-align">
                            <a href="#" class="post-category">Health & Fitness</a>
                            <h3 class="module-title">
                                <a href="#" class="">WordPress News Magazine Charts the Most Fashionable New York Women
                                    in 2018</a>
                            </h3>
                            <div class="module-meta-info">
                                <div class="rating-wrap">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                                <span class="post-author-name">
                                    <a href="#">Armin Vans</a>
                                    <span>-</span>
                                </span>
                                <span class="post-date">
                                    <time class="entry-date module-date">Mar 22, 2017</time>
                                </span>
                            </div>
                            <div class="content">
                                stay focused and remember we design the best WordPress News and Magazine Themes. It’s the ones closest to you that want to see you fail. Another one. It’s important to use cocoa butter. It’s the key to more success, why not live smooth? Why live rough? The key to success is to keep your head above the water, 
                            </div>                        
                    </div>
                </div>
            </div> <!-- item -->
            <?php } ?>
        </div><!-- jv-grid-block -->

        <?php

    }
}