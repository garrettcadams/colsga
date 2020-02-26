<?php
/**
 * Admin class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Admin extends Instance
{
	protected static $_instance;

	/**
	 * Init admin
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'plugin_action_links_dologin/dologin.php', array( $this, 'add_plugin_links' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'admin_enqueue_scripts', array( GUI::get_instance(), 'enqueue_style' ) ) ;

	}

	/**
	 * Admin setting page
	 *
	 * @since  1.0
	 * @access public
	 */
	public function admin_menu()
	{
		add_options_page( 'DoLogin Security', 'DoLogin Security', 'manage_options', 'dologin', array( $this, 'setting_page' ) );
	}

	/**
	 * admin_init
	 *
	 * @since  1.2.2
	 * @access public
	 */
	public function admin_init()
	{
		if ( get_transient( 'dologin_activation_redirect' ) ) {
			delete_transient( 'dologin_activation_redirect' );
			if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
				wp_safe_redirect( menu_page_url( 'dologin', 0 ) );
			}
		}

		// Register user phone column
		add_filter( 'user_contactmethods', array( $this, 'user_contactmethods' ), 10, 1 );
		add_filter( 'manage_users_columns', array( $this, 'manage_users_columns' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'manage_users_custom_column' ), 10, 3 );
	}

	/**
	 * Add phone number col in user profile
	 *
	 * @since  1.3
	 */
	public function user_contactmethods( $contactmethods )
	{
		if ( ! array_key_exists( 'phone_number', $contactmethods ) ) {
			$contactmethods[ 'phone_number' ] = __( 'Dologin Security Phone', 'dologin' );
		}
		return $contactmethods;
	}

	public function manage_users_columns( $column )
	{
		if ( ! array_key_exists( 'phone_number', $column ) ) {
			$column[ 'phone_number' ] = __( 'Dologin Security Phone', 'dologin' );
		}
		return $column;
	}

	public function manage_users_custom_column( $val, $column_name, $user_id )
	{
		if ( $column_name == 'phone_number' ) {
			$val = substr( get_the_author_meta( 'phone_number', $user_id ), -4 );
			if ( $val ) {
				$val = '***' . $val . '<br>';
			}

			// Append gen link
			$val .= '<a href="' . Util::build_url( Router::ACTION_PSWD, Pswdless::TYPE_GEN, false, null, array( 'uid' => $user_id ) ) . '" class="button button-primary">' . __( 'Generate Login Link', 'dologin' ) . '</a>';

			return $val;
		}

		return $val;
	}

	/**
	 * Plugin link
	 *
	 * @since  1.1
	 * @access public
	 */
	public function add_plugin_links( $links )
	{
		$links[] = '<a href="' . menu_page_url( 'dologin', 0 ) . '">' . __( 'Settings', 'dologin' ) . '</a>';

		return $links;
	}

	/**
	 * Display and save options
	 *
	 * @since  1.0
	 * @access public
	 */
	public function setting_page()
	{
		Data::get_instance()->tb_create( 'failure' );
		Data::get_instance()->tb_create( 'sms' );
		Data::get_instance()->tb_create( 'pswdless' );

		if ( ! empty( $_POST ) ) {
			check_admin_referer( 'dologin' );

			// Save options
			$list = array() ;

			foreach ( Conf::get_instance()->get_options() as $id => $v ) {
				if ( $id == '_ver' ) {
					continue;
				}

				$list[ $id ] = ! empty( $_POST[ $id ] ) ? $_POST[ $id ] : false ;
			}

			// Special handler for list
			$list[ 'whitelist' ] = $this->_sanitize_list( $_POST[ 'whitelist' ] );
			$list[ 'blacklist' ] = $this->_sanitize_list( $_POST[ 'blacklist' ] );

			foreach ( $list as $id => $v ) {
				Conf::update( $id, $v );
			}
		}

		require_once DOLOGIN_DIR . 'tpl/settings.tpl.php';
	}

	/**
	 * Sanitize list
	 *
	 * @since  1.0
	 * @access public
	 */
	private function _sanitize_list( $list )
	{
		if ( ! is_array( $list ) ) {
			$list = explode( "\n", trim( $list ) );
		}

		foreach ( $list as $k => $v ) {
			$list[ $k ] = implode( ', ', array_map( 'trim', explode( ',', $v ) ) );
		}

		return array_filter( $list );
	}

	/**
	 * Display pswdless
	 *
	 * @since  1.4
	 * @access public
	 */
	public function pswdless_log()
	{
		global $wpdb;

		$list = $wpdb->get_results( 'SELECT * FROM ' . Data::get_instance()->tb( 'pswdless' ) . ' ORDER BY id DESC' );
		foreach ( $list as $k => $v ) {
			$user_info = get_userdata( $v->user_id );
			$list[ $k ]->username = $user_info->user_login;
			$list[ $k ]->link = admin_url( '?dologin=' . $v->id . '.' . $v->hash );
		}

		return $list;
	}

	/**
	 * Display sms log
	 *
	 * @since  1.3
	 * @access public
	 */
	public function sms_log()
	{
		global $wpdb;
		return $wpdb->get_results( 'SELECT * FROM ' . Data::get_instance()->tb( 'sms' ) . ' ORDER BY id DESC LIMIT 10' );
	}

	/**
	 * Display failure log
	 *
	 * @since  1.1
	 * @access public
	 */
	public function log()
	{
		global $wpdb;
		return $wpdb->get_results( 'SELECT * FROM ' . Data::get_instance()->tb( 'failure' ) . ' ORDER BY id DESC LIMIT 10' );
	}
}