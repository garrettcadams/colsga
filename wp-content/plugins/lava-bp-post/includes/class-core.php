<?php

class Lava_Bp_Post_Func extends Lava_Bp_Post {

	public $slug = false;

	Const SLUG = 'post';
	Const NAME = 'post';

	Const FEATURED_TERM = 'category';

	Const STR_SECURITY = 'sqr';
	Const STR_BP_SLUG_FORMAT = 'lv_%s';

	Const STATUS_PUBLISH = 'publish';
	Const STATUS_PENDING = 'pending';

	protected $featured_term = false;

	public static $instance = false;

	private static $is_wpml_actived = false;

	public function __construct() {

		$this->slug = self::getSlug();
		$this->featured_term = self::getFeaturedTerm();

		self::$instance = &$this;
		add_action( 'plugins_loaded', Array( __CLASS__, 'parse_request' ) );

		add_action( 'init', Array( $this, 'core_post_type_publish' ), 99 );

		add_action( 'wp_enqueue_scripts' , Array( $this, 'parse_single_page' ) );
		add_filter( 'the_content' , Array( $this, 'singleTemplate' ), 15 );
		add_filter( 'lava_' . self::SLUG . '_more_meta'	, Array( __CLASS__, 'lava_more_meta_vars' ) );

		if( ! has_filter( 'lava_get_selbox_child_term_lists' ) )
			add_filter('lava_get_selbox_child_term_lists'	, Array( $this, 'selbox_child_term_lists_callback' ), 10, 7);


		add_action( 'bp_setup_nav', array( $this, 'add_bp_nav' ), 100 );

		$this->load_files();
	}

	public function load_files() {
		require_once( 'functions-core.php' );
	}

	public static function getFeaturedTerm() {
		return  join( '_', Array( self::NAME, self::FEATURED_TERM ) );
	}

	public static function getSlug() {
		return self::SLUG;
	}

	public function getName() {
		return self::NAME;
	}

	public static function parse_request() {
		self::$is_wpml_actived = function_exists( 'icl_object_id' );
	}

	public function load_admin_template( $template_name, $param=Array() ) {
		$this->include_template( parent::$instance->path . '/includes/admin', $template_name, $param );
	}

	public function load_template( $template_name, $param=Array() ) {
		$this->include_template( $this->template_path, $template_name, $param );
	}

	public function include_template( $strPath, $template_name, $param=Array() )	{
		$strFilename	= trailingslashit( $strPath ) . $template_name;
		if( !empty( $param ) && is_array( $param ) ) {
			extract( $param );
		}

		if( file_exists( $strFilename ) )
			require_once( $strFilename );
	}

	public function is_dashboard( $body_class ) {
		return wp_parse_args( Array( 'lava-dashboard' ), $body_class );
	}

	public function selbox_child_term_lists_callback(
		$taxonomy
		, $attribute=Array()
		, $el='ul'
		, $default=Array()
		, $parent=0
		, $depth=0
		, $separator='&nbsp;&nbsp;&nbsp;&nbsp;'
	){

		if( !taxonomy_exists( $taxonomy ) ){
			printf( __( "%s is invalid taxonomy name.", 'lvbp-bp-post' ), $taxonomy );
			return;
		}

		$lava_this_args			= Array(
			'parent'			=> $parent
			, 'hide_empty'		=> false
			, 'fields'			=> 'id=>name'
		);

		$lava_this_terms		= (Array) get_terms( $taxonomy, $lava_this_args );
		$lava_this_return		= '';
		$lava_this_attribute	= '';

		if( ! sizeof( $lava_this_terms ) )
			return;

		if( !isset( $attribute['style'] ) )
			$attribute['style'] = '';

		if( !empty( $attribute ) ){
			foreach( $attribute as $attr => $value){
				$lava_this_attribute .= $attr . '="'. $value .'" ';
			}
		}

		$depth++;

		if( is_wp_error( $lava_this_terms ) )
			echo $lava_this_terms->get_error_message();

		else
			if( !empty( $lava_this_terms ) )
				foreach( $lava_this_terms as $term_id => $term_name ){
					switch( $el ){
					case 'select':
						$lava_this_return	.= sprintf('<option value="%s"%s>%s%s</option>%s'
							, ( $taxonomy == 'listing_keyword' ? $term_name : $term_id )
							, selected( in_Array( $term_id, (Array)$default), true, false )
							, str_repeat( $separator, $depth-1 ).' '
							, $term_name
							, $this->selbox_child_term_lists_callback($taxonomy, $attribute, $el, $default, $term_id, $depth, $separator)
						);
					break;
					case 'ul':
					default:
						$lava_this_return	.= sprintf('<li %svalue="%s" data-filter data-origin-title="%s">%s %s</li>%s'
							, $lava_this_attribute
							, $term_id
							, $term_name
							, str_repeat( '-', $depth-1 )
							, $term_name
							, $this->selbox_child_term_lists_callback($taxonomy, $attribute, $el, $default, $term_id, $depth, $separator)
						);
					}; // End Switch
				};

		return $lava_this_return;
	}

