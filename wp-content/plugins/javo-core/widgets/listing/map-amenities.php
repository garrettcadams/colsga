<?php
/**
Widget Name: Map amenities widget
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


class jvbpd_map_amenities extends Widget_Base {

	public function get_name() {
		return 'jvbpd-map-amenities';
	}

	public function get_title() {
		return 'Amenities';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-wifi';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-map-page' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Amenities', 'jvfrmtd' ),
			)
		);

		$this->add_control(
		'Des',
			array(
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
			)
		);
		$this->end_controls_section();
    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();
		//$isPreviewMode = false;

		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'map-amenities.png';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function getContent( $settings, $obj ) {
		$jvbpd_multi_filters = Array(
			'listing_amenities' => (Array) $obj->req_listing_amenities,
		);

		$this->add_render_attribute( 'wrap', Array(
			'class' => 'text-left javo-map-box-advance-term row',
			'id' => 'javo-map-box-advance-term-' . $this->get_id(),
		) );

		$this->add_render_attribute( 'opener', Array(
			'class' => 'opener',
			'data-toggle' => 'collapse',
			'data-target' => '#javo-map-box-advance-term-' . $this->get_id(),
		) );

		if( !empty( $jvbpd_multi_filters ) ) : foreach( $jvbpd_multi_filters as $filter => $currentvalue ) {
			?>
			<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
				<div class="row jv-advanced-fields amenities-filter-area">
					<div class="text-center"><?php esc_html_e( "There is no amenities/features in this category", 'jvfrmtd' ); ?></div>
				</div><!-- /.col-md-9 -->
				<div <?php echo $this->get_render_attribute_string( 'opener' ); ?>>
					<div class="opener-inner">
						<?php esc_html_e( "More amenities", 'jvfrmtd' ); ?>
						<i class="fa fa-caret-down"></i>
					</div>
				</div>
			</div>
			<?php
		} endif;
	}

}
