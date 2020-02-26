<?php
/**
 * Core class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Core extends Instance
{
	protected static $_instance;

	const VER = DOLOGIN_V;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function __construct()
	{
		defined( 'debug' ) && debug2( 'init' );

		Conf::get_instance()->init();

		if ( is_admin() ) {
			Admin::get_instance()->init();
		}

		Auth::get_instance()->init();

		GUI::get_instance()->init();

		REST::get_instance()->init();

		Util::get_instance()->init();

		Router::get_instance()->init();

		Pswdless::get_instance()->init();

		register_activation_hook( DOLOGIN_DIR . 'dologin.php', __NAMESPACE__ . '\Util::activate' );
		register_deactivation_hook( DOLOGIN_DIR . 'dologin.php', __NAMESPACE__ . '\Util::deactivate' ) ;
		register_uninstall_hook( DOLOGIN_DIR . 'dologin.php', __NAMESPACE__ . '\Util::uninstall' ) ;

		Lang::get_instance()->init();
	}
}
