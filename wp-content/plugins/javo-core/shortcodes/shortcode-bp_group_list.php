<?php

use Elementor\Plugin;
class jvbpd_bp_group_list extends Jvbpd_OtherShortcode {

	Const STR_AJAX_HOOK_FORMAT = '%s_loop';

	public $bk_tempalte = null;
	public $ajaxHook = false;

	private static $instance = null;

	public function __construct() {

		if( ! function_exists( 'buddyPress' ) ) {
			return false;
		}

		parent::__construct( Array(
			'name' => 'bp_group_list',
			'label' => esc_html__( "Bp Group Lists", 'jvfrmtd' ),
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
				'default_var' => esc_html__( "Group", 'jvfrmtd' ),
			),
			Array(
				'type' => 'dropdown',
				'heading' => __( "Type", 'jvfrmtd'),
				'holder' => 'div',
				'param_name' => 'type',
				'value' => Array(
					__( "Newest", 'jvfrmtd' ) => 'newest',
					__( "Activity", 'jvfrmtd' ) => 'activity',
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
					__( "Dropdown", 'jvfrmtd' ) => 'dropdown',
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
		return array(
			'newest' => Array(
				'href' => esc_url( bp_get_groups_directory_permalink() ),
				'id' => 'newest-groups',
				'class' => '',
				'label' => esc_html__( "Newest", 'jvfrmtd' ),
			),
			'active' => Array(
				'href' => esc_url( bp_get_groups_directory_permalink() ),
				'id' => 'recently-active-groups',
				'class' => '',
				'label' => esc_html__( "Active", 'jvfrmtd' ),
			),
			'popular' => Array(
				'href' => esc_url( bp_get_groups_directory_permalink() ),
				'id' => 'popular-groups',
				'class' => '',
				'label' => esc_html__( "Popular", 'jvfrmtd' ),
			),
			'alphabetical' => Array(
				'href' => esc_url( bp_get_groups_directory_permalink() ),
				'id' => 'alphabetical-groups',
				'class' => '',
				'label' => esc_html__( "Alphabetical", 'jvfrmtd' ),
			),
		);
	}

	public function backupTemplate() {
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
							$is_selected = $tabID == $params[4];
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
				<div class="item-options">
					<?php
					$buffer = Array();
					foreach( $this->getTabs() as $tabID => $tabMeta ) {
						$is_selected = $tabID == $params['type'];
						$tabMeta[ 'class' ] .= $is_selected ? ' selected' : '';
						$buffer[] = sprintf(
							'<a href="%1$s" data-filter="%2$s" class="%3$s" title="%4$s" target="_self">%4$s</a>',
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
		bp_group_avatar( $avatarArgs );
	}

	public function render_module($moduleID,$id) {
		$output = '';
		if( false !== get_post_status($moduleID) ) {
			$output_content = Plugin::instance()->frontend->get_builder_content_for_display( $moduleID );
			$contentObject = new Jvbpd_BP_Replace_Content( $id, $output_content, 'groups' );
			$output = $contentObject->render();
		}
		echo $output;
	}

	public function loop( $args=Array(), $moduleID ) {
		global $groups_template;
		if ( bp_has_groups( $args ) ) :
			//var_dump($GLOBALS['groups_template']);
			// buddypress()->current_component = 'groups';
			// var_dump($groups_template);
			bp_update_is_directory(true, 'groups');
			echo '<script type="text/html" data-pagination>';
			function_exists('bp_nouveau_pagination') && bp_nouveau_pagination('top');
			echo '</script>';
			while ( bp_groups() ) : bp_the_group();
				?>
				<li <?php bp_group_class( Array( 'vcard' ) ); ?>><?php $this->render_module($moduleID,bp_get_group_id()); ?></li>
				<?php
			endwhile;
		else:
			?>
			<li class="widget-error alert bg-faded show-open">
				<?php _e('There are no groups to display.', 'jvfrmtd') ?>
			</li>
			<?php
		endif;

	}

	public function render( $params ) {

		if( ! isset( buddyPress()->groups ) ) {
			return;
		}

		$user_id = apply_filters( 'bp_group_widget_user_id', '0' );
		$title = esc_html__( "Group", 'jvfrmtd' );
		$group_args = array(
			'user_id' => $user_id,
			'type' => 'newest',
			'per_page' => $params[ 'max' ],
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
			<div class="shortcode-nav" data-param='<?php echo json_encode( Array( 'action' => $this->ajaxHook, '_wpnonce' => wp_create_nonce( 'groups_widget_groups_list' ), 'type' => $params[ 'list_type' ], 'moduleid' => $params[ 'moduleid' ], 'max_groups' => $params[ 'max' ] ) ); ?>'>
				<?php $this->getFilters( $params ); ?>
			</div>
		</div> */ ?>
		<div class="shortcode-nav" data-param='<?php echo json_encode( Array( 'action' => $this->ajaxHook, '_wpnonce' => wp_create_nonce( 'groups_widget_groups_list' ),  'moduleid' => $params[ 'moduleid' ], 'max_groups' => $params[ 'max' ] ) ); ?>'></div>

		<ul class="item-list jvbpd-grid <?php echo esc_attr($ani_type); ?>" aria-live="polite" aria-relevant="all" aria-atomic="true">
			<?php $this->loop( $group_args, $params[ 'moduleid' ] ); ?>
		</ul>
		<?php
	}

	public function ajax_render() {
		check_ajax_referer( 'groups_widget_groups_list' );

		switch ( $_POST['filter'] ) {
			case 'newest-groups':
				$type = 'newest';
			break;
			case 'recently-active-groups':
				$type = 'active';
			break;
			case 'popular-groups':
				$type = 'popular';
			break;
			case 'alphabetical-groups':
				$type = 'alphabetical';
			break;
		}

		$grpage = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 0;
		$s = isset( $_POST['search_terms'] ) ? intval( $_POST['search_terms'] ) : false;
		$scope = isset( $_POST['scope'] ) ? intval( $_POST['scope'] ) : 'all';

		$groups_args = array(
			'type' => $type,
			'page' => $grpage,
			'search_terms' => $s,
			'scope' => 'personal',
		);

		if('my'==$scope){
			$groups_args['user_id'] = get_current_user_id();
		}

		ob_start();
		$this->loop( $groups_args, $_POST['moduleid'] );
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