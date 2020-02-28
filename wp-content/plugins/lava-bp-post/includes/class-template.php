<?php

class Lava_Bp_Post_template {

	public $post_type = false;




	/**
	 *	Constructor
	 *
	 *
	 *	@return	void
	 */
	public function __construct() {
		$this->setStaticVariables();
		$this->registerHooks();
	}




	public function setStaticVariables() {}




	public function registerHooks() {
		add_action( 'init', array( $this, 'initialize' ), 15 );
	}


	public function setVariables() {
		$this->post_type = lava_bpp()->core->getSlug();
	}

	public function initialize() {
		$this->setVariables();

		/** Common hooks */ {
			// add_filter( 'template_include', Array( $this, 'load_templates' ) );
		}

		add_action( "lava_{$this->post_type}_single_container_before", Array( $this, 'single_control_buttons' ) );

		/** Single page template */ {

			add_action(
				"lava_{$this->post_type}_single_container_before"
				, Array( $this, 'parse_post_object' )
			);

			add_action(
				"lava_{$this->post_type}_single_container_after"
				, Array( $this, 'single_script_params' ), 20
			);

			add_action(
				"lava_{$this->post_type}_single_container_after"
				, Array( $this, 'single_script' ), 30
			);
		}

		/** Add form template */ {
			add_action( "lava_add_{$this->post_type}_form_before"	, Array( $this	, 'add_new_admin_notice' ), 20 );
			add_action( "lava_add_{$this->post_type}_form_before"	, Array( $this	, 'author_user_email' ), 20 );
			add_action( "lava_add_{$this->post_type}_form_after"	, Array( $this	, 'extend_form' ) );
			add_filter( "lava_add_{$this->post_type}_terms"			, Array( $this	, 'addItem_terms' ), 9 );

			foreach(
				Array( 'category', 'type' )
				as $key
			) add_filter( "lava_map_meta_{$key}"					, Array( $this, "map_meta_{$key}" ), 10, 2 );
		}
		add_action( 'pre_get_posts', array( $this, 'allow_preview' ) );
	}




	/**
	 *
	 *
	 *
	 *	@param	array
	 *	@return	array
	 */
	public static function addItem_terms( $args ) {
		$lava_exclude = Array( 'post_tag' );
		$lava_taxonomies = Array( 'category' );

		if( empty( $lava_taxonomies ) || !is_Array( $lava_taxonomies ) )
			return $args;

		if( !empty( $lava_exclude ) ) : foreach( $lava_exclude as $terms ) {
			if( in_Array( $terms, $lava_taxonomies ) )
				unset( $lava_taxonomies[ $terms] );
		} endif;

		return wp_parse_args( $lava_taxonomies, $args );
	}




	/**
	 *
	 *
	 *	@param	string	template path
	 *	@return	string	template path
	 */
	public function load_templates( $template )
	{
		global
			$wp_query
			, $lava_bpp_manager;

		$post		= $wp_query->queried_object;

		if( is_a( $post, 'WP_Post' ) ) {

			/* Single Template */ {
				if( $wp_query->is_single && $post->post_type == $this->post_type ) {

					if(  $__template = locate_template(
							Array(
								"single-{$this->post_type}.php"
								, $lava_bpp_manager->folder . "/single-{$this->post_type}.php"
							)
						)
					) $template = $__template;
				}
			}

			/* Map Template */ {
				if( "lava_{$this->post_type}_map" == get_post_meta( $post->ID, '_wp_page_template', true ) ){
					$template = $this->get_map_template();
				}
			}
		}
		return apply_filters( "lava_{$this->post_type}_get_template", $template, $wp_query, $this );
	}




	/**
	 *
	 *
	 *	@param	object
	 *	@return	void
	 */
	public function extend_form( $edit ) {

		$arrPartFiles	= apply_filters(
			'lava_bpp_add_item_extends', Array(
				'lava-add-item-terms.php',
				'lava-add-item-file.php',
				'lava-add-item-meta.php',
			)
		);

		if( !empty( $arrPartFiles ) ) :  foreach( $arrPartFiles as $filename ) {
			$filepath	= trailingslashit( lava_bpp()->template_path ) . "form/{$filename}";
			if( file_exists( $filepath ) ) require_once $filepath;
		} endif;
	}




	/**
	 *
	 *
	 *	@param	array
	 *	@return	void
	 */
	public function author_user_email( $edit ) {

		$lava_loginURL = apply_filters( "lava_{$this->post_type}_login_url", wp_login_url() );

		if( is_user_logged_in() )
			return;

		$filepath = trailingslashit( lava_bpp()->template_path ) . "form/lava-add-item-user.php";
		if( file_exists( $filepath ) ) require_once $filepath;
	}




	/**
	 *
	 *
	 *	@param	array
	 *	@return	void
	 */
	public function add_new_admin_notice( $edit )
	{
		$strNotice = lava_bpp()->admin->get_settings( 'add_new_admin_notice', false );
		if( false === $strNotice ) {
			return;
		}

		$filepath = trailingslashit( lava_bpp()->template_path ) . "form/lava-add-item-notice.php";
		if( file_exists( $filepath ) ) require_once $filepath;
	}





