<?php

class jvbpd_ajax_propcess{

	public $hooks = Array();

	private static $instance =null;

	public function __construct() {
		$this->setVariables();
		$this->register_hooks();
		add_action( 'init', array( $this, 'register_ajax_action' ) );
	}

	public function setVariables() {
		$this->hooks[ 'send_mail' ] = Array( $this, 'send_mail' );
		$this->hooks[ 'register_login_add_user' ] = Array( $this, 'ajax_signup' );
		$this->hooks[ 'jvbpd_ajax_user_join' ] = Array( $this, 'ajax_signup' );
		$this->hooks[ 'jvbpd_ajax_user_login' ] = Array( $this, 'ajax_login' );
	}

	public function register_hooks() {
		add_filter( 'jvbpd_front_user_registered_redirect', array( $this, 'registered_redirect' ) );
	}

	public function register_ajax_action() {
		foreach( $this->hooks as $strAction => $fnCallback ) {
			add_action( 'wp_ajax_' . $strAction, $fnCallback );
			add_action( 'wp_ajax_nopriv_' . $strAction, $fnCallback );
		}
	}

	public function ajax_signup() {
		$output = Array();
		$signup_args = Array('user_pass'=>null);

		$signup_args['user_login'] = isset($_POST['user_login']) ? $_POST['user_login'] : false;
		$signup_args['first_name'] = isset($_POST['first_name']) ? $_POST['first_name'] : false;
		$signup_args['last_name'] = isset($_POST['last_name']) ? $_POST['last_name'] : false;
		$signup_args['user_pass'] = isset($_POST['user_pass']) ? $_POST['user_pass'] : wp_generate_password( 12, false );

		if( isset( $_POST['user_name'] ) ){
			$signup_fullName = (Array) @explode(' ', $_POST['user_name']);
			$signup_args['first_name'] = $signup_fullName[0];
			if(
				!empty( $signup_fullName[1] ) &&
				$signup_fullName[1] != ''
			){
				$signup_args['last_name'] = $signup_fullName[1];
			}
		}

		if( isset( $_POST['user_login'] ) ) {
			$signup_args['user_email'] = isset($_POST['user_email']) ? $_POST['user_email'] : false;
			if( !is_email( $signup_args['user_email'] ) ) {
				$signup_args = new WP_Error( 'empty_email', esc_html__( "Your email address is invalid. Please enter a valid address.", 'jvfrmtd' ) );
			}
		}
		if(!is_wp_error($signup_args)) {
			$user_id = wp_insert_user($signup_args, true);
			if( !is_wp_error($user_id) ){
				update_user_option( $user_id, 'default_password_nag', true, true );

				if( apply_filters( 'jvbpd_add_new_user_notification', true ) ) {
					wp_new_user_notification( $user_id, $signup_args['user_pass'] );
				}

				// Assign Post
				if( isset( $_POST['post_id'] ) && (int)$_POST['post_id'] > 0 ){
					$origin_post_id		= (int) $_POST['post_id'];
					$parent_post_id		= (int)get_post_meta( $origin_post_id, 'parent_post_id', true);

					$post_id = wp_update_post(Array(
						'ID'			=> $parent_post_id
						, 'post_author'	=> $user_id
					));

					update_post_meta($origin_post_id	, 'approve', 'approved');
					update_post_meta($post_id			, 'claimed', 'yes');
				}else{
					wp_set_current_user( $user_id );
					wp_set_auth_cookie( $user_id );
					do_action( 'wp_login', $user_id, get_user_by( 'id', $login_state ) );
				}

				if( function_exists( 'bp_core_get_user_domain' ) ) {
					$strRedirectURL = bp_core_get_user_domain( $user_id );
				}

				do_action( 'jvbpd_new_user_append_meta', $user_id );
				$output['state'] = 'success';
				$output['link'] = apply_filters( 'jvbpd_front_user_registered_redirect', $strRedirectURL );

			}else{
				$output['state']		= 'failed';
				$output['comment']	= $user_id->get_error_message();
			}
		}else{
			$output['state']		= 'failed';
			$output['comment']	= $signup_args->get_error_message();
		}
		wp_send_json( $output );
	}

	public function send_mail(){
		do_action( 'jvbpd_contact_send_mail' );
	}

	public function getLoginRedirectURL( $user_id=0 ){
		global $bp;

		$strRedirect = home_url( '/' );
		if( function_exists( 'bp_core_get_user_domain' ) ) {
			$strRedirect = bp_core_get_user_domain( $user_id );
		}
		return $strRedirect;
	}

	public function ajax_login() {
		check_ajax_referer( 'user_login', 'security' );
		$response = Array();
		$login_state = wp_signon(
			Array(
				'user_login' => isset($_POST['log']) ? $_POST['log'] : false,
				'user_password'	=> isset($_POST['pwd']) ? $_POST['pwd'] : false,
				'remember' => isset($_POST['rememberme']) ? $_POST['rememberme'] : false,
			),
			false
		);

		if( is_wp_error( $login_state ) ) {
			$response[ 'error' ] = $login_state->get_error_message();
		}else{
			wp_set_current_user( $login_state->ID );
			wp_set_auth_cookie( $login_state->ID );
			do_action( 'wp_login', $login_state->user_login, $login_state );

			$afterLogin = jvbpd_tso()->get( 'login_redirect', '' );
			$response[ 'redirect' ] = 'refresh';
			if( isset( $_POST[ 'referer' ] ) && '' != $_POST[ 'referer' ] ) {
				$response[ 'redirect' ] = $_POST[ 'referer' ];
			}
			if( isset( $_POST[ 'redirect' ] ) && '' != $_POST[ 'redirect' ] ) {
				$afterLogin = $_POST[ 'redirect' ];
			}
			switch( $afterLogin ) {
				case 'home': $response[ 'redirect' ] = home_url( '/' ); break;
				case 'admin': $response[ 'redirect' ] = admin_url( '/' ); break;
				case 'profile': $response[ 'redirect' ] = $this->getLoginRedirectURL( $login_state->ID ); break;
			}
			$response[ 'state' ] = 'OK';
		}
		wp_send_json( $response );
	}

	public function registered_redirect( $permalink='' ) {
		$redirect_type = jvbpd_tso()->get( 'login_redirect', '' );

		switch( $redirect_type ) {
			case 'home' : $permalink = home_url( '/' ); break;
			case 'current' : $permalink = home_url( '/' ); break;
			case 'admin' : $permalink = admin_url( '/' ); break;
		}
		return $permalink;
	}

	public static function getInstance() {
		if( is_null( self::$instance ) )
			self::$instance = new self;
		return self::$instance;
	}

}

if( !function_exists( 'jvbpd_process' ) ) : function jvbpd_process() {
	return jvbpd_ajax_propcess::getInstance();
} jvbpd_process(); endif;