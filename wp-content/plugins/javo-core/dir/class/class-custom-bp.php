<?php

class jvbpd_buddypress {

	public static $instance = null;

	public function __construct() {
		add_action( 'init', array( $this, 'register_hooks' ) );
	}

	public function bp_is_activate() {
		return function_exists( 'buddypress' );
	}

	public function register_hooks() {

		add_action( 'wp', array( $this, 'admin_bar_in_header' ) );

		add_filter('body_class', Array($this, 'bp_body_class'));

		add_action( 'customize_register', array($this,'mypage_custom_options'));
		// add_filter( 'bp_nouveau_get_container_classes', array($this,'mypage_custom_css'), 10, 2);
		add_filter( 'bp_after_nouveau_appearance_settings_parse_args', array($this,'bp_app_settings_args'));

		add_action( 'wp_before_admin_bar_render', array( $this, 'memory_admin_bar_nodes' ) );
		add_action( 'wp_after_admin_bar_render', array( $this, 'memory_admin_bar_nodes' ) );
		add_filter( 'admin_bar_menu', array( $this, 'replace_howdy' ), 25 );

		add_filter('bp_nouveau_get_loop_classes', array($this,'bp_loop_class'), 10, 2);

		if($this->is_bp_page('profile')){
			add_filter('bp_nouveau_get_container_classes', array($this,'bp_nav_class'), 10, 2);
			add_filter('bp_nouveau_get_single_item_nav_classes', array($this,'bp_nav_class'), 10, 2);
		}

		add_filter('jv_bp_nav_classes', array($this,'bp_nav_ul_class'), 10, 2);

		add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', array( $this, 'jv_cover_image_css' ), 1, 1 );
		add_filter( 'bp_before_groups_cover_image_settings_parse_args', array( $this, 'jv_cover_image_css' ), 1, 1 );
		add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', array( $this, 'bb_cover_image' ), 10, 1 );
		add_filter( 'bp_before_groups_cover_image_settings_parse_args', array( $this, 'bb_cover_image' ), 10, 1 );

		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_buddypress' ) );
		add_filter('bbp_show_lead_topic', array( $this, 'jv_bbpress_active_lead_topic' ) );

		add_action( 'jvbpd_sidebar_id', Array( $this, 'bp_commponent_sidebar' ), 10, 2 );
		add_filter( 'jvbpd_sidebar_position', array( $this, 'bp_sidebar_position' ), 15, 2 );

		add_action( 'bp_before_member_header_meta', array( $this, 'bp_member_header_meta' ) );

		add_action( 'jvbpd_single_post_header', array( $this, 'single_page_header' ), 11, 2 );

		add_filter( 'jvbpd_header_relation_option', array( $this, 'header_relation_option' ) );
		add_filter( 'jvbpd_header_skin_option', array( $this, 'header_skin_option' ) );
		add_filter( 'jvbpd_header_bg_option', array( $this, 'header_bg_option' ) );
		add_filter( 'jvbpd_header_bg_opacity_option', array( $this, 'header_bg_opacity_option' ) );
		add_filter( 'jvbpd_header_shadow_option', array( $this, 'header_shadow_option' ) );

		add_action( 'jvbpd_theme_settings_bp_options_after', array( $this, 'module_selector' ) );
		add_action( 'jvbpd_theme_settings_bp_options_after', array( $this, 'header_parallax' ) );
		add_action( 'jvbpd_theme_settings_bp_options_after', array( $this, 'navigation_type' ) );

		add_action( 'the_post', array( $this, 'template_hook' ) );

		add_action( 'jvbpd_body_after', array( $this, 'members_sidebar' ) );

		if( function_exists( 'BuddyPress' ) ) {
			add_filter( 'heartbeat_received', Array( $this, 'responsse_notification' ), 10, 3 );
			add_action( 'admin_bar_menu', array( $this, 'strip_unnecessary_admin_bar_nodes' ), 999 );
		}

		add_action( 'jvbpd_dashboard_enqueues', array( $this, 'notify' ) );


	}

	public function template_hook( $template ) {
		add_action( 'jvbpd_single_page_before', array( $this, 'bp_single_page_header' ), 10, 2 );
		return $template;
	}

	public function is_show_admin_bar() { return true; }

	public function strip_unnecessary_admin_bar_nodes( &$wp_admin_bar ) {
		global $admin_bar_myaccount, $bb_adminbar_notifications, $bp;

		if ( is_admin() ) { //nothing to do on admin
			return;
		}
		$nodes = $wp_admin_bar->get_nodes();

		if( isset( $nodes[ 'bp-notifications' ] ) ) {
			$bb_adminbar_notifications[] = $nodes[ 'bp-notifications' ];
		}

		$current_href = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ];

