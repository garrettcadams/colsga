<?php

class Lava_Directory_Manager_template {

	/**
	 * @var string $post_type
	 */
	public $post_type = false;

	/**
	 * @brief Initialize
	 * @return void
	 */
	public function __construct() {
		$this->setStaticVariables();
		$this->registerHooks();
	}

	/**
	 * @return void
	 */
	public function setStaticVariables() {}

	/**
	 * @return void
	 */
	public function registerHooks() {
		add_action( 'init', array( $this, 'initialize' ), 15 );
		add_action('wp_footer', Array($this, 'render_schema'));
	}

	/**
	 * @return void
	 */
	public function setVariables() {
		$this->post_type = lava_directory()->core->getSlug();
	}

	/**
	 * @return void
	 */
	public function initialize() {
		$this->setVariables();

		/** Common hooks */ {
			add_filter( 'template_include', Array( $this, 'load_templates' ) );
		}

		add_action( "lava_{$this->post_type}_single_container_before", Array( $this, 'single_control_buttons' ) );

		/** Register Map Template */ {
			if( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
				add_filter( 'page_attributes_dropdown_pages_args'	, Array( $this, 'register_map_tempate_old' ) );
				add_filter( 'wp_insert_post_data', Array( $this, 'register_map_tempate_old' ) );
			}else{
				add_filter( 'theme_page_templates', array( $this, 'register_map_tempate' ) );
			}
		}

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

		/** Map page template  */ {
			add_action( "lava_{$this->post_type}_map_container_after" , Array( $this, 'print_map_templates' ) );
		}

		/** Add form template */ {
			add_action( "lava_add_{$this->post_type}_form_before"	, Array( $this	, 'author_user_email' ), 20 );
			add_action( "lava_add_{$this->post_type}_form_after"	, Array( $this	, 'extend_form' ) );
			add_filter( "lava_add_{$this->post_type}_terms"			, Array( $this	, 'addItem_terms' ), 9 );

			foreach(
				Array( 'category', 'type' )
				as $key
			) add_filter( "lava_map_meta_{$key}"					, Array( $this, "map_meta_{$key}" ), 10, 2 );
		}

		/** Shortcode - listings */ {

			// Output Templates
			add_action( "lava_{$this->post_type}_listings_after"	, Array( $this, 'print_listings_templates' ) );

			// Output Variables
			add_action( "lava_{$this->post_type}_listings_after"	, Array( $this, 'print_listings_var' ) );
		}
		add_action( 'pre_get_posts', array( $this, 'allow_preview' ) );
	}

	/**
	 * @param array $templates
	 * @return array template lists
	 */
	public function register_map_tempate( $templates=Array() ) {
		return wp_parse_args(
			$templates
			, Array(
				"lava_{$this->post_type}_map"	=> sprintf(
					__( "Lava %s Map Template", 'Lavacode' )
					, get_post_type_object( $this->post_type )->label
				)
			)
		);
	}

