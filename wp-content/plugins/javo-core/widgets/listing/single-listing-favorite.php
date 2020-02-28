<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Single_Favorite extends Widget_Base {

	public function get_name() { return 'jvbpd-single-favorite'; }
	public function get_title() { return 'Single listing Favorite'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );
		$this->end_controls_section();
	}

	public function getFavoirteButton() {
		$output = false;
		if( class_exists( 'lvDirectoryFavorite_button' ) ) {
			$instance = new \lvDirectoryFavorite_button( Array(
				'format' => '{text}',
				'post_id' => get_the_ID(),
				'save' => __( "<i class='fa fa-heart'></i> Save", 'jvfrmtd' ),
				'unsave' => __( "<i class='fa fa-heart'></i> Saved", 'jvfrmtd' ),
			) );
			$output = $instance->output( false );
		}
		return $output;
	}

	protected function render() {
		$settings = $this->get_settings();
		$this->add_render_attribute( 'wrap', array(
			'class' => 'jvbpd-single-favorite',
		) ); ?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php echo $this->getFavoirteButton(); ?>
		</div>
		<?php
	}
}