	/**
	 *
	 *
	 *	@return	void
	 */
	public function parse_post_object() {
		lava_bpp_setupdata();
	}




	/**
	 *
	 *
	 *	@return	void
	 */
	public function single_control_buttons() {

		$post = get_post();
		$arrActions = Array();
		$is_pending = $post->post_status == 'pending';
		$is_after_publish = lava_bpp()->core->is_allow_publish();
		$intFormPage = lava_bpp()->admin->get_settings( "page_add_{$this->post_type}", false );
		$intMyPage = lava_bpp()->admin->get_settings( "page_my_page", false );

		if( ! $is_pending ) {
			return false;
		}

		if( 0 < intVal( $intFormPage ) ) {
			$arrActions[ 'edit' ] = Array(
				'label' => esc_html__( "Edit this post", 'lvbp-bp-post' ),
				'url' => add_query_arg( Array( 'edit' => $post->ID, ), get_permalink( $intFormPage ) ),
			);
		}


		if( $is_after_publish ) {
			$arrActions[ 'publish' ] = Array(
				'label' => esc_html__( "Publish this post", 'lvbp-bp-post' ),
				'url' => wp_nonce_url(
					add_query_arg(
						Array(
							'publish' => 'true',
							'post_id' => $post->ID
						),
						get_permalink( $post->ID )
					),
					$this->post_type . '-publish', 'sqr'
				),
			);
		}else{

			if( 0 < intVal( $intMyPage ) ) {
				$strURL = get_permalink( $intMyPage );
			}else{
				$strURL = home_url( '/' );
			}

			$arrActions[ 'finish' ] = Array(
				'label' => esc_html__( "Finish", 'lvbp-bp-post' ),
				'url' => esc_url( $strURL ),
			);
		}

		$arrButtons = apply_filters( "lava_{$this->post_type}_single_control_buttons", $arrActions, $post, $intFormPage, $is_after_publish );
		if( !empty( $arrButtons ) ) {
			echo '<ul class="lava-single-control-button">';
				foreach( $arrButtons as $strKey => $arrButtonMeta ) {
					printf(
						'<li class="control-item %3$s"><a href="%2$s" title="%1$s">%1$s</a></li>',
						$arrButtonMeta[ 'label' ],
						esc_url_raw( $arrButtonMeta[ 'url' ] ), $strKey
					);
				}
			echo '</ul>';
		}
	}




	/**
	 *
	 *
	 *	@param	array
	 *	@return	void
	 */
	public function single_script_params()
	{
		$post		= get_post();
		$options	= Array(
			'strNotLocation'			=> __( "There is no location information on this item.", 'lvbp-bp-post' )
			, 'strNotStreetview'		=> __( "This location is not supported by google StreetView or the location did not add.", 'lvbp-bp-post' )
		);

		echo "<fieldset class=\"lava-single-map-param hidden\">";

		echo "
			<!-- parameters -->
			<input type=\"hidden\" value=\"disable\" data-cummute-panel>
			<input type=\"hidden\" value=\"450\" data-map-height>
			<input type=\"hidden\" value=\"500\" data-street-height>
			<!-- end parameters -->
			";

		if( ! empty( $options ) ) : foreach( $options as $key => $value ) {
			echo "<input type='hidden' key=\"{$key}\" value=\"{$value}\">";
		} endif;

		foreach(
			Array( 'lat', 'lng', 'street_lat', 'street_lng', 'street_heading', 'street_pitch', 'street_zoom', 'street_visible' )
			as $key
		) printf(
			"<input type=\"hidden\" data-item-%s value=\"%s\">\n"
			, str_replace( '_', '-', $key )
			, floatVal( get_post_meta( $post->ID, "lv_listing_{$key}", true ) )
		);
		echo "</fieldset>";
	}




	/**
	 *
	 *
	 *	@param	array
	 *	@return	void
	 */
	public function single_script()
	{
		echo "
			<script type=\"text/javascript\">
			jQuery( function($){
				jQuery.lava_single({
					map			: $( '#lava-single-map-area' )
					, street	: $( '#lava-single-streetview-area' )
					, slider	: $( '.lava-detail-images' )
					, param		: $( '.lava-single-map-param' )
				});
			} );
			</script>
			";
	}




	/**
	 *
	 *
	 *	@param	array
	 *	@return	void
	 */
	public function remove_html_margin_top()
	{
		remove_action('wp_head', '_admin_bar_bump_cb');
	}






	public function allow_preview( $query ) {
		if( ! $query->is_single || ! $query->is_main_query() ) {
			return false;
		}

		$post_id = intVal( $query->query_vars[ 'p' ] );
		$post = get_post( $post_id );

		if( ! $post instanceof WP_Post ) {
			return false;
		}

		if( get_current_user_id() == $post->post_author || current_user_can( 'manage_options' ) ) {
			if( $post->post_type == $this->post_type ) {
				$query->set( 'post_status', Array( 'publish', 'pending' ) );
			}
		}
	}



}