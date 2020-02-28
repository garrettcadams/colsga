<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_single_route_timeline extends Widget_Base {

	public function get_name() { return 'jvbpd-single-route-timeline'; }

	public function get_title() { return 'Route Timeline'; }

	public function get_icon() { return 'eicon-time-line'; }

	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function _register_controls() {

		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		//jvbpd_elements_tools()->add_button_control( $this );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
		jvbpd_elements_tools()->switch_preview_post();
		echo do_shortcode(sprintf( '[lava-route-timeline post_id=%s]', get_the_ID() ) );
		jvbpd_elements_tools()->restore_preview_post();
    }

}