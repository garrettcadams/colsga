<?php

class jvbpd_elementor_page_settings {

	Const TEMPLATE = 'jvbpd_template_type';

	public $controls = Array(
		'style' => Array(),
		'setting' => Array(),
	);
	public static $instance = NULL;

	public function __construct() {
		$this->reigster_style_controls();
		$this->reigster_setting_controls();
		$this->register_hooks();
	}

	public function add_control( string $type='setting', string $name, array $args=Array(), array $condition=Array() ) {
		$this->controls[ $type ][] = Array(
			'name' => $name,
			'control_args' => $args,
			'condition' => $condition,
		);
	}

	public function add_page_control( string $name, array $args=Array() ) {
		$this->add_control( 'setting', $name, $args, Array( 'post_type' => 'page' ) );
	}

	public function add_page_builder_control( string $name ='', string $template='', array $args=Array() ) {
		$this->add_control( 'setting', $name, $args, Array( 'post_type' => 'jvbpd-listing-elmt', 'template' => $template ) );
	}

	public function add_page_builder_sticky_control( string $name ='', string $template='', array $args=Array() ) {
		$this->add_control( 'sticky', $name, $args, Array( 'post_type' => 'jvbpd-listing-elmt', 'template' => $template ) );
	}

	public function add_posts_style( string $name, array $args=Array() ) {
		$this->add_control( 'style', $name, $args, Array() );
	}

	public function add_page_style( string $name, array $args=Array(), string $post_type='page' ) {
		$this->add_control( 'style', $name, $args, Array( 'post_type' => $post_type ) );
	}

	public function add_custom_style( string $name, array $args=Array(), array $param=Array() ) {
		$this->add_control( 'style', $name, $args, $param );
	}

	public function add_responsive_page_style( string $name, array $args=Array(), string $post_type='page' ) {
		$this->add_control( 'style', $name, $args, Array( 'post_type' => $post_type, 'responsive' => true ) );
	}

	public function add_page_builder_style( string $name ='', string $template='', array $args=Array() ) {
		$this->add_control( 'style', $name, $args, Array( 'post_type' => 'jvbpd-listing-elmt', 'template' => $template ) );
	}

	public function get_controls( string $type='setting' ) { return array_key_exists( $type, $this->controls ) ? $this->controls[$type] : array(); }

	public function register_hooks() {
		add_action(
			'elementor/documents/register_controls',
			Array( $this, 'register_page_settings_control' )
		);
		/*
		add_action(
			'elementor/element/post/document_settings/before_section_end',
			Array( $this, 'register_page_settings_control' ), 10, 2
		);
		add_action(
			'elementor/frontend/element/before_render',
			Array( $this, 'custom_module_container' )
		); */
	}

	public function reigster_style_controls() {

		/**
		 *
		 * Normal page
		 */
		$this->add_posts_style( 'hide_header', Array(
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__( "Hide header", 'jvfrmtd' ),
			'return_value' => 'yes',
			'selectors' => Array(
				'{{WRAPPER}} div.header.header-elementor' => 'display:none; visibility:hidden; opacity:0;',
				/* '{{WRAPPER}}.elementor-editor-active nav.navbar-static-top' => 'display:none; visibility:hidden; opacity:0;', */
			),
		) );

		$this->add_page_builder_style( 'header_position', 'custom_header', Array(
			'type' => \Elementor\Controls_Manager::SELECT,
			'label' => esc_html__( "Header style", 'jvfrmtd' ),
			'default' => 'static',
			'options' => Array(
				'static' => esc_html__( "Static", 'jvfrmtd' ),
				'absolute' => esc_html__( "Absolute", 'jvfrmtd' ),
				'relative' => esc_html__( "Relative", 'jvfrmtd' ),
			),
			'template' => 'custom_header',
			'selectors' => Array(
				'body .header-elementor.header-id-{{ID}}' => 'position:{{VALUE}}; width:100%; z-index:1000;',
				'body.admin-bar' => 'top:32px !important;',
			),
		) );

		/**
		 *
		 * Page Builder
		 */
		$this->add_custom_style( 'section_wrap_min_width', Array(
			'label' => esc_html__( "Max Width", 'jvfrmtd' ),
			'type' => \elementor\Controls_Manager::SLIDER,
			'default' => Array(
				'size' => 700,
				'unit' => 'px',
			),
			'range' => Array(
				'px' => Array(
					'min' => 100,
					'max' => 2000,
					'step' => 1,
				),
				'%' => Array(
					'min' => 0,
					'max' => 100,
				),
			),
			'size_units' => Array( 'px', '%' ),
			'selectors' => Array(
				'.elementor-{{ID}} .elementor-section-wrap' => 'max-width:{{SIZE}}{{UNIT}};',
				'.elementor-edit-mode.elementor-{{ID}} .elementor-section-wrap' => 'margin:0 auto;',
			),
		), Array(
			'template' => Array( 'custom_module', 'custom_login', 'custom_signup', 'custom_preview' ),
			'post_type' => 'jvbpd-listing-elmt',
			'responsive' => true
		) );
	}

