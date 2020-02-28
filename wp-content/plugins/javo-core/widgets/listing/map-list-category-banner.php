<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_map_list_category_banner extends Widget_Base {

	public function get_name() { return 'jvbpd-map-list-category-banner'; }

	public function get_title() { return 'Category Banner( List Type )'; }

	public function get_icon() { return 'eicon-image-rollover'; }

	public function get_categories() { return Array( 'jvbpd-map-page' ); }

	protected function _register_controls() {

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "General", 'jvfrmtd' ),
		) );

		$this->add_control( 'Des', array(
			'type' => Controls_Manager::RAW_HTML,
			'raw'  => sprintf(
				'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
				'<li class="doc-link">'.
				esc_html__('These images are from featured images of categories.','jvfrmtd').
				'</li></ul></div>'
			)
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-map-list-category-banner-wrap',
		) ); ?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>></div>
		<?php
	}

}