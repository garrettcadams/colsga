<?php

use Elementor\Plugin;
class jvbpd_bp_member_list extends Jvbpd_OtherShortcode {

	Const STR_AJAX_HOOK_FORMAT = '%s_loop';

	public $bk_tempalte = null;
	public $ajaxHook = false;

	private static $instance = null;

	public function __construct() {

		if( ! function_exists( 'buddyPress' ) ) {
			return false;
		}

		parent::__construct( Array(
			'name' => 'bp_member_list',
			'label' => esc_html__( "Bp Member Lists", 'jvfrmtd' ),
			'params' => $this->getParams(),
		) );

		$this->setup();
		$this->ajaxHooks();
	}

	public function setup() {
		$this->ajaxHook = sprintf( self::STR_AJAX_HOOK_FORMAT, get_class( $this ) );
	}

	public function ajaxHooks() {
		add_action( 'wp_ajax_' . $this->ajaxHook, array( $this, 'ajax_render' ) );
		add_action( 'wp_ajax_nopriv_' . $this->ajaxHook, array( $this, 'ajax_render' ) );
	}

	public function getParams() {
		return Array(
			Array(
				'type' => 'textfield',
				'heading' => __( "Title", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'title',
				'default_var' => esc_html__( "Member", 'jvfrmtd' ),
			),
			Array(
				'type' => 'dropdown',
				'heading' => __( "Type", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'type',
				'value' => Array(
					__( "Newest", 'jvfrmtd' ) => 'newest',
					__( "Active", 'jvfrmtd' ) => 'active',
					__( "Popular", 'jvfrmtd' ) => 'popular',
				),
				'default_var' => 'newest',
			),
			Array(
				'type' => 'dropdown',
				'heading' => __( "Filter Type", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'filter_type',
				'value' => Array(
					__( "Tabs Type", 'jvfrmtd' ) => 'tabs',
					__( "Dropdown Type", 'jvfrmtd' ) => 'dropdown',
					__( "Toggle Type", 'jvfrmtd' ) => 'toggle',
				),
				'default_var' => 'tabs',
			),
			Array(
				'type' => 'colorpicker',
				'heading' => __( "Header Background Color", 'jvfrmtd' ),
				'holder' => 'div',
				'param_name' => 'header_bg_color',
				'dependency' => Array(
					'element' => 'filter_type',
					'value' => 'toggle',
				),
				'default_var' => '',
			),
			Array(
				'type' => 'colorpicker',
				'heading' => __( "Header Font Color", 'jvfrmtd' ),
				'holder' => 'div',
				'param_name' => 'header_font_color',
				'dependency' => Array(
					'element' => 'filter_type',
					'value' => 'toggle',
				),
				'default_var' => '#000000',
			),
			Array(
				'type' => 'dropdown',
				'heading' => __( "List Type", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'list_type',
				'value' => Array(
					__( "List Type", 'jvfrmtd' ) => 'list',
					__( "Grid Type", 'jvfrmtd' ) => 'grid',
					__( "Small Grid Type", 'jvfrmtd' ) => 'small-grid',
				),
				'default_var' => 'list',
			),
			Array(
				'type' => 'textfield',
				'heading' => __( "Max count", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'max',
				'default_var' => '10',
			),
		);
	}

	public function getTabs() {
		$arrTabs = array(
			'newest' => Array(
				'href' => esc_url( bp_get_members_directory_permalink() ),
				'id' => 'newest-members',
				'class' => '',
				'label' => esc_html__( "Newest", 'jvfrmtd' ),
			),
			'active' => Array(
				'href' => esc_url( bp_get_members_directory_permalink() ),
				'id' => 'recently-active-members',
				'class' => '',
				'label' => esc_html__( "Active", 'jvfrmtd' ),
			),
		);
		if( bp_is_active( 'friends' ) ) {
			$arrTabs[ 'popular' ]  = Array(
				'href' => esc_url( bp_get_members_directory_permalink() ),
				'id' => 'popular-members',
				'class' => '',
				'label' => esc_html__( "Popular", 'jvfrmtd' ),
			);
		}
		return $arrTabs;
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
		$arrDefault[ 'moduleid' ] = '';
		$arrDefault[ 'ani' ] = '1';
		$args = shortcode_atts( $arrDefault, $args );
		$this->classes = Array( 'widget' );

		if( isset( $args[ 'list_type' ] ) && '' != $args[ 'list_type' ] ) {
			$this->classes[] = sprintf( 'type-%s', $args[ 'list_type' ] );
		}

		if( isset( $args[ 'filter_type' ] ) && '' != $args[ 'filter_type' ] ) {
			$this->classes[] = sprintf( 'filter-%s', $args[ 'filter_type' ] );
		}

		if( isset( $args[ 'moduleId' ] ) && '' != $args[ 'moduleId' ] ) {
			$this->classes[] = sprintf( 'moduleId-%s', $args[ 'moduleId' ] );
		}
		return $args;
	}

	public function getTitle( $params ) {
		$strTitle = $params[ 'title' ];
		echo $strTitle;
	}

	public function getFilters( $params ) {
		$separator = '|';

		switch( $params[ 'filter_type' ] ) {
			case 'dropdown' :
				 ?>
				<div class="dropdown">
					<a class="dropdown-toggle">
						<i class="jvbpd-icon2-paragraph"></i>
					</a>
					<div class="dropdown-menu item-options dropdown-menu-right">
						<?php
						foreach( $this->getTabs() as $tabID => $tabMeta ) {
							$is_selected = $tabID == $params['type'];
							$tabMeta[ 'class' ] .= ' dropdown-item';
							$tabMeta[ 'class' ] .= $is_selected ? ' selected' : '';
							printf(
								'<a href="%1$s" data-filter="%2$s" class="%3$s" title="%4$s" target="_self">%4$s</a>',
								$tabMeta[ 'href' ], $tabMeta[ 'id' ], $tabMeta[ 'class' ], $tabMeta[ 'label' ]
							);

						} ?>
					</div>
				</div>
				<?php
				break;
			case 'tabs':
			case 'toggle' :
			default:
				?>
				<div class="item-options" id="members-list-options">
					<?php
					$buffer = Array();
					foreach( $this->getTabs() as $tabID => $tabMeta ) {
						$is_selected = $tabID == $params['type'];
						$tabMeta[ 'class' ] .= $is_selected ? ' selected' : '';
						$buffer[] = sprintf(
							'<a href="%1$s" id="%2$s" data-filter="%2$s" class="%3$s" title="%4$s" target="_self">%4$s</a>',
							$tabMeta[ 'href' ], $tabMeta[ 'id' ], $tabMeta[ 'class' ], $tabMeta[ 'label' ]
						);
					}
					printf( join( '<span class="bp-separator" role="separator"> ' . $separator . ' </span>', $buffer ) );
					?>
				</div>
			<?php
		}
	}

	public function getAvatar( $type ) {
		$avatarArgs = Array();
		switch( $type ) {
			case 'grid' : $avatarArgs[ 'type' ] = 'full'; break;
			case 'list' :
			case 'small-grid' :
			default:
				$avatarArgs[ 'type' ] = 'thumb';
		}
		bp_member_avatar( $avatarArgs );
	}

	public function render_module($moduleID,$id) {
		$output = '';
		if( false !== get_post_status($moduleID) ) {
			$output_content = Plugin::instance()->frontend->get_builder_content_for_display(  $moduleID );
			$contentObject = new Jvbpd_BP_Replace_Content( $id, $output_content, 'members' );
			$output = $contentObject->render();
		}
		echo $output;
	}

	public function loop( $args=Array(), $moduleID ) {
		if ( bp_has_members( $args )) :
			bp_update_is_directory(true, 'members');
			echo '<script type="text/html" data-pagination>';
			function_exists('bp_nouveau_pagination') && bp_nouveau_pagination('top');
			echo '</script>';
			while ( bp_members() ) : bp_the_member();
				?>
				<li class="vcard">
					<?php $this->render_module($moduleID,bp_get_member_user_id()); ?>
				</li>
				<?php
			endwhile;
		else:
			?>
			<li class="widget-error alert bg-faded show-open">
				<?php esc_html_e( 'No one has signed up yet!', 'jvfrmtd' ); ?>
			</li>
			<?php
		endif;
	}

	public function render( $params ) {

		if( ! isset( buddyPress()->members ) ) {
			return;
		}

		//echo "search_terms=". $members_search;

		$members_args = array(
			'user_id'         => 0,
			'type'            => $params[ 'type' ],
			'per_page'        => $params[ 'max' ],
			'max'             => $params[ 'max' ],
			'populate_extras' => false,
			'search_terms'    => false,
		);

		$arrHeaderStyle = Array();
		$arrHeaderStyle[ 'background-color' ] = $params[ 'header_bg_color' ];
		$arrHeaderStyle[ 'color' ] = $params[ 'header_font_color' ];
		$ani_type = 'effect-' . $params['ani'];

		foreach( $arrHeaderStyle as $cssProperty => $cssPropertyValue ) {
			if( isset($cssPropertyValue) && $cssPropertyValue!='' )
				$arrParseStyle[] = sprintf( '%1$s:%2$s', $cssProperty, $cssPropertyValue );
		}
		$strHeaderStyle = sprintf( 'style="%s;"', join( ';', $arrParseStyle ) );

		$this->backupTemplate(); ?>


		<?php /*
		<div class="shortcode-header" <?php echo $strHeaderStyle; ?>>
			<div class="shortcode-title">
				<?php $this->getTitle( $params ); ?>
			</div>
			<div class="shortcode-nav" data-param='<?php echo json_encode( Array( 'action' => $this->ajaxHook, '_wpnonce' => wp_create_nonce( 'bp_core_widget_members' ), 'type' => $params[ 'list_type' ], 'moduleid' => $params[ 'moduleid' ], 'max_groups' => $params[ 'max' ] ) ); ?>'>
				<?php $this->getFilters( $params ); ?>
			</div>
		</div> */ ?>
		<div class="shortcode-nav" data-param='<?php echo json_encode( Array( 'action' => $this->ajaxHook, '_wpnonce' => wp_create_nonce( 'bp_core_widget_members' ), 'type' => $params[ 'list_type' ], 'moduleid' => $params[ 'moduleid' ], 'max_groups' => $params[ 'max' ] ) ); ?>'></div>


		<div>
			<?php // bp_directory_members_search_form(); ?>
		</div>
		<form action="" method="post" id="members-directory-form" class="dir-form">

			<ul class="item-list jvbpd-grid <?php echo esc_attr($ani_type); ?>" aria-live="polite" aria-relevant="all" aria-atomic="true">
				<?php $this->loop( $members_args, $params[ 'moduleid' ] ); ?>
			</ul>

			<?php /*
			<div id="pag-bottom" class="pagination">
				<div class="pag-count" id="member-dir-count-bottom">
					<?php bp_members_pagination_count(); ?>
				</div>
				<div class="pagination-links" id="member-dir-pag-bottom">
					<?php bp_members_pagination_links(); ?>
				</div>
			</div>
			<?php bp_member_hidden_fields(); ?> */
			?>

		</form>
		<?php
	}

	public function ajax_render() {
		$filter      = ! empty( $_POST['filter'] ) ? $_POST['filter'] : 'recently-active-members';
		$max_members = ! empty( $_POST['max-members'] ) ? absint( $_POST['max-members'] ) : 5;

		switch ( $filter ) {
			case 'newest-members' : $type = 'newest'; break;

			case 'popular-members' :
				if ( bp_is_active( 'friends' ) ) {
					$type = 'popular';
				} else {
					$type = 'active';
				}
				break;

			case 'recently-active-members' :
			default : $type = 'active'; break;
		}

		$grpage = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 0;
		$s = isset( $_POST['search_terms'] ) ? intval( $_POST['search_terms'] ) : false;
		$scope = isset( $_POST['scope'] ) ? intval( $_POST['scope'] ) : 'all';

		$members_args = array(
			'type' => $type,
			'page' => $grpage,
			// 'per_page' => $max_members,
			// 'max' => $max_members,
			'populate_extras' => false,
			'search_terms'    => $s,
		);

		if('my'==$scope){
			$groups_args['user_id'] = get_current_user_id();
		}

		ob_start();
		$this->loop( $members_args, $_POST['moduleid'] );
		$output = ob_get_clean();
		wp_send_json(Array(
			'output' => $output,
			'last' => false,
		));
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}