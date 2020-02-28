<?php
/**
 * Widget Name: Map container widget
 * Author: Javo
 * Version: 1.0.0.1
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class jvbpd_map_maps extends Widget_Base {

	public function get_name() { return 'jvbpd-map-maps'; }
	public function get_title() { return 'Map area'; }
	public function get_icon() { return 'fa fa-map-o'; }
	public function get_categories() { return [ 'jvbpd-map-page' ]; }

  protected function _register_controls() {

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "General", 'jvfrmtd' ),
		) );

		$this->add_responsive_control( 'map_height', Array(
			'type' => Controls_Manager::SLIDER,
			'default' => Array(  'size' => 450, 'unit' => 'px' ),
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				),
			),
			'size_units' => Array( 'px' ),
			'selectors' => Array(
				'{{WRAPPER}} .javo-maps-area-wrap .javo-maps-area' => 'min-height: {{SIZE}}{{UNIT}};',
			),
		) );

		$listingTaxonomies_options = jvbpd_elements_tools()->get_taxonomies( 'lv_listing' );

		$this->add_control( 'lv_listing_taxonomy', Array(
			'label' => __( 'Listing Taxonomy', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'default' => '',
			'options' => Array( '' => esc_html__( "Select One", 'jvfrmtd' ) ) + $listingTaxonomies_options,
			'separator' => 'none',
		) );

		jvbpd_elements_tools()->add_tax_term_control( $this, 'lv_listing_%1$s_term', Array(
			'taxonomies' => array_keys( $listingTaxonomies_options ),
			'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
			'parent' => 'lv_listing_taxonomy',
			'type' => Controls_Manager::SELECT2,
			'multiple' => false,
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_controls_settings', Array(
			'label' => esc_html__( "Controls Settings", 'jvfrmtd' ),
		) );

			foreach(Array(
				'zoom_in_out' => esc_html__("Use Zoom In/Out Control"),
				'draggable' => esc_html__("Use Lock Control"),
				'mapType' => esc_html__("Use Map Type Control"),
				'current_search' => esc_html__("Use Current Search Control"),
				'geolocation' => esc_html__("Use My Position Control"),
			) as $ctlKey => $ctlLabel ){
				$this->add_control( 'show_' . $ctlKey, Array(
					'label' => $ctlLabel,
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'none',
				) );
			}

		$this->end_controls_section();

		$this->start_controls_section( 'section_hover_settings', Array(
			'label' => esc_html__( "Hover Settings", 'jvfrmtd' ),
		) );

			$this->add_control( 'block_hover_event', Array(
				'label' => __( 'Listing Hover Event', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => Array(
					'' => esc_html__( "No Event", 'jvfrmtd' ),
					'bounce' => esc_html__( "Animation - Bounce marker", 'jvfrmtd' ),
					'replace_marker' => esc_html__( "Alternative Image", 'jvfrmtd' ),
				),
				'separator' => 'none',
			) );

			$this->add_control( 'block_hover_move_map', Array(
				'label' => __( 'Move to the area', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => Array( 'block_hover_event!' => '', ),
				'separator' => 'none',
			) );

			$this->add_control( 'block_hover_map_level', Array(
				'label' => __( 'Zoom Level ( after moved maps )', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'condition' => Array( 'block_hover_move_map' => 'yes', ),
				'default' => '18',
				'separator' => 'none',
			) );

			$this->add_control( 'block_hover_marker', Array(
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__( "Marker Image", 'jvfrmtd' ),
				'condition' => Array( 'block_hover_event' => 'replace_marker', ),
				'default' => Array(
					'url' => '', //\Elementor\Utils::get_placeholder_image_src(),
				),
			) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_addons', Array(
			'label' => esc_html__( "Addons", 'jvfrmtd' ),
		) );

			$this->add_control( 'cluster_radius', Array(
				'type' => Controls_Manager::NUMBER,
				'label' => esc_html__( "Cluster Radius", 'jvfrmtd' ),
				'default' => '50',
			) );

			$this->add_control( 'map_marker', Array(
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__( "Marker Image", 'jvfrmtd' ),
				'default' => Array(
					'url' => '', //\Elementor\Utils::get_placeholder_image_src(),
				),
			) );

			$this->add_control( 'map_init_zoom', Array(
				'type' => Controls_Manager::NUMBER,
				'label' => esc_html__( "Map Zoom Level", 'jvfrmtd' ),
				'min' => 1,
				'max' => 24,
			) );

			$this->add_control( 'google_map_style', Array(
				'type' => Controls_Manager::CODE,
				'label' => esc_html__( "Map Style Code", 'jvfrmtd' ),
				'language' => 'javascript',
				'description' => sprintf(
					__( 'Please <a href="%1$s" target="_blank">click here</a> to create your own stlye and paste json code here.(<a href="https://snazzymaps.com/" target="_blank">Snazzymaps</a>)', 'jvfrmtd' ),
					esc_url( 'mapstyle.withgoogle.com' )
				)
			) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_control_styles', Array(
			'label' => esc_html__( "Controls Style", 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );
			$this->add_control( 'control_background_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__( "Background Color", 'jvfrmtd' ),
				'selectors' => Array(
					'{{WRAPPER}} .map-controls .jvbpd-map-control' => 'background-color:{{VALUE}};',
				),
				'default' => '#4DB7FE',
			) );
			$this->add_control( 'control_border_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__( "Border Color", 'jvfrmtd' ),
				'selectors' => Array(
					'{{WRAPPER}} .map-controls .jvbpd-map-control' => '-webkit-box-shadow:0px 0px 0px 5px {{VALUE}};',
					'{{WRAPPER}} .map-controls .jvbpd-map-control' => 'box-shadow:0px 0px 0px 5px {{VALUE}};',
				),
				'default' => 'rgba(255, 255, 255, 0.4)',
			) );
			$this->add_control( 'control_icon_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__( "Icon Color", 'jvfrmtd' ),
				'selectors' => Array(
					'{{WRAPPER}} .map-controls .jvbpd-map-control i.fa' => 'color:{{VALUE}};',
				),
			) );
			$this->add_control( 'control_icon_size', Array(
				'type' => Controls_Manager::NUMBER,
				'label' => esc_html__( "Icon Size", 'jvfrmtd' ),
				'selectors' => Array(
					'{{WRAPPER}} .map-controls .jvbpd-map-control i.fa' => 'font-size:{{VALUE}}px;',
				),
				'default' => '15',
			) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_cluster_styles', Array(
			'label' => esc_html__( "Cluster Style", 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );
			$this->add_control( 'cluster_background_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__( "Background Color", 'jvfrmtd' ),
				'selectors' => Array(
					'{{WRAPPER}} .javo-map-cluster' => 'background-color:{{VALUE}};',
				),
				'default' => '#4DB7FE',
			) );
			$this->add_control( 'cluster_border_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__( "Border Color", 'jvfrmtd' ),
				'selectors' => Array(
					'{{WRAPPER}} .javo-map-cluster' => 'border:solid 5px {{VALUE}}!important;',
				),
				'default' => 'rgba(255, 255, 255, 0.4)',
			) );
			$this->add_control( 'cluster_icon_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__( "Icon Color", 'jvfrmtd' ),
				'selectors' => Array(
					'{{WRAPPER}} .javo-map-cluster' => 'color:{{VALUE}};',
				),
			) );
			$this->add_control( 'cluster_icon_size', Array(
				'type' => Controls_Manager::NUMBER,
				'label' => esc_html__( "Icon Size", 'jvfrmtd' ),
				'selectors' => Array(
					'{{WRAPPER}} .javo-map-cluster' => 'font-size:{{VALUE}}px;',
				),
				'default' => '15',
			) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_maptype_dropdown_styles', Array(
			'label' => esc_html__( "Map Type Dropdown Style", 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_mapType' => 'yes',
			]
		) );
			$this->add_control( 'dropdown_background_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__( "Background Color", 'jvfrmtd' ),
				'selectors' => Array(
					'.map-controls .dropdown-menu' => 'background-color:{{VALUE}};',
				),
				'default' => '#4DB7FE',
			) );
			$this->add_control( 'dropdown_border_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__( "Border Color", 'jvfrmtd' ),
				'selectors' => Array(
					'.map-controls .dropdown-menu' => '-webkit-box-shadow:0px 0px 0px 5px {{VALUE}};',
					'.map-controls .dropdown-menu' => 'box-shadow:0px 0px 0px 5px {{VALUE}};',
				),
				'default' => 'rgba(255, 255, 255, 0.4)',
			) );
			$this->add_control(
				'text_color',
				[
					'label' => __( 'Text Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#fff',
					'selectors' => [
						'{{WRAPPER}} .map-controls .dropdown-menu a' => 'color: {{VALUE}};',
					],
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_3,
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'caption_typography',
					'selector' => '{{WRAPPER}} .map-controls .dropdown-menu a',
					'scheme' => Scheme_Typography::TYPOGRAPHY_3,
				]
			);

		$this->end_controls_section();


    }

	public function getMapOptions() {
		$output = Array();
		foreach(
			Array(
				'google_map_style',
				'map_init_zoom',
				'map_marker',
			)
		as $setting ) {
			$output[ $setting ] = $this->get_settings( $setting );
		}

		$taxonomy = $this->get_settings( 'lv_listing_taxonomy' );
		$term = $this->get_settings( 'lv_listing_' . $taxonomy . '_term' );
		if( !empty( $taxonomy ) && !empty( $term ) ) {
			$output[ 'first_filter' ] = Array( 'taxonomy' => $taxonomy, 'term' => $term );
		}
		$output = array_filter( $output );
		return wp_json_encode( $output, JSON_NUMERIC_CHECK );
	}

    protected function render() {
		$settings = $this->get_settings();
		$this->getContent( $settings, get_post() );
    }

	public function getContent( $settings, $obj ) {
		$this->add_render_attribute( 'wrap', Array(
			'class' => apply_filters( 'jvbpd_map_class', Array( 'javo-maps-area-wrap' ), get_the_ID() ),
			'data-settings' => $this->getMapOptions(),
		) );
		$this->add_render_attribute( 'map', Array(
			'class' => 'javo-maps-area',
			'data-cluster-radius' => $this->get_settings('cluster_radius'),
			'data-block-hover' => $this->get_settings('block_hover_event'),
			'data-block-hover-move-map' => $this->get_settings('block_hover_move_map'),
			'data-block-hover-map-lv' => $this->get_settings('block_hover_map_level'),
		));
		if( 'replace_marker' == $this->get_settings('block_hover_event')) {
			$this->add_render_attribute( 'map', 'data-block-hover-marker', $this->get_settings('block_hover_marker')['url'] );
		}
		?>
		<div <?php echo $this->get_render_attribute_string('wrap'); ?>>
			<div <?php echo $this->get_render_attribute_string('map'); ?>></div>
			<div class="map-controls map-left-control">
				<div class="javo-map-inner-control-wrap">
					<?php if( "yes" == $this->get_settings('show_zoom_in_out') ) { ?>
						<div class="jvbpd-map-control control-zoom-in" title="<?php esc_html_e("ZOOM OUT", 'jvfrmtd');?>"><i class="fa fa-minus"></i></div>
						<div class="jvbpd-map-control control-zoom-out" title="<?php esc_html_e("ZOOM IN", 'jvfrmtd');?>"><i class="fa fa-plus"></i></div>
					<?php } ?>

					<?php if( "yes" == $this->get_settings('show_draggable') ) { ?>
						<div class="jvbpd-map-control active" data-map-move-allow title="<?php esc_html_e('LOCK' , 'jvfrmtd') ?>" data-unlock="<?php esc_html_e('UnLock' , 'jvfrmtd') ?>" data-lock="<?php esc_html_e('Lock','javospot'); ?>">
							<i class="fa fa-lock"></i>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="map-controls map-right-control">
				<?php if( "yes" == $this->get_settings('show_mapType') ) { ?>
					<div class="btn-group dropleft">
						<div class="jvbpd-map-control control-map-type dropdown-toggle" data-toggle="dropdown" role="group" aria-haspopup="true" aria-expanded="false" title="<?php esc_html_e("MAP TYPE", 'jvfrmtd');?>">
							<i class="fa fa-eye"></i>
						</div>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="#" data-type="roadmap"><?php esc_html_e("Roadmap", 'jvfmtd'); ?></a>
							<a class="dropdown-item" href="#" data-type="satellite"><?php esc_html_e("Satellite", 'jvfmtd'); ?></a>
							<a class="dropdown-item" href="#" data-type="hybrid"><?php esc_html_e("Hybrid", 'jvfmtd'); ?></a>
							<a class="dropdown-item" href="#" data-type="terrain"><?php esc_html_e("Terrain", 'jvfmtd'); ?></a>
						</div>
					</div>
				<?php } ?>

				<?php if( "yes" == $this->get_settings('show_geolocation') ) { ?>
					<div class="jvbpd-map-control my-position-trigger" title="<?php esc_html_e("SEARCH CURRENT LOCATION", 'jvfrmtd');?>"><i class="fa fa-crosshairs"></i></div>
				<?php } ?>

				<?php if( "yes" == $this->get_settings('show_current_search') ) { ?>
					<div class="jvbpd-map-control current-search" title="<?php esc_html_e("SEARCH ONLY THIS AREA", 'jvfrmtd');?>"><i class="fa fa-map-marker"></i></div>
				<?php } ?>

			</div>
		</div>
		<?php
	}

}