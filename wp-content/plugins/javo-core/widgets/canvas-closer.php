<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;


if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Canvas_Closer extends Widget_Base {

	public function get_name() { return 'jvbpd-canvas-closer'; }
	public function get_title() { return 'Canvas Closer'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements-canvas' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		$this->add_responsive_control(
		'closer_color',
		[
			'label' => esc_html__( 'Icon Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#fff',
			'selectors' => [
				'{{WRAPPER}} .jvbpd-menu-closer' => 'color: {{VALUE}};',
			],
		]
	);


	$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
			'name' => 'closer_typography',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			'selector' => '{{WRAPPER}} .jvbpd-menu-closer',
		]
	);

	$this->add_responsive_control(
		'closer_alignment',
		[
			'label' => __( 'Icon Alignment', 'jvfrmtd' ),
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
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
			],
		]
	);

		$this->end_controls_section();
	}




	protected function render() {
		$settings = $this->get_settings();
		$this->add_render_attribute( 'closer', array(
			'class' => 'jvbpd-menu-closer',
			'href' => 'javascript:',
		) );
		?>
		<a <?php echo $this->get_render_attribute_string( 'closer' ); ?>>
			&times;
		</a>
		<?php
	}
}