<?php

abstract class Jvbpd_Core_AdminHelper {

	Const CAPABILITY = 'manage_options';

	public $childs = Array();
	public $parent_args = Array();
	public $parent_slug = '';

	public function __construct( $args=Array() ) {
		$this->parent_args = shortcode_atts(
			Array(
				'slug' => '',
				'name' => '',
			), $args
		);
		$this->parent_slug = sanitize_title( $this->parent_args[ 'slug' ] );
		add_action( 'admin_menu', array( $this, 'createHelpMenu' ), 99 );
	}

	public function createHelpMenu() {
		$this->CreateParentMenu();
		$this->CreateChildMenus();
		$this->FirstNodeLabel();
	}

	public function CreateParentMenu() {
		add_menu_page(
			$this->parent_args[ 'name' ],
			$this->parent_args[ 'name' ],
			self::CAPABILITY,
			$this->parent_slug,
			Array( $this, 'helper_main' ),
			'',
			3
		);
	}

	public function CreateChildMenus() {
		$arrChilds = apply_filters( 'jvbpd_admin_help_submenus', $this->childs );
		foreach( $arrChilds as $child ) {
			add_submenu_page(
				$this->parent_slug,
				$child[ 'name' ],
				$child[ 'name' ],
				self::CAPABILITY,
				sprintf( '%1$s_%2$s', $this->parent_slug, $child[ 'slug' ] ),
				$child[ 'func' ]
			);
		}
	}

	public function FirstNodeLabel() {
		if( isset( $GLOBALS[ 'submenu' ][ $this->parent_slug ][0][0] ) ) {
			$GLOBALS[ 'submenu' ][ $this->parent_slug ][0][0] = esc_html__( "Welcome", 'jvfrmtd' );
		}
	}

	public function addSub( $args=Array() ) {
		$this->childs[] = shortcode_atts(
			Array(
				'slug' => '',
				'name' => '',
				'func' => array( $this, 'helper_main' ),
			), $args
		);
	}
	abstract public function helper_main();
}