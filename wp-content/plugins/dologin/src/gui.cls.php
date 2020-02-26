<?php
/**
 * GUI class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class GUI extends Instance
{
	protected static $_instance;

	/**
	 * Init
	 *
	 * @since  1.3
	 * @access public
	 */
	public function init()
	{
		add_action( 'login_message', array( $this, 'login_message' ) );

		// Register injection for phone number
		add_action( 'register_form', array( $this, 'register_form' ) );

		// Append js and set ajax url
		add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ) );
		add_action( 'login_form', array( $this, 'login_form' ) );
		add_action( 'woocommerce_login_form', array( $this, 'login_enqueue_scripts' ) );
		add_action( 'woocommerce_login_form', array( $this, 'login_form' ) );
	}

	/**
	 * Enqueue js
	 *
	 * @since  1.3
	 * @access public
	 */
	public function login_enqueue_scripts()
	{
		if ( ! Util::is_login_page() ) {
			// return;
		}

		$this->enqueue_style();

		// JS is only for sms code
		if ( ! Conf::val( 'sms' ) ) {
			return;
		}

		wp_register_script( 'dologin', DOLOGIN_PLUGIN_URL . 'assets/login.js', array( 'jquery' ), Core::VER, false );

		$localize_data = array();
		$localize_data[ 'login_url' ] = get_rest_url( null, 'dologin/v1/sms' );
		wp_localize_script( 'dologin', 'dologin', $localize_data ) ;

		wp_enqueue_script( 'dologin' );
	}

	/**
	 * Load style
	 * @since 1.3
	 */
	public function enqueue_style()
	{
		wp_enqueue_style( 'dologin', DOLOGIN_PLUGIN_URL . 'assets/login.css', array(), Core::VER, 'all');
	}

	/**
	 * Display login form
	 *
	 * @since  1.3
	 * @access public
	 */
	public function login_form()
	{
		if ( Conf::val( 'sms' ) ) {
			echo '	<p id="dologin-process">
						Dologin Security:
						<span id="dologin-process-msg"></span>
					</p>
					<p id="dologin-dynamic_code">
						<label for="dologin-two_factor_code">' . __( 'Dynamic Code', 'dologin' ) . '</label>
						<br /><input type="text" name="dologin-two_factor_code" id="dologin-two_factor_code" autocomplete="off" />
					</p>
				';
		}

		if ( Conf::val( 'gg' ) ) {
			Captcha::get_instance()->show();
		}
	}

	public function register_form()
	{
		if ( Conf::val( 'sms_force' ) ) {
			echo '	<p>
						<label for="phone_number">' . __( 'Dologin Security Phone', 'dologin' ) . '</label>
						<input type="text" name="phone_number" id="phone_number" class="input" size="25" required />
					</p>
			';
		}
	}

	/**
	 * Login default display messages
	 *
	 * @since  1.1
	 * @access public
	 */
	public function login_message( $msg )
	{
		if ( defined( 'DOLOGIN_ERR' ) ) {
			return;
		}

		$msg .= '<div class="success">' . Lang::msg( 'under_protected' ) . '<img src="' . DOLOGIN_PLUGIN_URL . 'assets/shield.svg" class="dologin-shield"></div>';

		return $msg;
	}
}