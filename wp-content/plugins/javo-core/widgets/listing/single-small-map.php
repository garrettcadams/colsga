<?php
/**
Widget Name: Single small map widget
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


class jvbpd_single_small_map extends Widget_Base {

	public function get_name() { return 'jvbpd-single-small-map'; }
	public function get_title() { return 'Listing Map (Single)'; } // title to show on elementor
	public function get_icon() { return 'eicon-google-maps'; }    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	public function get_categories() { return [ 'jvbpd-single-listing' ]; }    // category of the widget
	protected function _register_controls() {

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( 'Listing Map (Single Listing)', 'jvfrmtd' ),
		) );

			$this->add_control( 'Des', array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="doc-link">'.
					esc_html__('How to use this widget.','jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-single-listing-page/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only single listing detail page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;">' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			) );
			$this->add_control( 'map_relative_listings', Array(
				'label' => esc_html__( 'Show relative listings map marker', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
			) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_google_map_settings', Array(
			'label' => esc_html__( 'Map Settings', 'jvfrmtd' ),
		) );
			$this->add_control('map_zoom_level', Array(
				'type' => Controls_Manager::NUMBER,
				'label' => esc_html__( 'Map Zoom Level', 'jvfrmtd' ),
				'default' => '16',
				'frontend_available' => true,
			));
			$this->add_control('google_map_style', Array(
				'type' => Controls_Manager::CODE,
				'label' => esc_html__( 'Map Style Code', 'jvfrmtd' ),
				'frontend_available' => true,
				'language' => 'json',
			));

		$this->end_controls_section();
  }

    protected function render() {
		//jvbpd_elements_tools()->switch_preview_post();

		$settings = $this->get_settings();
		$isVisible = false;

		wp_reset_postdata();
		/* Check post type */
		$isPreviewMode = is_admin();

		$this->add_render_attribute( 'small_map_container', Array(
			'id' => 'lava-single-map-area',
			'class' => Array( 'small-map-container', 'single-lv-map-style', 'container-map' ),
		) );

		$this->add_render_attribute('wrap', Array(
			'data-jv-detail-nav' => '',
			'id' => 'javo-item-map-section',
		));

		if(!empty($this->get_settings('google_map_style'))){
			$this->add_render_attribute('wrap', 'class', 'has-map-style-code');
		}

		if( 'yes' == $settings['map_relative_listings'] ) {
			add_filter( 'jvbpd_core/single/listing/params', array( jvbpd_elements_tools(), 'add_single_listing_relative_markers' ), 10, 2 );
			$this->add_render_attribute( 'small_map_container', Array(
				'class' => 'relative-markers',
			) );
		}

		/*If preview, show images */
		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-small-map.png';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
		//jvbpd_elements_tools()->restore_preview_post();
    }

	/* Real output */
	public function getContent( $settings, $obj ) {
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="meta-small-map">
						<div <?php echo $this->get_render_attribute_string( 'small_map_container' ); ?>></div>
						<div class="lava-single-map-param">
							<input type="hidden" data-map-height="120">
						</div>
						<?php
						printf(
							'<a href="%1$s%2$s,%3$s" target="_blank" class="btn btn-block btn-default" title="%4$s">%4$s</a>',
							esc_url_raw( 'google.com/maps/dir/Current+Location/' ),
							get_post_meta( get_the_ID(), 'lv_listing_lat', true ),
							get_post_meta( get_the_ID(), 'lv_listing_lng', true ),
							esc_html__( "Get a direction", 'listopia' )
						); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}