	public function is_allow_publish() {
		$strReturn = lava_bpp()->admin->get_settings( 'new_' . self::SLUG . '_status' ) !== 'pending';
		return (boolean) apply_filters( 'lava_' . self::SLUG . '_new_status', $strReturn );
	}

	public function core_post_type_publish() {
		$strNonceKey = $this->slug . '-publish';
		$intPostId = isset( $_GET[ 'post_id' ] ) ? intVal( sanitize_text_field( $_GET[ 'post_id' ] ) ) : 0;
		$boolPublish = isset( $_GET[ 'publish' ] ) && sanitize_text_field( $_GET[ 'publish' ] ) == 'true';

		if( get_post_type( $intPostId ) != self::SLUG ) {
			return false;
		}

		if( 0 < $intPostId && $boolPublish ) {
			if( isset( $_GET[ self::STR_SECURITY ] ) && wp_verify_nonce( $_GET[ self::STR_SECURITY ], $strNonceKey ) ) {
				if( $this->is_allow_publish() ) {
					$post_id = wp_update_post( array( 'ID' => $intPostId, 'post_status' => self::STATUS_PUBLISH ), true );
					if( is_wp_error( $post_id ) ) {
						wp_die( $post_id->get_error_message() );
					}else{
						wp_safe_redirect( get_permalink( $post_id ) );
						do_action( 'lava_' . self::SLUG . '_single_publish_action', $post_id );
						die;
					}
				}else{
					wp_die( esc_html_e( "invalid access", 'lvbp-bp-post' ) );
				}
			}else{
				wp_die( esc_html__( "Error", 'lvbp-bp-post' ) );
			}
		}
	}

	public static function lava_more_meta_vars() {
		$args = Array();
		if('yes' == Lava_bpp()->admin->get_settings( 'add_new_user_field' ) ) {
			$strFieldName = Lava_bpp()->admin->get_settings( 'add_new_user_field_label' );
			if( !empty( $strFieldName ) ) {
				$args[ '_user_field1' ] = Array(
					'label'		=> Lava_bpp()->admin->get_settings( 'add_new_user_field_label' ),
					'element'	=> 'input',
					'type'		=> 'text',
					'class'		=> 'all-options',
				);
			}
		}
		return $args;
	}


	public function parse_single_page() {
		global $post;

		if( !empty( $post->post_type ) && $post->post_type === self::SLUG ) {
			add_action( 'wp_enqueue_scripts' , Array( $this, 'single_enqueue' ), 11 );

			/* Wordpress Emoji & Google StreetView Clash */
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
		}
	}

	public function singleTemplate( $content ) {
		global $post;

		if( apply_filters( 'lava_bpp_single_change_template', in_the_loop() && is_singular( self::SLUG ), self::SLUG, $post ) ) {
			remove_filter( 'the_content', Array( $this, 'singleTemplate' ), 15 );
			ob_start();
			require( lava_bpp()->template_path . '/content.php' );
			$content = ob_get_clean();
			add_filter( 'the_content', Array( $this, 'singleTemplate' ), 15 );
		}
		return $content;
	}

