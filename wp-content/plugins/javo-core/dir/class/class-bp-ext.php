<?php

Class Jvbpd_bp_dir_ext {

	private static $Instance = null;

	public static function getInstance() {
		if( is_null( self::$Instance ) ) {
			self::$Instance = new self;
		}
		return self::$Instance;

	}

	public function __construct() {
		$corePath = dirname(__file__) . '/class-custom-bp.php';
		if( file_exists( $corePath )) {
			require_once( $corePath );
		}
		$this->registerHooks();
	}

	public function registerHooks() {
		add_action( 'bp_setup_nav', array( $this, 'init' ) );
		add_action('jvbpd_core/bp/mypage/init', Array($this, 'bp_event_init'));
	}

	public function init() {
		$this->roleCheck();
		add_filter( 'query_vars', Array( $this, 'add_pagination_var' ));
		add_action( 'bp_setup_nav', Array( $this, 'add_dashboard_tab' ), 100 );
		add_action( 'bp_setup_nav', Array( $this, 'add_mylistings_tab' ), 101 );
		add_action( 'bp_setup_nav', Array( $this, 'add_favorites_tab' ), 101 );
		add_action( 'bp_setup_nav', Array( $this, 'add_reviews_tab' ), 101 );
		add_action( 'bp_setup_nav', Array( $this, 'add_events_tab' ), 101 );
		add_action( 'bp_setup_nav', Array( $this, 'add_orders_tab' ), 101 );
	}

	public function add_pagination_var($args=Array()) {
		$args[] = 'page';
		return $args;

	}

	/*** Adding listing menus **/
	public function add_dashboard_tab() {
		global $bp;
		bp_core_new_nav_item( array(
			'name'                  => esc_html__( "Home", 'jvfrmtd' ),
			'slug'                  => 'home',
			'screen_function'       => Array( $this, 'bp_index' ),
			'position'              => 1,
			'default_subnav_slug'   => 'index'
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Index", 'jvfrmtd' ),
			'slug'              => 'index',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'home' ),
			'parent_slug'       => 'home',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 1,
			'user_has_access'   => bp_is_my_profile()
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Settings", 'jvfrmtd' ),
			'slug'              => 'settings',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'home' ),
			'parent_slug'       => 'home',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 100,
			'user_has_access'   => bp_is_my_profile()
		) );

	}

	public function add_mylistings_tab() {
		global $bp;

		if( ! function_exists( 'lava_directory' ) ) {
			return;
		}

		if( ! apply_filters( 'jvbpd_core/member/menu/listings', true ) ) {
			return;
		}

		bp_core_new_nav_item( array(
			'name'                  => esc_html__( "Listings", 'jvfrmtd' ),
			'slug'                  => 'listings',
			'screen_function'       => Array( $this, 'bp_index' ),
			'position'              => 2,
			'default_subnav_slug'   => 'published'
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Published", 'jvfrmtd' ),
			'slug'              => 'published',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'listings' ),
			'parent_slug'       => 'listings',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 1,
			'user_has_access'   => true, //bp_is_my_profile()
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Pending", 'jvfrmtd' ),
			'slug'              => 'pending',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'listings' ),
			'parent_slug'       => 'listings',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 100,
			'user_has_access'   => bp_is_my_profile()
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Expired", 'jvfrmtd' ),
			'slug'              => 'expired',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'listings' ),
			'parent_slug'       => 'listings',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 100,
			'user_has_access'   => bp_is_my_profile()
		) );

	}

	public function add_favorites_tab() {
		if( ! function_exists( 'lv_directory_favorite' ) ) {
			return;
		}

		if( ! apply_filters( 'jvbpd_core/member/menu/favorites', true ) ) {
			return;
		}

		bp_core_new_nav_item( array(
			'name'                  => esc_html__( "Favorites", 'jvfrmtd' ),
			'slug'                  => 'favorites',
			'screen_function'       => Array( $this, 'bp_index' ),
			'position'              => 3,
			'default_subnav_slug'   => 'index'
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Index", 'jvfrmtd' ),
			'slug'              => 'index',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'favorites' ),
			'parent_slug'       => 'favorites',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 1,
			'user_has_access'   => bp_is_my_profile()
		) );
	}

	public function add_reviews_tab() {
		if( ! function_exists( 'lv_directoryReview' ) ) {
			return;
		}

		if( ! apply_filters( 'jvbpd_core/member/menu/reviews', true ) ) {
			return;
		}

		bp_core_new_nav_item( array(
			'name'                  => esc_html__( "Reviews", 'jvfrmtd' ),
			'slug'                  => 'reviews',
			'screen_function'       => Array( $this, 'bp_index' ),
			'position'              => 4,
			'default_subnav_slug'   => 'submitted'
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Received", 'jvfrmtd' ),
			'slug'              => 'received',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'reviews' ),
			'parent_slug'       => 'reviews',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 1,
			'user_has_access'   => bp_is_my_profile()
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Submitted", 'jvfrmtd' ),
			'slug'              => 'submitted',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'reviews' ),
			'parent_slug'       => 'reviews',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 100,
			'user_has_access'   => bp_is_my_profile()
		) );
	}

	public function add_events_tab() {
		if( ! function_exists( 'Lava_EventConnector' ) ) {
			return;
		}

		if( ! apply_filters( 'jvbpd_core/member/menu/events', true ) ) {
			return;
		}

		bp_core_new_nav_item( array(
			'name'                  => esc_html__( "Events", 'jvfrmtd' ),
			'slug'                  => 'events',
			'screen_function'       => Array( $this, 'bp_index' ),
			'position'              => 4,
			'default_subnav_slug'   => 'list'
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Add Event", 'jvfrmtd' ),
			'slug'              => 'add',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events' ),
			'parent_slug'       => 'events',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 1,
			'user_has_access'   => bp_is_my_profile()
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "My Events", 'jvfrmtd' ),
			'slug'              => 'list',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'events' ),
			'parent_slug'       => 'events',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 100,
			'user_has_access'   => bp_is_my_profile()
		) );

	}

	public function add_orders_tab() {
		global $bp;

		if( ! function_exists( 'lv_directory_payment' ) ) {
			return;
		}

		if( ! apply_filters( 'jvbpd_core/member/menu/orders', true ) ) {
			return;
		}

		bp_core_new_nav_item( array(
			'name'                  => esc_html__( "Orders", 'jvfrmtd' ),
			'slug'                  => 'order',
			'screen_function'       => Array( $this, 'bp_index' ),
			'position'              => 5,
			'default_subnav_slug'   => 'index'
		) );

		bp_core_new_subnav_item( array(
			'name'              => esc_html__( "Orders", 'jvfrmtd' ),
			'slug'              => 'index',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . 'orders' ),
			'parent_slug'       => 'order',
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 100,
			'user_has_access'   => bp_is_my_profile()
		) );

	}

	public function bp_index() {
		/*
		$fucName = sprintf( 'bp_page_%s_%s', BuddyPress()->current_component, BuddyPress()->current_action );
		$fnCallback = array( $this, $fucName );
		if( is_callable( $fnCallback ) ) {
			add_action( 'bp_template_content', $fnCallback );
		}**/
		add_action( 'bp_template_content', array( $this, 'bp_template_content' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'memberpage_enqueue' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		do_action('jvbpd_core/bp/mypage/init', BuddyPress()->current_component, BuddyPress()->current_action);
	}

	public function memberpage_enqueue() {
		// wp_enqueue_script( 'jquery' );
		// wp_enqueue_script( 'jquery-ui-core' );
		// wp_enqueue_script( 'jquery-ui-sortable' );
		// jquery-ui-core
		// wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'chart' ) );
	}

	public function bp_template_content() {
		$fileName = sprintf( '%1$s/dir/mypage/%2$s/%3$s.php', jvbpdCore()->path, BuddyPress()->current_component, BuddyPress()->current_action );
		if( file_exists( $fileName ) ) {
			require_once( $fileName );
		}
	}

	public function roleCheck() {
		if( is_admin() ) {
			return;
		}
		$user_info = get_userdata( get_current_user_id() );
		if( ! $user_info ) {
			return;
		}
		$user_roles = $user_info->roles;
		$allRolesSettings = jvbpd_tso()->get( 'bp_page' );
		foreach( $user_roles as $role ) {
			if( isset( $allRolesSettings[$role] )){
				$pageSettings = $allRolesSettings[$role];
				foreach( $pageSettings as $page => $value ) {
					if( '' != $page ) {
						add_filter( 'jvbpd_core/member/menu/' . $page, '__return_false' );
					}
					if( in_array( $page, Array( 'home', 'activity', 'profile', 'notifications', 'forums', 'settings' ) ) ) {
						if( function_exists( 'bp_core_remove_nav_item') ) {
							bp_core_remove_nav_item( $page );
						}
					}
				}
			}
		}
	}
	public function bp_event_init() {

	}
}