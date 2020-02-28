<?php

defined( 'ABSPATH' ) or exit;

class Jvbpd_Listing_Elementor_Admin {

	Const PAGE_BUILDER_SLUG = 'jvbpd-listing-elmt';

	private static $_instance = null;

	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'header_footer_posttype' ), 15 );

		// Disabled SEO
		add_filter('wpseo_accessible_post_types', array($this, 'remove_seo_page_builder'));

		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 100 );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );
		// add_action( 'admin_notices', array( $this, 'location_notice' ) );
		add_action( 'template_redirect', array( $this, 'block_template_frontend' ) );
		add_filter( 'single_template', array( $this, 'load_canvas_template' ) );
		add_action( 'manage_' . self::PAGE_BUILDER_SLUG . '_posts_custom_column', Array( $this, 'pageBuilderCustomColumn' ), 10, 2 );
		// add_filter( 'manage_' . self::PAGE_BUILDER_SLUG . '_posts_columns', Array( $this, 'setPageBuilderColumn' ) );

		$this->pageBuilderTabs();
	}

	/**
	 * Register Post type for header footer templates
	 */
	public function header_footer_posttype() {
		$labels = array(
			'name'               => __( 'Page builder', 'jvfrmtd' ),
			'singular_name'      => __( 'Page builder', 'jvfrmtd' ),
			'menu_name'          => __( 'Page builder', 'jvfrmtd' ),
			'name_admin_bar'     => __( 'Elementor Header Footer', 'jvfrmtd' ),
			'add_new'            => __( 'Add New', 'jvfrmtd' ),
			'add_new_item'       => __( 'Add New Page Builder', 'jvfrmtd' ),
			'new_item'           => __( 'New Page builder', 'jvfrmtd' ),
			'edit_item'          => __( 'Edit Page builder', 'jvfrmtd' ),
			'view_item'          => __( 'View Page builder', 'jvfrmtd' ),
			'all_items'          => __( 'All Page builder', 'jvfrmtd' ),
			'search_items'       => __( 'Search Page builder', 'jvfrmtd' ),
			'parent_item_colon'  => __( 'Parent Page builder:', 'jvfrmtd' ),
			'not_found'          => __( 'No Page builder found.', 'jvfrmtd' ),
			'not_found_in_trash' => __( 'No Page builder found in Trash.', 'jvfrmtd' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'rewrite'             => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-editor-kitchensink',
			'supports'            => array( 'title', 'thumbnail', 'elementor' ),
		);

		register_post_type( self::PAGE_BUILDER_SLUG, $args );
	}

	public function remove_seo_page_builder($post_types=Array()) {
		return array_diff($post_types, Array(self::PAGE_BUILDER_SLUG));
	}

	public function register_admin_menu() {
		add_submenu_page(
			'jvbpd_admin',
			__( 'Page Builder', 'jvfrmtd' ),
			__( 'Page Builder', 'jvfrmtd' ),
			'edit_pages',
			'edit.php?post_type=' . self::PAGE_BUILDER_SLUG
		);
	}

	/**
	 * Register meta box(es).
	 */
	function register_metabox() {
		add_meta_box( 'ehf-meta-box', __( 'Builder option', 'jvfrmtd' ), array( $this, 'metabox_render', ), self::PAGE_BUILDER_SLUG, 'normal', 'high' );
	}

	public function getTemplateTypes() {
		$TemplateTypes= Array(
			'single_post_page' => esc_html__( "Post Detail", 'jvfrmtd' ),
			'single_listing_page' => esc_html__( "Listing Detail", 'jvfrmtd' ),
			'post_archive' => esc_html__( "Post Archive", 'jvfrmtd' ),
			'listing_archive' => esc_html__( "Listing Archive", 'jvfrmtd' ),
			'custom_header' => esc_html__( "Header", 'jvfrmtd' ),
			'custom_footer' => esc_html__( "Footer", 'jvfrmtd' ),
			'custom_login' => esc_html__( "Login", 'jvfrmtd' ),
			'custom_signup' => esc_html__( "Sign-up", 'jvfrmtd' ),
			'custom_module' => esc_html__( "Module", 'jvfrmtd' ),
			'custom_bp_module' => esc_html__( "Buddypress Module", 'jvfrmtd' ),
			'custom_preview' => esc_html__( "Preview", 'jvfrmtd' ),
			'canvas' => esc_html__( "Canvas", 'jvfrmtd' ),
		);

		/**
		 * Check if the plugin is active
		 **/
		if (in_array('lava-ticket/lava-ticket.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$TemplateTypes['single_ticket_page'] = esc_html__( "Ticket Detail", 'jvfrmtd' );
		}
		return apply_filters('jvbpd_core/page_builder/templates', $TemplateTypes );
	}

	/**
	 * Render Meta field.
	 *
	 * @param  POST $post Currennt post object which is being displayed.
	 */
	function metabox_render( $post ) {
		$values   = get_post_custom( $post->ID );
		$selected = isset( $values['jvbpd_template_type'] ) ? esc_attr( $values['jvbpd_template_type'][0] ) : '';
		// We'll use this nonce field later on when saving.
		wp_nonce_field( 'jvbpd_meta_nounce', 'jvbpd_meta_nounce' );
		?>
		<p>
			<label for="jvbpd_template_type"><?php esc_html_e( "Select the type of page or parts", 'jvfrmtd' ); ?></label>
			<select name="jvbpd_template_type" id="jvbpd_template_type">
				<option value="" <?php selected( $selected, '' ); ?>><?php esc_html_e( "Select Option", 'jvfrmtd' ); ?></option>
				<?php
				foreach( $this->getTemplateTypes() as $template_id => $template_name ) {
					printf( '<option value="%1$s"%2$s>%3$s</option>', $template_id, selected( $template_id == $selected, true, false ), $template_name );
				} ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Save meta field.
	 *
	 * @param  POST $post_id Currennt post object which is being displayed.
	 *
	 * @return Void
	 */
	public function save_meta( $post_id ) {

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['jvbpd_meta_nounce'] ) || ! wp_verify_nonce( $_POST['jvbpd_meta_nounce'], 'jvbpd_meta_nounce' ) ) {
			return;
		}

		if ( isset( $_POST['jvbpd_template_type'] ) ) {
			update_post_meta( $post_id, 'jvbpd_template_type', esc_attr( $_POST['jvbpd_template_type'] ) );
		}

	}

	/**
	 * Display notice when editing the header or footer when there is one more of similar layout is active on the site.
	 *
	 * @since 1.0.0
	 */
	public function location_notice() {

		global $pagenow;
		global $post;

		if ( 'post.php' != $pagenow || ! is_object( $post ) || 'jvbpd-listing-elmt' != $post->post_type ) {
			return;
		}

		$template_type = get_post_meta( $post->ID, 'jvbpd_template_type', true );

		if ( '' !== $template_type ) {
			$templates = Jvbpd_Listing_Elementor::get_template_id( $template_type );

			// Check if more than one template is selected for current template type.
			if ( is_array( $templates ) && isset( $templates[1] ) && $post->ID != $templates[0] ) {

				$post_title        = '<strong>' . get_the_title( $templates[0] ) . '</strong>';
				$template_location = '<strong>' . $this->template_location( $template_type ) . '</strong>';
				/* Translators: Post title, Template Location */
				$message = sprintf( __( 'Template %1$s is already assigned to the location %2$s', 'jvfrmtd' ), $post_title, $template_location );

				echo '<div class="error"><p>';
				echo $message;
				echo '</p></div>';
			}
		}

	}

	/**
	 * Convert the Template name to be added in the notice.
	 *
	 * @since  1.0.0
	 *
	 * @param  String $template_type Template type name.
	 *
	 * @return String $template_type Template type name.
	 */
	public function template_location( $template_type ) {
		$template_type = ucfirst( str_replace( 'type_', '', $template_type ) );

		return $template_type;
	}

	/**
	 * Don't display the elementor header footer templates on the frontend for non edit_posts capable users.
	 *
	 * @since  1.0.0
	 */
	public function block_template_frontend() {
		if ( is_singular( 'jvbpd-listing-elmt' ) && ! current_user_can( 'edit_posts' ) ) {
			wp_redirect( site_url(), 301 );
			die;
		}
	}

	/**
	 * Single template function which will choose our template
	 *
	 * @since  1.0.1
	 *
	 * @param  String $single_template Single template.
	 */
	function load_canvas_template( $single_template ) {
		global $post;

		if ( in_array( $post->post_type, Array( 'lv_listing', 'jvbpd-listing-elmt' ) ) ) {
			if( version_compare( ELEMENTOR_VERSION, '1.9.9', '<' ) ) {
				return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
			}else{
				return ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';
			}
		}
		return $single_template;
	}

	public function custom_nav_mymenu( $is_not_admin=true ) {
		return false;
	}

	public function pageBuilderTabs() {
		add_filter( 'query_vars', array( $this, 'addBuildPageQueryVariable' ) );
		add_filter( 'parse_query', array( $this, 'pagebuiderFilter' ) );
		add_action( 'views_edit-' . self::PAGE_BUILDER_SLUG, Array( $this, 'builderHeader' ) );

		add_filter( 'manage_' . self::PAGE_BUILDER_SLUG . '_posts_columns', Array( $this, 'pageBuildColumns' ) );
		add_action( 'manage_' . self::PAGE_BUILDER_SLUG . '_posts_custom_column', Array( $this, 'pageBuildColumnContent' ), 10, 2 );
	}

	public function addBuildPageQueryVariable( $vars=Array() ) {
		return wp_parse_args( Array( 'template' ), $vars );
	}

	public function pagebuiderFilter( $query ) {
		if( function_exists( 'get_current_screen' ) ) {
			if(
				!empty( get_current_screen()->post_type ) &&
				get_current_screen()->post_type == self::PAGE_BUILDER_SLUG &&
				$query->get( 'post_type' ) == self::PAGE_BUILDER_SLUG
			) {
				$query->query_vars[ 'meta_key' ] = 'jvbpd_template_type';
				$query->query_vars[ 'meta_value' ] = get_query_var( 'template', '' );
			}

		}
	}

	public function builderHeader() {

		$tabs = $this->getTemplateTypes();
		$output = sprintf( '<div id="%1$s-tab" class="%2$s">', self::PAGE_BUILDER_SLUG, 'nav-tab-wrapper' );
		$tabLink = add_query_arg( Array(
			'post_type' => self::PAGE_BUILDER_SLUG,
			'post_status' => 'trash',
		), admin_url( 'edit.php' ) );
		$output .= sprintf(
			'<a href="%1$s" title="%2$s" class="nav-tab%3$s">%2$s</a>',
			esc_url( $tabLink ), esc_attr__("Trash", 'jvfrmtd'), ( 'trash' == get_query_var( 'post_status', '' ) ? ' nav-tab-active' : '' )
		);
			foreach( wp_parse_args( $this->getTemplateTypes(), Array( '' => esc_html__( "All", 'jvfrmtd' ) ) ) as $tabSlug => $tabLabel ) {
				$currentTab = get_query_var( 'template', '' ) == $tabSlug && 'trash' != get_query_var( 'post_status', '' );

				$tabLink = add_query_arg( Array(
					'post_type' => self::PAGE_BUILDER_SLUG,
					'template' => $tabSlug,
				), admin_url( 'edit.php' ) );
				$output .= sprintf(
					'<a href="%1$s" title="%2$s" class="nav-tab%3$s">%2$s</a>',
					esc_url( $tabLink ), $tabLabel, ( $currentTab ? ' nav-tab-active' : '' ), $tabSlug
				);
			}
		$output .= '</div>';
		echo $output;
	}

	public function pageBuildColumns( $columns=Array() ) {
		unset( $columns['date'] );
		return array_merge( $columns, Array(
			'template' => esc_html__( "Template", 'jvfrmtd' ),
			'shortcode' => esc_html__( "Shortcode", 'jvfrmtd' ),
			'date' => esc_html__( "Date", 'jvfrmtd' ),
		) );
	}

	public function pageBuildColumnContent( $column, $post_id=0 ) {
		if( 'template' == $column ) {
			$template = false;
			$templateType = get_post_meta( $post_id, 'jvbpd_template_type', true );
			$allTemplateTypes = $this->getTemplateTypes();
			if( array_key_exists( $templateType, $allTemplateTypes ) ) {
				$tabLink = add_query_arg( Array(
					'post_type' => self::PAGE_BUILDER_SLUG,
					'template' => $templateType,
				), admin_url( 'edit.php' ) );
				$template = sprintf( '<a href="%1$s">%2$s</a>', $tabLink, $allTemplateTypes[$templateType] );
			}
			echo $template;
		}
		if( 'shortcode' == $column ) {
			printf( '<input type="text" value="[jve_template id=%1$s]" onfocus="this.select();" class="large-text" readonly="readonly">', $post_id );
		}
	}

	public function setPageBuilderColumn($columns=Array()){
		return array_merge(Array(
			'cb' => '',
			'featured' => '',
		), $columns);
	}

	public function pageBuilderCustomColumn($column, $post_id) {
		switch($column) {
			case 'featured':
				echo get_the_post_thumbnail($post_id);
				break;
		}
	}

}


if( class_exists( 'Jvbpd_Listing_Elementor_Admin' ) ) {
	Jvbpd_Listing_Elementor_Admin::instance();
}