<?php

use Awps\Custom\Admin;

if( !defined( 'ABSPATH' ) ) {
	die;
}

if( class_exists( 'Lynk_Core' ) && ! property_exists( 'Lynk_Core', 'prefix' ) ) {

	// For lynk theme
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	deactivate_plugins( 'lynk-core/lynk-core.php' );

	wp_die(
		esc_html__( "The old version of Core plugin has been deactivated due to compatibility. Please update Core plugin to the newest version.", 'jvbpd' ) .
		sprintf( '<p><a href="%1$s" target="_self" title="%2$s">%2$s</a></p>', admin_url( 'plugins.php' ), esc_html__( "Go to plugin update", 'jvbpd' ) )
	);
}

if( class_exists( 'Jvbpd_Core_AdminHelper' ) ) {
	class jvbpd_admin_helper extends Jvbpd_Core_AdminHelper {

		const PLUGIN_URL_FORMAT = 'admin.php?page=%1$s_plugins';

		public static $instance =null;

		public $name;
		public $slug;

		private $theme;
		private $path;
		private $template_part;

		public function __construct() {
			$this->theme = wp_get_theme();
			$this->name = $this->theme->get( 'Name' );
			$this->path = get_template_directory();
			$this->template_part = $this->path . '/views/admin-helper/';

			if( $this->theme->get( 'Template' ) ) {
				$this->parent = wp_get_theme(  $this->theme->get( 'Template' ) );
				$this->name = $this->parent->get( 'Name' );
			}

			// $this->slug = sanitize_title( $this->name );
			$this->slug = 'jvbpd_admin';
			parent::__construct( Array(
				'slug' => $this->slug,
				'name' => $this->name,
			) );

			add_action( 'admin_menu', array( $this, 'createHelperMenu' ), 9 );

			// Plugin Install
			add_action( 'admin_init', Array( $this, 'plugin_actions' ) );

			add_action( 'jvbpd_admin_helper_page_header', Array( $this, 'helper_page_header' ) );
			add_action( 'jvbpd_admin_helper_page_footer', Array( $this, 'helper_page_footer' ) );

			add_filter( 'jvbpd_tgmpa_return_link', Array( $this, 'tgmpa_return_link' ) );
		}

		/**
		 *
		 * Common Function
		 */

		 function plugin_link( $item ) {
			$installed_plugins = get_plugins();

			$item['sanitized_plugin'] = $item['name'];

			// We have a repo plugin
			if ( ! $item['version'] ) {
				$item['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );
			}

			/** We need to display the 'Install' hover link */
			if ( ! isset( $installed_plugins[$item['file_path']] ) ) {
				$actions = array(
					'install' => sprintf(
						'<a href="%1$s" class="button button-primary" title="Install %2$s">Install</a>',
						esc_url( wp_nonce_url(
							add_query_arg(
								array(
									'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
									'plugin'        => urlencode( $item['slug'] ),
									'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
									'plugin_source' => urlencode( $item['source'] ),
									'tgmpa-install' => 'install-plugin',
									'return_url'    => sprintf( '%1$s_plugins', $this->slug )
								),
								TGM_Plugin_Activation::$instance->get_tgmpa_url()
							),
							'tgmpa-install',
							'tgmpa-nonce'
						) ),
						$item['sanitized_plugin']
					),
				);
			}
			/** We need to display the 'Activate' hover link */
			elseif ( is_plugin_inactive( $item['file_path'] ) ) {
				$actions = array(
					'activate' => sprintf(
						'<a href="%1$s" class="button button-primary" title="Activate %2$s">Activate</a>',
						esc_url( add_query_arg(
							array(
								'plugin'               => urlencode( $item['slug'] ),
								'plugin_name'          => urlencode( $item['sanitized_plugin'] ),
								'plugin_source'        => urlencode( $item['source'] ),
								'jvbpd-activate'       => 'activate-plugin',
								'jvbpd-activate-nonce' => wp_create_nonce( 'jvbpd-activate' ),
							),
							admin_url( sprintf( self::PLUGIN_URL_FORMAT, $this->slug ) )
						) ),
						$item['sanitized_plugin']
					),
				);
			}
			/** We need to display the 'Update' hover link */
			elseif ( version_compare( $installed_plugins[$item['file_path']]['Version'], $item['version'], '<' ) ) {
				$actions = array(
					'update' => sprintf(
						'<a href="%1$s" class="button button-primary" title="Install %2$s">Update</a>',
						wp_nonce_url(
							add_query_arg(
								array(
									'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
									'plugin'        => urlencode( $item['slug'] ),

									'tgmpa-update'  => 'update-plugin',
									'plugin_source' => urlencode( $item['source'] ),
									'version'       => urlencode( $item['version'] ),
									'return_url'    => sprintf( '%1$s_plugins', $this->slug )
								),
								TGM_Plugin_Activation::$instance->get_tgmpa_url()
							),
							'tgmpa-update',
							'tgmpa-nonce'
						),
						$item['sanitized_plugin']
					),
				);
			} elseif ( jvbpd_active_plugin( $item['file_path'] ) ) {
				$actions = array(
					'deactivate' => sprintf(
						'<a href="%1$s" class="button button-primary" title="Deactivate %2$s">Deactivate</a>',
						esc_url( add_query_arg(
							array(
								'plugin'                 => urlencode( $item['slug'] ),
								'plugin_name'            => urlencode( $item['sanitized_plugin'] ),
								'plugin_source'          => urlencode( $item['source'] ),
								'jvbpd-deactivate'       => 'deactivate-plugin',
								'jvbpd-deactivate-nonce' => wp_create_nonce( 'jvbpd-deactivate' ),
							),
							admin_url( sprintf( self::PLUGIN_URL_FORMAT, $this->slug ) )
						) ),
						$item['sanitized_plugin']
					),
				);
			}

			return $actions;
		}


		public function createHelperMenu() {

			$this->addSub( Array(
				'slug' => 'status',
				'name' => esc_html__( "Status", 'jvbpd' ),
				'func' => array( $this, 'helper_staus' ),
			) );

			$this->addSub( Array(
				'slug' => 'plugins',
				'name' => esc_html__( "Plugins", 'jvbpd' ),
				'func' => array( $this, 'helper_plugins' ),
			) );

			$this->addSub( Array(
				'slug' => 'settings',
				'name' => esc_html__( "Theme Settings", 'jvbpd' ),
				'func' => array( Admin::$instance, 'settings_page_initialize' ),
			) );
		}

		public function plugin_actions() {
			if ( isset( $_GET['jvbpd-deactivate'] ) && $_GET['jvbpd-deactivate'] == 'deactivate-plugin' ) {
				check_admin_referer( 'jvbpd-deactivate', 'jvbpd-deactivate-nonce' );

				$plugins = TGM_Plugin_Activation::$instance->plugins;

				foreach( $plugins as $plugin ) {
					if ( $plugin['slug'] == $_GET['plugin'] ) {
						deactivate_plugins( $plugin['file_path'] );
					}
				}
			} if ( isset( $_GET['jvbpd-activate'] ) && $_GET['jvbpd-activate'] == 'activate-plugin' ) {
				check_admin_referer( 'jvbpd-activate', 'jvbpd-activate-nonce' );

				$plugins = TGM_Plugin_Activation::$instance->plugins;

				foreach( $plugins as $plugin ) {
					if ( $plugin['slug'] == $_GET['plugin'] ) {
						activate_plugin( $plugin['file_path'] );

						wp_redirect( admin_url( sprintf( self::PLUGIN_URL_FORMAT, $this->slug ) ) );
						exit;
					}
				}
			}
		}

		public function helper_main() { $this->get_template( 'welcome' ); }
		public function helper_staus() {$this->get_template( 'status' ); }
		public function helper_plugins() {$this->get_template( 'plugins' ); }

		public function get_template( $template_name )  {
			$objTheme		= $this->theme;
			$strFileName	= $this->template_part . $template_name . '.php';
			do_action( 'jvbpd_admin_helper_page_header', $template_name );
			if( file_exists( $strFileName ) ) {
				require_once $strFileName;
			}
			do_action( 'jvbpd_admin_helper_page_footer', $template_name );
		}

		public function helper_page_header( $template='' ) {
			global $submenu;

			$jvbpdTabMenues = Array();
			if( isset( $submenu[ sanitize_title( 'jvbpd' ) ] ) ) {
				$jvbpdTabMenues = $submenu[ sanitize_title( 'jvbpd' ) ];
			}

			do_action( 'jvbpd_admin_helper_header_before' );

			if( is_array( $jvbpdTabMenues ) ) {
				?>
				<div class="wrap about-wrap">
					<h2 class="nav-tab-wrapper">
						<?php
						foreach( $jvbpdTabMenues as $menuItem ) {
							if( false !== strpos( $menuItem[2], 'jvbpd-listing-elm' ) ) {
								continue;
							} ?>
							<a href="<?php echo esc_url( add_query_arg( Array( 'page' => $menuItem[2] ), admin_url( 'admin.php' ) ) ); ?>" class="nav-tab <?php if( isset( $_GET[ 'page' ] ) and $_GET['page'] == $menuItem[2] ) { echo sanitize_html_class( 'nav-tab-active' ); }?> "><?php  echo esc_html( $menuItem[0] ); ?></a>
							<?php
						} ?>
					</h2>
				</div>
				<?php
			}

			do_action( 'jvbpd_admin_helper_header_after' );
		}

		public function helper_page_footer( $template='' ){}

		/**
		 *
		 * ## TGMPA UPDATE REQUIRED ACTION ##
		 *
		 * library / functions / class-tgm-plugin-activation.php
		 * function : do_plugin_install()
		 * Line : 915
		 *
		 *
		 */
		public function tgmpa_return_link( $link='' ) {
			if(
				(
					isset( $_GET[ 'tgmpa-install' ] ) && $_GET[ 'tgmpa-install' ] =='install-plugin' ||
					isset( $_GET[ 'tgmpa-update' ] ) && $_GET[ 'tgmpa-update' ] =='update-plugin'
				) &&
				( isset( $_GET[ 'return_url' ] ) && $_GET[ 'return_url' ] == sprintf( '%1$s_plugins', $this->slug ) )
			){
				$url = add_query_arg(
					array(
						'page' => $_GET[ 'return_url' ],
					),
					admin_url( 'admin.php' )
				);
				$link = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $url ),
					esc_html__( 'Return Javo Core Plugin Page', 'jvbpd' )
				);
			}
			return $link;
		}

		public static function getInstance() {
			if( is_null( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}


	if( !function_exists( 'jvbpd_admin_helper_init') ){
		function jvbpd_admin_helper_init() {
			$instance = jvbpd_admin_helper::getInstance();
			$GLOBALS[ 'jvbpd_admin_helper' ] = $instance;
			return $instance;
		}
		jvbpd_admin_helper_init();
	}
}