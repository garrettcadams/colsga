<?php
namespace jvbpdelement\Modules\Testimonial\Widgets;

use jvbpdelement\Base\Base_Widget;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Utils;

abstract class Base extends Base_Widget {

	Const TESTIMONIAL = 'jvbpd_testimonial';
	Const TESTIMONIAL_WIDE = 'jvbpd_testimonial_wide';
	Const FEATURED_BLOCK = 'jvbpd_featured_block';
	Const MEMBERS = 'jvbpd_members';

	public $testimonial = Array( self::TESTIMONIAL );
	public $testimonial_wide = Array( self::TESTIMONIAL_WIDE );
	public $featured_block = Array( self::FEATURED_BLOCK );
	public $members = Array( self::MEMBERS );

	public function get_categories() { return [ 'jvbpd-elements' ]; }

	public function get_widget_name() {
		$output = Array();
		if( in_array( $this->get_name(), $this->testimonial ) ) {
			$output = Array( 'testimonial' );
		}
		if( in_array( $this->get_name(), $this->testimonial_wide ) ) {
			$output = Array( 'testimonial-wide' );
		}
		if( in_array( $this->get_name(), $this->featured_block ) ) {
			$output = Array( 'featured-block' );
		}
		if( in_array( $this->get_name(), $this->members ) ) {
			$output = Array( 'members' );
		}
		return $output;
	}

