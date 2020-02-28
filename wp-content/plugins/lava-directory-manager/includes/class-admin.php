<?php

class Lava_Directory_Manager_Admin extends Lava_Directory_Manager_Func {

	Const __OPTION_GROUP__ = 'lava_directory_manager_group';
	Const __SCHEMA_OPTION_GROUP__ = 'ldm_schema';

	Const STR_TERM_META_FORMAT = 'lava_%s_%s_%s';

	private $admin_dir;
	private static $form_loaded = false;
	private static $is_wpml_actived;
	private static $item_refresh_message;

	static $once = false;

	public $options;

	public function __construct() {

		if(self::$once) {
			return;
		}

		$this->setVariables();
		$this->registerHooks();
		$this->loadFIles();

		do_action( "lava_{$this->post_type}_admin_class_init" );
		self::$once = true;
	}

	public function setVariables() {
		$this->admin_dir = trailingslashit( dirname( __FILE__ ) . '/admin' );
		$this->post_type = self::SLUG;
		$this->featured_term = self::getFeaturedTerm();
		$this->options = get_option( $this->getOptionFieldName() );
		self::$is_wpml_actived = function_exists( 'icl_object_id' );
	}

	public function registerHooks() {

		$this->save_once();

		/*
		 *
		 * Admin manager page
		 */
		add_action( 'admin_init', Array( $this, 'register_options' ) );
		add_action( 'admin_init', Array( $this, 'upload_cap' ) );
		add_action( 'pre_get_posts', Array( $this, 'uploader_filter' ));
		add_action( 'admin_menu', Array( $this, 'register_setting_page' ) );


		/*
		 *
		 * Backend Metabox and scripts
		 */
		add_action( 'admin_footer', Array( $this, 'admin_form_scripts' ) );
		add_action( 'add_meta_boxes', Array( $this, 'reigster_meta_box' ), 0 );
		add_action( 'admin_enqueue_scripts', Array( $this, 'load_admin_page' ) );

		if( is_admin() ) {
			add_action( 'save_post', Array( $this, 'save_post' ) );
		}


		/*
		 *
		 * Auto Json file generator
		 */
		add_filter( "lava_{$this->post_type}_json_addition", Array( $this, 'json_addition' ), 10, 3 );
		add_filter( "lava_{$this->post_type}_categories", Array( $this, 'json_categories' ) );

		/*
		 *
		 * Add form > login link ( require : This hook move to class-core.php )
		 */
		add_filter( "lava_{$this->post_type}_login_url", Array( $this, 'login_url' ) );

		/*
		 *
		 * Listing > No image ( require : This hook move to class-core.php )
		 */
		add_filter( 'lava_directory_listing_featured_no_image'	, Array( $this, 'noimage' ) );

		/*
		 *
		 * Custom Back-end column
		 */
		add_filter( 'manage_edit-' . $this->post_type . '_columns', Array( $this, 'add_manage_column' ), 8 );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', Array( $this, 'custom_manage_column_content' ), 10, 2 );

		add_action( 'lava_' . $this->post_type . '_admin_setting_page_before', array( $this, 'settingPageBefore' ) );
		add_action( 'lava_' . $this->post_type . '_admin_setting_page_after', array( $this, 'settingPageAfter' ) );


		// Custom Category Marker
		add_action( 'admin_enqueue_scripts', Array( $this, 'admin_enqueue_callback' ) );

		add_action( 'admin_init', Array( $this, 'customTaxonomies' ) );

		add_action( 'lava_edit_listing_amenities_term', array( $this, 'amenitiesIcons' ), 10, 3 );
		add_action( 'lava/directory/field/args', array( $this, 'required_field' ), 10, 2 );


		// $this->customAmenitiesTerm();
		$this->ajax_hooks();
	}

	public function loadFIles() {
		require_once( 'functions-admin.php' );
	}

	public function customTaxonomies() {
		$arrTaxonomiesMeta = apply_filters( 'lava_' . $this->post_type . '_taxonomies', Array() );
		$arrTaxonomies = array_diff( array_keys( $arrTaxonomiesMeta ), array( 'listing_keyword' ) );

		add_action( 'lava_file_script', Array( $this, 'lava_file_script_callback' ) );

		if( !empty( $arrTaxonomies ) ) {
			foreach( $arrTaxonomies as $strTax ) {

				add_action( $strTax . '_add_form_fields', Array( $this,'apped_AddFeaturedImageFIeld' ) );
				add_action( $strTax . '_edit_form_fields', Array( $this,'apped_EditFeaturedImageFIeld' ), 10, 2 );

				add_action( 'created_' . $strTax, Array( $this,'setFeaturedImageOption' ), 10, 2 );
				add_action( 'edited_' . $strTax, Array( $this,'setFeaturedImageOption' ), 10, 2 );

				add_action( 'deleted_term_taxonomy', Array( $this, 'remove_featured_term' ));

				add_filter( 'manage_edit-' . $strTax . '_columns', Array( $this, 'setFeaturedColumnHeader' ) );
				add_filter( 'manage_' . $strTax . '_custom_column', Array( $this, 'setFeaturedColumnBody' ), 10, 3 );

				if( $strTax == $this->featured_term ) {
					add_action( "{$this->featured_term}_edit_form_fields", Array($this,'edit_featured_term'), 10, 2);
					add_action( "{$this->featured_term}_add_form_fields", Array($this,'add_featured_term'), 10, 1 );

					/*
					add_action( "created_{$this->featured_term}", Array($this, 'save_featured_term'), 10, 2);
					add_action( "edited_{$this->featured_term}", Array($this, 'save_featured_term'), 10, 2); */
					/*
					// add_action( "{$this->featured_term}_add_form_fields", Array($this, 'add_featured_term'));
					add_action( 'deleted_term_taxonomy', Array($this, 'remove_featured_term'));
					add_action( 'lava_file_script', Array($this, 'lava_file_script_callback'));
					add_filter( "manage_edit-{$this->featured_term}_columns" , Array($this, 'featured_term_columns'));
					add_filter( "manage_{$this->featured_term}_custom_column" , Array($this, 'manage_featured_term_columns'), 10, 3); */
				}

			}
		}
	}

