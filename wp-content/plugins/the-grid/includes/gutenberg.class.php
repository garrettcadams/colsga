<?php
/**
 * @package   The_Grid
 * @author    Themeone <themeone.master@gmail.com>
 * @copyright 2015 Themeone
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Gutenberg block
 *
 * @class WP_Grid_Builder\Admin\Gutenberg
 * @since 1.0.0
 */
class The_Grid_Gutenberg {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'rest_api_init', array( $this, 'routes' ) );
		add_filter( 'block_categories', array( $this, 'block_category' ), 10, 2 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );

	}

	/**
	 * Add custom routes to WP Rest API
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function routes() {

		register_rest_route(
			'the_grid/v1',
			'/get/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get' ),
				'permission_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'type' => array(
						'type'     => 'string',
						'required' => true,
					),
				),
			)
		);

	}

	/**
	 * Get Grids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Rest route parameters (required).
	 * @return array.
	 */
	public function get( $data ) {

		if ( 'grids' !== $data['type'] ) {
			return array();
		}

		$args = array(
			'post_type'      => 'the_grid',
			'post_status'    => 'any',
			'posts_per_page' => -1,
		);

		$grids = get_posts( $args );

		$objects = array_map(
			function( $object ) {
				return array(
					'value' => $object->post_title,
					'label' => $object->post_title,
				);
			},
			(array) $grids
		);

		// Add placeholder.
		array_unshift(
			$objects,
			array(
				'label' => esc_html__( 'None', 'wpgb-text-domain' ),
				'value' => '',
			)
		);

		return $objects;

	}

	/**
	 * Register The Grid block
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_block() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'the-grid/grid',
			array(
				'editor_script'   => TG_SLUG . '-editor',
				'render_callback' => array( $this, 'render_block' ),
				'attributes'      => array(
					'className' => array(
						'type'    => 'string',
						'default' => '',
					),
					'align'     => array(
						'type'    => 'string',
						'default' => 'none',
					),
					'name'        => array(
						'type'    => 'string',
						'default' => '',
					),
				),
			)
		);

	}

	/**
	 * Add custom category for blocks
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $categories Holds Gutenberg categories.
	 * @param object $post Holds post object.
	 * @return array
	 */
	public function block_category( $categories, $post ) {

		return array_merge(
			$categories,
			array(
				array(
					'slug'  => TG_SLUG,
					'title' => 'The Grid',
				),
			)
		);

	}

	/**
	 * Enqueue Gutenberg block assets
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function editor_assets() {

		wp_enqueue_script(
			TG_SLUG . '-editor',
			TG_PLUGIN_URL . 'backend/assets/js/gutenberg.js',
			array( 'wp-api-fetch', 'wp-blocks', 'wp-components', 'wp-data', 'wp-editor', 'wp-element', 'wp-i18n', 'wp-url' ),
			TG_VERSION
		);

		$this->i18n_register();

	}


	/**
	 * Registers the i18n script
	 *
	 * @since 1.0.0
	 */
	public function i18n_register() {

		if ( function_exists( 'wp_set_script_translations' ) ) {

			wp_set_script_translations( TG_SLUG . '-editor', 'tg-text-domain' );
			return;

		}

		if ( function_exists( 'gutenberg_get_jed_locale_data' ) ) {

			// Get translations.
			$locale = gutenberg_get_jed_locale_data( 'tg-text-domain' );
			// Add translations to wp.i18n.setLocaleData.
			$content = 'wp.i18n.setLocaleData(' . wp_json_encode( $locale ) . ', "tg-text-domain" );';
			// Add inline script before 'gcb-editor-js' script.
			wp_script_add_data( TG_SLUG . '-editor', 'data', $content );

		}

	}

	/**
	 * Render grid block
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $attributes Holds block attributes.
	 */
	public function render_block( $attributes ) {

		if ( is_admin() ) {
			return;
		}

		$class = array(
			'align' . $attributes['align'],
			$attributes['className'],
		);

		$class = array_map( 'sanitize_html_class', $class );
		$class = array_filter( $class );
		$class = implode( ' ', $class );

		$output = '<div class="' . esc_attr( $class ) . '">';
			$output .= The_Grid( $attributes['name'] );
		$output .= '</div>';

		// Remove HTML comments to prevent wrapping </p> tags (TinyMCE behaviour).
		$output = preg_replace( '/<!--(.|s)*?-->/', '', $output );

		return $output;

	}
}


new The_Grid_Gutenberg();
