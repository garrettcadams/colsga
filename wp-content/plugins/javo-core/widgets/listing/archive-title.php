<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Archive_Title extends Widget_Base {

	public function get_name() { return 'jvbpd-archive-title'; }
	public function get_title() { return 'Archive Title'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function _register_controls() {}

	protected function render() {
		$term_id = 0;
		$term_name = '';
		$queried = get_queried_object();

		if( $queried instanceof \WP_Term ) {
			$term_id = $queried->term_id;
			$term_name = $queried->name;
		}

		$this->add_render_attribute( Array(
			'class' => Array( 'jvbpd-archive-title', 'term-id-' . $term_id ),
		) );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php echo esc_html( $term_name ); ?>
		</div>
		<?php
	}
}