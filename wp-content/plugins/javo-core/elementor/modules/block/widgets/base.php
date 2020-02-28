<?php
namespace jvbpdelement\Modules\Block\Widgets;

use jvbpdelement\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\HOVER_ANIMATION;

abstract class Base extends Base_Widget {

	Const PAGE_BLOCK = 'jvbpd-page-block';
	Const MAP_BLOCK = 'jvbpd-map-listing-blocks';
	Const LIST_BLOCK = 'jvbpd-map-list-listing-blocks';
	Const ARCHIVE_BLOCK = 'jvbpd-archive-block';

	public $generalBlock = Array( self::PAGE_BLOCK );
	public $mapBlock = Array( self::MAP_BLOCK, self::LIST_BLOCK );

	public function get_categories() {
		$output = Array();
		if( in_array( $this->get_name(), $this->generalBlock ) ) {
			$output = Array( 'jvbpd-elements' );
		}
		if( in_array( $this->get_name(), $this->mapBlock ) ) {
			$output = Array( 'jvbpd-map-page' );
		}
		return $output;
	}

	protected function _register_controls() {
		$this->getNote();

		if( in_array( $this->get_name(), $this->generalBlock ) ) {
			$this->add_post_type_controls();
		}

		$this->add_block_settings_controls();
		$this->add_module_settings_controls();
		$this->get_order_settings_controls();
		$this->add_effect_settings_controls();
		if(function_exists('lava_pf_connector')) {
			$this->add_portfolio_connector_controls();
		}

		$this->add_pagination_style_controls();
		// $this->add_content_style_controls();
		// $this->get_custom_code_controls();
	}

	public function getNote() {
		$this->start_controls_section( 'section_note', Array(
			'label' => esc_html__( 'Note', 'jvfrmtd' ),
		) );

		if( in_array( $this->get_name(), $this->generalBlock ) ) {
			$this->add_control( 'block_note', array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="doc-link">'.
					esc_html__('How to use this widget.','jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-block/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li></ul></div>'
				)
			));
		}

