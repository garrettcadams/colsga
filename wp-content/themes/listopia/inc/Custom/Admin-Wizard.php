<?php
use Awps\Custom\Admin;
use Awps\Api\Settings;
class jvbpd_init_helper {

	public $settings;

	public static $instance = null;

	public $tgmpa_menu_slug = 'tgmpa-install-plugins';
	public $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

	public function __construct() {
		$this->settings = new Settings();
		$this->settings->loadPlugins();
		$this->registerHooks();
	}

	public function registerHooks() {
		add_action( 'admin_menu', array( $this, 'regieter_menu' ) );
		add_action( 'admin_init', array( $this, 'helper_content' ) );
		add_action( 'init', array( $this, 'initialize' ),11  );
		add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ) );
		add_filter( 'tgmpa_register', array( $this, 'tgmpa_register' ) );

	}

	public function initialize() {
		add_action( 'wp_ajax_jvbpd_wizard_plugins', array( $this, 'ajax_plugins' ) );
		add_action( 'wp_ajax_jvbpd_wizard_addons', array( $this, 'ajax_addons' ) );
	}

	public function tgmpa_load( $state ) {
		return is_admin();
	}

	public function tgmpa_register( $state ) {
		if( isset( $_POST[ 'helper' ] ) && $_POST[ 'helper' ] == 'yes' ) {
			$this->_get_addons();
		}
	}

	public function regieter_menu() {
		add_theme_page( 'Install Wizard', 'Install Wizard', 'manage_options', 'jvbpd-core', '__return_false' );
	}

	public function step_sort( $param1=Array(), $param2=Array() ) {
		return $param1[ 'priority' ] < $param2[ 'priority' ] ? -1 : 1;
	}

	public function helper_content() {

		if ( empty( $_GET['page'] ) || 'jvbpd-core' !== $_GET['page'] ) {
			return;
		}

		remove_all_actions( 'jvbpd_admin_helper_page_header' );
		remove_all_actions( 'jvbpd_admin_helper_page_footer' );

		$default_steps = array(
			'introduction' => array(
				'name'    => esc_html__( 'Introduction', 'jvbpd' ),
				'view'    => array( $this, 'wc_setup_introduction' ),
				'handler' => '',
				'priority' => 1,
			),
			'plugins' => array(
				'name'    => esc_html__( 'Plugins', 'jvbpd' ),
				'view'    => array( $this, 'plugins_page' ),
				'handler' => '',
				'priority' => 10,
			),
			'addons' => array(
				'name'    => esc_html__( 'Addons', 'jvbpd' ),
				'view'    => array( $this, 'addons_page' ),
				'handler' => '',
				'priority' => 20,
			),
			'status' => array(
				'name'    => esc_html__( 'Server Status', 'jvbpd' ),
				'view'    => array( $this, 'status_page' ),
				'handler' => array( $this, 'wc_setup_location_save' ),
				'priority' => 30,
			),
			'demo_import' => array(
				'name'    => esc_html__( 'Demo import', 'jvbpd' ),
				'view'    => array( $this, 'import_page' ),
				'handler' => '',
				'priority' => 40,
			),
			'next_steps' => array(
				'name'    => esc_html__( 'Ready!', 'jvbpd' ),
				'view'    => array( $this, 'finish_page' ),
				'handler' => '',
				'priority' => 50,
			),
		);

		if( defined('JVBPD_IW_SKIP_ADDONS') && JVBPD_IW_SKIP_ADDONS ) {
			unset( $default_steps['addons'] );
		}

		uasort( $default_steps, array( $this, 'step_sort' ) );

		if ( ! current_user_can( 'install_themes' ) || ! current_user_can( 'switch_themes' ) || is_multisite() || current_theme_supports( 'jvbpd' ) ) {
			unset( $default_steps['theme'] );
		}

		$this->steps = apply_filters( 'jvbpd_wizard_steps', $default_steps );
		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'jvbpd-admin', JVBPD_THEME_DIR . '/assets/dist/css/admin.css', array( 'dashicons', 'install', 'themes' ) );
		// wp_enqueue_style( 'jvbpd-admin-meta', JVBPD_THEME_DIR . '/assets/css/javo_admin_post_meta.css', array( 'jvbpd-admin' ) );

		wp_register_script( 'jvbpd-wizard', JVBPD_THEME_DIR . '/assets/dist/js/backend.js', array( 'jquery' ) );
		wp_localize_script(
			'jvbpd-wizard',
			'jvbpd_wizard_param',
			Array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'verify_text' => ' ' . esc_html__( "Verifying...", 'jvbpd' ),
			)
		);

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;

	}

	public function _get_plugins() {
		$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$plugins  = array(
			'all'      => array(), // Meaning: all plugins which still have open actions.
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),
		);

		foreach ( $instance->plugins as $plugin ) {
			if ( $instance->is_plugin_active( $plugin[ 'slug' ] ) && false === $instance->does_plugin_have_update( $plugin[ 'slug' ] ) ) {
				// No need to display plugins if they are installed, up-to-date and active.
				continue;
			} else {
				$plugins['all'][ $plugin[ 'slug' ] ] = $plugin;
				if ( ! $instance->is_plugin_installed( $plugin[ 'slug' ] ) ) {
					$plugins['install'][ $plugin[ 'slug' ] ] = $plugin;
				} else {
					if ( false !== $instance->does_plugin_have_update( $plugin[ 'slug' ] ) ) {
						$plugins['update'][ $plugin[ 'slug' ] ] = $plugin;
					}

					if ( $instance->can_plugin_activate( $plugin[ 'slug' ] ) ) {
						$plugins['activate'][ $plugin[ 'slug' ] ] = $plugin;
					}
				}
			}
		}
		return $plugins;
	}

	public function _get_addons() {
		$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$plugins  = array(
			'all'      => array(), // Meaning: all plugins which still have open actions.
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),
		);

		$addons = apply_filters( 'jvbpd_wizard_addons', Array() );

		if( is_array( $addons ) ) {
			foreach ( $addons as $addon ) {
				call_user_func( array( $instance, 'register' ), $addon );
			}
		}

		foreach ( $addons as $plugin ) {
			if ( $instance->is_plugin_active( $plugin[ 'slug' ] ) && false === $instance->does_plugin_have_update( $plugin[ 'slug' ] ) ) {
				// No need to display plugins if they are installed, up-to-date and active.
				continue;
			} else {
				$plugins['all'][ $plugin[ 'slug' ] ] = $plugin;
				if ( ! $instance->is_plugin_installed( $plugin[ 'slug' ] ) ) {
					$plugins['install'][ $plugin[ 'slug' ] ] = $plugin;
				} else {
					if ( false !== $instance->does_plugin_have_update( $plugin[ 'slug' ] ) ) {
						$plugins['update'][ $plugin[ 'slug' ] ] = $plugin;
					}

					if ( $instance->can_plugin_activate( $plugin[ 'slug' ] ) ) {
						$plugins['activate'][ $plugin[ 'slug' ] ] = $plugin;
					}
				}
			}
		}
		return $plugins;
	}

	public function ajax_plugins() {
		$slug = isset( $_POST[ 'slug' ] ) ? $_POST[ 'slug' ] : false;
		$this->ajax_active_plugins( $slug, $this->_get_plugins() );
	}

	public function ajax_addons() {
		$slug = isset( $_POST[ 'slug' ] ) ? $_POST[ 'slug' ] : false;
		$this->ajax_active_plugins( $slug, $this->_get_addons() );
	}

	public function ajax_active_plugins( $slug, $plugins=Array() ) {
		if ( ! $slug ) {
			wp_send_json_error( array( 'error' => 1, 'message' => esc_html__( 'No Slug Found', 'jvbpd' ) ) );
		}

		$json = array();

		// what are we doing with this plugin?
		foreach ( $plugins['activate'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				if( 'javo-core' == $slug ){
					// Disable welcome page for javo-core active
					delete_transient( 'elementor_activation_redirect' );
				}
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-activate',
					'action2'       => - 1,
					'tgmpa-activate' => 'activate-plugin',
					'message'       => esc_html__( 'Activating Plugin', 'jvbpd' ),
				);
				break;
			}
		}
		foreach ( $plugins['update'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				if( 'javo-core' == $slug ){
					// Disable welcome page for javo-core active
					delete_transient( 'elementor_activation_redirect' );
				}
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-update',
					'action2'       => - 1,
					'tgmpa-update' => 'update-plugin',
					'message'       => esc_html__( 'Updating Plugin', 'jvbpd' ),
				);
				break;
			}
		}
		foreach ( $plugins['install'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				if( 'javo-core' == $slug ){
					// Disable welcome page for javo-core active
					delete_transient( 'elementor_activation_redirect' );
				}
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-install',
					'action2'       => - 1,
					'tgmpa-install' => 'install-plugin',
					'message'       => esc_html__( 'Installing Plugin', 'jvbpd' ),
				);
				break;
			}
		}

		if ( $json ) {
			$json[ 'helper' ] = 'yes';
			$json[ 'hash' ] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
			wp_send_json( $json );
		} else {
			wp_send_json( array( 'done' => 1, 'message' => esc_html__( 'Success', 'jvbpd' ) ) );
		}
		exit;

	}

	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<?php printf( '<%1$s>%2$s</%1$s>', 'title', esc_html__( 'Javo &rsaquo; Setup Wizard', 'jvbpd' ) ); ?>
			<?php
			wp_print_scripts( 'jvbpd-wizard' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="jvbpd-wizard wp-core-ui">
			<h1 id="jvbpd-logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/dist/images/jv-logo2.png' ); ?>" alt="logo2" /></h1>
		<?php
	}

	public function setup_wizard_steps() {
		$ouput_steps = $this->steps;
		array_shift( $ouput_steps );
		?>
		<ol class="jvbpd-wizard-steps">
			<?php foreach ( $ouput_steps as $step_key => $step ) : ?>
				<li class="<?php
					if ( $step_key === $this->step ) {
						echo esc_attr( 'active' );
					} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
						echo esc_attr( 'done' );
					}
				?>"><?php echo esc_html( $step['name'] ); ?></li>
			<?php endforeach; ?>
		</ol>
		<?php
	}

	public function setup_wizard_content() {
		echo '<div class="jvbpd-wizard-content">';
		call_user_func( $this->steps[ $this->step ]['view'], $this );
		echo '</div>';
	}

	public function setup_wizard_footer() {
		?>
			<?php if ( 'next_steps' === $this->step ) : ?>
				<a class="wc-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to the WordPress Dashboard', 'jvbpd' ); ?></a>
			<?php endif; ?>
			</body>
		</html>
		<?php
		do_action( 'jvbpd_wizard_footer', $this );
	}

	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys );
		if ( false === $step_index ) {
			return '';
		}

		return add_query_arg( 'step', $keys[ $step_index + 1 ] );
	}

	public function wc_setup_introduction() {
		Admin::$instance->load_template( Array( 'name' => 'intro', 'sub' => 'admin-wizard' ), Array( 'helper' => &$this ) );
	}

	public function plugins_page() {
		Admin::$instance->load_template( Array( 'name' => 'plugins', 'sub' => 'admin-wizard' ), Array( 'helper' => &$this ) );
	}

	public function addons_page() {
		Admin::$instance->load_template( Array( 'name' => 'addons', 'sub' => 'admin-wizard' ), Array( 'helper' => &$this ) );
	}

	public function status_page() {
		Admin::$instance->load_template( Array( 'name' => 'status', 'sub' => 'admin-wizard' ), Array( 'helper' => &$this ) );
	}

	public function import_page() {
		// function_exists( 'jvbpd_core' ) && jvbpd_core();
		Admin::$instance->load_template( Array( 'name' => 'import', 'sub' => 'admin-wizard' ), Array( 'helper' => &$this ) );
	}

	public function finish_page() {
		Admin::$instance->load_template( Array( 'name' => 'finish', 'sub' => 'admin-wizard' ), Array( 'helper' => &$this ) );
	}

	public function wc_setup_ready_actions() {

		if ( isset( $_GET['wc_tracker_optin'] ) && isset( $_GET['wc_tracker_nonce'] ) && wp_verify_nonce( $_GET['wc_tracker_nonce'], 'wc_tracker_optin' ) ) {
			update_option( 'woocommerce_allow_tracking', 'yes' );

		} elseif ( isset( $_GET['wc_tracker_optout'] ) && isset( $_GET['wc_tracker_nonce'] ) && wp_verify_nonce( $_GET['wc_tracker_nonce'], 'wc_tracker_optout' ) ) {
			update_option( 'woocommerce_allow_tracking', 'no' );
		}
	}

	public function wc_setup_pages_save() {
		check_admin_referer( 'jvbpd-wizard' );

		WC_Install::create_pages();
		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}


}

if( ! function_exists( 'jvbpd_init' ) ) {
	function jvbpd_init() {
		return jvbpd_init_helper::getInstance();
	}
	jvbpd_init();
}