<?php
namespace jvbpdelement\Modules\Testimonial\Widgets;
use Elementor\Repeater;
use Elementor\Controls_Manager;


class members extends Base {

	public function get_name() { return parent::MEMBERS; }
	public function get_title() { return 'Members'; }
	public function get_icon() { return 'eicon-testimonial'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }
	public function get_script_depends() {return [ 'owl-carousel' ];}

	protected function _register_controls() {
		parent::_register_controls();

		$this->start_controls_section(
			'section_social',
			[
				'label' => esc_html__( 'Social Links', 'jvfrmtd' ),
			]
		);

			$repeater = new Repeater();

			    $repeater->add_control(
			      'title',
			      [
			          'label'   => esc_html__( 'Social Name', 'jvfrmtd' ),
			          'type'    => Controls_Manager::TEXT,
			          'default' => esc_html__( 'Facebook', 'jvfrmtd' ),
			      ]
			    );


			$repeater->add_control(
		            'social_link',
		            [
		                'label' => esc_html__( 'Social Link', 'jvfrmtd' ),
		                'type'  => Controls_Manager::TEXT,
		                'default' => esc_html__( 'https://www.facebook.com/envato', 'jvfrmtd' ),
		            ]
		        );
		        $repeater->add_control( '_social_icon',[
					'label' => esc_html__( 'Social Icon', 'jvfrmtd' ),
					'type'  => Controls_Manager::ICONS,
					'default' => 'fa fa-facebook',
					'fa4compatibility' => 'social_icon',
					'default' => Array(
						'value' => 'fab fa-facebook-f',
						'library' => 'solid',
					),
				] );


			$this->add_control(
			    'social_share_1',
			      [
			          'label'       => esc_html__( 'Social Share', 'jvfrmtd' ),
			          'type'        => Controls_Manager::REPEATER,
			          'show_label'  => true,
			          'default'     => [
			              [
			              	'title'       => esc_html__( 'Facebook', 'jvfrmtd' ),
			                'social_link' => esc_html__( 'https://www.facebook.com/envato', 'jvfrmtd' ),
			                '_social_icon' => Array(
								'library' => 'solid',
								'value' => 'fab fa-facebook-f',
							),
			              ],
			              [
			              	'title'       => esc_html__( 'Twitter', 'jvfrmtd' ),
			                'social_link' => esc_html__( 'https://www.twitter.com/envato', 'jvfrmtd' ),
			                '_social_icon' => Array(
								'library' => 'solid',
								'value' => 'fab fa-twitter',
							),

			              ],
			              [
			              	'title'       => esc_html__( 'Linkedin', 'jvfrmtd' ),
							'social_link' => esc_html__( 'https://www.linkedin.com/envato', 'jvfrmtd' ),
							'_social_icon' => Array(
								'library' => 'solid',
								'value' => 'fab fa-linkedin-in',
							),
			              ]
			          ],
			          'fields'      => array_values( $repeater->get_controls() ),
			          'title_field' => '{{{title}}}',
			      ]
			  );

		$this->end_controls_section();

		$this->start_controls_section(
			'social_icon_style',
				[
					'label' => esc_html__( 'Social Icon', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'icon_size',
				[
					'label' => esc_html__( 'Icon Font Size', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default'  => [
						'size' => 14,
					],
					'range' => [
						'px' => [
							'min' => 12,
							'max' => 30,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .team-social a' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .team-social a' => 'display: inline-block; width: 40px; height: 40px; line-height: 40px; border-radius: 2px; text-align: center; background-color: #fff; margin: 0 5px 0 0;',
					],
				]
			);

			$this->add_responsive_control(
				'icon_alignment',
				array(
					'label'   => esc_html__( 'Alignment', 'jvfrmtd' ),
					'type'    => Controls_Manager::CHOOSE,
					'default' => 'center',
					'options' => array(
						'left'    => array(
							'title' => esc_html__( 'Left', 'jvfrmtd' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'jvfrmtd' ),
							'icon'  => 'fa fa-align-center',
						),
						'right' => array(
							'title' => esc_html__( 'Right', 'jvfrmtd' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .team-social' => 'text-align: {{VALUE}};',
					),
				)
			);


			$this->add_responsive_control(
				'icon_bottom_space',
				[
					'label' => esc_html__( 'Icon Spacing', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default'  => [
						'size' => 10,
					],
					'range' => [
						'px' => [
							'min' => -10,
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .team-social' => 'margin: {{SIZE}}{{UNIT}} 0;',
					],
				]
			);

			$this->add_control(
				'border_radius',
				[
					'label' => esc_html__( 'Icon Border Radius', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .team-social a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


			$this->add_control(
				'icon_color',
				[
					'label'     => esc_html__( 'Icon Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#8c8c8c',
					'selectors' => [
						'{{WRAPPER}} .team-social a' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'icon_bg_color',
				[
					'label'     => esc_html__( 'Icon Bg Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .team-social a' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'icon_hover_color',
				[
					'label'     => esc_html__( 'Icon Hover Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .team-social a:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'icon_hover_bg_color',
				[
					'label'     => esc_html__( 'Icon Hover Background Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ed485f',
					'selectors' => [
						'{{WRAPPER}} .team-social a:hover' => 'background-color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();
	}

	protected function render() {
		parent::render();


	}
}