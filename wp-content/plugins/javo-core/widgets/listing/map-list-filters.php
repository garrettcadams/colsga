<?php
/**
 * Widget Name: Map list filter
 * Author: Javo
 * Version: 1.0.0.6
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_map_list_filters extends Widget_Base {

	public function get_name() { return 'jvbpd-map-list-filters'; }
	public function get_title() { return 'Filter Buttons ( List Type)'; }
	public function get_icon() { return 'fa fa-list-ul'; }
	public function get_categories() { return [ 'jvbpd-map-page' ]; }

    protected function _register_controls() {

		add_filter( 'jvbpd_widget_map_list_filter_fields', array( $this, 'addAcfField' ) );

        $this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'List Type Filters (Group)', 'jvfrmtd' ),
		) );

		$this->add_control( 'Des', array(
			'type' => Controls_Manager::RAW_HTML,
			'raw'  => sprintf(
				'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
				'<li class="doc-link">'.
				esc_html__('How to use this widget.','jvfrmtd').
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-filter-buttons-for-map//" style="color:#fff;"> ' .
				esc_html__( 'Documentation', 'jvfrmtd' ) .
				'</a></li><li>&nbsp;</li>'.
				'<li class="notice">'.
				esc_html__('This widget is for only list page.', 'jvfrmtd').
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;"> ' .
				esc_html__( 'Detail', 'jvfrmtd' ) .
				'</a><br/></li><li>&nbsp;</li><li>'.
				esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
				'</li></ul></div>'
			)
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_content', Array(
			'label' => esc_html__( 'Filter Setting', 'jvfrmtd' ),   //section name for controler view
		) );

		$repeaterInstance = new \Elementor\Repeater();

		$repeaterInstance->add_group_control(
			\jvbpd_group_block_style::get_type(),
			Array(
				'label' => esc_html__( "Block Settings", 'jvfrmtd' ),
				'name' => 'block',
				'condition' => Array( 'callback' => 'featured_listing' ),
				'fields' => Array( 'module', 'columns' ),
			)
		);

		$this->add_control( 'layout_arrange', Array(
			'label'       => __( 'Layout Style', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'vertical',
			'options' => Array(
				'vertical'  => __( 'Vertical', 'jvfrmtd' ),
				'horizon' => __( 'Horizon', 'jvfrmtd' ),
			),
		) );

		$this->add_control( 'horizon-filter-width', [
			'label' => __( 'Min Width', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 240,
				'unit' => 'px',
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 1000,
					'step' => 1,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .panel ' => 'min-width: {{SIZE}}{{UNIT}};',
			],
			'condition'	=>[
				'layout_arrange'	=> 'horizon',
			],
		] );

		$this->add_control( 'filters', Array(
			'label' => __( 'Menu Filters', 'jvfrmtd' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => wp_parse_args(
				Array_Values( $repeaterInstance->get_controls() ),
				apply_filters( 'jvbpd_widget_map_list_filter_fields', Array(
					Array(
						'name' => 'filter_title',
						'label' => esc_html__( "Filter Title", 'jvfrmtd' ),
						'type' => Controls_Manager::TEXT,
						'default' => '',
						'description'=> esc_html__("It shows only for vertical layout", 'jvfrmtd'),
					),
                    Array(
                        'name' => 'hide_title',
                        'label' => esc_html__( "Hide Title", 'jvfrmtd' ),
                        'type' => Controls_Manager::SWITCHER,
					),
					Array(
                        'name' => 'extend',
                        'label' => esc_html__( "Extend", 'jvfrmtd' ),
						'type' => Controls_Manager::SWITCHER,
						'default' => 'yes',
                    ),
					Array(
						'name' => 'callback',
						'label' => __( 'Filter', 'jvfrmtd' ),
						'type' => Controls_Manager::SELECT,
						'options' => Array(
							'' => esc_html__( 'Select a filter', 'jvfrmtd' ),
							'address' => __( 'Near me(By Address)', 'jvfrmtd' ),
							'category' => __( 'Category', 'jvfrmtd' ),
							'location' => __( 'Location', 'jvfrmtd' ),
							'amenities' => __( 'Amenities', 'jvfrmtd' ),
							'keyword' => __( 'Keyword', 'jvfrmtd' ),
							'more_taxonomy' => __( 'More Taxonomies', 'jvfrmtd' ),
							'price' => __( 'Price', 'jvfrmtd' ),
							// 'featured_listing' => __( 'Featured Listings', 'jvfrmtd' ),
							'image' => __( 'Banner / Image', 'jvfrmtd' ),
							// 'acf_field' => esc_html__( "ACF Field", 'jvfrmtd' ),
						),
					),
					Array(
						'name' => 'condition',
						'label' => __( 'Filter Condition', 'jvfrmtd' ),
						'type' => Controls_Manager::SELECT,
						'options' => Array(
							'or' => esc_html__( 'OR', 'jvfrmtd' ),
							'and' => __( 'And', 'jvfrmtd' ),
						),
						'default' => 'or',
						'frontend_available' => true,
						'condition' => Array(
							'callback' => Array('category', 'location', 'amenities', 'keyword', 'more_taxonomy'),
						),
					),
					Array(
						'name' => 'acf_key',
						'label' => __( 'Filter', 'jvfrmtd' ),
						'type' => Controls_Manager::SELECT,
						'condition' => Array( 'callback' => 'acf_field' ),
						'options' => wp_parse_args( jvbpd_elements_tools()->getStaticACFieldMeta(), Array( '' => esc_html( 'Select a field', 'jvfrmtd' ) ) ),
					),
					Array(
						'name' => 'address_distance_default',
						'label' => __( 'Default distance slider value', 'jvfrmtd' ),
						'type' => Controls_Manager::NUMBER,
						'default' => '25',
						'condition' => Array(
							'callback' => 'address',
						),
					),
					Array(
						'name' => 'address_distance_max',
						'label' => __( 'Maximum distance slider', 'jvfrmtd' ),
						'type' => Controls_Manager::NUMBER,
						'default' => '50',
						'condition' => Array(
							'callback' => 'address',
						),
					),
					Array(
						'name' => 'address_distance_unit',
						'label' => __( 'Distance slider Unit', 'jvfrmtd' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'km',
						'condition' => Array(
							'callback' => 'address',
						),
						'options' => Array(
							'km' => esc_html__( "KM", 'jvfrmtd' ),
							'mile' => esc_html__( "Mile", 'jvfrmtd' ),
						),
					),
					Array(
						'name' => 'filter_type',
						'label' => esc_html__( "Filter Type", 'jvfrmtd' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'select',
						'options' => Array(
							'select' => esc_html__( "Dropdown", 'jvfrmtd' ),
							'checkbox' => esc_html__( "Check Box", 'jvfrmtd' ),
						),
						'condition' => Array(
							'callback' => Array( 'more_taxonomy', 'category', 'location', 'amenities', 'keyword' ),
						),
					),
					Array(
						'name' => 'filter_dropdown_remove_button',
						'label' => esc_html__( "Remove button in dropdown filter", 'jvfrmtd' ),
						'type' => Controls_Manager::SWITCHER,
						'default' => 'enable',
						'return_value' => 'enable',
						'condition' => Array(
							'filter_type' => Array( 'select' ),
							'callback' => Array( 'more_taxonomy', 'category', 'location', 'amenities', 'keyword' ),
						),
					),
					Array(
						'name' => 'filter_dropdown_limit_select',
						'label' => esc_html__( "Limit dropdown select items", 'jvfrmtd' ),
						'type' => Controls_Manager::NUMBER,
						'default' => '1',
						'condition' => Array(
							'filter_type' => Array( 'select' ),
							'callback' => Array( 'more_taxonomy', 'category', 'location', 'amenities', 'keyword' ),
						),
					),
					Array(
						'name' => 'filter_show_count',
						'label' => esc_html__( "Show Count", 'jvfrmtd' ),
						'type' => Controls_Manager::SWITCHER,
						'condition' => Array(
							'callback' => Array( 'more_taxonomy', 'category', 'location', 'amenities', 'keyword' ),
						),
					),
					Array(
						'name'	=> 'checkbox-height',
						'label' => __( "Check box max height", "jvfrmtd" ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 200,
							'unit' => 'px',
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1000,
								'step' => 1,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'size_units' => [ 'px', '%' ],
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}}.ui-checkbox .panel-body' => 'max-height: {{SIZE}}{{UNIT}}; overflow-y: auto; overflow-x: hidden;',
						],
						'condition' => [
							'filter_type' => 'checkbox',
							'callback' => Array( 'more_taxonomy', 'category', 'location', 'amenities', 'keyword' ),
						]
					),
					Array(
						'name' => 'filter_placeholder',
						'label' => esc_html__( "Placeholder", 'jvfrmtd' ),
						'type' => Controls_Manager::TEXT,
						'condition' => Array(
							'filter_type' => Array( 'select' ),
							'callback' => Array( 'more_taxonomy', 'category', 'location', 'amenities', 'keyword' ),
						),
					),
					Array(
						'name' => 'more_taxonomy',
						'label' => esc_html__( "More Taxonomy", 'jvfrmtd' ),
						'type' => Controls_Manager::SELECT,
						'default' => '',
						'options' => $this->getMoreTaxonomies(),
						'condition' => Array(
							'callback' => 'more_taxonomy',
						),
					),
					Array(
						'name' => 'image_image',
						'label' => __( 'Choose Image', 'jvfrmtd' ),
						'type' => Controls_Manager::MEDIA,
						'label_block' => true,
						'default' => Array(
							'url' => Utils::get_placeholder_image_src(),
						),
						'condition' => Array(
							'callback' => 'image',
						),
					),
					Array(
						'name' => 'image_link',
						'label' => __( 'Banner URL', 'jvfrmtd' ),
						'type' => Controls_Manager::URL,
						'default' => Array(
							'url' => 'http://',
							'is_external' => '',
						),
						'show_external' => true, // Show the 'open in new tab' button.
						'condition' => Array(
							'callback' => 'image',
						),
					),
					Array(
						'name' => 'price_min',
						'label' => __( 'Price Min', 'jvfrmtd' ),
						'type' => Controls_Manager::NUMBER,
						'default' => 0,
						'condition' => Array(
							'callback' => 'price',
						),
					),
					Array(
						'name' => 'price_max',
						'label' => __( 'Price Max', 'jvfrmtd' ),
						'type' => Controls_Manager::NUMBER,
						'default' => 1000,
						'condition' => Array(
							'callback' => 'price',
						),
					),
					Array(
						'name' => 'price_unit',
						'label' => __( 'Currency Unit', 'jvfrmtd' ),
						'type' => Controls_Manager::TEXT,
						'default' => '$',
						'condition' => Array(
							'callback' => 'price',
						),
					),

				) )
			),
			'title_field' => 'Button : {{{ filter_title }}}',
		) );

	$this->end_controls_section();


	/** Filter Panel Style */
    $this->start_controls_section(
			'filter_panel',
			[
				'label' => __( 'Filter Panel','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

    $this->add_control(
    'panel_space',
        [
            'label' => __( 'Panel Bottom Space', 'jvfrmtd' ),
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
                '{{WRAPPER}} .panel' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $this->add_control(
			'filter_panel_padding',
			[
				'label' => __( 'Padding','jvfrmtd'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_border',
				'selector' => '{{WRAPPER}} .panel',
			]
		);

		$this->add_control(
			'filter_border_radius',
			[
				'label' => __( 'Border Radius','jvfrmtd'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_box_shadow',
				'selector' => '{{WRAPPER}} .panel',
			]
		);
		$this->end_controls_section();

        /* Filter Title */
        $this->start_controls_section(
          'filter_title',
          [
            'label' => __( 'Filter Title','jvfrmtd'),
            'tab' => Controls_Manager::TAB_STYLE,
          ]
        );

        $this->add_control(
                'filter_panel_heading_padding',
                [
                    'label' => __( 'Padding','jvfrmtd'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'default' =>[
                    ],
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .panel-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->add_control(
        'title_color',
            [
                'label' => __( 'Title Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#454545',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .panel-heading h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control( Group_Control_Typography::get_type(), [
          'name' => 'title_typography',
          'selector' => '{{WRAPPER}} .panel-heading h3',
          'scheme' => Scheme_Typography::TYPOGRAPHY_1,
        ] );

         $this->add_control(
        'opener_color',
            [
                'label' => __( 'Opener Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#cccccc',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jvbpd_map_list_sidebar_wrap .panel-heading::after' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();




        /* Filter items Style */
        $this->start_controls_section(
          'filter_options',
          [
            'label' => __( 'Filter Items ( General )','jvfrmtd'),
            'tab' => Controls_Manager::TAB_STYLE,
          ]
        );

        $this->add_control(
                'filter_panel_body_padding',
                [
                    'label' => __( 'Padding','jvfrmtd'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .panel-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->add_control(
            'body_bg_color',
                [
                    'label' => __( 'Bacground Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => 'rgba(255,255,255,0)',
                    'scheme' => [
                        'type' => Scheme_Color::get_type(),
                        'value' => Scheme_Color::COLOR_1,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .panel-body' => 'background-color: {{VALUE}}',
                    ],
                ]
        );

        $this->end_controls_section();




        /* Filter items Style */
        $this->start_controls_section(
            'filter_options_dropdown',
            [
                'label' => __( 'Filter Items ( Dropdown )','jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_dropdown_styles' );
        $this->start_controls_tab( 'dropdown_input',['label' => __( 'Input', 'jvfrmtd' ),]);
            $this->add_control(
                'dropdown_input_color',
                [
                    'label' => __( 'Selected Text Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#cccccc',
                    'scheme' => [
                        'type' => Scheme_Color::get_type(),
                        'value' => Scheme_Color::COLOR_1,
                    ],
                    'selectors' => [
						'{{WRAPPER}} .selectize-input .item' => 'color: {{VALUE}}',
						'{{WRAPPER}} .selectize-input.items input::placeholder' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control( Group_Control_Typography::get_type(), [
                'name' => 'dropdown_input_typography',
                'selector' => '{{WRAPPER}} .selectize-input .item,{{WRAPPER}} .selectize-input.items',
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            ] );
            $this->add_control(
                'dropdown_input_padding',
                [
                    'label' => __( 'Input Padding','jvfrmtd'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .selectize-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'dropdown_input_border',
                    'label' => __( 'Select border', 'jvfrmtd' ),
                    'selector' => '{{WRAPPER}} .selectize-input.items',
                ]
            );

        $this->end_controls_tab();
        $this->start_controls_tab( 'dropdown_dropdown',['label' => __( 'Dropdown', 'jvfrmtd' ),]);
            $this->add_control(
                'dropdown_text_color',
                [
                    'label' => __( 'Text Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#cccccc',
                    'scheme' => [
                        'type' => Scheme_Color::get_type(),
                        'value' => Scheme_Color::COLOR_1,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .selectize-dropdown-content .option' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control( Group_Control_Typography::get_type(), [
                'name' => 'dropdown_typography',
                'selector' => '{{WRAPPER}} .selectize-dropdown-content .option',
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
            ] );
            $this->add_control(
                'dropdown_padding',
                [
                    'label' => __( 'Dropdown Items Padding','jvfrmtd'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .selectize-dropdown-content [data-selectable]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
			);

			$this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'dropdown_border',
                    'label' => __( 'Select border', 'jvfrmtd' ),
                    'selector' => '{{WRAPPER}} .selectize-dropdown.multi',
                ]
            );
        $this->end_controls_tab();
        $this->end_controls_tabs();



        $this->end_controls_section();

		//check box style
        $this->start_controls_section(
            'checkbox_style',
            [
                'label' => __( 'Filter Items (Check Box)','jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'check_txt_color',
            [
                'label' => __( 'Title Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#cccccc',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .panel-body .form-check .chk-wrap' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'check_txt_typography',
            'selector' => '{{WRAPPER}} .panel-body .form-check .chk-wrap',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
        ] );

        $this->start_controls_tabs( 'tabs_submenu_item_style' );
        $this->start_controls_tab(
            'checkbox_nomal',
            [
                'label' => __( 'Normal', 'jvfrmtd' ),
            ]
        );

        $this->add_control(
            'nomal_checkbox_color',
            [
                'label' => __( 'CheckBox Background Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#eee',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .chk-wrap .checkmark' => 'background-color: {{VALUE}}',
                ],
            ]
		);

		/************************** Checkbox */
			/** Check mark */
			$this->add_control(
				'checkbox_size',
					[
						'label' => __( 'Checkbox Size', 'jvfrmtd' ),
						'type' => Controls_Manager::SLIDER,
						'default' => [
							'size' => 20,
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 30,
							],
						],
						'size_units' => [ 'px' ],
						'separator' => 'before',
						'selectors' => [
							'{{WRAPPER}} .chk-wrap .checkmark' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'checkbox_top',
						[
							'label' => __( 'Checkbox Top Position', 'jvfrmtd' ),
							'type' => Controls_Manager::SLIDER,
							'default' => [
								'size' => 0,
							],
							'range' => [
								'px' => [
									'min' => 1,
									'max' => 30,
								],
							],
							'size_units' => [ 'px' ],
							'separator' => 'after',
							'selectors' => [
								'{{WRAPPER}} .chk-wrap .checkmark' => 'top: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'checked_top',
							[
								'label' => __( 'Check Mark Top', 'jvfrmtd' ),
								'type' => Controls_Manager::SLIDER,
								'default' => [
									'size' => 8,
								],
								'range' => [
									'px' => [
										'min' => -15,
										'max' => 15,
									],
								],
								'size_units' => [ 'px' ],
								'selectors' => [
									'{{WRAPPER}} .chk-wrap .checkmark:after' => 'top: {{SIZE}}{{UNIT}};',
								],
							]
						);

					$this->add_control(
						'checked_left',
							[
								'label' => __( 'Check Mark Left', 'jvfrmtd' ),
								'type' => Controls_Manager::SLIDER,
								'default' => [
									'size' => 8,
								],
								'range' => [
									'px' => [
										'min' => -15,
										'max' => 15,
									],
								],
								'size_units' => [ 'px' ],
								'selectors' => [
									'{{WRAPPER}} .chk-wrap .checkmark:after' => 'left: {{SIZE}}{{UNIT}};',
								],
							]
						);



				$this->add_control(
					'checked_width',
						[
							'label' => __( 'Check Width', 'jvfrmtd' ),
							'type' => Controls_Manager::SLIDER,
							'default' => [
								'size' => 8,
							],
							'range' => [
								'px' => [
									'min' => 1,
									'max' => 15,
								],
							],
							'size_units' => [ 'px' ],
							'selectors' => [
								'{{WRAPPER}} .chk-wrap .checkmark:after' => 'width: {{SIZE}}{{UNIT}};',
							],
						]
					);

				$this->add_control(
					'checked_height',
						[
							'label' => __( 'Check Height', 'jvfrmtd' ),
							'type' => Controls_Manager::SLIDER,
							'default' => [
								'size' => 8,
							],
							'range' => [
								'px' => [
									'min' => 1,
									'max' => 15,
								],
							],
							'size_units' => [ 'px' ],
							'separator' => 'after',
							'selectors' => [
								'{{WRAPPER}} .chk-wrap .checkmark:after' => 'height: {{SIZE}}{{UNIT}};',
							],
						]
					);

				/** //Check mark */

				/** Checkbox border, radius */

				$this->add_control( 'checkbox_radius', [
					'label' => __( 'Checkbox Radius', 'jvfrmtd' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
							'{{WRAPPER}} .chk-wrap .checkmark' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'checkbox_border',
						'label' => __('Checkbox Border','jvfrmtd'),
						'separator' => 'after',
						'selector' => '{{WRAPPER}} .chk-wrap .checkmark',
					]
				);
			/** //Checkbox border, radius */



			/************************** // Checkbox */

        $this->end_controls_tab(); // btn normal

        $this->start_controls_tab(
            'checkbox_hover',
            [
                'label' => __( 'Hover', 'jvfrmtd' ),
            ]
        );

        $this->add_control(
            'hover_checkbox_color',
            [
                'label' => __( 'CheckBox Background Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#cccccc',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .chk-wrap:hover input ~ .checkmark' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab(); // btn hover


        $this->start_controls_tab(
            'checkbox_checked',
            [
                'label' => __( 'Checked', 'jvfrmtd' ),
            ]
        );

        $this->add_control(
            'checked_checkbox_color',
            [
                'label' => __( 'Checkbox Background Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#454545',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .chk-wrap input:checked ~ .checkmark' => 'background-color: {{VALUE}}',
                ],
            ]
        );


        $this->add_control(
            'checked_marker_color',
            [
                'label' => __( 'Check Marker Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .chk-wrap .checkmark:after' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab(); // btn focus
        $this->end_controls_tabs();

        $this->end_controls_section();


    /* Slider Style */
    $this->start_controls_section(
      'slider_options',
      [
        'label' => __( 'Filter Items (Slider Style)','jvfrmtd'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control( 'slider_background_color', [
		'label' => __( 'Slider Color', 'jvfrmtd' ),
		'type' => Controls_Manager::COLOR,
		'default' => '#aaaaaa',
		'scheme' => [
			'type' => Scheme_Color::get_type(),
			'value' => Scheme_Color::COLOR_1,
		],
		'selectors' => [
			'{{WRAPPER}} .jvbpd-ui-slider .noUi-background' => 'background-color: {{VALUE}}',
			//'{{WRAPPER}} .jvbpd-map-distance-bar-wrap .noUi-base' => 'background-color: {{VALUE}}',
			'{{WRAPPER}} .javo-noUISlider .noUi-background' => 'background-color: {{VALUE}}',
		],
	]);

	$this->add_control( 'slider_connect_background_color', [
		'label' => __( 'Slider Connect Background Color', 'jvfrmtd' ),
		'type' => Controls_Manager::COLOR,
		'default' => '#aaaaaa',
		'scheme' => [
			'type' => Scheme_Color::get_type(),
			'value' => Scheme_Color::COLOR_1,
		],
		'selectors' => [
			'{{WRAPPER}} .jvbpd-ui-slider .noUi-connect' => 'background-color: {{VALUE}}',
			'{{WRAPPER}} .javo-noUISlider.noUi-connect' => 'background-color: {{VALUE}}',
		],
	]);

	$this->add_control( 'slider_range_text_color', Array(
		'label' => esc_html__( 'Slider Range Text Color', 'jvfrmtd' ),
		'type' => Controls_Manager::COLOR,
		'default' => '#2185bd',
		'scheme' => Array(
			'type' => Scheme_Color::get_type(),
			'value' => Scheme_Color::COLOR_1,
		),
		'selectors' => Array(
			'{{WRAPPER}} .jvbpd-ui-slider .tooltips' => 'color: {{VALUE}}',
			'{{WRAPPER}} .jvbpd-map-distance-bar-wrap .tooltips' => 'color: {{VALUE}}',
		),
	));

    $this->add_control(
    'slider_Handle_color',
        [
            'label' => __( 'Slider Handle Color', 'jvfrmtd' ),
            'type' => Controls_Manager::COLOR,
			'default' => '#ffffff',
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .jvbpd-ui-slider .noUi-base .noUi-origin .noUi-handle' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .jvbpd-map-distance-bar-wrap .noUi-base .noUi-origin .noUi-handle' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
		'slider_padding',
		[
			'label' => __( 'Padding','jvfrmtd'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .jvbpd-ui-slider' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .jvbpd-map-distance-bar-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]
	);

    $this->add_control(
		'slider_margin',
		[
			'label' => __( 'Margin','jvfrmtd'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .jvbpd-ui-slider' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .jvbpd-map-distance-bar-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]
	);

	$this->add_group_control(
		Group_Control_Border::get_type(),
		[
			'name' => 'slider_handle_border',
			'selector' => '{{WRAPPER}} .jvbpd_map_list_sidebar_wrap .javo-noUISlider .noUi-handle, {{WRAPPER}} .jvbpd-ui-slider .noUi-handle',
		]
	);

	$this->add_group_control(
		Group_Control_Box_Shadow::get_type(),
		[
			'name' => 'slider_handle_shadow',
			'selector' => '{{WRAPPER}} .jvbpd_map_list_sidebar_wrap .javo-noUISlider .noUi-handle',
		]
	);

	$this->add_control(
    'slider_bar_space',
        [
            'label' => __( 'Slider Bar space', 'jvfrmtd' ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 10,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 40,
                ],
            ],
            'size_units' => [ 'px' ],
            'selectors' => [
                '{{WRAPPER}} .slider.noUi-target.noUi-ltr.noUi-horizontal.noUi-background' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .jvbpd-map-distance-bar-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .jvbpd-ui-slider' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

	$this->add_control(
    'slider_bar_height',
        [
            'label' => __( 'Slider Bar Height', 'jvfrmtd' ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 8,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 15,
                ],
            ],
            'size_units' => [ 'px' ],
            'selectors' => [
				'{{WRAPPER}} .panel-collapse.collapse.show .panel-body .noUi-horizontal' => 'height: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .panel-collapse.collapse.show .jvbpd-ui-slider .slider' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

	$this->add_control(
    'slider_handle_size',
        [
            'label' => __( 'Slider Handle Size', 'jvfrmtd' ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 20,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 40,
                ],
            ],
            'size_units' => [ 'px' ],
            'selectors' => [
                '{{WRAPPER}} .jvbpd-ui-slider .noUi-base .noUi-origin .noUi-handle' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .jvbpd-map-distance-bar-wrap .noUi-base .noUi-origin .noUi-handle' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

	$this->add_control('handle_radius', Array(
		'label' => __( 'Handle Radius', 'jvfrmtd' ),
		'type' => Controls_Manager::SLIDER,
		'default' => Array(
			'size' => 50,
		),
		'range' => Array(
			'px' => Array(
				'min' => 0,
				'max' => 50,
				'step' => 1,
			),
			'%' => Array(
				'min' => 0,
				'max' => 100,
			),
		),
		'size_units' => Array( 'px', '%' ),
		'selectors' => Array(
			'{{WRAPPER}} .noUi-handle.noUi-handle-lower' => 'border-radius: {{SIZE}}{{UNIT}};',
			'{{WRAPPER}} .noUi-handle.noUi-handle-upper' => 'border-radius: {{SIZE}}{{UNIT}};',
		),
	));

	$this->add_control('handle_border_size', Array(
		'label' => __( 'Handle Border Size', 'jvfrmtd' ),
		'type' => Controls_Manager::SLIDER,
		'default' => Array(
			'size' => 3,
		),
		'range' => Array(
			'px' => Array(
				'min' => 0,
				'max' => 50,
				'step' => 1,
			),
			'%' => Array(
				'min' => 0,
				'max' => 100,
			),
		),
		'size_units' => Array( 'px', '%' ),
		'selectors' => Array(
			'{{WRAPPER}} .noUi-handle' => 'border-style:solid;border-width: {{SIZE}}{{UNIT}};',
		),
	));

    $this->add_control(
    'slider_tooltip_text_color',
        [
            'label' => __( 'Slider Tooltip  Text Color', 'jvfrmtd' ),
            'type' => Controls_Manager::COLOR,
			'default' => '#ffffff',
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
				'{{WRAPPER}} .jvbpd_map_list_sidebar_wrap .jvbpd-map-distance-bar-wrap .javo-slider-tooltip' => 'color: {{VALUE}}',
			],
        ]
    );

    $this->add_control(
    'slider_tooltip_background_color',
        [
            'label' => __( 'Slider Tooltip Background Color', 'jvfrmtd' ),
            'type' => Controls_Manager::COLOR,
			'default' => '#11c0c6',
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
				'{{WRAPPER}} .jvbpd_map_list_sidebar_wrap .jvbpd-map-distance-bar-wrap .javo-slider-tooltip' => 'background-color: {{VALUE}}',
			],
        ]
    );

	$this->end_controls_section();
    }

	public function addAcfField( $fields=Array() ) {
		// $fields = jvbpd_elements_tools()->getACFOptions( $this, Array( 'callback' => 'acf_field' ), $fields );

		$fields[] = Array(
			'name' => 'acf_field_is_slide_filter',
			'label' => esc_html__( "Use slide filter", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'condition' => Array(
				'callback' => 'acf_field',
			),
		);

		$fields[] = Array(
			'name' => 'acf_field_slide_number_unit',
			'label' => esc_html__( "Price unit", 'jvfrmtd' ),
			'type' => Controls_Manager::TEXT,
			'condition' => Array(
				'callback' => 'acf_field',
				'acf_field_is_slide_filter' => 'yes',
			),
		);

		$fields[] = Array(
			'name' => 'acf_field_slide_number_min',
			'label' => esc_html__( "Minimum value", 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'condition' => Array(
				'callback' => 'acf_field',
				'acf_field_is_slide_filter' => 'yes',
			),
		);

		$fields[] = Array(
			'name' => 'acf_field_slide_number_max',
			'label' => esc_html__( "Maximum value", 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'condition' => Array(
				'callback' => 'acf_field',
				'acf_field_is_slide_filter' => 'yes',
			),
		);

		return $fields;
	}

    protected function render() {
		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();
		$isPreviewMode = false;
		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-button.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function getContent( $settings, $obj ) {
		?>
		<div class="jvbpd_map_list_sidebar_wrap <?php echo $settings['layout_arrange']; ?>">
			<?php
			$arrCallBack = $settings[ 'filters' ];
			if( !empty( $arrCallBack ) && is_array( $arrCallBack ) ) {
				foreach( $arrCallBack as $strCallBack ) {
					$this->add_render_attribute('panel-header', Array(
						'class' => Array('panel-heading'),
						'data-toggle' => 'collapse',
					), null, true);
					$this->add_render_attribute('panel-body', 'class', Array( 'panel-collapse', 'collapse'), true);
					if('yes' == $strCallBack['hide_title']) {
						$this->add_render_attribute('panel-header', 'class', 'hidden');
					}
					if('yes' == $strCallBack['extend']) {
						$this->add_render_attribute('panel-body', 'class', 'show');
					}
					if( method_exists( $this, $strCallBack['callback'] ) ) {
						call_user_func( Array( $this, $strCallBack[ 'callback' ] ), $strCallBack );
					}
				}
			}?>
		</div>
		<?php
	}

	public function getPlaceHolder( $fieldSettings=Array(), $default=false ) {
		$placeholder = !empty( $fieldSettings[ 'filter_placeholder' ] ) ? $fieldSettings[ 'filter_placeholder' ] : false;
		if( $placeholder ) {
			$default = $placeholder;
		}
		return $default;
	}

	public function getMoreTaxonomies() {
		$output = Array( '' => esc_html__( "Select a taxonomy", 'jvfrmtd' ) );
		if( function_exists( 'javo_moreTax' ) ) {
			$taxonomies = javo_moreTax()->admin->getMoreTaxonomies();
			if( is_array( $taxonomies ) ) {
				foreach( $taxonomies as $taxonomy ) {
					$output[ $taxonomy[ 'name' ] ] = $taxonomy[ 'label' ];
				}
			}
		}
		return $output;
	}

	public function term_filter($taxonomy, $terms) {
		$output = Array();
		$terms = is_array($terms) ? $terms : Array($terms);
		foreach( $terms as $term ) {
			$target = 'name';
			if(is_numeric($term)){
				$target = 'term_id';
			}
			$term_obj = get_term_by($target, $term, $taxonomy);
			if(!empty($term_obj)){
				$output[] = $term_obj->term_id;
			}
		}
		return $output;
	}

	public function getHeader($settings=Array(), $label='') {
		if(!empty($settings['filter_title'])) {
			$label = $settings['filter_title'];
		}
		printf(
			'<div %1$s><h3 class="panel-title">%2$s</h3><span class="toggle chevron"></span></div>',
			$this->get_render_attribute_string('panel-header'),
			$label
		);
	}

	public function address( $settings=Array() ) {
		$this->add_render_attribute('panel-header', 'data-target', '#filter-address-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-address-' . $settings['_id'], true ); ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
			<?php $this->getHeader($settings, esc_html__( "Address", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php
					$arrJavoOutput = Array();
					$arrJavoOutput[] = "<div class=\"input-group\">";
					$arrJavoOutput[] = sprintf(
						'<input type="text" id="%s" class="%s" value="%s" placeholder="%s">',
						'javo-map-box-location-trigger',
						'address javo-location-search',
						( isset( $_GET[ 'radius_key' ] ) ? $_GET[ 'radius_key' ] : '' ),
						$this->getPlaceHolder( $settings, esc_html__( "Address", 'jvfrmtd' ) )
					);
					$arrJavoOutput[] = "
						<span class=\"input-group-btn\">
							<button class=\"btn my-position-trigger\">
								<i class=\"fa fa-map-marker\"></i>
							</button>
						</span>";
					$arrJavoOutput[] = "</div>";
					$arrJavoOutput[] = sprintf(
						'<div class="jvbpd-map-distance-bar-wrap not-layer tooltip-bottom"><div class="javo-geoloc-slider-trigger javo-noUISlider" data-unit="%1$s" data-current="%2$s" data-max="%3$s"></div>
							<div class="distance tooltips">
								<div class="distance distance-min tooltip-min">0 %1$s</div>
								<div class="distance distance-max tooltip-max">%3$s %1$s</div>
							</div>
						</div>',
						$settings[ 'address_distance_unit' ], $settings[ 'address_distance_default' ], $settings[ 'address_distance_max' ] );
					echo join( false, $arrJavoOutput ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function category( $settings=Array() ) {
		$this->add_render_attribute('panel-header', 'data-target', '#filter-category-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-category-' . $settings['_id'], true ); ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
			<?php $this->getHeader($settings, esc_html__( "Category", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php
					$defaults = isset( $_GET[ 'category' ] ) ? $_GET[ 'category' ] : Array();
					$defaults = call_user_func_array(Array($this,'term_filter'), Array('listing_category', $defaults));
					echo jvbpd_elements_tools()->getTaxonomyElement( Array(
						'type' => $settings[ 'filter_type' ],
						'default_val' => $defaults,
						'remove_button' => 'enable' == $settings[ 'filter_dropdown_remove_button' ],
						'limit_items' => $settings['filter_dropdown_limit_select'],
						'taxonomy' => 'listing_category',
						'show_count' => 'yes' === $settings[ 'filter_show_count' ],
						'placeholder' => $this->getPlaceHolder( $settings ),
						'condition' => isset($settings['condition']) ? $settings['condition'] : 'or',
					) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function location( $settings=Array() ) {
		$this->add_render_attribute('panel-header', 'data-target', '#filter-location-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-location-' . $settings['_id'], true ); ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
			<?php $this->getHeader($settings, esc_html__( "Location", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php
					$defaults = isset( $_GET[ 'location' ] ) ? $_GET[ 'location' ] : Array();
					$defaults = call_user_func_array(Array($this,'term_filter'), Array('listing_location', $defaults));
					echo jvbpd_elements_tools()->getTaxonomyElement( Array(
						'type' => $settings[ 'filter_type' ],
						'default_val' => $defaults,
						'remove_button' => 'enable' == $settings[ 'filter_dropdown_remove_button' ],
						'limit_items' => $settings['filter_dropdown_limit_select'],
						'taxonomy' => 'listing_location',
						'show_count' => 'yes' === $settings[ 'filter_show_count' ],
						'placeholder' => $this->getPlaceHolder( $settings ),
						'condition' => isset($settings['condition']) ? $settings['condition'] : 'or',
					) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function amenities( $settings=Array() ) {
		$this->add_render_attribute('panel-header', 'data-target', '#filter-amenities-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-amenities-' . $settings['_id'], true ); ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
			<?php $this->getHeader($settings, esc_html__( "Amenities", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php
					$defaults = isset( $_GET[ 'amenity' ] ) ? $_GET[ 'amenity' ] : Array();
					$defaults = call_user_func_array(Array($this,'term_filter'), Array('listing_amenities', $defaults));
					echo jvbpd_elements_tools()->getTaxonomyElement( Array(
						'type' => $settings[ 'filter_type' ],
						'default_val' => $defaults,
						'remove_button' => 'enable' == $settings[ 'filter_dropdown_remove_button' ],
						'limit_items' => $settings['filter_dropdown_limit_select'],
						'taxonomy' => 'listing_amenities',
						'show_count' => 'yes' === $settings[ 'filter_show_count' ],
						'placeholder' => $this->getPlaceHolder( $settings ),
						'condition' => isset($settings['condition']) ? $settings['condition'] : 'or',
					) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function keyword($settings=Array()){
		$this->add_render_attribute('panel-header', 'data-target', '#filter-keyword-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-keyword-' . $settings['_id'], true ); ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
			<?php $this->getHeader($settings, esc_html__( "Keyword", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php
					$defaults = isset( $_GET[ 'listing_keyword' ] ) ? $_GET[ 'listing_keyword' ] : Array();
					$defaults = call_user_func_array(Array($this,'term_filter'), Array('listing_keyword', $defaults));
					echo jvbpd_elements_tools()->getTaxonomyElement( Array(
						'type' => $settings[ 'filter_type' ],
						'default_val' => $defaults,
						'remove_button' => 'enable' == $settings[ 'filter_dropdown_remove_button' ],
						'limit_items' => $settings['filter_dropdown_limit_select'],
						'taxonomy' => 'listing_keyword',
						'show_count' => 'yes' === $settings[ 'filter_show_count' ],
						'placeholder' => $this->getPlaceHolder( $settings ),
						'condition' => isset($settings['condition']) ? $settings['condition'] : 'or',
					) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function more_taxonomy( $settings=Array() ) {
		$this->add_render_attribute('panel-header', 'data-target', '#filter-more-tax-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-more-tax-' . $settings['_id'], true ); ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
			<?php $this->getHeader($settings, esc_html__( "More Taxonomy", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php
					$defaults = isset( $_GET[$settings['more_taxonomy']] ) ? $_GET[$settings['more_taxonomy']] : Array();
					$defaults = call_user_func_array(Array($this,'term_filter'), Array($settings['more_taxonomy'], $defaults));
					echo jvbpd_elements_tools()->getTaxonomyElement( Array(
						'type' => $settings[ 'filter_type' ],
						'default_val' => $defaults,
						'remove_button' => 'enable' == $settings[ 'filter_dropdown_remove_button' ],
						'limit_items' => $settings['filter_dropdown_limit_select'],
						'taxonomy' => $settings[ 'more_taxonomy' ],
						'show_count' => 'yes' === $settings[ 'filter_show_count' ],
						'placeholder' => $this->getPlaceHolder( $settings ),
						'condition' => isset($settings['condition']) ? $settings['condition'] : 'or',
					) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function image( $settings=Array() ) {
		$this->add_render_attribute('panel-header', 'data-target', '#filter-image-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-image-' . $settings['_id'], true ); ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
			<?php $this->getHeader($settings, esc_html__( "Image", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php
					$image = isset( $settings[ 'image_image' ][ 'url' ] ) ? $settings[ 'image_image' ][ 'url' ] : false;
					$link = isset( $settings[ 'image_link' ][ 'link' ] ) ? $settings[ 'image_link' ][ 'link' ] : false;
					$target = isset( $settings[ 'image_link' ][ 'is_external' ] ) && $settings[ 'image_link' ][ 'is_external' ] == 'on' ? '_blank' : '_self';
					$format = $link ? '<a href="%1$s" target="%3$s"><img src="%2$s"></a>' : '<img src="%2$s">';
					printf( $format, $link, $image, $target ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function featured_listing( $settings=Array() ) {
		return;
		$this->add_render_attribute('panel-header', 'data-target', '#filter-featured-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-featured-' . $settings['_id'], true ); ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
			<?php $this->getHeader($settings, esc_html__( "Featured Listings", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php
					$carouselOptions = Array(
						'autoplay' => false, // $settings[ 'carousel_autoplay' ] === '1',
						'loop' => true, // $settings[ 'carousel_loop' ] === '1',
						'mousewheel' => true, // $settings[ 'carousel_mouse_wheel' ] === '1',
						'nav' => $settings[ 'block_carousel_navigation' ] === '1',
						'nav_pos' => $settings[ 'block_carousel_navi_position' ],
						'dots' => $settings[ 'block_carousel_dots' ] === '1',
						'items' => 1, // intVal( $settings[ 'carousel_items_per_slide' ] ),
					);
					echo jvbpd_elements_tools()->getShortcode( 'jvbpd_block2', Array(
						'carousel' => json_encode( $carouselOptions ),
						'post_type' => 'lv_listing',
						'featured_only' => true,
						'block_display_type' => 'carousel',
						'module_contents_length' => 10,
					) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function price($settings=Array()){
		$this->add_render_attribute('panel-header', 'data-target', '#filter-price-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-price-' . $settings['_id'], true );
		$this->add_render_attribute( 'slider-wrap', Array(
			'class' => Array('jvbpd-noui-price-filter', 'jvbpd-ui-slider', 'not-layer'),
			'data-min' => $settings['price_min'],
			'data-max' => $settings['price_max'],
			'data-step' => 1,
			'data-prefix' => $settings['price_unit'],
			'data-suffix' => '',
		));
		$this->add_render_attribute( 'slider', Array(
			'class' => 'slider',
		));
		?>
		<?php $this->getHeader($settings, esc_html__( "Price", 'jvfrmtd' )); ?>
		<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
			<div <?php echo $this->get_render_attribute_string('slider-wrap');?>>
				<div <?php echo $this->get_render_attribute_string('slider');?>></div>
				<div class="tooltips">
					<div class="tooltip-min inline-block"></div>
					<div class="tooltip-max inline-block"></div>
				</div>
			</div>
		</div>
		<?php
	}

	public function getACFieldFilter( $field, $settings=Array() ) {
		switch( $field[ 'type' ] ) {
			case 'number' :
				if( 'yes' == $settings[ 'acf_field_is_slide_filter' ] ) {
					printf(
						'<div class="jvbpd-ui-slider" data-min="%1$s" data-max="%2$s" data-prefix="%3$s"><div class="slider"></div><div class="status text-center"><div class="tooltip-min inline-block"></div><div class="tooltip-to inline-block">%4$s</div><div class="tooltip-max inline-block"></div></div></div>',
						$settings[ 'acf_field_slide_number_min' ],
						$settings[ 'acf_field_slide_number_max' ],
						$settings[ 'acf_field_slide_number_unit' ],
						esc_html__( "To", 'jvfrmtd' )
					);
				}
				break;
		}
	}

	public function get_acf_key( $field='' ) {
		$length = strlen( $field );
		return $GLOBALS[ 'wpdb' ]->get_var(
			" SELECT `meta_key`
			FROM {$GLOBALS['wpdb']->postmeta}
			WHERE `meta_key` LIKE 'field_%' AND `meta_value` LIKE '%\"name\";s:{$length}:\"{$field}\";%';"
		);
	}

	public function acf_field( $settings=Array() ) {
		$this->add_render_attribute('panel-header', 'data-target', '#filter-acf-' . $settings['_id'], true );
		$this->add_render_attribute('panel-body', 'id', 'filter-acf-' . $settings['_id'], true );
		// $acfField = get_field_object( $this->get_acf_key( $settings[ 'acf_key' ] ) );
		$acfField = Array(
			'type' => 'number',
		);  ?>
		<div id="<?php echo esc_attr( 'filter-address' ); ?>" class="panel elementor-repeater-item-<?php echo $settings['_id'] ?> ui-<?php echo $settings['filter_type']; ?>">
		<?php $this->getHeader($settings, esc_html__( "ACF Filter", 'jvfrmtd' )); ?>
			<div <?php echo $this->get_render_attribute_string('panel-body'); ?>>
				<div class="panel-body">
					<?php $this->getACFieldFilter( $acfField, $settings ); ?>
				</div>
			</div>
		</div>
		<?php
	}

}