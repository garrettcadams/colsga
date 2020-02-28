<?php

class Jvbpd_Listing_Elementor {

	public $template;
	public $temp_is_singualr = null;
	public static $instance = null;

	public $parallax_items = Array();

	private static $elementor_instance = false;

	public function __construct() {

		$this->template = get_template();

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			self::$elementor_instance = Elementor\Plugin::instance();
			$this->includes();
			$this->load_textdomain();
			// Scripts and styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_filter( 'body_class', array( $this, 'body_class' ) );
			add_filter( 'jvbpd_theme/header/file-name', array( $this, 'loadElementorHeader' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'elementor_not_available' ) );
			add_action( 'network_admin_notices', array( $this, 'elementor_not_available' ) );
		}

		add_filter( 'template_include', array( $this, 'elementor_core_template' ), 999 );
		// add_filter( 'template_include', array( $this, 'post_template' ), 999 );
		// add_filter( 'template_include', array( $this, 'ticket_template' ), 999 );

		add_filter( 'jvbpd_theme/header/none/content', array( $this, 'header_alert' ) );
		add_filter( 'jvbpd_core/elementor/custom_header_id', array( $this, 'load_custom_header' ), 10, 2 );
		add_filter( 'jvbpd_core/elementor/custom_footer_id', array( $this, 'load_custom_footer' ), 10, 2 );
		add_filter( 'jvbpd_core/elementor/custom_login_id', array( $this, 'load_custom_login' ) );
		add_filter( 'jvbpd_core/elementor/custom_signup_id', array( $this, 'load_custom_signuo' ) );
		add_filter( 'jvbpd_core/elementor/custom_single_listing', array( $this, 'load_custom_single_listing' ), 10, 2 );
		add_filter( 'jvbpd_core/elementor/custom_single_post_template_id', array( $this, 'load_custom_single_post' ), 10, 2 );
		add_filter( 'jvbpd_core/elementor/custom_single_product_template_id', array( $this, 'load_custom_single_product' ), 15, 2 );
		add_filter( 'jvbpd_core/elementor/custom_archive_listing', array( $this, 'load_custom_archive_listing' ), 10, 2 );

		// add_filter( 'Javo/Header/Wrap/CustomCSS', array( $this, 'header_custom_css' ), 10, 2 );
		add_action( 'Javo/Footer/Render', Array($this, 'output_member_modal') );
		add_action( 'Javo/Footer/Render', Array($this, 'output_get_direction_modal') );


		add_filter( 'elementor/frontend/before_enqueue_scripts', array( $this, 'support_bp_single' ) );
		add_filter( 'elementor/frontend/after_enqueue_scripts', array( $this, 'support_bp_single_restore' ) );

		// add_filter( 'elementor/frontend/widget/before_render', array( $this, 'custom_module_attribute' ) );

		// add_action( 'elementor/template-library/before_get_source_data', array( $this, 'switch_to_preview_query' ) );
		// add_action( 'elementor/template-library/after_get_source_data', array( $this, 'restore_current_query' ) );
		// add_action( 'elementor/dynamic_tags/before_render', array( $this, 'switch_to_preview_query' ) );
		// add_action( 'elementor/dynamic_tags/after_render', array( $this, 'restore_current_query' ) );

		add_shortcode( 'jve_template', Array( $this, 'get_elementor_template' ) );

		// Sticky
		add_action('elementor/element/before_section_start', Array($this, 'sticky_section'), 10, 3);
		add_action('elementor/frontend/section/before_render', Array($this, 'sticky_render'));

		// Parallax
		add_action('elementor/element/before_section_start', Array($this, 'parallax_section'), 10, 3);
		add_action('elementor/frontend/section/before_render', Array($this, 'parallax_render'));
		add_action('elementor/frontend/element/before_render', Array($this, 'parallax_render'));
		add_action('elementor/frontend/before_enqueue_scripts', Array($this, 'parallax_param'));

