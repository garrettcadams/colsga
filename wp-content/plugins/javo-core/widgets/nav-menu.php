<?php
/*
Widget Name: Javo Widget
Description: Javo widget
Author: Javothemes
Author URI: https://www.javothemes.com
*/
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_nav_menu extends Widget_Base {

	Const MENU_LV1_PARAM_FORMAT = 'menu_%1$s_%2$s';
	private $current_item_id = 0;

	public function get_name() {
		return 'jvbpd_nav_menu';
	}

	public function get_title() {
		return 'Javo Nav Menu';   // title to show on elementor
	}

	public function get_icon() {
		return 'jvic-block-menu';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-elements' ];    // category of the widget
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Javo Nav Menu', 'jvfrmtd' ),
			)
		);
			$this->add_control(
			'Des',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => sprintf(
						'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
						'<li class="doc-link">'.
						esc_html__('How to use this widget.','jvfrmtd').
						'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-single-listing-page/" style="color:#fff;"> ' .
						esc_html__( 'Documentation', 'jvfrmtd' ) .
						'</a></li><li>&nbsp;</li>'.
						'<li class="notice">'.
						esc_html__('This widget is for only single listing detail page.', 'jvfrmtd').
						'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;">' .
						esc_html__( 'Detail', 'jvfrmtd' ) .
						'</a><br/></li><li>&nbsp;</li><li>'.
						esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
						'</li></ul></div>'
					)
				)
			);

		$this->end_controls_section();

		$this->start_controls_section( 'section_menu_settings', Array(
			'label' => esc_html__( 'Menu Setting', 'jvfrmtd' ),
		));

			$this->add_control( 'nav_menu', Array(
				'label' => esc_html__( 'Menu', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'options' => jvbpd_elements_tools()->getNavLocations(),
			) );
			$this->add_menu_control( 'nav_menu' );
		$this->end_controls_section();

		$this->start_controls_section( 'section_mobile_settings', Array(
			'label' => esc_html__( 'Mobile Setting', 'jvfrmtd' ),
		));
			$this->add_control( 'mobile_breakpoint', Array(
				'label' => esc_html__( 'Breakpoint', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => Array(
					'' => esc_html__( "Select a device", 'jvfrmtd' ),
					'mobile' => esc_html__( "Mobile ( 767px )", 'jvfrmtd' ),
					'tablet' => esc_html__( "Tablet ( 1023px )", 'jvfrmtd' ),
				),
			) );
			$this->add_control( 'mobile_fullwidth', Array(
				'label' => esc_html__( 'Full Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'full-width',
				'prefix_class' => 'jvbpd-nav-menu-',
			) );

			$this->add_control( '_mobile_opener_icon', Array(
				'label' => esc_html__( 'Opener Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'mobile_opener_icon',
				'default' => Array(
					'value' => 'fas fa-star',
					'library' => 'solid',
				),
			) );

			$this->add_control( 'mobile_opener_icon_color',Array(
				'label' => esc_html__( 'Opener Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#bbbbbb',
				'selectors' => [
					'{{WRAPPER}} .jvbpd-nav-menu-widget a.mobile-opener i' => 'color: {{VALUE}};',
				],
			) );

			$this->add_control(
				'menu_icon_align',
				[
					'label' => __( 'Alignment', 'jvfrmtd' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'start' => [
							'title' => __( 'Left', 'jvfrmtd' ),
							'icon' => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'jvfrmtd' ),
							'icon' => 'fa fa-align-center',
						],
						'end' => [
							'title' => __( 'Right', 'jvfrmtd' ),
							'icon' => 'fa fa-align-right',
						],
					],
					'default' => 'center',
					'selectors' => [
						'{{WRAPPER}} .elementor-widget-container' => 'justify-content: {{VALUE}};',
					],
				]
			);

			$this->add_control( 'mobile_opener_icon_size',Array(
				'label' => esc_html__( 'Opener Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
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
					'{{WRAPPER}} .jvbpd-nav-menu-widget a.mobile-opener i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			) );

			$this->add_control( 'mobile_collapse_position',Array(
				'label' => esc_html__( 'Menu Gap From Top', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'description'	=> __('You can only see on real page.', 'jvfrmtd'),
				'default' => [
					'size' => 59,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px' ],
			) );

			$this->add_control( 'mobile_bg', Array(
				'label' => esc_html__( 'Background', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0)',
				'selectors' => Array(
					'{{WRAPPER}}.cur-device-mobile div.menu-wrap' => 'background-color:{{VALUE}};',
					'{{WRAPPER}}.cur-device-tablet div.menu-wrap' => 'background-color:{{VALUE}};',
				),
			));

			$this->add_control( 'mobile_menu_color', Array(
				'label' => esc_html__( 'Menu Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => Array(
					'{{WRAPPER}}.cur-device-mobile li.main-menu-item.menu-item-depth-0 > a > span.menu-titles' => 'color:{{VALUE}};',
					'{{WRAPPER}}.cur-device-tablet li.main-menu-item.menu-item-depth-0 > a > span.menu-titles' => 'color:{{VALUE}};',
				),
			));

		$this->end_controls_section();


	//Style

	$this->start_controls_section(
	  'menu-item-height',
	  [
		'label' => __( 'Style', 'jvfrmtd' ),
		'tab'   => Controls_Manager::TAB_STYLE,
	  ]
	);

	$this->add_control( 'menu_hover_effect', Array(
		'label' => esc_html__( 'Menu Hover Effect', 'jvfrmtd' ),
		'type' => Controls_Manager::SELECT,
		'options' => Array(
			'' => esc_html__( 'None', 'jvfrmtd' ),
			'left-right' => esc_html__( 'Left to right', 'jvfrmtd' ),
			'center' => esc_html__( 'Center', 'jvfrmtd' ),
		),
		'prefix_class' => 'menu-hover-effect-',
		'default' => '',
	));

	$this->add_control( 'menu_hover_effect_spacing', Array(
		'label' => esc_html__( 'Menu Space Between Line And Label', 'jvfrmtd' ),
		'type' => Controls_Manager::SLIDER,
		'default' => Array(
			'size' => 0,
			'unit' => 'px',
		),
		'range' => Array(
			'px' => Array(
				'min' => 0,
				'max' => 100,
				'step' => 1,
			),
			'%' => Array(
				'min' => 0,
				'max' => 100,
			),
		),
		'size_units' => Array( 'px', '%' ),
		'condition' => Array('menu_hover_effect!'=>''),
		'selectors' => Array(
			'{{WRAPPER}} .nav-item.menu-item-depth-0 > a.nav-link > span:after' => 'margin-bottom:-{{SIZE}}{{UNIT}};',
		),
	));

	$this->add_control( 'menu_hover_effect_color', Array(
		'label' => esc_html__( 'Menu Hover Effect Boder Color', 'jvfrmtd' ),
		'type' => Controls_Manager::COLOR,
		'default' => '#000000',
		'condition' => Array('menu_hover_effect!'=>''),
		'selectors' => Array(
			'{{WRAPPER}} .nav-item.menu-item-depth-0 > a.nav-link > span:after' => 'border-color:{{VALUE}};',
		),
	));

	$this->add_control( 'sticky_menu_hover_effect_color', Array(
		'label' => esc_html__( 'Sticky Menu Hover Effect Boder Color', 'jvfrmtd' ),
		'type' => Controls_Manager::COLOR,
		'default' => '#000000',
		'condition' => Array('menu_hover_effect!'=>''),
		'selectors' => Array(
			'.is-sticky .elementor-element.elementor-element-{{ID}} .nav-item.menu-item-depth-0 > a.nav-link > span:after' => 'border-color:{{VALUE}};',
		),
	));

	$this->add_control( 'dropdown_menu_spacing', Array(
		'label' => esc_html__( 'Dropdown menu spacing', 'jvfrmtd' ),
		'type' => Controls_Manager::SLIDER,
		'default' => Array(
			'size' => 0,
			'unit' => 'px',
		),
		'range' => Array(
			'px' => Array(
				'min' => 0,
				'max' => 100,
				'step' => 1,
			),
			'%' => Array(
				'min' => 0,
				'max' => 100,
			),
		),
		'size_units' => Array( 'px', '%' ),
		'selectors' => Array(
			'.elementor-element.elementor-element-{{ID}} .nav-item.menu-item-depth-0 > a.nav-link' => 'margin-bottom:{{SIZE}}{{UNIT}};',
		),
	));

	$this->add_control( 'dropdown_menu_effect', Array(
		'label' => esc_html__( 'Menu Hover Effect Boder Color', 'jvfrmtd' ),
		'type' => Controls_Manager::SELECT,
		'default' => 'fade',
		'options' => Array(
			'' => esc_html__( 'None', 'jvfrmtd' ),
			'fade' => esc_html__( 'Fade', 'jvfrmtd' ),
			'slide' => esc_html__( 'Slide', 'jvfrmtd' ),
		),
		'prefix_class' => 'dropdown-effect-',
	) );

	$this->add_control(
		'menu_display_style',
		[
			'label' => __( 'Display Type', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'horizon' => __( 'Horizontal', 'jvfrmtd' ),
				'vertical' => __( 'Vertical', 'jvfrmtd' ),
			],
			'default' => 'solid',
		]
	);

/*
    $this->add_control(
      'menu_wrap_height',
      [
          'label' => __( 'Menu Wrap Height', 'jvfrmtd' ),
          'type' => Controls_Manager::SLIDER,
          'default' => [
              'size' => 60,
          ],
          'range' => [
              'px' => [
                  'min' => 0,
                  'max' => 300,
                  'step' => 1,
              ],
              '%' => [
                  'min' => 0,
                  'max' => 100,
              ],
          ],
          'size_units' => [ 'px', '%' ],
          'selectors' => [
              '{{WRAPPER}} #navigation-bar' => 'height: {{SIZE}}{{UNIT}};',
          ],
      ]
  );
*/

	$this->add_responsive_control( 'menu_text_align', Array(
		'label' => __( 'Alignment', 'jvfrmtd' ),
		'type' => Controls_Manager::CHOOSE,
		'default' =>'center',
		'options' => Array(
			'flex-start' => Array(
				'title' => __( 'Left', 'jvfrmtd' ),
				'icon' => 'fa fa-align-left',
			),
			'center' => Array(
				'title' => __( 'Center', 'jvfrmtd' ),
				'icon' => 'fa fa-align-center',
			),
			'flex-end' => Array(
				'title' => __( 'Right', 'jvfrmtd' ),
				'icon' => 'fa fa-align-right',
			),
		),
		'selectors' => Array(
			'{{WRAPPER}} .jvbpd-nav-menu' => 'justify-content: {{VALUE}};',
		),
	) );

  $this->end_controls_section();

  $this->start_controls_section(
			'section_menu_typo',
			[
				'label' => __( 'Menu', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


	$this->start_controls_tabs( 'tabs_menu_item_style' );

		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);
		  $this->add_group_control( Group_Control_Typography::get_type(), [
			'label'	=> __('Main Menu', 'jvfrmtd'),
			'name' => 'menu_typography',
			'selector' => '{{WRAPPER}} li.main-menu-item.menu-item-depth-0 > a > span.menu-titles',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		  ] );
		$this->add_control(
			'menu_color',
			  [
				  'label' => __( 'Menu Color', 'jvfrmtd' ),
				  'type' => Controls_Manager::COLOR,
				  'default' => '',
				  'separator' => 'after',
				  'scheme' => [
					  'type' => Scheme_Color::get_type(),
					  'value' => Scheme_Color::COLOR_1,
				  ],
				  'selectors' => [
					  '{{WRAPPER}} li.main-menu-item.menu-item-depth-0 > a > span.menu-titles' => 'color: {{VALUE}}',
				  ],
			  ]
		  );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		  $this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'menu_typography_hover',
			'selector' => '{{WRAPPER}} li.main-menu-item > a:hover span.menu-titles',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		  ] );

		 $this->add_control(
			'menu_hover_color',
			  [
				  'label' => __( 'Menu Hover Color', 'jvfrmtd' ),
				  'type' => Controls_Manager::COLOR,
				  'default' => '',
				  'separator' => 'after',
				  'scheme' => [
					  'type' => Scheme_Color::get_type(),
					  'value' => Scheme_Color::COLOR_1,
				  ],
				  'selectors' => [
					  '{{WRAPPER}} li.main-menu-item > a:hover > span.menu-titles' => 'color: {{VALUE}}',
				  ],
			  ]
		  );

		$this->end_controls_tab();
	$this->end_controls_tabs();
/*
   $this->add_responsive_control(
      'menu_padding_top_bottom',
      [
          'label' => __( 'Menu Top Bottom', 'jvfrmtd' ),
          'type' => Controls_Manager::SLIDER,
          'default' => [
              'size' => 20,
          ],
          'range' => [
              'px' => [
                  'min' => 0,
                  'max' => 100,
                  'step' => 1,
              ],
              '%' => [
                  'min' => 0,
                  'max' => 100,
              ],
          ],
          'size_units' => [ 'px', '%' ],
          'selectors' => [
              '{{WRAPPER}} .navbar-nav > li > a, {{WRAPPER}} .navbar-nav > li > .btn , {{WRAPPER}} .navbar-nav > li > .overlay-sidebar-opener' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
          ],
      ]
  );
*/
   $this->add_responsive_control(
      'menu_padding_right',
      [
          'label' => __( 'Menu Right Space', 'jvfrmtd' ),
          'type' => Controls_Manager::SLIDER,
          'default' => [
              'size' => 20,
          ],
          'range' => [
              'px' => [
                  'min' => 0,
                  'max' => 100,
                  'step' => 1,
              ],
              '%' => [
                  'min' => 0,
                  'max' => 100,
              ],
          ],
          'size_units' => [ 'px', '%' ],
          'selectors' => [
              '{{WRAPPER}} .jvbpd-nav-menu>li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
          ],
      ]
  );

/*
   $this->add_responsive_control(
      'menu_space',
      [
          'label' => __( 'Space with Dropdown', 'jvfrmtd' ),
          'type' => Controls_Manager::SLIDER,
          'default' => [
              'size' => 0,
          ],
          'range' => [
              'px' => [
                  'min' => -50,
                  'max' => 100,
                  'step' => 1,
              ],
              '%' => [
                  'min' => 0,
                  'max' => 100,
              ],
          ],
          'size_units' => [ 'px', '%' ],
          'selectors' => [
              '{{WRAPPER}} .menu-depth-1' => 'margin-top: {{SIZE}}{{UNIT}};',
          ],
      ]
  );
*/
  $this->end_controls_section();

/** Sub menu **/
  $this->start_controls_section(
  'section_submenu_typo',
      [
        'label' => __( 'Sub Menu', 'jvfrmtd' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );


	$this->start_controls_tabs( 'tabs_submenu_item_style' );

		$this->start_controls_tab(
			'tab_submenu_item_normal',
			[
				'label' => __( 'normal', 'jvfrmtd' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
					'label'	=> __('Sub Menu', 'jvfrmtd'),
					'name' => 'submenu_typography',
					'selector' =>'{{WRAPPER}} .sub-menu-item > a > span.menu-titles',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				]
		);
					//'{{WRAPPER}}.jvbpd-nav>li.jvbpd-my_menu-nav ul.sub-menu-item > li.sub-menu-item div.menu-my-menu-container ul li a span.menu-titles',

			$this->add_control(
				'submenu_text_color',
				  [
					  'label' => __( 'Sub Menu Color', 'jvfrmtd' ),
					  'type' => Controls_Manager::COLOR,
					  'default' => '#a9a9a9',
					  'scheme' => [
						  'type' => Scheme_Color::get_type(),
						  'value' => Scheme_Color::COLOR_1,
					  ],
					  'selectors' => [
						  '{{WRAPPER}} .sub-menu-item > a > span.menu-titles' => 'color: {{VALUE}}',
						  '{{WRAPPER}} .jvbpd-nav>li.jvbpd-my_menu-nav ul.sub-menu-second > li.sub-menu-item div.menu-my-menu-container ul li a span.menu-titles' => 'color: {{VALUE}}', //sub top menu ( right )
					  ],
				  ]
			  );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_submenu_item_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);
			$this->add_group_control( Group_Control_Typography::get_type(), Array(
				'label' => __( 'Sub menu', 'jvfrmtd' ),
				'name' => 'submenu_typography_hover',
				'selector' =>
						'{{WRAPPER}} .sub-menu-item > a:hover > span.menu-titles',
						//'{{WRAPPER}}.jvbpd-nav>li.jvbpd-my_menu-nav ul.sub-menu-second > li.sub-menu-item div.menu-my-menu-container ul li a:hover span.menu-titles',

				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			) );

			$this->add_control(
				'submenu_text_hover_color',
				  [
					  'label' => __( 'Sub Menu Color', 'jvfrmtd' ),
					  'type' => Controls_Manager::COLOR,
					  'default' => '#666666',
					  'scheme' => [
						  'type' => Scheme_Color::get_type(),
						  'value' => Scheme_Color::COLOR_1,
					  ],
					  'selectors' => [
						  '{{WRAPPER}} .sub-menu-item > a:hover > span.menu-titles' => 'color: {{VALUE}}',
						  '{{WRAPPER}}.jvbpd-nav>li.jvbpd-my_menu-nav ul.sub-menu-second > li.sub-menu-item div.menu-my-menu-container ul li a:hover span.menu-titles' => 'color: {{VALUE}}',
					  ],
				  ]
			  );


		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'submenu_bg_color', [
			'label' => __( 'Background Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#ffffff',
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'description' => '(Except mega menu. You can set it up on mega menu setting.)',
			'selectors' => [
				'{{WRAPPER}} li.menu-item-depth-0:not(.wide-container) .sub-menu-second' => 'background-color: {{VALUE}}',
			],
		]);

		 $this->add_responsive_control( 'submenu_wrap_padding', Array(
			'label' => __( 'Sub Wrap Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'default' => Array(
				'top' => 0,
				'right' => 0,
				'bottom' => 0,
				'left' => 0,
				'unit' => 'px'
			),
			'size_units' => Array( 'px', 'em' ),
			'selectors' => Array(
				'{{WRAPPER}} .menu-depth-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );



		$this->add_responsive_control( 'submenu_text_padding', Array(
			'label' => __( 'Sub Menu Text Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'default' => Array(
				'top' => 15,
				'right' => 15,
				'bottom' => 15,
				'left' => 15,
				'unit' => 'px'
			),
			'size_units' => Array( 'px', 'em' ),
			'selectors' => Array(
				'{{WRAPPER}} .sub-menu-second .sub-menu-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .adminbar-wrap.sub-menu'=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'submenu_border_style',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} li.menu-item-depth-0:not(.wide-container) .sub-menu-second, {{WRAPPER}} .wide-container .menu-depth-1',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submenu_box_shadow',
				'selector' => '{{WRAPPER}} .jvbpd-nav > li.main-menu-item > ul.sub-menu-second',
			]
		);
		$this->end_controls_section();


		$this->start_controls_section(
			'section_wide_menu_setting',
			[
				'label' => __( 'Wide Menu', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


  $this->start_controls_tabs( 'tabs_wide_submenu_item_style' );

		$this->start_controls_tab(
			'tab_width_submenu_item_normal',
			[
				'label' => __( 'normal', 'jvfrmtd' ),
			]
		);



		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'top_submenu_typography',
				'label'	=> __('Top menu Typography', 'jvfrmtd'),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .wide-container .menu-item-depth-1 > a > .menu-titles',
			]
		);


	    $this->add_control(
			'top_submenu_text_color',
			  [
				  'label' => __( 'Top Menu Color', 'jvfrmtd' ),
				  'type' => Controls_Manager::COLOR,
				  'default' => '#a9a9a9',
				  'scheme' => [
					  'type' => Scheme_Color::get_type(),
					  'value' => Scheme_Color::COLOR_1,
				  ],
				  'separator' => 'after',
				  'selectors' => [
					  '{{WRAPPER}} .wide-container .menu-item-depth-1 > a > .menu-titles' => 'color: {{VALUE}}', //wide top menu title
					  '{{WRAPPER}} .jvbpd-nav>li.jvbpd-my_menu-nav ul.sub-menu-second li.user-info-item a span' => 'color: {{VALUE}}', //sub top menu ( right )
				  ],
			  ]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_wide_submenu_item_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);




			$this->add_group_control( Group_Control_Typography::get_type(), Array(
					'label'	=> __('Top Sub Menu', 'jvfrmtd'),
					'name' => 'top_submenu_typography_hover',
					'selector' =>'{{WRAPPER}} .wide-container .menu-item-depth-1 > a:hover > .menu-titles',
   					   // '{{WRAPPER}} .jvbpd-nav>li.jvbpd-my_menu-nav ul.sub-menu-second li.user-info-item a:hover span', //sub top menu ( right )

					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			) );


			  $this->add_control(
				'top_submenu_text_color_hover',
				  [
					  'label' => __( 'Top Sub Menu Color', 'jvfrmtd' ),
					  'type' => Controls_Manager::COLOR,
					  'default' => '#a9a9a9',
					  'scheme' => [
						  'type' => Scheme_Color::get_type(),
						  'value' => Scheme_Color::COLOR_1,
					  ],
					  'separator' => 'after',

					  'selectors' => [
						  '{{WRAPPER}} .wide-container .menu-item-depth-1 > a:hover > .menu-titles' => 'color: {{VALUE}}',
						  '{{WRAPPER}} .jvbpd-nav>li.jvbpd-my_menu-nav ul.sub-menu-second li.user-info-item a:hover span' => 'color: {{VALUE}}', //sub top menu ( right )
					  ],
				  ]
			  );


		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'submenu_top_menu_gap',Array(
			'label' => esc_html__( 'Sub menu Top & Sub Gap', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 5,
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
				'{{WRAPPER}} .wide-container li.menu-item-depth-2:first-child' => 'margin-top: {{SIZE}}{{UNIT}};',
			],
		) );

		$this->add_responsive_control( 'top_submenu_text_padding', Array(
			'label' => __( 'Top Sub Menu Text Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'default' => Array(
				'top' => 15,
				'right' => 15,
				'bottom' => 15,
				'left' => 15,
				'unit' => 'px'
			),
			'size_units' => Array( 'px', 'em' ),
			'selectors' => Array(
				'{{WRAPPER}} .wide-container .sub-menu-second .menu-item-depth-1 > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );


		$this->add_responsive_control( 'wide_submenu_each_padding', Array(
			'label' => __( 'Each Sub Wrap Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'default' => Array(
				'top' => 0,
				'right' => 0,
				'bottom' => 0,
				'left' => 0,
				'unit' => 'px'
			),
			'size_units' => Array( 'px', 'em' ),
			'selectors' => Array(
				'{{WRAPPER}} .wide-container .menu-item-depth-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );


		/*
		$this->add_responsive_control( 'wide_submenu_text_padding', Array(
			'label' => __( 'Sub Menu Text Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'default' => Array(
				'top' => 15,
				'right' => 15,
				'bottom' => 15,
				'left' => 15,
				'unit' => 'px'
			),
			'size_units' => Array( 'px', 'em' ),
			'selectors' => Array(
				'{{WRAPPER}} .wide-container .sub-menu-second > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		*/
  $this->end_controls_section();
	}

	public function add_menu_control( $parent=false ) {
		$menus = jvbpd_elements_tools()->getNavLocations();
		foreach( $menus as $menu => $menuName ) {
			if( empty( $menu ) ) {
				continue;
			}
			$menu_items = wp_get_nav_menu_items( $menu );
			foreach( $menu_items as $key => $menu_item ) {
				if( $menu_item->menu_item_parent != 0 ) {
					continue;
				}
				$this->add_group_control( \jvbpd_group_nav_menu::get_type(), Array(
					'name' => sprintf( self::MENU_LV1_PARAM_FORMAT, $menu, $menu_item->ID ),
					'label' => isset( $menu_item->title ) ? $menu_item->title : $menu_item->post_title,
					'fields' => Array( 'mega_menu', 'mega_menu_width', 'mega_menu_columns_width', 'mega_menu_left', 'submenu', 'background', /*'block',*/ 'custom_menu' ),
					'params' => Array( 'menu_item' => $menu_item ),
					'condition' => Array( $parent => $menu ),
				) );
			}
		}
	}

    protected function render() {
		$this->_register_hooks();

		//$menu_display_style = $this -> get_settings('menu_display_style');
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrap', 'class', 'jvbpd-nav-menu-widget' );
		if( '' != $this->get_settings( 'mobile_breakpoint' ) ) {
			$this->add_render_attribute( 'wrap', 'class', 'device-' . $this->get_settings( 'mobile_breakpoint' ) );
		}

		$menu_display_style = ($this -> get_settings('menu_display_style')=='vertical') ? "flex-column" : "horizon";
		$verticalMenuOptionValue = 'nav jvbpd-nav jvbpd-nav-menu ' . $menu_display_style;

		$mobileMenuTopPosition = $this->get_settings( 'mobile_collapse_position' );
		$mobileMenuTopPosition = sprintf( '%spx', intVal( $mobileMenuTopPosition[ 'size' ] ) ); ?>

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<a href="#" class="mobile-opener" style="color:#000;" data-toggle="collapse" data-target="#jvbpd-nav-menu-id-<?php echo $this->get_id(); ?>">
				<?php
				if(
					isset($settings['__fa4_migrated']['_mobile_opener_icon']) ||
					(empty($settings['mobile_opener_icon']) && Icons_Manager::is_migration_allowed())
				) {
					Icons_Manager::render_icon( $settings['_mobile_opener_icon'], Array('aria-hidden' => 'true') );
				}else{
					printf('<i %s></i>', esc_attr( $settings['mobile_opener_icon']));
				}
				/* <i class="<?php echo $this->get_settings( 'mobile_opener_icon' ); ?>"></i> */
				?>
			</a>
			<div class="menu-wrap" id="jvbpd-nav-menu-id-<?php echo $this->get_id(); ?>" data-position-top="<?php echo esc_attr( $mobileMenuTopPosition ); ?>" data-mobile-bg="<?php echo esc_attr($this->get_settings('mobile_bg'));?>">
					<?php
					wp_nav_menu( array(
						'menu'				=> $this->get_settings( 'nav_menu' ),
						'theme_location'	=> $this->get_settings( 'nav_menu' ),
						'menu_id'			=> '',
						'menu_class'		=> $verticalMenuOptionValue,/*'nav jvbpd-nav jvbpd-nav-menu'*/
						'echo'				=> true,
						'depth'				=> 3,
						'fallback_cb'		=> '\jvnavwalker::fallback',
						'walker'			=> new \jvnavwalker()
					) ); ?>
			</div>
		</div>
		<?php
	}

	public function getNavSetting( $menu=false, $item_id=0, $key='' ) {
		if( $menu instanceof \WP_Term ) {
				$menu = $menu->slug;
		}
		$menu_key = sprintf( self::MENU_LV1_PARAM_FORMAT . '_%3$s', $menu, $item_id, $key );
		return $this->get_settings( $menu_key );
	}

	public function _register_hooks() {
		add_filter( 'nav_menu_css_class', array( $this, 'walker_start_el_class' ), 10, 3 );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'walker_start_el_output' ), 10, 4 );

		foreach( array( 'add-new-button', 'my-menu', 'my-notifications' ) as $jvbpd_menu ) {
			/*
			add_filter( 'jvbpd_menu/' . $jvbpd_menu . '/label_icon', array( $this, 'menu_custom_label_icon' ), 10, 2 );
			add_filter( 'jvbpd_menu/' . $jvbpd_menu . '/label', array( $this, 'menu_custom_label' ), 10, 2 );
			add_filter( 'jvbpd_menu/' . $jvbpd_menu . '/dropdown_icon', array( $this, 'menu_custom_dropdown_icon' ), 10, 2 ); **/
		}
	}

	public function walker_start_el_class( $classes=Array(), $item, $args=Array() ) {
		if( 'yes' == $this->getNavSetting( $args->menu, $item->ID, 'mega_menu_enable' ) ) {
			$classes[] = 'wide-container';
		}
		return $classes;
	}

	public function walker_start_el_output( $item_output, $item, $depth=0, $args=Array() ) {
		$this->current_item_id = $item->ID;
		remove_filter( 'jvbpd/front/walker/start_lvl/output', array( $this, 'walker_start_lv_output' ), 10, 4 );
		if( 'yes' == $this->getNavSetting( $args->menu, $this->current_item_id, 'mega_menu_enable' ) ) {
			if( 'yes' == $this->getNavSetting( $args->menu, $this->current_item_id, 'block_enable' ) ) {
				$item_output .= $this->walker_start_lv_output( '', $depth, $args, Array( 'nav', 'sub-menu-second', 'menu-depth-' . ( ++$depth ) ) );
			}else{
				add_filter( 'jvbpd/front/walker/start_lvl/output', array( $this, 'walker_start_lv_output' ), 10, 4 );
			}
		}
		return $item_output;
	}

	public function parseNavStyles( $menu, $item_id=0 ) {
		$output = Array();

		$attributes = Array(
			'background-color' => $this->getNavSetting( $menu, $item_id, 'background_color' ),
			'background-image' => $this->getNavSetting( $menu, $item_id, 'background_image' ),
			'background-position' => 'right bottom',
			'background-repeat' => 'no-repeat',
		);

		foreach( $attributes as $property => $value ) {
			if( 'background-image' == $property ) {
				if( isset( $value[ 'id' ] ) ) {
					$value = sprintf( 'url(%s)', wp_get_attachment_image_url( $value[ 'id' ], 'large' ) );
				}else{
					$value = false;
				}
			}
			if( empty( $value ) ) {
				continue;
			}
			$output[] = sprintf( '%1$s:%2$s;', $property, $value );
		}
		return join( ' ', $output );
	}

	public function getBlock( $menu, $item_id=0 ) {
		if( ! function_exists( 'jvbpd_layout' ) ) {
			return false;
		}
		ob_start();
		jvbpd_layout()->load_template(
			'parts/part-menu-wide-category',
			array(
				'jvbpd_menu_args' => (object) Array(
					'term_id' => intVal( 0 ),
				),
			)
		);
		return ob_get_clean();
	}

	public function walker_start_lv_output( $render, $depth=0, $args, $classes=Array() ) {
		$menuID = $this->current_item_id;
		$classes[] = 'wide-nav-overlay';
		$toClass = join( ' ', array_filter( $classes ) );

		if( 'yes' == $this->getNavSetting( $args->menu, $menuID, 'block_enable' ) ) {
			$output_format = '<ul class="%1$s" style="%2$s"><li>%3$s</li></ul>';
		}else{
			$output_format = '<ul class="%1$s" style="%2$s">';
		}
		return sprintf(
			$output_format,
			$toClass,
			$this->parseNavStyles( $args->menu, $menuID ),
			$this->getBlock( $args->menu, $menuID )
		);
	}

	public function menu_custom_label_icon( $def='', $item ) {
		return 'fa-pencil';
	}

	public function menu_custom_label( $def='', $item ) {
		return 'Test';
	}

	public function menu_custom_dropdown_icon( $def='', $item ) {
		return 'fa-bookmark';
	}
}

/*
if( function_exists( '\jvbpdelement\Widgets\jvbpd_nav_menu_autoload' ) ) {
  add_action( 'elementor/widget/before_render_content', '\jvbpdelement\Widgets\jvbpd_nav_menu_autoload' );
  function jvbpd_nav_menu_autoload( $el ) {
      $menuInstance = new jvbpd_nav_menu;
      if( $el->get_name() == $menuInstance->get_name() ) {
          $menuInstance->_register_hooks();
      }
  }
} */