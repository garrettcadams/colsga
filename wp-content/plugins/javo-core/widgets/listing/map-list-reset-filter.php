<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_map_list_reset_filter extends Widget_Base {

	public function get_name() { return 'jvbpd-map-list-reset-filter'; }

	public function get_title() { return 'Reset Filters( List Type )'; }

	public function get_icon() { return 'eicon-button'; }

	public function get_categories() { return Array( 'jvbpd-map-page' ); }

	protected function _register_controls() {

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "General", 'jvfrmtd' ),
        ) );

        $this->add_control( 'label', Array(
			'type' => Controls_Manager::TEXT,
			'label' => esc_html__( "Label", 'jvfrmtd' ),
			'default' => esc_html__( "Clear", 'jvfrmtd' ),
        ) );


        $this->add_control(
			'show_selected_filters',
			[
				'label' => __( 'Show Selected Filters', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'yes' => __( 'Show', 'jvfrmtd' ),
				'no' => __( 'Hide', 'jvfrmtd' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->add_control(
			'show_clear_btn',
			[
				'label' => __( 'Show Clear Button', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'yes' => __( 'Show', 'jvfrmtd' ),
				'no' => __( 'Hide', 'jvfrmtd' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
        );

        $this->add_control( 'empty_filter_hidden', Array(
            'label' => __( 'Hidden empty filter', 'jvfrmtd' ),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'frontend_available' => true,
        ));

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_alignment',
			[
				'label' => __( 'Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control( 'btn_align', [
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
            'body_color',
            [
                'label' => __( 'Text Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#878787',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .filter-item' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'body_typography',
            'selector' => '{{WRAPPER}} .filter-item',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
        ] );

		$this->end_controls_section();
	}

	protected function render() {

        $settings = $this->get_settings();
		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-map-list-reset-filter-wrap',
		) );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
            <?php if ($settings['show_selected_filters']=='yes'){ ?>
			    <div class="items"></div>
            <?php }; ?>

            <?php if ($settings['show_clear_btn']=='yes'){ ?>
			<span class="filter-item all-reset" data-filter="all-reset">
                <?php echo $settings['label']; ?>
			</span>
            <?php }; ?>
		</div>
		<?php
	}

}