		add_filter( 'elementor/icons_manager/native', Array( $this, 'add_icon_library' ) );

	}

	public function get_elementor_template( $atts, $content='' ) {
		$args = shortcode_atts( Array(
			'id' => 0,
		), $atts );

		return self::$elementor_instance->frontend->get_builder_content_for_display( $args[ 'id' ] );
	}

	public static function getElementorInstance() {
		return self::$elementor_instance;
	}

	/**
	 * Prints the admin notics when Elementor is not installed or activated.
	 */
	public function elementor_not_available() {

		if ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) {
			$url = network_admin_url() . 'plugins.php?s=elementor';
		} else {
			$url = network_admin_url() . 'plugin-install.php?s=elementor';
		}

		echo '<div class="notice notice-error">';
		/* Translators: URL to install or activate Elementor plugin. */
		echo '<p>' . sprintf( __( '<strong>This theme</strong> requires <strong><a href="%s">Elementor</strong></a> plugin installed & activated.', 'jvfrmtd' ) . '</p>', $url );
		echo '</div>';
	}

	/**
	 * Loads the globally required files for the plugin.
	 */
	public function includes() {
		require_once( jvbpdCore()->elementor_path . '/class-admin.php' );
		require_once( jvbpdCore()->elementor_path . '/functions.php' );
	}

	/**
	 * Loads textdomain for the plugin.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'jvfrmtd' );
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public function enqueue_scripts() {
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			if ( jvbpd_header_enabled() ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( get_jvbpd_header_id() );
				$css_file->enqueue();
			}
		}
	}

	/**
	 * Adds classes to the body tag conditionally.
	 *
	 * @param  Array $classes array with class names for the body tag.
	 *
	 * @return Array          array with class names for the body tag.
	 */
	public function body_class( $classes ) {

		if ( jvbpd_header_enabled() ) {
			$classes[] = 'jvbpd-header';
		}

		if ( jvbpd_footer_enabled() ) {
			$classes[] = 'jvbpd-footer';
		}

		if ( jvbpd_listing_archive_enabled() ) {
			$classes[] = 'jvbpd-listing-archive';
		}

		$classes[] = 'jvbpd-template-' . $this->template;
		$classes[] = 'jvbpd-stylesheet-' . get_stylesheet();

		return $classes;
	}

	/**
	 * Prints the Header content.
	 */
	public static function get_header_content() {
		$singleListing = apply_filters( 'jvbpd_core/elementor/custom_single_listing', get_jvbpd_header_id(), get_post() );
		if( -1 < $singleListing ) {
			echo self::$elementor_instance->frontend->get_builder_content_for_display( $singleListing );
		}
	}

	/**
	 * Prints the Footer content.
	 */
	public static function get_footer_content() {
		echo self::$elementor_instance->frontend->get_builder_content_for_display( get_jvbpd_footer_id() );
	}

	/**
	 * Prints the Archive content.
	 */
	public static function get_listing_archive_content( WP_Term $term ) {
		$archiveListing = apply_filters( 'jvbpd_core/elementor/custom_archive_listing', get_jvbpd_listing_archive_id(), $term );
		if( -1 < $archiveListing ) {
			echo self::$elementor_instance->frontend->get_builder_content_for_display( $archiveListing );
		}
	}

	/**
	 * Prints the Header content.
	 */
	public static function get_custom_header_content() {
		$headerID = apply_filters( 'jvbpd_core/elementor/custom_header_id', get_jvbpd_listing_custom_header_id(), get_queried_object() );
		if( -1 < $headerID ) {
			echo self::$elementor_instance->frontend->get_builder_content_for_display( $headerID );
		}
	}

	/**
	 * Prints the Footer content.
	 */
	public static function get_custom_footer_content() {
		$footerID = apply_filters( 'jvbpd_core/elementor/custom_footer_id', get_jvbpd_listing_custom_footer_id(), get_post() );
		if( -1 < $footerID ) {
			echo self::$elementor_instance->frontend->get_builder_content_for_display( $footerID );
		}
	}

	/**
	 * Prints the Login content.
	 */
	public static function get_custom_login_content() {
		$loginID = apply_filters( 'jvbpd_core/elementor/custom_login_id', get_jvbpd_listing_custom_login_id() );
		echo self::$elementor_instance->frontend->get_builder_content_for_display( $loginID );
	}

	/**
	 * Prints the Login content.
	 */
	public static function get_custom_signup_content() {
		$signupID = apply_filters( 'jvbpd_core/elementor/custom_signup_id', get_jvbpd_listing_custom_signup_id() );
		echo self::$elementor_instance->frontend->get_builder_content_for_display( $signupID );
	}

	/**
	 * Prints the Single Post
	 */
	public static function get_custom_single_post_content() {
		$signupID = apply_filters( 'jvbpd_core/elementor/custom_single_post_template_id', self::get_settings( 'single_post_page' ), get_post() );
		echo self::$elementor_instance->frontend->get_builder_content_for_display( $signupID );
	}

	/**
	 * Prints the Single Product
	 */
	public static function get_custom_single_product_content() {
		$signupID = apply_filters( 'jvbpd_core/elementor/custom_single_product_template_id', self::get_settings( 'single_product_page' ), get_post() );
		echo self::$elementor_instance->frontend->get_builder_content_for_display( $signupID );
	}

	/**
	 * Prints the Single ticket
	 */
	public static function get_custom_single_ticket_content() {
		$ticketID = apply_filters( 'jvbpd_core/elementor/custom_single_ticket_template_id', self::get_settings( 'single_ticket_page' ), get_post() );
		echo self::$elementor_instance->frontend->get_builder_content_for_display( $ticketID );
	}


	/**
	 * Get option for the plugin settings
	 *
	 * @param  mixed $setting Option name.
	 * @param  mixed $default Default value to be received if the option value is not stored in the option.
	 *
	 * @return mixed.
	 */
	public static function get_settings( $setting = '', $default = '' ) {
		if( in_array( $setting, Array(
			'single_listing_page',
			'listing_search',
			'type_listing_archive',
			'custom_header',
			'custom_footer',
			'custom_login',
			'custom_signup',
			'custom_preview',
			'single_post_page',
			'single_ticket_page',
			'single_product_page',
		) ) ){
			$templates = self::get_template_id( $setting );
			return is_array( $templates ) ? $templates[0] : '';
		}
	}

	public static function get_template_id( $type ) {

		$cached = wp_cache_get( $type );

		if ( false !== $cached ) {
			return $cached;
		}

		$template = new WP_Query(
			array(
				'post_type'    => 'jvbpd-listing-elmt',
				'meta_key'     => 'jvbpd_template_type',
				'meta_value'   => $type,
				'posts_per_page' => -1,
				'meta_type'    => 'post',
				'meta_compare' => '>=',
				'orderby'      => 'meta_value',
				'order'        => 'ASC',
				'meta_query'   => array(
					'relation' => 'OR',
					array(
						'key'     => 'jvbpd_template_type',
						'value'   => $type,
						'compare' => '==',
						'type'    => 'post',
					),
				),
			)
		);

		if ( $template->have_posts() ) {
			$posts = wp_list_pluck( $template->posts, 'ID' );
			wp_cache_set( $type, $posts );

			return $posts;
		}

		return '';
	}

	public function elementor_core_template( $template ) {
		global $page, $pages;
		$post = get_queried_object();

		// Count, offset -1 error fix
		if($post instanceof \WP_Post) {
			$page = is_numeric($page) ? max(1, $page) : 1;
			$pages = $pages ? $pages : Array($post->post_content);
		}

		$template_type = '';
		if( $post instanceof WP_Post ) {
			if( $post->post_type == 'jvbpd-listing-elmt' ) {
				$template_type = get_post_meta( $post->ID, 'jvbpd_template_type', true );
				switch( $template_type ) {
					case 'custom_header' : $template = jvbpdCore()->elementor_path . '/preview/template-header.php'; break;
					case 'single_product_page' :
					case 'single_listing_page' : $template = jvbpdCore()->elementor_path . '/preview/template-single.php'; break;
					// case 'listing_archive' : $template = jvbpdCore()->template_path . '/template-map.php'; break;
					case 'custom_login' :
					case 'custom_signup' : $template = jvbpdCore()->elementor_path . '/preview/template-login.php'; break;
				}
			}elseif( $post->post_type == 'post' ) {
				$template = jvbpdCore()->template_path . '/single-post.php';
			}elseif( $post->post_type == 'lv_ticket' ) {
				$template = jvbpdCore()->template_path . '/single-ticket.php';
			}
		}
		return apply_filters('jvbpd_core/page_builder/single-template', $template, $post, $template_type);
	}

	public function load_custom_header( $header_id, $queried ) {
		$defaultHeaderID = jvbpd_tso()->get( 'elementor_header', 0 );
		$themeSettingID = $singleSettingID = 0;

		if( $queried instanceof WP_Post ) {
			$themeSettingID = jvbpd_tso()->get( sprintf( 'elementor_header_%s', $queried->post_type ), 0 );
			$singleSettingID = intVal( get_post_meta( $queried->ID, 'elementor_header_id', true ) );
		}elseif( $queried instanceof WP_Term ) {
			$taxonomy = get_taxonomy( $queried->taxonomy );
			$themeSettingID = jvbpd_tso()->get( sprintf( 'elementor_header_%s_archive', current( $taxonomy->object_type ) ), 0 );
		}

		if( function_exists( 'jvbpd_bp' ) && jvbpd_bp()->is_bp_page( Array( 'profile' ) ) ) {
			$themeSettingID = jvbpd_tso()->get( 'elementor_header_member', 0 );
		}

		if( function_exists( 'jvbpd_bp' ) && jvbpd_bp()->is_bp_page( Array( 'profile' ) ) && get_current_user_id() == bp_displayed_user_id() ) {
			$themeSettingID = jvbpd_tso()->get( 'elementor_header_profile', 0 );
		}

		if( function_exists( 'jvbpd_bp' ) && jvbpd_bp()->is_bp_page( Array( 'group' ) ) ) {
			$themeSettingID = jvbpd_tso()->get( 'elementor_header_group', 0 );
		}

		if( 0 !== $defaultHeaderID ) {
			if( false !== get_post_status( $defaultHeaderID ) ) {
				$header_id = $defaultHeaderID;
			}
		}

		if( 0 !== $themeSettingID ) {
			if( false !== get_post_status( $themeSettingID ) ) {
				$header_id = $themeSettingID;
			}
		}

		if( 0 !== $singleSettingID ) {
			if( false !== get_post_status( $singleSettingID ) ) {
				$header_id = $singleSettingID;
			}elseif(-1 === $singleSettingID) {
				$header_id = $singleSettingID;
			}
		}

		return $header_id;
	}

	public function load_custom_footer( $footer_id, $post ) {
		if( !$post ) {
			return $footer_id;
		}
		$defaultFooterID = jvbpd_tso()->get( 'elementor_footer', 0 );
		$themeSettingID = jvbpd_tso()->get( sprintf( 'elementor_footer_' . $post->post_type ), 0 );
		$singleSettingID = intVal( get_post_meta( $post->ID, 'elementor_footer_id', true ) );

		if( 0 !== $defaultFooterID ) {
			if( false !== get_post_status( $defaultFooterID ) ) {
				$footer_id = $defaultFooterID;
			}
		}

		if( 0 !== $themeSettingID ) {
			if( false !== get_post_status( $themeSettingID ) ) {
				$footer_id = $themeSettingID;
			}
		}

		if( 0 !== $singleSettingID ) {
			if( false !== get_post_status( $singleSettingID ) ) {
				$footer_id = $singleSettingID;
			}elseif(-1 === $singleSettingID) {
				$footer_id = $singleSettingID;
			}
		}
		return $footer_id;
	}

	public function load_custom_login( $login_id ) {
		$defaultLoginID = jvbpd_tso()->get( 'login_template', 0 );
		if( 0 !== $defaultLoginID ) {
			if( false !== get_post_status( $defaultLoginID ) ) {
				$login_id = $defaultLoginID;
			}
		}
		return $login_id;
	}

	public function load_custom_signuo( $signup_id ) {
		$defaultSignUpID = jvbpd_tso()->get( 'login_template', 0 );
		if( 0 !== $defaultSignUpID ) {
			if( false !== get_post_status( $defaultSignUpID ) ) {
				$signup_id = $defaultSignUpID;
			}
		}
		return $signup_id;
	}

	public function load_custom_single_listing( $single_id, $post ) {
		$themeSettingID = jvbpd_tso()->get( sprintf( 'single_%s_template', $post->post_type ), 0 );
		$singleSettingID = intVal( get_post_meta( $post->ID, 'elementor_single_lv_listing_id', true ) );

		if( 0 !== $themeSettingID ) {
			if( false !== get_post_status( $themeSettingID ) ) {
				$single_id = $themeSettingID;
			}
		}

		if( 0 !== $singleSettingID ) {
			if( false !== get_post_status( $singleSettingID ) ) {
				$single_id = $singleSettingID;
			}elseif(-1 === $singleSettingID) {
				$single_id = $singleSettingID;
			}
		}
		return $single_id;
	}

    public function load_custom_single_post( $single_id, $post ) {
        $themeSettingID = jvbpd_tso()->get( sprintf( 'single_%s_template', $post->post_type ), 0 );
        $singleSettingID = intVal( get_post_meta( $post->ID, 'elementor_single_post_id', true ) );

        if( 0 !== $themeSettingID ) {
            if( false !== get_post_status( $themeSettingID ) ) {
                $single_id = $themeSettingID;
            }
        }

        if( 0 !== $singleSettingID ) {
            if( false !== get_post_status( $singleSettingID ) ) {
                $single_id = $singleSettingID;
            }elseif(-1 === $singleSettingID) {
				$single_id = $singleSettingID;
			}
        }
        return $single_id;
	}

	public function load_custom_single_product( $single_id, $post ) {
        $themeSettingID = jvbpd_tso()->get( sprintf( 'single_%s_template', $post->post_type ), 0 );
		$singleSettingID = intVal( get_post_meta( $post->ID, 'elementor_single_product_id', true ) );

        if( 0 !== $themeSettingID ) {
            if( false !== get_post_status( $themeSettingID ) ) {
                $single_id = $themeSettingID;
            }
        }

        if( 0 !== $singleSettingID ) {
            if( false !== get_post_status( $singleSettingID ) ) {
                $single_id = $singleSettingID;
            }elseif(-1 === $singleSettingID) {
				$single_id = $singleSettingID;
			}
		}
        return $single_id;
	}

	public function load_custom_archive_listing( $archive_id, WP_Term $term ) {
		$themeSettingID = jvbpd_tso()->get( sprintf( 'archive_%s_template', 'lv_listing' ), 0 );
		$archiveSettingID = intVal( get_option( 'elementor_archive_lv_listing_' . $term->term_id . '_id' ) );

		if( 0 !== $themeSettingID ) {
			if( false !== get_post_status( $themeSettingID ) ) {
				$archive_id = $themeSettingID;
			}
		}

		if( 0 !== $archiveSettingID ) {
			if( false !== get_post_status( $archiveSettingID ) ) {
				$archive_id = $archiveSettingID;
			}
		}
		return $archive_id;
	}

	public function header_custom_css( $csses=Array(), $template_id=0 ) {
		if('yes' == jvbpd_elements_tools()->is_sticky_header( $template_id )) {
			$csses[] = 'jvbpd-sticky-element';
		}
		return $csses;
	}

	public function output_member_modal(){
		?>
		<div class="modal fade login-type2" id="login_panel" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog"><div class="modal-content no-padding"></div></div>
		</div>
		<div class="modal fade" id="register_panel" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog"><div class="modal-content no-padding"></div></div>
		</div>
		<?php
	}

	public function output_get_direction_modal(){
		if(!function_exists('lava_directory_direction')) {
			return;
		}
		if(!is_singular('lv_listing')){
			return;
		} ?>
		 <div id="single-title-line-modal-get-dir" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php lava_directory_direction()->template->singleTemplate(); ?>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	public function support_bp_single() {
		wp_localize_script(
			jvbpdCore()->var_instance->getHandleName( 'jvbpd-listing-single' ),
			'jvbpd_custom_post_param ',
			apply_filters( 'jvbpd_core/single/listing/params', Array(
				'widget_sticky' => jvbpd_tso()->get( jvbpdCore()->var_instance->slug . '_single_sticky_widget' ),
				'map_type' => jvbpd_tso()->get( jvbpdCore()->var_instance->slug . '_map_width_type' ),
				'single_type' => 'lv_listing',
				'map_style' => stripslashes( htmlspecialchars_decode( jvbpd_tso()->get( 'map_style_json' ) ) ),
			), get_post() )
		);
		wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'jvbpd-listing-single' ) );
		if( 0 === get_the_ID() ) {
			$this->temp_is_singualr = $GLOBALS[ 'wp_query' ]->is_singular;
			$GLOBALS[ 'wp_query' ]->is_singular = false;
		}
	}

	public function support_bp_single_restore() {
		if( 0 === get_the_ID() ) {
			$GLOBALS[ 'wp_query' ]->is_singular = $this->temp_is_singualr;

			wp_localize_script( 'elementor-frontend', 'elementorFrontendConfig', Array(
				'isEditMode' => \Elementor\Plugin::$instance->preview->is_preview_mode(),
				'is_rtl' => is_rtl(),
				'urls' => Array( 'assets' => ELEMENTOR_ASSETS_URL ),
				'settings' => Array( 'general' => Array() )
			) );
		}
	}

	public function custom_module_attribute( \Elementor\Element_Base $widget ) {
		if( in_array( $widget->get_name(), Array( 'jvbpd-module-card', 'jvbpd-block-media' ) ) ) {
			$widget->add_render_attribute( '_wrapper', Array(
				'class' => 'jvbpd-module',
				'data-post-id' => '{post_id}',
			) );
		}
	}

	public function switch_to_preview_query() {
		$current_post_id = get_the_ID();
		$document = self::$elementor_instance->documents->get_doc_or_auto_save( $current_post_id );
		self::$elementor_instance->db->switch_to_query( Array(
			'p' => 1,
			'post_type' => 'post',
		) );
	}

	public function restore_current_query() {
		self::$elementor_instance->db->restore_current_query();
	}

	public function loadElementorHeader( $name=null ) {
		$headerID = self::get_settings( 'custom_header', '' );
		if( $headerID === '' ) {
			$name = 'none';
		}else{
			$name = 'elementor';
		}
		return $name;
	}

	public function header_alert() {
		$alertMessage = sprintf( esc_html__( "You haven't created any headers. Please create one. ( Dashboard >> %s >> Page builder )", 'jvfrmtd' ), jvbpdCore()->template );
		printf( '<div class="container"><div class="alert alert-link">%1$s</div></div>', $alertMessage );
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function sticky_section($element, $section_id, $args) {
		if('section'!= $element->get_name() ) {
			return;
		}
		if('section_background' != $section_id) {
			return;
		}
		$element->start_controls_section( 'jvbpd_sticky_section', Array(
			'label' => esc_html__( 'Javo Sticky', 'jvfrmtd' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		));
			$element->add_control( 'jvbpd_sticky', Array(
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => esc_html__( "Use Javo Sticky", 'jvfrmtd' ),
			) );
			$element->add_control( 'jvbpd_sticky_zIndex', Array(
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( "Sticky Z-Index", 'jvfrmtd' ),
				'default' => '1000',
				'frontend_available' => true,
			) );

			$element->add_control( 'jvbpd_sticky_spacing', Array(
				'type' => \Elementor\Controls_Manager::SLIDER,
				'label' => esc_html__( "Sticky Section Top & Bottom Space", 'jvfrmtd' ),
				'default' => Array(
					'size' => 10,
					'unit' => 'px',
				),
				'range' => Array(
					'px' => Array(
						'min' => 0,
						'max' => 30,
						'step' => 1,
					),
				),
				'size_units' => Array( 'px' ),
				'condition' => Array(
					'jvbpd_sticky' => 'yes'
				),
				'selectors' => Array(
					'div.is-sticky>.elementor-element.elementor-element-{{ID}}' => 'padding-top:{{SIZE}}{{UNIT}};padding-bottom:{{SIZE}}{{UNIT}};',
				)
			) );

			$element->add_control( 'jvbpd_sticky_offset', Array(
				'type' => \Elementor\Controls_Manager::SLIDER,
				'label' => esc_html__( "Sticky Offset", 'jvfrmtd' ),
				'default' => Array(
					'size' => 0,
					'unit' => 'px',
				),
				'range' => Array(
					'px' => Array(
						'min' => 0,
						'max' => 30,
						'step' => 1,
					),
				),
				'size_units' => Array( 'px' ),
				'frontend_available' => true,
				'condition' => Array(
					'jvbpd_sticky' => 'yes'
				),
				'selectors' => Array(
					'div.is-sticky>.elementor-element.elementor-element-{{ID}}' => 'margin-top:{{SIZE}}{{UNIT}};',
				)
			) );

			/*
			$this->add_page_builder_control( 'header_background_color', 'custom_header', Array(
				'type' => \Elementor\Controls_Manager::COLOR,
				'label' => esc_html__( "Header Background Color", 'jvfrmtd' ),
				'template' => 'custom_header',
				'condition' => Array(
					'header_sticky' => 'yes'
				),
				'selectors' => Array(
					'.header-elementor.header-id-{{ID}} .header-elementor-wrap' => 'background-color:{{VALUE}};',
				)
			) ); */

			$element->add_control( 'jvbpd_sticky_background_color', Array(
				'type' => \Elementor\Controls_Manager::COLOR,
				'label' => esc_html__( "Header Sticky Background Color", 'jvfrmtd' ),
				'condition' => Array(
					'jvbpd_sticky' => 'yes'
				),
				'selectors' => Array(
					'div.is-sticky>.elementor-element.elementor-element-{{ID}}' => 'background-color:{{VALUE}};',
				)
			) );

			$element->add_control( 'jvbpd_sticky_main_menu_color', Array(
				'type' => \Elementor\Controls_Manager::COLOR,
				'label' => esc_html__( "Header Sticky Main Menu Color", 'jvfrmtd' ),
				'template' => 'custom_header',
				'condition' => Array(
					'jvbpd_sticky' => 'yes'
				),
				'selectors' => Array(
					'div.is-sticky>.elementor-element.elementor-element-{{ID}} li.main-menu-item.menu-item-depth-0 > a > span.menu-titles' =>
					'color:{{VALUE}};',
				),
			) );

		$element->end_controls_section();
	}

	public function parallax_section($element, $section_id, $args) {
		if('section'!= $element->get_name() ) {
			return;
		}
		if('section_background' != $section_id) {
			return;
		}
		$element->start_controls_section( 'jvbpd_parallax_section', Array(
			'label' => esc_html__( 'Javo Parallax', 'jvfrmtd' ),
			'tab' => \Elementor\Controls_Manager::TAB_LAYOUT,
		));
			$element->add_control('use_parallax', Array(
				'label' => esc_html__( 'Parallax', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
			));

			$element->add_control('parallax_items', Array(
				'show_label' => false,
				'type' => \Elementor\Controls_Manager::REPEATER,
				'condition' => Array('use_parallax' => 'yes'),
				'fields' => Array(
					Array(
						'name' => 'image',
						'label' => esc_html__( 'Image', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
					),
					Array(
						'name' => 'backgroundPositionX',
						'label' => esc_html__( 'Background Position X (%)', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => Array(
							'size' => '0',
							'unit' => '%',
						),
						'range' => Array(
							'%' => Array(
								'min' => 0,
								'max' => 100,
								'step' => 1,
							),
						),
						'size_units' => Array( '%' ),
					),
					Array(
						'name' => 'backgroundPositionY',
						'label' => esc_html__( 'Background Position Y (%)', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => Array(
							'size' => '0',
							'unit' => '%',
						),
						'range' => Array(
							'%' => Array(
								'min' => 0,
								'max' => 100,
								'step' => 1,
							),
						),
						'size_units' => Array( '%' ),
					),
					Array(
						'name' => 'backgroundSize',
						'label' => esc_html__( 'Background Size', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'default' => 'auto',
						'options' => Array(
							'auto' => esc_html__( 'None', 'jvfrmtd' ),
							'cover' => esc_html__( 'Cover', 'jvfrmtd' ),
							'contain' => esc_html__( 'Contain', 'jvfrmtd' ),
						),
					),
					Array(
						'name' => 'transform',
						'label' => esc_html__( 'Transition Type', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'default' => 'translate',
						'options' => Array(
							'translate' => esc_html__( 'Translate', 'jvfrmtd' ),
							'translate3d' => esc_html__( 'Translate 3D', 'jvfrmtd' ),
							'back_pos' => esc_html__( 'Background Position', 'jvfrmtd' ),
						),
					),
					Array(
						'name' => 'speed',
						'label' => esc_html__( 'Speed(%)', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => Array(
							'size' => '1',
							'unit' => '%',
						),
						'range' => Array(
							'%' => Array(
								'min' => 1,
								'max' => 200,
								'step' => 1,
							),
						),
						'size_units' => Array( '%' ),
					),
					Array(
						'name' => 'type',
						'label' => esc_html__( 'Type', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => Array(
							'' => esc_html__( 'None', 'jvfrmtd' ),
							'scroll' => esc_html__( 'Scroll', 'jvfrmtd' ),
							'mouse_move' => esc_html__( 'Mouse Move', 'jvfrmtd' ),
						),
					),
					Array(
						'name' => 'zIndex',
						'label' => esc_html__( 'z-index', 'jvfrmtd' ),
						'type' => \Elementor\Controls_Manager::TEXT,
					),
				),
			));
		$element->end_controls_section();

	}

	public function sticky_render($section) {
		if('yes' == $section->get_settings('jvbpd_sticky')) {
			$section->add_render_attribute('_wrapper', 'class', 'jvbpd-sticky-element');
		}
	}

	public function parallax_render($section) {
		if('section' != $section->get_name() ) {
			return;
		}
		if('yes' != $section->get_settings('use_parallax')) {
			return;
		}
		$layouts = $section->get_settings('parallax_items');
		if(!empty($layouts) && is_array($layouts)) {
			$this->parallax_items[$section->get_id()] = $layouts;
		}
	}

	public function parallax_param() {
		wp_localize_script(
			jvbpdCore()->var_instance->getHandleName( 'frontend' ),
			'parallax_section_data', $this->parallax_items
		);
	}

	public function add_icon_library($icons=Array()) {
		$icons['jvbpd-icon1'] = Array(
			'name' => 'jvbpd-icon1',
			'label' => esc_html__("Javo Icon 1", 'jvfrmtd'),
			'url' => get_template_directory_uri() . '/assets/dist/css/icons.css',
			//'url' => '',
			'enqueue' => '',
			'prefix' => 'jvic-',
			'displayPrefix' => 'fab',
			'labelIcon' => 'fab fa-font-awesome-flag',
			'ver' => '1.0.0',
			'fetchJson' => jvbpdCore()->assets_url .'/json/jvbpd-icon1.json',
			'native' => true,
		);

		$icons['jvbpd-icon2'] = Array(
			'name' => 'jvbpd-icon2',
			'label' => esc_html__("Javo Icon 2", 'jvfrmtd'),
			'url' => get_template_directory_uri() . '/assets/dist/css/icons.css',
			//'url' => '',
			'enqueue' => '',
			'prefix' => 'jvic-',
			'displayPrefix' => 'fab',
			'labelIcon' => 'fab fa-font-awesome-flag',
			'ver' => '1.0.0',
			'fetchJson' => jvbpdCore()->assets_url .'/json/jvic-icon.json',
			'native' => true,
		);
		return $icons;
	}

}