	public function reigster_setting_controls() {
		/*
		$this->add_page_builder_sticky_control( 'header_sticky', 'custom_header', Array(
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__( "Header Sticky", 'jvfrmtd' ),
		) );

		$this->add_page_builder_sticky_control( 'header_sticky_after_height', 'custom_header', Array(
			'type' => \Elementor\Controls_Manager::SLIDER,
			'label' => esc_html__( "Sticky header spacing", 'jvfrmtd' ),
			'default' => Array(
				'size' => 100,
				'unit' => 'px',
			),
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				),
				'%' => Array(
					'min' => 0,
					'max' => 100,
				),
			),
			'size_units' => Array( 'px', '%' ),
			'condition' => Array(
				'header_sticky' => 'yes'
			),
			'selectors' => Array(
				'.header-elementor.header-id-{{ID}} > .is-sticky .header-elementor-wrap' => 'padding:{{SIZE}}{{UNIT}} 0;',
			)
		) );

		$this->add_page_builder_sticky_control( 'header_sticky_offset', 'custom_header', Array(
			'type' => \Elementor\Controls_Manager::SLIDER,
			'label' => esc_html__( "Sticky Header Offset", 'jvfrmtd' ),
			'default' => Array(
				'size' => 0,
				'unit' => 'px',
			),
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				),
				'%' => Array(
					'min' => 0,
					'max' => 100,
				),
			),
			'size_units' => Array( 'px', '%' ),
			'condition' => Array(
				'header_sticky' => 'yes'
			),
			'frontend_available' => true,
		) );

		$this->add_page_builder_sticky_control( 'header_background_color', 'custom_header', Array(
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__( "Header Background Color", 'jvfrmtd' ),
			'template' => 'custom_header',
			'condition' => Array(
				'header_sticky' => 'yes'
			),
			'selectors' => Array(
				'.header-elementor.header-id-{{ID}} .header-elementor-wrap' => 'background-color:{{VALUE}};',
			)
		) );

		$this->add_page_builder_sticky_control( 'header_sticky_background_color', 'custom_header', Array(
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__( "Header Sticky Background Color", 'jvfrmtd' ),
			'template' => 'custom_header',
			'condition' => Array(
				'header_sticky' => 'yes'
			),
			'selectors' => Array(
				'.header-elementor.header-id-{{ID}} > .is-sticky .header-elementor-wrap' => 'background-color:{{VALUE}};',
			)
		) );

		$this->add_page_builder_control( 'header_sticky_main_menu_color', 'custom_header', Array(
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__( "Header Sticky Main Menu Color", 'jvfrmtd' ),
			'template' => 'custom_header',
			'condition' => Array(
				'header_sticky' => 'yes'
			),
			'selectors' => Array(
				'.header-elementor.header-id-{{ID}} > .is-sticky li.main-menu-item.menu-item-depth-0 > a > span.menu-titles' =>
				'color:{{VALUE}};',
			),
		) ); */

		$map_type_args = Array(
			'lava_lv_listing_map_type' => Array(
				'label' => __( 'List Type', 'jvfrmtd' ),
				'type' => \elementor\Controls_Manager::SELECT,
				'default' => 'maps',
				'options' => Array(
					'maps' => esc_html__( "Map", 'jvfrmtd' ),
					'listings' => esc_html__( "List", 'jvfrmtd' ),
				),
			),
			'map_distance_unit' => Array(
				'label' => __( 'Map Distance Type', 'jvfrmtd' ),
				'type' => \elementor\Controls_Manager::SELECT,
				'default' => 'km',
				'options' => Array(
					'km' => esc_html__( "KM", 'jvfrmtd' ),
					'mile' => esc_html__( "Mile", 'jvfrmtd' ),
				),
			),
		);

		foreach( $map_type_args as $map_opt_key => $map_type_arg ) {
			$this->add_page_builder_control( $map_opt_key, 'listing_archive', $map_type_arg );
			$map_type_arg[ 'condition' ] = Array(
				'template' => 'lava_lv_listing_map',
			);
			$this->add_page_control( $map_opt_key, $map_type_arg );
		}

	}

