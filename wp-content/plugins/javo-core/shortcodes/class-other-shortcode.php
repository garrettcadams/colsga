<?php

abstract class Jvbpd_OtherShortcode {

	Const SHORTCODE_FORMAT = 'jvbpd_%s';

	public $label = '';

	protected $name = '';
	protected $params = Array();
	protected $classes= Array();

	public function __get( $key ) {
		$output = false;
		if( in_array( $key, array( 'name' ) ) ) {
			$output = $this->$key;
		}
		return $output;
	}

	public function __construct( $args ) {
		$this->setVariables( $args );
		$this->registerHooks();
	}

	public function setVariables( $args=Array() ) {
		$args =  wp_parse_args(
			$args, Array(
				'name' => '',
				'label' => '',
				'params' => Array(),
			)
		);
		$this->name = sprintf( self::SHORTCODE_FORMAT, $args[ 'name' ] );
		$this->label = $args[ 'label' ];
		$this->params = $args[ 'params' ];
	}

	public function registerHooks() {
		$this->createShortcode();
		add_action( 'vc_before_init', array( $this, 'registerVC' ) );
	}

	public function createShortcode() {
		add_shortcode( $this->name, array( $this, 'output' ) );
	}

	public function registerVC() {
		$args = array(
			'base' => $this->name,
			'name' => $this->label,
			'category' => __( "Javo", 'jvfrmtd' ),
		);

		if( !empty( $this->params ) ) {
			$args[ 'params' ] = $this->params;
		}
		vc_map( $args );
	}

	public function getClasses() {
		$strClass = apply_filters( 'jvbpd_other_shortcode_css', (Array) $this->classes, $this );
		return join( ' ', $strClass );
	}

	public function getHeader() {
		printf( '<div class="javo-shortcode shortcode-%1$s %2$s">', $this->name, $this->getClasses() );
	}

	public function getFooter() {
		printf( '</div>' );
	}

	public function output( $args=Array(), $content='' ) {
		add_action( 'wp_footer', array( $this, 'enqueues' ) );
		$params = $this->parse( $args );
		ob_start();
		$this->getHeader();
		$this->render( $params );
		$this->getFooter();
		return ob_get_clean();
	}

	public function enqueues() {
		wp_enqueue_script( 'jquery-javo-ajaxshortcode' );
		wp_enqueue_script( 'owl-carousel' );
		wp_enqueue_script( 'flexmenu' );
		wp_enqueue_script( 'jquery-javo-ajaxshortcode' );
	}

	abstract protected function parse( $args );
	abstract protected function render( $params );
}