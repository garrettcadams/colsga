<?php

class jvbpd_login3 extends Jvbpd_OtherShortcode {

	private static $instance = null;

	public function __construct() {

		if( ! class_exists( 'buddyPress' ) ) {
			return false;
		}

		$args = Array(
			'name' => 'login3',
			'label' => esc_html__( "Login3", 'jvfrmtd' ),
			'params' => $this->getParams(),
		);
		parent::__construct( $args );
	}

	public function getParams() {
		return Array(
			Array(
				'type' => 'textfield',
				'heading' => __( "Title", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'title',
				'default_var' => esc_html__( "Active Member", 'jvfrmtd' ),
			),
		);

	}

	public function parse( $args ) {
		$arrDefault = Array();
		foreach( $this->getParams() as $params ) {
			$arrDefault[ $params[ 'param_name' ] ] = $params[ 'default_var' ];
		}
		return shortcode_atts( $arrDefault, $args );
	}

	public function getTitle( $params ) {
		$strTitle = $params[ 'title' ];
		echo $strTitle;
	}

	public function render( $params ) {
		echo bp_core_load_template( 'login', true );


		wp_login_form(
			Array(
				'form_id' => $this->sID . '_login_form'
			)
		);
		printf(
			"<p class=\"login-lostpassword\"><a href=\"%s\">%s</a></p>",
			wp_lostpassword_url( home_url( '/' ) ),
			__( "Lost your password?", 'jvfrmtd' )
		);
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}