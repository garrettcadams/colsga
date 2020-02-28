<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Single_Post_Header extends Widget_Base {

	public function get_name() { return 'jvbpd-single-post-header'; }
	public function get_title() { return 'Single Post Header'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-single-post' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		$this->add_control( 'header_height', Array(
			'type' => Controls_Manager::SLIDER,
			'default' => Array(
				'size'	=> '100',
			),
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 1000,
				),
			),
			'selectors' => Array(
				'{{WRAPPER}} .jvbpd-single-post-header' => 'min-height: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->end_controls_section();
	}

	protected function render() {
		jvbpd_elements_tools()->switch_preview_post();

		$imageURL = ELEMENTOR_ASSETS_URL.'images/placeholder.png';
		$thumbnailURL = wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' );

		if( $thumbnailURL ) {
			$imageURL = $thumbnailURL;
		}

		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-single-post-header',
			'style' => sprintf( 'background-size:cover;background-position:center;background-image:url(%s);', $imageURL ),
		) ); ?>

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>></div>
		<?php
		jvbpd_elements_tools()->restore_preview_post();

	}

}