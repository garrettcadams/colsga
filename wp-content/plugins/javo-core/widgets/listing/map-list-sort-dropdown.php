<?php
namespace jvbpdelement\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_map_list_sort_dropdown extends Widget_Base {

	public function get_name() { return 'jvbpd-map-list-sort-dropdown'; }

	public function get_title() { return 'Sort dropdown( List Type )'; }

	public function get_icon() { return 'eicon-button'; }

	public function get_categories() { return Array( 'jvbpd-map-page' ); }

	protected function _register_controls() {

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "General", 'jvfrmtd' ),
		) );

		$repeater = new Repeater();

		$repeater->add_control( 'sort_type', Array(
			'label' => esc_html__( "Sort Type", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'default' => 'name',
			'options' => Array(
				'name' => esc_html__( "Post title", 'jvfrmtd' ),
				'date' => esc_html__( "Post date", 'jvfrmtd' ),
				'rating' => esc_html__( "Rating", 'jvfrmtd' ),
				'reviewed' => esc_html__( "Reviewed", 'jvfrmtd' ),
				'favorite' => esc_html__( "Favorite", 'jvfrmtd' ),
				'openhour' => esc_html__( "Working hours", 'jvfrmtd' ),
			)
		) );

		$repeater->add_control( 'sort_label', Array(
			'label' => esc_html__( "Label", 'jvfrmtd' ),
			'type' => Controls_Manager::TEXT,
			'default' => esc_html__( "Post title", 'jvfrmtd' ),
		) );

		$this->add_control( 'sort', Array(
			'label' => esc_html__( "Sort Type", 'jvfrmtd' ),
			'type' => Controls_Manager::REPEATER,
			'default' => Array(
				Array(
					'sort_type' => 'name',
					'sort_label' => esc_html__( "Post title", 'jvfrmtd' ),
				),
			),
			'fields' => array_values( $repeater->get_controls() ),
			'title_field' => '{{{ sort_type }}}',
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_alignment',
			[
				'label' => __( 'Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control( 'horizon-filter-width', [
			'label' => __( 'Height', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 38,
				'unit' => 'px',
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
				'{{WRAPPER}} .jvbpd-map-list-sort-dropdown select' => 'height: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .jvbpd-map-list-sort-dropdown button' => 'height: {{SIZE}}{{UNIT}};',
			],			
		] );

		$this->add_responsive_control( 'alignment', [
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
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .jvbpd-map-list-sort-dropdown' => 'justify-content: {{VALUE}};',
            ],
		] );


		$this->add_control(
			'text_color',
				[
					'label' => __( 'Text Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#3c3d42',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .jvbpd-map-list-sort-dropdown select' => 'color: {{VALUE}}',
						'{{WRAPPER}} .jvbpd-map-list-sort-dropdown button i' => 'color: {{VALUE}}',
					],
				]
		);
		
		$this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'count_typography',
            'selector' => '{{WRAPPER}} .jvbpd-map-list-sort-dropdown select, {{WRAPPER}} .jvbpd-map-list-sort-dropdown button i',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );


		$this->add_control(
			'filter_panel_padding',
			[
				'label' => __( 'Padding','jvfrmtd'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-map-list-sort-dropdown select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jvbpd-map-list-sort-dropdown button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_border',
				'selector' => '{{WRAPPER}} .jvbpd-map-list-sort-dropdown select, {{WRAPPER}} .jvbpd-map-list-sort-dropdown button',
			]
		);

		$this->add_control(
			'filter_border_radius',
			[
				'label' => __( 'Border Radius','jvfrmtd'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-map-list-sort-dropdown select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jvbpd-map-list-sort-dropdown button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_box_shadow',
				'selector' => '{{WRAPPER}} .jvbpd-map-list-sort-dropdown select, {{WRAPPER}} .jvbpd-map-list-sort-dropdown button',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$sortFields = $this->get_settings( 'sort' );
		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-map-list-sort-dropdown',
		) );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php if( !empty( $sortFields ) ) : ?>
				<select data-map-sort-by>
					<?php
					foreach( $sortFields as $sort ) {
						printf( '<option value="%1$s">%2$s</option>', $sort['sort_type'], $sort['sort_label'] );
					} ?>
				</select>
				<button type="button" data-map-sort-type="asc">
					<i class="jvic-arrow-up-1" data-sort="asc"></i>
					<i class="jvic-arrow-down-1 hidden" data-sort="desc"></i>
				</button>
			<?php endif; ?>
		</div>
		<?php
	}
}