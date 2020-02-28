<?php

if( ! function_exists( 'jvbpd_single_navigation' ) ) {
	function jvbpd_single_navigation(){
		return apply_filters(
			'jvbpd_detail_item_nav'
			, Array(
				'page-style'				=> Array(
					'label'					=> esc_html__( "Top", 'jvfrmtd' )
					, 'class'				=> 'glyphicon glyphicon-home'
					, 'type'				=> Array( get_post_type() )
				)
			)
		);
	}
}

if( !function_exists( 'jvbpd_has_attach' ) ) : function jvbpd_has_attach(){
	global $post;
	return !empty( $post->attach );
} endif;

if( !function_exists( 'jvbpd_get_reportShortcode' ) ) : function jvbpd_get_reportShortcode(){
	global $lava_report_shortcode;
	return $lava_report_shortcode;
} endif;

if( !function_exists( 'jvbpd_getSearch1_Shortcode' ) ) : function jvbpd_getSearch1_Shortcode(){
	global $jvbpd_search1;
	return $jvbpd_search1;
} endif;


if( !function_exists( 'jvbpd_elementor_widget' ) ) : function jvbpd_elementor_widget() {
	// Load localization file
	//load_plugin_textdomain( 'jvbpd' );

	// Notice if the Elementor is not active
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}

	// Check version required
	$elementor_version_required = '1.0.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		return;
	}
	// Require the main plugin file
	//require( __DIR__ . '/class-elementor.php' );   //loading the main plugin
}
add_action( 'plugins_loaded', 'jvbpd_elementor_widget' ); endif;


if( !function_exists( 'jvbpd_getDashboardUser' ) ) {
	function jvbpd_getDashboardUser() {
		$objDisplayedUser = get_user_by( 'login', str_replace( "%20", " ", get_query_var( 'user' ) ) );
		if( ! $objDisplayedUser instanceof WP_User ) {
			$objDisplayedUser = wp_get_current_user();
		}
		return $objDisplayedUser;
	}
}

if( !function_exists( 'jvbpd_getUserPage' ) ) {
	function jvbpd_getUserPage( $user_id, $slug='', $closechar='/' ) {

		$user_id = intVal( $user_id );

		if( ! 0 < $user_id )
			return false;

		$user = new WP_User( $user_id );

		$arrDashboard = Array();

		$arrDashboard[]	= 'members';
		$arrDashboard[]	= $user->user_login;

		if( !empty( $slug ) ) {
			$arrDashboard[] = strtolower( $slug );
		}

		$strDashboard = @implode( '/', $arrDashboard );
		return esc_url( home_url( $strDashboard . $closechar ) );
	}
}

if(!function_exists('jvbpd_core_get_sortware') ) {
	function jvbpd_core_get_sortware() {
		return esc_html( $_SERVER['SERVER_SOFTWARE'], 'jvfrmtd' );
	}
}

if(!function_exists('jvbpd_active_plugin') ) {
	function jvbpd_active_plugin($path) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active($path);
	}
}

if( !class_exists( 'jvbpd_elementor_custom_css' ) ) : class jvbpd_elementor_custom_css {

	public function __construct() {
		add_action( 'elementor/element/before_section_start', array( $this, 'jvbpd_custom_css' ), 10, 3 );
		add_action( 'elementor/element/parse_css', array( $this, 'add_post_css' ), 10, 2 );
		//add_action( 'elementor/post-css-file/parse', array( $this, 'add_page_settings_css' ) );
	}

	function jvbpd_custom_css ( $element, $section_id, $args ) {
	   /** @var \Elementor\Element_Base $element */
		$enableControl='';
		if ( 'section' === $element->get_name() && 'section_background' === $section_id ) {
			$enableControl = 'enable';
		}

		if ( 'column' === $element->get_name() && 'section_style' === $section_id ) {
			$enableControl = 'enable';
		}

		if ( '_section_style' === $section_id ) {
			$enableControl = 'enable';
		}

		if ( $enableControl !== 'enable' ) {
			return;
		}


   		$element->start_controls_section(
			'jv_section_custom_css',
			[
				'label' => __( 'Javo Custom CSS', 'jvfrmtd' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'jv_custom_css',
			[
				'type' => \Elementor\Controls_Manager::CODE,
				'label' => __( 'Custom CSS', 'jvfrmtd' ),
				'language' => 'css',
				'render_type' => 'ui',
				'show_label' => true,
				'separator' => 'none',
			]
		);

		$element->add_control(
			'jv_custom_css_description',
			[
				'raw' => __( 'Use "wrapper" to target wrapper element.<br/> ex) wrapper .my-class {color: blue;}', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$element->end_controls_section();
	}

	/**
	 * @param $post_css Post_CSS_File
	 * @param $element Element_Base
	 */
	function add_post_css( $post_css, $element ) {
		$element_settings = $element->get_settings();

		if ( empty( $element_settings['jv_custom_css'] ) ) {
			return;
		}

		$css = trim( $element_settings['jv_custom_css'] );

		if ( empty( $css ) ) {
			return;
		}
		$css = str_replace( 'wrapper', $post_css->get_element_unique_selector( $element ), $css );
		//$css = preg_replace('/wrapper/', $post_css->get_element_unique_selector( $element ), $css, 1);


		// Add a css comment
		$css = sprintf( '/* Start custom CSS for %s, class: %s */', $element->get_name(), $element->get_unique_selector() ) . $css . '/* End custom CSS */';

		$post_css->get_stylesheet()->add_raw_css( $css );
	}

	/**
	 * @param $post_css Post_CSS_File
	 */
	function add_page_settings_css( $post_css ) {
        $page_settings_instance = \Elementor\Plugin::$instance->documents->get( $post_css->get_post_id() );
        $custom_css = $page_settings_instance->get_settings( 'jv_custom_css' );

		$custom_css = trim( $custom_css );

		if ( empty( $custom_css ) ) {
			return;
		}

		$custom_css = str_replace( 'wrapper', 'body.elementor-page-' . $post_css->get_post_id(), $custom_css );

		// Add a css comment
		$custom_css = '/* Start custom CSS for page-settings */' . $custom_css . '/* End custom CSS */';

		$post_css->get_stylesheet()->add_raw_css( $custom_css );
	}

	/**
	 * @param $element Element_Base
	 */
}
new jvbpd_elementor_custom_css; endif;