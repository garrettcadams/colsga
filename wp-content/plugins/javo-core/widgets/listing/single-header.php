<?php
/**
 * Widget Name: Single header ( Not used )
 * Author: Javo
 * Version: 1.0.0.3
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if( !defined( 'ABSPATH' ) ) {
	die;
}

class jvbpd_single_header extends Widget_Base {

	public function get_name() { return 'jvbpd-single-header'; }

	public function get_title() { return 'Single header (Group)'; }

	public function get_icon() { return 'fa fa-user-o'; }

	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

    protected function _register_controls() {
		$this->start_controls_section( 'section_header_settings', Array(
			'label' => esc_html__( 'Header Settings', 'jvfrmtd' ),
		) );
			$this->add_control( 'first_container', Array(
				'label' => __( 'First show container', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'featured',
				'options' => Array(
					/** 'grid' => esc_html__( 'Grid', 'jvfrmtd' ), **/
					'grid_style' => esc_html__( 'Grid', 'jvfrmtd' ),
					'featured' => esc_html__( 'Featured', 'jvfrmtd' ),

					/** 'category_featured' => esc_html__( 'Category Featured', 'jvfrmtd' ), **/
					'listing_category' => esc_html__( 'Category Featured', 'jvfrmtd' ),

					'map' => esc_html__( 'Google Map', 'jvfrmtd' ),
					'streetview' => esc_html__( 'Street View', 'jvfrmtd' ),
					'view3d' => esc_html__( '3D(360) Image', 'jvfrmtd' ),
					'viewVideo' => esc_html__( 'Video', 'jvfrmtd' ),
				),
			) );

			$this->add_control( 'map_relative_listings', Array(
				'label' => esc_html__( 'Show relative listings map marker', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
			) );

			$this->add_control( 'transition_effect', Array(
				'label' => __( 'Transition Effect', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => Array(
					'fade' => esc_html__( 'Fade', 'jvfrmtd' ),
					'left-to-right' => esc_html__( 'Left to right', 'jvfrmtd' ),
				),
			) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_overlay_settings', Array(
			'label' => esc_html__( 'Overlay Settings', 'jvfrmtd' ),
		) );

		foreach(Array(
			'featured' => Array(
				'label' => esc_html__("Featured Image", 'jvfrmtd'),
				'selector' => '{{WRAPPER}} .container-featured .header-overlay',
			),
			'category_featured' => Array(
				'label' => esc_html__("Featured Category Image", 'jvfrmtd'),
				'selector' => '{{WRAPPER}} .container-category-featured .header-overlay',
			),
			'grid' => Array(
				'label' => esc_html__("Grid", 'jvfrmtd'),
				'selector' => '{{WRAPPER}} .container-grid .swiper-slide:after',
			),
		) as $controlName => $controlMeta){
			$this->add_control( 'overlay_' . $controlName, Array(
				'label' => $controlMeta['label'],
				'type' => Controls_Manager::COLOR,
				'selectors' => Array(
					$controlMeta['selector'] => 'background-color:{{VALUE}};',
				),
			) );
		}
		$this->end_controls_section();

		$this->start_controls_section( 'section_parallax_settings', Array(
			'label' => esc_html__( 'Parallax Settings', 'jvfrmtd' ),
		) );

		foreach(Array(
			'featured' => esc_html__("Featured Image", 'jvfrmtd'),
			'category_featured' => esc_html__("Featured Category Image", 'jvfrmtd'),
		) as $controlName => $controlLabel){
			$this->add_control( 'parallax_' . $controlName, Array(
				'label' => $controlLabel,
				'type' => Controls_Manager::SWITCHER,
			) );
		}
		$this->end_controls_section();

		$this->start_controls_section( 'section_google_map_settings', Array(
			'label' => esc_html__( 'Map Settings', 'jvfrmtd' ),
		) );
			$this->add_control('google_map_style', Array(
				'type' => Controls_Manager::CODE,
				'label' => esc_html__( 'Map Style Code', 'jvfrmtd' ),
				'frontend_available' => true,
				'language' => 'json',
			));
		$this->end_controls_section();

		$this->start_controls_section( 'section_custom_meta_settings', Array(
			'label' => esc_html__( 'Custom Meta', 'jvfrmtd' ),
		) );
			$this->add_control( 'header_featured_note', array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="notice">'.
					esc_html__('Note : The Custom meta works only in the Featured image header.', 'jvfrmtd').
					'</li></ul></div>'
				)
			));

			$this->add_control('header_custom_meta', Array(
				'type' => Controls_Manager::CODE,
				'show_label' => false,
				'language' => 'html',
			));

		$this->end_controls_section();

        $this->start_controls_section(
			'header_group_style',
				[
				  'label' => __( 'Header Group Style', 'jvfrmtd' ),
				  'tab'   => Controls_Manager::TAB_STYLE,
				]
		);

		$this->add_responsive_control(
			'header_group_height',
				[
					'label' => __( 'Header Group Height', 'jvfrmtd' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 600,
						'unit' => 'px',
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .single-item-tab-feature-bg-wrap' => 'min-height: {{SIZE}}{{UNIT}};',
					],
				]
			);
		$this->end_controls_section();
	}

	protected function render() {
		jvbpd_elements_tools()->switch_preview_post();
		add_filter( 'jvbpd_widget/single-header/first_container', array( $this, 'first_container_filter' ) );
		$settings = $this->get_settings();

		if( 'yes' == $settings['map_relative_listings'] ) {
			add_filter( 'jvbpd_core/single/listing/params', array( jvbpd_elements_tools(), 'add_single_listing_relative_markers' ), 10, 2 );
			$this->add_render_attribute( 'map_container', Array(
				'class' => 'relative-markers',
			) );
		}
		$this->getContent( $settings, get_post() );
		jvbpd_elements_tools()->restore_preview_post();
	}

	public function getContent( $settings, $obj ) {
		$this->add_render_attribute( 'wrap', Array(
			'class' => Array(
				'single-item-tab-feature-bg-wrap',
				'single-header-wrap',
				'effect-' . $this->get_settings('transition_effect'),
			),
			'data-first' => $this->getFirstShowContainer(),
		));
		if(!empty($this->get_settings('google_map_style'))){
			$this->add_render_attribute('wrap', 'class', 'has-map-style-code');
		}?>

		<div <?php echo $this->get_render_attribute_string('wrap'); ?>>
			<div class="containers-wrap">
				<?php
				foreach( Array( 'grid', 'featured', 'category_featured', 'map', 'streetview', 'view3d', 'viewVideo' )
				as $container_method  ) {
					if( method_exists($this, $container_method) ) {
						if('yes' == $this->get_settings('parallax_' . $container_method)) {
							$this->add_render_attribute('container-' . $container_method, 'class', 'parallax-overlay');
						}
						call_user_func( Array( $this, $container_method ) );
					}
				} ?>
			</div>
		</div>
		<?php
	}

	public function getFirstShowContainer() { return apply_filters( 'jvbpd_widget/single-header/first_container', $this->get_settings( 'first_container' ) ); }
	public function first_container_filter( $container='' ) {
		$replaces = Array(
			'grid_style' => Array( 'grid', 'grid' ),
			'listing_category' => Array( 'category_featured', 'listing_category' ),
		);

		foreach( $replaces as $replace => $find ) {
			if( in_array( $container, $find ) ) {
				$container = $replace;
			}
		}
		return $container;
	}

	public function preloader() {
		?>
		<div class="preloader">
			<div class="sk-three-bounce">
				<div class="sk-child sk-bounce1"></div>
				<div class="sk-child sk-bounce2"></div>
				<div class="sk-child sk-bounce3"></div>
			</div>
		</div>
		<?php
	}

	public function grid() {
		$this->add_render_attribute( 'container-grid', Array(
			'class' => Array( 'container-item', 'container-grid' ),
		)); ?>
		<div <?php echo $this->get_render_attribute_string( 'container-grid' ); ?>>
			<div class="swiper-container hidden">
				<div class="swiper-wrapper">
					<?php
					$arrImages = get_post_meta( get_queried_object_id(), 'detail_images', true );
					if( is_array( $arrImages ) ) : foreach( $arrImages as $intImageID ) {
						if( $strSRC = wp_get_attachment_image_src( $intImageID, 'jvbpd-large-v' ) ) {
							$strFullSRC = wp_get_attachment_image_src( $intImageID, 'full' );
							$strALT = explode("/",$strSRC[0]);
							$strALT = array_pop($strALT);
							printf( '<div class="swiper-slide" data-src="%1$s"><div style="background-repeat:no-repeat; background-size:cover; background-image:url(%2$s); width:100%%; height:100%%;" alt="%3$s"></div></div>', $strFullSRC[0], $strSRC[0], $strALT );
						}
					} endif; ?>
				</div> <!-- swiper-wrapper -->
				<!-- Add Pagination -->
				<div class="swiper-pagination"></div>
				<!-- Arrow -->
				<div class="swiper-button-next"><span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></div>
				<div class="swiper-button-prev"><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span></div>
				<div class="jvbpd-single-header-gradient shadow <?php echo sanitize_html_class( jvbpd_tso()->get( 'lv_listing_single_header_cover', null ) ); ?>"></div>
			</div><!-- swiper-container -->
			<?php $this->preloader(); ?>
		</div>
		<?php
	}

	public function featured() {
		$this->add_render_attribute( 'container-featured', Array(
			'class' => Array( 'container-item', 'container-featured' ),
			'data-background' => esc_url_raw( wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' ) ),
		)); ?>
		<div <?php echo $this->get_render_attribute_string( 'container-featured' ); ?>>
			<div class="container-image-viewer"></div>
			<div class="header-overlay"></div>
			<div class="header-parallax"></div>
			<div class="header-content">
				<div class="header-content-wrap">
					<?php
					$headerContents = $this->get_settings('header_custom_meta');
					$contentInst = new \Jvbpd_Replace_Content(get_the_ID(), $headerContents);
					echo $contentInst->render(); ?>
				</div>
			</div>
			<?php $this->preloader(); ?>
		</div>
		<?php
	}

	public function category_featured() {
		$strCurrentTermURl = '';
		if( function_exists( 'lava_directory' ) ) {
			$intCurrentItemTerms = wp_get_object_terms( get_the_ID(), 'listing_category', array( 'fields' => 'ids' ) );
			$intCurrentItemTermsID = isset( $intCurrentItemTerms[0] ) ? $intCurrentItemTerms[0] : 0;
			$intCurrentTermFeaturedImage = lava_directory()->admin->getTermOption( $intCurrentItemTermsID, 'featured', 'listing_category' );
			$strCurrentTermURl = wp_get_attachment_image_url( $intCurrentTermFeaturedImage, 'full' );
		}
		$this->add_render_attribute( 'container-category_featured', Array(
			'class' => Array( 'container-item', 'container-category-featured' ),
			'data-background' => esc_url_raw( $strCurrentTermURl ),
		)); ?>
		<div <?php echo $this->get_render_attribute_string( 'container-category_featured' ); ?>>
			<div class="container-image-viewer"></div>
			<div class="header-overlay"></div>
			<div class="header-parallax"></div>
			<?php $this->preloader(); ?>
		</div>
		<?php
	}

	public function map() {
		$this->add_render_attribute( 'container-map', Array(
			'class' => Array( 'container-item', 'container-map' ),
		) ); ?>
		<div <?php echo $this->get_render_attribute_string( 'container-map' ); ?>></div>
		<?php
	}

	public function streetview() {
		$this->add_render_attribute( 'container-streetview', Array(
			'class' => Array( 'container-item', 'container-streetview' ),
		) ); ?>
		<div <?php echo $this->get_render_attribute_string( 'container-streetview' ); ?>></div>
		<?php
	}

	public function view3d() {
		if( class_exists( 'lvDirectory3DViewer_Render' ) ) {
			$obj3DViewer = new \lvDirectory3DViewer_Render( get_post() );
			$is_has_3d = $obj3DViewer->viewer;
		}else{
			$is_has_3d = false;
		}

		$this->add_render_attribute( 'container-3dview', Array(
			'class' => Array( 'container-item', 'container-3dview' ),
		) ); ?>
		<div <?php echo $this->get_render_attribute_string( 'container-3dview' ); ?>>
			<script type="text/html">
				<?php
				if( $is_has_3d ) {
					$obj3DViewer->output();
				} ?>
			</script>
			<?php $this->preloader(); ?>
		</div>
		<?php
	}

	public function viewVideo() {
		if( class_exists( 'lvDirectoryVideo_Render' ) ) {
			$objVideo = new \lvDirectoryVideo_Render( get_post(), array(
				'width' => '100',
				'height' => '100',
				'unit' => '%',
			) );
			$is_has_video = $objVideo->hasVideo();
		}else{
			$is_has_video = false;
		}
		$this->add_render_attribute( 'container-video', Array(
			'class' => Array( 'container-item', 'container-video' ),
		) ); ?>
		<div <?php echo $this->get_render_attribute_string( 'container-video' ); ?>>
			<script type="text/html">
				<?php
				if( $is_has_video ) {
					$objVideo->output();
				} ?>
			</script>
			<?php $this->preloader(); ?>
		</div>
		<?php
	}
}