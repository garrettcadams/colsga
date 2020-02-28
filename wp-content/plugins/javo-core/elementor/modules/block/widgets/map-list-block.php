<?php
namespace jvbpdelement\Modules\Block\Widgets;

class map_list_block extends Base {
	public function get_name() { return parent::LIST_BLOCK; }
	public function get_title() { return 'Listing Blocks (List Type)'; }
	public function get_icon() { return 'fa fa-newspaper-o'; }

	protected function _register_controls() {
		parent::_register_controls();

		$this->add_listing_style_controls();
		$this->add_map_block_control();
		$this->add_block_style_control();
		$this->add_masonry_contorls();

		/*
		$this->get_list_block_settings_control();
		$this->get_listing_style_controls();
		$this->get_pagination_style_controls();
		$this->get_module_style_controls();
		$this->get_filter_style_controls();
		$this->get_filter_option_controls(); */
	}

	 protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();
		//$isPreviewMode = false;

		/*
		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'map-listing-modules.png';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{ */
			$this->getContent( $settings, get_post() );
			/*
		} */
    }

	public function getContent( $settings, $obj ) {

		$this->add_render_attribute( 'wrap', Array(
			'class' => apply_filters( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_list_content_column_class' , '', $GLOBALS[ 'post' ] ),
			'data-first-featured' => (boolean) $settings[ 'featured_first' ] == '1' ? 'true' : 'false',
			'data-module' => !empty( $settings[ 'module_id' ] ) ? $settings[ 'module_id' ] : false,
			'data-columns' => !empty( $settings[ 'block_columns' ] ) ? intVal( $settings[ 'block_columns' ] ) : 1,
		) );

		$this->add_render_attribute( 'wrap', 'class', 'list-block-wrap' );

		if( 'yes' == $this->get_settings('block_set_vscroll')) {
			$this->add_render_attribute('wrap','class','set-vscroll');
		}

		$this->add_render_attribute( 'container', Array(
			'id' => 'javo-listings-wrapType-container',
			'class' => apply_filters( 'jvbpd_map_list_output_class', Array( 'javo-shortcode', 'row', 'ajax-processing' ) ),
		) ); ?>

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div id="results">
				<div id="spaces">
					<?php do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_list_container_before', $GLOBALS[ 'post' ] ); ?>
					<div id="space-0" class="space" itemscope="" itemtype="http://schema.org/LodgingBusiness">
						<ul <?php echo $this->get_render_attribute_string( 'container' ); ?>></ul>
					</div><!--/.space row-->
				</div><!--/#spaces-->
				<div class="javo-loadmore-wrap">
					<button type="button" class="btn btn-default btn-block javo-map-box-morebutton" data-javo-map-load-more>
						<i class="fa fa-spinner fa-spin"></i>
						<?php esc_html_e("Load More", 'jvfrmtd');?>
					</button>
				</div>
				<?php do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_list_container_after', $GLOBALS[ 'post' ] ); ?>
			</div><!--/#results-->
		</div><!--/.row-->

		<?php
	}

}