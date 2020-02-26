<?php
/**
 * SMS class
 *
 * @since 1.3
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class SMS extends Instance
{
	protected static $_instance;

	private $_dry_run = false;

	/**
	 * Return current usre's phone number
	 *
	 * @since 1.3
	 */
	public function current_user_phone()
	{
		$uid = get_current_user_id();
		$phone = get_user_meta( $uid, 'phone_number', true );
		return $phone;
	}

	/**
	 * Check if is dry run (dry run = before sending sms) or not
	 *
	 * @since  1.6
	 */
	public static function is_dry_run()
	{
		return self::get_instance()->_dry_run;
	}

	/**
	 * Verify SMS after u+p authenticated
	 *
	 * @since  1.3
	 */
	public function authenticate( $user, $username, $password )
	{
		global $wpdb;

		defined( 'debug' ) && debug( 'auth' );

		if ( $this->_dry_run ) {
			defined( 'debug' ) && debug( 'bypassed due to dryrun' );
			return $user;
		}

		if ( empty( $username ) || empty( $password ) ) {
			defined( 'debug' ) && debug( 'bypassed due to lack of u/p' );
			return $user;
		}

		if ( is_wp_error( $user ) ) {
			defined( 'debug' ) && debug( 'bypassed due to is_wp_error already' );
			return $user;
		}

		// If sms is optional and the user doesn't have phone set, bypass
		$phone = get_user_meta( $user->ID, 'phone_number', true );
		if ( ! $phone ) {
			defined( 'debug' ) && debug( 'no phone number set' );
			if ( ! Conf::val( 'sms_force' ) ) {
				defined( 'debug' ) && debug( 'bypassed due to no force_sms check' );
				return $user;
			}
		}

		$error = new \WP_Error();

		// Validate dynamic code
		if ( empty( $_POST[ 'dologin-two_factor_code' ] ) ) {
			$error->add( 'sms_missing', Lang::msg( 'sms_missing' ) );
			define( 'DOLOGIN_ERR', true );
			defined( 'debug' ) && debug( '❌ sms missing' );
			return $error;
		}

		$tb_sms = Data::get_instance()->tb( 'sms' );

		$q = "SELECT id, code FROM $tb_sms WHERE user_id = %d AND used = 0";
		$row = $wpdb->get_row( $wpdb->prepare( $q, array( $user->ID ) ) );

		if ( $row->id ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $tb_sms SET used = 1 WHERE id = %d", array( $row->id ) ) );
		}

		if ( ! $row->code || $row->code != $_POST[ 'dologin-two_factor_code' ] ) {
			$error->add( 'sms_wrong', Lang::msg( 'sms_wrong' ) );
			define( 'DOLOGIN_ERR', true );
			defined( 'debug' ) && debug( '❌ sms wrong' );
			return $error;
		}

		defined( 'debug' ) && debug( '✅ auth successfully' );

		return $user;
	}

	/**
	 * Send test SMS
	 *
	 * @since  1.3
	 */
	public function test_send()
	{
		global $wpdb;
		if ( empty( $_POST[ 'phone' ] ) ) {
			return REST::err( Lang::msg( 'not_phone_set_curr' ) );
		}

		// Check interval
		if ( time() - get_option( 'dologin_test' ) < 60 ) {
			return REST::err( Lang::msg( 'try_after', 60 ) );
		}

		update_option( 'dologin_test', time() );

		$phone = $_POST[ 'phone' ];
		$info = 'Test SMS message at ' . date( 'm/d/Y H:i:s', time() + get_option( 'gmt_offset' ) * 60 * 60 );

		// Send
		try {
			$res = $this->_api( $phone, $info );
		} catch ( \Exception $ex ) {
			return REST::err( $ex->getMessage() );
		}

		return REST::ok( array( 'info' => 'Sent to ***' . substr( $phone, -4 ) . ' at ' . date( 'm/d/Y H:i:s', time() + get_option( 'gmt_offset' ) * 60 * 60 ) ) );
	}

	/**
	 * Send SMS
	 *
	 * @since  1.3
	 */
	public function send()
	{
		global $wpdb;

		if ( ! Conf::val( 'sms' ) ) {
			return REST::ok( array( 'bypassed' => 1 ) );
		}

		$field_u = 'log';
		$field_p = 'pwd';
		if ( isset( $_POST[ 'woocommerce-login-nonce' ] ) ) {
			$field_u = 'username';
			$field_p = 'password';
		}

		if ( empty( $_POST[ $field_u ] ) || empty( $_POST[ $field_p ] ) ) {
			return REST::err( Lang::msg( 'empty_u_p' ) );
		}

		// Verify u & p first
		$this->_dry_run = true;
		$user = wp_authenticate( $_POST[ $field_u ], $_POST[ $field_p ] );
		$this->_dry_run = false;
		if ( is_wp_error( $user ) ) {
			return REST::err( $user->get_error_message() );
		}

		// Search if the user has number set in phone
		$phone = get_user_meta( $user->ID, 'phone_number', true );

		if ( ! $phone ) {
			if ( ! Conf::val( 'sms_force' ) ) {
				defined( 'debug' ) && debug( 'bypassed due to no phone set' );
				return REST::ok( array( 'bypassed' => 1 ) );
			}
			return REST::err( Lang::msg( 'not_phone_set_user' ) );
		}

		// Generate dynamic code
		$code = s::rrand( 4, 1 );
		$rid = s::rrand( 2, 1 );
		$ip_info = ip::geo();
		$info = "Dynamic Code:$code.(Tag:$rid) From: " . $ip_info[ 'country' ] . '-' . $ip_info[ 'city' ] . '.';

		$tb_sms = Data::get_instance()->tb( 'sms' );

		// Expire old ones
		$wpdb->query( $wpdb->prepare( "UPDATE $tb_sms SET used = -1 WHERE user_id = %d AND used = 0", array( $user->ID ) ) );

		// Save to db
		$s = array(
			'user_id'	=> $user->ID,
			'sms'		=> $info,
			'code'		=> $code,
			'used'		=> 0,
			'dateline'	=> time(),
		);
		$q = 'INSERT INTO ' . $tb_sms . ' ( ' . implode( ',', array_keys( $s ) ) . ' ) VALUES ( ' . implode( ',', array_fill( 0, count( $s ), '%s' ) ) . ' )' ;
		$wpdb->query( $wpdb->prepare( $q, $s ) );
		$id = $wpdb->insert_id;

		// Send
		try {
			$res = $this->_api( $phone, $info );
		} catch ( \Exception $ex ) {
			return REST::err( $ex->getMessage() );
		}

		// Update log
		$wpdb->query( $wpdb->prepare( "UPDATE $tb_sms SET res = %s WHERE id = %d", array( $res, $id ) ) );

		$res_json = json_decode( $res, true );

		// Expected response
		if ( ! empty( $res_json[ '_res' ] ) && $res_json[ '_res' ] == 'ok' ) {
			return REST::ok( array( 'info' => "Tag:$rid. Sent to ***" . substr( $phone, -4 ) . '.' ) );
		}

		if ( ! empty( $res_json[ '_msg' ] ) ) {
			return REST::err( $res_json[ '_msg' ] );
		}

		return REST::err( 'Unknown error' );
	}

	/**
	 * Call API to send msg
	 *
	 * @since  1.5
	 */
	private function _api( $phone, $content )
	{
		$app = 'wp-' . home_url();

		// Send
		$url = 'https://doapi.us/text?format=json';
		$data = array( 'phone' => $phone, 'content' => $content, 'app' => $app );

		$res = wp_remote_post( $url, array( 'body' => $data, 'timeout' => 15, 'sslverify' => false ) ); // id=>xx

		if ( is_wp_error( $res ) ) {
			$error_message = $res->get_error_message();
			throw new \Exception( $error_message );
		}

		return $res[ 'body' ];
	}

}
