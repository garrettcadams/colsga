<?php

class Jvbpd_Core_Shortcode  {

	Const SLUG = 'lv_listing';
	Const NAME = 'listing';

	private $corePrefix = false;

	private $modules = Array();

	public $raty_onmoduleExcerpt = Array(
		'module1',
		'module3',
		'module4',
		'module14',
		'module15',
	);

	public $raty_on_moduleThumbnail	= Array(
		'module2',
		'module6',
		'module8',
		'module9',
		'module10',
		'module11',
		'module12',
		'moduleSmallGrid',
		'moduleHorizontalGrid',
		'moduleBigGrid',
	);

	public static $scdInstance = false;

	public function __construct() {

		self::$scdInstance = $this;

		/*
		$this->dir = get_template_directory() . '/includes/';

		$this->shortcode_path	= $this->dir . 'shortcodes/';
		$this->corePrefix = Jvbpd_Core::get_instance()->template . '_';
		*/
		$this->corePrefix = 'jvbpd_';

		// Normal Shortcode Category & Meta
		add_action( 'jvbpd_modules_loaded', Array(  $this, 'all_category_additional_meta' ) );

		/*
		// Normal Shortcode Category & Meta
		add_action( 'jvbpd_modules_loaded', Array(  $this, 'get_modules' ) );

		// Map Template Slug
		add_filter( 'jvbpd_get_map_template_slug', Array( $this, 'get_map_template_slug' ), 10, 2 );
		add_filter( 'jvbpd_shortcodes_post_type_addition', Array( $this, 'addition_posttype' ) );

		*/
		// Custom Parameter
		add_filter( $this->corePrefix .'commonParam', Array( $this, 'commonParam' ), 10, 2 );
		add_filter( $this->corePrefix .'shortcodes_atts', Array( $this, 'shortcodes_atts' ) );
		add_filter( $this->corePrefix .'shotcode_query', Array( $this, 'shotcode_query' ), 10, 2 );


		// Custom Order

		add_action( 'jvbpd_parsed_shortcode', Array( $this, 'parsed_shortcode' ) );
		add_action( 'jvbpd_shortcode_after', Array( $this, 'after_shortcode' ) );

		/*

		// Load module contents on map ajax template
		add_action( 'jvbpd_template_all_module_loop_before', Array( $this, 'before_shortcode' ) );
		add_action( 'jvbpd_template_all_module_loop_after', Array( $this, 'after_shortcode' ) );

		$this->theme_shortcode(); */
		do_action( 'jvbpd_themes_custom_shortcode' );
	}

	public function get_modules( $modules ) {
		$this->modules = array_keys( $modules );
	}

	public function theme_shortcode(){

		$arrShortcodes = Array(
			'shortcode-search1.php',
			'shortcode-category-slider1.php',
			'shortcode-jv_featured1.php',
			'shortcode-javo-slider.php',
		);

		if( !empty( $arrShortcodes ) ) : foreach( $arrShortcodes as $filePath ) {
			if( file_exists( $this->shortcode_path . $filePath ) )
				require_once $this->shortcode_path . $filePath;
		} endif;

	}

	public function all_category_additional_meta( $modules ) {
		if( !empty( $modules ) ) : foreach( $modules as $module_name => $args ) {
			add_filter( "jvbpd_{$module_name}_featured_tax", Array( $this, 'featured_category' ), 10, 2 );
			add_filter( "jvbpd_{$module_name}_featured_no_tax", Array( $this, 'no_category_comment' ), 10, 2 );
			add_filter( "jvbpd_{$module_name}_additional_meta", Array( $this, 'additional_meta' ), 10, 2 );
		} endif;
	}

	public function addition_posttype( $post_types ) {
		return wp_parse_args( Array( self::SLUG ), $post_types );
	}

	public function featured_category( $taxonomy, $post_id ) {

		if( get_post_type( $post_id ) == self::SLUG )
			$taxonomy = self::NAME . '_category';
		return $taxonomy;
	}

	public function no_category_comment( $comment, $post_id ) {
		if( get_post_type( $post_id ) == self::SLUG )
			$comment = esc_html__( "Not Set", 'jvfrmtd' );
		return $comment;
	}

