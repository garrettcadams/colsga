<?php
namespace jvbpdelement\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Jvbpd_Search_Form_Section {

	public static $instance = null;

	public function __construct() {
		$this->add_actions();
	}

	public function add_actions() {
		add_action( 'elementor/element/after_section_end', Array( $this, 'register_controls' ), 10, 3 );
		add_action( 'elementor/frontend/section/before_render', Array( $this, 'before_render' ), 10, 1 );
		add_action( 'elementor/frontend/section/after_render', Array( $this, 'after_render' ), 10, 1 );
	}

	public function register_controls( $element, $section_id, $args ) {
		if( ! in_array( $section_id, Array( /* 'layout' , */'section_layout' ) ) ) {
			return;
		}

		$element->start_controls_section( 'section_javo_settings', Array(
			'label' => esc_html__( "Javo Section Settings", 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_LAYOUT,
		) );

		$element->add_control( 'jvbpd_bp_section', Array(
			'label' => esc_html__( "Use this section for Buddypress loop container?", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
		) );

		$element->add_control( 'jvbpd_bp_section_type', Array(
			'label' => esc_html__( "Use this section for Buddypress loop container?", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'members',
			'options' => Array(
				'members' => esc_html__( "Members", 'jvfrmtd' ),
				'groups' => esc_html__( "Groups", 'jvfrmtd' ),
			),
			'condition' => Array('jvbpd_bp_section' => 'yes' ),
		) );

		$element->add_control( 'jvbpd_search_form', Array(
			'label' => esc_html__( "Use this section for Javo Search Form container?", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'return_value' => 'yes',
		) );

		$element->add_control( 'jvbpd_collapse_section', Array(
			'label' => esc_html__( "Use this section for Advanced Filter ( Collapsible )", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'return_value' => 'yes',
		) );

		$element->add_control( 'jvbpd_collapse_section_position', Array(
			'label' => esc_html__( "Advanced Filter Position Setting ( Cover or Push )", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'options' => Array(
				'' => esc_html__( 'Push', 'jvfrmtd' ),
				'absolute' => esc_html__( 'Cover', 'jvfrmtd' ),
			)
		) );

		$element->add_control( 'query_requester', Array(
			'label' => __( 'Please select search result page', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'default' => '',
			'options' => array_flip( apply_filters(
				'jvbpd_get_map_templates',
				Array( esc_html__( "Default Search Page", 'jvfrmtd' ) => '' )
			) ),
			'description' => esc_html__( 'Select a fit image size depends on the columns.','jvfrmtd' ),
		) );

		$element->end_controls_section();

		$element->update_control( 'content_width', Array(
			'label' => __( 'Content Width', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'range' => Array(
				'px' => Array(
					'min' => 30,
					'max' => 1600,
				),
			),
			'selectors' => Array(
				'{{WRAPPER}} > .elementor-container' => 'max-width: {{SIZE}}{{UNIT}};',
			),
			'condition' => Array( 'layout' => Array( 'boxed' ), ),
			'show_label' => false,
			'separator' => 'none',
		) );

		if( in_array( get_post_type(), Array( 'post', 'lv_listing', 'jvbpd-listing-elmt' ) ) ) {
			$element->start_controls_section( 'section_single_custom', Array(
				'label' => esc_html__( "Single posts option", 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_LAYOUT,
			) );
			$element->add_control( 'use_this_section_featured_image', Array(
				'label' => __( 'Show a post featured image as background image.', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( "It's only for single detail pages.", 'jvfrmtd' ),
			) );
			$element->end_controls_section();
		}

	}

	public function before_render( $element ) {
		$settings = $element->get_settings();
		if( $this->is_search_form_section( $element ) ) {
			$searchActionPage = '';
			$searchActionPageID = intVal( $element->get_settings( 'query_requester' ) );
			if( false !== get_post_status($searchActionPageID) ) {
				$searchActionPage = get_permalink( $searchActionPageID );
			}

			$element->add_render_attribute( 'search_form', Array(
				'class' => 'jvbpd-search-form-section',
				'method' => 'get',
				'action' => esc_attr( $searchActionPage ),
			) );
			?>
			<form <?php echo $element->get_render_attribute_string( 'search_form' ); ?>>
			<?php
		}

		if( $this->is_collapse_section( $element ) ) {
			$element->add_render_attribute( '_wrapper', 'class', Array( 'collapse', 'jvbpd-collapse-section' ) );
			if( $this->is_collapse_section_absolute( $element ) ){
				$element->add_render_attribute( '_wrapper', 'class', 'jvbpd-collapse-section-absolute' );
			}
		}

		if( 'yes' === $element->get_settings( 'use_this_section_featured_image' ) ) {
			$element->add_render_attribute( '_wrapper', Array(
				'class' => 'jvbpd-featured-image-section',
				'style' => sprintf( 'background-image:url(%s);', wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' ) ),
			) );
		}

		if( $this->is_bp_loop_section($element)){
			function_exists('bp_nouveau_before_loop')&&bp_nouveau_before_loop();
			if( function_exists('buddypress')) {
				$loopType = $element->get_settings('jvbpd_bp_section_type');
				// <div id="groups-dir-list" class="groups dir-list" data-bp-list="groups" style="display: block;">
				$hasTypes = sprintf('bp_has_%s', $loopType);
				if( function_exists($hasTypes) ) {
					call_user_func($hasTypes,bp_ajax_querystring( $loopType ));
				}
				buddypress()->current_component = $loopType;
				$element->add_render_attribute( '_wrapper', Array(
					'class' => 'jvbpd-bp-section',
					'data-type' => $loopType,
				) );
			}
		}

	}

	public function after_render( $element ) {
		$settings = $element->get_settings();
		if( $this->is_search_form_section( $element ) ) {
			echo '</form>';
		}
		if( $this->is_bp_loop_section($element)){
			function_exists('bp_nouveau_after_loop')&&bp_nouveau_after_loop();
		}
	}

	public function is_search_form_section( $element ) {
		if( ! in_array( $element->get_name(), Array( 'section' ) ) ) {
			return false;
		}
		$settings = $element->get_settings();
		return isset( $settings[ 'jvbpd_search_form' ] ) && 'yes' === $settings[ 'jvbpd_search_form' ];
	}

	public function is_bp_loop_section( $element ) {
		if( ! in_array( $element->get_name(), Array( 'section' ) ) ) {
			return false;
		}
		$settings = $element->get_settings();
		return isset( $settings[ 'jvbpd_bp_section' ] ) && 'yes' === $settings[ 'jvbpd_bp_section' ];
	}

	public function is_collapse_section( $element ) {
		if( ! in_array( $element->get_name(), Array( 'section' ) ) ) {
			return false;
		}
		$settings = $element->get_settings();
		return isset( $settings[ 'jvbpd_collapse_section' ] ) && 'yes' === $settings[ 'jvbpd_collapse_section' ];
	}

	public function is_collapse_section_absolute( $element ) {
		if( ! in_array( $element->get_name(), Array( 'section' ) ) ) {
			return false;
		}
		$settings = $element->get_settings();
		return isset( $settings[ 'jvbpd_collapse_section_position' ] ) && 'absolute' === $settings[ 'jvbpd_collapse_section_position' ];
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

if( ! function_exists( 'jvbpd_search_container' ) ) {
	function jvbpd_search_container() {
		return Jvbpd_Search_Form_Section::getInstance();
	}
	jvbpd_search_container();
}