	public function getTermsNameInItems( $post_id, $taxonomy=false, $sep=', ' )
	{

		$output_terms = Array();

		if( $terms = wp_get_object_terms( $post_id, $taxonomy, Array( 'fields' => 'names' ) ) )
		{
			$output_terms = @implode( $sep, $terms );
			$output_terms = trim( $output_terms );
			// $output_terms = substr( $output_terms, 0, -1 );
		}else{
			$output_terms = '';
		}
		return $output_terms;
	}

	function single_enqueue(){
		global $WP_Views;

			// Stylesheets
			wp_enqueue_style( 'flexslider-css' );

			wp_enqueue_script( lava_bpp()->enqueue->getHandleName( 'jquery.flexslider-min.js' ) );
			wp_enqueue_script( lava_bpp()->enqueue->getHandleName( 'lava-single.js' ) );

			remove_action('wp_print_styles', array($WP_Views, 'add_render_css'));
			remove_action('wp_head', 'wpv_add_front_end_js');

			do_action( 'lava_' . self::SLUG . '_single_enqueues' );
	}

	public function getAddFormLink() {
		$strOutput = null;
		$intPageID = intVal( lava_bpp_get_option( 'page_add_' . self::SLUG ) );
		$objPage = get_post( $intPageID );
		if( $objPage instanceof WP_Post ) {
			$strOutput = get_permalink( $objPage );
		}
		return $strOutput;
	}

	public function get_edit_link( $post_id=false ) {

		$post = $GLOBALS[ 'post' ];
		$strOutput = false;
		$strFormURL = $this->getAddFormLink();

		if( 0 < intVal( $post_id ) ) {
			$post = get_post( $post_id );
		}

		if( ! is_null( $strFormURL ) ) {
			if( get_current_user_id() == $post->post_author || current_user_can( 'manage_options' ) ) {
				$strOutput = add_query_arg( Array( 'edit' => $post->ID ), $strFormURL );
			}
		}

		return esc_url_raw( apply_filters( 'lava_' . self::SLUG . '_edit_page_link', $strOutput, $post, $strFormURL ) );
	}

	public static function setupdata( &$post ) {
		global $lava_bpp_manager;
		setup_postdata( $post );


		$arrMoreMeta			= apply_filters( 'lava_' . self::SLUG . '_more_meta', Array() );

		$attachment_noimage		= lava_bpp()->admin->getNoImage();
		$avatar_meta			= wp_get_attachment_image_src( get_the_author_meta( 'avatar' ), 'large' );
		$strAvatarURL			= isset( $avatar_meta[0] ) ? $avatar_meta[0] : $attachment_noimage ;


		// Post Author Meta
		$post->avatar			= $strAvatarURL;
		$post->display_name		= get_the_author_meta( 'display_name' );
		$post->email			= get_the_author_meta( 'user_email' );
		$post->item_count	= count_user_posts( get_the_author_meta( 'ID' ), self::SLUG );

		// Post More Meta
		$post->attach			= get_post_meta( $post->ID, 'detail_images', true );

		if( !empty( $arrMoreMeta ) ) foreach( $arrMoreMeta as $key => $meta )
			$post->$key			= get_post_meta( $post->ID, $key, true );
	}

	public function wpml_post_id( $post_id=0, $post_type=false, $lang=false ) {
		if( function_exists( 'icl_object_id' ) ){
			if( !$post_type ) $post_type = self::SLUG;
			if( !$lang ) $lang = ICL_LANGUAGE_CODE;
			return icl_object_id( $post_id, $post_type, $lang );
		}
		return $post_id;
	}

	public function getBpCommponentName() {
		return sprintf( self::STR_BP_SLUG_FORMAT, self::SLUG );
	}

