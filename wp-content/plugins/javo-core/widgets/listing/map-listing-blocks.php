<?php
/**
Widget Name: Map listing block widget
Author: Javo
Version: 1.0.0.0
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_map_listing_blocks extends Widget_Base {

	public function get_name() {
		return 'jvbpd-map-listing-blocks';
	}

	public function get_title() {
		return 'Listing Blocks (Maps)';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-newspaper-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-map-page' ];    // category of the widget
	}

    protected function _register_controls() {

		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'Listing Blocks', 'jvfrmtd' ),
		) );
		$this->add_control( 'Des', array(
			'type' => Controls_Manager::RAW_HTML,
			'raw'  => sprintf(
				'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
				'<li class="doc-link">'.
				esc_html__('How to use this widget.','jvfrmtd'). 
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-map-page/" style="color:#fff;"> ' . 
				esc_html__( 'Documentation', 'jvfrmtd' ) . 
				'</a></li><li>&nbsp;</li>'.
				'<li class="notice">'.
				esc_html__('This widget is for only map page.', 'jvfrmtd'). 
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;"> ' . 
				esc_html__( 'Detail', 'jvfrmtd' ) . 
				'</a><br/></li><li>&nbsp;</li><li>'.
				esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
				'</li></ul></div>'
			)
		) );
		$this->end_controls_section();
	}

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();
		//$isPreviewMode = false;

		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'map-listing-modules.png';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function getContent( $settings, $obj ) {
			// Map list output
			if( apply_filters( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_lists_output_visible', true, get_the_ID() ) ): ?>
				<!-- Ajax Results Output Element-->
				<div class="javo-maps-panel-list-output item-list-page-wrap">
					<div class="body-content">
						<div class="col-md-12">
							<div id="products" <?php // echo $strMapOutputClass;?> class="list-group javo-shortcode"><div class="row"></div></div><!-- /#prodicts -->
						</div><!-- /.col-md-12 -->
					</div><!-- /.body-content -->

					<div class="javo-loadmore-wrap">
						<button type="button" class="btn btn-block javo-map-box-morebutton" data-nomore="<?php esc_html_e( "No more listings", 'jvfrmtd' ); ?>" data-javo-map-load-more>
							<i class="fa fa-spinner fa-spin hidden"></i>
							<?php esc_html_e("Load More", 'jvfrmtd');?>
						</button>
					</div><!-- /.col-md-12 -->
				</div>
				<?php do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_lists_after' ); ?>
			<?php endif; ?>

		<?php
	}
}