		foreach ( $nodes as $name => $node ) {

			if ( $node->parent == "bp-notifications" ) {
				$bb_adminbar_notifications[] = $node;
			}

			if ( $node->parent == "" || $node->parent == "top-secondary" AND $node->id != "top-secondary" ) {

				if ( $node->id == "my-account" ) {
					continue;
				}

				if ( $this->is_show_admin_bar() )
					$wp_admin_bar->remove_node( $node->id );
				}

				//adding active for parent link
				if ( $node->id == "my-account-xprofile-edit" || $node->id == "my-account-groups-create" ) {
					$is_active = strpos( "http://" . $current_href, $node->href ) !== false || strpos( "https://" . $current_href, $node->href ) !== false;
					$this->adminbar_item_add_active( $wp_admin_bar, $name, $is_active );
				}

				if ( $node->id == "my-account-activity-personal" ) {
					$is_active = $bp->current_component == "activity" AND $bp->current_action == "just-me" AND bp_displayed_user_id() == get_current_user_id();
					$this->adminbar_item_add_active( $wp_admin_bar, $name, $is_active );
				}

				if ( $node->id == "my-account-xprofile-public" ) {
					$is_active = $bp->current_component == "profile" AND $bp->current_action == "public" AND bp_displayed_user_id() == get_current_user_id();
					$this->adminbar_item_add_active( $wp_admin_bar, $name, $is_active );
				}

				if ( $node->id == "my-account-messages-inbox" ) {
					$is_active = $bp->current_component == "messages" AND $bp->current_action == "inbox";
					$this->adminbar_item_add_active( $wp_admin_bar, $name, $is_active );
				}

				//adding active for child link
				if ( $node->id == "my-account-settings-general" ) {
					$is_active = $bp->current_component == "settings" || $bp->current_action == "general";
					$this->adminbar_item_add_active( $wp_admin_bar, $name, $is_active );
				}

				$curNode = $wp_admin_bar->get_node( $name );
				$curNode->meta[ 'class' ] = isset( $node->meta[ 'class' ] ) ? $node->meta[ 'class' ] . ' aaaa' : 'aaaa';
				$wp_admin_bar->add_node( $curNode );

			return;

			//add active class if it has viewing page href
			if ( !empty( $node->href ) ) {
				if (
				( "http://" . $current_href == $node->href || "https://" . $current_href == $node->href ) ||
				( $node->id = 'my-account-xprofile-edit' && strpos( "http://" . $current_href, $node->href ) === 0 )
				) {
					$this->adminbar_item_add_active( $wp_admin_bar, $name, true );
					//add active class to its parent
					if ( $node->parent != '' && $node->parent != 'my-account-buddypress' ) {
						foreach ( $nodes as $name_inner => $node_inner ) {
							if ( $node_inner->id == $node->parent ) {
								$this->adminbar_item_add_active( $wp_admin_bar, $name_inner, true );
								break;
							}
						}
					}
				}
			}
		}
	}

	public function adminbar_item_add_active( &$wp_admin_bar, $name='', $is_active=true ) {
		$gnode = $wp_admin_bar->get_node( $name );
		if ( $gnode ) {
			$arrCSS = @explode( ' ', $gnode->meta[ 'class' ] );
			$arrCSS[] = 'idof';
			if( $is_active ) {
				$arrCSS[] = 'active';
			}
			$gnode->meta[ 'class' ] = join( ' ', $arrCSS );
			$wp_admin_bar->add_node( $gnode );
		}
	}

	public function memory_admin_bar_nodes() {
		static $bb_memory_admin_bar_step;
		global $bb_adminbar_myaccount;

		if ( is_admin() ) { //nothing to do on admin
			return;
		}

		if ( !empty( $bb_adminbar_myaccount ) ) { //avoid multiple run
			return false;
		}

		if ( empty( $bb_memory_admin_bar_step ) ) {
			$bb_memory_admin_bar_step = 1;
			ob_start();
		} else {
			$admin_bar_output = ob_get_contents();
			ob_end_clean();

			if ( $this->is_show_admin_bar() ) {
				echo $admin_bar_output;
			}

			//strip some waste
			$admin_bar_output = str_replace( array( 'id="wpadminbar"',
				'role="navigation"',
				'class ',
				'class="nojq nojs"',
				'class="quicklinks" id="wp-toolbar"',
				'id="wp-admin-bar-top-secondary" class="ab-top-secondary ab-top-menu"',
			), '', $admin_bar_output );

			//remove screen shortcut link
			$admin_bar_output	 = @explode( '<a class="screen-reader-shortcut"', $admin_bar_output, 2 );
			$admin_bar_output2	 = "";
			if ( count( $admin_bar_output ) > 1 ) {
				$admin_bar_output2 = @explode( "</a>", $admin_bar_output[ 1 ], 2 );
				if ( count( $admin_bar_output2 ) > 1 ) {
					$admin_bar_output2 = $admin_bar_output2[ 1 ];
				}
			}
			$admin_bar_output = $admin_bar_output[ 0 ] . $admin_bar_output2;

			//remove screen logout link
			$admin_bar_output	 = @explode( '<a class="screen-reader-shortcut"', $admin_bar_output, 2 );
			$admin_bar_output2	 = "";
			if ( count( $admin_bar_output ) > 1 ) {
				$admin_bar_output2 = @explode( "</a>", $admin_bar_output[ 1 ], 2 );
				if ( count( $admin_bar_output2 ) > 1 ) {
					$admin_bar_output2 = $admin_bar_output2[ 1 ];
				}
			}
			$admin_bar_output = $admin_bar_output[ 0 ] . $admin_bar_output2;

			//remove script tag
			$admin_bar_output	 = @explode( '<script', $admin_bar_output, 2 );
			$admin_bar_output2	 = "";
			if ( count( $admin_bar_output ) > 1 ) {
				$admin_bar_output2 = @explode( "</script>", $admin_bar_output[ 1 ], 2 );
				if ( count( $admin_bar_output2 ) > 1 ) {
					$admin_bar_output2 = $admin_bar_output2[ 1 ];
				}
			}
			$admin_bar_output = $admin_bar_output[ 0 ] . $admin_bar_output2;

			//remove user details
			$admin_bar_output	 = @explode( '<a class="ab-item"', $admin_bar_output, 2 );
			$admin_bar_output2	 = "";
			if ( count( $admin_bar_output ) > 1 ) {
				$admin_bar_output2 = @explode( "</a>", $admin_bar_output[ 1 ], 2 );
				if ( count( $admin_bar_output2 ) > 1 ) {
					$admin_bar_output2 = $admin_bar_output2[ 1 ];
				}
			}
			$admin_bar_output = $admin_bar_output[ 0 ] . $admin_bar_output2;

			//add active class into vieving link item
			$current_link = $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ];

			$bb_adminbar_myaccount = $admin_bar_output;
		}
	}

	public function adminbar_myaccount() {
		return isset( $GLOBALS[ 'bb_adminbar_myaccount' ] ) ? $GLOBALS[ 'bb_adminbar_myaccount' ] : '';
	}

	function adminbar_notification() {
		return isset( $GLOBALS[ 'bb_adminbar_notifications' ] ) ? $GLOBALS[ 'bb_adminbar_notifications' ] : null;
	}

	public function remove_admin_bar_links() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'wp-logo' );
		$wp_admin_bar->remove_menu( 'search' );
		if ( !current_user_can( 'administrator' ) ):
			$wp_admin_bar->remove_menu( 'site-name' );
		endif;
	}

	public function replace_howdy( $wp_admin_bar ) {
		if ( is_user_logged_in() ) {
			$my_account	 = $wp_admin_bar->get_node( 'my-account' );
			$newtitle	 = str_replace( 'Howdy,', '', $my_account->title );
			$wp_admin_bar->add_node( array(
				'id'	 => 'my-account',
				'title'	 => $newtitle,
			) );
		}
	}

	public function bp_loop_class($class=Array(), $component){
		$class[] = "jvbpd-grid";
		switch($component){
			case "members": $class[] = "effect-2"; break;
			case "groups": $class[] = "effect-2"; break;
			case "activity": $class[] = "effect-2"; break;
		}
		return $class;
	}

	public function bp_nav_class($strClass='', $classes=Array()){
		$navOptions = (Array) get_option('jvbpd_mypage');
		$navPosType = isset($navOptions['nav_pos_type']) ? $navOptions['nav_pos_type'] : 'vertical';
		$arrVertical = Array('vertical', 'bp-vertical-navs', 'bp-single-vert-nav');
		$arrHorizontal = Array('horizontal', 'bp-horizontal-navs');
		if (bp_is_my_profile()) {
			$classes[] = "vertical";
		}else{
			$classes = 'vertical' == $navPosType ? array_diff($classes, $arrHorizontal) : array_diff($classes, $arrVertical);
			$classes = 'vertical' == $navPosType ? array_merge($classes, $arrVertical) : array_merge($classes, $arrHorizontal);
		}
		$classes = array_diff(array_unique($classes), Array('horizon'));
		return join( ' ', $classes );
	}

	public function bp_nav_ul_class($class='', $bp_page=''){
		//if( !bp_is_my_profile() ) {
		if(
			$this->is_bp_page('profile') &&
			( function_exists('jvbpd_tso') &&
			in_array( jvbpd_tso()->get('bp_skin'), Array('skin3', 'skin4', 'skin5') ) )
		){
			//var_dump(bp_nouveau_get_appearance_settings('user_nav_display'));
			//if(bp_nouveau_get_appearance_settings('user_nav_display')=='0') {
				$class = "class='responsive-tabdrop'";
			//}
		}
		return $class;
	}

	public function admin_bar_in_header() {
		if ( !is_admin() ) {
			remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
			add_action( 'Javo/Footer/Render', 'wp_admin_bar_render' );
		}
	}

	public function bp_body_class($classes=Array()) {
		if($this->is_bp_page()) {
			$skin = 'jv-bp-skin1';
			if(function_exists('jvbpd_tso')) {
				$skinSettings = jvbpd_tso()->get('bp_skin');
				if('' != $skinSettings) {
					$skin = 'jv-bp-' . $skinSettings;
				}
			}
			$classes[] = $skin;
		}
		return $classes;
	}

	public function mypage_custom_options($wp_custom) {
		// Section
		$wp_custom->add_section( 'jvbpd_mypage_section', Array(
			'title' => esc_html__( "My page", 'jvfrmtd'),
			'priority' => 31,
		) );
		{
			// Settings
			$wp_custom->add_setting( 'jvbpd_mypage[nav_pos_type]' , array(
				'type' => 'option',
				'default'   => 'vertical',
			) );
			$wp_custom->add_control( 'jvbpd_mypage_nav_type', Array(
				'type' => 'select',
				'label' => esc_html__("Member Navigation Type",'jvfrmtd'),
				'section' => 'jvbpd_mypage_section',
				'settings' => 'jvbpd_mypage[nav_pos_type]',
				'choices'    => array(
					'vertical' => esc_html__("Vertical",'jvfrmtd'),
					'horizon' => esc_html__("Horizon",'jvfrmtd'),
				),
			) );
		}
	}

	public function bp_app_settings_args($settings=Array()) {
		return wp_parse_args( Array(
			'avatar_style' => 1,
			'user_nav_display' => 1,
			'members_layout' => 3,
			'members_friends_layout' => 3,
			'groups_layout' => 3,
			'members_group_layout' => 3,
			'members_dir_layout' => 1,
			'members_dir_tabs' => 1,
			'activity_dir_layout' => 1,
			'activity_dir_tabs' => 1,
			'groups_dir_layout' => 1,
			'groups_dir_tabs' => 1,
		), $settings );
	}

	public function remove_wp_nodes() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_node( 'my-sites' );
		$wp_admin_bar->remove_node( 'site-name' );
		$wp_admin_bar->remove_node( 'customize' );
		$wp_admin_bar->remove_node( 'new-content' );
		$wp_admin_bar->remove_node( 'updates' );
		$wp_admin_bar->remove_node( 'comments' );
		$wp_admin_bar->remove_node( 'vc_inline-admin-bar-link' );
		$wp_admin_bar->remove_node( 'jvbpd_adminbar_theme_setting' );
	}

	function shapeSpace_remove_toolbar_menu() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'vc_inline-admin-bar-link' );
		$wp_admin_bar->remove_menu( 'jvbpd_adminbar_theme_setting' );
	}

	public function bp_legacy_theme_cover_image1( $params = array() ) {
		if ( empty( $params ) || (isset($params['cover_image']) && ! $params['cover_image'] ) ) {
			return;
		}

		/* Add body class for users with cover */
		add_filter( 'body_class', array( $this, 'jv_bp_cover_add_body_class' ), 30 );

		$cover_image = 'background-image: url(' . $params['cover_image'] . '); ' .
		'background-repeat: no-repeat; background-size: cover; background-position: center center !important;';
		return '
		/* Cover image */
		body.buddypress div#item-header #header-cover-image {
		' . $cover_image . '
		}';
	}

	//inject custom class for profile pages
	public function jv_bp_cover_add_body_class( $classes ) {
		$classes[] = 'is-user-profile';
		return $classes;
	}

	public function jv_cover_image_css( $settings = array() ) {
		/**
		* If you are using a child theme, use bp-child-css
		* as the theme handel
		*/
		// $theme_handle = 'bp-parent-css';
		// css/bp.css -> wp_inline_css handle
		$settings['theme_handle'] = 'jvcore-bp';

		/**
		* Then you'll probably also need to use your own callback function
		* @see the previous snippet
		*/
		$settings['callback'] = array( $this, 'bp_legacy_theme_cover_image1' );

		return $settings;
	}


	//message count
	public function bp_get_message_thread_total_and_unread_count_number( $thread_id = false ) {
		if ( false === $thread_id ) {
			$thread_id = bp_get_message_thread_id();
		}

		$total  = bp_get_message_thread_total_count( $thread_id );
		$unread = bp_get_message_thread_unread_count( $thread_id );

		return sprintf(
			/* translators: 1: total number, 2: accessibility text: number of unread messages */
			'<span class="thread-count label label-rouded label-custom pull-right">%1$s</span> <span class="bp-screen-reader-text">%2$s</span>',
			number_format_i18n( $total ),
			sprintf( _n( '%d unread', '%d unread', $unread, 'listopia' ), number_format_i18n( $unread ) )
		);
	}

	/** Custom cover image in member, group **/
	public function bb_cover_image( $settings = array() ) {

		$strCoverImageURi = JVBPD_IMG_DIR .'jv-bp-default-cover-bg.jpg';
		$strThemeSettingImageURi = jvbpd_tso()->get( 'bp_no_image', false );
		$strCoverImageURi = false != $strThemeSettingImageURi ? $strThemeSettingImageURi : $strCoverImageURi;

		$settings['width']  = 1900;
		$settings['height'] = 450;
		$settings['default_cover'] = $strCoverImageURi;
		return $settings;
	}

	/** Remove original bp css **/
	public function dequeue_buddypress() {
		//wp_dequeue_style('bp-legacy-css');
		//wp_dequeue_style('bbp-default');
		//wp_dequeue_style( 'bp-nouveau' );
		wp_dequeue_style('lava_ajax_search_selectize-css-css');
		wp_dequeue_style('lava_ajax_search_lava-ajax-search-css-css');
		wp_dequeue_style('selectize-css-css');
	}

	public function jv_bp_user_nav () {
		bp_nav_menu ( array (
			'after' => '',
			'before' => '',
			'container' => '',
			'container_class' => '',
			'container_id' => '',
			'depth' => 3,
			'echo' => true,
			'fallback_cb' => null,
			'items_wrap' => '<ul id="side-menu" class="%2$s" role="navigation">%3$s</ul>',
			'link_before' => '<span class="menu-titles">',
			'link_after' => '</span>',
			'menu_class' => 'nav',
			'menu_id' => 'side-menu'
		) );

	}

	public function jv_bp_admin () {
		bp_adminbar_menus ( array (
			'after' => '',
			'before' => '',
			'container' => '',
			'container_class' => '',
			'container_id' => '',
			'depth' => 3,
			'echo' => true,
			'fallback_cb' => null,
			'items_wrap' => '<ul id="side-menu" class="%2$s" role="navigation">%3$s</ul>',
			'link_before' => '<span class="menu-titles">',
			'link_after' => '</span>',
			'menu_class' => 'nav',
			'menu_id' => 'side-menu'
		) );

	}

	public function jv_bbpress_active_lead_topic( $show_lead ) {
		$show_lead[] = 'true';
		return $show_lead;
	}

	public function is_bp_page( $query=false, $directory=false ) {
		global $bp;

		$is_bp_page = false;

		if( function_exists( 'bp_is_current_component' ) ) {

			$status = Array(
				'member' => isset( $bp->members ) && bp_is_current_component( $bp->members->slug ) && bp_is_directory(),
				'group' => isset( $bp->groups ) && bp_is_current_component( $bp->groups->slug ),
				'activity' => isset( $bp->activity ) && bp_is_current_component( $bp->activity->slug ),
				'register' => bp_is_register_page(),
				'profile' => bp_is_user(),
			);

			if( false !== $query ) {
				foreach( (array)$query as $component ) {
					$is_bp_page = $is_bp_page || ( array_key_exists( $component, $status ) && true === $status[ $component ] );
				}
			}else{
				foreach( $status as $component ) {
					$is_bp_page = $is_bp_page || $component;;
				}
			}
			if( $directory && ! bp_is_directory() ) {
				$is_bp_page = false;
			}
		}

		return $is_bp_page;
	}

	public function is_bb_page( $query=false ) {

		$is_bb_page = false;

		$state = Array(
			'forum' => function_exists( 'bbp_is_single_forum' ) && bbp_is_single_forum(),
			'topic' => function_exists( 'bbp_is_single_topic' ) && bbp_is_single_topic(),
			'reply' => function_exists( 'bbp_is_single_reply' ) && bbp_is_single_reply(),
		);

		if( false !== $query ) {
			foreach( (array)$query as $component ) {
				$is_bb_page = $is_bb_page || ( array_key_exists( $component, $state ) && $state[ $component ] );
			}
		}else{
			foreach( $state as $component ) {
				$is_bb_page = $is_bb_page || $component;
			}
		}
		return $is_bb_page;
	}

	public function is_bb_archive( $query=false ) {

		$is_bb_archive = false;
		$state = Array(
			'forum' => function_exists( 'bbp_is_forum_archive' ) && bbp_is_forum_archive(),
			'topic' => function_exists( 'bbp_is_topic_archive' ) && bbp_is_topic_archive(),
			'reply' => function_exists( 'bbp_is_reply_archive' ) && bbp_is_reply_archive(),
			'tag' => function_exists( 'bbp_is_topic_tag' ) && bbp_is_topic_tag(),
		);

		if( false !== $query ) {
			foreach( (array)$query as $component ) {
				$is_bb_archive = $is_bb_archive || ( array_key_exists( $component, $state ) && $state[ $component ] );
			}
		}else{
			foreach( $state as $component ) {
				$is_bb_archive = $is_bb_archive || $component;
			}
		}
		return $is_bb_archive;
	}

	public function bp_commponent_sidebar( $sidebar_id='', $post ) {

		if( $this->is_bb_page() || $this->is_bb_archive() ) {
			$sidebar_id = 'bb-sidebar';
		}

		if( $this->is_bp_page() ) {
			$sidebar_id = 'bp-sidebar';
		}

		return $sidebar_id;
	}

	public function bp_sidebar_position( $position='', $post_id=0 ) {
		if( function_exists( 'bp_is_user_messages' ) && bp_is_user_messages() ) {
			$position = 'full';
		}else{
			foreach(
				Array(
					array( 'component' => 'member', 'key' => 'bp_members_sidebar', 'default' => 'right', 'dir' => false ),
					array( 'component' => 'group', 'key' => 'bp_group_sidebar', 'default' => 'right', 'dir' => false ),
					array( 'component' => 'group', 'key' => 'bp_groups_sidebar', 'default' => 'right', 'dir' => true ),
					array( 'component' => 'activity', 'key' => 'bp_activity_sidebar', 'default' => 'right', 'dir' => false ),
					array( 'component' => 'register', 'key' => 'bp_register_sidebar', 'default' => 'right', 'dir' => false ),
					array( 'component' => 'profile', 'key' => 'bp_profile_sidebar', 'default' => 'full', 'dir' => false ),
					array( 'component' => 'mypage', 'key' => 'bp_mypage_sidebar', 'default' => 'full', 'dir' => false ),
				) as $componentMeta
			) {
				if( $this->is_bp_page( $componentMeta[ 'component' ], $componentMeta[ 'dir' ] ) ) {
					$position = jvbpd_tso()->get( $componentMeta[ 'key' ], $componentMeta[ 'default' ] );
				}
				if( $componentMeta[ 'component' ] == 'mypage' && $this->is_bp_page( Array( 'profile' ) ) && get_current_user_id() == bp_displayed_user_id() ) {
					$position = jvbpd_tso()->get( $componentMeta[ 'key' ], $componentMeta[ 'default' ] );
				}
			}
		}

		if( $this->is_bb_archive() || $this->is_bb_page() ) {
			$position = jvbpd_tso()->get( 'bb_sidebar', 'right' );
		}

		if( $this->is_bb_page( 'topic' ) ) {
			$position = jvbpd_tso()->get( 'bb_single_sidebar', 'right' );
		}

		return $position;
	}

	public function bp_single_page_header() {
		if( !$this->is_bp_page( 'group' ) && ( $this->is_bb_archive() || $this->is_bb_page( 'forum' ) ) ) {
			jvbpd_layout()->load_template( 'template-bb-header' );
		}
	}

	public function getBpMemberMeta( $user_id=0, $meta_key='', $default=false ) {
		$fieldID = null;
		if( function_exists( 'xprofile_get_field_id_from_name' ) ) {
			$fieldID = xprofile_get_field_id_from_name( $meta_key );
		}
		if( class_exists( 'BP_XProfile_ProfileData' ) ) {
			$default = BP_XProfile_ProfileData::get_value_byid( $fieldID, $user_id );
		}
		return $default;
	}

	public function bp_member_header_meta() {

		$arrSocialMeta = Array(
			'facebook' => Array(
				'icon' => 'fab fa-facebook-f',
				'label' => esc_html__( "Facebook", 'jvfrmtd' ),
				'link' => $this->getBpMemberMeta( bp_displayed_user_id(), 'Facebook' ),
			),
			'twitter' => Array(
				'icon' => 'fab fa-twitter',
				'label' => esc_html__( "Twitter", 'jvfrmtd' ),
				'link' => $this->getBpMemberMeta( bp_displayed_user_id(), 'Twitter' ),
			),
		);

		echo '<br>'; // don't remove !

		if( !empty( $arrSocialMeta ) ) {
			foreach( $arrSocialMeta as $strSocial => $arrMeta ) {
				if( empty( $arrMeta[ 'link' ] ) ) {
					continue;
				}
				printf(
					'<a href="%1$s" target="_blank" title="%2$s"><span class="fa-stack"><i class="far fa-circle fa-stack-2x"></i><i class="%3$s"></i></span></a>',
					$arrMeta[ 'link' ], $arrMeta[ 'label' ], $arrMeta[ 'icon' ]
				);
			}
		}
	}

	public function single_page_header( $option=false, $post_id=0 ) {
		if( $this->is_bp_page() || $this->is_bb_page() || $this->is_bb_archive() ) {
			$option = 'notitle';
		}
		return $option;
	}