	public function getInertIconDescriptions() {
		$description = Array();
		$description[] = '<div class="lv-amenities-icon-description">';
			$description[] = '<p>';
				$description[] = esc_html__( 'You can add icon class ', 'Lavacode' );
				$description[] = sprintf(
					'<a href="%1$s" target="_blank">%2$s</a>.',
					esc_url( 'fontawesome.io/icons/' ),
					esc_html__( 'Awesome Font Icons', 'Lavacode' )
				);
				$description[] = '</p>';
				$description[] = '<p>';
				$description[] = esc_html__( 'Before you use font icons, you need to enqueue icon code ', 'Lavacode' );
				$description[] = sprintf(
					'<a href="%1$s" target="_blank">%2$s</a>.',
					esc_url( 'fontawesome.io/get-started/' ),
					esc_html__( 'Here', 'Lavacode' )
				);
				$description[] = '</p>';
				$description[] = '<p>';
				$description[] = esc_html__( '(If you are using javo themes, you do not need to enqueue)', 'Lavacode' );
			$description[] = '</p>';
		$description[] = '</div>';

		return join( false, $description );
	}

	public function getFeaturedImageFieldData( $taxonomy=false, $tag=false ) {
		$objTaxonomy = get_taxonomy( $taxonomy );
		$output = new stdClass();
		$output->taxonomy = $taxonomy;
		$output->tag = $tag;
		$output->subject = sprintf( '%s %s', $objTaxonomy->label, esc_html__( "Featured Image", 'Lavacode' ) );
		$output->fieldKey = '_featured_id';
		$output->fieldID = 'lava_term_meta' . $output->fieldKey;
		$output->fieldName = 'lava_term_meta[' . $output->fieldKey . ']';

		if( $tag instanceof WP_Term ) {
			$output->tag_id = $tag->term_id;
			/*
			if( version_compare( lava_directory()->getVersion(), '1.0.9.2', '>=' ) ) {
				$output->featured_id = intVal( get_term_meta( $output->tag_id, $output->fieldKey, true ) );
			}else{ */
				$output->featured_id = intVal( $this->getTermOption( $tag, 'featured', $taxonomy, 0 ) );
				/*
			} */
			$output->featured_src = wp_get_attachment_thumb_url( $output->featured_id );
			$output->featured_image = wp_get_attachment_image( $output->featured_id, 'thumbnail', false, array( 'style' => 'max-width:100%;height:auto;' ) );
			$output->amenities_icon = $this->getTermOption( $tag, 'amenities_icon', $taxonomy, '' );
			$output->icon = $this->getTermOption( $tag, 'icon', $taxonomy, '' );
			$image_icon = $this->getTermOption( $tag, 'image_icon', $taxonomy, '' );
			$output->image_icon = '';
			if($image_icon) {
				$image_src = wp_get_attachment_image_url($image_icon);
				$output->image_icon = sprintf(
					'<img src="%1$s" style="%2$s">', $image_src, implode( ';', Array(
					'height:auto',
					'max-width:100%'
				)));
			}
			$output->marker = $this->getTermOption( $tag, 'marker', $taxonomy, '' );
		}
		return $output;
	}

	public function apped_AddFeaturedImageFIeld( $taxonomy='' ) {
		lava_directory()->core->load_admin_template(
			'part-add-term-featured-image.php',
			Array(
				'lavaArgs' => $this->getFeaturedImageFieldData( $taxonomy ),
				'lavaModal' => Array(
					'title' => esc_html( "Select featured image", 'Lavacode' ),
					'select' => esc_html( "Select", 'Lavacode' ),
				),
			)
		);
		do_action( 'lava_edit_' . $taxonomy . '_term', 0, $taxonomy, $edit_mode=false );
		do_action( 'lava_file_script' );
	}

	public function apped_EditFeaturedImageFIeld( $tag=false, $taxonomy=false ) {
		echo '<table class="form-table">';
		lava_directory()->core->load_admin_template(
			'part-edit-term-featured-image.php',
			Array(
				'lavaArgs' => $this->getFeaturedImageFieldData( $taxonomy, $tag ),
				'lavaModal' => Array(
					'title' => esc_html( "Select featured image", 'Lavacode' ),
					'select' => esc_html( "Select", 'Lavacode' ),
				),
			)
		);
		do_action( 'lava_edit_' . $taxonomy . '_term', $tag, $taxonomy, $edit_mode=true );
		echo '</table>';
		do_action( 'lava_file_script' );
	}

