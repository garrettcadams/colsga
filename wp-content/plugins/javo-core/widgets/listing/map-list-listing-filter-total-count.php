<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_map_list_filter_total_count extends Widget_Base {

	public function get_name() { return 'jvbpd-map-list-filter-total-count'; }

	public function get_title() { return 'Listing filter total count'; }

	public function get_icon() { return 'eicon-number-field'; }

	public function get_categories() { return Array( 'jvbpd-map-page' ); }

	protected function _register_controls() {

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "General", 'jvfrmtd' ),
		) );

		$this->add_control( 'suffix_label', Array(
			'type' => Controls_Manager::TEXT,
			'label' => esc_html__( "Suffix label", 'jvfrmtd' ),
			'default' => esc_html__( "Item(s)", 'jvfrmtd' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_alignment',
			[
				'label' => __( 'Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control( 'alignment', [
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
                '{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
            ],
		] );


		$this->add_control(
			'count_color',
				[
					'label' => __( 'Count Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#3c3d42',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .counter-output' => 'color: {{VALUE}}',
					],
				]
		);
		
		$this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'count_typography',
            'selector' => '{{WRAPPER}} .counter-output',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->add_control(
			'suffix_color',
				[
					'label' => __( 'Suffix Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#3c3d42',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .counter-suffix' => 'color: {{VALUE}}',
					],
				]
		);
	
		

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'suffix_typography',
            'selector' => '{{WRAPPER}} .counter-suffix',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );
		
		
		
		$this->end_controls_section();
	}

	protected function render() {
		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-map-list-total-count-wrap',
		) );
		$this->add_render_attribute( 'counter', Array(
			'class' => 'counter-output',
		) );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<span <?php echo $this->get_render_attribute_string( 'counter' ); ?>>0</span>
			<span class="counter-suffix"><?php echo $this->get_settings( 'suffix_label' ); ?></span>
		</div>
		<?php
	}

}