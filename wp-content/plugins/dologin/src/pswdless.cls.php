<?php
/**
 * Password less class
 *
 * @since 1.4
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Pswdless extends Instance
{
	protected static $_instance;

	const TYPE_GEN = 'gen';
	const TYPE_LOCK = 'lock';
	const TYPE_DEL = 'del';
	const TYPE_TOGGLE_ONETIME = 'toggle_onetime';
	const TYPE_EXPIRE_7 = 'expire_7';

	/**
	 * Init
	 * @since  1.4
	 */
	public function init()
	{
		if ( ! empty( $_GET[ 'dologin' ] ) ) {
			add_action( 'init', array( $this, 'try_login' ) );
		}
	}

	/**
	 * Login
	 * @since  1.4
	 */
	public function try_login()
	{
		global $wpdb;

		$username = 'N/A';

		$info = explode( '.', $_GET[ 'dologin' ] );
		if ( empty( $info[ 0 ] ) || empty( $info[ 1 ] ) ) {
			return $this->_failed_login( $username );
			// exit( 'dologin_no_token' );
		}

		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' . Data::get_instance()->tb( 'pswdless' ) . '` WHERE id = %d', $info[ 0 ] ) );
		if ( $row ) {
			$user_info = get_userdata( $row->user_id );
			$username = $user_info->user_login;
		}

		if ( ! $row || $row->hash != $info[ 1 ] ) {
			return $this->_failed_login( $username );
			// exit( 'dologin_err_hash' );
		}

		if ( $row->active != 1 ) {
			exit( 'dologin_link_used' );
		}

		if ( $row->expired_at < time() ) {
			exit( 'dologin_link_expired' );
		}

		// can login, update record first
		$q = 'UPDATE `' . Data::get_instance()->tb( 'pswdless' ) . '` SET last_used_at = %d, count = count + 1 ';
		if ( $row->onetime ) {
			$q .= ', active = 0 ';
		}
		$q .= ' WHERE id = %d';
		$wpdb->query( $wpdb->prepare( $q, array( time(), $info[ 0 ] ) ) );

		// Login
		wp_set_auth_cookie( $user_info->ID, false );
		do_action('wp_login', $user_info->user_login, $user_info );

		nocache_headers();

		Router::redirect( admin_url() );
	}

	/**
	 * Note failed login
	 * @since  1.4
	 */
	private function _failed_login( $username )
	{
		do_action( 'wp_login_failed', $username );
	}

	/**
	 * Expiration set
	 *
	 * @since  1.4
	 */
	private function _expire_link()
	{
		global $wpdb;

		if ( empty( $_GET[ 'dologin_id' ] ) ) {
			return;
		}

		$q = 'UPDATE `' . Data::get_instance()->tb( 'pswdless' ) . '` SET expired_at = GREATEST( expired_at, %d ) + 86400*7 WHERE id = %d';
		$wpdb->query( $wpdb->prepare( $q, time(), $_GET[ 'dologin_id' ] ) );
	}

	/**
	 * Switch one time
	 *
	 * @since  1.4
	 */
	private function _onetime_link()
	{
		global $wpdb;

		if ( empty( $_GET[ 'dologin_id' ] ) ) {
			return;
		}

		$q = 'UPDATE `' . Data::get_instance()->tb( 'pswdless' ) . '` SET onetime = ( onetime + 1 ) % 2 WHERE id = %d';
		$wpdb->query( $wpdb->prepare( $q, $_GET[ 'dologin_id' ] ) );
	}

	/**
	 * Lock
	 *
	 * @since  1.4
	 */
	private function _lock_link()
	{
		global $wpdb;

		if ( empty( $_GET[ 'dologin_id' ] ) ) {
			return;
		}

		$q = 'UPDATE `' . Data::get_instance()->tb( 'pswdless' ) . '` SET active = ( active + 1 ) % 2 WHERE id = %d';
		$wpdb->query( $wpdb->prepare( $q, $_GET[ 'dologin_id' ] ) );
	}

	/**
	 * Delete
	 *
	 * @since  1.4.1
	 */
	private function _del_link()
	{
		global $wpdb;

		if ( empty( $_GET[ 'dologin_id' ] ) ) {
			return;
		}

		$q = 'DELETE FROM `' . Data::get_instance()->tb( 'pswdless' ) . '` WHERE id = %d';
		$wpdb->query( $wpdb->prepare( $q, $_GET[ 'dologin_id' ] ) );
	}

	/**
	 * Generate link
	 *
	 * @since  1.4
	 * @access public
	 */
	public function gen_link( $src = false, $return_url = false )
	{
		global $wpdb;

		Data::get_instance()->tb_create( 'pswdless' );

		$current_user = wp_get_current_user();

		$user_id = ! empty( $_GET[ 'uid' ] ) ? $_GET[ 'uid' ] : $current_user->ID;
		if ( ! $user_id ) {
			return;
		}

		if ( ! $src ) {
			$src = $current_user->display_name;
		}

		$hash = s::rrand( 32 );

		$q = 'INSERT INTO `' . Data::get_instance()->tb( 'pswdless' ) . '` SET user_id = %d, hash = %s, dateline = %d, onetime = 1, active = 1, src = %s, expired_at = %d';
		$wpdb->query( $wpdb->prepare( $q, array( $user_id, $hash, time(), $src, time() + 86400 * 7 ) ) );
		$id = $wpdb->insert_id;

		if ( $return_url ) {
			return admin_url( '?dologin=' . $id . '.' . $hash );
		}

		Router::redirect( admin_url( 'options-general.php?page=dologin' ) );
	}

	/**
	 * Handler
	 *
	 * @since  1.4
	 */
	public static function handler()
	{
		$instance = self::get_instance();

		$type = Router::verify_type();

		switch ( $type ) {
			case self::TYPE_GEN:
				$instance->gen_link();
				break;

			case self::TYPE_LOCK:
				$instance->_lock_link();
				break;

			case self::TYPE_DEL:
				$instance->_del_link();
				break;

			case self::TYPE_TOGGLE_ONETIME:
				$instance->_onetime_link();
				break;

			case self::TYPE_EXPIRE_7:
				$instance->_expire_link();
				break;

			default:
				break;
		}

		Router::redirect();
	}

}