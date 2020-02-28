<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Single_Post_Navigation extends Widget_Base {

	public function get_name() { return 'jvbpd-single-post-navigation'; }
	public function get_title() { return 'Single Post Navigation'; }
	public function get_icon() { return 'eicon-post-navigation'; }
	public function get_categories() { return [ 'jvbpd-single-post' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-single-post-navigation',
		) ); ?>

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php
			if( function_exists( 'jvbpd_post_nav' ) ) {
				jvbpd_post_nav();
			} ?>
		</div>
		<?php
	}

}