/*** header options **/


	public function header_relation_option( $option='' ) {
		$newOption = false;

		if( $this->is_bb_page( 'topic' ) ) {
			// Single Toipic Header
			$newOption = jvbpd_tso()->get( 'bb_forum_header_relation', false );
		}

		if( $this->is_bp_page( Array( 'profile', 'group' ) ) && !bp_is_directory() ) {
			// Profile Single Header
			$newOption = jvbpd_tso()->get( 'bp_profile_header_relation', false );
		}

		if( $this->is_bb_archive( Array( 'forum', 'topic', 'tag' ) ) || ( $this->is_bb_page( 'forum' ) && ! $this->is_bp_page( 'group' ) ) ) {
			// Bbpress Directories Header( Forum, Topic List )
			$newOption = jvbpd_tso()->get( 'bb_directories_header_relation', false );
		}

		return $newOption ? $newOption : $option;
	}

	public function header_skin_option( $option='' ) {
		$newOption = false;

		if( $this->is_bb_page( 'topic' ) ) {
			// Single Toipic Header
			$newOption = jvbpd_tso()->get( 'bb_forum_header_skin', false );
		}

		if( $this->is_bp_page( Array( 'profile', 'group' ) ) && !bp_is_directory() ) {
			// Profile Single Header
			$newOption = jvbpd_tso()->get( 'bp_profile_header_skin', false );
		}

		if( $this->is_bb_archive( Array( 'forum', 'topic', 'tag' ) ) || ( $this->is_bb_page( 'forum' ) && ! $this->is_bp_page( 'group' ) ) ) {
			// Bbpress Directories Header( Forum, Topic List )
			$newOption = jvbpd_tso()->get( 'bb_directories_header_skin', false );
		}

		return $newOption ? $newOption : $option;
	}

	public function header_bg_option( $option='' ) {
		$newOption = false;
		if( $this->is_bb_page( 'topic' ) ) {
			// Single Toipic Header
			$newOption = jvbpd_tso()->get( 'bb_forum_header_bg', false );
		}
		if( $this->is_bp_page( Array( 'profile', 'group' ) ) && !bp_is_directory() ) {
			// Profile Single Header
			$newOption = jvbpd_tso()->get( 'bp_profile_header_bg', false );
		}
		if( $this->is_bb_archive( Array( 'forum', 'topic', 'tag' ) ) || ( $this->is_bb_page( 'forum' ) && ! $this->is_bp_page( 'group' ) ) ) {
			// Bbpress Directories Header( Forum, Topic List )
			$newOption = jvbpd_tso()->get( 'bb_directories_header_bg', false );
		}
		return $newOption ? $newOption : $option;
	}

	public function header_bg_opacity_option( $option='' ) {
		$newOption = false;

		if( $this->is_bb_page( 'topic' ) ) {
			// Single Toipic Header
			$newOption = jvbpd_tso()->get( 'bb_forum_header_opacity', false );
		}

		if( $this->is_bp_page( Array( 'profile', 'group' ) ) && !bp_is_directory() ) {
			// Profile Single Header
			$newOption = jvbpd_tso()->get( 'bp_profile_header_opacity', false );
		}

		if( $this->is_bb_archive( Array( 'forum', 'topic', 'tag' ) ) || ( $this->is_bb_page( 'forum' ) && ! $this->is_bp_page( 'group' ) ) ) {
			// Bbpress Directories Header( Forum, Topic List )
			$newOption = jvbpd_tso()->get( 'bb_directories_header_opacity', false );
		}

		return false !== $newOption ? $newOption : $option;
	}

	public function header_shadow_option( $option='' ) {
		$newOption = false;

		if( $this->is_bb_page( 'topic' ) ) {
			// Single Toipic Header
			$newOption = jvbpd_tso()->get( 'bb_forum_header_shadow', false );
		}

		if( $this->is_bp_page( Array( 'profile', 'group' ) ) && !bp_is_directory() ) {
			// Profile Single Header
			$newOption = jvbpd_tso()->get( 'bp_profile_header_shadow', false );
		}

		if( $this->is_bb_archive( Array( 'forum', 'topic' ) ) || ( $this->is_bb_page( 'forum' ) && ! $this->is_bp_page( 'group' ) ) ) {
			// Bbpress Directories Header( Forum, Topic List )
			$newOption = jvbpd_tso()->get( 'bb_directories_header_shadow', false );
		}

		return false !== $newOption ? $newOption : $option;
	}

	public function module_selector( $section='' ) {
		$targets = array( 'bp_members_', 'bp_groups_' );
		if( in_array( $section, $targets ) ) {
			?>
			<div>
				<span><?php esc_html_e( "Loop module style", 'listopia' ); ?> : </span>
				<select name="jvbpd_ts[<?php echo esc_attr( $section ); ?>loop_module]">
					<?php
					foreach(
						Array(
							'' => esc_html__( "Defalut", 'listopia' ),
							'small-box' => esc_html__( "Smail Box", 'listopia' ),
							'bg-box' => esc_html__( "BG-Box", 'listopia' )
						) as $strModuleStyle => $strStyleLabel
					) {
						printf(
							'<option value="%1$s"%3$s>%2$s</option>',
							$strModuleStyle, $strStyleLabel,
							selected( $strModuleStyle == jvbpd_tso()->get( $section . 'loop_module' ), true, false )
						);
					} ?>
				</select>
			</div>
			<?php
		}
	}

	public function header_parallax( $section='' ) {
		$targets = array( 'bp_profile_', 'bp_group_' );
		if( in_array( $section, $targets ) ) {
			?>
			<div>
				<span><?php esc_html_e( "Background parallax", 'listopia' ); ?> : </span>
				<select name="jvbpd_ts[<?php echo esc_attr( $section ); ?>header_parallax]">
					<?php
					foreach(
						Array(
							'' => esc_html__( "Disabled", 'listopia' ),
							'enable' => esc_html__( "Enable", 'listopia' ),
						) as $strHeaderPallax => $strOptionLabel
					) {
						printf(
							'<option value="%1$s"%3$s>%2$s</option>',
							$strHeaderPallax, $strOptionLabel,
							selected( $strHeaderPallax == jvbpd_tso()->get( $section . 'header_parallax' ), true, false )
						);
					} ?>
				</select>
			</div>
			<?php
		}
	}

	public function navigation_type( $section='' ) {
		if( function_exists( 'jvbpd_layout' ) && jvbpd_layout()->getThemeType() != 'jvd-lk' ) {
			return false;
		}

		$targets = array( 'bp_profile_', 'bp_group_' );
		if( in_array( $section, $targets ) ) {
			?>
			<div>
				<span><?php esc_html_e( "Navigation Type", 'listopia' ); ?> : </span>
				<select name="jvbpd_ts[<?php echo esc_attr( $section ); ?>navi_type]">
					<?php
					foreach(
						Array(
							'' => esc_html__( "Type 1", 'listopia' ),
							'bp-header-type-2' => esc_html__( "Type 2", 'listopia' ),
						) as $strHeaderPallax => $strOptionLabel
					) {
						printf(
							'<option value="%1$s"%3$s>%2$s</option>',
							$strHeaderPallax, $strOptionLabel,
							selected( $strHeaderPallax == jvbpd_tso()->get( $section . 'navi_type' ), true, false )
						);
					} ?>
				</select>
			</div>
			<?php
		}
	}

	public function members_sidebar() {
		function_exists( 'jvbpd_layout' ) && jvbpd_layout()->load_template( 'template-member-sidebar' );
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	/** Notifications **/

	public function notify( $handle='' ) {
		if( $handle == 'jv-extend.js' ) {
			wp_add_inline_script(
				jvbpd_dashboard()->getEnqueueHandle( $handle ),
				sprintf( 'var jvbpd_notify = %s;',
					json_encode(
						Array(
							'timeout' => 10,
							'last_notified' => $this->getLastNotificationID(),
						)
					)
				),
				'before'
			);
		}
	}

	public function responsse_notification( $response, $data, $screen_id ) {

		if ( isset( $data['jvbpd-data'] ) ) {

			$notifications    = array();
			$notification_ids = array();

			$request = $data['jvbpd-data'];

			$last_notified_id = absint( $request['last_notified'] );

			if ( ! empty( $request ) ) {

				$notifications = $this->getNewNotifications( get_current_user_id(), $last_notified_id );



				$notification_ids = wp_list_pluck( $notifications, 'id' );

				$notifications = $this->getNotifyMessages( $notifications );

			}
			//include our last notified id to the list
			$notification_ids[] = $last_notified_id;
			//find the max id that we are sending with this request
			$last_notified_id = max( $notification_ids );

			$response['jvbpd-data'] = array( 'messages' => $notifications, 'last_notified' => $last_notified_id );

		}

		return $response;
	}



	public function getNotifyArgs() {
		$arrResults = $arrQuery = Array();
		if( function_exists( 'buddypress' ) && function_exists( 'bp_notifications_get_registered_components' ) ) {
			$arrResults[ 'table_name' ] = buddypress()->notifications->table_name;
			$arrResults[ 'components' ] = bp_notifications_get_registered_components();
			foreach ( $arrResults[ 'components' ] as $component ) {
				$arrQuery[] = "'" . esc_sql( $component ) . "'";
			}
			$arrResults[ 'components_raw' ] = join( ',', $arrQuery );
		}

		return (object) shortcode_atts(
			Array(
				'table_name' => '',
				'components' => Array(),
				'components_raw' => '',
			), $arrResults
		);
	}

	public function getNewNotifications( $user_id=0, $last=0 ) {

		if( ! function_exists( 'BuddyPress' ) ) {
			return Array();
		}
		$obj = $this->getNotifyArgs();
		$query = $GLOBALS[ 'wpdb' ]->prepare(
			"SELECT * FROM {$obj->table_name} WHERE user_id = %d AND component_name IN ({$obj->components_raw}) AND id > %d AND is_new = %d",
			$user_id, $last, 1
		);
		return $GLOBALS[ 'wpdb' ]->get_results( $query );
	}

	public function getLastNotificationID( $user_id=0 ) {
		if( ! function_exists( 'BuddyPress' ) ) {
			return false;
		}

		if( !$user_id ) {
			$user_id = get_current_user_id();
		}
		$obj = $this->getNotifyArgs();
		$query = $GLOBALS[ 'wpdb' ]->prepare(
			"SELECT MAX(id) FROM {$obj->table_name} WHERE user_id = %d AND component_name IN ({$obj->components_raw}) AND is_new = %d",
			$user_id, 1
		);
		return intVal( $GLOBALS[ 'wpdb' ]->get_var( $query ) );
	}

	public function getNotifyMessages( $notifications ) {

		$messages = array();

		if ( empty( $notifications ) ) {
			return $messages;
		}

		$total_notifications = count( $notifications );
		for ( $i = 0; $i < $total_notifications; $i ++ ) {
			$notification = $notifications[ $i ];
			$messages[] = $this->getNotifyDescribe( $notification );
		}
		return $messages;
	}

	public function getNotifyDescribe( $notification ) {
		$bp = buddypress();
		if ( isset( $bp->{$notification->component_name}->notification_callback ) && is_callable( $bp->{$notification->component_name}->notification_callback ) ) {
			$description = call_user_func( $bp->{$notification->component_name}->notification_callback, $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 );

		} elseif ( isset( $bp->{$notification->component_name}->format_notification_function ) && function_exists( $bp->{$notification->component_name}->format_notification_function ) ) {
			$description = call_user_func( $bp->{$notification->component_name}->format_notification_function, $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 );

		} else {

			$description = apply_filters_ref_array( 'bp_notifications_get_notifications_for_user', array(
				$notification->component_action,
				$notification->item_id,
				$notification->secondary_item_id,
				1
			) );
		}

		return apply_filters( 'bp_get_the_notification_description', $description );
	}
}



if( ! function_exists( 'jvbpd_bp' ) ) {
	function jvbpd_bp() {
		return jvbpd_buddypress::getInstance();
	}
	jvbpd_bp();
}

if( !function_exists( 'bp_get_message_thread_total_and_unread_count_number' ) ) {
	function bp_get_message_thread_total_and_unread_count_number( $thread_id=0 ) {
		return jvbpd_bp()->bp_get_message_thread_total_and_unread_count_number( $thread_id );
	}
}

if( !function_exists( 'bp_get_group_member_count_int' ) ) {
	function bp_get_group_member_count_int() {
		global $groups_template;
		if ( isset( $groups_template->group->total_member_count ) ) {
			$count = (int) $groups_template->group->total_member_count;
		} else {
			$count = 0;
		}
		return $count;
	}
}