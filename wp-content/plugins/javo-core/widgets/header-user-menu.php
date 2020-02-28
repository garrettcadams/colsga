<?php
/**
 * Widget Name: Header Widget
 * Author: Javo
 * Version: 1.0.0.0
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;

if( !defined( 'ABSPATH' ) ) {
    exit;
}

class Jvbpd_Header_User_Menu_Widget extends Widget_Base {

	public function get_name() { return 'jvbpd-user-menu-widget'; }
	public function get_title() { return 'User Menu Widget'; }
    public function get_icon() { return 'eicon-button'; }
    public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function _register_controls() {

        $this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
        ) );

            $this->add_control( 'user_menus', Array(
                'label' => __( 'Menus', 'jvfrmtd' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => Array(
                    Array(
                        'name' => 'menu_type',
                        'label' => esc_html__( 'Menu Type', 'jvfrmtd' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => Array(
                            '' => esc_html__("Select Type",'jvfrmtd'),
                            'add_new' => esc_html__("Add New",'jvfrmtd'),
                            'mymenu' => esc_html__("My Menu",'jvfrmtd'),
                            'bp_notification' => esc_html__("Buddypress Notification",'jvfrmtd'),
                        ),
                    ),
                    Array(
                        'name' => 'mymenu_background',
                        'label' => __( 'My menu background image', 'jvfrmtd' ),
                        'type' => Controls_Manager::MEDIA,
                        'condition' => Array('menu_type'=>'mymenu'),
                        'default' => Array(
                            'url' => '',
                        ),
                    ),
                    Array(
                        'name' => 'mymenu_location',
                        'label' => esc_html__( 'Select menu in My menu', 'jvfrmtd' ),
                        'type' => Controls_Manager::SELECT2,
                        'default' => '',
                        'condition' => Array('menu_type'=>'mymenu'),
                        'options' => jvbpd_elements_tools()->getNavLocations(),
                    ),
                    Array(
                        'name' => 'dropdown_position',
                        'label' => esc_html__('Menu Position', 'jvfrmtd'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'right',
                        'options' => Array(
                            '' => esc_html__( "Left", 'jvfrmtd'),
                            'right' => esc_html__( "Right", 'jvfrmtd'),
                        ),
                    ),
                    Array(
                        'name' => 'my_menu_text',
                        'label' => esc_html__('My Menu Text', 'jvfrmtd'),
                        'type' => Controls_Manager::TEXT,
                        //'default' => esc_html__('New', 'jvfrmtd'),
                        'condition' => Array('menu_type'=>'mymenu'),
                    ),
                    Array(
                        'name' => '_my_menu_icon',
                        'label' => esc_html__('My Menu Icon', 'jvfrmtd'),
                        'type' => Controls_Manager::ICONS,
                        'fa4compatibility' => 'my_menu_icon',
                        'default' => Array(
                            'value' => 'fas fa-plus',
                            'library' => '',
                        ),
                        'condition' => Array('menu_type'=>'mymenu'),
                    ),
                    Array(
                        'name' => 'notification_text',
                        'label' => esc_html__('Notification Text', 'jvfrmtd'),
                        'type' => Controls_Manager::TEXT,
                        //'default' => esc_html__('New', 'jvfrmtd'),
                        'condition' => Array('menu_type'=>'bp_notification'),
                    ),
                    Array(
                        'name' => '_notification_icon',
                        'label' => esc_html__('Notification Icon', 'jvfrmtd'),
                        'type' => Controls_Manager::ICONS,
                        'fa4compatibility' => 'notification_icon',
                        'default' => Array(
                            'value' => 'fas fa-plus',
                            'library' => '',
                        ),
                        'condition' => Array('menu_type'=>'bp_notification'),
                    ),
                    Array(
                        'name' => 'add_new_text',
                        'label' => esc_html__('Add New Button Text', 'jvfrmtd'),
                        'type' => Controls_Manager::TEXT,
                        'default' => esc_html__('New', 'jvfrmtd'),
                        'condition' => Array('menu_type'=>'add_new'),
                    ),
                    Array(
                        'name' => '_add_new_icon',
                        'label' => esc_html__('Add New Button Icon', 'jvfrmtd'),
                        'type' => Controls_Manager::ICONS,
                        'fa4compatibility' => 'add_new_icon',
                        'default' => Array(
                            'value' => 'fas fa-plus',
                            'library' => '',
                        ),
                        'condition' => Array('menu_type'=>'add_new'),
                    ),
                ),
            ) );
        $this->end_controls_section();

        $this->start_controls_section( 'section_addnew_content', array(
            'label' => esc_html__( 'Add New Contents', 'jvfrmtd' ),
        ) );
            $this->add_control( 'add_new_links', Array(
                'label' => __( 'Add New Links', 'jvfrmtd' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => Array(
                    Array(
                        'name' => 'label',
                        'label' => esc_html__( 'Label', 'jvfrmtd' ),
                        'type' => Controls_Manager::TEXT,
                    ),
                    Array(
                        'name' => 'page',
                        'label' => __( 'Landing Page', 'jvfrmtd' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 0,
                        'options' => Array(
                            '0' => esc_html__( 'Select a page', 'jvfrmtd' ),
                        ) + jvbpd_get_all_pages(),
                    ),
                ),
            ) );
        $this->end_controls_section();

        $this->start_controls_section(
			'add_new_btn_style',
			[
				'label' => esc_html__( 'Add New Button Style', 'jvfrmtd' ),
				'condition' => [
                    'menu_type'=>'add_new',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'          => 'add_new_btn_title',
                'label'         => __( 'Button Title', 'jvfrmtd' ),
                'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
                'selector'      => '{{WRAPPER}} .add-new-btn',
            ]
        );

        $this->add_control(
			'add_new_btn_title_color',
			[
				'label' => __( 'Button Title Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .add-new-btn' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'add_new_btn_bg_color',
			[
				'label' => __( 'Button Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .add-new-btn' => 'background-color: {{VALUE}}',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'          => 'add_new_btn_dropdown',
                'label'         => __( 'Dropdown Menu', 'jvfrmtd' ),
                'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
                'selector'      => '{{WRAPPER}} .add-new-list li a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'notify_btn_style',
			[
				'label' => esc_html__( 'Notify Button Style', 'jvfrmtd' ),
				'condition' => [
                    'menu_type'=>'bp_notification',
				],
			]
        );

        $this->add_control('notify_btn_icon',
                [
                    'label'         => esc_html__('Icon Size', 'jvfrmtd'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', 'em', '%'],
                    'selectors'     => [
                        '{{WRAPPER}} .notify-btn' => 'font-size: {{SIZE}}{{UNIT}}',
                    ],
                ]
        );

        $this->add_control(
			'notify_btn_icon_color',
			[
				'label' => __( 'Button icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .notify-btn' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'notify_btn_bg_color',
			[
				'label' => __( 'Button Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .notify-btn' => 'background-color: {{VALUE}}',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'          => 'notify_btn_dropdown',
                'label'         => __( 'Dropdown Menu', 'jvfrmtd' ),
                'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
                'selector'      => '{{WRAPPER}} .notify-list li a',
            ]
        );

        $this->end_controls_section();

         //My_menu_general_style
		$this->start_controls_section( 'my_menu_general_style', [
			'label' => __( 'General', 'jvfrmtd' ),
			'tab'   => Controls_Manager::TAB_STYLE,
        ] );

            $this->add_control( 'menu_gap', Array(
                'label' => __( 'Menu Gap', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu-wrap .jvbpd-user-menu:not(:last-child)' => 'margin-right:{{SIZE}}{{UNIT}};',
                ],
            ) );

            $this->add_responsive_control(
                'menu_alignment',
                [
                    'label' => __( 'Alignment', 'jvfrmtd' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'flex-start' => [
                            'title' => __( 'Start (Left)', 'jvfrmtd' ),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __( 'Center', 'jvfrmtd' ),
                            'icon' => 'fa fa-align-center',
                        ],
                        'flex-end' => [
                            'title' => __( 'End (Right)', 'jvfrmtd' ),
                            'icon' => 'fa fa-align-right',
                        ],
                        'space-around' => [
                            'title' => __( 'Space around', 'jvfrmtd' ),
                            'icon' => 'fa fa-align-justify',
                        ],

                        'space-between' => [
                            'title' => __( 'Space between', 'jvfrmtd' ),
                            'icon' => 'fa fa-align-justify',
                        ],
                    ],
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .jvbpd-user-menu-wrap' => 'justify-content: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_section();

        //Dropdown_Menu_style
		$this->start_controls_section( 'dropdown_style', [
			'label' => __( 'Dropdown Menu', 'jvfrmtd' ),
			'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control(
            'dropdown_bg_color',
            [
                'label' => __( 'Menu BG Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .dropdown-menu.mymenu-list' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .dropdown-menu.notify-list' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .dropdown-menu.add-new-list' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dropdown_text_color',
            [
                'label' => __( 'Menu Text Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#454545',
                'selectors' => [
                    '{{WRAPPER}} .dropdown-menu.notify-list li a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .jvbpd-user-menu ul li span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .jvbpd-user-menu ul li a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dropdown_hover_color',
            [
                'label' => __( 'Menu Hover Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#aaa',
                'selectors' => [
                    '{{WRAPPER}} .dropdown-menu.notify-list li a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .jvbpd-user-menu ul li a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => __( 'Dropdown Menu Typography', 'jvfrmtd' ),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .dropdown-menu.notify-list li a, {{WRAPPER}} .jvbpd-user-menu ul li span, {{WRAPPER}} .jvbpd-user-menu ul li a',
            ]
        );

        $this->add_control( 'dropdown_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .jvbpd-user-menu > .dropdown > .dropdown-menu.show' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dropdown_border',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} .jvbpd-user-menu > .dropdown > .dropdown-menu.show',
			]
		);

        $this->end_controls_section();


        //My_menu_style
		$this->start_controls_section( 'my_menu_style', [
			'label' => __( 'My Menu', 'jvfrmtd' ),
			'tab'   => Controls_Manager::TAB_STYLE,
        ] );

            $this->add_control(
                'my_menu_text_color',
                [
                    'label' => __( 'Text Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#fff',
                    'selectors' => [
                        '{{WRAPPER}} .jvbpd-user-menu.menu-type-mymenu .login-btn' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'my_menu_bg',
                [
                    'label' => __( 'Background Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#000',
                    'selectors' => [
                        '{{WRAPPER}} .jvbpd-user-menu.menu-type-mymenu .login-btn' => 'background-color: {{VALUE}};',
                    ],
                ]
            );


            $this->add_control( 'my_menu_radius', Array(
                'label' => __( 'Radius', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                    'size_units' => '%',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-mymenu .avartar-btn img' => 'border-radius:{{SIZE}}{{UNIT}};',
                ],
            ) );


            $this->add_control( 'my_menu_size', Array(
                'label' => __( 'Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 38,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-mymenu .avartar-btn img' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .jvbpd-user-menu a.avartar-btn' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
                ],
            ) );
        $this->end_controls_section();


        //notification_style
		$this->start_controls_section( 'notification_style', [
			'label' => __( 'Notification', 'jvfrmtd' ),
			'tab'   => Controls_Manager::TAB_STYLE,
        ] );

            $this->add_control(
                'notification_text_color',
                [
                    'label' => __( 'Text Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#fff',
                    'selectors' => [
                        '{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'notification_bg',
                [
                    'label' => __( 'Background Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#000',
                    'selectors' => [
                        '{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn' => 'background-color: {{VALUE}};',
                    ],
                ]
            );


            $this->add_control( 'notification_radius', Array(
                'label' => __( 'Radius', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                    'size_units' => '%',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn' => 'border-radius:{{SIZE}}{{UNIT}};',
                ],
            ) );


            $this->add_control( 'notification_size', Array(
                'label' => __( 'Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 38,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
                ],
            ) );


            $this->add_control( 'notification_count_position', Array(
				'label' => __( 'Count Position', 'jvfrmtd' ),
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
					'{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn .bedge' => 'top: {{TOP}}{{UNIT}};',
					'{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn .bedge' => 'right: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn .bedge' => 'bottom: {{BOTTOM}}{{UNIT}};',
					//'{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn .bedge' => 'left: {{LEFT}}{{UNIT}};',
				],
			) );



            $this->add_control( 'notification_count_bottom', Array(
                'label' => __( 'Count Position - Bottom', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn .bedge' => 'bottom:{{SIZE}}{{UNIT}};',
                ],
            ) );

            $this->add_control( 'notification_count_right', Array(
                'label' => __( 'Count Position - Right', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-bp_notification .notify-btn .bedge' => 'right:{{SIZE}}{{UNIT}};',
                ],
            ) );
        $this->end_controls_section();


         //Add_new_style
		$this->start_controls_section( 'add_new_style', [
			'label' => __( 'Add New', 'jvfrmtd' ),
			'tab'   => Controls_Manager::TAB_STYLE,
        ] );

            $this->add_control(
                'add_new_text_color',
                [
                    'label' => __( 'Text Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#fff',
                    'selectors' => [
                        '{{WRAPPER}} .jvbpd-user-menu.menu-type-add_new .add-new-btn' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'add_new_bg',
                [
                    'label' => __( 'Background Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#000',
                    'selectors' => [
                        '{{WRAPPER}} .jvbpd-user-menu.menu-type-add_new .add-new-btn' => 'background-color: {{VALUE}};',
                    ],
                ]
            );


            $this->add_control( 'add_new_radius', Array(
                'label' => __( 'Radius', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                    'size_units' => '%',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-add_new .add-new-btn' => 'border-radius:{{SIZE}}{{UNIT}};',
                ],
            ) );


            $this->add_control( 'add_new_width', Array(
                'label' => __( 'Width', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 85,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-add_new .add-new-btn' => 'width:{{SIZE}}{{UNIT}};',
                ],
            ) );

            $this->add_control( 'add_new_height', Array(
                'label' => __( 'Height', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 38,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd-user-menu.menu-type-add_new .add-new-btn' => 'height:{{SIZE}}{{UNIT}};',
                ],
            ) );
        $this->end_controls_section();
    }

	protected function render() {
        $this->add_render_attribute( 'wrap', 'class', Array( 'jvbpd-user-menu-wrap' ) ); ?>
        <div <?php echo $this->get_render_attribute_string('wrap'); ?>>
            <?php
            foreach( $this->get_settings('user_menus') as $menu ) {
                $render_menu_type = $menu['menu_type'];
                $this->add_render_attribute( 'menu_wrap', 'class', Array(
                    'jvbpd-user-menu', 'menu-type-' . $render_menu_type,
                ), true ); ?>
                <div <?php echo $this->get_render_attribute_string('menu_wrap'); ?>>
                    <?php
                    if(method_exists( $this, 'render_' . $render_menu_type)){
                        call_user_func(Array($this, 'render_' . $render_menu_type),$menu);
                    } ?>
                </div>
                <?php
            } ?>
        </div>
        <?php
    }

    public function render_add_new($args=Array()) {
        $this->add_render_attribute( 'add_new_toggle', Array(
            'class' => 'btn dropdown-toggle add-new-btn',
            'data-toggle' => 'dropdown',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false',
        ));
        $this->add_render_attribute( 'add_new_menu', Array(
            'class' => 'dropdown-menu add-new-list',
        ));
        if( 'right' == $args['dropdown_position'] ) {
            $this->add_render_attribute( 'add_new_menu', 'class', 'dropdown-menu-right' );
        }
        $strAddNewButtons = Array();
        foreach( $this->get_settings('add_new_links') as $addNewKey => $addNewMeta ) {
            if( false !== get_post_status($addNewMeta['page'])) {
                $strAddNewButtons[] = sprintf(
                    '<li class="dropdown-item"><a href="%1$s" target="_self"> %2$s</a></li>',
                    get_permalink($addNewMeta['page']), $addNewMeta['label']
                );
            }
        } ?>
            <div class="dropdown">
                <a <?php echo $this->get_render_attribute_string('add_new_toggle'); ?>>
                    <?php printf('<i class="%1$s"></i> %2$s', $args['add_new_icon'], $args['add_new_text']);?>
                </a>
                <ul <?php echo $this->get_render_attribute_string('add_new_menu'); ?>><?php echo join( '', $strAddNewButtons ); ?></ul>
            </div>
        <?php
    }

    public function render_mymenu($args=Array()) {
        if( is_user_logged_in() ) :
            $navBackgroundID = $args['mymenu_background']['id'];
            $navBackground = wp_get_attachment_image_url( $navBackgroundID, 'full' );
            //$navBackground = $navBackground ? $navBackground : JVBPD_IMG_DIR . '/bg-my-menu.png'; // Default bg
            $this->add_render_attribute( 'mymenu_toggle', Array(
                'class' => 'dropdown-toggle avartar-btn',
                'data-toggle' => 'dropdown',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
            ));
            $this->add_render_attribute( 'mymenu_menu', Array(
                'class' => 'dropdown-menu mymenu-list',
                'style' => 'background-image:url(' . $navBackground .');',
            ));
            if( 'right' == $args['dropdown_position'] ) {
                $this->add_render_attribute( 'mymenu_menu', 'class', 'dropdown-menu-right' );
            } ?>

            <div class="dropdown">
                <a <?php echo $this->get_render_attribute_string('mymenu_toggle'); ?>>
                    <?php
                    if( function_exists( 'bp_loggedin_user_avatar' ) ){
                        echo bp_loggedin_user_avatar();
                    } ?>
                </a>
                <div <?php echo $this->get_render_attribute_string('mymenu_menu'); ?>>
                    <div class="dropdown-item">
                        <?php $this->render_userinfo(); ?>
                    </div>
                    <div class="dropdown-item">
                        <?php
                        wp_nav_menu( array(
                            'menu' => $args['mymenu_location'],
                            'theme_location' => $args['mymenu_location'],
                            //'container' => 'div',
                            //'container_id' => 'jv-nav',
                            //'container_class' => 'sidebar-nav navbar-collapse',
                            'echo' => true,
                            'depth' => 3,
                            // 'fallback_cb'     => 'jvnavwalker::fallback',
                            // 'walker' => new jvnavwalker()
                        ) ); ?>
                    </div>
                </div>
            </div>
        <?php
        else:
            printf(
                '<a href="javascript:" class="login-btn" data-toggle="modal" data-target="%1$s" title="%3$s"><i class="%2$s"></i></a>',
                '#login_panel', 'fa fa-user', esc_attr__( "Login", 'jvfrmtd' )
            );
        endif;
    }

    public function render_userinfo() {
        if( is_user_logged_in() ) {
            $objCurrentUser = wp_get_current_user();
            $arrUserActions = Array(
                'display_name' => Array(
                    'label' => $objCurrentUser->display_name,
                ),
                'edit_profile' => Array(
                    'href' => is_multisite() ? get_dashboard_url( $objCurrentUser->ID, 'profile.php' ) : get_edit_profile_url( $objCurrentUser->ID ),
                    'label' => esc_html__( "Edit my profile", 'jvfrmtd' ),
                ),
                'logout' => Array(
                    'href' => wp_logout_url(),
                    'label' => esc_html__( "Logout", 'jvfrmtd' ),
                ),
            ); ?>

            <div class="user-info-in-nav-wrap">
                <div class="user-info-avatar">
                    <?php echo get_avatar( $objCurrentUser->ID, 64 ); ?>
                </div>

                <ul class="user-info-item-group">
                    <?php
                    $strLinkTemplate = '<li class="user-info-item %1$s"><a href="%3$s" target="_self"><span>%2$s</span></a></li>';
                    $strLabelTemplate = '<li class="user-info-item %1$s"><span>%2$s</span></li>';
                    foreach( $arrUserActions as $strSection => $arrInfoMeta ) {
                        printf(
                            ( isset( $arrInfoMeta[ 'href' ] ) ? $strLinkTemplate : $strLabelTemplate ),
                            $strSection,
                            $arrInfoMeta[ 'label' ],
                            ( isset( $arrInfoMeta[ 'href' ] ) ? $arrInfoMeta[ 'href' ] : '' )
                        );
                    } ?>
                </ul>
            </div>
            <?php
        }
    }

    public function render_bp_notification($args=Array()) {
        if( ! is_user_logged_in() ) {
            return;
        }
        $arrNotifications = jvbpd_bp()->getNewNotifications( get_current_user_id(), 0 );
        $arrNotifyMessages = jvbpd_bp()->getNotifyMessages( $arrNotifications );
        $intNotifyMessagesCount = sizeof( $arrNotifyMessages );
        $this->add_render_attribute( 'bp_notify_toggle', Array(
            'class' => 'dropdown-toggle notify-btn',
            'data-toggle' => 'dropdown',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false',
        ));
        $this->add_render_attribute( 'bp_notify_menu', Array(
            'class' => 'dropdown-menu notify-list',
        ));
        if( 'right' == $args['dropdown_position'] ) {
            $this->add_render_attribute( 'bp_notify_menu', 'class', 'dropdown-menu-right' );
        } ?>
        <div class="dropdown">
            <a <?php echo $this->get_render_attribute_string('bp_notify_toggle'); ?>>
                <i class="fa fa-bell"></i>
                <div class="bedge" data-bp-notifications="count"><?php echo intVal( $intNotifyMessagesCount ); ?></div>
            </a>
            <div <?php echo $this->get_render_attribute_string('bp_notify_menu'); ?>>
            <?php
            if( !empty( $arrNotifyMessages ) ) {
                foreach( $arrNotifyMessages as $strMessage ) {
                    printf( '<li class="sub-menu-item  menu-item-odd menu-item-depth-1 menu-item menu-item-type-post_type menu-item-object-page">%s</li>', $strMessage );
                }
            }else{
                printf( '<li class="sub-menu-item  menu-item-odd menu-item-depth-1 menu-item menu-item-type-post_type menu-item-object-page not-found-notification"><a href="#">%s</a></li>', esc_html__( "No new notifications", 'jvfrmtd' ) );
            } ?>
            </div>
        </div>
        <?php
    }
}