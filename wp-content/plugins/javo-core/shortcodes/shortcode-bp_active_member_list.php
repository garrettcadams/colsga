<?php

class jvbpd_bp_active_member_list extends Jvbpd_OtherShortcode {

	public $bk_tempalte = null;

	private static $instance = null;

	public function __construct() {

		if( ! class_exists( 'buddyPress' ) ) {
			return false;
		}

		$args = Array(
			'name' => 'bp_active_member_list',
			'label' => esc_html__( "Bp Active Member Lists", 'jvfrmtd' ),
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
			Array(
				'type' => 'textfield',
				'heading' => __( "Max count", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'max',
				'default_var' => '5',
			),
		);
	}

	public function backupTemplate() {
		// Back up the global.
		if( isset( $GLOBALS[ 'members_template' ] ) ) {
			$this->bk_tempalte = $GLOBALS[ 'members_template' ];
		}
	}

	public function dispatchTemplate() {
		if( isset( $GLOBALS[ 'members_template' ] ) ) {
			$GLOBALS[ 'members_template' ] = $this->bk_tempalte;
		}
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
		$members_args = array(
			'user_id'         => 0, //is_user_logged_in() ? get_current_user_id() : 0,
			'type'            => 'active',
			'per_page'        => $params[ 'max' ],
			'max'             => $params[ 'max' ],
			'populate_extras' => false,
			'search_terms'    => false,
		);

		wp_enqueue_script( 'bp-widget-members' );

		$this->backupTemplate(); ?>
		<div class="shortcode-header">
			<div class="shortcode-title">
				<?php $this->getTitle( $params ); ?>
			</div>
		</div>
		<?php
		if ( bp_has_members( $members_args ) ) :
			printf( '<div class="row">' );
			while ( bp_members() ) : bp_the_member();
				?>
				<div class="col-3 text-center">
					<a href="<?php bp_member_permalink() ?>" title="<?php bp_member_name(); ?>"><?php bp_member_avatar(); ?></a>
				</div>
				<?php
			endwhile;
			printf( '</div>' );
		else:
			?>
			<div class="widget-error alert bg-faded show-open">
				<?php esc_html_e( 'No one has signed up yet!', 'jvfrmtd' ); ?>
			</div>
		<?php endif;
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}