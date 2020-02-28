<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Settings;
use Elementor\Scheme_Typography;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class jvbpd_social_share extends Widget_Base {

	private static $networks_class_dictionary = [
		'google' => 'fa fa-google-plus',
		'pocket' => 'fa fa-get-pocket',
		'email' => 'fa fa-envelope',
		'kakaotalk' => 'fa fa-envelope',
		'kakaostory' => 'fa fa-envelope',
	];

	private static function get_social_icon_class( $social_name ) {
		if ( isset( self::$networks_class_dictionary[ $social_name ] ) ) {
			return self::$networks_class_dictionary[ $social_name ];
		}

		return 'fa fa-' . $social_name;
	}

	public function get_name() {
		return 'social-share-buttons';
	}

	public function get_title() {
		return __( 'JV Social Share Buttons', 'jvfrmtd' );
	}

	public function get_icon() {
		return 'eicon-share';
	}
	
	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

	public function get_script_depends() {
		return [ 'social-share' ];
	}

	private static $networks = [
		'facebook' => [
			'title' => 'Facebook',
			
		],
		'twitter' => [
			'title' => 'Twitter',
		],
		'google' => [
			'title' => 'Google+',
			
		],
		//'linkedin' => [
			//'title' => 'LinkedIn',			
		//],
		'pinterest' => [
			'title' => 'Pinterest',
			
		],
		'reddit' => [
			'title' => 'Reddit',
			
		],
		'vk' => [
			'title' => 'VK',
			
		],
		'odnoklassniki' => [
			'title' => 'OK',
			
		],
		//'tumblr' => [
			//'title' => 'Tumblr',
		//],
		'delicious' => [
			'title' => 'Delicious',
		],
		'digg' => [
			'title' => 'Digg',
		],
		'skype' => [
			'title' => 'Skype',
		],
		'stumbleupon' => [
			'title' => 'StumbleUpon',
			
		],
		'telegram' => [
			'title' => 'Telegram',
		],
		//'pocket' => [
	//		'title' => 'Pocket',
			
	//	],
		'xing' => [
			'title' => 'XING',
			
		],
		'whatsapp' => [
			'title' => 'WhatsApp',
		],
		/*'kakaotalk' => [
			'title' => 'KakaoTalk',
		],*/
		'kakaostory' => [
			'title' => 'KakaoStory',
		],
		'email' => [
			'title' => 'Email',
		],
		//'print' => [
			//'title' => 'Print',
		//],
	];


	public static function get_networks( $social_name = null ) {
		if ( $social_name ) {
			return isset( self::$networks[ $social_name ] ) ? self::$networks[ $social_name ] : null;
		}

		return self::$networks;
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_button_style',
			[
				'label' => __( 'Skin', 'jvfrmtd' ),
			]
		);

		$this->add_control(
		  'social_btn_skin',
		  [
			 'label'       => __( 'Button Skin', 'jvfrmtd' ),
			 'type' => Controls_Manager::SELECT,
			 'default' => 'classic',
			 'options' => [
				'classic' => __( 'Classic', 'jvfrmtd' ),
				'box' => __( 'Box', 'jvfrmtd' ),
				'expand'  => __( 'Expand', 'jvfrmtd' ),
			 ],	
			'prefix_class' => 'jv-share-skin-',
		  ]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_expand_settings',
			[
				'label' => __( 'Expand Setting', 'jvfrmtd' ),
				'condition' => [
					'social_btn_skin' => 'expand',
				],
			]
		);

		$this->add_responsive_control(
			'expand-direction',
			[
				'label' => __( 'Expand Direction', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'right',
				'label_block' => false,
				'options' => [
					/*'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon'  => 'eicon-h-align-left',
					],
					'above' => [
						'title' => __( 'Above', 'jvfrmtd' ),
						'icon'  => 'eicon-v-align-top',
					],*/
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon'  => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'jvfrmtd' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'prefix_class' => 'expand-direction-',
				'condition' => [
					'social_btn_skin' => 'expand',
				],
			]
		);

		$this->add_control(
		  'expand_title',
		  [
			 'label'       => __( 'Title', 'your-plugin' ),
			 'type'        => Controls_Manager::TEXT,
			 'default'     => __( 'Share', 'your-plugin' ),
			 'placeholder' => __( 'Type your title text here', 'your-plugin' ),
		  ]
		);

		$this->add_responsive_control(
			'expand_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .jv-social-share-holder.jv-expand' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .jv-social-share-title',
				'condition' => [
					'title!' => '',
				],
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
					'{{WRAPPER}} .jv-social-share-expand-opener' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .jv-social-share-holder.jv-expand:hover .jv-social-share-expand-opener' => 'background-color: transparent;', //hover transparant
				],
				'condition' => [
					'social_btn_skin' => 'expand',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#454545',
				'selectors' => [
					'{{WRAPPER}} .jv-social-share-title' => 'color: {{VALUE}}',
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
				'label' => __( 'Hover Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#000',
				'selectors' => [
					'{{WRAPPER}} .jv-social-share-holder.jv-expand:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'social_btn_skin' => 'expand',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label' => __( 'Title Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .jv-social-share-wrap .jv-social-share-holder.jv-expand:hover .jv-social-share-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'social_btn_skin' => 'expand',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();

		$this->start_controls_section(
			'section_other_settings',
			[
				'label' => __( 'Box Settings', 'jvfrmtd' ),
				'condition' => [
						'social_btn_skin'		=> 'box',
				],
			]
		);
		

		$this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => [
					'0' => 'Auto',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'prefix_class' => 'jv-share-grid%s-',
				'condition' => [
					'social_btn_skin'		=> 'box',
				],
				
			]
		);

		$this->add_control(
			'alignment',
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
					'justify' => [
						'title' => __( 'Justify', 'jvfrmtd' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'prefix_class' => 'jv-share-btn--align-',
				'condition' => [
					'social_btn_skin'		=> 'box',
					'columns' => '0',
				],
			]
		);
	
		$this->end_controls_section();



		$this->start_controls_section(
			'section_social_buttons_content',
			[
				'label' => __( 'Social Setting', 'jvfrmtd' ),
			]
		);

		$repeater = new Repeater();

		$networks = self::get_networks();

		$networks_names = array_keys( $networks );

		$repeater->add_control(
			'social_selection',
			[
				'label' => __( 'Network', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => array_reduce( $networks_names, function( $options, $social_name ) use ( $networks ) {
					$options[ $social_name ] = $networks[ $social_name ]['title'];

					return $options;
				}, [] ),
				'default' => 'facebook',
			]
		);

		/*$repeater->add_control(
			'text',
			[
				'label' => __( 'Custom Label', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
			]
		);*/

		$this->add_control(
			'share_buttons',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'social_selection' => 'facebook',
					],
					[
						'social_selection' => 'google',
					],
					[
						'social_selection' => 'twitter',
					],
					[
						'social_selection' => 'linkedin',
					],
				],
				'title_field' => '{{{ social_selection }}}',
			]
		);

		$this->end_controls_section();

		
		$this->start_controls_section(
			'section_buttons_alignment',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => __( 'Columns Gap', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .jv-share-btn' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .elementor-grid' => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => __( 'Rows Gap', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .jv-share-btn' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_size',
			[
				'label' => __( 'Button Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.5,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jv-share-btn' => 'font-size: calc({{SIZE}}{{UNIT}} * 10);',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => 0.5,
						'max' => 4,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'size_units' => [ 'em', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .jv-share-btn-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],				
			]
		);

		$this->add_responsive_control(
			'button_height',
			[
				'label' => __( 'Button Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => 1,
						'max' => 7,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'size_units' => [ 'em', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .jv-share-btn' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
			]
		);



		$this->add_control(
			'social_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'original' => 'Original Color',
					'custom' => 'Custom Color',
				],
				'default' => 'original',
				'prefix_class' => 'jv-share-btn--color-',
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
				'condition' => [
					'social_color' => 'custom',
				],
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label' => __( 'Primary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .jv-share-btn' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'social_color' => 'custom',
				],
			]
		);

		$this->add_control(
			'secondary_color',
			[
				'label' => __( 'Secondary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jv-share-btn-icon i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'social_color' => 'custom',
				],
				'separator' => 'after',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
				'condition' => [
					'social_color' => 'custom',
				],
			]
		);

		$this->add_control(
			'primary_color_hover',
			[
				'label' => __( 'Primary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jv-share-btn:hover .jv-share-btn-icon' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'social_color' => 'custom',
				],
			]
		);

		$this->add_control(
			'secondary_color_hover',
			[
				'label' => __( 'Secondary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.jv-share-btn--skin-flat .jv-share-btn:hover .jv-share-btn-icon, 
					 {{WRAPPER}}.jv-share-btn--skin-flat .jv-share-btn:hover .jv_share-btn__text, 
					 {{WRAPPER}}.jv-share-btn--skin-gradient .jv-share-btn:hover .jv-share-btn-icon,
					 {{WRAPPER}}.jv-share-btn--skin-gradient .jv-share-btn:hover .jv_share-btn__text,
					 {{WRAPPER}}.jv-share-btn--skin-boxed .jv-share-btn:hover .jv-share-btn-icon,
					 {{WRAPPER}} .jv-share-btn:hover .jv-share-btn-icon i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'social_color' => 'custom',
				],
				'separator' => 'after',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/*$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .jv_share-btn__title',
				'exclude' => [ 'line_height' ],
			]
		);

		$this->add_control(
			'text_padding',
			[
				'label' => __( 'Text Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',			
			]
		);*/

		$this->end_controls_section();

	}


	protected function render() {
		$settings = $this->get_active_settings();
		//$settings = $this->get_settings();

		if ( empty( $settings['share_buttons'] ) ) {
			return;
		}

		$button_classes = 'jv-share-btn';

		?>
		<div class="jv-socail-share-wrap">		
			<a href="javascript:void(0)" target="_self" class="social-opener">
				<i class="eicon-share"></i><span class="social-share-txt">Share</span>
			</a>
			<div class="jv-sns-list">
			<ul><?php
				foreach ( $settings['share_buttons'] as $button ) {
					$social_name = $button['social_selection'];
					$sns_class = 'sns-' . $social_name;					
					$social_network_class = ' jv_share-btn_' . $social_name;
					?><li class="jv-social-item"><a class="<?php echo $button_classes . $social_network_class; ?> javo-share <?php echo $sns_class; ?>" data-title="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>">
							<span class="jv-share-btn-icon">
								<i class="<?php echo self::get_social_icon_class( $social_name ); ?>"></i>
							</span>
						</a>
					</li><?php
					}
					?></ul></div></div> <!-- jv-socail-share-wrap -->		
		<?php
	}
}