	public function setFeaturedImageOption( $term_id=0, $taxonomy='' ) {
		$taxonomy = $GLOBALS[ 'taxonomy'];
		if( 0 < intVal( $term_id ) ) {
			foreach(
				Array(
					'icon' => 'term_icon',
					'marker' => 'lava_listing_category_marker',
					'features' => 'lava_listing_category_features',
				) as $strType => $strFieldName
			) {
				$arrOptionValues = isset( $_POST[ $strFieldName ] ) ? $_POST[ $strFieldName ] : false;
				$this->setTermOption( get_term( $term_id ), $strType, $arrOptionValues, $taxonomy );
			}
		}

		if( isset( $_POST[ 'lava_term_meta' ] ) && is_array( $_POST[ 'lava_term_meta' ] ) ) {
			$arrAllowFields = Array( '_featured_id', 'icon', 'image_icon' );
			foreach( $_POST[ 'lava_term_meta' ] as $strFieldName => $strFieldValue ) {
				if( in_array( $strFieldName, $arrAllowFields ) ) {
					update_term_meta( $term_id, $strFieldName, $strFieldValue );
					$strUpdateOptionKey = $strFieldName == '_featured_id' ? 'featured' : $strFieldName;
					$this->setTermOption( $term_id, $strUpdateOptionKey, $strFieldValue, $taxonomy );
				}
			}
		}
	}

	public function setFeaturedColumnHeader( $columns=Array() ) {

		$headers = Array(
			'cb' => '<input type="checkbox">',
			'featured' => __( "Featured Image", 'Lavacode' ),
			'name' => __( "Name", 'Lavacode' ),
			'icon' => __( "Icon", 'Lavacode' ),
			'image' => __( "Image", 'Lavacode' ),
			'description' => __( "Description", 'Lavacode' ),
			'marker' => __( "Map Marker", 'Lavacode' ),
			'slug' => __( "Slug", 'Lavacode' ),
			'posts' => __( "Items", 'Lavacode' ),
		);

		if( $GLOBALS[ 'taxonomy' ] != $this->featured_term ) {
			unset( $headers[ 'marker' ] );
		}

		if( in_array( $GLOBALS[ 'taxonomy' ], Array( 'listing_amenities', 'listing_category' ) ) ) {
			unset( $headers[ 'featured' ] );
		}else{
			unset( $headers[ 'icon' ] );
			unset( $headers[ 'image' ] );
		}

		return wp_parse_args( $columns, $headers );
	}

	public function setFeaturedColumnBody( $output='', $column_name='', $cat_id=0 ){
		$objTerm = get_term( $cat_id );
		$objFeaturedData = $this->getFeaturedImageFieldData( $objTerm->taxonomy, $objTerm );
		switch ($column_name) {
			case 'featured':
				$output .= sprintf( '<img src="%1$s" style="max-width:100%%; height:auto;">', $objFeaturedData->featured_src );
				break;
			case 'marker' :
				$output .= sprintf( '<img src="%1$s" style="max-width:100%%; height:auto;">', $objFeaturedData->marker );
				break;
			case 'icon' :
				$output .= $objFeaturedData->icon;
				break;
			case 'image':
				$output .= $objFeaturedData->image_icon;
		};
		return $output;
	}

	public function customAmenitiesTerm() {
		$strAmenities = sprintf( '%s_amenities', parent::NAME );
		// add_filter( "manage_edit-{$strAmenities}_columns" , Array($this, 'amenities_term_columns'));
		// add_filter( "manage_{$strAmenities}_custom_column" , Array( $this, 'manage_amenities_columns' ), 10, 3);
		add_action( $strAmenities . '_edit_form_fields', Array( $this,'edit_amenities_term' ), 10, 2);
		add_action( $strAmenities . '_add_form_fields', Array( $this, 'add_amenities_term' ) );
		/*
		add_action( 'created_' . $strAmenities, Array($this, 'save_amenities_term'), 10, 2);
		add_action( 'edited_' . $strAmenities, Array($this, 'save_amenities_term'), 10, 2); */
	}

