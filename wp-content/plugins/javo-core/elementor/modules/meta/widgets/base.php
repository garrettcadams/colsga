<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use jvbpdelement\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

abstract class Base extends Base_Widget {

	Const ACF_META = 'jvbpd-acf-detail';
	Const MODULE_META = 'jvbpd-module-meta';
	Const MODULE_CARD = 'jvbpd-module-card';
	Const BLOCK_MEDIA = 'jvbpd-block-media';
	Const ARCHIVE_META = 'jvbpd-archive-meta';
	Const POST_META = 'jvbpd-single-post-base-meta';
	Const LISTING_META = 'jvbpd-single-listing-base-meta';
	Const LISTING_FIELD = 'jvbpd-submit-listing-field';

	public $input = Array( self::LISTING_FIELD );
	public $output = Array( self::ACF_META, self::LISTING_META, self::POST_META );

	public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function add_field_controls() {
		$this->start_controls_section( 'section_field', Array(
			'label' => __('Field Settings', 'jvfrmtd'),
		) );

		$this->add_control( 'field_type', Array(
			'label' => esc_html__( "Field type", 'jvfrmtd' ),
			'description' => '',
			'type' => Controls_Manager::SELECT,
			'default' => 'base',
			'multiple' => false,
			'options' => Array(
				'base' => esc_html__( "Listing meta", 'jvfrmtd' ),
				/// 'acf' => esc_html__( "ACF", 'jvfrmtd' ),
			),
		) );

		// if( self::ACF_META == $this->get_name() ) {
			jvbpd_elements_tools()->getACFOptions( $this, Array( 'field_type' => 'acf' ) );
		// }
		// if( self::LISTING_META == $this->get_name() ) {
			$this->getListingBaseControl();
		// }

		$this->add_control( 'custom_meta', Array(
			'label'   => __( 'Custom Meta', 'jvfrmtd' ),
			'type'    => Controls_Manager::CODE,
			'language' => 'html',
			'condition' => Array(
				'meta' => 'custom_meta',
			),
		) );

		$this->add_control('field_image_size', Array(
			'label'   => __( 'Custom Meta', 'jvfrmtd' ),
			'type'    => Controls_Manager::SELECT,
			'options' => jvbpd_elements_tools()->get_image_sizes(),
			'default' => 'thumbnail',
			'condition' => Array(
				'meta' => '_logo',
			),
		));

		$this->end_controls_section();
	}