	public function add_bp_nav() {

		if( is_admin() || !function_exists( 'BuddyPress' ) ) {
			return false;
		}

		$bp = BuddyPress();

		$author = 0;
		if( isset( $bp->displayed_user->id ) ) {
			$author = $bp->displayed_user->id;
		}

		$count  = $this->getCorePostCount( array( 'author' => $author ) );
		$class  = ( 0 === $count ) ? 'no-count' : 'count';
		$name = sprintf( __( 'My Posts <span class="%s">%s</span>', 'lvbp-bp-post' ), esc_attr( $class ), number_format_i18n( $count ) );
		$strCoreSLUG = $this->getBpCommponentName();

		bp_core_new_nav_item( array(
			'name'                  => $name,
			'slug'                  => $strCoreSLUG,
			'parent_url'            => $bp->displayed_user->domain,
			'parent_slug'           => $bp->profile->slug,
			'screen_function'       => Array( $this, 'bp_index' ),
			'position'              => 50,
			'default_subnav_slug'   => 'lists'
		) );

		bp_core_new_subnav_item( array(
			'name'              => sprintf( esc_html__( "All Lists", 'lvbp-bp-post' ), self::NAME ),
			'slug'              => 'lists',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . $strCoreSLUG ),
			'parent_slug'       => $strCoreSLUG,
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 1,
			'user_has_access'   => true,
		) );

		bp_core_new_subnav_item( array(
			'name'              => sprintf( esc_html__( "publish", 'lvbp-bp-post' ), self::NAME ),
			'slug'              => 'publish',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . $strCoreSLUG ),
			'parent_slug'       => $strCoreSLUG,
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 2,
			'user_has_access'   => bp_is_my_profile(),
		) );

		bp_core_new_subnav_item( array(
			'name'              => sprintf( esc_html__( "Pending", 'lvbp-bp-post' ), self::NAME ),
			'slug'              => 'pending',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . $strCoreSLUG ),
			'parent_slug'       => $strCoreSLUG,
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 3,
			'user_has_access'   => bp_is_my_profile()
		) );

		bp_core_new_subnav_item( array(
			'name'              => sprintf( esc_html__( "New %s", 'lvbp-bp-post' ), self::NAME ),
			'slug'              => 'new',
			'parent_url'        => trailingslashit( bp_displayed_user_domain() . $strCoreSLUG ),
			'parent_slug'       => $strCoreSLUG,
			'screen_function'   => Array( $this, 'bp_index' ),
			'position'          => 4,
			'user_has_access'   => bp_is_my_profile()
		) );

	}

	public function getCorePostCount( $args=Array() ) {

		$_Default = Array(
			'author' => 0,
			'post_type' => self::SLUG,
			'posts_per_page' => -1,
			'post_status' => 'any',
		);

		$query_args = shortcode_atts( $_Default, $args );
		$query = new WP_Query( $query_args );
		return $query->found_posts;
	}

	public function bp_index() {

		$strCoreSLUG = $this->getBpCommponentName();
		if( ! bp_is_current_component( $strCoreSLUG ) ) {
			return;
		}

		$fnCallback = array( $this, 'bp_page_' . BuddyPress()->current_action );
		if( is_callable( $fnCallback ) ) {
			add_action( 'bp_template_content', $fnCallback );
		}
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	public function bp_page_new() {
		$strShortcode = sprintf( '[%1$s]', lava_bpp()->shortcode->getShortcodeName( 'form' ) );
		echo do_shortcode( $strShortcode );
	}

	public function bp_page_lists() {
		if( bp_is_my_profile() ) {
			$format = '[%1$s title="" count="-1" author="%2$s"]';
		}else{
			$format = '[%1$s type="publish" guest="true" title="" count="-1" author="%2$s"]';
		}
		$strShortcode = sprintf(
			$format,
			lava_bpp()->shortcode->getShortcodeName( 'mypage' ),
			BuddyPress()->displayed_user->id
		);
		echo do_shortcode( $strShortcode );
	}

	public function bp_page_publish() {
		$strShortcode = sprintf(
			'[%1$s type="publish" title="" count="-1" author="%2$s"]',
			lava_bpp()->shortcode->getShortcodeName( 'mypage' ),
			BuddyPress()->displayed_user->id
		);
		echo do_shortcode( $strShortcode );
	}

	public function bp_page_pending() {
		$strShortcode = sprintf(
			'[%1$s type="pending" title="" count="-1" author="%2$s"]',
			lava_bpp()->shortcode->getShortcodeName( 'mypage' ),
			BuddyPress()->displayed_user->id
		);
		echo do_shortcode( $strShortcode );
	}

}