	public function additional_meta( $args, $obj=false ) {

		if( get_post_type( $obj->post ) != self::SLUG || empty( $obj ) )
			return $args;

		$arrOutput				= Array(
			'meta-location'		=> Array(
				'icon'			=> 'glyphicon glyphicon-map-marker',
				'value'			=> sprintf( "<span>%s</span>", esc_html( $obj->c( self::NAME . '_location', esc_html__( "Not Set", 'jvfrmtd' ) ) ) )
			),
			'meta-author'		=> Array(
				'icon'			=> 'jv-icon3-user2',
				'value'			=> sprintf( "<span>%s</span>", esc_html( $obj->author_name ) )
			),
			'module-meta-category' => Array(
				'icon'			=> 'fa fa-bookmark',
				'value'			=> sprintf( "<span>%s</span>", esc_html( $obj->category() ) )
			),
		);

		return $arrOutput;
	}

	public function more_meta( $obj=Array() )
	{
		if( get_post_type( $obj->post ) != self::SLUG )
			return;

		switch( get_class( $obj ) )
		{
			case 'module1' :
				printf( "<div class=\"meta-price\">%s %s</div>", esc_html( $obj->m( '_price_prefix' ) ), esc_html( $obj->m( '_price' ) ) );
				printf( "<div class=\"author-name\">%s <span>%s</span></div>", _x( 'By', 'More meta author', 'jvfrmtd' ), esc_html( $obj->author_name ) );
			break;

			default:
				printf( "<div class=\"meta-price\">%s %s</div>", esc_html( $obj->m( '_price_prefix' ) ), esc_html( $obj->m( '_price' ) ) );
		}

	}

	public function contents_with_more_button( $html, $obj=null ) {
		if( is_null( $obj ) )
			return $html;
		return sprintf( '%1$s<a class="jv-module-move-button"><div class="btn %2$s"><i class="fa fa-plus" aria-hidden="true"></i></div></a>', $html, 'move-marker');
	}

	public function get_map_template_slug( $template_slug, $post_type='post'  ) {
		$template_slug		= 'lava_' . self::SLUG . '_map';
		return $template_slug;
	}

	public function commonParam( $args, $strGroup=Array() )
	{
		$arrShortcodeTax = Array();

		if( function_exists( 'jvbpd_shortcode_taxonomies' ) )
			$arrShortcodeTax = jvbpd_shortcode_taxonomies( self::SLUG, Array( esc_html__( "None", 'jvfrmtd' )	=> '' ) );

		$arrAppend = Array(

			/** Filter Taxonomy */
			Array(
				'type'						=> 'dropdown'
				, 'group'					=> $strGroup[ 'filter' ]
				, 'heading'				=> esc_html__( "Category Filter", 'jvfrmtd')
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'post_type'
					, 'value'				=> self::SLUG
				)
				, 'param_name'		=> self::SLUG . '_filter_by'
				, 'value'					=> $arrShortcodeTax
			)

			/** Custom Post Taxonomies */
			, Array(
				'type'						=> 'checkbox'
				, 'group'					=> $strGroup[ 'filter' ]
				, 'heading'				=> esc_html__( "Use Custom Terms", 'jvfrmtd')
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'post_type'
					, 'value'				=> self::SLUG
				)
				,  'description'		=> esc_html__( "To display specific terms only, please enable. if you use custom terms..", 'jvfrmtd' )
				, 'param_name'		=> 'custom_filter_by_' . self::SLUG
				, 'value'					=> Array( esc_html__( "Enable", 'jvfrmtd' ) => '1' )
			)