	protected function _register_controls() {
		// Content options Start
		$this->start_controls_section(
			'section_content',
				[
					'label' => esc_html__( 'Testimonial Items!', 'jvfrmtd' ),
				]
			);


			$repeater = new Repeater();
			$repeater->add_control(
				'title_1',
					[
					  'label' => esc_html__( 'Title', 'jvfrmtd' ),
					  'type'  => Controls_Manager::TEXTAREA,
					  'default' => esc_html__( 'Jonathan Morgan', 'jvfrmtd' ),
					]
			);

			$repeater->add_control(
				'designation_1',
					[
					  'label' => esc_html__( 'Designation', 'jvfrmtd' ),
					  'type'  => Controls_Manager::TEXTAREA,
					  'default' => esc_html__( 'Jonathan Morgan', 'jvfrmtd' ),
					]
			);


			$repeater->add_control(
			   'testimoni_image_1',
					[
					  'label' => esc_html__( 'Testimonial Image', 'jvfrmtd' ),
					  'type'  => Controls_Manager::MEDIA,
					  'default' => [
							'url' => plugins_url('/widgetkit-for-elementor/assets/images/testimoni-demo.jpg'),
						],
					]
			);

			$repeater->add_control(
				'testimoni_content_1', [
					'label' => esc_html__( 'Description', 'jvfrmtd' ),
					'type'  => Controls_Manager::TEXTAREA,
					'default' => esc_html__( 'Corem ipsum dolor si amet consectetur adipisic ingelit sed do adipisicido executiv
					sunse pit lore kome.', 'jvfrmtd' ),
				]
			);

			$repeater->add_control( 'use_addition_desc', Array(
				'type' =>  Controls_Manager::SWITCHER,
				'label' => esc_html__("Additional Description (Popup)", 'jvfrmtd'),
				'frontend_available' => true,
			));

			$repeater->add_control( 'addition_desc', Array(
				'type' =>  Controls_Manager::WYSIWYG,
				'show_label' => false,
				'default' => esc_html__("Additional description content", 'jvfrmtd'),
				'condition' => Array('use_addition_desc'=>'yes'),
			));

			$this->add_control(
				'testimonial_option_1',
				  [
					  'label'       => esc_html__( 'Testimonials Options', 'jvfrmtd' ),
					  'type'        => Controls_Manager::REPEATER,
					  'show_label'  => true,
					  'default'     => [
						  [
							'title_1'       => esc_html__( 'Jonathan Morgan', 'jvfrmtd' ),
							'designation_1' => esc_html__( 'Marketting', 'jvfrmtd' ),
							'testimoni_content_1' => 'Enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo conse quat. Duis aute irure dolor in reprehenderit in voluptate.',
							'testimoni_image_1' => '',

						  ],
						  [
							'title_1'       => esc_html__( 'Harsul Hisham', 'jvfrmtd' ),
							'designation_1' => esc_html__( 'Engineer', 'jvfrmtd' ),
							'testimoni_content_1' => 'Enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo conse quat. Duis aute irure dolor in reprehenderit in voluptate.',
							'testimoni_image_1' => '',

						  ],
						  [
							'title_1'       => esc_html__( 'Teem Southy', 'jvfrmtd' ),
							'designation_1' => esc_html__( 'Developer', 'jvfrmtd' ),
							'testimoni_content_1' => 'Enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo conse quat. Duis aute irure dolor in reprehenderit in voluptate.',
							'testimoni_image_1' => '',

						  ]
					  ],
					  'fields'      => array_values( $repeater->get_controls() ),
					  'title_field' => '{{{title_1}}}',
				  ]
			  );

	  	$this->add_control(
			'slidesToShow',
				[
					'label'     => esc_html__( 'Amount items in per slider', 'jvfrmtd' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '3',
					'options'   => [
						'1'   => esc_html__( 'Item 1', 'jvfrmtd' ),
						'2'   => esc_html__( 'Item 2', 'jvfrmtd' ),
						'3'   => esc_html__( 'Item 3', 'jvfrmtd' ),
						'4'   => esc_html__( 'Item 4', 'jvfrmtd' ),
					],
				]
			);

		$this->end_controls_section();


		$this->start_controls_section(
			'layout_option', [
				'label' => esc_html__( 'Layout', 'jvfrmtd' ),
			]
		);

		$this->add_control( 'image_box_style', [
			'label'     => esc_html__( 'Image box style', 'jvfrmtd' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'image-box1',
			'options'   => [
				'image-box1'   => esc_html__( 'Bottom - inline ', 'jvfrmtd' ),
				'image-box2'   => esc_html__( 'Top - blocks', 'jvfrmtd' ),
				//'image-box3'   => esc_html__( 'Style 3', 'jvfrmtd' ),
			],
		] );

		$this->add_control( 'hide_quote', Array(
			'label' => esc_html__( "Hide quotation", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'selectors' => [
				'{{WRAPPER}} .testimony:before' => 'display:none;',
			],
		) );

		$this->add_control( 'hide_separator', Array(
			'label' => esc_html__( "Hide Separator", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'selectors' => [
				'{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper div.author' => 'border-top:none;',
				'{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper div.author:before' => 'display:none;',
			],
		) );

		$this->end_controls_section();


		$this->add_slick_setting_controls();

		/*
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

		// Carousel
		$this->add_control(
            'carousel_autoplay', [
                'label' => esc_html__( 'Carousel Autoplay', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'use_carousel' => 'yes',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_loop', [
                'label' => esc_html__( 'Carousel Infinity Loop', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'use_carousel' => 'yes',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_navigation', [
                'label' => esc_html__( 'Carousel Navigation', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'use_carousel' => 'yes',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_navi_position', [
				'label' => __( 'Carousel Navigation Position', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'carousel_navigation' => '1',
				],
				'default' => 'bottom',
				'options' => [
					'top' => __( 'Top', 'jvfrmtd' ),
					'middle' => __( 'Side', 'jvfrmtd' ),
					'bottom'  => __( 'Bottom', 'jvfrmtd' ),
				],
				'separator' => 'none',
            ]
        );

		$this->add_control(
            'carousel_animation', [
                'label' => esc_html__( 'Carousel Animation', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'use_carousel' => 'yes',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
            ]
        );

		$this->add_control(
            'carousel_animation1', [
				'label' => __( 'Animation out', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'carousel_animation' => 'yes',
				],
				'default' => 'fadeIn',
				'options' => [

				  'bounce' => __('bounce', 'jvfrmtd'),
				  'flash' => __('flash', 'jvfrmtd'),
				  'pulse' => __('pulse', 'jvfrmtd'),
				  'rubberBand' => __('rubberBand', 'jvfrmtd'),
				  'shake' => __('shake', 'jvfrmtd'),
				  'swing' => __('swing', 'jvfrmtd'),
				  'tada' => __('tada', 'jvfrmtd'),
				  'wobble' => __('wobble', 'jvfrmtd'),
				  'jello' => __('jello', 'jvfrmtd'),

				  'bounceIn' => __('bounceIn', 'jvfrmtd'),
				  'bounceInDown' => __('bounceInDown', 'jvfrmtd'),
				  'bounceInLeft' => __('bounceInLeft', 'jvfrmtd'),
				  'bounceInRight' => __('bounceInRight', 'jvfrmtd'),
				  'bounceInUp' => __('bounceInUp', 'jvfrmtd'),

				  'bounceOut' => __('bounceOut', 'jvfrmtd'),
				  'bounceOutDown' => __('bounceOutDown', 'jvfrmtd'),
				  'bounceOutLeft' => __('bounceOutLeft', 'jvfrmtd'),
				  'bounceOutRight' => __('bounceOutRight', 'jvfrmtd'),
				  'bounceOutUp' => __('bounceOutUp', 'jvfrmtd'),

				  'fadeIn' => __('fadeIn', 'jvfrmtd'),
				  'fadeInDown' => __('fadeInDown', 'jvfrmtd'),
				  'fadeInDownBig' => __('fadeInDownBig', 'jvfrmtd'),
				  'fadeInLeft' => __('fadeInLeft', 'jvfrmtd'),
				  'fadeInLeftBig' => __('fadeInLeftBig', 'jvfrmtd'),
				  'fadeInRight' => __('fadeInRight', 'jvfrmtd'),
				  'fadeInRightBig' => __('fadeInRightBig', 'jvfrmtd'),
				  'fadeInUp' => __('fadeInUp', 'jvfrmtd'),
				  'fadeInUpBig' => __('fadeInUpBig', 'jvfrmtd'),

				  'fadeOut' => __('fadeOut', 'jvfrmtd'),
				  'fadeOutDown' => __('fadeOutDown', 'jvfrmtd'),
				  'fadeOutDownBig' => __('fadeOutDownBig', 'jvfrmtd'),
				  'fadeOutLeft' => __('fadeOutLeft', 'jvfrmtd'),
				  'fadeOutLeftBig' => __('fadeOutLeftBig', 'jvfrmtd'),
				  'fadeOutRight' => __('fadeOutRight', 'jvfrmtd'),
				  'fadeOutRightBig' => __('fadeOutRightBig', 'jvfrmtd'),
				  'fadeOutUp' => __('fadeOutUp', 'jvfrmtd'),
				  'fadeOutUpBig' => __('fadeOutUpBig', 'jvfrmtd'),

				  'flip' => __('flip', 'jvfrmtd'),
				  'flipInX' => __('flipInX', 'jvfrmtd'),
				  'flipInY' => __('flipInY', 'jvfrmtd'),
				  'flipOutX' => __('flipOutX', 'jvfrmtd'),
				  'flipOutY' => __('flipOutY', 'jvfrmtd'),

				  'lightSpeedIn' => __('lightSpeedIn', 'jvfrmtd'),
				  'lightSpeedOut' => __('lightSpeedOut', 'jvfrmtd'),

				  'rotateIn' => __('rotateIn', 'jvfrmtd'),
				  'rotateInDownLeft' => __('rotateInDownLeft', 'jvfrmtd'),
				  'rotateInDownRight' => __('rotateInDownRight', 'jvfrmtd'),
				  'rotateInUpLeft' => __('rotateInUpLeft', 'jvfrmtd'),
				  'rotateInUpRight' => __('rotateInUpRight', 'jvfrmtd'),

				  'rotateOut' => __('rotateOut', 'jvfrmtd'),
				  'rotateOutDownLeft' => __('rotateOutDownLeft', 'jvfrmtd'),
				  'rotateOutDownRight' => __('rotateOutDownRight', 'jvfrmtd'),
				  'rotateOutUpLeft' => __('rotateOutUpLeft', 'jvfrmtd'),
				  'rotateOutUpRight' => __('rotateOutUpRight', 'jvfrmtd'),

				  'slideInUp' => __('slideInUp', 'jvfrmtd'),
				  'slideInDown' => __('slideInDown', 'jvfrmtd'),
				  'slideInLeft' => __('slideInLeft', 'jvfrmtd'),
				  'slideInRight' => __('slideInRight', 'jvfrmtd'),

				  'slideOutUp' => __('slideOutUp', 'jvfrmtd'),
				  'slideOutDown' => __('slideOutDown', 'jvfrmtd'),
				  'slideOutLeft' => __('slideOutLeft', 'jvfrmtd'),
				  'slideOutRight' => __('slideOutRight', 'jvfrmtd'),


				  'zoomIn' => __('zoomIn', 'jvfrmtd'),
				  'zoomInDown' => __('zoomInDown', 'jvfrmtd'),
				  'zoomInLeft' => __('zoomInLeft', 'jvfrmtd'),
				  'zoomInRight' => __('zoomInRight', 'jvfrmtd'),
				  'zoomInUp' => __('zoomInUp', 'jvfrmtd'),


				  'zoomOut' => __('zoomOut', 'jvfrmtd'),
				  'zoomOutDown' => __('zoomOutDown', 'jvfrmtd'),
				  'zoomOutLeft' => __('zoomOutLeft', 'jvfrmtd'),
				  'zoomOutRight' => __('zoomOutRight', 'jvfrmtd'),
				  'zoomOutUp' => __('zoomOutUp', 'jvfrmtd'),

				  'hinge' => __('hinge', 'jvfrmtd'),
				  'jackInTheBox' => __('jackInTheBox', 'jvfrmtd'),
				  'rollIn' => __('rollIn', 'jvfrmtd'),
				  'rollOut' => __('rollOut', 'jvfrmtd'),
				],
				'separator' => 'none',
            ]
        );

		$this->add_control(
            'carousel_animation2', [
				'label' => __( 'Animation in', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'carousel_animation' => 'yes',
				],
				'default' => 'fadeIn',
				'options' => [

				  'bounce' => __('bounce', 'jvfrmtd'),
				  'flash' => __('flash', 'jvfrmtd'),
				  'pulse' => __('pulse', 'jvfrmtd'),
				  'rubberBand' => __('rubberBand', 'jvfrmtd'),
				  'shake' => __('shake', 'jvfrmtd'),
				  'swing' => __('swing', 'jvfrmtd'),
				  'tada' => __('tada', 'jvfrmtd'),
				  'wobble' => __('wobble', 'jvfrmtd'),
				  'jello' => __('jello', 'jvfrmtd'),

				  'bounceIn' => __('bounceIn', 'jvfrmtd'),
				  'bounceInDown' => __('bounceInDown', 'jvfrmtd'),
				  'bounceInLeft' => __('bounceInLeft', 'jvfrmtd'),
				  'bounceInRight' => __('bounceInRight', 'jvfrmtd'),
				  'bounceInUp' => __('bounceInUp', 'jvfrmtd'),

				  'bounceOut' => __('bounceOut', 'jvfrmtd'),
				  'bounceOutDown' => __('bounceOutDown', 'jvfrmtd'),
				  'bounceOutLeft' => __('bounceOutLeft', 'jvfrmtd'),
				  'bounceOutRight' => __('bounceOutRight', 'jvfrmtd'),
				  'bounceOutUp' => __('bounceOutUp', 'jvfrmtd'),

				  'fadeIn' => __('fadeIn', 'jvfrmtd'),
				  'fadeInDown' => __('fadeInDown', 'jvfrmtd'),
				  'fadeInDownBig' => __('fadeInDownBig', 'jvfrmtd'),
				  'fadeInLeft' => __('fadeInLeft', 'jvfrmtd'),
				  'fadeInLeftBig' => __('fadeInLeftBig', 'jvfrmtd'),
				  'fadeInRight' => __('fadeInRight', 'jvfrmtd'),
				  'fadeInRightBig' => __('fadeInRightBig', 'jvfrmtd'),
				  'fadeInUp' => __('fadeInUp', 'jvfrmtd'),
				  'fadeInUpBig' => __('fadeInUpBig', 'jvfrmtd'),

				  'fadeOut' => __('fadeOut', 'jvfrmtd'),
				  'fadeOutDown' => __('fadeOutDown', 'jvfrmtd'),
				  'fadeOutDownBig' => __('fadeOutDownBig', 'jvfrmtd'),
				  'fadeOutLeft' => __('fadeOutLeft', 'jvfrmtd'),
				  'fadeOutLeftBig' => __('fadeOutLeftBig', 'jvfrmtd'),
				  'fadeOutRight' => __('fadeOutRight', 'jvfrmtd'),
				  'fadeOutRightBig' => __('fadeOutRightBig', 'jvfrmtd'),
				  'fadeOutUp' => __('fadeOutUp', 'jvfrmtd'),
				  'fadeOutUpBig' => __('fadeOutUpBig', 'jvfrmtd'),

				  'flip' => __('flip', 'jvfrmtd'),
				  'flipInX' => __('flipInX', 'jvfrmtd'),
				  'flipInY' => __('flipInY', 'jvfrmtd'),
				  'flipOutX' => __('flipOutX', 'jvfrmtd'),
				  'flipOutY' => __('flipOutY', 'jvfrmtd'),

				  'lightSpeedIn' => __('lightSpeedIn', 'jvfrmtd'),
				  'lightSpeedOut' => __('lightSpeedOut', 'jvfrmtd'),

				  'rotateIn' => __('rotateIn', 'jvfrmtd'),
				  'rotateInDownLeft' => __('rotateInDownLeft', 'jvfrmtd'),
				  'rotateInDownRight' => __('rotateInDownRight', 'jvfrmtd'),
				  'rotateInUpLeft' => __('rotateInUpLeft', 'jvfrmtd'),
				  'rotateInUpRight' => __('rotateInUpRight', 'jvfrmtd'),

				  'rotateOut' => __('rotateOut', 'jvfrmtd'),
				  'rotateOutDownLeft' => __('rotateOutDownLeft', 'jvfrmtd'),
				  'rotateOutDownRight' => __('rotateOutDownRight', 'jvfrmtd'),
				  'rotateOutUpLeft' => __('rotateOutUpLeft', 'jvfrmtd'),
				  'rotateOutUpRight' => __('rotateOutUpRight', 'jvfrmtd'),

				  'slideInUp' => __('slideInUp', 'jvfrmtd'),
				  'slideInDown' => __('slideInDown', 'jvfrmtd'),
				  'slideInLeft' => __('slideInLeft', 'jvfrmtd'),
				  'slideInRight' => __('slideInRight', 'jvfrmtd'),

				  'slideOutUp' => __('slideOutUp', 'jvfrmtd'),
				  'slideOutDown' => __('slideOutDown', 'jvfrmtd'),
				  'slideOutLeft' => __('slideOutLeft', 'jvfrmtd'),
				  'slideOutRight' => __('slideOutRight', 'jvfrmtd'),


				  'zoomIn' => __('zoomIn', 'jvfrmtd'),
				  'zoomInDown' => __('zoomInDown', 'jvfrmtd'),
				  'zoomInLeft' => __('zoomInLeft', 'jvfrmtd'),
				  'zoomInRight' => __('zoomInRight', 'jvfrmtd'),
				  'zoomInUp' => __('zoomInUp', 'jvfrmtd'),


				  'zoomOut' => __('zoomOut', 'jvfrmtd'),
				  'zoomOutDown' => __('zoomOutDown', 'jvfrmtd'),
				  'zoomOutLeft' => __('zoomOutLeft', 'jvfrmtd'),
				  'zoomOutRight' => __('zoomOutRight', 'jvfrmtd'),
				  'zoomOutUp' => __('zoomOutUp', 'jvfrmtd'),

				  'hinge' => __('hinge', 'jvfrmtd'),
				  'jackInTheBox' => __('jackInTheBox', 'jvfrmtd'),
				  'rollIn' => __('rollIn', 'jvfrmtd'),
				  'rollOut' => __('rollOut', 'jvfrmtd'),
				],
				'separator' => 'none',
            ]
        );

		$this->add_control(
            'carousel_dots', [
                'label' => esc_html__( 'Carousel Dots Navigation', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'use_carousel' => 'yes',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		/**
		$this->add_control(
            'carousel_lazyload', [
                'label' => esc_html__( 'Carousel Lazy Load', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'use_carousel' => 'yes',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        ); ** /

		$this->add_control(
            'carousel_mouse_wheel', [
                'label' => esc_html__( 'Carousel Enable MouseWheel', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'use_carousel' => 'yes',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		*/




		$this->end_controls_section();

		$this->start_controls_section(
		'layout_style',
			[
				'label' => esc_html__( 'Layout', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'bg_color',
				[
					'label'     => esc_html__( 'Background Color', 'jvfrmtd' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper' => 'background-color: {{VALUE}};',

					],
				]
			);

			$this->add_control(
				'item_center_bg_color',
				[
					'label'     => esc_html__( 'Center Bg/Hover Color', 'jvfrmtd' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .tgx-testimonial-1 .center .testimoni-wrapper,
						{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper:hover' => 'background-color: {{VALUE}};',

					],
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'    => 'item_box_shadow',
					'exclude' => [
						'box_shadow_position',
					],
					'selector' => '{{WRAPPER}} .tgx-testimonial-1 .center .testimoni-wrapper,
					{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper:hover:hover',
				]
			);

			$this->add_control( 'between_space', Array(
				'label' => esc_html__( 'Between space', 'jvfrmtd' ),
				'type'  => Controls_Manager::SLIDER,
				'default'  => Array(
					'size' => 14,
				),
				'range' => Array(
					'px' => Array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => Array(
					'{{WRAPPER}} .slick-slide' => 'margin: 0px {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-list' => 'margin: 0px -{{SIZE}}{{UNIT}};',
				),
			) );


		$this->end_controls_section();

		$this->start_controls_section(
				'section_style',
				[
					'label' => esc_html__( 'Content', 'jvfrmtd' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

		$this->add_control( 'icon_show1', Array(
			'label' => esc_html__( "Hide Separator", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'selector' =>[
				'{{WRAPPER}} .testimony:befor' => 'display:none;',
			],
		) );







			$this->add_group_control(
				Group_Control_Typography::get_type(),
					[
						'name'     => 'content_typography',
						'label'    => esc_html__( 'Content Typography', 'jvfrmtd' ),
						'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
						'selector' => '{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper .testimony',
					]
			);

			$this->add_control(
				'testimoni_1_color',
				[
					'label'     => esc_html__( 'Testimoni Color', 'jvfrmtd' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#676767',
					'selectors' => [
						'{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper .testimony' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_name',
				[
					'label' => esc_html__( 'Name/Designation', 'jvfrmtd' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'name_color',
				[
					'label'     => esc_html__( 'Name Color', 'jvfrmtd' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#182432',
					'selectors' => [
						'{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper .author .name' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
					[
						'name'     => 'name_typography',
						'label'    => esc_html__( 'Name Typography', 'jvfrmtd' ),
						'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
						'selector' => '{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper .author .name',
					]
			);



			$this->add_control(
				'designation_color',
				[
					'label'     => esc_html__( 'Designation Color', 'jvfrmtd' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#989898',
					'selectors' => [
						'{{WRAPPER}}  .tgx-testimonial-1 .testimoni-wrapper .author .designation' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
					[
						'name'     => 'designation_typography',
						'label'    => esc_html__( 'Designation Typography', 'jvfrmtd' ),
						'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
						'selector' => '{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper .author .designation',
					]
			);

			$this->add_control(
				'border_color',
				[
					'label'     => esc_html__( 'Border/Quite Color', 'jvfrmtd' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ddd',
					'selectors' => [
						'{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper .testimony:before' => 'color: {{VALUE}};',
						'{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper .author:before' => 'border-bottom-color: {{VALUE}};',
						'{{WRAPPER}} .tgx-testimonial-1 .testimoni-wrapper .author' => 'border-top:1px solid {{VALUE}};',
					],
				]
			);



			$this->add_control(
				'image_border_radius',
				[
					'label' => esc_html__( 'Image Border Radius', 'jvfrmtd' ),
					'type'  => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tgx-testimonial-1 .testimoni-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		$this->end_controls_section();
	}
	//


	protected function add_slick_setting_controls() {
		$this->start_controls_section( 'section_slider_option', Array(
			'label' => esc_html__( "Slider Option", 'jvfrmtd' ),
		) );

			$this->add_control( 'heading_general', Array(
				'label' => esc_html__( "General", 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			) );

			$this->add_control( 'infinite', Array(
				'label' => esc_html__( "Infinite loop", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'adaptiveHeight', Array(
				'label' => esc_html__( "Adapts slider height", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'none',
			) );

			$this->add_control( 'heading_autoplay', Array(
				'label' => esc_html__( "Autoplay", 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			) );

			$this->add_control( 'autoplay', Array(
				'label' => esc_html__( "Use Autoplay", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'autoplaySpeed', Array(
				'label' => esc_html__( "Autoplay Speed", 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => Array(
					'autoplay' => 'yes',
				),
				'separator' => 'none',
			) );

			$this->add_control( 'heading_navi', Array(
				'label' => esc_html__( "Navigation", 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			) );

			$this->add_control( 'dots', Array(
				'label' => esc_html__( "Dots", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'arrows', Array(
				'label' => esc_html__( "Arrows", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'heading_effect', Array(
				'label' => esc_html__( "Effects", 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			) );

			$this->add_control( 'fade', Array(
				'label' => esc_html__( "Use fade", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'lazyLoad', Array(
				'label' => esc_html__( "Lazyload", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ondemand',
				'options' => Array(
					'ondemand' => esc_html__( "Ondemand", 'jvfrmtd' ),
					'progressive' => esc_html__( "Progressive", 'jvfrmtd' ),
				),
				'separator' => 'none',
			) );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		$sliderSettings = Array();
		foreach( Array( 'adaptiveHeight', 'autoplay', 'autoplaySpeed', 'arrows', 'dots', 'fade', 'lazyLoad', 'slidesToShow', 'infinite' ) as $options ) {
			$sliderSettings[ $options ] = $this->get_settings( $options );
		}

		$this->add_render_attribute( 'container', Array(
			'class' => sprintf( '%s %s %s %s',
				'tgx-testimonial-1',
				$this->get_id(),
				$this->get_name(),
				'jvbpd-slider-wrap'
			),
			'data-settings' => wp_json_encode( $sliderSettings ),
		) ); ?>

	   <div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
          <?php foreach ( $settings['testimonial_option_1'] as $testimonial_1 ) : ?>
            <div class="testimoni-wrapper">

				<?php if ($settings['image_box_style']=='image-box1') { ?>
					<div class="testimony"> <p> <?php echo $testimonial_1['testimoni_content_1'];?></p></div>
				<?php } ?>

                <div class="author <?php echo $settings['image_box_style']; ?>">

                    <?php if ($settings['slidesToShow'] == '1'):?>
                        <div class="col-md-1">
                    <?php elseif ($settings['slidesToShow'] == '2'):?>
                        <div class="col-md-3">
                    <?php else:?>
                       <div class="col-md-4">
                    <?php endif;?>

                       <?php if ($testimonial_1['testimoni_image_1']['url']):?>
                            <span>
                                <img class="testimoni-image" src="<?php echo $testimonial_1['testimoni_image_1']['url']; ?>" alt="<?php the_title(); ?>">
                            </span>
                      <?php endif;?>

                    </div>
                    <?php
                        if ($settings['slidesToShow'] == '1'):?>
                            <div class="col-md-11">
                        <?php elseif ($settings['slidesToShow'] == '2'):?>
                            <div class="col-md-9">
                        <?php else:?>
                           <div class="col-md-8">
                        <?php endif;?>
                            <?php if ($testimonial_1['title_1']):?>
                              <h4 class="name"><?php echo $testimonial_1['title_1'];  ?></h4>
                            <?php endif; ?>

                            <?php if ($testimonial_1['designation_1']):?>
                              <p class="designation"><?php echo $testimonial_1['designation_1'];  ?></p>
                            <?php endif; ?>
                     </div>
                </div>
				<?php if ($settings['image_box_style']=='image-box2') { ?>
					<div class="testimony"> <p> <?php echo $testimonial_1['testimoni_content_1'];?></p></div>
				<?php } ?>

				<?php if ( ! empty( $settings['social_share_1'] ) ) : ?>
                        <div class="team-social">
                            <?php foreach ( $settings['social_share_1'] as $social ) : ?>
                                <?php if ( ! empty( $social['social_link'] ) ) : ?>
									<a href="<?php  echo $social['social_link'];?>" class="<?php  echo strtolower($social['title']);?>">
										<?php
										if(
											isset($social['__fa4_migrated']['_social_icon']) ||
											(empty($social['social_icon']) && Icons_Manager::is_migration_allowed())
										) {
											Icons_Manager::render_icon( $social['_social_icon'], Array('aria-hidden' => 'true') );
										}else{
											printf('<i cla1ss="%s"></i>', esc_attr( $social['social_icon']));
										}
										/*
                                        <i class="<?php echo esc_attr( $social['social_icon']); ?>"></i> */ ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                <?php endif; ?>
				<?php
				if('yes'== $testimonial_1['use_addition_desc']) { ?>
					<script type="text/html" data-item-additional-content><?php echo $testimonial_1['addition_desc']; ?></script>
					<?php
				} ?>
            </div>
          <?php endforeach; ?>
    </div><!-- /.section -->


	<?php

	/*
    <script type='text/javascript'>
         jQuery(document).ready(function($) {
            jQuery(".<?php echo $id; ?>").addClass("owl-carousel").owlCarousel({
                  pagination: false,
                  margin:10,
			      //autoWidth:true,

				  //stagePadding:10,

				 <?php if ($settings['carousel_animation'] == 'yes'):?>
				   animateOut: '<?php echo $settings['carousel_animation1']; ?>',
				   animateIn:  '<?php echo $settings['carousel_animation2']; ?>',
				 <?php endif; ?>

				  <?php if ($settings['carousel_dots'] == '1'):?>
                      dots:false,
                  <?php else: ?>
                      dots:true,
                  <?php endif; ?>

				  <?php if ($settings['carousel_loop'] == '1'):?>
                      loop:true,
                  <?php else: ?>
                      loop:false,
                  <?php endif; ?>

				  <?php if ($settings['carousel_items_per_slide'] == '2'):?>
                      center:false,
                  <?php else: ?>
                      center:true,
                  <?php endif; ?>


                  <?php if ($settings['carousel_navigation'] == '1'): ?>
					nav:true,
                  <?php else: ?>
				    nav:false,
                  <?php endif; ?>

                  navClass: ['owl-carousel-left','owl-carousel-right'],
                  navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                  autoHeight : true,
                  <?php if ($settings['carousel_autoplay'] == '1'): ?>
                        autoplay: true,
                  <?php else: ?>
                       autoplay:false,
                  <?php endif; ?>

                  responsive:{
                      0:{
                          items:1
                      },
                      600:{
                          items:1
                      },
                      1000:{
						  items:<?php echo $settings['carousel_items_per_slide']; ?>
                      }
                  }
               });
        });
    </script>
	**/ ?>


	<?php
	}



}