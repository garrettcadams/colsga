<?php
/**
Widget Name: Single spyscroll widget
Author: Javo
Version: 1.0.0.0
*/


namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_single_spyscroll extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-spyscroll';
	}

	public function get_title() {
		return 'Spyscroll Menu';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-scroll';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {
	$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Spyscroll Menu', 'jvfrmtd' ),
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
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only single listing detail page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/" style="color:#fff;">' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
    );

	$this->end_controls_section();

	$this->start_controls_section(
			'section_block_setting',
			array(
				'label' => esc_html__( 'Spyscroll Menu List', 'jvfrmtd' ),
			)
		);
	$this->add_control(
		'list',
		[
			'label' => __( 'Menu List', 'jvfrmtd' ),
			'type' => Controls_Manager::REPEATER,
			'default' => [
				[
					'list_title' => __( 'Title #1', 'jvfrmtd' ),
				],
				[
					'list_title' => __( 'Title #2', 'jvfrmtd' ),
				],
			],
			'fields' => [
				[
					'name' => 'list_title',
					'label' => __( 'Title', 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'List Title' , 'jvfrmtd' ),
					'label_block' => true,
				],
				[
					'name' => 'switcher_Custom_basic',
					'label' => __( 'Custom Link', 'jvfrmtd' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_off' => __( 'Custom', 'jvfrmtd' ),
					'label_on' => __( 'Basic', 'jvfrmtd' ),
				],
				[
					'name' => 'scroll_point',
					'label'       => __( 'Landing Section', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						'#wrapper'  => __( 'Top', 'jvfrmtd' ),
						'#javo-item-describe-section' => __( 'Description', 'jvfrmtd' ),
						'#javo-item-amenities-section' => __( 'Amenities', 'jvfrmtd' ),
						'#lava_faq' => __( 'FAQ', 'jvfrmtd' ),
					],
					'condition' => [
					    'switcher_Custom_basic' => 'yes',
					],
				 ],
				 [
					'name' => 'custom_landing_id',
					'label'       => __( 'Landing ID', 'jvfrmtd' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => __( '', 'jvfrmtd' ),
					'placeholder' => __( 'Type Custom point id text here', 'jvfrmtd' ),
					'condition' => [
					    'switcher_Custom_basic!' => 'yes',
					],
				 ],
			],
			'title_field' => '{{{ list_title }}}',
		]
	);

	$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tab-padding-top-bottom',
			[
				'label' => __( 'Padding Top, Bottom', 'jvfrmtd' ),
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
					'{{WRAPPER}} .javo-single-nav' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab-padding-left-right',
			[
				'label' => __( 'Padding Left, Right', 'jvfrmtd' ),
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
					'{{WRAPPER}} .javo-single-nav' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);



			//---------- Icon Style Tabs ---------//
			$this->add_control(
				'icon_style_heading',
				[
					'label' => __( 'Icon Styles', 'jvfrmtd' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'after',
				]
			);

			$this->start_controls_tabs( 'icon_styles' );
			$this->start_controls_tab( 'icon_normal', [ 'label' => __( 'Normal', 'jvfrmtd' ) ] );
				$this->add_control(
					'text_color',
					[
						'label' => __( 'Text Color', 'jvfrmtd' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .javo-single-nav a' => 'color: {{VALUE}};',
						],
						'scheme' => [
							'type' => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_1,
						],
					]
				);

				$this->add_group_control( Group_Control_Typography::get_type(), [
					'name' => 'normal_typography',
					'selector' => '{{WRAPPER}} .javo-single-nav a',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				] );

			$this->end_controls_tab();

			$this->start_controls_tab( 'text_hover', [ 'label' => __( 'Hover', 'jvfrmtd' ) ] );
				$this->add_control(
					'hover_color',
					[
						'label' => __( 'Text Color', 'jvfrmtd' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .javo-single-nav a:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'border',
						'label' => __( 'Border', 'plugin-domain' ),
						'selector' => '{{WRAPPER}} .javo-single-nav:hover',
					]
				);


				$this->add_control(
					'hover_animation',
					[
						'label' => __( 'Hover Animation', 'jvfrmtd' ),
						'type' => Controls_Manager::HOVER_ANIMATION,
					]
				);
			$this->end_controls_tab();
			$this->end_controls_tabs();
		//---------- Icon Style Tabs ---------//


	$this->end_controls_section();

    }

    protected function render() {
		?>
		<div id="javo-detail-item-header-wrap-sticky-wrapper">
			<div id="javo-detail-item-header-wrap">
				<div id="javo-detail-item-header" class="">
					<ul class="">
						<?php
							$list = $this->get_settings( 'list' );
							if ( $list ) {
								foreach ( $list as $item ) {
									echo '<li class="javo-single-nav">';
									if($item['switcher_Custom_basic']=='yes'){
										echo '<a href="'.$item['scroll_point'].'"><i></i>'.$item['list_title'].'</a>';
									}else{
										echo '<a href="#'.$item['custom_landing_id'].'"><i></i>'.$item['list_title'].'</a>';
									}
									echo '</li>';
								}
							}
						?>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
}