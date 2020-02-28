<?php

class jvbpd_register extends Jvbpd_OtherShortcode {

	private static $instance = null;

	public function __construct() {
			
		$args = Array(
			'name' => 'register',
			'label' => esc_html__( "Register", 'jvfrmtd' ),
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
				'default_var' => esc_html__( "Register", 'jvfrmtd' ),
			),
			Array(
				'type' => 'dropdown',
				'heading' => __( "Style", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'style',
				'default_var' => 'trans_white',
				'value' => array(
					esc_html__( "Transparent White", 'jvfrmtd' ) => 'trans_white',
					esc_html__( "Transparent Dark", 'jvfrmtd' ) => 'trans_dark',
					esc_html__( "Default", 'jvfrmtd' ) => 'none',
				),
			),
		);

	}

	public function parse( $args ) {
		$arrDefault = Array();
		foreach( $this->getParams() as $params ) {
			$arrDefault[ $params[ 'param_name' ] ] = $params[ 'default_var' ];
		}

		if( isset( $args[ 'style' ] ) && '' != $args[ 'style' ] ) {
			$this->classes[] = sprintf( 'type-%s', $args[ 'style' ] );
		}

		return shortcode_atts( $arrDefault, $args );
	}

	public function getTitle( $params ) {
		$strTitle = $params[ 'title' ];
		echo $strTitle;
	}

	public function render( $params ) {
		printf( '<h3 class="register-title">%s</h3>', $params[ 'title' ] );
		echo jvbpd_basic_scode()->createJoinForm( Array( 'close_button' => true ), null );
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}