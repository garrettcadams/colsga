<?php
namespace jvbpdelement\Modules\Button\Widgets;

use Elementor\Controls_Manager;
class AddFormSubmit extends Base {
	public function get_name() { return parent::ADDFORM_SUBMIT_BUTTON; }
	public function get_title() { return 'Add Submit Button'; }
	public function get_icon() { return 'eicon-form-horizontal'; }
	public function get_categories() { return [ 'jvbpd-core-add-form' ]; }

	protected function _register_controls() {
		parent::_register_controls();
	}

	public function linkAttributes( $args=Array() ) {
		$args[ 'href' ] = 'javascript:';
		$args[ 'onclick' ] = 'jQuery( this ).closest( "form" ).submit();';

		$output = '';
		foreach( $args as $key => $value ) {
			$output .= sprintf( '%1$s="%2$s"', $key, esc_attr( $value ) );
		}
		echo $output;
	}

	protected function render() {
		?>
		<div class="hidden">
			<?php
			if( function_exists( 'lava_add_item_submit_button' ) ) {\
				lava_add_item_submit_button();
			} ?>
		</div>
		<?php
		$this->button_render();
	}

}