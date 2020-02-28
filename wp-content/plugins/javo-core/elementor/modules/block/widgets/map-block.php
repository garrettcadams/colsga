<?php
namespace jvbpdelement\Modules\Block\Widgets;

class map_block extends Base {
	public function get_name() { return parent::MAP_BLOCK; }
	public function get_title() { return 'Listing Blocks (Maps)'; }
	public function get_icon() { return 'eicon-sidebar'; }

	protected function _register_controls() {
		parent::_register_controls();
		$this->add_block_style_control();
		// $this->add_masonry_contorls();
	}

	protected function render() {
		$settings = $this->get_settings();
		$this->add_render_attribute( 'wrap', Array(
			'class' => Array( 'javo-maps-panel-list-output', 'item-list-page-wrap' ),
			'data-module' => !empty( $settings[ 'module_id' ] ) ? $settings[ 'module_id' ] : false,
			'data-columns' => !empty( $settings[ 'block_columns' ] ) ? intVal( $settings[ 'block_columns' ] ) : 1,
		) );

		$this->add_render_attribute( 'output', Array(
			'id' => 'products',
			'class' => Array( 'list-group', 'javo-shortcode' ),
		) );

		if( 'yes' == $this->get_settings('block_set_vscroll')) {
			$this->add_render_attribute('wrap','class','set-vscroll');
		} ?>

		<!-- Ajax Results Output Element-->
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div class="body-content">
				<div class="col-md-12">
					<div <?php echo $this->get_render_attribute_string( 'output' ); ?>><ul class="row"></ul></div><!-- /#prodicts -->
				</div><!-- /.col-md-12 -->
			</div><!-- /.body-content -->

			<div class="javo-loadmore-wrap">
				<button type="button" class="btn btn-block javo-map-box-morebutton" data-nomore="<?php esc_html_e( "No more listings", 'jvfrmtd' ); ?>" data-javo-map-load-more>
					<i class="fa fa-spinner fa-spin hidden"></i>
					<?php esc_html_e("Load More", 'jvfrmtd');?>
				</button>
			</div><!-- /.col-md-12 -->
		</div>
		<?php
		do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_lists_after' );
	}
}