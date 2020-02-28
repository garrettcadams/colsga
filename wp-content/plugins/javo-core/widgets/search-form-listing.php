<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Style for header
 *
 *
 * @since 1.0.0
 */

class jvbpd_search_form_listing extends Widget_Base {   //this name is added to class-elementor.php of the root folder

	public function get_name() {
		return 'jvbpd-search-form-listing';
	}

	public function get_title() {
		return 'Search Form ( Listing )';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-button';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-page-builder-search' ];    // category of the widget
	}

	/**
	 * A list of scripts that the widgets is depended in
	 * @since 1.3.0
	 **/
	protected function _register_controls() {

		//start of a control box
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'Note', 'jvfrmtd' ),
		) );

		$this->add_control( 'Des', array(
			'type' => Controls_Manager::RAW_HTML,
			'raw'  => sprintf(
				'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
				'<li class="doc-link">'.
				esc_html__('How to use this widget.','jvfrmtd').
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-search-form/" style="color:#fff;"> ' .
				esc_html__( 'Documentation', 'jvfrmtd' ) .
				'</a></li></ul></div>'
			)
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Listing Search Form', 'jvfrmtd' ),   //section name for controler view
		] );

		$this->add_control( 'field_type', Array(
			'type' => Controls_Manager::SELECT2,
			'label' => esc_html__( "Form field", 'jvfrmtd' ),
			'options' => jvbpd_elements_tools()->getMoreTaxonoiesOptions( Array(
				'' => esc_html( 'Select a field', 'jvfrmtd' ),
				'keyword' => esc_html__( 'Keyword', 'jvfrmtd' ),
				'listing_category' => esc_html__( 'Category', 'jvfrmtd' ),
				'listing_location' => esc_html__( 'Location', 'jvfrmtd' ),
				'listing_category_with_keyword' => esc_html__( 'Category with keyword', 'jvfrmtd' ),
				'listing_location_with_google_search' => esc_html__( 'Location + Address', 'jvfrmtd' ),
				'listing_amenities' => esc_html__( 'Amenities', 'jvfrmtd' ),
				'google_search' => esc_html__( 'Address', 'jvfrmtd' ),
				'google_current_loadtion_search' => esc_html__( 'Address + Current Location', 'jvfrmtd' ),
				'ajax_search' => esc_html__( 'Ajax search', 'jvfrmtd' ),
				'advanced_field' => esc_html__( 'Advannced', 'jvfrmtd' ),
				// 'acf_field' => esc_html__( 'ACF Plugin Field', 'jvfrmtd' ),
			) )
		) );

		$this->add_control( 'hr', [
			'type' => \Elementor\Controls_Manager::DIVIDER,
			'style' => 'thick',
		] );

		$this->add_control( 'remove_button', Array(
			'label' => esc_html__( "Remove button in dropdown filter", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'enable',
			'return_value' => 'enable',
			'condition' => Array(
				'field_type' => Array( 'listing_category', 'listing_location', 'listing_category_with_keyword', 'listing_location_with_google_search' ),
			),
		) );

		$this->add_control( 'taxonomy_hide_empty', Array(
			'label' => esc_html__( "Hide Empty Terms", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'condition' => Array(
				'field_type' => Array(
					'listing_category',
					'listing_location',
					'listing_category_with_keyword',
					'listing_location_with_google_search'
				),
			),
		) );

		$this->add_control( 'taxonomy_hide_child', Array(
			'label' => esc_html__( "Hide Child Terms", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'condition' => Array(
				'field_type' => Array(
					'listing_category',
					'listing_location',
					'listing_category_with_keyword',
					'listing_location_with_google_search'
				),
			),
		) );

		/*
		$this->add_control( 'acf_field', Array(
			'type' => Controls_Manager::SELECT2,
			'label' => esc_html__( "Form field", 'jvfrmtd' ),
			'condition' => Array( 'field_type' => 'acf_field' ),
			'options' => wp_parse_args( jvbpd_elements_tools()->getStaticACFieldMeta(), Array( '' => esc_html( 'Select a field', 'jvfrmtd' ) ) ),
		) );

		$this->add_control( 'acf_field_number_min', Array(
			'type' => Controls_Manager::NUMBER,
			'label' => esc_html__( "Slider Min", 'jvfrmtd' ),
			'default' => '0',
			'condition' => Array(
				'field_type' => 'acf_field',
				'acf_field' => Array(
					'lvac_default_price',
					'lvac_bedrooms',
					'lvac_bathrooms',
					'lvac_garages',
					'lvac_garages_size',
					'lvac_area',
					'lvac_land_area',
				),
			),
		) );

		$this->add_control( 'acf_field_number_max', Array(
			'type' => Controls_Manager::NUMBER,
			'label' => esc_html__( "Slider Max", 'jvfrmtd' ),
			'default' => '10',
			'condition' => Array(
				'field_type' => 'acf_field',
				'acf_field' => Array(
					'lvac_default_price',
					'lvac_bedrooms',
					'lvac_bathrooms',
					'lvac_garages',
					'lvac_garages_size',
					'lvac_area',
					'lvac_land_area',
				),
			),
		) );

		$this->add_control( 'acf_field_number_before_unit', Array(
			'type' => Controls_Manager::TEXT,
			'label' => esc_html__( "Prefix", 'jvfrmtd' ),
			'condition' => Array(
				'field_type' => 'acf_field',
				'acf_field' => Array(
					'lvac_default_price',
					'lvac_garages_size',
					'lvac_area',
					'lvac_land_area',
				),
			),
		) );

		$this->add_control( 'acf_field_number_after_unit', Array(
			'type' => Controls_Manager::TEXT,
			'label' => esc_html__( "Suffix", 'jvfrmtd' ),
			'condition' => Array(
				'field_type' => 'acf_field',
				'acf_field' => Array(
					'lvac_default_price',
					'lvac_garages_size',
					'lvac_area',
					'lvac_land_area',
				),
			),
		) );

		$this->add_control( 'acf_field_title', Array(
			'type' => Controls_Manager::TEXT,
			'label' => esc_html__( "Title", 'jvfrmtd' ),
			'condition' => Array(
				'field_type' => 'acf_field',
				'acf_field' => Array(
					'lvac_default_price',
					'lvac_garages_size',
					'lvac_area',
					'lvac_land_area',
				),
			),
		) ); */

		$this->add_control( 'advanced_field_description', array(
			'type' => Controls_Manager::RAW_HTML,
			'condition' => Array( 'field_type' => 'advanced_field' ),
			'raw'  => sprintf(
				'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
				'<li class="doc-link">'.
				esc_html__( "A doc for this widget",'jvfrmtd').
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-search-form/" style="color:#fff;"> ' .
				esc_html__( 'Documentation', 'jvfrmtd' ) .
				'</a></li></ul></div>'
			)
		) );

		$this->add_control( 'advanced_section_id', Array(
			'label' => __( "Section ID", 'jvfrmtd' ),
			'type' => Controls_Manager::TEXT,
			'placeholder' => __( 'e.g : section1', 'jvfrmtd' ),
			'description' => esc_html__('If you have an advanced field, add the collase section ID', 'jvfrmtd'),
			'condition' => Array( 'field_type' => 'advanced_field' ),
		) );

		$this->add_control( 'ajax_defult_category', Array(
			'label' => __( "Hide default categories", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'condition' => Array( 'field_type' => 'ajax_search' ),
			'frontend_available' => true,
		) );

		$this->add_control( 'ajax_loading_img', Array(
			'label' => __( "Loading Image", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'options' => Array(
				''	=> esc_html__("Default", 'jvfrmtd'),
				'ellipsis' => esc_html__( 'Ellipsis', 'jvfrmtd' ),
				'bars' => esc_html__( 'Bars', 'jvfrmtd' ),
				'ripple-thin' => esc_html__( 'Ripple Thin', 'jvfrmtd' ),
				'ripple-thick' => esc_html__( 'Ripple Thick', 'jvfrmtd' ),
			),
			'description' => esc_html__('Please select one loading image you like', 'jvfrmtd'),
			'condition' => Array( 'field_type' => 'ajax_search' ),
		) );

		$this->add_control( 'field_width', Array(
			'type' => Controls_Manager::SLIDER,
			'label' => esc_html__( "Form Width (%)", 'jvfrmtd' ),
			'default' => Array( 'size' => 100, 'unit' => '%' ),
			'range' => Array(
				'%' => Array(
					'min' => 0,
					'max' => 100,
				),
			),
			'size_units' => Array( '%' ),
		) );


		jvbpd_elements_tools()->add_button_control( $this, Array(
			'condition' => Array( 'field_type' => 'advanced_field' ),
		) );

		$this->end_controls_section();


		//start of a control box
		$this->start_controls_section( 'amenities_style', array(
			'label' => esc_html__( 'Amenities Style', 'jvfrmtd' ),
			'condition'	=>[
				'field_type'	=>	'listing_amenities',
			]
		) );

		$this->add_responsive_control( 'amenities-width', [
			'label' => __( 'Each Cols Width', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 33,
				'unit' => '%',
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 500,
					'step' => 5,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
			],
			'size_units' => [ 'px','%' ],
			'selectors' => [
				'{{WRAPPER}} .field-listing_amenities .amenities' => 'width: {{SIZE}}{{UNIT}};',
			]
		] );


		/** Checkbox default */
		$this->add_control(
			'check_txt_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#cccccc',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .chk-wrap' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'check_txt_typography',
			'selector' => '{{WRAPPER}} .chk-wrap',
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
		/** //Checkbox default */

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




		$this->end_controls_section();

		//start of a control box
		$this->start_controls_section( 'switcher_style', array(
			'label' => esc_html__( 'Switcher Style', 'jvfrmtd' ),
			'condition' => Array( 'field_type' => 'listing_location_with_google_search' ),
		) );


			$this->add_control(
			'switcher_bg_color',
				[
					'label' => __( 'Background Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#222d79',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .jvbpd-switcher' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control( 'switcher_wrap_radius', Array(
                'label' => __( 'Switcher Wrap Radius', 'jvfrmtd' ),
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
                    '{{WRAPPER}} .jvbpd-switcher' => 'border-radius:{{SIZE}}{{UNIT}};',
                ],
            ) );


			$this->add_control( 'geolocation_position', Array(
                'label' => __( 'Geolocation Position', 'jvfrmtd' ),
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
                    '{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline.field-listing_location_with_google_search .javo-geoloc-trigger' => 'right:{{SIZE}}{{UNIT}};',
                ],
            ) );


			$this->add_control(
				'geolocation_color',
					[
						'label' => __( 'Geolocation Color', 'jvfrmtd' ),
						'type' => Controls_Manager::COLOR,
						'default' => '#888',
						'scheme' => [
							'type' => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_1,
						],
						'selectors' => [
							'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline.field-listing_location_with_google_search .javo-geoloc-trigger' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control( 'geolocation_btn_size', Array(
					'label' => __( 'Geolocation Button Size', 'jvfrmtd' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 11,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 30,
						],
						'%' => [
							'min' => 0,
							'max' => 40,
						],
					],
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline.field-listing_location_with_google_search .javo-geoloc-trigger' => 'font-size:{{SIZE}}{{UNIT}};',
					],
				) );

				$this->add_control(
					'geo_font_weight',
					[
						'label' => __( 'Geolocation Weight', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'default' => '300',
						'options' => [
							'100'  => __( '100', 'jvfrmtd' ),
							'200'  => __( '200', 'jvfrmtd' ),
							'300'  => __( '300', 'jvfrmtd' ),
							'400'  => __( '400', 'jvfrmtd' ),
							'500'  => __( '500', 'jvfrmtd' ),
							'600'  => __( '600', 'jvfrmtd' ),
							'700'  => __( '700', 'jvfrmtd' ),
							'800'  => __( '800', 'jvfrmtd' ),
							'900'  => __( '900', 'jvfrmtd' ),
						],
						'selectors' => [
							'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline.field-listing_location_with_google_search .javo-geoloc-trigger' => 'font-weight:{{VALUE}};',
						],
					]
				);


		$this->end_controls_section();



		$this->start_controls_section( 'slider_style', [
			'label' => esc_html__( 'Slider Style', 'jvfrmtd' ),   //section name for controler view
			'condition' => Array(
				'field_type' => 'acf_field',
				'acf_field' => Array(
					'lvac_default_price',
					'lvac_bedrooms',
					'lvac_bathrooms',
					'lvac_garages',
					'lvac_garages_size',
					'lvac_area',
					'lvac_land_area',
				),
			),
		] );

		$this->add_control(
		'slider_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#55606e',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-ui-slider .noUi-base .noUi-connect' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
		'slider_handle_color',
			[
				'label' => __( 'Handle Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2185bd',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-ui-slider .noUi-base .noUi-origin .noUi-handle' => 'background-color: {{VALUE}}',
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
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'slider_border',
				'selector' => '{{WRAPPER}} .jvbpd-ui-slider .slider.noUi-target',
			]
		);

		$this->add_control(
		'slider_top_space',
			[
				'label' => __( 'Top Space', 'jvfrmtd' ),
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
					'{{WRAPPER}} .slider.noUi-target.noUi-ltr.noUi-horizontal.noUi-background' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		'slider_height',
			[
				'label' => __( 'Price Slider Bar Height', 'jvfrmtd' ),
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
					'{{WRAPPER}} .slider.noUi-target.noUi-ltr.noUi-horizontal.noUi-background' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		'price_slider_handle_size',
			[
				'label' => __( 'Price Slider Handle Size', 'jvfrmtd' ),
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
				],
			]
		);

		$this->add_control(
		'price_handle_radius',
			[
				'label' => __( 'Price Handle Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .noUi-handle.noUi-handle-lower' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .noUi-handle.noUi-handle-upper' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section( 'section_style_search-from', [
			'label' => __( 'Search Fields', 'jvfrmtd' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'search_from_placeholder_typography',
			'selector' =>'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline input,{{WRAPPER}} .search-box-inline select, {{WRAPPER}} .selectize-input.items',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		  ] );

		$this->add_control(
			'search_from_placeholder_color',
			[
				'label' => __( 'Placeholder Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#999999',
				'selectors' => [
					'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .search-box-inline select::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_from_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .lava-ajax-search-form-wrap > input' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .shortcode-jvbpd_search_field .search-box-inline input' => 'background-color: {{VALUE}};',
				],
			]
		);

		/*font color*/
		/*$this->add_responsive_control( 'filter_font_color', [
			'label' => esc_html__( 'Fitler Font Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#000000',
			'selectors' => [
			  '{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline input',
			  '{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline div div label',
			],
		] ); */


		/* Ajax search dropdown items start */
		// $this->add_control( 'ajax_search_items_line_height', Array(
		// 	'label' => __( 'Ajax Search Items Line Height', 'jvfrmtd' ),
		// 	'type' => Controls_Manager::NUMBER,
		// 	'default' => 1,
		// 	'selectors' => Array(
		// 		'ul.jvbpd-ajax-search-{{ID}} > li' => 'line-height:{{VALUE}};',
		// 	),
		// 	'condition' => Array( 'field_type' => 'ajax_search' ),
		// ) );
		/* Ajax search dropdown items end */

		$this->add_responsive_control( 'filed_inner_padding', [
			'label' => __( 'Field Inner Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'default' => [
				'top' => 0,
				'right' => 0,
				'bottom' => 0,
				'left' => 10,
				'unit' => 'px'
			],
			'size_units' => [ 'px', 'em', '%' ],
			'selectors' => [
				/* Location & address */
				'{{WRAPPER}} .javo-shortcode .field-listing_location_with_google_search' => 'display:table;',
				'{{WRAPPER}} .field-location' => 'display:table-cell;',
				'{{WRAPPER}} .field-google' => 'display:table-cell;',
				/* ajax search */
				/* '{{WRAPPER}} .field-ajax_search .actions' => 'position: absolute; top: 32%; right: 20px;', */
				/* '{{WRAPPER}} .javo-search-form-geoloc i' => 'position: absolute; top: 20%; right: 20px;', */



				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .selectize-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .lava-ajax-search-form-wrap input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .javo-search-form-geoloc input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );


		$this->add_control( 'field-height', Array(
			'label' => __( 'Field Height', 'jvfrmtd' ),
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
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline input' => 'height:{{SIZE}}{{UNIT}}; min-height:{{SIZE}}{{UNIT}}; vertical-align:middle;',
				'{{WRAPPER}} .selectize-control .selectize-input'=> 'height:{{SIZE}}{{UNIT}}; min-height:{{SIZE}}{{UNIT}}; vertical-align:middle;',
			],
		) );

		$this->add_responsive_control( 'filter_border_color', [
			'label' => esc_html__( 'Fitler Border Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#e4e7ea',
			'selectors' => [
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .selectize-input' => 'border-color: {{VALUE}};',
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .lava-ajax-search-form-wrap input' => 'border-color: {{VALUE}};',
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .javo-search-form-geoloc input' => 'border-color: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'field-border', [
			'label' => __( 'Field Border', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'default' => [
				'top' => 1,
				'right' => 1,
				'bottom' => 1,
				'left' => 1,
				'unit' => 'px'
			],
			'size_units' => [ 'px', 'em', '%' ],
			'selectors' => [
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .selectize-input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .lava-ajax-search-form-wrap input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control(
			'field-radius',
			[
				'label' => __( 'Field Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'unit' => 'px'
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .selectize-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline .lava-ajax-search-form-wrap input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .javo-shortcode.shortcode-jvbpd_search_field .search-box-inline input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'search_from_sub_menu',
			[
				'label' => __( 'Dropdown Menu', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => Array( 'field_type!' => 'ajax_search' ),
			]
		);

     	  $this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'search_from_sub_menu_typography',
			'selector' => '{{WRAPPER}} .selectize-dropdown .selectize-dropdown-content > div',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		  ] );


        $this->add_control(
            'search_form_sub_menu_padding',
            [
                'label' => __( 'Padding','jvfrmtd'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
					'{{WRAPPER}} .selectize-dropdown .selectize-dropdown-content > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->start_controls_tabs( 'dropdown_style' );
        $this->start_controls_tab( 'dropdown_normal', [ 'label' => __( 'Normal', 'jvfrmtd' ) ] );
            $this->add_control(
                'search_from_sub_menu_font_color',
                [
                    'label' => esc_html__( 'Text Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
						'{{WRAPPER}} .selectize-dropdown .selectize-dropdown-content > div' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'search_from_sub_menu_background_color',
                [
                    'label' => esc_html__( 'Background Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .selectize-dropdown.form-control' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
        $this->end_controls_tab();

        $this->start_controls_tab( 'dropdown_hover', [ 'label' => __( 'Hover', 'jvfrmtd' ) ] );
            $this->add_control(
                'search_from_sub_menu_hover_font_color',
                [
                    'label' => esc_html__( 'Text Hover Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .selectize-dropdown.form-control .slimScrollDiv .selectize-dropdown-content div:not(i):hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'search_from_sub_menu_hover_background_color',
                [
                    'label' => esc_html__( 'Item Hover Background Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .selectize-dropdown.form-control .slimScrollDiv .selectize-dropdown-content div:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
        $this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/*
		$this->start_controls_section(
			'search_from_sub_menu_icon',
			[
				'label' => __( 'Dropdown Menu Icons', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => Array( 'field_type!' => 'ajax_search' ),
			]
		);

		$this->add_control(
			'search_from_sub_menu_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'body ul.lava_ajax_search li.ui-menu-item.group-title span div.result-item a i' => 'color: {{VALUE}};',

				],
			]
		);

		$this->add_control(
			'search_from_sub_menu_hover_icon_color',
			[
				'label' => esc_html__( 'Icon Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'body ul.lava_ajax_search li.ui-menu-item.group-title:hover span div.result-item a i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
		*/

		/* Ajax Style */
		$this->start_controls_section(
			'ajax_search_from_sub_menu',
			[
				'label' => __( 'Dropdown Style', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => Array( 'field_type' => 'ajax_search' ),
			]
		);

		$this->add_control( 'ajax_sub_menu_position', Array(
			'label' => esc_html__( 'Custom position', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'frontend_available' => true,
		) );
		$this->add_control( 'ajax_sub_menu_position_top', Array(
			'label' => esc_html__( 'Top', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => Array( 'size' => 0, 'unit' => 'px' ),
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 1000,
				),
			),
			'condition' => Array('ajax_sub_menu_position'=>'yes'),
			'frontend_available' => true,
		) );

		$this->add_control( 'ajax_sub_menu_position_left', Array(
			'label' => esc_html__( 'Left', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => Array( 'size' => 0, 'unit' => 'px' ),
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 1000,
				),
			),
			'condition' => Array('ajax_sub_menu_position'=>'yes'),
			'frontend_available' => true,
		) );

		$this->add_control( 'ajax_sub_menu_position_width', Array(
			'label' => esc_html__( 'Width', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => Array( 'size' => 100, 'unit' => 'px' ),
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 1000,
				),
			),
			'condition' => Array('ajax_sub_menu_position'=>'yes'),
			'frontend_available' => true,
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'ajax_sub_menu_typography',
			'selector' => 'ul.jvbpd-ajax-search-{{ID}} .result-item > a,ul.jvbpd-ajax-search-{{ID}} .result-item > a',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		  ] );


        $this->add_control(
            'ajax_sub_menu_padding',
            [
                'label' => __( 'Padding','jvfrmtd'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
					'ul.jvbpd-ajax-search-{{ID}} .ui-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->start_controls_tabs( 'ajax_dropdown_style' );
        $this->start_controls_tab( 'ajax_dropdown_normal', [ 'label' => __( 'Normal', 'jvfrmtd' ) ] );
            $this->add_control(
                'ajax_sub_menu_font_color',
                [
                    'label' => esc_html__( 'Text Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
						'ul.jvbpd-ajax-search-{{ID}} .result-item > a' => 'color: {{VALUE}};',
                    ],
                ]
			);

            $this->add_control(
                'ajax_sub_menu_icon_color',
                [
                    'label' => esc_html__( 'Icon Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
						'ul.jvbpd-ajax-search-{{ID}} .result-item > a > i' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ajax_sub_menu_background_color',
                [
                    'label' => esc_html__( 'Background Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        'ul.jvbpd-ajax-search-{{ID}} .ui-menu-item' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
        $this->end_controls_tab();

        $this->start_controls_tab( 'ajax_dropdown_hover', [ 'label' => __( 'Hover', 'jvfrmtd' ) ] );
            $this->add_control(
                'ajax_sub_menu_hover_font_color',
                [
                    'label' => esc_html__( 'Text Hover Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        'ul.jvbpd-ajax-search-{{ID}} .ui-menu-item:hover .result-item > a' => 'color: {{VALUE}} !important;',
                    ],
                ]
			);

            $this->add_control(
                'ajax_sub_menu_hover_icon_color',
                [
                    'label' => esc_html__( 'Icon Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
						'ul.jvbpd-ajax-search-{{ID}} .ui-menu-item:hover .result-item > a > i' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_control(
                'ajax_sub_menu_hover_background_color',
                [
                    'label' => esc_html__( 'Item Hover Background Color', 'jvfrmtd' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        'ul.jvbpd-ajax-search-{{ID}} .ui-menu-item:hover' => 'background-color: {{VALUE}} !important;',
                    ],
                ]
            );
        $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function __html( $setting = null, $format = '%s' ) {
		call_user_func( array( $this, '__render_html' ), $setting, $format );
	}

	public function __render_html( $setting = null, $format = '%s' ) {

		if ( is_array( $setting ) ) {
			$key     = $setting[1];
			$setting = $setting[0];
		}

		$val = $this->get_settings( $setting );

		if ( ! is_array( $val ) && '0' === $val ) {
			printf( $format, $val );
		}

		if ( is_array( $val ) && empty( $val[ $key ] ) ) {
			return '';
		}

		if ( ! is_array( $val ) && empty( $val ) ) {
			return '';
		}

		if ( is_array( $val ) ) {
			printf( $format, $val[ $key ] );
		} else {
			printf( $format, $val );
		}
	}

	public function linkAttributes() {
		$this->add_render_attribute( 'advanced_link', Array(
			'data-toggle' => 'collapse',
			'data-target' => '#' . $this->get_settings( 'advanced_section_id' ),
		) );
	}

	public function advanced_field( $params=Array(), $element='' ) {
		$settings = $this->get_settings();

		$classes_list = array( 'jvbpd-advanced-button' );

		$button_url_data = $this->get_settings( 'button_url' );
		$button_url = $button_url_data['url'];
		$button_is_external = $button_url_data['is_external'];
		$button_nofollow = $button_url_data['nofollow'];

		$position = $this->get_settings( 'icon_normal_style_btn_icon_arrange' );
		$use_icon = $this->get_settings( 'icon_normal_style_use_btn_icon' );
		$hover_effect = $this->get_settings( 'settings_hover_effect' );

		$classes_list[] = 'jvbpd-advanced-button--icon-' . $position;
		$classes_list[] = 'hover-' . $hover_effect;

		$this->linkAttributes();
		$this->add_render_attribute( 'advanced_link', Array(
			'class' => $classes_list,
		) ); ?>
		<div class="jvbpd-button__container">
			<a <?php echo $this->get_render_attribute_string( 'advanced_link' ); ?>>
				<div class="jvbpd-button_wapper jvbpd-button_wrapper-normal"></div>
				<div class="jvbpd-button_wapper jvbpd-button_wrapper-hover"></div>
				<div class="jvbpd-button_inner jvbpd-button_inner-normal">
					<?php
						if ( filter_var( $use_icon, FILTER_VALIDATE_BOOLEAN ) ) {
							echo $this->__html( 'txt_icon_button_icon_normal', '<span class="jvbpd-button_icon"><i class="%s"></i></span>' );
						}
						echo $this->__html( 'txt_icon_button_label_normal', '<span class="jvbpd-button_txt">%s</span>' );
					?>
				</div>
				<div class="jvbpd-button_inner jvbpd-button_inner-hover">
					<?php
						if ( filter_var( $use_icon, FILTER_VALIDATE_BOOLEAN ) ) {
							echo $this->__html( 'txt_icon_button_icon_hover', '<span class="jvbpd-button_icon"><i class="%s"></i></span>' );
						}
						echo $this->__html( 'txt_icon_button_label_hover', '<span class="jvbpd-button_txt">%s</span>' );
					?>
				</div>
			</a>
		</div>
		<?php
	}

	public function sliderField( array $params=Array() ) {
		$options = shortcode_atts( Array(
			'min' => $this->get_settings( 'acf_field_number_min' ),
			'max' => $this->get_settings( 'acf_field_number_max' ),
			'step' => false,
			'before_unit' => $this->get_settings( 'acf_field_number_before_unit' ),
			'after_unit' => $this->get_settings( 'acf_field_number_after_unit' ),
			'name' => false,
			'title' => esc_html__( "Slider : ", 'jvfrmtd' ),
			'slidePosition' => 'bottom',
			'labelAlign' => 'center',
		), $params );

		if( $this->get_settings( 'acf_field_title' ) ) {
			$options[ 'title' ] = $this->get_settings( 'acf_field_title' );
		}

		$output = '<div class="jvbpd-ui-slider" data-min="%1$s" data-max="%2$s" data-step="%3$s" data-prefix="%4$s" data-suffix="%5$s">';
			if( 'top' === $options[ 'slidePosition' ] ) {
				$output .= '<div class="slider"></div>';
			}
				$output .= '<div class="tooltip-title pull-left inline-block"><strong>%7$s</strong></div>';
			$output .= '<div class="status text-%6$s">';
				$output .= '<div class="tooltip-min inline-block"></div>';
				$output .= '<div class="tooltip-to inline-block">%9$s</div>';
				$output .= '<div class="tooltip-max inline-block"></div>';
			$output .= '</div>';
			if( 'bottom' === $options[ 'slidePosition' ] ) {
				$output .= '<div class="slider"></div>';
			}
			if( false !== $options[ 'name' ] ) {
				$output .= '<div class="fields">';
					$output .= '<input type="hidden" name="min_%8$s" data-min-val>';
					$output .= '<input type="hidden" name="max_%8$s" data-max-val>';
				$output .= '</div>';
			}
		$output .= '</div>';

		return sprintf(
			$output,
			$options[ 'min' ],
			$options[ 'max' ],
			$options[ 'step' ],
			$options[ 'before_unit' ],
			$options[ 'after_unit' ],
			$options[ 'labelAlign' ],
			$options[ 'title' ],
			$options[ 'name' ],
			esc_html__( "To", 'jvfrmtd' )
		);
	}

	public function selectize( array $params=Array() ) {
		$options = shortcode_atts( Array(
			'min' => $this->get_settings( 'acf_field_number_min' ),
			'max' => $this->get_settings( 'acf_field_number_max' ),
			'step' => 1,
			'unit' => '',
			'name' => '',
			'placeholder' => false,
		), $params );

		$items = '';
		if( $options[ 'placeholder' ] ) {
			$items = sprintf( '<option value="">%1$s</option>', $options[ 'placeholder' ] );
		}
		$min = floatVal( $options[ 'min' ] );
		$max = floatVal( $options[ 'max' ] );
		$step = max( floatVal( $options[ 'step' ] ), 1 );

		for( $i = $min; $i <= $max; $i+=$step ) {
			$items .= sprintf( '<option value="%1$s">%1$s</option>', $i );
		}
		return sprintf( '<select name="%1$s" class="form-class" data-selectize data-max-items="1">%2$s</select>', $options[ 'name' ], $items );
	}

	public function input( array $params=Array() ) {
		$options = shortcode_atts( Array(
			'name' => '',
			'placeholder' => null,
		), $params );
		return sprintf(
			'<input type="text" name="%1$s" class="form-control" placeholder="%2$s">',
			$options[ 'name' ], $options[ 'placeholder' ]
		);
	}

	public function lvac_default_price( $field ) { echo $this->sliderField( Array( 'name' => 'price', 'title' => esc_html__( "Price : ", 'jvfrmtd' ) ) ); }
	public function lvac_area( $field ) { echo $this->sliderField( Array( 'name' => 'area', 'title' => esc_html__( "Area : ", 'jvfrmtd' ) ) ); }

	public function lvac_bedrooms( $field ) { echo $this->selectize( Array( 'name' => $field, 'placeholder' => 'Beds', 'jvfrmtd' ) ); }
	public function lvac_bathrooms( $field ) { echo $this->selectize( Array( 'name' => $field, 'placeholder' => 'Baths', 'jvfrmtd' ) ); }
	public function lvac_garages( $field ) { echo $this->selectize( Array( 'name' => $field, 'placeholder' => 'Garages', 'jvfrmtd' ) ); }
	public function lvac_garages_size( $field ) { echo $this->sliderField( Array( 'name' => 'garages_size', 'title' => esc_html__( "Garage Size : ", 'jvfrmtd' ) ) ); }
	public function lvac_land_area( $field ) { echo $this->sliderField( Array( 'name' => 'land_area', 'title' => esc_html__( "Land Area : ", 'jvfrmtd' ) ) ); }

	public function lvac_property_id( $field ) {
		echo $this->input( Array(
			'name' => $field,
			'placeholder' => esc_html__( "Property ID", 'jvfrmtd' )
		) );
	}

	public function acf_field( $params=Array(), $element='' ) {
		$field = $this->get_settings( 'acf_field' );

		if( method_exists( $this, $field ) ) {
			call_user_func_array( Array( $this, $field ), Array( $field ) );
		}
	}

	protected function render() {
		$settings = $this->get_settings();

		if( ! class_exists( '\jvbpd_search_field' ) ) {
			return;
		}

		$searchInstance = \jvbpd_search_field::getInstance();
		$searchParam = Array(
			'strip_form' => true,
			'break_submit' => 'true',
			'keyword_auto' => 'disable',
			'amenities_field' => 'disable',
			'disable_submit' => true,

			'columns' => 1,
			'column1' => $settings[ 'field_type' ],
			'widths' => json_encode( Array( 'column1' => $settings[ 'field_width' ][ 'size' ] ) ),
			'remove_button' => 'enable' == $settings[ 'remove_button' ],
			'taxonomy_hide_empty' => 'yes' == $settings[ 'taxonomy_hide_empty' ],
			'taxonomy_hide_child' => 'yes' == $settings[ 'taxonomy_hide_child' ],
			'advanced_section_id' => $settings[ 'advanced_section_id' ],
		);
		add_action( 'jvbpd_search_field_element_advanced_field', Array( $this, 'advanced_field' ), 10, 2);
		add_action( 'jvbpd_search_field_element_acf_field', Array( $this, 'acf_field' ), 10, 2 );
		add_action( 'jvbpd_other_shortcode_css', Array( $this, 'loading_class' ), 10, 2 );
		remove_action( 'jvbpd_search_field_element_advanced_field', Array( $searchInstance, 'advanced_field' ), 10, 2 );
		echo $searchInstance->output( $searchParam );
		remove_action( 'jvbpd_search_field_element_advanced_field', Array( $this, 'advanced_field' ), 10, 2);
		remove_action( 'jvbpd_search_field_element_acf_field', Array( $this, 'acf_field' ), 10, 2 );
		remove_action( 'jvbpd_other_shortcode_css', Array( $this, 'loading_class' ), 10, 2 );
		add_action( 'jvbpd_search_field_element_advanced_field', Array( $searchInstance, 'advanced_field' ), 10, 2 );
    }

	/* adding loading class */
	public function loading_class( $classes=Array() ) {
		$settings = $this->get_settings('ajax_loading_img');
		$classes[] = $settings;
		return  $classes;
	}

}