	public function register_page_settings_control( $document ){
		foreach( array_keys( $this->controls ) as $type ) {
			if( empty( $this->get_controls( $type ) ) ) {
				continue;
			}

			if('style' == $type) {
				$document->start_controls_section( 'section_jvbpd_page_style', Array(
					'label' => esc_html__( "Javo Page Style", 'jvfrmtd' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				) );
			}elseif('sticky' == $type){
				$document->start_controls_section( 'section_jvbpd_sticky', Array(
					'label' => esc_html__( "Sticky", 'jvfrmtd' ),
					'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
				) );
			}else{
				$document->start_controls_section( 'section_jvbpd_page_setting', Array(
					'label' => esc_html__( "Javo Page Settings", 'jvfrmtd' ),
					'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
				) );
			}
			foreach( $this->get_controls( $type ) as $control ) {
				$condition = $control[ 'condition' ];

				if( isset( $condition[ 'post_type' ] ) ) {
					$PostTypes = $condition[ 'post_type' ];
					if( ! is_array( $PostTypes ) ) {
						$PostTypes = Array( $PostTypes );
					}
					if( ! in_array( $document->get_main_post()->post_type, $PostTypes ) ) {
						continue;
					}
				}

				if( isset( $condition[ 'template' ] ) ) {
					$templates = $condition[ 'template' ];
					if( ! is_array( $condition[ 'template' ] ) ) {
						$templates = Array( $condition[ 'template' ] );
					}
					if( ! in_array( get_post_meta( $document->get_main_id(), self::TEMPLATE, true ), $templates ) ) {
						continue;
					}
				}

				if( isset( $control[ 'control_args' ][ 'selectors' ] ) && is_array( $control[ 'control_args' ][ 'selectors' ] ) ) {
					foreach( $control[ 'control_args' ][ 'selectors' ] as $selectorIndex => $cssProperty ) {
						$newSelectorIndex = str_replace( '{{ID}}', $document->get_main_id(), $selectorIndex );
						unset( $control[ 'control_args' ][ 'selectors' ][ $selectorIndex ] );
						$control[ 'control_args' ][ 'selectors' ][ $newSelectorIndex ] = $cssProperty;
					}
				}

				if( isset( $condition[ 'responsive' ] ) && $condition[ 'responsive' ] ) {
					$document->add_responsive_control( $control[ 'name' ], $control[ 'control_args' ] );
				}else{
					$document->add_control( $control[ 'name' ], $control[ 'control_args' ] );
				}
			}
			$document->end_controls_section();
		}
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}

function jvbpd_el_page_settings() {
	return jvbpd_elementor_page_settings::getInstance();
}