	public function add_rating_control() {
		$this->start_injection(Array( 'type' => 'control', 'of' => 'meta_key' ) );
			$this->add_control( 'rating_type', Array(
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__("Rating Type", 'jvfrmtd'),
				'default' => 'image',
				'condition' => Array( 'meta_key' => 'rating'),
				'separator' => 'before',
				'prefix_class' => 'rating-type-',
				'options' => Array(
					'image' => esc_html__("Image", 'jvfrmtd'),
					'icon' => esc_html__("Icon", 'jvfrmtd'),
					'number' => esc_html__("Number", 'jvfrmtd'),
					'star_number' => esc_html__("Star + Rating", 'jvfrmtd'),
				),
			));
			$this->add_control( 'rating_icon_icon_base_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Rating Icon Base Color", 'jvfrmtd'),
				'condition' => Array( 'rating_type' => 'icon'),
				'default' => '#FFFFFF',
				'selectors' => Array(
					'{{WRAPPER}}.rating-type-icon .rating-wrap i.fa-star-o' => 'color:{{VALUE}};',
				),
			));
			$this->add_control( 'rating_icon_icon_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Rating Icon Color", 'jvfrmtd'),
				'condition' => Array( 'rating_type' => 'icon'),
				'default' => '#000000',
				'selectors' => Array(
					'{{WRAPPER}}.rating-type-icon .rating-wrap i.fa-star' => 'color:{{VALUE}};',
				),
			));
			$this->add_control( 'rating_number_bg_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Rating Number Background Color", 'jvfrmtd'),
				'condition' => Array( 'rating_type' => 'number'),
				'default' => '#000000',
				'selectors' => Array(
					'{{WRAPPER}}.rating-type-number .rating-wrap' => 'background-color:{{VALUE}}; display:inline-block; padding:5px 10px;',
				),
			));
			$this->add_control( 'rating_number_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Rating Number Color", 'jvfrmtd'),
				'condition' => Array( 'rating_type' => 'number'),
				'default' => '#FFFFFF',
				'selectors' => Array(
					'{{WRAPPER}}.rating-type-number .rating-wrap' => 'color:{{VALUE}};',
				),
			));
		$this->end_injection();
	}

	public function add_price_control() {
		$this->start_injection(Array( 'type' => 'control', 'of' => 'meta_key' ) );
		$this->add_control( 'price_currency_unit', Array(
			'type' => Controls_Manager::SELECT2,
			'label' => esc_html__("Currency Unit", 'jvfrmtd'),
			'options' => Array(
				'' => esc_html__("None", 'jvfrmtd'),
				'before' => esc_html__("Before", 'jvfrmtd'),
				'After' => esc_html__("After", 'jvfrmtd'),
			),
			'condition' => Array(
				'meta_key' => Array('_price', '_sale_price', '_print_price'),
			),
		));
		$this->end_injection();
	}

	public function add_more_tax_control() {
		$this->start_injection(Array( 'type' => 'control', 'of' => 'meta_key' ) );
		$this->add_control( 'more_taxonomy', Array(
			'type' => Controls_Manager::SELECT2,
			'label' => esc_html__("Taxonomy", 'jvfrmtd'),
			'options' => Array('' => esc_html__("None", 'jvfrmtd')) +jvbpd_elements_tools()->getMoreTaxonoiesOptions(),
			'default' => '',
			'condition' => Array(
				'meta_key' => 'more_tax',
			),
		));
		$this->add_control( 'more_taxonomy_display', Array(
			'type' => Controls_Manager::SELECT2,
			'label' => esc_html__("Display Type", 'jvfrmtd'),
			'default' => 'single',
			'options' => Array(
				'single' => esc_html__("The 1st on by alphabet", 'jvfrmtd'),
				'multiple' => esc_html__("All selected terms", 'jvfrmtd'),
			),
			'condition' => Array(
				'meta_key' => 'more_tax',
			),
		));
		$this->add_control( 'more_taxonomy_link', Array(
			'type' => Controls_Manager::SELECT2,
			'label' => esc_html__("Link", 'jvfrmtd'),
			'default' => '',
			'options' =>  array_flip( apply_filters(
				'jvbpd_get_map_templates',
				Array( esc_html__( "Default Search Page", 'jvfrmtd' ) => '' )
			) ),
			'condition' => Array(
				'meta_key' => 'more_tax',
			),
		));
		$this->end_injection();
	}

	public function moreBaseMeta() {
		return Array(
			'post_author' => esc_html__( "Author", 'jvfrmtd' ),
			'post_date' => esc_html__( "Post Date", 'jvfrmtd' ),
			'post_category' => esc_html__( "Post Category", 'jvfrmtd' ),
			'post_tags' => esc_html__( "Post Tags", 'jvfrmtd' ),
			'_rating_average' => esc_html__( "Rating Average", 'jvfrmtd' ),
			'_favorite' => esc_html__( "Favorite", 'jvfrmtd' ),
			'_print_price' => esc_html__("Price(Sale)", 'jvfrmtd' ),
			'custom_meta' => esc_html__( "Custom Meta", 'jvfrmtd' ),
			'more_tax' => esc_html__( "More taxonomy", 'jvfrmtd' ),
		);
	}

	public function getListingBaseMetaOptions() {
		$output = Array( '' => esc_html__( "Select a field", 'jvfrmtd' ) );
		// if( self::LISTING_FIELD == $this->get_name() ) {
			$output = wp_parse_args(
				Array(
					'txt_title' => esc_html__( "Post title", 'jvfrmtd' ),
					'_tagline' => esc_html__( "Tagline", 'jvfrmtd' ),
					'txt_content' => esc_html__( "Post Content", 'jvfrmtd' ),
				), $output
			);
		// }
		$output = wp_parse_args( jvbpd_elements_tools()->getListingMetaFields(), $output );
		if( self::LISTING_META == $this->get_name() ) {
			$output = wp_parse_args( $this->moreBaseMeta(), $output );
		}
		$output = wp_parse_args(
			Array(
				'_featured' => esc_html__( "Featured Image", 'jvfrmtd' ),
				'_logo' => esc_html__( "Logo", 'jvfrmtd' ),
			), $output
		);
		if( self::LISTING_FIELD == $this->get_name() ) {
			$output = wp_parse_args(
				Array(
					'_detail_images' => esc_html__( "Detail Images", 'jvfrmtd' ),
					'map' => esc_html__( "Map information", 'jvfrmtd' ),
					'more_tax' => esc_html__( "More taxonomy", 'jvfrmtd' ),
					'custom_field' => esc_html__( "Custom Field", 'jvfrmtd' ),
					'user_join_form' => esc_html__( "User Join Form (Not user logged in)", 'jvfrmtd' ),
				), $output
			);
		}
		return $output;
	}

	protected function getListingBaseControl() {
		$this->add_control( 'meta', Array(
			'label' => esc_html__( "Base field", 'jvfrmtd' ),
			'description' => '',
			'type' => Controls_Manager::SELECT2,
			'multiple' => false,
			'default' => '',
			'condition' => Array( 'field_type' => 'base' ),
			'options' => $this->getListingBaseMetaOptions(),
		) );
	}

	protected function this_get_field_control() { return true; }

	protected function _register_controls() {

		if( false !== $this->this_get_field_control() ) {
			$this->add_field_controls();
		}

		if( in_array( $this->get_name(), $this->output ) ) {
			$this->add_html_tag_controls();
			$this->add_prefix_settings_control();
		}

		$this->start_controls_section( 'section_icon', [
			'label' => __( 'JV Icons', 'jvfrmtd' ),
		] );

			$this->add_control( 'view', [
				'label' => __( 'View', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'jvfrmtd' ),
					'stacked' => __( 'Stacked', 'jvfrmtd' ),
					'framed' => __( 'Framed', 'jvfrmtd' ),
				],
				'default' => 'default',
				'prefix_class' => 'elementor-view-',
			] );

			/*
			require  jvbpdCore()->elementor_path . '/jv-icons.php';
			$this->add_control( 'icon', [
			'label' => __( 'Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::ICON,
				'default' => '',
				'options' => get_jv_icons_options( $icons ),
				'include' => get_jv_icons( $icons ),
				'description' => __('It may take time to load all icons', 'jvfrmtd'),
			] ); */

			$this->add_control( 'icons', [
				'label' => __( 'Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => Array(
					'value' => '',
					'library' => '',
				),
			] );

			$this->add_control( 'shape', [
				'label' => __( 'Shape', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
				'circle' => __( 'Circle', 'jvfrmtd' ),
				'square' => __( 'Square', 'jvfrmtd' ),
				],
				'default' => 'circle',
				'condition' => [
				'view!' => 'default',
				],
				'prefix_class' => 'elementor-shape-',
			] );

			$this->add_control( 'link', [
				'label' => __( 'Link', 'jvfrmtd' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'jvfrmtd' ),
			] );

			$this->add_responsive_control( 'align', [
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-wrapper' => 'text-align: {{VALUE}};',
				],
			] );


			$this->add_responsive_control( 'icon-top-padding', [
				'label' => __( 'Top Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size'	=> '0',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-wrapper' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			] );

			$this->add_responsive_control( 'icon-right-padding', [
				'label' => __( 'Right Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size'	=> '10',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-wrapper' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			] );


			//---------- Icon Style Tabs ---------//
			$this->add_control( 'icon_style_heading', [
				'label' => __( 'Icon Styles', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			] );

		$this->start_controls_tabs( 'icon_styles' );
		$this->start_controls_tab( 'icon_normal', [ 'label' => __( 'Normal', 'jvfrmtd' ) ] );

			$this->add_control( 'primary_color', [
				'label' => __( 'Primary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
				'{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			] );

			$this->add_control( 'secondary_color', [
				'label' => __( 'Secondary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'view!' => 'default',
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
				],
			] );

			$this->add_control( 'size', [
				'label' => __( 'Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size'	=> '10',
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			] );

		$this->add_control( 'icon_padding', [
			'label' => __( 'Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'selectors' => [
				'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
			],
			'range' => [
				'em' => [
					'min' => 0,
					'max' => 5,
				],
			],
			'condition' => [
				'view!' => 'default',
			],
		] );

		$this->add_control( 'rotate', [
			'label' => __( 'Rotate', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 0,
				'unit' => 'deg',
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
			],
		] );

		$this->add_control( 'border_width', [
			'label' => __( 'Border Width', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'selectors' => [
				'{{WRAPPER}} .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'view' => 'framed',
			],
		] );

		$this->add_control( 'border_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'view!' => 'default',
			],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_hover', [ 'label' => __( 'Hover', 'jvfrmtd' ) ] );

		$this->add_control( 'hover_primary_color', [
			'label' => __( 'Primary Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '',
			'selectors' => [
			'{{WRAPPER}}.elementor-view-stacked .elementor-icon:hover' => 'background-color: {{VALUE}};',
			'{{WRAPPER}}.elementor-view-framed .elementor-icon:hover, {{WRAPPER}}.elementor-view-default .elementor-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'hover_secondary_color', [
			'label' => __( 'Secondary Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '',
			'condition' => [
				'view!' => 'default',
			],
			'selectors' => [
				'{{WRAPPER}}.elementor-view-framed .elementor-icon:hover' => 'background-color: {{VALUE}};',
				'{{WRAPPER}}.elementor-view-stacked .elementor-icon:hover' => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'hover_animation', [
			'label' => __( 'Hover Animation', 'jvfrmtd' ),
			'type' => Controls_Manager::HOVER_ANIMATION,
		] );

		$this->end_controls_tab();
		$this->end_controls_tabs();
		//---------- Icon Style Tabs ---------//
		$this->end_controls_section();


		$this->start_controls_section( 'section_empty_msg', [
			'label' => __( 'Empty Message', 'jvfrmtd' ),
			'description'  => __( 'Empty Value', 'jvfrmtd' ),
		] );

		$this->add_control( 'show_msg', [
			'label' => __( 'Show this message for preview', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Hide', 'jvfrmtd' ),
			'label_off' => __( 'Show', 'jvfrmtd' ),
			'return_value' => 'yes',
			'description'	=> __('Show only preview page. This message shows when this data is empty or not added in real pages.', 'jvfrmtd'),
		]);


		$this->add_control( 'empty_msg', [
			'label'       => __( 'Message', 'jvfrmtd' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => __( 'No data', 'jvfrmtd' ),
			'placeholder' => __( 'Type a message here', 'jvfrmtd' ),
			'description' => __('It shows when this data is empty or not added', 'jvfrmtd'),
		] );

		$this->add_control( 'msg_color', [
			'label' => __( 'Message Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .item-value .no-data' => 'color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'empty_msg_typography',
			'selector' => '{{WRAPPER}} .item-value .no-data',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );
		$this->end_controls_section();


		//Style
		$this->start_controls_section( 'section_open_hours', [
			'label' => __( 'Open Hours Settings', 'jvfrmtd' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'meta_key' => 'open_hours',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'open_typography',
			'label' => __('Open Typography','jvfrmtd'),
			'selector' => '{{WRAPPER}} .working-hours.open',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->add_control( 'open_color', [
			'label' => __( 'Open Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#fff',
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .working-hours.open' => 'color: {{VALUE}}',
			],
		] );

		$this->add_control( 'open_bg_color', [
			'label' => __( 'Open Background Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#fff',
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .working-hours.open' => 'background-color: {{VALUE}}',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'acf_style', [
			'label' => __( 'Style', 'jvfrmtd' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'label_height', [
			'label' => __( 'Label Height', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 0,
				'unit' => 'px',
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
				/*
				'{{WRAPPER}} .single-item' => 'height: {{SIZE}}{{UNIT}}; vertical-align:middle;',
				'{{WRAPPER}} .item-label' => 'height: {{SIZE}}{{UNIT}}; vertical-align:middle;',
				'{{WRAPPER}} .item-value' => 'height: {{SIZE}}{{UNIT}}; vertical-align:middle;', */
				'{{WRAPPER}} .single-item' => 'min-height: {{SIZE}}{{UNIT}}; vertical-align:middle;',
				'{{WRAPPER}} .item-label' => 'min-height: {{SIZE}}{{UNIT}}; vertical-align:middle;',
				'{{WRAPPER}} .item-value' => 'min-height: {{SIZE}}{{UNIT}}; vertical-align:middle;',
			],
		] );

		$this->start_controls_tabs( 'label_value_tab' );
		$this->start_controls_tab( 'value', [ 'label' => __( 'Value', 'jvfrmtd' ) ] );

		/* value */
		$this->add_control( 'value_color', [
			'label' => __( 'Title Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#878787',
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .item-value, {{WRAPPER}} .item-value a' => 'color: {{VALUE}}',
			],
		] );

		$this->add_control( 'value_color_hover', [
			'label' => __( 'Hover Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#6a71a3',
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .item-value:hover, {{WRAPPER}} .item-value a:hover' => 'color: {{VALUE}}',
			],
			'condition' => [
				'meta_key' => 'favorite',
			]
		] );

		$this->add_control( 'value_selected_color', [
			'label' => __( 'Favorite Saved Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#4c58a4',
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .item-value a.lava-favorite.saved' => 'color: {{VALUE}}',
			],
			'condition' => [
				'meta_key' => 'favorite',
			]
		] );


		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'value_typography',
			'selector' => '{{WRAPPER}} .item-value, {{WRAPPER}} .item-value a',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->add_control( 'value_width', [
			'label' => __( 'Value Width', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 100,
				'unit' => '%',
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
				'{{WRAPPER}} .item-value' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );


		$this->add_responsive_control( 'value_align', [
			'label' => __( 'Alignment', 'jvfrmtd' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'flex-start' => [
					'title' => __( 'Left', 'jvfrmtd' ),
					'icon' => 'fa fa-align-left',
				],
				'center' => [
					'title' => __( 'Center', 'jvfrmtd' ),
					'icon' => 'fa fa-align-center',
				],
				'flex-end' => [
					'title' => __( 'Right', 'jvfrmtd' ),
					'icon' => 'fa fa-align-right',
				],
			],
			'selectors' => [
				'{{WRAPPER}} .item-value' => 'justify-content: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();
		$this->start_controls_tab( 'label_tab', [ 'label' => __( 'Label', 'jvfrmtd' ) ] );
		/* Label */
		$this->add_control( 'show_label', [
			'label' => __( 'Show Label', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'label_off',
			'label_on' => __( 'Show', 'jvfrmtd' ),
			'label_off' => __( 'Hide', 'jvfrmtd' ),
			'return_value' => 'yes',
			/*
			'selectors' => [
				'{{WRAPPER}} .item-label' => 'display:none;',
			], */
		]);

		$this->add_control('field_label_type', Array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__( 'Label Type', 'jvfrmtd' ),
			'default' => '',
			'options' => Array(
				'' => esc_html("Default", 'jvfrmtd'),
				'custom' => esc_html("Custom label", 'jvfrmtd'),
			),
			'condition' => Array(
				'show_label' => 'yes',
			),
		));

		$this->add_control( 'field_label', Array(
			'label' => esc_html__( 'Label name', 'jvfrmtd' ),
			'type' => Controls_Manager::TEXT,
			'placeholder' => esc_html__( 'Type your label name here', 'jvfrmtd' ),
			'description' => esc_html__('If empty value, it will be showing the default name. Format : only input, text fields', 'jvfrmtd'),
			'condition' => Array(
				'show_label' => 'yes',
				'field_label_type' => 'custom',
			),
		) );

		$this->add_control( 'field_align', [
			'label' => __( 'Align', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'inline',
			'options' => Array(
				'block'  => __( 'Vertical ( Block )', 'jvfrmtd' ),
				'inline' => __( 'Horizon (Inline Block)', 'jvfrmtd' ),
			),
			'prefix_class' => 'field-align-',
			/*
			'selectors' => [ // You can use the selected value in an auto-generated css rule.
				'{{WRAPPER}}.field-align-block .item-label' => 'display: block;',
				'{{WRAPPER}} .item-value' => 'display: {{VALUE}}',
				'{{WRAPPER}} .single-item' => 'display: flex;',
			], */
			'condition' => [
				'show_label'	=> 'yes',
			],
		] );

		$this->add_control( 'label_color', [
			'label' => __( 'Title Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'scheme' => [
			'type' => Scheme_Color::get_type(),
			'defalut' => '#666',
			'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .item-label' => 'color: {{VALUE}}',
			],
			'condition' => [
				'show_label'	=> 'yes',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'label_typography',
			'selector' => '{{WRAPPER}} .item-label',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			'condition' => [
				'show_label'	=> 'yes',
			],
		] );

		$this->add_responsive_control( 'label_align', [
			'label' => __( 'Alignment', 'jvfrmtd' ),
			'type' => Controls_Manager::CHOOSE,
			'default' =>'left',
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
				'{{WRAPPER}} .item-label' => 'text-align: {{VALUE}};',
			],
			'condition' => [
				'show_label'	=> 'yes',
			],
		] );

		$this->add_control( 'label_gap', [
			'label' => __( 'Label Gap', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 0,
				'unit' => '%',
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
				'{{WRAPPER}} .item-label' => 'margin-right: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'show_label'	=> 'yes',
			],
		] );

		$this->add_control( 'label_width', [
			'label' => __( 'Label Width', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 0,
				'unit' => '%',
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
				'{{WRAPPER}} .item-label' => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'show_label'	=> 'yes',
			],
		] );

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_prefix_settings_control() {
			$this->start_controls_section( 'section_additional', Array(
			'label' => __('Prefix & Suffix (Value only)', 'jvfrmtd'),
		) );

		$this->start_controls_tabs( 'prefix_suffix_tab' );
		$this->start_controls_tab( 'prefix', [ 'label' => __( 'Prefix', 'jvfrmtd' ) ] );
			$this->add_control( 'prefix_text', [
				'label'       => __( 'Prefix Text', 'jvfrmtd' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '', 'jvfrmtd' ),
				'placeholder' => __( 'Type your title text here', 'jvfrmtd' ),
			] );

			$this->add_control( 'prefix_color', [
				'label' => __( 'Prefix Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .prefix-text' => 'color: {{VALUE}}',
				],
			] );

			$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'prefix_typography',
				'selector' => '{{WRAPPER}} .prefix-text',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			] );

			$this->add_responsive_control(
			'prefix_space',
				[
					'label' => __( 'Prefix Space', 'jvfrmtd' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 15,
						'unit' => 'px',
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
						'{{WRAPPER}} .prefix-text' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'prefix_padding',
				[
					'label'      => esc_html__( 'Prefix Padding', 'jvfrmtd' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors'  => [
						'{{WRAPPER}} .prefix-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_tab();

		$this->start_controls_tab( 'suffix', [ 'label' => __( 'Suffix', 'jvfrmtd' ) ] );
			$this->add_control( 'suffix_text', [
				'label'       => __( 'suffix Text', 'jvfrmtd' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '', 'jvfrmtd' ),
				'placeholder' => __( 'Type your title text here', 'jvfrmtd' ),
			] );

			$this->add_control( 'suffix_color', [
				'label' => __( 'Suffix Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
				'{{WRAPPER}} .suffix-text' => 'color: {{VALUE}}',
				],
			] );

			$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'suffix_typography',
				'selector' => '{{WRAPPER}} .suffix-text',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			] );

			$this->add_responsive_control(
			'suffix_space',
				[
					'label' => __( 'Suffix Space', 'jvfrmtd' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 15,
						'unit' => 'px',
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
						'{{WRAPPER}} .suffix-text' => 'margin-left: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'suffix_padding',
				[
					'label'      => esc_html__( 'Suffix Padding', 'jvfrmtd' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors'  => [
						'{{WRAPPER}} .suffix-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_label_settings_control() {
		$this->start_controls_section( 'section_label', [
			'label' => __( 'Label', 'jvfrmtd' ),
		] );

		$this->add_control( 'use_label_link', Array(
			'label' => __( 'Use label link', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
		) );

		$this->add_control( 'label_link_url', Array(
			'label' => esc_html__( 'URL', 'jvfrmtd' ),
			'type' => Controls_Manager::URL,
			'default' => Array(
				'url' => 'http://',
				'is_external' => '',
			),
			'condition' => Array(
				'use_label_link' => 'yes'
			),
		) );
		$this->end_controls_section();
	}

	protected function add_html_tag_controls() {
		$this->start_injection(Array(
			'at' => 'after',
			'of' => 'meta',
		));
			$this->add_control( 'header_tag',Array(
				'label' => __( 'HTML Tag', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => Array(
					'' => esc_html__( 'None', 'jvfrmtd' ),
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				),
				'default' => '',
			) );
		$this->end_injection();
	}

	public function getReplaceOptions() {
		$base = Array(
			'post_title' => esc_html__( "TItle", 'jvfrmtd' ),
			'post_content' => esc_html__( "Content", 'jvfrmtd' ),
			'post_excerpt' => esc_html__( "Excerpt", 'jvfrmtd' ),
			'post_author' => esc_html__( "Author", 'jvfrmtd' ),
			'post_date' => esc_html__( "Date", 'jvfrmtd' ),
			'post_author_avatar' => esc_html__( "Author Avatar", 'jvfrmtd' ),
			'comment_count' => esc_html__( "Comment Count", 'jvfrmtd' ),
			'category' => esc_html__( "Category", 'jvfrmtd' ),
			'listing_category' => esc_html__( "Listing Category", 'jvfrmtd' ),
			'listing_location' => esc_html__( "Listing Location", 'jvfrmtd' ),
			'listing_keyword' => esc_html__( "Listing Keyword", 'jvfrmtd' ),
			'_address' => esc_html__( "Address", 'jvfrmtd' ),
			'_email' => esc_html__( "Email", 'jvfrmtd' ),
			'_website' => esc_html__( "Website", 'jvfrmtd' ),
			'_phone1' => esc_html__( "Phone 1", 'jvfrmtd' ),
			'_phone2' => esc_html__( "Phone 2", 'jvfrmtd' ),
			'_price' => esc_html__( "Price", 'jvfrmtd' ),
			'_sale_price' => esc_html__( "Sale Price", 'jvfrmtd' ),
			'_print_price' => esc_html__( "Print Price", 'jvfrmtd' ),
			'_price_range' => esc_html__( "Price Range", 'jvfrmtd' ),
			'_logo' => esc_html__( "Logo", 'jvfrmtd' ),
			'rating' => esc_html__( "Rating", 'jvfrmtd' ),
			'favorite' => esc_html__( "Favorite", 'jvfrmtd' ),
			'favorite_count' => esc_html__( "Favorite count", 'jvfrmtd' ),
			'post_view_count' => esc_html__( "Post view count", 'jvfrmtd' ),
			'open_hours' => esc_html__( "Working hours", 'jvfrmtd' ),
			'preview_map' => esc_html__( "Preview Map", 'jvfrmtd' ),
			'preview_detail' => esc_html__( "Detail Images(Modal)", 'jvfrmtd' ),
			'slider_detail' => esc_html__( "Detail Images(Slider)", 'jvfrmtd' ),
			'preview_video' => esc_html__( "Video(Modal)", 'jvfrmtd' ),
			'more_tax' => esc_html__( "More taxonomy", 'jvfrmtd' ),
			'image_category_featured' => esc_html__( "Category Featured Image", 'jvfrmtd' ),
			'icon_category_featured' => esc_html__( "Category Featured Icon", 'jvfrmtd' ),

		);

		// if( $this->get_name() == self::BLOCK_MEDIA ) {
			$base = wp_parse_args( Array(
				'preview' => esc_html__( "Preview", 'jvfrmftd' ),
				'distance' => esc_html__( "Distance", 'jvfrmftd' ),
			), $base );
		// }

			$base = wp_parse_args( Array(
				'term_taxonomy' => esc_html__( "Term Taxonomy Name", 'jvfrmftd' ),
				'term_name' => esc_html__( "Term Name", 'jvfrmftd' ),
				'term_permalink' => esc_html__( "Term Link", 'jvfrmftd' ),
				'term_count' => esc_html__( "Posts Count Of Term", 'jvfrmftd' ),
				'term_description' => esc_html__( "Term Description", 'jvfrmftd' ),
				'term_icon' => esc_html__( "Term Icon", 'jvfrmftd' ),
				'custom_meta' => esc_html__( "Custom Meta", 'jvfrmftd' ),
			), $base );

		return $base;
	}

	protected function getFieldMeta( $field ) {
		$output = false;
		if( method_exists( $this, $field ) ) {
			$output = call_user_func_array( Array( $this, $field ), Array() );
		}
		return $output;
	}

	public function metaFilter( $key, array $args=Array() ) {
		$args = shortcode_atts(
			Array(
				'link' => false,
				'protocal' => 'none',
				'format' => '%1$s',
				'default' => null,
				'type' => 'string',
			), $args
		);
		$url = '';
		$value = get_post_meta( get_the_ID(), $key, true );

		switch( $args[ 'type' ] ) {
			case 'float' : $value = floatVal( $value ); break;
			case 'numeric' : $value = intVal( $value ); break;
		}

		switch( $args[ 'protocal' ] ) {
			case 'email' : $url = 'mailto:' . $value; break;
			case 'tel' : $url = 'tel:' . $value; break;
		}

		if( $args[ 'link' ] ) {
			$args[ 'format' ] = '<a href="%2$s" target="_blank">%1$s</a>';
			if(!in_array($args['protocal'], Array('email', 'tel'))) {
				$url = esc_url( $value );
			}
		}
		return sprintf( $args[ 'format' ], $value, $url );
	}

	public function termFilter( $taxonomy, array $args=Array() ) {
		$args = shortcode_atts(
			Array(
				'single' => true,
				'separator' => ', ',
				'field' => false,
			), $args
		);
		$output = false;

		if( taxonomy_exists( $taxonomy ) ) {
			$terms = wp_get_object_terms( get_the_ID(), $taxonomy );
			if( !empty( $args[ 'field' ] ) ) {
				$output = array();
				foreach( (Array) $terms as $term ) {
					$output[] = $term->{$args['field']};
				}
				if( $args[ 'single' ] ) {
					$output = isset( $output[0] ) ? $output[0] : false;
				}else{
					$output = join( $args[ 'separator' ], $output );
				}
			}
		}
		return $output;
	}

	public function getAuthorMeta( $key='', $default=false ) {
		$author = get_user_by( 'id', get_post()->post_author );
		if( $author ) {
			if( isset( $author->{$key} ) ) {
				$default = $author->{$key};
			}
		}
		return $default;
	}

	protected function txt_title() { return get_post()->post_title; }
	protected function post_author() { return $this->getAuthorMeta( 'display_name' ); }
	protected function _tagline() { return $this->metaFilter( '_tagline', Array( 'link' => false ) ); }
	protected function txt_content() {
		$contents = esc_html__( "Description", 'jvfrmtd' );
		if( ! is_admin() ) {
			$contents = apply_filters( 'the_content', get_post()->post_content );
		}
		return $contents;
	}

	protected function _featured() {
		$imageUrl = ELEMENTOR_ASSETS_URL . 'images/placeholder.png';
		$featuedURL = wp_get_attachment_image_url( get_post_thumbnail_id(), 'thumbnail' );
		if( $featuedURL ) {
			$imageUrl = $featuedURL;
		}
		return sprintf( '<img src="%s">', $imageUrl );
	}

	protected function _logo() {
		$imageSize = $this->get_settings('field_image_size');
		$imageUrl = ELEMENTOR_ASSETS_URL . 'images/placeholder.png';
		$logoID = get_post_meta( get_the_ID(), '_logo', true );
		$logoURL = wp_get_attachment_image_url( $logoID, $imageSize );
		if( $logoURL ) {
			$imageUrl = $logoURL;
		}
		return sprintf( '<img src="%s">', $imageUrl );
	}

	protected function _rating_average() {
		return $this->metaFilter( 'rating_average', Array( 'link' => false, 'type' => 'float' ) );
	}

	protected function _favorite() {
		if( !class_exists( 'lvDirectoryFavorite_button' ) ) {
			return;
		}
		$instance = new \lvDirectoryFavorite_button( Array(
			'format' => '{text}',
			'post_id' => get_the_ID(),
			'save' => "<i class='fa fa-heart'></i>",
			'unsave' => "<i class='fa fa-heart'></i>"
		) );
		return $instance->output( false );
	}

	protected function _website() { return $this->metaFilter( '_website', Array( 'link' => true ) ); }
	protected function _email() { return $this->metaFilter( '_email', Array( 'link' => true, 'protocal' => 'email' ) ); }
	protected function _address() { return $this->metaFilter( '_address' ); }
	protected function _phone1() { return $this->metaFilter( '_phone1', Array( 'link' => true, 'protocal' => 'tel' ) ); }
	protected function _phone2() { return $this->metaFilter( '_phone2', Array( 'link' => true, 'protocal' => 'tel' ) ); }

	protected function _price() {
		$format = '%1$s';
		$price = $this->metaFilter('_price');
		$price = !empty($price) ? $price : 0;
		if('before' == $this->get_settings('currency_settings')){
			$format = '%2$s%1$s';
		}
		if('after' == $this->get_settings('currency_settings')){
			$format = '%1$s%2$s';
		}
		return sprintf($format, number_format($price), $this->metaFilter('_currency_unit'));
	}

	protected function _sale_price() {
		$format = '%1$s';
		$price = $this->metaFilter('_sale_price');
		$price = !empty($price) ? $price : 0;
		if('before' == $this->get_settings('currency_settings')){
			$format = '%2$s%1$s';
		}
		if('after' == $this->get_settings('currency_settings')){
			$format = '%1$s%2$s';
		}
		return sprintf($format, number_format($price), $this->metaFilter('_currency_unit'));
	}

	protected function _print_price() {
		$format = '<span class="regular-price">%1$s</span>';
		$hasSalePrice = false;
		$price = $this->metaFilter('_price');
		$salePrice = $this->metaFilter('_sale_price');
		$price = !empty($price) ? floatVal($price) : 0;
		if(is_numeric($salePrice)) {
			$hasSalePrice = true;
		}

		$price = floatVal($price);
		$salePrice = floatVal($salePrice);

		if('before' == $this->get_settings('currency_settings')){
			$format = $hasSalePrice ? '<span class="regular-price price-middle-line">%2$s%1$s</span> <span class="sale-price">%2$s%3$s</span>' : '<span class="regular-price">%2$s%1$s</span>';
		}
		if('after' == $this->get_settings('currency_settings')){
			$format = $hasSalePrice ? '<span class="regular-price price-middle-line">%1$s%2$s</span> <span class="sale-price">%3$s%2$s</span>' : '<span class="regular-price">%1$s%2$s</span>';
		}
		return sprintf($format, number_format($price), $this->metaFilter('_currency_unit'), number_format($salePrice));
	}

	protected function _date1() { return $this->metaFilter( '_date1' ); }
	protected function _date2() { return $this->metaFilter( '_date2' ); }
	protected function _facebook_link() { return $this->metaFilter( '_facebook_link', Array( 'link' => true ) ); }
	protected function _twitter_link() { return $this->metaFilter( '_twitter_link', Array( 'link' => true ) ); }
	protected function _instagram_link() { return $this->metaFilter( '_instagram_link', Array( 'link' => true ) ); }
	protected function _google_link() { return $this->metaFilter( '_google_link', Array( 'link' => true ) ); }
	protected function _linkedin_link() { return $this->metaFilter( '_linkedin_link', Array( 'link' => true ) ); }
	protected function _youtube_link() { return $this->metaFilter( '_youtube_link', Array( 'link' => true ) ); }

	protected function post_category() { return $this->termFilter( 'category', Array( 'field' => 'name', 'single' => false ) ); }
	protected function post_tag() { return $this->termFilter( 'post_tag', Array( 'field' => 'name', 'single' => false ) ); }
	protected function post_date() { return date_i18n( get_option( 'date_format' ), strtotime( get_post()->post_date ) ); }

	protected function post_product() { return $this->termFilter( 'product_name', Array( 'field' => 'name', 'single' => false ) ); }
	protected function post_status() { return $this->termFilter( 'ticket_status', Array( 'field' => 'name', 'single' => false ) ); }

	protected function listing_category() {
		return $this->post_term_meta(
			'listing_category',
			( 'all' == $this->get_settings('terms_display') ? 'multiple' : 'single'),
			$this->get_settings('terms_link')
		);
	}
	protected function listing_location() {
		return $this->post_term_meta(
			'listing_location',
			( 'all' == $this->get_settings('terms_display') ? 'multiple' : 'single'),
			$this->get_settings('terms_link')
		);
	}
	protected function listing_amenities() {
		return $this->post_term_meta(
			'listing_amenities',
			( 'all' == $this->get_settings('terms_display') ? 'multiple' : 'single'),
			$this->get_settings('terms_link')
		);
	}
	protected function listing_keyword() {
		return $this->post_term_meta(
			'listing_keyword',
			( 'all' == $this->get_settings('terms_display') ? 'multiple' : 'single'),
			$this->get_settings('terms_link')
		);
	}

	protected function more_tax() {
		return $this->post_term_meta(
			$this->get_settings('more_taxonomy'),
			( 'all' == $this->get_settings('terms_display') ? 'multiple' : 'single'),
			$this->get_settings('terms_link')
		);
	}

	public function post_term_meta($taxonomy, $field='single', $link=''){
		$template = sprintf( '{more_tax:%1$s|%2$s|%3$s}', $taxonomy, $field, $link);
		$instance = new \Jvbpd_Replace_Content( get_the_ID(), $template );
		return $instance->render();
	}


	protected function getValue( $field, $default=false, $separator=', ' ) {
		$values = get_post_meta( get_the_ID(), $field[ 'name' ], true );
		switch( $field[ 'type' ] ) {
			case 'url' :
				$format = '<a href="%1$s" target="_blank">%1$s</a>';
				$values = esc_url( $values );
				break;
			case 'email' :
				$format = '<a href="mailto:%1$s" target="_blank">%1$s</a>';
				$values = sanitize_email($values);
				break;
			case 'image' :
				$format = '<img src="%1$s">';
				$values = wp_get_attachment_image_url( $values, $field[ 'preview_size' ] );
				break;
		    case 'taxonomy' :
				$format = '%1$s';
		  		$values = Array();

				$terms = get_field( $field[ 'name' ] );
				$terms = is_array( $terms ) ? $terms : Array( $terms );

				foreach( array_filter( $terms ) as $term ) {
					if( is_numeric( $term ) ) {
						$term = get_term_by( 'id', $term, $field[ 'taxonomy' ] );
					}
					if( $term instanceof \WP_Term ) {
						$values[] = $term->name;
					}
				}
  				break;
			case 'table' :
				$format='%1$s';
				$values=jvbpd_elements_tools()->getACFTable($field['name']);
				break;
			default: $format='%1$s';
		}

		if( is_array( $values ) ) {
			$default = join( $separator, $values );
		}elseif( is_numeric( $values ) ) {
			$default = floatVal( $values );
		}elseif( !empty( $values ) ) {
			$default = $values;
		}

		return sprintf( $format, $default );
	}

	protected function getAcfObject() {
		if( ! function_exists( 'get_field_object' ) ) {
			return Array();
		}

		$settings = $this->get_settings();

		$acfGroup = $settings[ 'acf_group' ];
		$acfFieldID = isset( $settings[ 'acf_field_' . $acfGroup ] ) ? $settings[ 'acf_field_' . $acfGroup ] : false;
		return get_field_object( $acfFieldID );
	}

	public function get_taxonomy_terms( Array $args=Array() ) {

		$output = Array();
		$args = wp_parse_args( $args,
			Array(
				'taxonomy' => NULL,
				'parent' => 0,
				'depth' => 0,
				'value' => 'term_id',
				'separator' => '-',
			)
		);

		if( ! taxonomy_exists( $args[ 'taxonomy' ] ) ) {
			return $output;
		}

		$term_args = Array(
			'taxonomy' => $args[ 'taxonomy' ],
			'parent' => $args[ 'parent' ],
			'hide_empty' => false,
		);

		$terms = get_terms( $term_args );

		if( 0 >= sizeof( $terms ) ) {
			return $output;
		}

		$args[ 'depth' ]++;

		foreach( $terms as $term ) {
			$term_value = $args[ 'value' ] == 'name' ? $term->name : $term->term_id;
			$output[ $term_value ] = sprintf( '%1$s %2$s', str_repeat( $args[ 'separator' ], $args[ 'depth' ] -1 ), ucfirst( $term->name ) );
			$args[ 'parent' ] = $term->term_id;
			$childs = $this->get_taxonomy_terms( $args );
			if( !empty( $childs ) ) {
				$output += $childs;
			}
		}
		return $output;
	}

	protected function getPostObject() {
		$fieldKey = $this->get_settings( 'meta' );
		return Array(
			$fieldKey => Array(
				'key' => ''
			),
		);
	}

	protected function getListingObject() {
		global $edit;

		$output = $fields = $args = Array();
		$fieldKey = $this->get_settings( 'meta' );

		if( ! class_exists( 'Lava_Directory_Manager_Func' ) ) {
			return $output;
		}

		$meta = apply_filters( 'lava_lv_listing_more_meta', Array() );

		$fields = wp_parse_args(
			Array(
				'txt_title' => Array(
					'label' => esc_html__( "Title", 'Lavacode' ),
					'element' => 'input',
					'type' => 'text',
					'class' => 'all-options',
					'group' => false,
				),
				'_tagline' => Array(
					'label' => esc_html__( "TagLine", 'Lavacode' ),
					'element' => 'input',
					'type' => 'text',
					'class' => 'all-options',
					'placeholder' => esc_html__( "Tagline", 'Lavacode' )
				),
				'txt_content' => Array(
					'label' => esc_html__( "Description", 'Lavacode' ),
					'element' => 'textarea',
					'class' => 'all-options',
					'group' => false,
				),
				'_logo' => Array(
					'label'		=> esc_html__( "Logo", 'Lavacode' ),
					'element'	=> 'wp_library',
					'type'		=> 'text',
					'class'		=> 'all-options',
					'value' => $edit instanceof \WP_Post ? get_post_meta( $edit->ID, '_logo', true ) : null,
					'dialog_title' => esc_html__( "Select logo", 'Lavacode' ),
					'button_upload_label' => esc_html__( "Select", 'Lavacode' ),
					'button_remove_label' => esc_html__( "Reset", 'Lavacode' ),
				),
				'_featured' => Array(
					'label'		=> esc_html__( "Featured Image", 'Lavacode' ),
					'element'	=> 'featured_image',
				),
				'_detail_images' => Array(
					'label'		=> esc_html__( "Detail Images", 'Lavacode' ),
					'element'	=> 'detail_image',
				),
				'map' => Array(
					'label'		=> esc_html__( "Map", 'Lavacode' ),
					'element'	=> 'map',
				),
				'custom_field' => Array(
					'label'		=> esc_html__( "Custom Field", 'Lavacode' ),
					'element'	=> 'custom_field',
				),
				'user_join_form' => Array(
					'label'		=> esc_html__( "User Join Form (Not user logged in)", 'Lavacode' ),
					'element'	=> 'user_join_form',
				),
			), $meta
		);

		$terms = apply_filters( 'lava_lv_listing_taxonomies', Array() );
		if( 'more_tax' == $fieldKey ) {
			$fieldKey = $this->get_settings('more_taxonomy');
			$terms[$fieldKey] = Array();
		}
		$fields = wp_parse_args( $terms, $fields );

		if( array_key_exists( $fieldKey, $fields ) ) {
			if( taxonomy_exists( $fieldKey ) ) {
				$thisValue = $edit instanceof \WP_Post ? wp_get_object_terms($edit->ID, $fieldKey, Array('fields' => 'ids')) : false;
				$output = Array(
					'label' => get_taxonomy( $fieldKey )->label,
					'element' => 'select',
					'name_suffix' => '][',
					'class' => 'lava-add-item-selectize text-left',
					'value' => $thisValue,
					'values' => $this->get_taxonomy_terms( Array( 'taxonomy' => $fieldKey ) ),
					'group' => 'lava_additem_terms',
					'attribute' => Array(
						'multiple' => 'multiple',
						'data-limit' => '',
						'data-create' => 0,
						'data-tax' => $fieldKey,
					)
				);
				if( 'listing_keyword' == $fieldKey ) {
					$output[ 'values' ] = $this->get_taxonomy_terms( Array( 'taxonomy' => $fieldKey, 'value' => 'name' ) );
					$output[ 'attribute' ][ 'data-create' ] = true;
				}

				$placeholder = esc_html__( "Select a one", 'jvfrmtd' );
				if( 'yes' === $this->get_settings( 'use_own_label' ) ) {
					$placeholder = $this->get_settings( 'field_placeholder' );
				}
				$output[ 'values' ] = Array( '' => $placeholder ) + $output[ 'values' ];
			}else{
				$output = $fields[ $fieldKey ];
			}
			$output[ 'key' ] = $fieldKey;
		}
		return $output;
	}

	protected function getObject() {
		$output = false;
		// if( self::ACF_META == $this->get_name() ) {
		if( 'acf' == $this->get_settings( 'field_type' ) ) {
			$output = $this->getAcfObject();
		}

		// if( self::LISTING_META == $this->get_name() ) {
		if( 'base' == $this->get_settings( 'field_type' ) ) {
			$output = $this->getListingObject();
		}

		return $output;
	}

	protected function get_label( $field ) {
		$format = '%1$s';
		$link = $target = '';
		$hasLabel = false;
		if( 'yes' == $this->get_settings( 'use_label_link' ) ) {
			$hasLabel = true;
			$linkMeta = $this->get_settings( 'label_link_url' );
			$link = esc_url( $linkMeta[ 'url' ] );
			$target = !empty( $linkMeta[ 'is_external' ] ) ? '_black' : '_self';
			$format = '<a href="%2$s" class="item-label-link" target="%3$s" title="%1$s">%1$s</a>';
		}
		if('yes' == $this->get_settings('show_label')) {
			$hasLabel = true;
			if('custom' == $this->get_settings('field_label_type')){
				$field['label'] = $this->get_settings('field_label');
			}
		}else{
			$field['label'] ='';
		}
		if( 'yes' === $this->get_settings( 'use_own_label' ) ) {
			$hasLabel = true;
			$field[ 'label' ] = $this->get_settings( 'field_name_label' );
			$field[ 'placeholder' ] = $this->get_settings( 'field_placeholder' );
		}
		if($hasLabel || '' != $field['label']) {
			$content = sprintf('<div %s>', $this->get_render_attribute_string('item_label'));
			$content .= sprintf( $format, ( isset( $field['label'] ) ? $field['label'] : false ), $link, $target );
			$content .= '</div>';
			return $content;
		}
		return '';
	}

	protected function before() {}
	protected function after() {}

	protected function _render() {

		$settings = $this->get_settings();
		$field = $this->getObject();

		$fieldKey = $fieldValue = '';

		if( ! $field ) {
			if( in_array( $this->get_name(), Array( self::MODULE_META, self::POST_META ) ) ) {
				$field[ 'key' ] = $this->get_settings( 'field' );
				$field[ 'label' ] = $this->get_settings( 'field_label' );
				$fieldValue = $this->getModuleMeta();
			}elseif( ! array_key_exists( $this->get_settings( 'meta' ), $this->moreBaseMeta() ) ) {
				return false;
			}else{
				$field[ 'key' ] = $this->get_settings( 'meta' );
			}
		}

		// if( self::ACF_META == $this->get_name() ) {
		if( 'acf' == $this->get_settings( 'field_type' ) ) {
			$fieldKey = $field[ 'name' ];
			$fieldValue = $this->getValue( $field );
		}

		// if( self::LISTING_META == $this->get_name() ) {
		if( 'base' == $this->get_settings( 'field_type' ) ) {
			$fieldKey = $field[ 'key' ];
			$fieldValue = $this->getFieldMeta( $settings[ 'meta' ] );
		}

		//===== Icon =====//
		$this->add_render_attribute( 'wrapper', 'class', 'elementor-icon-wrapper' );
		$this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-icon' );

		$this->add_render_attribute('item_label', 'class', 'item-label');
		$this->add_render_attribute('item_value', 'class', 'item-value');

		if ( ! empty( $settings['hover_animation'] ) ) {
			$this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		$icon_tag = 'div';

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'icon-wrapper', 'href', $settings['link']['url'] );
			$icon_tag = 'a';

			if ( ! empty( $settings['link']['is_external'] ) ) {
				$this->add_render_attribute( 'icon-wrapper', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'icon-wrapper', 'rel', 'nofollow' );
			}
		}

		if ( ! empty( $settings['icon'] ) ) {
			$this->add_render_attribute( 'icon', 'class', $settings['icon'] );
		}
		//===== Icon =====//
		//===== prefix, suffix =====//
		$prefix_text='';
		$suffix_text='';
		if ( ! empty( $settings['prefix_text'] ) ) {
				$prefix_text = "<span class='prefix-text'>". $settings['prefix_text'] ."</span>";
		}
		if ( ! empty( $settings['suffix_text'] ) ) {
				$suffix_text = "<span class='suffix-text'>". $settings['suffix_text'] ."</span>";
		}
		//===== prefix, suffix =====//

		if($settings['show_msg']=='yes'){
			$is_admin_text = "<span class='no-data'>". $settings['empty_msg']. "</span>";
		}else{
			$is_admin_text= $prefix_text . __( 'Value', 'jvfrmtd' ) . $suffix_text;
		}

		if( self::LISTING_FIELD == $this->get_name() ) {
			$tempFieldKey = $fieldKey;
			if( isset( $field[ 'name_suffix' ] ) ) {
				$fieldKey .=  $field[ 'name_suffix' ];
			}
			$instance = new listing_field( $fieldKey, $field );
			$fieldKey = $tempFieldKey;
			if( isset( $field[ 'group' ] ) ) {
				$instance->fieldGroup = $field[ 'group' ];
			}
			if( isset( $field[ 'value' ] ) ) {
				$instance->value = $field[ 'value' ];
			}
			$fieldValue = $instance->output();
		}

		if ( !empty( $fieldValue )){
			$value_data = $prefix_text . $fieldValue . $suffix_text;
		}else{
			$value_data = $settings['empty_msg'];
		}

		if('user_join_form' == $this->get_settings('meta')) {
			$this->add_render_attribute('item_label', 'class', 'hidden');
		}

		$this->before(); ?>

		<?php //echo $settings['custom_meta']; ?>

		<div class="single-item <?php echo $fieldKey; ?>">
			<?php
			if( !empty($settings['icon']) || !empty( $settings['icons']['value'] ) ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
					<<?php echo $icon_tag . ' ' . $this->get_render_attribute_string( 'icon-wrapper' ); ?>>
						<?php
						if(
							isset($settings['__fa4_migrated']['icons']) ||
							(empty($settings['icon']) && Icons_Manager::is_migration_allowed())
						) {
							Icons_Manager::render_icon( $settings['icons'], Array('aria-hidden' => 'true') );
						}else{
							printf('<i class="%s"></i>', $settings['icon']);
						} ?>
					</<?php echo $icon_tag; ?>>
				</div>
			<?php endif; ?>

			<?php echo $this->get_label( $field ); ?>
			<div <?php echo $this->get_render_attribute_string('item_value'); ?>>
				<?php
				//if( $settings['custom_meta']=='custom_meta' ){
					// echo $settings['custom_meta'];
				//}else{
					$html_tag = $this->get_settings('header_tag');
					if('' != $html_tag ) {
						printf('<%1$s>%2$s</%1$s>', $html_tag, $value_data);
					}else{
						echo $value_data;
					}
				//}  ?>
			</div>
		</div>
		<?php
		$this->after();
	}

	protected function __card_register_controls() {
		$this->start_controls_section(
			'section_layout_a',
			[
				'label' => __( 'Layout', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'scheme',
			[
				'label' => __( 'Blocks', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'one-block' => __( '1 block', 'jvfrmtd' ),
					'two-block' => __( '2 blocks', 'jvfrmtd' ),
				],
				'render_type' => 'template',
				'prefix_class' => 'module-card--scheme-',
				'default' => 'one-block',
			]
		);

		$this->add_responsive_control(
			'layout',
			[
				'label' => __( 'Additional Layout', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon'  => 'eicon-h-align-left',
					],
					'above' => [
						'title' => __( 'Above', 'jvfrmtd' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'module-card-%s-layout-image-',
				'condition' => [
					'scheme!' => 'one-block',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image',
			[
				'label' => __( 'Media', 'jvfrmtd' ),
			]
		);

		$this->add_control( 'bg_select', [
			'label' => __( 'Image', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'label_block' => true,
			'options' => [
				'' => __( 'None', 'jvfrmtd' ),
				'featured_image' => __( 'Featured Image', 'jvfrmtd' ),
				'category_image' => __( 'Category Featured Image', 'jvfrmtd' ),
				'custom_image' => __( 'Custom Image', 'jvfrmtd' ),
				'bp_cover_image' => __( 'Cover Image ( BuddyPress )', 'jvfrmtd' ),
			],
			'separator' => 'before',
			'default' => 'featured_image',
		] );

		$this->add_control( 'default_image', Array(
			'label' => __( 'Default image ( if there are no images )', 'jvfrmtd' ),
			'type' => Controls_Manager::MEDIA,
			'default' => Array(
				'url' => Utils::get_placeholder_image_src(),
			),
			'condition' => Array( 'bg_select!' => 'custom_image'),
		) );

		$this->add_control(
		  'thumb_size',
		  [
			 'label'       => __( 'Image Size', 'jvfrmtd' ),
			 'type' => Controls_Manager::SELECT,
			 'default' => '',
			 'options' => jvbpd_elements_tools()->get_image_sizes(),
			'condition' => [
				'bg_select' => Array( 'featured_image', 'category_image', 'bp_cover_image' ),
			],
		  ]
		);

		$this->add_control(
			'custom_bg_image',
			[
				'label' => __( 'Custom Image', 'jvfrmtd' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'show_label' => false,
				'condition' => [
					'bg_select' => 'custom_image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'custom_bg_image', // Actually its `image_size`
				'label' => __( 'Image Resolution', 'jvfrmtd' ),
				'default' => 'large',
				'condition' => [
					'bg_select' => 'custom_image',
					'custom_bg_image[id]!' => '',
				],
				'separator' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_badge',
			[
				'label' => __( 'Badges on Image', 'jvfrmtd' ),
			]
		);

		$this->start_controls_tabs( 'badge_tabs' );

		$this->start_controls_tab( 'badge_top_left',
			[
				'label' => __( 'Top Left', 'jvfrmtd' ),
			]
		);
		$this->add_badge_control( $this, 'top_left', esc_html__( "Top Left", 'jvfrmtd' ) );
		$this->end_controls_tab();


		$this->start_controls_tab( 'badge_top_right',
			[
				'label' => __( 'Top Right', 'jvfrmtd' ),
			]
		);
		$this->add_badge_control( $this, 'top_right', esc_html__( "Top Right", 'jvfrmtd' ) );
		$this->end_controls_tab();


		$this->start_controls_tab( 'badge_bottom_left',
			[
				'label' => __( 'BTM Left', 'jvfrmtd' ),
			]
		);
		$this->add_badge_control( $this, 'bottom_left', esc_html__( "Bottom Left", 'jvfrmtd' ) );
		$this->end_controls_tab();


		$this->start_controls_tab( 'badge_bottom_right',
			[
				'label' => __( 'BTM Right', 'jvfrmtd' ),
			]
		);
		$this->add_badge_control( $this, 'bottom_right', esc_html__( "Bottom Right", 'jvfrmtd' ) );
		$this->end_controls_tab();




		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_body',
			[
				'label' => __( 'Content', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'content_media',
			[
				'label' => __( 'Media in Content', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'none' => __( 'None', 'jvfrmtd' ),
					'featured_image' => __( 'Featured Image', 'jvfrmtd' ),
					'featured_category_image' => __( 'Featured Category Image', 'jvfrmtd' ),
					'image' => __( 'Custom Image', 'jvfrmtd' ),
					'bp_cover_image' => __( 'Cover Image (BuddyPress)', 'jvfrmtd' ),
					'featured_category_icon' => __( 'Featured Category Icon', 'jvfrmtd' ),
				],
				'separator' => 'before',
				'default' => 'none',
			]
		);

		$this->add_control(
			'graphic_image',
			[
				'label' => __( 'Choose Image', 'jvfrmtd' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'content_media' => 'image',
				],
				'show_label' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'graphic_image', // Actually its `image_size`
				'default' => 'thumbnail',
				'condition' => [
					'content_media' => 'image',
					'graphic_image[id]!' => '',
				],
			]
		);


		$this->add_control(
			'icon_view',
			[
				'label' => __( 'View', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'jvfrmtd' ),
					'stacked' => __( 'Stacked', 'jvfrmtd' ),
					'framed' => __( 'Framed', 'jvfrmtd' ),
				],
				'default' => 'default',
				'condition' => [
					'content_media' => 'featured_category_icon',
				],
			]
		);

		$this->add_control(
			'icon_shape',
			[
				'label' => __( 'Shape', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle' => __( 'Circle', 'jvfrmtd' ),
					'square' => __( 'Square', 'jvfrmtd' ),
				],
				'default' => 'circle',
				'condition' => [
					'icon_view!' => 'default',
					'content_media' => 'featured_category_icon',
				],
			]
		);

		$this->add_control( 'title', [
			'label' => __( 'Title Field', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'default' => '',
			'options' => $this->getReplaceOptions(),
		] );

		$this->add_control( 'title_length', [
			'label' => __( 'Title Field Length', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '0',
		] );

		$this->add_control(
		  'custom_meta1',
		  [
			 'label'   => __( 'Custom Meta1', 'jvfrmtd' ),
			 'type'    => Controls_Manager::CODE,
			 'language' => 'html',
		  ]
		);

		$this->add_control( 'description', [
			'label' => __( 'Content Field', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'default' => '',
			'options' => $this->getReplaceOptions(),
		] );

		$this->add_control( 'description_length', [
			'label' => __( 'Content Field Length', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '0',
		] );

		$this->add_control(
		  'custom_meta2',
		  [
			 'label'   => __( 'Custom Meta2', 'jvfrmtd' ),
			 'type'    => Controls_Manager::CODE,
			 'language' => 'html',
		  ]
		);

		/* For category block
		$this->add_control(
			'title0',
			[
				'label' => __( 'Title & Description', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the heading', 'jvfrmtd' ),
				'placeholder' => __( 'Your Title', 'jvfrmtd' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description0',
			[
				'label' => __( 'Description', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Click000 edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'jvfrmtd' ),
				'placeholder' => __( 'Your Description', 'jvfrmtd' ),
				'title' => __( 'Input image text here', 'jvfrmtd' ),
				'separator' => 'none',
				'rows' => 5,
				'show_label' => false,
			]
		);
		*/

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
				],
				'default' => 'h2',
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_link',
			[
				'label' => __( 'Link', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'use_button',
			[
				'label' => __( 'Use a button', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
				'selectors'		=>	[
					'{{WRAPPER}} .media-link-detail' => 'height: 100%;width: 100%;display: inline-block;position: absolute;',
				],
			]
		);

		$this->add_control(
			'button',
			[
				'label' => __( 'Button Text', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'separator' => 'after',
				'condition' => [
					'use_button'=>'yes',
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'jvfrmtd' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://your-link.com', 'jvfrmtd' ),
				'description' => __('Single Post : {permalink_url} <br/> Category : {term_permalink}', 'jvfrmtd'),
				'separator' => 'after',
			]
		);

		$this->add_control(
			'link_range',
			[
				'label' => __( 'Link Range', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'whole_card' => __( 'Whole Card', 'jvfrmtd' ),
					'button_only' => __( 'Button Only', 'jvfrmtd' ),
				],
				'default' => 'whole_card',
				'separator' => 'none',
				'condition' => [
					'link[url]!' => '',
				],
			]
		);

		$this->add_control( 'link_range_zindex', [
			'label' => __( 'Z-Index', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '1',
			'description' => __( 'It`s mostly 1 z-index in whole Card', 'jvfrmtd' ),
			'selectors'=>[
				'{{WRAPPER}} .module-card .one-block-whole-link' => 'z-index: {{VALUE}}',
			],
			'condition' => [
				'link_range' => 'whole_card',
			],
		] );




		$this->end_controls_section();

		$this->start_controls_section(
			'content_img_style',
			[
				'label' => __( 'Media / Image', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'heading_custom_bg_image_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Image', 'jvfrmtd' ),
				'condition' => [
					'custom_bg_image[url]!' => '',
					'scheme' => 'two-block',
				],
				'separator' => 'none',
				'condition' => [
					'scheme' => 'two-block',
				],
			]
		);

		$this->add_responsive_control(
			'image_min_width',
			[
				'label' => __( 'Min. Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .module-card__bg-wrapper' => 'min-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'scheme' => 'two-block',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_min_height',
			[
				'label' => __( 'Min. Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],

				'selectors' => [
					'{{WRAPPER}} .module-card__bg-wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'scheme' => 'two-block',
				],
			]
		);

		$this->add_control(
			'img_advanced_size',
			[
				'label' => __( 'Advanced Image Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
			]
		);


		$this->add_responsive_control(
			'image_width',
			[
				'label' => __( 'Image Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],

				'selectors' => [
					'{{WRAPPER}} .module-card__bg-wrapper' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'img_advanced_size' => 'yes',
				],
			]
		);


		$this->add_responsive_control(
			'image_height',
			[
				'label' => __( 'Image Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],

				'selectors' => [
					'{{WRAPPER}} .module-card__bg-wrapper' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'img_advanced_size' => 'yes',
				],
			]
		);


		$this->add_control(
			'img_radius',
			[
				'label' => __( 'Image Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
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
					'{{WRAPPER}} .module-card__bg' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'img_advanced_size' => 'yes',
				],
			]
		);


		$this->add_control(
			'img_center',
			[
				'label' => __( 'Image Center', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .module-card__bg-wrapper' => 'margin: 0 auto;',
				],
				'condition' => [
					'img_advanced_size' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style',
			[
				'label' => __( 'Content Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'min-height',
			[
				'label' => __( 'Min. Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
					],
					'vh' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],
				'selectors' => [
					'{{WRAPPER}} .module-card__content' => 'min-height: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .module-card__content' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
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
				'prefix_class' => 'module-card--valign-',
				'separator' => 'none',
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .module-card__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'content_media_style',
			[
				'label' => __( 'Additional Media', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content_media!' => 'none',
				],
			]
		);

		$this->add_control(
			'graphic_image_spacing',
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
					'{{WRAPPER}} .module-card__image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_media' => 'image',
				],
			]
		);

		$this->add_control(
			'graphic_image_width',
			[
				'label' => __( 'Size (%)', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .module-card__image img' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'content_media' => 'image',
				],
			]
		);

		$this->add_control(
			'graphic_image_opacity',
			[
				'label' => __( 'Opacity (%)', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .module-card__image' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'content_media' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'graphic_image_border',
				'selector' => '{{WRAPPER}} .module-card__image img',
				'condition' => [
					'content_media' => 'image',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'graphic_image_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .module-card__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'content_media' => 'image',
				],
			]
		);

		$this->add_control(
			'icon_spacing',
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
					'{{WRAPPER}} .elementor-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_media' => 'featured_category_icon',
				],
			]
		);

		$this->add_control(
			'icon_primary_color',
			[
				'label' => __( 'Primary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-view-framed .elementor-icon, {{WRAPPER}} .elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}}',
				],
				'condition' => [
					'content_media' => 'featured_category_icon',
				],
			]
		);

		$this->add_control(
			'icon_secondary_color',
			[
				'label' => __( 'Secondary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon_view!' => 'default',
					'content_media' => 'featured_category_icon',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'content_media' => 'featured_category_icon',
				],
			]
		);

		$this->add_control(
			'icon_padding',
			[
				'label' => __( 'Icon Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'condition' => [
					'content_media' => 'featured_category_icon',
					'icon_view!' => 'default',
				],
			]
		);

		$this->add_control(
			'icon_border_width',
			[
				'label' => __( 'Border Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'content_media' => 'featured_category_icon',
					'icon_view' => 'framed',
				],
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_media' => 'featured_category_icon',
					'icon_view!' => 'default',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_body_style',
			[
				'label' => __( 'Content', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'title',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'description',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'button',
							'operator' => '!==',
							'value' => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'heading_style_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Title', 'jvfrmtd' ),
				'separator' => 'before',
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .module-card__title',
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Spacing', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .module-card__title:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_control(
			'heading_style_description',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Description', 'jvfrmtd' ),
				'separator' => 'before',
				'condition' => [
					'description!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .module-card__description',
				'condition' => [
					'description!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label' => __( 'Spacing', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .module-card__description:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'description!' => '',
				],
			]
		);

		$this->add_control(
			'custom_meta1_style_description',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Custom Meta1', 'jvfrmtd' ),
				'separator' => 'before',
				'condition' => [
					'custom_meta1!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'custom_meta1_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .module-card__custom_meta1',
				'condition' => [
					'custom_meta1!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'custom_meta1_spacing',
			[
				'label' => __( 'Spacing', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .module-card__custom_meta1' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_meta1!' => '',
				],
			]
		);

		$this->add_control(
			'custom_meta2_style_description',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Custom Meta2', 'jvfrmtd' ),
				'separator' => 'before',
				'condition' => [
					'custom_meta2!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'custom_meta2_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .module-card__custom_meta2',
				'condition' => [
					'custom_meta2!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'custom_meta2_spacing',
			[
				'label' => __( 'Spacing', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .module-card__custom_meta2' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_meta2!' => '',
				],
			]
		);


		$this->add_control(
			'heading_content_colors',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Colors', 'jvfrmtd' ),
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'color_tabs' );

		$this->start_controls_tab( 'colors_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'content_bg_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__content' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'scheme' => 'two-block',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Description Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__description' => 'color: {{VALUE}}',
				],
				'condition' => [
					'description!' => '',
				],
			]
		);

		$this->add_control(
			'custom_meta1_color',
			[
				'label' => __( 'Custom Meta1', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__custom_meta1' => 'color: {{VALUE}};',
				],
				'condition' => [
					'custom_meta1!' => '',
				],
			]
		);

		$this->add_control(
			'custom_meta2_color',
			[
				'label' => __( 'Custom Meta1', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__custom_meta2' => 'color: {{VALUE}};',
				],
				'condition' => [
					'custom_meta2!' => '',
				],
			]
		);



		$this->end_controls_tab();

		$this->start_controls_tab(
			'colors_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'content_bg_color_hover',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card:hover .module-card__content' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'scheme' => 'two-block',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label' => __( 'Title Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card:hover .module-card__title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_control(
			'description_color_hover',
			[
				'label' => __( 'Description Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card:hover .module-card__description' => 'color: {{VALUE}}',
				],
				'condition' => [
					'description!' => '',
				],
			]
		);

		$this->add_control(
			'custom_meta1_color_hover',
			[
				'label' => __( 'Custom Meta1', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card:hover .module-card__custom_meta1' => 'color: {{VALUE}};',
				],
				'condition' => [
					'custom_meta1!' => '',
				],
			]
		);

		$this->add_control(
			'custom_meta2_color_hover',
			[
				'label' => __( 'Custom Meta1', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card:hover .module-card__custom_meta2' => 'color: {{VALUE}};',
				],
				'condition' => [
					'custom_meta2!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'button_style',
			[
				'label' => __( 'Button', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'use_button'=>'yes',
				],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label' => __( 'Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => [
					'xs' => __( 'Extra Small', 'jvfrmtd' ),
					'sm' => __( 'Small', 'jvfrmtd' ),
					'md' => __( 'Medium', 'jvfrmtd' ),
					'lg' => __( 'Large', 'jvfrmtd' ),
					'xl' => __( 'Extra Large', 'jvfrmtd' ),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => __( 'Typography', 'jvfrmtd' ),
				'selector' => '{{WRAPPER}} .module-card__button',
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
			]
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab( 'button_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label' => __( 'Border Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button-hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'button_hover_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card__button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_border_width',
			[
				'label' => __( 'Border Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .module-card__button' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .module-card__button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_ribbon_style',
			[
				'label' => __( 'Ribbon', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => [
					'ribbon_title!' => '',
				],
			]
		);

		$this->add_control(
			'ribbon_bg_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-ribbon-inner' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ribbon_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-ribbon-inner' => 'color: {{VALUE}}',
				],
			]
		);

		$ribbon_distance_transform = is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg)' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg)';

		$this->add_responsive_control(
			'ribbon_distance',
			[
				'label' => __( 'Distance', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . $ribbon_distance_transform,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ribbon_typography',
				'selector' => '{{WRAPPER}} .elementor-ribbon-inner',
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .elementor-ribbon-inner',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'hover_effects',
			[
				'label' => __( 'Overlay', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_hover_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Content ', 'jvfrmtd' ),
				'separator' => 'before',
				'condition' => [
					'scheme' => 'one-block',
				],
			]
		);

		$this->add_control(
			'content_animation',
			[
				'label' => __( 'Hover Animation', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'groups' => [
					[
						'label' => __( 'None', 'jvfrmtd' ),
						'options' => [
							'' => __( 'None', 'jvfrmtd' ),
						],
					],
					[
						'label' => __( 'Entrance', 'jvfrmtd' ),
						'options' => [
							'enter-from-right' => 'Slide In Right',
							'enter-from-left' => 'Slide In Left',
							'enter-from-top' => 'Slide In Up',
							'enter-from-bottom' => 'Slide In Down',
							'enter-zoom-in' => 'Zoom In',
							'enter-zoom-out' => 'Zoom Out',
							'fade-in' => 'Fade In',
						],
					],
					[
						'label' => __( 'Reaction', 'jvfrmtd' ),
						'options' => [
							'grow' => 'Grow',
							'shrink' => 'Shrink',
							'move-right' => 'Move Right',
							'move-left' => 'Move Left',
							'move-up' => 'Move Up',
							'move-down' => 'Move Down',
						],
					],
					[
						'label' => __( 'Exit', 'jvfrmtd' ),
						'options' => [
							'exit-to-right' => 'Slide Out Right',
							'exit-to-left' => 'Slide Out Left',
							'exit-to-top' => 'Slide Out Up',
							'exit-to-bottom' => 'Slide Out Down',
							'exit-zoom-in' => 'Zoom In',
							'exit-zoom-out' => 'Zoom Out',
							'fade-out' => 'Fade Out',
						],
					],
				],
				'default' => 'grow',
				'condition' => [
					'scheme' => 'one-block',
				],
			]
		);

		/*
		 *
		 * Add class 'elementor-animated-content' to widget when assigned content animation
		 *
		 */
		$this->add_control(
			'animation_class',
			[
				'label' => 'Animation',
				'type' => Controls_Manager::HIDDEN,
				'default' => 'animated-content',
				'prefix_class' => 'elementor-',
				'condition' => [
					'content_animation!' => '',
				],
			]
		);

		$this->add_control(
			'content_animation_duration',
			[
				'label' => __( 'Animation Duration', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'default' => [
					'size' => 1000,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .module-card__content-item' => 'transition-duration: {{SIZE}}ms',
					'{{WRAPPER}}.module-card--sequenced-animation .module-card__content-item:nth-child(2)' => 'transition-delay: calc( {{SIZE}}ms / 3 )',
					'{{WRAPPER}}.module-card--sequenced-animation .module-card__content-item:nth-child(3)' => 'transition-delay: calc( ( {{SIZE}}ms / 3 ) * 2 )',
					'{{WRAPPER}}.module-card--sequenced-animation .module-card__content-item:nth-child(4)' => 'transition-delay: calc( ( {{SIZE}}ms / 3 ) * 3 )',
				],
				'condition' => [
					'content_animation!' => '',
					'scheme' => 'one-block',
				],
			]
		);

		$this->add_control(
			'sequenced_animation',
			[
				'label' => __( 'Sequenced Animation', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'jvfrmtd' ),
				'label_off' => __( 'Off', 'jvfrmtd' ),
				'return_value' => 'module-card--sequenced-animation',
				'prefix_class' => '',
				'condition' => [
					'content_animation!' => '',
				],
			]
		);

		$this->add_control(
			'background_hover_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Background ', 'jvfrmtd' ),
				'separator' => 'before',
				'condition' => [
					'scheme' => 'one-block',
				],
			]
		);

		$this->add_control(
			'transformation',
			[
				'label' => __( 'Hover Animation', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => 'None',
					'zoom-in' => 'Zoom In',
					'zoom-out' => 'Zoom Out',
					'move-left' => 'Move Left',
					'move-right' => 'Move Right',
					'move-up' => 'Move Up',
					'move-down' => 'Move Down',
				],
				'default' => 'zoom-in',
				'prefix_class' => 'elementor-bg-transform elementor-bg-transform-',
			]
		);

		$this->start_controls_tabs( 'bg_effects_tabs' );

		$this->start_controls_tab( 'normal', [
			'label' => __( 'Normal', 'jvfrmtd' ),
		] );

		/*
		$this->add_control( 'overlay_color', [
			'label' => __( 'Overlay Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .module-card:not(:hover) .module-card__bg-overlay' => 'background-color: {{VALUE}}',
			],
		] ); */

		$this->add_group_control(
			\JV_Group_Control_Box_Style::get_type(),
			Array(
				'name' => 'bg_effect_normal',
				'label' => esc_html__( "Gradient", 'jvfrmtd' ),
				'selector' => '{{WRAPPER}} .module-card:not(:hover) .module-card__bg-overlay',
			)
		);

		$this->add_control(
			'overlay_blend_mode',
			[
				'label' => __( 'Blend Mode', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Normal', 'jvfrmtd' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .module-card__bg-overlay' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		/*
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'bg_filters',
				'selector' => '{{WRAPPER}} .module-card__bg',
			]
		);
		*/

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);
		/*
		$this->add_control(
			'overlay_color_hover',
			[
				'label' => __( 'Overlay Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .module-card:hover .module-card__bg-overlay' => 'background-color: {{VALUE}}',
				],
			]
		); */
		$this->add_group_control(
			\JV_Group_Control_Box_Style::get_type(),
			Array(
				'name' => 'bg_effect_hover',
				'label' => esc_html__( "Gradient", 'jvfrmtd' ),
				'selector' => '{{WRAPPER}} .module-card:hover .module-card__bg-overlay',
			)
		);

		/*
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'bg_filters_hover',
				'selector' => '{{WRAPPER}} .module-card:hover .module-card__bg',
			]
		);
		*/

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'effect_duration',
			[
				'label' => __( 'Effect Duration', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'default' => [
					'size' => 1500,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .module-card .module-card__bg, {{WRAPPER}} .module-card .module-card__bg-overlay' => 'transition-duration: {{SIZE}}ms',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function add_badge_control( $instance, $direction='', $dirLabel='' ) {

		$controls = Array(
			'badge_%s_setting' => Array(
				'label' => __( 'Badge %s Setting', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				'condition'	=> Array(
					//'badge_%s' =>'yes',
					//'badge_%s_field!' => '',
				),
			),
			'badge_%s_content' => Array(
				'label' => __( 'Field', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'listing_category',
				'options' => $instance->getReplaceOptions(),
				'condition' => [
					'badge_%s_setting' => 'yes',
				]
			),
			'badge_%s_content_price_currency_unit' => Array(
				'label' => esc_html__( 'Currency Unit', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'options' => Array(
					'' => esc_html__( 'None', 'jvfrmtd' ),
					'before' => esc_html__( 'Before', 'jvfrmtd' ),
					'after' => esc_html__( 'After', 'jvfrmtd' ),
				),
				'condition' => Array(
					'badge_%s_setting' => 'yes',
					'badge_%s_content' => Array( '_price', '_sale_price', '_print_price' ),
				),
			),
			'badge_%s_content_rating_type' => Array(
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__("Rating Type", 'jvfrmtd'),
				'default' => 'image',
				'prefix_class' => 'rating-type-',
				'options' => Array(
					'image' => esc_html__("Image", 'jvfrmtd'),
					'icon' => esc_html__("Icon", 'jvfrmtd'),
					'number' => esc_html__("Number", 'jvfrmtd'),
					'star_number' => esc_html__("Star + Rating", 'jvfrmtd'),
				),
				'condition' => Array(
					'badge_%s_setting' => 'yes',
					'badge_%s_content' => 'rating',
				),
			),
			'badge_%s_content_rating_icon_base_icon_color' => Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Rating Icon Base Color", 'jvfrmtd'),
				'condition' => Array( 'rating_type' => 'icon'),
				'default' => '#FFFFFF',
				'selectors' => Array(
					'{{WRAPPER}}.rating-type-icon .cblock-media-%s .rating-wrap i.fa-star-o' => 'color:{{VALUE}};',
				),
				'condition' => Array(
					'badge_%s_setting' => 'yes',
					'badge_%s_content_rating_type' => 'icon',
				),
			),
			'badge_%s_content_rating_icon_icon_color' => Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Rating Icon Color", 'jvfrmtd'),
				'default' => '#000000',
				'selectors' => Array(
					'{{WRAPPER}}.rating-type-icon .cblock-media-%s .rating-wrap i.fa-star' => 'color:{{VALUE}};',
				),
				'condition' => Array(
					'badge_%s_setting' => 'yes',
					'badge_%s_content_rating_type' => 'icon',
				),
			),
			'badge_%s_content_rating_number_bg_color' => Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Rating Number Background Color", 'jvfrmtd'),
				'default' => '#000000',
				'selectors' => Array(
					'{{WRAPPER}}.rating-type-number .cblock-media-%s .rating-wrap' => 'background-color:{{VALUE}}; display:inline-block; padding:5px 10px;',
				),
				'condition' => Array(
					'badge_%s_setting' => 'yes',
					'badge_%s_content_rating_type' => 'number',
				),
			),
			'badge_%s_content_rating_number_color' => Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Rating Number Color", 'jvfrmtd'),
				'default' => '#FFFFFF',
				'selectors' => Array(
					'{{WRAPPER}}.rating-type-number .cblock-media-%s .rating-wrap' => 'color:{{VALUE}};',
				),
				'condition' => Array(
					'badge_%s_setting' => 'yes',
					'badge_%s_content_rating_type' => 'number',
				),
			),
			'badge_%s_content_label_hidden' => Array(
				'label' => __( 'Hide Field Label', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => Array(
					'badge_%s_setting' => 'yes',
					'badge_%s_content' => 'favorite',
				),
				'selectors' => Array(
					 '{{WRAPPER}} .cblock-media-%s .lava-favorite .button-label' => 'display:none;',
				),
			),
			'badge_%s_content_image_size' => Array(
				'label' => __( 'Image Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'options' => jvbpd_elements_tools()->get_image_sizes(),
				'condition' => [
					'badge_%s_setting' => 'yes',
					'badge_%s_content' => Array( '_logo', 'post_author_avatar' ),
				]
			),
			'badge_%s_field_position_x' => Array(
				'label' => __( 'From Left', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 12,
					'unit'	=> '%',
				],
				'size_units' => [ '%'],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'position:absolute; left: {{SIZE}}{{UNIT}};',
				],
				 'condition' => [
				 	'badge_%s_setting' => 'yes',
				 ]
			),
			'badge_%s_field_position_y' => Array(
				'label' => __( 'From Top', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 12,
					'unit'	=> '%',
				],
				'size_units' => ['%'],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => ' top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
				]
			),
			'badge_%s_text_color' => Array(
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'color: {{VALUE}};',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
				]
			),
			'badge_%s_text_bgcolor' => Array(
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#000',
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
				]
			),
			'badge_%s_typo' => Array(
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .cblock-media-%s',
				'condition' => [
					'badge_%s_setting' => 'yes',
					//'badge_%s'=>'yes',
					//'badge_%s_field!' => '',
				],
				'group_ctl' => Group_Control_Typography::get_type(),
			),
			'badge_%s_padding' => Array(
				'label' => __( '%s Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '2',
					'right' => '8',
					'bottom' => '2',
					'left' => '8',
					'unit' => 'px',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
					//'badge_%s'=>'yes',
					//'badge_%s_field!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'responsive_ctl' => true,
			),
			'badge_%s_radius' => Array(
				'label' => __( '%s Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '2',
					'right' => '2',
					'bottom' => '2',
					'left' => '2',
					'unit' => 'px',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
					//'badge_%s'=>'yes',
					//'badge_%s_field!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'responsive_ctl' => true,
			),
		);

		//$instance->start_controls_section( 'section_badge_' . $direction, Array(
			//'label' => sprintf( esc_html__( 'Badge %s', 'jvfrmtd' ), $dirLabel )
		//) );

		foreach( $controls as $control_key => $control_meta ){
			$controlName = sprintf( $control_key, $direction );

			if( false !== strpos( $direction, '_right' ) && 'badge_%s_field_position_x' == $control_key ) {
				$control_meta[ 'label' ] = esc_html__( "From Right", 'jvfrmtd' );
				$control_meta[ 'selectors' ] = Array(
					'{{WRAPPER}} .cblock-media-%s' => 'position:absolute; right: {{SIZE}}{{UNIT}};',
				);
			}

			if( false !== strpos( $direction, 'bottom_' ) && 'badge_%s_field_position_y' == $control_key ) {
				$control_meta[ 'label' ] = esc_html__( "From Bottom", 'jvfrmtd' );
				$control_meta[ 'selectors' ] = Array(
					'{{WRAPPER}} .cblock-media-%s' => 'position:absolute; bottom: {{SIZE}}{{UNIT}};',
				);
			}

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
					if( 'badge_%s_text_color' == $control_key ) {
						$newKey .= ','  . $newKey . '  a' ;
					}
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
		//$instance->end_controls_section();
	}

	protected function printfBadge() {
		if( self::MODULE_CARD != $this->get_name() ) {
			return;
		}
		foreach( Array( 'top_left', 'top_right', 'bottom_left', 'bottom_right' ) as $direction ) {
			$settingKey = sprintf( 'badge_%s_setting', $direction );
			$this->add_render_attribute( 'badge_' . $direction . '_setting', array(
				'class' => Array( 'media-badges', 'cblock-media-' . str_replace( '_', '-', $direction ) ),
			) );
			if( 'yes' === $this->get_settings( $settingKey ) ) {
				$param = false;
				$imageSize = $this->get_settings( 'badge_' . $direction . '_content_image_size' );
				if( !empty( $imageSize ) ) {
					$param = $imageSize;
				}
				if( 'rating' == $this->get_settings( 'badge_' . $direction . '_content' ) ) {
					$param = $this->get_settings( 'badge_' . $direction . '_content_rating_type' );
				}
				if( in_array($this->get_settings( 'badge_' . $direction . '_content' ), Array('_price', '_sale_price', '_print_price') ) ) {
					$param = $this->get_settings( 'badge_' . $direction . '_content_price_currency_unit' );
				}
				$badgeFormat = false === $param ? '{%1$s}' : '{%1$s:%2$s}'; ?>
				<div <?php echo $this->get_render_attribute_string( 'badge_' . $direction . '_setting' ); ?>>
					<?php printf( $badgeFormat, $this->get_settings( 'badge_' . $direction . '_content' ), $param ); ?>
				</div>
				<?php
			}
		}
	}

	public function getCardTitle() {
		$format = '%1$s';
		if( self::MODULE_CARD == $this->get_name() ) {
			$format = '{%1$s:%2$s}';
		}
		return sprintf( $format, $this->get_settings( 'title' ), $this->get_settings( 'title_length' ) );
	}

	public function getCardDescription() {
		$format = '%1$s';
		if( self::MODULE_CARD == $this->get_name() ) {
			$format = '{%1$s:%2$s}';
		}
		return sprintf( $format, $this->get_settings( 'description' ), $this->get_settings( 'description_length' ) );
	}

	protected function __card_render() {
		$settings = $this->get_settings();

		$title_tag = $settings['title_tag'];
		$wrapper_tag = $overlay_tag ='div';
		$button_tag = 'a';
		$link_url = empty( $settings['link']['url'] ) ? false : $settings['link']['url'];
		$custom_bg_image = '';
		$content_animation = $settings['content_animation'];
		$animation_class = '';
		$print_bg = true;
		$print_content = true;

		// foreach( Array( 'bg_select' => 'custom_bg_image', 'content_media' =>

		switch( $this->get_settings( 'bg_select' ) ) {
			case 'custom_image':
				if ( ! empty( $settings['custom_bg_image']['id'] ) ) {
					$custom_bg_image = Group_Control_Image_Size::get_attachment_image_src( $settings['custom_bg_image']['id'], 'custom_bg_image', $settings );
				} elseif ( ! empty( $settings['custom_bg_image']['url'] ) ) {
					$custom_bg_image = $settings['custom_bg_image']['url'];
				}
				break;
			case 'category_image': $custom_bg_image = sprintf( '{term_featured_image:%s|%s}', $this->get_settings( 'thumb_size' ), $this->get_settings( 'default_image' )['url'] ); break;
			case 'bp_cover_image': $custom_bg_image = sprintf( '{bp_coverimage_url:%s|%s}', $this->get_settings( 'thumb_size' ), $this->get_settings( 'default_image' )['url'] ); break;
			case 'featured_image': default: $custom_bg_image = sprintf( '{thumbnail_url:%s|%s}', $this->get_settings( 'thumb_size' ), $this->get_settings( 'default_image' )['url'] );
		}

		/*
		<?php if ( 'image' === $settings['content_media'] && ! empty( $settings['graphic_image']['url'] ) ) : ?>
			<div <?php echo $this->get_render_attribute_string( 'content_media' ); ?>>
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings , 'graphic_image' ); ?>
			</div>
		<?php elseif ( 'icon' === $settings['content_media'] && ! empty( $settings['icon'] ) ) : ?>
			<div <?php echo $this->get_render_attribute_string( 'content_media' ); ?>>
				<div class="elementor-icon">
					<i <?php echo $this->get_render_attribute_string( 'icon' ); ?>></i>
				</div>
			</div>
		<?php endif; ?>

		if ( empty( $custom_bg_image ) && 'two-block' == $settings['scheme'] ) {
			$print_bg = false;
		}

		if ( empty( $settings['title'] ) && empty( $settings['description'] ) && empty( $settings['button'] ) && 'none' == $settings['content_media'] ) {
			$print_content = false;
		} */

		$this->add_render_attribute( 'background_image', Array(
			'style' => 'background-image: url(' . $custom_bg_image . ');',
			// 'data-src' => $custom_bg_image,
		) );

		$this->add_render_attribute( 'title', 'class', [
			'module-card__title',
			'module-card__content-item',
			'elementor-content-item',
		] );

		$this->add_render_attribute( 'description', 'class', [
			'module-card__description',
			'module-card__content-item',
			'elementor-content-item',
		] );

		$this->add_render_attribute( 'custom_meta1', 'class', [
			'module-card__custom_meta1',
			'module-card__content-item',
			'elementor-content-item',
		] );

		$this->add_render_attribute( 'custom_meta2', 'class', [
			'module-card__custom_meta2',
			'module-card__content-item',
			'elementor-content-item',
		] );

		$this->add_render_attribute( 'button', 'class', [
			'module-card__button',
			'elementor-button',
			'elementor-size-' . $settings['button_size'],
		] );

		$this->add_render_attribute( 'content_media', 'class',
			[
				'elementor-content-item',
				'module-card__content-item',
			]
		);

		if ( 'featured_category_icon' === $settings['content_media'] ) {
			$this->add_render_attribute( 'content_media', 'class',
				[
					'elementor-icon-wrapper',
					'module-card__icon',
				]
			);
			$this->add_render_attribute( 'content_media', 'class', 'elementor-view-' . $settings['icon_view'] );
			if ( 'default' != $settings['icon_view'] ) {
				$this->add_render_attribute( 'content_media', 'class', 'elementor-shape-' . $settings['icon_shape'] );
			}
			//if ( ! empty( $settings['icon'] ) ) {
				//$this->add_render_attribute( 'icon', 'class', $settings['icon'] );
			//}
		} elseif ( 'image' === $settings['content_media'] && ! empty( $settings['graphic_image']['url'] ) ) {
			$this->add_render_attribute( 'content_media', 'class', 'module-card__image');
		}

		switch( $settings['content_media'] ) {
			case 'featured_image':
				$this->add_render_attribute( 'content_media', Array(
					'class' => Array(
						'elementor-content-item',
						'module-card__content-item',
					),
				) );
				$this->add_render_attribute( 'content_media_image', Array(
					'src' => '{thumbnail_url}',
				) );
				break;
			case 'featured_category_image':
				$this->add_render_attribute( 'content_media', Array(
					'class' => Array(
						'elementor-content-item',
						'module-card__content-item',
					),
				) );
				$this->add_render_attribute( 'content_media_image', Array(
					'src' => '{term_featured_image}',
				) );
				break;

			case 'featured_category_icon':
				$this->add_render_attribute( 'content_media', Array(
					'class' => Array(
						'elementor-icon-wrapper',
						'module-card__icon',
					),
				) );
				$this->add_render_attribute( 'icon', 'class', '{term_icon}' );
				break;
		}

		if ( ! empty( $content_animation ) && 'one-block' == $settings['scheme'] ) {
			$animation_class = 'elementor-animated-item--' . $content_animation;
			$this->add_render_attribute( 'title', 'class', $animation_class );
			$this->add_render_attribute( 'content_media', 'class', $animation_class );
			$this->add_render_attribute( 'description', 'class', $animation_class );
			$this->add_render_attribute( 'custom_meta2', 'class', $animation_class );
		}


		$this->add_render_attribute( 'overlay', array(
			'class' => 'module-card__bg-overlay',
		) );

		$this->add_render_attribute( 'link_in_body', 'class', 'one-block-whole-link' );

		//if( 'one-block' != $this->get_settings( 'scheme' ) || 'whole_card' != $this->get_settings( 'link_range' ) ) {
			//$this->add_render_attribute( 'link_in_body', 'class', 'hidden' );
		//}

		if ( ! empty( $link_url ) ) {
			$this->add_render_attribute( 'link_in_body', Array(
				'href' => $link_url,
			) );

			if( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'link_in_body', 'target', '_blank' );
			}

			if ( 'whole_card' === $settings['link_range'] ) {
				//$wrapper_tag = 'a';
				$overlay_tag = 'a';
				$button_tag  = 'button';
				$this->add_render_attribute( 'overlay', 'href', $link_url );
				if ( $settings['link']['is_external'] ) {
					$this->add_render_attribute( 'overlay', 'target', '_blank' );
				}
			} else {
				$this->add_render_attribute( 'button', 'href', $link_url );
				if ( $settings['link']['is_external'] ) {
					$this->add_render_attribute( 'button', 'target', '_blank' );
				}
			}
		}

		/*
		$this->add_inline_editing_attributes( 'title' );
		$this->add_inline_editing_attributes( 'description' );
		$this->add_inline_editing_attributes( 'custom_meta' );
		$this->add_inline_editing_attributes( 'button' ); */

		?>
		<<?php echo $wrapper_tag . ' ' . $this->get_render_attribute_string( 'wrapper' ); ?> class="module-card">

		<?php if ( $print_bg ) : ?>
			<div class="module-card__bg-wrapper">
				<div class="module-card__bg elementor-bg" <?php echo $this->get_render_attribute_string( 'background_image' ); ?>></div>
				<<?php echo $overlay_tag . ' ' . $this->get_render_attribute_string( 'overlay' );; ?>></<?php echo $overlay_tag; ?>>
				<?php
				if( 'two-block' == $this->get_settings( 'scheme' ) ) {
					echo $this->printfBadge();
				} ?>
			</div>
		<?php endif; ?>
		<?php if ( $print_content ) : ?>
			<div class="module-card__content">
				<a <?php echo $this->get_render_attribute_string( 'link_in_body' ); ?>></a>
				<?php
				switch( $settings['content_media'] ) {
					case 'image' :
						if( $settings['graphic_image']['url'] ) {
							?>
							<div <?php echo $this->get_render_attribute_string( 'content_media' ); ?>>
								<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings , 'graphic_image' ); ?>
							</div>
							<?php
						}
						break;
					case 'featured_category_icon' :
						?>
						<div <?php echo $this->get_render_attribute_string( 'content_media' ); ?>>
							<div class="elementor-icon">{term_icon}</div>
						</div>
						<?php
						break;
					case 'featured_image' :
						?>
						<div <?php echo $this->get_render_attribute_string( 'content_media' ); ?>>
							<img <?php echo $this->get_render_attribute_string( 'content_media_image' ); ?>>
						</div>
						<?php
						break;
					case 'featured_category_image' :
						?>
						<div <?php echo $this->get_render_attribute_string( 'content_media' ); ?>>
							<img <?php echo $this->get_render_attribute_string( 'content_media_image' ); ?>>
						</div>
						<?php
						break;
				} ?>


				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<<?php echo $title_tag . ' ' . $this->get_render_attribute_string( 'title' ); ?>>
						<?php echo $this->getCardTitle(); ?>
					</<?php echo $title_tag; ?>>
				<?php endif; ?>

				<?php if ( ! empty( $settings['custom_meta1'] ) ) : ?>
				<div <?php echo $this->get_render_attribute_string('custom_meta1'); ?>>
					<?php echo $settings['custom_meta1']; ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<div <?php echo $this->get_render_attribute_string( 'description' ); ?>>
						<?php echo $this->getCardDescription(); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $settings['custom_meta2'] ) ) : ?>
				<div <?php echo $this->get_render_attribute_string('custom_meta2'); ?>>
					<?php echo $settings['custom_meta2']; ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $settings['button'] ) ) : ?>
					<div class="module-card__button-wrapper module-card__content-item elementor-content-item <?php echo $animation_class; ?>">
					<<?php echo $button_tag . ' ' . $this->get_render_attribute_string( 'button' ); ?>>
						<?php echo $settings['button']; ?>
					</<?php echo $button_tag; ?>>
					</div>
				<?php endif; ?>
				<?php
				if( 'one-block' == $this->get_settings( 'scheme' ) ) {
					echo $this->printfBadge();
				} ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $settings['ribbon_title'] ) ) :
			$this->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon' );

			if ( ! empty( $settings['ribbon_horizontal_position'] ) ) {
				$this->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon-' . $settings['ribbon_horizontal_position'] );
			} ?>
			<div <?php echo $this->get_render_attribute_string( 'ribbon-wrapper' ); ?>>
				<div class="elementor-ribbon-inner"><?php echo $settings['ribbon_title']; ?></div>
			</div>
		<?php endif; ?>
		</<?php echo $wrapper_tag; ?>>
		<?php
	}

	protected function __card_content_template() {
		?>
		<#
		var wrapperTag = 'div',
			overlayTag = 'div',
			buttonTag = 'a',
			contentAnimation = settings.content_animation,
			animationClass,
			btnSizeClass = 'elementor-size-' + settings.button_size,
			printBg = true,
			printContent = true;

		view.addRenderAttribute( 'link_in_body', 'class', 'one-block-whole-link' );

		if( 'one-block' != settings.scheme || 'whole_card' != settings.link_range ) {
			view.addRenderAttribute( 'link_in_body', 'class', 'hidden' );
		}

		if ( 'whole_card' === settings.link_range ) {
			overlayTag = 'a';
			buttonTag = 'button';
			view.addRenderAttribute( 'overlay', 'href', '#' );
		}

		if ( '' !== settings.custom_bg_image.url ) {
			var custom_bg_image = {
				id: settings.custom_bg_image.id,
				url: settings.custom_bg_image.url,
				size: settings.custom_bg_image_size,
				dimension: settings.custom_bg_image_custom_dimension,
				model: view.getEditModel()
			};

			var bgImageUrl = elementor.imagesManager.getImageUrl( custom_bg_image );
		}

		/*
		if ( ! custom_bg_image && 'two-block' == settings.scheme ) {
			printBg = false;
		}

		if ( ! settings.title && ! settings.description && ! settings.button && 'none' == settings.content_media ) {
			printContent = false;
		} */

		if ( 'featured_category_icon' === settings.content_media ) {
			var iconWrapperClasses = 'elementor-icon-wrapper';
				iconWrapperClasses += ' module-card__image';
				iconWrapperClasses += ' elementor-view-' + settings.icon_view;
			if ( 'default' !== settings.icon_view ) {
				iconWrapperClasses += ' elementor-shape-' + settings.icon_shape;
			}
			view.addRenderAttribute( 'content_media', 'class', iconWrapperClasses );

		} else if ( 'image' === settings.content_media && '' !== settings.graphic_image.url ) {
			var image = {
				id: settings.graphic_image.id,
				url: settings.graphic_image.url,
				size: settings.graphic_image_size,
				dimension: settings.graphic_image_custom_dimension,
				model: view.getEditModel()
			};

			var imageUrl = elementor.imagesManager.getImageUrl( image );
			view.addRenderAttribute( 'content_media', 'class', 'module-card__image' );
		}

		if ( contentAnimation && 'one-block' === settings.scheme ) {

			var animationClass = 'elementor-animated-item--' + contentAnimation;

			view.addRenderAttribute( 'title', 'class', animationClass );

			view.addRenderAttribute( 'description', 'class', animationClass );

			view.addRenderAttribute( 'content_media', 'class', animationClass );
		}

		view.addRenderAttribute( 'background_image', 'style', 'background-image: url(' + bgImageUrl + ');' );
		/* view.addRenderAttribute( 'background_image', 'data-src', bgImageUrl ); */
		view.addRenderAttribute( 'title', 'class', [ 'module-card__title', 'module-card__content-item', 'elementor-content-item' ] );
		view.addRenderAttribute( 'description', 'class', [ 'module-card__description', 'module-card__content-item', 'elementor-content-item' ] );
		view.addRenderAttribute( 'button', 'class', [ 'module-card__button', 'elementor-button', btnSizeClass ] );
		view.addRenderAttribute( 'content_media', 'class', [ 'module-card__content-item', 'elementor-content-item' ] );

		#>

		<{{ wrapperTag }} class="module-card">

		<# if ( printBg ) { #>
			<div class="module-card__bg-wrapper">
				<div class="module-card__bg elementor-bg" {{{ view.getRenderAttributeString( 'background_image' ) }}}></div>
				<{{ overlayTag }} class="module-card__bg-overlay" {{{ view.getRenderAttributeString( 'overlay' ) }}}></{{ overlayTag }}>
				<# if ( 'yes' === settings.badge_top_left_setting ) { #>
					<div class="media-badges cblock-media-top-left">{{settings.badge_top_left_content}}</div>
				<# } #>
				<# if ( 'yes' === settings.badge_top_right_setting ) { #>
					<div class="media-badges cblock-media-top-right">{{settings.badge_top_right_content}}</div>
				<# } #>
				<# if ( 'yes' === settings.badge_bottom_left_setting ) { #>
					<div class="media-badges cblock-media-bottom-left">{{settings.badge_bottom_left_content}}</div>
				<# } #>
				<# if ( 'yes' === settings.badge_bottom_right_setting ) { #>
					<div class="media-badges cblock-media-bottom-right">{{settings.badge_bottom_right_content}}</div>
				<# } #>

			</div>
		<# } #>
		<# if ( printContent ) { #>
			<div class="module-card__content">
				<a {{{ view.getRenderAttributeString( 'link_in_body' ) }}}></a>
				<# if ( 'image' === settings.content_media && '' !== settings.graphic_image.url ) { #>
					<div {{{ view.getRenderAttributeString( 'content_media' ) }}}>
						<img src="{{ imageUrl }}">
					</div>
				<#  } else if ( 'featured_category_icon' === settings.content_media ) { #>
					<div {{{ view.getRenderAttributeString( 'content_media' ) }}}>
						<div class="elementor-icon">
							<i class="fa fa-star"></i>
						</div>
					</div>
				<# } #>
				<# if ( settings.title ) { #>
					<{{ settings.title_tag }} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{ settings.title_tag }}>
				<# } #>

				<# if ( settings.description ) { #>
					<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
				<# } #>

				<# if ( settings.button ) { #>
					<div class="module-card__button-wrapper module-card__content-item elementor-content-item {{ animationClass }}">
						<{{ buttonTag }} href="#" {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button }}}</{{ buttonTag }}>
					</div>
				<# } #>
			</div>
		<# } #>
		<# if ( settings.ribbon_title ) {
			var ribbonClasses = 'elementor-ribbon';

			if ( settings.ribbon_horizontal_position ) {
				ribbonClasses += ' elementor-ribbon-' + settings.ribbon_horizontal_position;
			} #>
			<div class="{{ ribbonClasses }}">
				<div class="elementor-ribbon-inner">{{{ settings.ribbon_title }}}</div>
			</div>
		<# } #>
		</{{ wrapperTag }}>
	<?php
	}
}