		if( in_array( $this->get_name(), $this->mapBlock ) ) {
			$this->add_control( 'block_note', array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="doc-link">'.
					esc_html__('How to use this widget.','jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-map-page/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only map page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;"> ' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			));
		}
		$this->end_controls_section();
	}

	public function add_masonry_contorls() {
		$this->start_controls_section( 'section_masonry_settings', Array(
			'label' => esc_html__( 'Masonry Settings', 'jvfrmtd' ),
		) );
			$this->add_control( 'block_masonry_note', array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="notice">'.
					esc_html__('Grid/list toggle widget will not be working in masonry mode.', 'jvfrmtd').
					'</li></ul></div>'
				)
			));
			$this->add_control( 'use_masonry', array(
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Use Masonry', 'jvfrmtd' ),
				'prefix_class' => 'masonry-',
				'frontend_available' => true,
			) );
			$aniOptions = Array();
			for($aniID=1;$aniID<=11;$aniID++){
				$aniOptions[$aniID] = sprintf(esc_html__("Effect %s", 'jvfrmtd'), $aniID);
			}
			$this->add_control( 'masonry_ani', Array(
				'label' => esc_html__( "Animation type", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 1,
				'options' => $aniOptions,
				'condition' => Array( 'use_masonry' => 'yes', ),
				'frontend_available' => true,
			));
			$this->add_responsive_control('masonry_cols', Array(
				'label' => esc_html__( "Columns", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'prefix_class' => 'columns%s-',
				'options' => jvbpd_elements_tools()->getColumnsOption(1, 4),
				'devices' => Array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'condition' => Array( 'use_masonry' => 'yes', ),
			));
		$this->end_controls_section();
	}

	public function add_block_settings_controls() {
		$this->start_controls_section( 'section_block_settings', Array(
			'label' => esc_html__( 'Block', 'jvfrmtd' ),
		) );

		$fields = Array();

		if( in_array( $this->get_name(), $this->generalBlock ) ) {
			$this->add_control( 'module_layout_type', Array(
				'label' => esc_html__( "Layout Type", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'type_a',
				'options' => array(
					'type_a' => esc_html__( "List Style A", 'jvfrmtd' ),
					'type_b' => esc_html__( "List Style B", 'jvfrmtd' ),
					'type_c' => esc_html__( "Box Style C", 'jvfrmtd' ),
					'type_d' => esc_html__( "Box Style D", 'jvfrmtd' ),
					'type_e' => esc_html__( "Box Style E", 'jvfrmtd' ),
				),
			) );
			$fields = Array( 'columns' );
		}

		if( in_array( $this->get_name(), $this->mapBlock ) || $this->get_name() == self::ARCHIVE_BLOCK ) {
			$fields = Array( 'columns' );
			$this->add_control( 'module_id', Array(
				'label' => esc_html__( "Module", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => jvbpd_elements_tools()->getModuleIDs(),
			) );
		}

		$this->add_group_control(
			\jvbpd_group_block_style::get_type(),
			Array(
				'name' => 'block',
				'label' => esc_html__( "Block", 'jvfrmtd' ),
				'fields' => $fields,
			)
		);

		if( in_array( $this->get_name(), $this->generalBlock ) ) {
			$column_number = Array( 1 );
			for( $columns=1; $columns <= 2; $columns++ ) {
				//$column_number[] = $columns;
				$this->add_control( 'column' . $columns . '_module', Array(
					'label' => sprintf( esc_html__( "Column %s module", 'jvfrmtd' ), $columns ),
					'type' => Controls_Manager::SELECT2,
					'options' => jvbpd_elements_tools()->getModuleIDs(),
					'label_block' => true,
					// 'condition' => Array( 'block_columns' => $column_number),
				) );
			}

		}

		/*
		$this->add_control( 'block_name', Array(
			'label' => esc_html__( 'Block name', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'multiple' => false,
			'default' => 'block2',
			'options' => jvbpd_elements_tools()->getActivateBlocks(),
		) );

		$this->add_control( 'block_columns', Array(
			'label' => esc_html__( 'Columns', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'multiple' => false,
			'default' => '2',
			'options' => jvbpd_elements_tools()->getColumnsOption( 1, 4 )
		) ); **/


		if( in_array( $this->get_name(), $this->generalBlock ) ) {

			$this->add_control( 'contents_length', Array(
				'label' => esc_html__( 'Limit length of description', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => Array( 'size' => 10 ),
				'range' => Array( 'px' => Array( 'min' => 0, 'max' => 1000, 'step' => 1 ) ),
				'size_units' => Array( 'px' ),
			) );

			$this->add_control( 'contents_count', Array(
				'label' => esc_html__( 'Number of posts to load', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => Array( 'size' => 6 ),
				'range' => Array( 'px' => Array( 'min' => 0, 'max' => 100, 'step' => 1 ) ),
				'size_units' => Array( 'px' ),
			) );

		}

		$this->add_control(
            'pagination', [
                'label' => esc_html__( 'Load More Type', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
				'condition' => [
					'use_carousel!' => 'yes',
				],
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'number' => esc_html__( 'Pagination', 'jvfrmtd' ),
                    'prevNext' => esc_html__( 'Pre/Next', 'jvfrmtd' ),
                    'loadmore' => esc_html__( 'Load More', 'jvfrmtd' ),
                ]
            ]
        );

		$this->end_controls_section();
	}

	public function add_module_settings_controls() {
		$this->start_controls_section( 'section_module_settings', Array(
			'label' => __( 'Module', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'module_wrap_padding', [
			'label' => __( 'Out Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .shortcode-output' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .javo-shortcode' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'module_out_padding', [
			'label' => __( 'Each module Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} div.col-md-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} div.col-md-3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} div.col-md-4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} div.col-md-6' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} div.col-md-12' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	public function add_content_style_controls() {
		$this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__( 'Content', 'jvfrmtd' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

				$this->add_responsive_control(
            'image_default_overlay',
            [
                'label' => esc_html__( 'Image Default Overlay', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
								'default' => 'rgba(64,84,178,0.04)',
                'selectors' => [
									'{{WRAPPER}} .javo-shortcode .module.javo-module12 .thumb-wrap .javo-thb:after' => 'background-color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'title_text_transform',
            [
                'label' => esc_html__( 'Title Text Transform', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'uppercase',
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'uppercase' => esc_html__( 'UPPERCASE', 'jvfrmtd' ),
                    'lowercase' => esc_html__( 'lowercase', 'jvfrmtd' ),
                    'capitalize' => esc_html__( 'Capitalize', 'jvfrmtd' ),
                ],
                'selectors' => [
					'{{WRAPPER}} .module.card > .card-block .card-title' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
                ],
            ]
        );

        $this->add_responsive_control(
            'title_font_size',
            [
                'label' => esc_html__( 'Title Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
                'range' => [
                    'px' => [
                        'min' => 8,
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
                    '{{WRAPPER}} .javo-shortcode .module.card > .card-block .card-title a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#333',
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode .module.card > .card-block .card-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_color_hover',
            [
                'label' => esc_html__( 'Title Hover Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_color',
            [
                'label' => esc_html__( 'Meta Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-meta a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_hover_color',
            [
                'label' => esc_html__( 'Meta Hover Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-meta a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_color_i',
            [
                'label' => esc_html__( 'Meta Icon Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'excerpt_text_transform',
            [
                'label' => esc_html__( 'Excerpt Transform', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'uppercase' => esc_html__( 'UPPERCASE', 'jvfrmtd' ),
                    'lowercase' => esc_html__( 'lowercase', 'jvfrmtd' ),
                    'capitalize' => esc_html__( 'Capitalize', 'jvfrmtd' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-excerpt p' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
                ],
            ]
        );

        $this->add_responsive_control(
            'excerpt_font_size',
            [
                'label' => esc_html__( 'Excerpt Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
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
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .blog-excerpt p' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'exceprt_color',
            [
                'label' => esc_html__( 'Excerpt Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-excerpt p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'te_align',
            [
                'label' => __( 'Text Alignment', 'jvfrmtd' ),
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
                    '{{WRAPPER}} .blog-excerpt p' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_align',
            [
                'label' => __( 'Pagination Alignment', 'jvfrmtd' ),
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
                    '{{WRAPPER}} .void-grid-nav' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'pagi_font_size',
            [
                'label' => esc_html__( 'Pagination Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
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
                    '{{WRAPPER}} .void-grid-nav' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

	}

	public function add_carousel_settings_controls() {
		$this->start_controls_section(
			'section_carousel', [
				'label' => esc_html__( 'Carousel', 'jvfrmtd' ),
			]
		);

		$this->add_control( 'use_carousel', Array(
			'label' => esc_html__( "Use carousel", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'return_value' => 'yes'
		) );

		$this->add_control( 'show_arrows', [
			'type' => Controls_Manager::SWITCHER,
			'label' => __( 'Arrows', 'jvfrmtd' ),
			'default' => 'yes',
			'label_off' => __( 'Hide', 'jvfrmtd' ),
			'label_on' => __( 'Show', 'jvfrmtd' ),
			'frontend_available' => true,
			'prefix_class' => 'jvbpd-arrows-',
			'render_type' => 'template',
			'condition' => Array( 'use_carousel' => 'yes' ),
		] );

		$this->add_control( 'arrows_color', Array(
			'label' => esc_html__( "Arrows Color", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'condition' => Array( 'show_arrows' => 'yes' ),
			'prefix_class' => 'arrow-color-',
			'default' => 'white',
			'options' => Array(
				'white' => esc_html__( "White", 'jvfrmtd' ),
				'black' => esc_html__( "Black", 'jvfrmtd' ),
			),
		) );

		$this->add_control(
			'speed',
			[
				'label' => __( 'Transition Duration', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true,
				'condition' => Array( 'use_carousel' => 'yes' ),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => Array( 'use_carousel' => 'yes' ),
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => [
					'autoplay' => 'yes',
					'use_carousel' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => Array( 'use_carousel' => 'yes' ),
				'frontend_available' => true,
			]
		);

		$this->add_control( 'effect', Array(
			'label' => __( 'Effect', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'slide',
			'options' => Array(
				'slide' => __( 'Slide', 'jvfrmtd' ),
				'fade' => __( 'Fade', 'jvfrmtd' ),
				'cube' => __( 'Cube', 'jvfrmtd' ),
				'coverflow' => __( 'Coverflow', 'jvfrmtd' ),
				'flip' => __( 'Flip', 'jvfrmtd' ),
			),
		) );

		$this->add_control( 'paginationType', [
			'label' => __( 'Pagination', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'bullets',
			'options' => [
				'' => __( 'None', 'jvfrmtd' ),
				'bullets' => __( 'Dots', 'jvfrmtd' ),
				// 'fraction' => __( 'Fraction', 'jvfrmtd' ),
				'progress' => __( 'Progress', 'jvfrmtd' ),
			],
			'prefix_class' => 'jvbpd-pagination-type-',
			'render_type' => 'template',
			'frontend_available' => true,
		] );

		$this->add_control( 'spaceBetween', Array(
			'label' => __( 'Between Space', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '0',
			'condition' => Array( 'use_carousel' => 'yes' ),
		) );

		$this->add_control( 'slidesPerView', Array(
			'label' => __( 'Slides Per View', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '1',
			'condition' => Array( 'use_carousel' => 'yes' ),
		) );

		$this->end_controls_section();
	}

	public function add_post_type_controls() {
		$this->start_controls_section( 'section_post_type', [
			'label' => esc_html__( 'Post Type', 'jvfrmtd' ),   //section name for controler view
		] );

		$this->add_control( 'post_type', [
			'label' => esc_html__( 'Post type', 'jvfrmtd' ),
			'description' => esc_html__('Please select a post type you want.','jvfrmtd'),
			'type' => Controls_Manager::SELECT,
			'multiple' => false,
			'default' => 'post',
			'options' => [
				'post' => __( 'Post', 'jvfrmtd' ),
				'lv_listing'  => __( 'Listing', 'jvfrmtd' ),
			],
		]);

		/*
		$this->add_control(
			'custom_term_listing', [
				'label' => __( 'Select / Add Terms', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'post_type' => 'lv_listing',
				],
				'default' => 'select_terms',
				'options' => [
					'select_terms' => __( 'Select Terms', 'jvfrmtd' ),
					'custom_ids'  => __( 'Add Custom IDs', 'jvfrmtd' ),
				],
				'separator' => 'none',
				'description' => 'Add custom term id or select terms.',
			]
		);

		$this->add_control(
			'custom_term_post',
			[
				'label' => __( 'Terms Selection Type : Post', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'post_type' => 'post',
				],
				'default' => 'select_terms',
				'options' => [
					'select_terms' => __( 'Select Terms', 'jvfrmtd' ),
					'custom_ids'  => __( 'Add Custom IDs', 'jvfrmtd' ),
				],
				'separator' => 'none',
				'description' => 'Add custom term id or select terms.',
			]
		);

		$this->add_control(
			'custom_listing_terms_ids',
			[
				'label' => esc_html__( 'Custom Terms IDs', 'jvfrmtd' ),
				'description' => esc_html__('Enter category IDs separated by commas (ex: 13,23,18). To exclude categories please add "-" (ex: -9, -10)','jvfrmtd'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'post_type' => 'lv_listing',
					'custom_term_listing' => 'custom_ids',
				],
				'default' => __('','jvfrmtd'),
				'separator' => 'none',
			]
		);

		$this->add_control(
			'custom_post_terms_ids',
			[
				'label' => esc_html__( 'Custom Terms IDs', 'jvfrmtd' ),
				'description' => esc_html__('Enter category IDs separated by commas (ex: 13,23,18). To exclude categories please add "-" (ex: -9, -10)','jvfrmtd'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'post_type' => 'post',
					'custom_term_post' => '1',
				],
				'default' => __('','jvfrmtd'),
				'separator' => 'none',
			]
		); */

		$postTaxonomies_options = jvbpd_elements_tools()->get_taxonomies( 'post' );
		$listingTaxonomies_options = jvbpd_elements_tools()->get_taxonomies( 'lv_listing' );

		$this->add_control(
			'post_taxonomy',
			Array(
				'label'       => __( 'Post Taxonomy', 'jvfrmtd' ),
				'type'        => Controls_Manager::SELECT2,
				'condition' => Array(
					'post_type' => 'post',
					// 'custom_term_listing' => 'select_terms',
				),
				'default' => '',
				'options' => Array( '' => esc_html__( "Select once", 'jvfrmtd' ) ) + $postTaxonomies_options,
				'separator' => 'none',
			)
		);

		jvbpd_elements_tools()->add_tax_term_control( $this, 'post_%1$s_term', Array(
			'taxonomies' => array_keys( $postTaxonomies_options ),
			'parent' => 'post_taxonomy',
			'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
			'condition' => Array( 'post_type' => 'post' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
		) );

		$this->add_control(
			'lv_listing_taxonomy',
			Array(
				'label'       => __( 'Listing Taxonomy', 'jvfrmtd' ),
				'type'        => Controls_Manager::SELECT2,
				'condition' => Array(
					'post_type' => 'lv_listing',
					// 'custom_term_listing' => 'select_terms',
				),
				'default' => '',
				'options' => Array( '' => esc_html__( "Select once", 'jvfrmtd' ) ) + $listingTaxonomies_options,
				'separator' => 'none',
			)
		);

		jvbpd_elements_tools()->add_tax_term_control( $this, 'lv_listing_%1$s_term', Array(
			'taxonomies' => array_keys( $listingTaxonomies_options ),
			'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
			'parent' => 'lv_listing_taxonomy',
			'condition' => Array( 'post_type' => 'lv_listing' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
		) );

		$this->add_control( 'only_featured', Array(
			'label' => __( 'Display only featured items', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'condition' => Array(
				'post_type' => 'lv_listing',
			),
			'default' => '',
			'label_on' => __( 'Yes', 'jvfrmtd' ),
			'label_off' => __( 'No', 'jvfrmtd' ),
			'return_value' => '1',
			'condition' => [
				'post_type' => 'lv_listing',
			],
			'separator' => 'none',
		) );

		$this->end_controls_section();
	}

	public function add_block_header_settings_controls() {
		$this->start_controls_section( 'section_block_header_setting', Array(
				'label' => esc_html__( 'Block Header', 'jvfrmtd' ),
		) );
		$this->add_control( 'filter_style', [
			'label' => esc_html__( 'Filter Style', 'jvfrmtd' ),
			'description' => esc_html__('Please select a filter type you want.','jvfrmtd'),
			'type' => Controls_Manager::SELECT,
			'multiple' => false,
			'default' => '',
			'options' => [
				'' => __( 'None', 'jvfrmtd' ),
				'general' => esc_html__( 'Style1', 'jvfrmtd' ),
				'two_titles' => esc_html__( 'Style2', 'jvfrmtd' ),
				//'box' => esc_html__( 'Style3', 'jvfrmtd' ),
			 ]
		] );
		$this->add_control( 'header_title', [
			'label' => esc_html__( 'Header Title', 'jvfrmtd' ),
			'type' => Controls_Manager::TEXT,
			'default' => __('','jvfrmtd'),
			'placeholder' => __('Please add a title.','jvfrmtd'),
			'separator' => 'none',
			'condition' => [
				'filter_style!'=>'',
			],
		] );
		$this->add_control( 'subtitle', [
			'label' => esc_html__( 'Sub Title', 'jvfrmtd' ),
			'type' => Controls_Manager::TEXT,
			'default' => __('','jvfrmtd'),
			'placeholder' => __('Please add a title.','jvfrmtd'),
			'separator' => 'none',
			'condition' => [
				'filter_style'=>'two_titles',
			],
		] );
		$this->add_control(
		'subtitle_color',
			[
				'label' => __( 'Sub Title Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
						'{{WRAPPER}} .shortcode-title .subtitle' => 'color: {{VALUE}}',
				],
				'condition' => [
					'filter_style'=>'two_titles',
				],
			]
		);
		$this->end_controls_section();
	}

	public function get_order_settings_controls() {
		$this->start_controls_section( 'section_order_setting', Array(
			'label' => esc_html__( "Order", 'jvfrmtd' ),
		) );

		$this->add_control( 'order_by', Array(
			'label' => esc_html__( 'Order By', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'options' => Array(
				'' => __( 'None', 'jvfrmtd' ),
				'title'  => __( 'Post Title', 'jvfrmtd' ),
				'date'  => __( 'Date', 'jvfrmtd' ),
				'rand'  => __( 'Random', 'jvfrmtd' ),
				'rating' => esc_html__( "Rating", 'jvfrmtd' ),
			),
			'frontend_available' => true,
		) );

		$this->add_control( 'order_type', Array(
			'label' => esc_html__( 'Order Type', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'DESC',
			'options' => Array(
				'DESC' => __( 'Descending', 'jvfrmtd' ),
				'ASC'  => __( 'Ascending', 'jvfrmtd' )
			),
			'frontend_available' => true,
		) );

		$this->end_controls_section();
	}

	public function add_effect_settings_controls() {
		$this->start_controls_section( 'section_effect_setting', Array(
			'label' => esc_html__( "Effect", 'jvfrmtd' ),
		) );

			$this->add_control('animation_property', Array(
				'type' =>Controls_Manager::SELECT2,
				'label' => esc_html__( 'Animation Property', 'jvfrmtd' ),
				'default' => '',
				'options' => Array(
					'' => esc_html__( 'None', 'jvfrmtd' ),
					'fadeIn' => esc_html__( 'Fade In', 'jvfrmtd' ),
					'zoomIn' => esc_html__( 'Zoom In', 'jvfrmtd' ),
					'zoomOut' => esc_html__( 'Zoom Out', 'jvfrmtd' ),
					'fromTop' => esc_html__( 'From Top', 'jvfrmtd' ),
					'fromLeft' => esc_html__( 'From Left', 'jvfrmtd' ),
					'fromBottom' => esc_html__( 'From Bottom', 'jvfrmtd' ),
					'fromRight' => esc_html__( 'From Right', 'jvfrmtd' ),
					'fromTopLeft' => esc_html__( 'From Top Left', 'jvfrmtd' ),
					'fromTopRight' => esc_html__( 'From Top Right', 'jvfrmtd' ),
					'fromBottomLeft' => esc_html__( 'From Bottom Left', 'jvfrmtd' ),
					'fromBottomRight' => esc_html__( 'From Bottom Right', 'jvfrmtd' ),
					'slideTop' => esc_html__( 'Slide Top', 'jvfrmtd' ),
					'slideLeft' => esc_html__( 'Slide Left', 'jvfrmtd' ),
					'slideBottom' => esc_html__( 'Slide Bottom', 'jvfrmtd' ),
					'slideRight' => esc_html__( 'Slide Right', 'jvfrmtd' ),
					'slideTopLeft' => esc_html__( 'Slide Top Left', 'jvfrmtd' ),
					'slideTopRight' => esc_html__( 'Slide Top Right', 'jvfrmtd' ),
					'slideBottomLeft' => esc_html__( 'Slide Bottom Left', 'jvfrmtd' ),
					'slideBottomRight' => esc_html__( 'Slide Bottom Right', 'jvfrmtd' ),
					'flipX' => esc_html__( 'Flip X', 'jvfrmtd' ),
					'flipY' => esc_html__( 'Flip Y', 'jvfrmtd' ),
					'flipZ' => esc_html__( 'Flip Z', 'jvfrmtd' ),
					'fromTopFlipX' => esc_html__( 'From Top Flip X', 'jvfrmtd' ),
					'fromTopFlipY' => esc_html__( 'From Top Flip Y', 'jvfrmtd' ),
					'fromTopFlipZ' => esc_html__( 'From Top Flip Z', 'jvfrmtd' ),
					'fromLeftFlipX' => esc_html__( 'From Left Flip X', 'jvfrmtd' ),
					'fromLeftFlipY' => esc_html__( 'From Left Flip Y', 'jvfrmtd' ),
					'fromLeftFlipZ' => esc_html__( 'From Left Flip Z', 'jvfrmtd' ),
					'fromBottomFlipX' => esc_html__( 'From Bottom Flip X', 'jvfrmtd' ),
					'fromBottomFlipY' => esc_html__( 'From Bottom Flip Y', 'jvfrmtd' ),
					'fromBottomFlipZ' => esc_html__( 'From Bottom Flip Z', 'jvfrmtd' ),
					'fromRightFlipX' => esc_html__( 'From Right Flip X', 'jvfrmtd' ),
					'fromRightFlipY' => esc_html__( 'From Right Flip Y', 'jvfrmtd' ),
					'fromRightFlipZ' => esc_html__( 'From Right Flip Z', 'jvfrmtd' ),
					'perspectiveX' => esc_html__( 'Perspective X', 'jvfrmtd' ),
					'perspectiveY' => esc_html__( 'Perspective Y', 'jvfrmtd' ),
					'perspectiveZ' => esc_html__( 'Perspective Z', 'jvfrmtd' ),
					'falling_rotate' => esc_html__( 'Falling Rotate', 'jvfrmtd' ),
				),
				'frontend_available' => true,
			));

			$this->add_control('animation_on_target', Array(
				'type' =>Controls_Manager::SELECT,
				'label' => esc_html__( 'Animation Target', 'jvfrmtd' ),
				'default' => '',
				'options' => Array(
					'' => __( 'Each Items', 'jvfrmtd' ),
					'block'  => __( 'Each Blocks', 'jvfrmtd' ),
				),
				'frontend_available' => true,
			));

			$this->add_control('animation_delay', Array(
				'type' =>Controls_Manager::NUMBER,
				'label' => esc_html__( 'Animation Delay(ms)', 'jvfrmtd' ),
				'default' => '300',
				'condition' => Array(
					'animation_on_target' => 'block',
				),
				'frontend_available' => true,
			));

			$this->add_control('animation_speed', Array(
				'type' =>Controls_Manager::NUMBER,
				'label' => esc_html__( 'Animation Speed(ms)', 'jvfrmtd' ),
				'default' => '1000',
				'frontend_available' => true,
			));

			$this->add_control( 'loading_style', Array(
				'label' => esc_html__( 'Loading Style', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'jvfrmtd' ),
					'rect'  => __( 'Rectangle', 'jvfrmtd' ),
					'circle'  => __( 'Circle', 'jvfrmtd' ),
					'loading1'  => __( 'loading1', 'jvfrmtd' ),
					'loading2'  => __( 'loading2', 'jvfrmtd' ),
				]
			) );

		$this->end_controls_section();
	}

	public function add_portfolio_connector_controls() {
		$this->start_controls_section( 'section_portfolio_connector_setting', Array(
			'label' => esc_html__( "Protfolio Connector", 'jvfrmtd' ),
		) );
			$this->add_control( 'display_custom_post_id', Array(
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Display Custom Post ID', 'jvfrmtd' ),
			));
			$this->add_control( 'module_click_popup', Array(
				'label' => esc_html__( 'Enable a popup to show content', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			) );
		$this->end_controls_section();
	}

	public function add_block_style_control() {
		$this->start_controls_section( 'section_block_style', Array(
			'label' => __( 'Block Style', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'block_set_vscroll', Array(
			'label' => esc_html__( "Use Block Slimscroll Scrollbar", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'prefix_class' => 'slimscroll-',
			'default' => 'no',
		) );

		$this->add_control( 'minHeight', Array(
			'label' => esc_html__( "Min Height (px)", 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '',
			'selectors' => Array(
				'{{WRAPPER}} .item-list-page-wrap' => 'min-height: {{VALUE}}px;',
				'{{WRAPPER}} #javo-listings-wrapType-container' => 'min-height: {{VALUE}}px;',
				'{{WRAPPER}}.slimscroll-yes .item-list-page-wrap' => 'height: {{VALUE}}px; overflow:hidden;',
				'{{WRAPPER}}.slimscroll-yes .list-block-wrap' => 'height: {{VALUE}}px;  overflow:hidden;',
			),
			'condition' => [
				'block_set_vscroll' => 'yes',
			]
		) );

		$this->end_controls_section();

	}
	public function add_map_block_control() {
		$this->start_controls_section( 'section_map_block_settings', Array(
				'show_label' => false,
				'label' => esc_html__( "Module Settings", 'jvfrmtd' ),
			)
		);

			$this->add_control( 'count_first_loadmore', Array(
				'label' => esc_html__( 'First Load More Count', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '12',
				'frontend_available' => true,
			));

			$this->add_control( 'count_loadmore', Array(
				'label' => esc_html__( 'Load More Count', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '3',
				'frontend_available' => true,
			));

			$this->add_control( 'featured_first', array(
				'label' => esc_html__( 'First Featured Listings', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
			) );

		$this->end_controls_section();
	}

	public function add_listing_style_controls() {
		/** Filter Panel Style */
		$this->start_controls_section(
			'listing_wrap',
			[
				'label' => __( 'Listing Wrap', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'list_wrap_margin',
			[
				'label' => __( 'Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #results #spaces' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'list_wrap_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #results' => 'padding: 0;',
					'{{WRAPPER}} #results #spaces' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'list_wrap_border',
				'selector' => '{{WRAPPER}} .panel',
			]
		);

		$this->add_control(
			'List_wrap_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #results #spaces' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'list_wrap_shadow',
				'selector' => '{{WRAPPER}} #results #spaces',
			]
		);

		$this->add_control(
		'list_wrap_bg',
				[
						'label' => __( 'List Wrap Background Color', 'jvfrmtd' ),
						'type' => Controls_Manager::COLOR,
						'scheme' => [
								'type' => Scheme_Color::get_type(),
								'value' => Scheme_Color::COLOR_1,
						],
						'selectors' => [
								'{{WRAPPER}} #results #spaces' => 'background-color: {{VALUE}}',
						],
				]
		);

		$this->end_controls_section();
	}

	public function add_pagination_style_controls() {
		 /* Filter Title */
		$this->start_controls_section( 'section_pagination_style', Array(
			'label' => __( 'Pagination', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );

		if( self::PAGE_BLOCK == $this->get_name() ) {
			$this->add_control( 'pgn_btn_space', [
				'label' => __( 'Top Space', 'jvfrmtd' ),
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
					'{{WRAPPER}} ul.pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			] );
		}

		if( self::LIST_BLOCK == $this->get_name() ) {

			$this->add_control(
				'pgn_btn_bg',
				[
					'label' => __( 'Button Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
							'{{WRAPPER}} #results .javo-map-box-morebutton ' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'pgn_btn_border',
					'selector' => '{{WRAPPER}} #results .javo-map-box-morebutton',
				]
			);

			$this->add_control(
				'pgn_btn_radius',
				[
					'label' => __( 'Border Radius', 'jvfrmtd' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} #results .javo-map-box-morebutton' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
			'pgn_btn_color',
				[
					'label' => __( 'Text Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} #results .javo-map-box-morebutton ' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control( Group_Control_Typography::get_type(), [
			  'name' => 'pgn_btn_typography',
			  'selector' => '{{WRAPPER}} #results .javo-map-box-morebutton',
			  'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			] );
		}

		$this->end_controls_section();
	}

	public function add_filter_style_controls() {
		$this->start_controls_section( 'section_style_filter', [
			'label' => esc_html__( 'Filter Wrap, Title', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		] );


		$this->add_control(
		'filter_wrap_styles_heading',
			[
				'label' => __( 'Filter Wrap', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		 $this->add_responsive_control(
            'filter_wrap_color',
            [
                'label' => esc_html__( 'Filter Wrap Background Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
                'selectors' => [
					'{{WRAPPER}} .shortcode-header' => 'background-color: {{VALUE}};',
                ],
            ]
        );

		 $this->add_responsive_control(
			'filter_wrap_margin',
			[
				'label'      => esc_html__( 'Filter Margin', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	   => [
					'top' => 0,
					'right' => 0,
					'bottom' => 20,
					'left' => 0,
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .shortcode-header ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

	 	$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __( 'Filter border', 'jvfrmtd' ),
				'selector' => '{{WRAPPER}} .shortcode-header',
			]
		);

		 $this->add_responsive_control(
			'filter_wrap_radius',
			[
				'label'      => esc_html__( 'Filter Wrap Radius', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	   => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .shortcode-header ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		'filter_title_styles_heading',
			[
				'label' => __( 'Filter Title', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(Group_Control_Typography::get_type(),	[
				'name' => 'filter_title_typography',
				'label' => __( 'Title Typography', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title',
			]
		);

		$this->add_responsive_control(
			'filter_title_padding',
			[
				'label'      => esc_html__( 'Title Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	   => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .shortcode-header .shortcode-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'filter_title_color',
            [
                'label' => esc_html__( 'Title Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#fff',
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'filter_title_bg_color',
            [
                'label' => esc_html__( 'Title Background Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#f9c100',
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .javo-shortcode.filter-box .shortcode-header .shortcode-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );


		$this->add_control( 'filter_title_use_underline', Array(
			'label' => esc_html__( "Border & Underline (Title)", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'return_value' => 'yes'
		) );

		$this->add_responsive_control(
            'filter_title_underline_color',
            [
                'label' => esc_html__( 'Title Underline Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .shortcode-title' => 'border-color: {{VALUE}}; border-style:solid;',
					'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title:before' => 'background: {{VALUE}};',

                ],
				'condition' => [
					'filter_title_use_underline' => 'yes',
				],
            ]
        );

		$this->add_control(
			'filter_title_underline',
			[
				'label' => __( 'Title Border & Underline', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .shortcode-title' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'filter_title_use_underline' => 'yes',
				],
			]
		);

		$this->add_responsive_control( 'filter_header_line_width', [
			'label' => __( 'Custom Underline Width', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 20,
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
				'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title:before' => 'content: ""; width: {{SIZE}}{{UNIT}}; position: absolute;',
				'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title' => 'position:relative;',
			],
			'condition' => [
				'filter_title_use_underline' => 'yes',
			],
		] );

		$this->add_responsive_control( 'filter_header_line_height', [
			'label' => __( 'Custom Underline Height', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 3,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 30,
					'step' => 1,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title:before' => 'height: {{SIZE}}{{UNIT}}; bottom: -{{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'filter_title_use_underline' => 'yes',
			],
		] );


		$this->add_control( 'filter_middle_line', Array(
			'label' => esc_html__( "Use Middle Line", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'return_value' => 'yes'
		) );

		$this->add_responsive_control(
            'filter_middle_line_color',
            [
                'label' => esc_html__( 'Title Middle Line Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .shortcode-header' => 'position: relative;',
					'{{WRAPPER}} .shortcode-header:before' => 'content: ""; display: inline-block; position: absolute; top: 50%; margin-top: -1.5px; bottom: 0; left: 0; width: 100%; background: {{VALUE}};',
					'{{WRAPPER}} .shortcode-title' => 'z-index:1',
					'{{WRAPPER}} .shortcode-nav' => 'z-index:1; background:#fff;', // if there is a shortcode nav
				],
				'condition' => [
					'filter_middle_line' => 'yes',
				],
            ]
        );

		$this->add_responsive_control(
            'filter_middle_line_height',
            [
                'label' => esc_html__( 'Middle Line height', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 3,
					'unit' => 'px',
				],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px' ],
                'selectors' => [
						'{{WRAPPER}} .shortcode-header:before' => 'height: {{SIZE}}{{UNIT}};',
                ],
				'condition' => [
					'filter_middle_line' => 'yes',
				],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_filter_items', [
			'label' => esc_html__( 'Filter Items', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		] );


		$this->add_control( 'hide_filter_items', Array(
			'label' => esc_html__( "Hide Filter Items", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Hide', 'your-plugin' ),
			'label_off' => __( 'Show', 'your-plugin' ),
			'return_value' => 'yes',
		    'selectors' => [
				'{{WRAPPER}} .shortcode-header .shortcode-nav' => 'display:none;',
            ],
		) );

        $this->add_control(
            'filter_items_alignment',
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
                'default' => 'right',
                'selectors' => [
                    '{{WRAPPER}} ul.shortcode-filter' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'filter_items_spacing_title_items',
            [
                'label' => esc_html__( 'Space (Title and items)', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
						'{{WRAPPER}} .shortcode-header .shortcode-title' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
				'condition' => [
					'hide_filter_items!' => 'yes',
				],
            ]
        );


		  $this->add_responsive_control(
            'filter_items_spacing',
            [
                'label' => esc_html__( 'Space each items', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 7,
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
						'{{WRAPPER}} ul.shortcode-filter > li' => 'margin: 0 {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore' => 'margin-right: 0px;',
                ],
			  'condition' => [
					'hide_filter_items!' => 'yes',
				],
            ]
        );

		  $this->add_responsive_control(
            'filter_items_height',
            [
                'label' => esc_html__( 'Height', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
                'range' => [
                    'px' => [
                        'min' => 8,
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
						'{{WRAPPER}} ul.shortcode-filter > li' => 'height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .flexMenu-popup' => 'top: {{SIZE}}{{UNIT}};',
                ],
			  'condition' => [
					'hide_filter_items!' => 'yes',
				],
            ]
        );

		$this->add_control( 'use_underline', Array(
			'label' => esc_html__( "Use underline (Hover)", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'condition' => [
					'hide_filter_items!' => 'yes',
			],
		) );


		$this->start_controls_tabs( 'fiter_item_tabs');

		$this->start_controls_tab( 'filter_item_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
				'condition' => [
					'hide_filter_items!' => 'yes',
				],
			]
		);


        $this->add_responsive_control(
            'filter_items_color',
            [
                'label' => esc_html__( 'Filter Tax Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#7a7a7a',
                'selectors' => [
					'{{WRAPPER}} ul.shortcode-filter > li' => 'color: {{VALUE}};',
					'{{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore li' => 'color: {{VALUE}};',
                ],
				'condition' => [
					'hide_filter_items!' => 'yes',
				],
            ]
        );

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'filter_itmes_typography',
				'label' => __( 'Items Typography', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ul.shortcode-filter > li, {{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore li',
				'condition' => [
					'hide_filter_items!' => 'yes',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'filter_item_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
				'condition' => [
					'hide_filter_items!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
            'filter_items_hover_color',
            [
                'label' => esc_html__( 'Filter Hover Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#aaa',
                'selectors' => [
					'{{WRAPPER}} ul.shortcode-filter > li:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore li:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
				'prefix_class' => 'filter-nav-item',
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'filter_itmes_hover_typography',
				'label' => __( 'Items Typography', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' =>	'{{WRAPPER}} ul.shortcode-filter > li:hover, {{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore li:hover',
			]
		);


		$this->add_responsive_control(
            'underline_color_hover',
            [
                'label' => esc_html__( 'Underline Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#000',
                'selectors' => [
					'{{WRAPPER}} ul.shortcode-filter > li:hover' => 'border-bottom: 2px solid {{VALUE}};',
					'{{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore' => 'border-bottom: 0px;',
					'{{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore:hover' => 'border-bottom: 0px;',
                ],
				'condition' => [
					'use_underline' => 'yes',
				],
            ]
        );

		$this->end_controls_tab();
		$this->start_controls_tab( 'filter_item_actived',
			[
				'label' => __( 'Actived', 'jvfrmtd' ),
				'condition' => [
					'hide_filter_items!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
            'filter_items_actived_color',
            [
                'label' => esc_html__( 'Filter Actived Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#222',
                'selectors' => [
					'{{WRAPPER}} ul.shortcode-filter > li.current' => 'color: {{VALUE}};',
					'{{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore li.current' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'filter_itmes_actived_typography',
				'label' => __( 'Items Typography', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ul.shortcode-filter > li.current, {{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore li.current',
			]
		);

		$this->add_responsive_control(
            'underline_color_actived',
            [
                'label' => esc_html__( 'Underline Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#000',
                'selectors' => [
					'{{WRAPPER}} ul.shortcode-filter > li.current' => 'border-bottom: 2px solid {{VALUE}};',
					'{{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore' => 'border-bottom: 0px;',
					'{{WRAPPER}} ul.shortcode-filter > li.flexMenu-viewMore.current' => 'border-bottom: 0px;',
                ],
				'condition' => [
					'use_underline' => 'yes',
				],
            ]
        );

		$this->end_controls_tab();

		$this->end_controls_tabs();



		$this->end_controls_section();
	}

	public function get_filter_option_controls() {
		 /* Filter options Style */
		$this->start_controls_section(
		  'filter_options',
		  [
			'label' => __( 'Filter Options', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		  ]
		);

		$this->add_control(
			'filter_panel_body_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
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
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .panel-body' => 'background-color: {{VALUE}}',
				],
			]
		);

	    $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_selector_border',
				'selector' => '{{WRAPPER}} .form-control',
			]
		);

		$this->add_control(
		'body_color',
			[
				'label' => __( 'Title Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .form-control' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'body_typography',
			'selector' => '{{WRAPPER}} .form-control',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->end_controls_section();
	}

	public function get_custom_code_controls() {
		 $this->start_controls_section(
            'section_style_custom',
            [
                'label' => esc_html__( 'Custom Code', 'jvfrmtd' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'custom_css',
            [
                'label' => esc_html__( 'Custom CSS', 'jvfrmtd' ),
                'type' => Controls_Manager::TEXTAREA,
			    'default' => __( 'Add your own CSS code', 'jvfrmtd' ),
                'selectors' => [
                    '' => '{{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();
	}

	public function getSliderOption() {
		$output = Array();
		foreach( Array( 'speed', 'autoplay', 'autoplay_speed', 'effect', 'paginationType', 'spaceBetween', 'slidesPerView', 'loop' ) as $setting ) {
			$output[ $setting ] = $this->get_settings( $setting );
		}
		$output = array_filter( $output );
		return wp_json_encode( $output, JSON_NUMERIC_CHECK );
	}

}