			/** Header / Filter Custom Terms */
			, Array(
				'type'						=> 'textfield'
				, 'group'					=> $strGroup[ 'filter' ]
				, 'heading'				=> esc_html__( "Custom Terms IDs", 'jvfrmtd')
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'custom_filter_by_' . self::SLUG
					, 'value'				=> '1'
				)
				, 'param_name'		=> 'custom_filter_' . self::SLUG
				, 'description'			=> esc_html__( "Enter category IDs separated by commas (ex: 13,23,18). To exclude categories please add '-' (ex: -9, -10)", 'jvfrmtd' )
				, 'value'					=> ''
			)

			/** Featured Item */
			, Array(
				'type'						=> 'checkbox'
				, 'group'					=> $strGroup[ 'filter' ]
				, 'heading'				=> esc_html__( "Display only featured items", 'jvfrmtd')
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'post_type'
					, 'value'				=> self::SLUG
				)
				, 'description'			=> esc_html__( "To display featured items only, please enable", 'jvfrmtd' )
				, 'param_name'		=> 'featured_' . self::SLUG
				, 'value'					=> Array( esc_html__( "Enable", 'jvfrmtd' ) => '1' )
			)
		);

		return wp_parse_args( $arrAppend, $args );
	}

	public function shortcodes_atts( $attr )
	{
		return wp_parse_args(
			Array(
				self::SLUG . '_filter_by'			=> ''
				, 'custom_filter_by_' . self::SLUG	=> ''
				, 'custom_filter_' . self::SLUG		=> ''
				, 'featured_' . self::SLUG			=> ''
				, 'rating_type'						=> 'star'
				, 'display_rating_grade'			=> ''
				, 'rating_grade_color'				=> ''
			)
			, $attr
		);
	}

	public function shotcode_query( $query, $obj=null )
	{
		$prefixFeatured		= 'featured_' . self::SLUG;
		if( !is_null( $obj ) ) {
			if( isset( $obj->$prefixFeatured	 ) ) {
				if( '1' == $obj->$prefixFeatured	 )
					$query[ 'meta_query' ][] = Array(
						'key'		=> '_featured_item',
						'value'	=> '1'
					);
			}
		}
		return $query;
	}

	public function parsed_shortcode( $obj ) {

		$arrObject = get_object_vars( $obj );

		if( $obj->post_type == self::SLUG ) {

			if( !empty( $arrObject[ self::SLUG . '_filter_by' ] ) )
				$obj->filter_by					= $arrObject[ self::SLUG . '_filter_by' ];

			if( isset( $arrObject[ 'custom_filter_by_' . self::SLUG ] ) && $arrObject[ 'custom_filter_by_' . self::SLUG ]  == '1' ) {
				$obj->custom_filter_by_post	= $arrObject[ 'custom_filter_by_' . self::SLUG ];
				if( !empty( $arrObject[ 'custom_filter_' . self::SLUG ] ) )
					$obj->custom_filter			= $arrObject[ 'custom_filter_' . self::SLUG ];
			}

			// $obj->enq_prefix	= str_replace( '_', '-', sanitize_title( self::MAINPLUG ) ) . '-';


			/**
			if( class_exists( 'Lava_Directory_Review' ) && $obj->rating_type != 'disabled' ) {
				if( !empty( $this->modules ) ) : foreach( $this->modules as $module_name ) {
					if( in_Array( $module_name, $this->raty_onmoduleExcerpt ) && !empty( $obj->rating_type )  ) {
						add_filter( "jvfrm_spot_{$module_name}_core_module_excerpt_after", Array( $this, 'contents_with_raty_' . $obj->rating_type  ), 10, 2 );
					}else if( in_Array( $module_name, $this->raty_on_moduleThumbnail ) && !empty( $obj->rating_type ) ) {
						add_filter( "jvfrm_spot_{$module_name}_core_module_thumbnail_after", Array( $this, 'contents_with_raty_' . $obj->rating_type ), 10, 2 );
					}
				} endif;
			} **/

		}
	}

	public function getMapTemplateOption( $template_id= 0 ) {
		return get_post_meta( $template_id, 'jvbpd_map_page_opt', true );
	}

	public function before_shortcode( $template_id=0 ) {
		$arrOptions = (Array) $this->getMapTemplateOption( $template_id );
		if( isset( $arrOptions[ 'link_type' ] ) && $arrOptions[ 'link_type' ] == 'type3' ) {
			if( !empty( $this->modules ) )
				foreach( $this->modules as $module_name )
					add_filter( "jvbpd_{$module_name}_core_module_thumbnail_after", array( $this, 'contents_with_more_button' ), 15, 2 );
		}
	}

	public function after_shortcode( $obj=null ) {
		if( !empty( $this->modules ) ) : foreach( $this->modules as $module_name ) {
			remove_all_filters( "jvbpd_{$module_name}_core_module_excerpt_after" );
			remove_all_filters( "jvbpd_{$module_name}_core_module_thumbnail_after" );
		} endif;
	}
}