	public function settingPageBefore() {
		wp_localize_script(
			sanitize_title( lava_directory()->enqueue->handle_prefix . 'admin.js' ),
			'lava_dir_admin_param',
			Array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajax_hook' => sprintf( '%s_', $this->post_type ),
			)
		);
		wp_enqueue_script( sanitize_title( lava_directory()->enqueue->handle_prefix . 'admin.js' ) );
	}

	public function settingPageAfter() {
		wp_enqueue_media();
	}

	public function load_admin_page() {
		wp_enqueue_script( 'lava-directory-manager-gmap-v3' );
	}

	public function reigster_meta_box() {
		foreach(
			Array( 'commentstatusdiv', 'commentsdiv')
			as $keyMetaBox
		) remove_meta_box( $keyMetaBox, self::SLUG, 'normal' );

		add_meta_box(
			'lava_directory_manager_metas'
			, __( "Listing Additional Meta", 'Lavacode' )
			, Array( $this, 'lava_directory_manager_addition_meta' )
			, self::SLUG
			, 'advanced'
			, 'high'
		);
	}

	public function amenitiesIcons( $tag, $taxonomy='', $edit=false ) {
		$strFormat = $edit ? '<tr><th><label>%1$s</label></th><td><input type="text" name="lava_term_meta[%2$s]" value="%3$s" class="large-text">%4$s</td></tr>' :
		 '<div class="form-field"><label>%1$s</label><input type="text" name="lava_term_meta[%2$s]" value="%3$s" class="large-text">%4$s</div>';

		$description = $this->getInertIconDescriptions();
		printf( $strFormat, esc_html__( "Icon", 'Lavacode' ), 'icon', $this->getTermOption( $tag, 'icon', $taxonomy, '' ), $description );

		$image_icon = $this->getTermOption( $tag, 'image_icon', $taxonomy, '' );
		$image_src = '';
		if($image_icon) {
			$image_src = wp_get_attachment_image_url($image_icon);
		}
		$uploadContent = '<div class="lava-edit-term-wp-upload" data-args="{title:\"\"}">';
		$uploadContent .= sprintf(
			'<div class="preview-wrap"><img class="preview-upload" src="%1$s" style="%2$s"></div>',
			$image_src, implode( ';', Array(
				'height:auto',
				'margin:15px 0 0 0',
				'max-width:300px'
			))
		);
		$uploadContent .= '<div class="action-wrap">';
		$uploadContent .= '<input type="hidden" name="lava_term_meta[%2$s]" value="%3$s">';
		$uploadContent .= '<button type="button" class="button button-primary upload">';
		$uploadContent .= esc_html__("Select", 'Lavacode') . '</button>';
		$uploadContent .= '<button type="button" class="button button-default remove">';
		$uploadContent .= esc_html__("Remove", 'Lavacode') . '</button>';
		$uploadContent .= '</div></div>';

		$strFormat = $edit ? '<tr><th><label>%1$s</label></th><td>' . $uploadContent . '</td></tr>' :
		 '<div class="form-field"><label>%1$s</label>' . $uploadContent . '</div>';
		printf( $strFormat, esc_html__( "Image icon", 'Lavacode' ), 'image_icon', $image_icon );
	}

	public function lava_directory_manager_addition_meta( $post ) {
		global $post;

		self::$form_loaded = 1;

		foreach(
			Array( 'lat', 'lng', 'street_lat', 'street_lng', 'street_heading', 'street_pitch', 'street_zoom', 'street_visible' )
			as $key
		) $post->$key	= floatVal( get_post_meta( $post->ID, 'lv_listing_' . $key, true ) );

		foreach(
			Array('country', 'locality', 'political', 'political2', 'address', 'zipcode' )
			as $key
		) $post->$key	= get_post_meta( $post->ID, 'lv_listing_' . $key, true );

		$lava_item_fields	= apply_filters( "lava_{$this->post_type}_more_meta", Array() );

		ob_start();
			do_action( "lava_{$this->post_type}_admin_metabox_before" , $post );
			require_once dirname( __FILE__) . '/admin/admin-metabox.php';
			do_action( "lava_{$this->post_type}_admin_metabox_after" , $post );
		ob_end_flush();
	}

	public function lava_directory_manager_map_meta( $post ) {
		global $post;

		ob_start();
			do_action( "lava_{$this->post_type}_admin_map_meta_before" , $post );
			require_once dirname( __FILE__) . '/admin/admin-mapmeta.php';
			do_action( "lava_{$this->post_type}_admin_map_meta_after" , $post );
		ob_end_flush();
	}

	public function admin_form_scripts() {
		if( ! self::$form_loaded )
			return;

		wp_localize_script(
			sanitize_title( lava_directory()->enqueue->handle_prefix . 'admin-metabox.js' ),
			'lava_directory_manager_admin_meta_args',
			Array(
				'fail_find_address'	=> __( "Not found address", 'Lavacode' )
			)
		);

		wp_enqueue_script( sanitize_title( lava_directory()->enqueue->handle_prefix . 'admin-metabox.js' ) );
	}

	public function save_post( $post_id ) {

		if( ! is_admin() ) {
			return false;
		}

		$has_lavafield = isset( $_POST['lava_pt'] );
		$lava_query = new lava_Array( $_POST );
		$lava_PT = new lava_Array( $lava_query->get( 'lava_pt', Array() ) );
		$lava_mapMETA = $lava_query->get( 'lava_map_param' );
		$lava_moreMETA = $lava_query->get( 'lava_additem_meta' );

		// More informations
		if( !empty( $lava_moreMETA ) ) : foreach( $lava_moreMETA as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		} endif;

		// Map informations
		if( !empty( $lava_mapMETA ) ) : foreach( $lava_mapMETA as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		} endif;

		// More detail picture or image ids meta
		if( $has_lavafield ) {
			update_post_meta( $post_id, 'detail_images', $lava_query->get( 'lava_attach' ) );
		}

		// Google Map position meta
		if( false !== (boolean)( $meta = $lava_PT->get( 'map', false ) ) ) {
			foreach( $meta as $key => $value ) {
				update_post_meta( $post_id, "lv_listing_{$key}", $value );
			}
		}

		// Featured item meta
		$thisFeatured = isset($_POST['lava_pt']['featured']) ? 1 : null;
		update_post_meta( $post_id, '_featured_item', $thisFeatured );

		// Upldate Json
		do_action( "lava_{$this->post_type}_json_update", $post_id, get_post( $post_id ), null );

	}

	public function register_options() {
		register_setting( self::__OPTION_GROUP__ , $this->getOptionFieldName() );
		register_setting( self::__SCHEMA_OPTION_GROUP__ , $this->getOptionFieldName(false, '_schema') );
	}

	public function upload_cap() {
		wp_get_current_user()->add_cap('upload_files');
	}

	public function uploader_filter($query) {
		if( $query->is_main_query() ) return;
		if( current_user_can('administrator') ){ return; }

		if( $query->get('post_type') == 'attachment'){
			if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] != 'query-attachments' ) return;
			$query->set( 'author', get_current_user_ID() );
		}
	}

	public function save_once() {
		if( 'yes' != get_option( 'lvdr_req_field_once' ) ) {
			$this->set_setting( 'required_fields', Array( 'txt_title', 'map' ) );
			update_option( 'lvdr_req_field_once', 'yes' );
		}
	}

	public function getOptionFieldName( $option_name=false, $key=false ){    // option field name

		$strFieldName = 'lava_directory_manager_settings';
		if($key) {
			$strFieldName .= $key;
		}

		if( $option_name ){
			$strFieldName = sprintf( '%1$s[%2$s]', $strFieldName, $option_name );
		}

		return $strFieldName;
	}

	public function getOptionsPagesLists( $default=0 ) {
		$pages_output = Array();
		if(
			! $pages = get_posts(
				Array(
					'post_type' => 'page',
					'posts_per_page' => -1,
					'suppress_filters' => false,
				)
			)
		) return false;

		$default = $this->wpml_post_id( $default, 'page' );

		foreach( $pages as $page ) {
			$pages_output[]	= "<option value=\"{$page->ID}\"";
			$pages_output[]	= selected( $default == $page->ID, true, false );
			$pages_output[]	= ">{$page->post_title}</option>";
		}

		return @implode( false, $pages_output );
	}

	public function register_setting_page() {
		add_submenu_page(
			'edit.php?post_type=' . self::SLUG
			, __( "Lava Directory Manager Settings", 'Lavacode' )
			, __( "Settings", 'Lavacode' )
			, 'manage_options'
			, 'lava-' . self::SLUG . '-settings'
			, Array( $this, 'admin_page_template' )
		);
	}

	public function admin_page_template() {
		global $lava_directory_manager;
		do_action( 'lava_' . $this->post_type . '_admin_setting_page_before' );

		$arrTabs_args = Array(
			'' => Array(
				'label' => __( "Home", 'Lavocode' ),
				'group'	=> self::__OPTION_GROUP__,
				'file'	=> $this->admin_dir . 'admin-index.php'
			),
			'schema' => Array(
				'label' => __( "Schema", 'Lavocode' ),
				'group'	=> self::__SCHEMA_OPTION_GROUP__,
				'file'	=> $this->admin_dir . 'schema.php'
			),
		);

		$arrTabs		= apply_filters( "lava_{$this->post_type}_admin_tab", $arrTabs_args );

		echo self::$item_refresh_message;
		echo "<div class=\"wrap\">";
			printf( "<h2>%s</h2>", __( "Lava Directory Manager Settings", 'Lavacode' ) );
			echo "<form method=\"post\" action=\"options.php\">";
			echo "<h2 class=\"nav-tab-wrapper\">";
			$strCurrentPage	= isset( $_GET[ 'index' ] ) && $_GET[ 'index' ] != '' ? $_GET[ 'index' ] : '';
			if( !empty( $arrTabs ) ) : foreach( $arrTabs as $key => $meta ) {
					printf(
						"<a href=\"%s\" class=\"nav-tab %s\">%s</a>"
						, esc_url(
								add_query_arg(
									Array(
										'post_type' => self::SLUG
										, 'page' => 'lava-' . self::SLUG . '-settings'
										, 'index' => $key
									)
									, admin_url( 'edit.php' )
								)
							)
						, ( $strCurrentPage == $key ? 'nav-tab-active' : '' )
						, $meta[ 'label' ]
					);

				}
				echo "</h2>";
				if( $strTabMeta = $arrTabs[ $strCurrentPage ] ) {
					settings_fields( $strTabMeta[ 'group' ] );
					if( file_exists( $strTabMeta[ 'file' ] ) )
						require_once $strTabMeta[ 'file' ];
				}
			endif;

			if( apply_filters( "lava_{$this->post_type}_admin_save_button", true ) )
				printf( "<button type=\"\" class=\"button button-primary\">%s</button>", __( "Save", 'Lavacode' ) );

			echo "</form>";
			echo "<form id=\"lava_common_item_refresh\" method=\"post\">";
			wp_nonce_field( "lava_{$this->post_type}_items", "lava_{$this->post_type}_refresh" );
			echo "<input type=\"hidden\" name=\"lang\">";
			echo "</form>";
		echo "</div>";
		do_action( 'lava_' . $this->post_type . '_admin_setting_page_after' );
	}

	public function admin_welcome_template() {
		if( file_exists( $this->admin_dir . 'admin-welcome.php' ) )
			require_once $this->admin_dir . 'admin-welcome.php';
	}

	public function json_categories( $args ) {
		global $lava_directory_manager_func;

		$lava_exclude = Array();
		$lava_taxonomies = $lava_directory_manager_func->lava_extend_item_taxonomies();

		if( empty( $lava_taxonomies ) || !is_Array( $lava_taxonomies ) )
			return $args;

		if( !empty( $lava_exclude ) ) : foreach( $lava_exclude as $terms ) {
			if( in_Array( $terms, $lava_taxonomies ) )
				unset( $lava_taxonomies[ $terms] );
		} endif;

		return wp_parse_args( Array_Keys( $lava_taxonomies ), $args );
	}

	public function json_addition( $args, $post_id, $tax ) {
		$lava_taxonomies	= $this->json_categories( Array() );

		if( !empty( $lava_taxonomies ) ) : foreach( $lava_taxonomies as $term ) {
			$args[ $term ]	= $tax->get( $term );
		} endif;

		$args[ 'price' ] = get_post_meta( $post_id, '_price', true );
		$args[ 'sale_price' ] = get_post_meta( $post_id, '_sale_price', true );
		$args[ 'price_range' ] = get_post_meta( $post_id, '_price_range', true );

		// Require : move to directory_review
		$args[ 'rating' ] = get_post_meta( $post_id, 'rating_average', true );
		$args[ 'rating_count' ] = $this->reviewCount( $post_id );

		// Favorite
		$args[ 'save_count' ] = $this->favoriteCount( $post_id );

		// Working Hour
		$args[ 'working_hours' ] = $this->getWorkingHours( $post_id );

		return $args;
	}

	public function get_settings( $option_key, $default=false, $key=false ) {
		$options = get_option( $this->getOptionFieldName( false, $key) );

		if( array_key_exists( $option_key, (Array) $options ) )
			if( $value = $options[ $option_key ] ) {
				$default = $value;
			}

		return $default;
	}

	public function set_setting( $option_key, $option_value=false ) {
		$options = is_array( $this->options ) ? $this->options : Array();
		$options[ $option_key ] = $option_value;
		update_option( $this->getOptionFieldName(), $options );
		$this->options = $options;
	}

	public function noimage( $image_url ) {
		if( $noimage = $this->get_settings( 'blank_image' ) )
			return $noimage;
		return $image_url;
	}

	public function login_url( $login_url ) {
		if( $redirect = $this->get_settings( 'login_page' ) )
			return get_permalink( $redirect );
		return $login_url;
	}

	public function add_manage_column( $columns ) {
		return wp_parse_args(
			$columns,
			Array(
				'cb' => '<input type="checkbox">',
				'thumbnail'	=> __( "Thumbnail", 'Lavacode' ),
			)
		);
	}

	public function custom_manage_column_content( $cols_id, $post_id=0 ) {
		switch( $cols_id ) {
			case 'thumbnail':
				the_post_thumbnail();
				break;
		}
	}

	public function admin_enqueue_callback(){
		if ( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
	}

	public function getTermMetaKey( $term=Array(), $meta_key='' ) {
		$output = false;
		if( $term instanceof WP_Term ) {
			$output = sprintf( self::STR_TERM_META_FORMAT, $term->taxonomy, $term->term_id, $meta_key );
		}
		return $output;
	}

	public function getTermOption( $term=0, $meta_key='', $taxonomy='', $default=false ) {
		if( is_numeric( $term ) ) {
			$term = get_term( $term, $taxonomy );
		}
		$metaKey = $this->getTermMetaKey( $term, $meta_key );
		$option = get_option( $metaKey );
		return !empty( $option ) ? $option : $default;
	}

	public function getTermFeaturedImage( $term=0, $size='thumbnail', $taxonomy='' ) {
		$thumbnail = false;
		$thumbnail_id = $this->getTermOption( $term, 'featured', $taxonomy );
		if( false !== $thumbnail_id ) {
			$noImage = $this->noimage( false );
			$thumbnail = wp_get_attachment_image_src( $thumbnail_id, $size );
			$thumbnail = isset( $thumbnail[0] ) ? $thumbnail[0] : $noImage;
		}
		return $thumbnail;
	}

	public function setTermOption( $term=0, $meta_key='', $meta_value='', $taxonomy='' ) {
		$result = false;
		if( is_numeric( $term ) ) {
			$term = get_term( $term, $taxonomy );
		}
		if( $term instanceof WP_Term ) {
			$str_meta_key = $this->getTermMetaKey( $term, $meta_key );
			$result = update_option( $str_meta_key, $meta_value );
		}
		return $result;
	}

	public function add_featured_term( $tag ) {
		parent::$instance->load_admin_template(
			'part-add-category-meta.php',
			Array(
				'lavaInstance' => $this,
				'tag' => $tag,
				'taxonomy' => $GLOBALS[ 'taxonomy' ],
				'iconDescription' => $this->getInertIconDescriptions(),
			)
		);
	}

	public function edit_featured_term( $tag, $taxonomy ) {
		parent::$instance->load_admin_template(
			'part-edit-category-meta.php',
			Array(
				'lavaInstance' => $this,
				'tag' => $tag,
				'taxonomy' => $taxonomy,
				'iconDescription' => $this->getInertIconDescriptions(),
			)
		);
	}

	public function add_amenities_term( $tag ) {
		global $taxonomy;
		parent::$instance->load_admin_template(
			'admin-amenities-form.php',
			Array(
				'is_edit_mode' => !empty( $tag->is_lava_edit_mode ),
				'lava_template_args' => (object) Array(
					'name' => parent::NAME,
					'fieldPrefix' => sprintf( 'lava_%s_', parent::NAME ),
					'icon' => $this->getTermOption( $tag, 'icon', $taxonomy, '' ),
				),
			)
		);
	}

	public function edit_amenities_term( $tag, $taxonomy ) {
		$tag->is_lava_edit_mode = true;
		$this->add_amenities_term( $tag );
	}

	public function save_amenities_term( $term_id, $tt_id ) {
		$strPrefix = sprintf( 'lava_%s_', parent::NAME );
		if( isset( $_POST[ $strPrefix . 'amenities_icon' ] ) ) {
			$this->setTermOption( get_term( $term_id ), 'icon', $_POST[ $strPrefix . 'amenities_icon' ] );
		}
	}

	public function remove_featured_term($id) {
		delete_option( 'lava_listing_category_'.$id.'_marker' );
		delete_option( 'lava_listing_category_'.$id.'_featured' );
		delete_option( 'lava_listing_category_'.$id.'_amenties' );
	}

	public function featured_term_columns( $columns ) {
		$new_columns		= array(
			'cb' => '<input type="checkbox">'
			, 'name' => __('Name', 'Lavacode')
			, 'description' => __('Description', 'Lavacode')
			, 'marker' => __('Marker Preview', 'Lavacode')
			, 'featured' => __('Featured Preview', 'Lavacode')
			, 'slug' => __('Slug', 'Lavacode')
			, 'posts' => __('Items', 'Lavacode')
		);
		return $new_columns;
	}

	public function amenities_term_columns( $old_column ) {
		$new_columns		= array(
			'cb' => '<input type="checkbox">',
			'name' => __( "Name", 'Lavacode'),
			'description' => __( "Description", 'Lavacode'),
			'icon' => __( "Icon", 'Lavacode'),
			'slug' => __( "Slug", 'Lavacode'),
			'posts' => __( "Items", 'Lavacode')
		);
		return $new_columns;
	}

	public function manage_featured_term_columns( $out, $column_name, $cat_id ){
		global $taxonomy;

		$marker = $this->getTermOption( $cat_id, 'marker', $taxonomy );
		$amenities = $this->getTermOption( $cat_id, 'amenities', $taxonomy );

		switch( $column_name ) {
			case 'marker':
				if(!empty($marker)){
					$out .= '<img src="'.$marker.'" style="max-width:100%;" alt="">';
				}
			break;
			case 'featured':
				$out .= sprintf( '<img src="%1$s" style="max-width:100%%; height:auto;">', $this->getTermFeaturedImage( $cat_id, 'thumbnail', $taxonomy ) );
			break;
		};
		return $out;
	}

	public function manage_amenities_columns( $out, $column_name, $cat_id ) {

		$strIcon = $this->getTermOption( get_term( $cat_id ), 'icon', $taxonomy, '' );
		switch ($column_name) {
			case 'icon':
				if( !empty( $strIcon ) ){
					$out .= sprintf( '<span>%1$s</span>', $strIcon );
				}
			break;
		};
		return $out;
	}

	public function lava_file_script_callback(){
		wp_localize_script(
			sanitize_title( lava_directory()->enqueue->handle_prefix . 'admin-edit-term.js' ),
			'lv_edit_featured_taxonomy_variables',
			Array(
				'mediaBox_title'		=> __( "Select Category Featured Image", 'Lavacode' ),
				'mediaBox_select'	=> __( "Apply", 'Lavacode' ),
			)
		);
		wp_enqueue_script( sanitize_title( lava_directory()->enqueue->handle_prefix . 'admin-edit-term.js' ) );
	}

	public function ajax_hooks() {
		$strPrefix = sprintf( 'wp_ajax_%s_', $this->post_type );
		add_action( $strPrefix . 'get_listings_count', array( $this, 'json_listings_count' ) );
		add_action( $strPrefix . 'json_writer', array( $this, 'json_writer' ) );
	}

	public function reviewCount( $post_id=0 ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare(
			"SELECT count(*) FROM {$wpdb->comments} as cmt left join {$wpdb->commentmeta} as cmtMeta on cmt.comment_ID = cmtMeta.comment_id where cmt.comment_post_ID=%s and cmt.comment_approved='1' and cmtMeta.meta_key='rating_average'; ", $post_id
		)  );
	}

	public function favoriteCount( $post_id ) {
		return get_post_meta( $post_id, '_save_count', true );
	}

	public function getWorkingHours( $post_id ) {
		$result = get_post_meta( $post_id, '_open_hours', true );
		return json_decode( $result );
	}

	public function getGeneralQuery( $lang=false, $type=false ) {
		global $wpdb;
		$orderby = apply_filters( 'lava_' . $this->post_type . '_json_order_by', 'p.post_date' );
		$is_wpml_active = function_exists( 'icl_object_id' ) && $lang;
		if( $is_wpml_active ) {
			$innerJoin = 'INNER JOIN %2$s as w ON p.ID = w.element_id';
			$where = 'WHERE p.post_type=%%s AND p.post_status=%%s AND w.language_code="%3$s" AND element_type="post_%4$s"';
		}else{
			$innerJoin = '';
			$where = 'WHERE p.post_type=%%s AND p.post_status=%%s';
		}
		if(false !== json_decode($type)) {
			$innerJoin .= ' ' . 'INNER JOIN ' . $wpdb->postmeta . ' as pm ON pm.post_id=p.ID';
			$where .= ' ' . 'AND pm.meta_key="_listing_type" AND pm.meta_value="' . $type . '"';
		}

		if( $is_wpml_active ) {
			$strSQL = 'SELECT DISTINCT ID FROM %1$s as p ' . $innerJoin .' ' . $where . ' ORDER BY ' . $orderby . ' ASC';
		}else{
			$strSQL = 'SELECT DISTINCT ID FROM %1$s p ' . $innerJoin . ' ' . $where . ' ORDER BY ' . $orderby . ' ASC';
		}
		return $wpdb->prepare( sprintf( $strSQL, $wpdb->posts, $wpdb->prefix . 'icl_translations', $lang, $this->post_type ), $this->post_type, 'publish' );
	}

	public function getJsonDataCount($lang='', $type=false) {
		return $GLOBALS['wpdb']->get_col( $this->getGeneralQuery( $lang, $type ) );
	}

	public function json_listings_count() {
		$strGeneralLang = ! empty( $_POST[ 'lang' ] ) ? $_POST[ 'lang' ] : false;
		$strListingType = !empty($_POST['type']) ? $_POST['type'] : false;
		wp_send_json(Array('result' => $this->getJsonDataCount($strGeneralLang, $strListingType)));
	}

	public function json_writer() {
		$arrIDs = isset( $_POST[ 'items' ] ) && is_array( $_POST[ 'items' ] ) ? $_POST[ 'items' ] : Array();
		$strGeneralLang = !empty( $_POST[ 'lang' ] ) ? $_POST[ 'lang' ] : '';
		$strListingType = !empty($_POST['type']) ? $_POST['type'] : false;
		$is_rewriteMode = isset( $_POST[ 'renew' ] ) && $_POST[ 'renew' ] === 'true';
		wp_send_json(Array(
			'result'=>'OK',
			'filename' => $this->parseJSON($arrIDs, $strListingType, $strGeneralLang, $is_rewriteMode),
		));
	}

	public function parseJSON($IDs=Array(), $type=false, $lang='', $rewrite=true){
		global $wpdb;
		$is_file_mode = 'w'; // $is_rewriteMode ? 'w' : 'a';
		$strFileName = lava_directory()->core->getJsonFileName( $lang, true, $type );
		if( file_exists( $strFileName ) && !$rewrite ) {
			$lava_all_posts = json_decode( file_get_contents( $strFileName ), true );
		}else{
			$lava_all_posts = Array();
		}

		foreach( $IDs as $item_id ) {

			$item = get_post( $item_id );

			// Google Map LatLng Values
			$latlng = Array(
				'lat' => get_post_meta( $item_id, 'lv_listing_lat', true ),
				'lng' => get_post_meta( $item_id, 'lv_listing_lng', true )
			);

			/* Taxonomies */ {

				$category = Array();
				$category_label = Array();
				$lava_all_taxonomies = apply_filters( 'lava_' . self::SLUG . '_categories', Array() );

				foreach( $lava_all_taxonomies as $taxonomy ) {
					$results = $wpdb->get_results(
						$wpdb->prepare("
							SELECT t.term_id, t.name, tt.parent FROM $wpdb->terms AS t
							INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id
							INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
							WHERE
								tt.taxonomy IN (%s)
							AND
								( tr.object_id IN ($item->ID) OR tt.parent=t.term_id )
							ORDER BY
								t.name ASC"
							, $taxonomy
						)
					);

					//$category[ $taxonomy ] = $results;
					foreach( $results as $result ) {
						$category[ $taxonomy ][] = $result->term_id;
						$category_label[ $taxonomy ][] = $result->name;
						$curObj = get_term( $result->parent, $taxonomy );
						if( 'enable' == $this->get_settings( 'json_create_term_type', 'enable' ) ) {
							if( $curObj instanceof WP_Term ) {
								$category[ $taxonomy ][] = (string)$curObj->term_id;
								$category_label[ $taxonomy ][] = (string)$curObj->name;
							}
						}
					}
				}
				$lava_categories = new lava_ARRAY( $category );
				$lava_categories_label = new lava_ARRAY( $category_label );
			}

			/* Marker Icon */ {
				$category_icon = $lava_categories->get( 'listing_category', Array() );
				$category_icon = reset( $category_icon );
				// $lava_set_icon = get_option( "lava_listing_category_{$category_icon}_marker", '' );
				$lava_set_icon = $this->getTermOption( $category_icon, 'marker', 'listing_category', 0 );
			}

			$lava_all_posts_args	= Array(
				'post_id' => $item->ID,
				'post_title' => $item->post_title,
				'date' => strtotime( $item->post_date ),
				'order' => $item->menu_order,
				'icon' => $lava_set_icon,
				'tags' => $lava_categories_label->get( 'listing_keyword' ),
			);
			if( !empty( $latlng['lat'] ) && !empty( $latlng['lng'] ) ) {
				$lava_all_posts_args = wp_parse_args( $latlng, $lava_all_posts_args );
			}
			$parse_data = apply_filters( 'lava_' . self::SLUG . '_json_addition', $lava_all_posts_args, $item->ID, $lava_categories );
			$lava_all_posts[] = array_filter($parse_data);
		}

		$file_handle = @fopen( $strFileName, $is_file_mode );
		@fwrite( $file_handle, json_encode( $lava_all_posts ) );
		@fclose( $file_handle );
		return $strFileName;
	}


	public function getSubmitFields() {
		$defaults = Array(
			'txt_title' => Array(
				'label' => esc_html__( "Title", 'Lavacode' ),
			),
			'_tagline' => Array(
				'label' => esc_html__( "TagLine", 'Lavacode' ),
			),
			'txt_content' => Array(
				'label' => esc_html__( "Content", 'Lavacode' ),
			),
			'_logo' => Array(
				'label' => esc_html__( "Logo", 'Lavacode' ),
			),
			'featured_image' => Array(
				'label' => esc_html__( "Featured Image", 'Lavacode' ),
			),
			'detail_images' => Array(
				'label' => esc_html__( "Detail Images", 'Lavacode' ),
			),
			'map' => Array(
				'label' => esc_html__( "Map Informations", 'Lavacode' ),
			),
		);
		$metas = apply_filters( 'lava_' . self::SLUG . '_more_meta', Array() );
		$output = wp_parse_args( $metas, $defaults );

		$taxonomies = apply_filters( 'lava_' . self::SLUG . '_taxonomies', Array() );
		foreach( $taxonomies as $taxonomy => $args ) {
			$output[ $taxonomy ] = Array( 'label' => $args[ 'args' ][ 'label' ] );
		}

		return $output;
	}

	public function required_field( Array $args, $field='' ) {
		$required_fields = $this->get_settings( 'required_fields', Array() );
		if( in_array( $field, $required_fields ) ) {
			$args[ 'required' ] = true;
		}

		return $args;

	}

}