	/**
	 * @param array $attr
	 * @return array $attr
	 */
	public function register_map_tempate_old( $attr ) {
		$cache_key	= 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
		$templates	= wp_get_theme()->get_page_templates();
		$templates	= empty( $templates ) ? Array() : $templates;
		$templates	= wp_parse_args(
			$templates
			, Array(
				"lava_{$this->post_type}_map"	=> sprintf(
					__( "Lava %s Map Template", 'Lavacode' )
					, get_post_type_object( $this->post_type )->label
				)
			)
		);
		wp_cache_delete( $cache_key , 'themes');
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );
		return $attr;
	}

	/**
	 * @param array $args
	 * @return array Submit form with more taxonomies
	 */
	public static function addItem_terms( $args ) {
		global $lava_directory_manager_func;

		$lava_exclude = Array( 'post_tag' );

		$lava_taxonomies = $lava_directory_manager_func->lava_extend_item_taxonomies();

		if( empty( $lava_taxonomies ) || !is_Array( $lava_taxonomies ) )
			return $args;

		if( !empty( $lava_exclude ) ) : foreach( $lava_exclude as $terms ) {
			if( in_Array( $terms, $lava_taxonomies ) )
				unset( $lava_taxonomies[ $terms] );
		} endif;

		return wp_parse_args( Array_Keys( $lava_taxonomies ), $args );
	}

	/**
	 * @param array $template
	 * @return array Templates
	 */
	public function load_templates( $template ) {

		global $wp_query, $lava_directory_manager;

		$post = $wp_query->queried_object;

		if( is_a( $post, 'WP_Post' ) ) {

			/* Single Template */ {
				if( $wp_query->is_single && $post->post_type == $this->post_type ) {

					if(  $__template = locate_template(
							Array(
								"single-{$this->post_type}.php"
								, $lava_directory_manager->folder . "/single-{$this->post_type}.php"
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
	 *	@return	string
	 */
	public function get_map_template()
	{
		global $lava_directory_manager;

		add_action( 'wp_enqueue_scripts', Array( $this, 'map_template_enqueues' ) );
		add_action( 'body_class', Array( $this, 'map_template_body_class' ) );
		add_action( 'get_header', Array( $this, 'remove_html_margin_top' ) );
		add_action( 'wp_head', Array( $this, 'parse_mapdata' ) );

		$result_template	= $lava_directory_manager->template_path . "/template-map.php";
		if(
			$__template = locate_template(
				Array(
					"lava-map-template.php"
					, $lava_directory_manager->folder . "/lava-map-template.php"
				)
			)
		) $result_template = $__template;

		return $result_template;
	}





	/**
	 *
	 *
	 *	@param	Array
	 *	@return	void
	 */
	public function map_template_body_class( $classes ) {
		$classes[] = 'lv-map-template';
		$classes = apply_filters( "lava_{$this->post_type}_map_classes",$classes );
		return wp_parse_args( Array( "page-template-lava_{$this->post_type}_map" ), $classes );
	}





	/**
	 *
	 *
	 *	@return	void
	 */
	public function parse_mapdata()
	{
		lava_directory_mapdata( $post );
		$GLOBALS[ 'post' ] = $post;
		do_action( "lava_{$this->post_type}_map_wp_head" );
	}





	/**
	 *
	 *
	 *	@param	object
	 *	@return	void
	 */
	public function extend_form( $edit )
	{
		global $lava_directory_manager;

		$arrPartFiles	= apply_filters(
			'lava_directory_manager_add_item_extends'
			, Array(
				'lava-add-item-terms.php'
				, 'lava-add-item-file.php'
				, 'lava-add-item-location.php'
				, 'lava-add-item-meta.php'
			)
		);

		if( !empty( $arrPartFiles ) ) :  foreach( $arrPartFiles as $filename ) {
			$filepath	= trailingslashit( $lava_directory_manager->template_path ) . "form/{$filename}";
			if( file_exists( $filepath ) ) require_once $filepath;
		} endif;
	}




	/**
	 *
	 *
	 *	@param	array
	 *	@return	void
	 */
	public function author_user_email( $edit )
	{
		global $lava_directory_manager;

		$lava_loginURL			= apply_filters( "lava_{$this->post_type}_login_url", wp_login_url() );

		if( is_user_logged_in() )
			return;

		$filepath				= trailingslashit( $lava_directory_manager->template_path ) . "form/lava-add-item-user.php";
		if( file_exists( $filepath ) ) require_once $filepath;
	}




	/**
	 *
	 *
	 *	@return	void
	 */
	public function parse_post_object() {
		lava_directory_setupdata();
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
		$is_after_publish = lava_directory()->core->is_allow_publish();
		$intFormPage = lava_directory()->admin->get_settings( "page_add_{$this->post_type}", false );
		$intMyPage = lava_directory()->admin->get_settings( "page_my_page", false );

		if( ! $is_pending ) {
			return false;
		}

		if( 0 < intVal( $intFormPage ) ) {
			$arrActions[ 'edit' ] = Array(
				'label' => esc_html__( "Edit this post", 'Lavacode' ),
				'url' => add_query_arg( Array( 'edit' => $post->ID, ), get_permalink( $intFormPage ) ),
			);
		}


		if( $is_after_publish ) {
			$arrActions[ 'publish' ] = Array(
				'label' => esc_html__( "Publish this post", 'Lavacode' ),
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
				'label' => esc_html__( "Finish", 'Lavacode' ),
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
		echo "<fieldset class=\"lava-single-map-param hidden\">";
		echo "
			<!-- parameters -->
			<input type=\"hidden\" value=\"disable\" data-cummute-panel>
			<input type=\"hidden\" value=\"450\" data-map-height>
			<input type=\"hidden\" value=\"500\" data-street-height>
			<!-- end parameters -->
			";
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
	public function map_template_enqueues()
	{
		wp_enqueue_script( 'google-maps' );
		wp_enqueue_script( 'lava-directory-manager-gmap-v3' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'lava-directory-manager-google-map-infobubble-js' );
		wp_enqueue_script( 'lava-directory-manager-lava-map-js' );
		do_action( "lava_{$this->post_type}_map_box_enqueue_scripts" );
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




	/**
	 *
	 *
	 *	@return	void
	 */
	public function print_map_templates()
	{
		global $lava_directory_manager;

		$tmpDir				= $lava_directory_manager->template_path . '/';

		$load_map_htmls		= Array(
			'lava-map-output-template'		=> $tmpDir . 'template-map-htmls.php'
			, 'lava-map-not-found-template'	=> $tmpDir . 'template-not-list.php'
		);

		$load_map_htmls		= apply_filters( "lava_{$this->post_type}_map_htmls", $load_map_htmls, $tmpDir );

		$output_script		= Array();
		if( !empty( $load_map_htmls ) ) : foreach( $load_map_htmls as $sID => $strFilePath ) {

			$output_script[]	= "<script type='text/html' id=\"{$sID}\">";
			ob_start();

			if( file_exists( $strFilePath ) )
				require_once $strFilePath;

			$output_script[]	= ob_get_clean();
			$output_script[]	= "</script>";

		} endif;
		echo @implode( "\n", $output_script );
	}




	/**
	 *
	 *
	 *	@return	void
	 */
	public function print_listings_templates()
	{
		global $lava_directory_manager;

		$load_map_htmls		= Array(
			'lava-directory-manager-listing-template'	=> 'template-listing-list.php'
		);

		$load_map_htmls		= apply_filters( 'lava_{$this->post_type}_map_htmls', $load_map_htmls );
		$output_script		= Array();
		if( !empty( $load_map_htmls ) ) : foreach( $load_map_htmls as $sID => $strFilename ) {

			$output_script[]	= "<script type='text/html' id=\"{$sID}\">";
			ob_start();
			require_once $lava_directory_manager->template_path . "/{$strFilename}";
			$output_script[]	= ob_get_clean();
			$output_script[]	= "</script>";

		} endif;
		echo @implode( "\n", $output_script );
	}




	/**
	 *
	 *
	 *	@return	void
	 */
	public function print_listings_var() {
		$lava_script_param			= Array();
		$lava_script_param[]		= "<script type=\"text/javascript\">";
			$lava_script_param[]	= sprintf( "var ajaxurl=\"%s\";", admin_url( 'admin-ajax.php' ) );
			$lava_script_param[]	= sprintf( "var _jb_not_results=\"%s\";", __( "Not found results.", 'Lavacode' ) );
		$lava_script_param[]		= "</script>";

		echo @implode( "\n", $lava_script_param );
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
			if( $query->get( 'post_type' ) == $this->post_type ) {
				$query->set( 'post_status', Array( 'publish', 'pending' ) );
			}
		}
	}

	/**
     * @brief Render schema json
     * @return void
     */
    public function render_schema() {
		$queried_object = get_queried_object();
		if( $queried_object instanceof \WP_Post && $queried_object->post_type == 'lv_listing') {
			$schemaTemplate = lava_directory()->admin->get_settings('schema_template', '', '_schema');
			$markup = $this->compile_schema($schemaTemplate, get_the_ID());

			// Generate code.
			$output = PHP_EOL . '<script type="application/ld+json">' . PHP_EOL;
			$output .= wp_json_encode($markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			$output .= PHP_EOL . '</script>' . PHP_EOL;
			echo $output;
		}
	}

	/**
     * @brief Compie schema
     * @param string $data Json code
     * @return array
     */
    public function compile_schema($data, $listingID = 0) {
		$data = json_decode($data, true);
		if(!$data) {
			return $data;
		}
		array_walk($data, Array($this, 'replace_schema'), $listingID);
        return $data;
	}

	public function getReviewInfo($listingID=0) {
		$args = Array(
			'post_id' => $listingID,
			'parent' => 0,
			'status' => 'approve',
		);
		$reviewsQuery = new WP_Comment_Query($args);
		return Array(
			'count' => sizeof($reviewsQuery->comments),
			'value' => floatVal(get_post_meta($listingID, 'rating_average', true)),
		);
	}

	public function getOpenHours($listingID=0) {
		$getData = get_post_meta($listingID, '_open_hours', true);
		$data = json_decode($getData, true);
		$output = Array();
		if(is_array($data)){
			$days = Array('Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su');
			foreach($data as $dayIndex => $day) {
				$openTime = $closeTime =  false;
				if(!isset($day['isActive'])) {
					continue;
				}
				if(false === $day['isActive']) {
					continue;
				}
				if(isset($day['isOpen24h']) && true === $day['isOpen24h']) {
					$openTime = '00:00';
					$closeTime = '23:59';
				}else{
					if(isset($day['timeFrom'][0])){
						$openTime = $day['timeFrom'][0];
					}
					if(isset($day['timeTill'][0])){
						$closeTime = $day['timeTill'][0];
					}
				}
				if(!$openTime || !$closeTime) {
					continue;
				}
				$output[] = sprintf('%1$s, %2$s-%3$s', $days[$dayIndex], $openTime, $closeTime);
			}
		}
		return $output;
	}

    public function replace_schema(&$data, $data_key = '', $listingID = 0) {
		$listing = get_post($listingID);
		$reviews = $this->getReviewInfo($listingID);
        $gallery = array();
        $detailImages = get_post_meta($listing->ID, 'detail_images', true);
        if ($detailImages) {
            foreach ($detailImages as $imageID) {
                $gallery[] = wp_get_attachment_image_url($imageID, 'full');
            }
		}
        $replaces = Array(
            '[[title]]' => $listing->post_title,
            '[[:url]]' => get_permalink($listing),
            '[[description]]' => $listing->post_excerpt,
            '[[:lat]]' => get_post_meta($listing->ID, 'lv_listing_lat', true),
			'[[:lng]]' => get_post_meta($listing->ID, 'lv_listing_lng', true),
			'[[logo]]' => wp_get_attachment_url(get_post_meta($listing->ID, '_logo', true)),
            '[[location]]' => get_post_meta($listing->ID, '_address', true),
            '[[phone]]' => get_post_meta($listing->ID, '_phone1', true),
            '[[email]]' => get_post_meta($listing->ID, '_email', true),
            '[[cover]]' => get_the_post_thumbnail_url($listing->ID),
			'[[gallery]]' => json_encode($gallery),
			// '[[work_hours]]' => $this->getOpenHours($listingID),
			'[[:reviews-average]]' => $reviews['value'],
			'[[:reviews-count]]' => max(1, $reviews['count']),
			'[[:reviews-mode]]' => 5,
			'[[price_range]]' => get_post_meta($listingID, '_price_range', true),
		);
		switch($data) {
		case '[[work_hours]]':
			$data = $this->getOpenHours($listingID);
			break;
		default:
			$data = str_replace(array_keys($replaces), array_values($replaces), $data);
		}
    }
}