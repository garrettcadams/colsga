<?php
/**
 * Rest class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class REST extends Instance
{
	protected static $_instance;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init()
	{
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Register REST hooks
	 *
	 * @since  1.0
	 * @access public
	 */
	public function rest_api_init()
	{
		register_rest_route( 'dologin/v1', '/myip', array(
			'methods' => 'GET',
			'callback' => __CLASS__ . '::geoip',
		) );

		register_rest_route( 'dologin/v1', '/sms', array(
			'methods' => 'POST',
			'callback' => __CLASS__ . '::sms',
		) );

		register_rest_route( 'dologin/v1', '/test_sms', array(
			'methods' => 'POST',
			'callback' => __CLASS__ . '::test_sms',
		) );
	}

	/**
	 * Get GeoIP info
	 */
	public static function geoip()
	{
		return IP::geo();
	}

	/**
	 * Send SMS
	 */
	public static function sms()
	{
		return SMS::get_instance()->send();
	}

	/**
	 * Send test SMS
	 */
	public static function test_sms()
	{
		return SMS::get_instance()->test_send();
	}

	/**
	 * Return content
	 */
	public static function ok( $data )
	{
		$data[ '_res' ] = 'ok';
		return $data;
	}

	/**
	 * Return error
	 */
	public static function err( $msg )
	{
		defined( 'debug' ) && debug( '❌ [err] ' . $msg );
		return array( '_res' => 'err', '_msg' => $msg );
	}

}
