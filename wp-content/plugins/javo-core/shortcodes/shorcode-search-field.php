<?php
class jvbpd_search_field extends Jvbpd_OtherShortcode {

	public static $instance;

	public $loaded = false;

	public $is_keyword_active = false;

	private $sID;

	private $is_mobile = false;

	private $numCols = 1;

	public function __construct(){

		parent::__construct( Array(
			'name' => 'search_field',
			'label' => esc_html__( "Search Field", 'jvfrmtd' ),
			// 'params' => $this->getParams(),
		) );

		add_action( 'init', array( $this, 'initialize' ), 15 );
		add_action( 'jvbpd_search_field_after', Array( $this, 'tags_object' ) );

		// Filters
		add_filter( 'jvbpd_search_field_params', array( $this, 'getFieldParams' ) );
		add_filter( 'jvbpd_search_field_shortcode_atts', array( $this, 'addColumnParam' ) );

		// Coulmn Actions
		add_action( 'jvbpd_search_field_element_keyword', Array( $this, 'keyword' ) );
		add_action( 'jvbpd_search_field_element_google_search', Array( $this, 'google_search' ) );
		add_action( 'jvbpd_search_field_element_listing_category', Array( $this, 'listing_category' ) );
		add_action( 'jvbpd_search_field_element_listing_category_with_keyword', Array( $this, 'listing_category_with_keyword' ) );
		add_action( 'jvbpd_search_field_element_listing_location_with_google_search', Array( $this, 'location_with_google_search' ) );
		add_action( 'jvbpd_search_field_element_ajax_search', Array( $this, 'ajax_search' ) );
		add_action( 'jvbpd_search_field_element_listing_location', Array( $this, 'listing_location' ) );
		add_action( 'jvbpd_search_field_element_google_current_loadtion_search', Array( $this, 'google_current_loadtion_search' ) );
		add_action( 'jvbpd_search_field_element_listing_amenities', Array( $this, 'amenities' ), 10, 2);
		add_action( 'jvbpd_search_field_element_advanced_field', Array( $this, 'advanced_field' ), 10, 2);

		// add_action( 'jvbpd_search_field_element_advanced', Array( $this, 'advanced' ), 10, 2);
		add_filter( 'lava_ajax_search_taxonomy_template', array( $this, 'las_custom_term_template' ), 10, 3 );
		add_filter( 'lava_ajax_search_instances', array( $this, 'las_load_custom_instances' ), 10, 2 );
		add_filter( 'lava/ajax-search/type/label/replace', array( $this, 'las_type_replaces' ), 10, 2 );
		add_filter( 'lava_ajax_search_prepare_search', array( $this, 'las_prepare_search' ), 10, 2 );
		add_filter( 'lava_ajax_search_type_orders', array( $this, 'las_types_order' ), 10, 2 );
	}

	public function initialize() {
		if( function_exists( 'javo_moreTax' ) ) {
			$fields = javo_moreTax()->admin->getMoreTaxonomies();
			if( is_array( $fields ) ) {
				foreach( $fields as $field ) {
					add_action( 'jvbpd_search_field_element_' . $field[ 'name' ], Array( $this, 'other_field' ), 10, 2 );
				}
			}
		}
	}

	/*
	Shortcode parameter : fieldParameter for VC.
	 */
	public static function fieldParameter( $param_name, $label=false ){
		return Array(
			'type'				=> 'dropdown',
			'heading'			=> $label,
			'holder'			=> 'div',
			'group'			=> esc_html__( "Fields", 'jvfrmtd' ),
			'class'				=> '',
			'param_name'	=> $param_name,
			'value'				=> apply_filters(
				'jvbpd_search_field_element_lists',
				Array(
					esc_html__( "None", 'jvfrmtd' ) => '',
					esc_html__( "Keyword", 'jvfrmtd' ) => 'keyword',
					esc_html__( "Google Search", 'jvfrmtd' ) => 'google_search',
					esc_html__( "Category", 'jvfrmtd' ) => 'listing_category',
					esc_html__( "Location", 'jvfrmtd' ) => 'listing_location',
					esc_html__( "Global Search", 'jvfrmtd' ) => 'ajax_search',
				)
			)
		);
	}

	public function getParams() {
		return apply_filters(
			'jvbpd_search_field_params',
			Array(
				/**
				 *	@group : general
				 */
					Array(
						'type'				=> 'textfield',
						'heading'			=> esc_html__( "Title", 'jvfrmtd' ),
						'holder'			=> 'div',
						'class'				=> '',
						'param_name'	=> 'title',
						'value'				=> ''
					),
					Array(
						'type'				=> 'dropdown',
						'heading'			=> esc_html__('Please select search result page', 'jvfrmtd'),
						'holder'			=> 'div',
						'class'				=> '',
						'param_name'	=> 'query_requester',
						'value'				=> apply_filters(
							'jvbpd_get_map_templates',
							Array( esc_html__("Default Search Page", 'jvfrmtd') => '' ) )
					),
				/**
				 *	@group : Style
				 */
					Array(
						'type'			=> 'colorpicker'
						, 'group'		=> esc_html__( "Style", 'jvfrmtd' )
						, 'heading'		=> esc_html__( "Button Background Color", 'jvfrmtd')
						, 'holder'		=> 'div'
						, 'class'		=> ''
						, 'param_name'	=> 'button_bg_color'
						, 'value'		=> ''
					),
					Array(
						'type'			=> 'colorpicker'
						, 'group'		=> esc_html__( "Style", 'jvfrmtd' )
						, 'heading'		=> esc_html__( "Button Text Color", 'jvfrmtd')
						, 'holder'		=> 'div'
						, 'class'		=> ''
						, 'param_name'	=> 'button_text_color'
						, 'value'		=> ''
					),
				/**
				 *	@group : Fields
				 */
					Array(
						'type' => 'dropdown',
						'group' => esc_html__( "Fields", 'jvfrmtd' ),
						'heading' => esc_html__( "Keyword Autocomplete", 'jvfrmtd'),
						'holder' => 'div',
						'class' => '',
						'param_name' => 'keyword_auto',
						'value' => Array(
							__( "Enable", 'jvfrmtd' ) => '',
							__( "Disable", 'jvfrmtd' ) => 'disable',
						)
					),
					Array(
						'type' => 'dropdown',
						'group' => esc_html__( "Fields", 'jvfrmtd' ),
						'heading' => esc_html__( "Amenities Field", 'jvfrmtd'),
						'holder' => 'div',
						'class' => '',
						'param_name' => 'amenities_field',
						'value' => Array(
							__( "Enable", 'jvfrmtd' ) => '',
							__( "Disable", 'jvfrmtd' ) => 'disable',
						)
					),

			)
		);
	}

	public function getFieldParams( $params=Array() ) {

		$arrColumn = $arrParam = Array();
		$intFieldCount = intVal( apply_filters( 'jvbpd_search_field_field_count', 3 ) );
		$intFieldCount = $intFieldCount > 0 ? $intFieldCount : 1;

		for( $intCount = 1; $intCount <= $intFieldCount; $intCount++ ) {
			$arrColumn[ $intCount . ' ' . _n( "Column", "Columns", $intCount, 'jvfrmtd' ) ] = $intCount;
			$arrDepdency = Array();
			for( $intDepdency=$intCount; $intDepdency <= $intFieldCount; $intDepdency++ ) {
				$arrDepdency[] = "{$intDepdency}";
			}
			$arrParam[] = wp_parse_args(
				Array(
					'dependency'	=> Array(
						'element'	=> 'columns',
						'value'		=> $arrDepdency,
					)
				),
				self::fieldParameter( 'column' . $intCount, sprintf( esc_html__( "%d column", 'jvfrmtd'  ), $intCount ) )
			);
		}

		$params[] = Array(
			'type'			=> 'dropdown',
			'group'			=> esc_html__( "Fields", 'jvfrmtd' ),
			'heading'		=> esc_html__( "Columns", 'jvfrmtd'),
			'holder'		=> 'div',
			'class'			=> '',
			'param_name'	=> 'columns',
			'value'			=> $arrColumn
		);
		return wp_parse_args( $arrParam, $params );
	}

	public function addColumnParam( $args=Array() ) {
		$intFieldCount = intVal( apply_filters( 'jvbpd_search_field_field_count', 3 ) );
		$intFieldCount = $intFieldCount > 0 ? $intFieldCount : 1;
		for( $intColumn=1; $intColumn <= $intFieldCount; $intColumn++ ) {
			$args[ 'column' . $intColumn ] = '';
		}
		return $args;
	}

	/*
	Shortcode call back
	 */
	public function parse( $args ) {
		$attr = shortcode_atts(
			apply_filters(
				'jvbpd_search_field_shortcode_atts',
				Array(
					'title' => false,
					'break_submit' => false,
					'disable_submit' => false,
					'remove_button' => true,
					'taxonomy_hide_empty' => false,
					'taxonomy_hide_child' => false,
					'strip_form' => false,
					'query_requester' => 0,
					'border_color' => '#000000',
					'border_width' => 1,
					'button_bg_color' => '',
					'button_text_color'	=> '',
					'keyword_auto'	=> '',
					'columns' => 1,
					'mobile' => false,
					'amenities_field' => '',
					'column4' => '',
					'widths' => Array(),
					'advanced_section_id' => '',
				)
			), $args
		);
		$this->loaded		= true;
		$this->sID			= 'jvbpd_scd' . md5( wp_rand( 0 , 500 ) .time() );
		$this->is_mobile	= (boolean) $attr[ 'mobile' ];
		$this->numCols	= intVal( $attr[ 'columns' ] );

		$this->classes[] = 'column-' . $this->numCols;
		$this->classes[] = 'active';

		if( $this->is_mobile ) {
			$this->classes[] = 'is-mobile';
		}

		// Shortcode Enqueues & Scripts
		add_action( 'wp_footer', Array( $this, 'scripts' ) );
		return $attr;

	}

	/*
	Search shortcode container class
	 */
	public function classes() {
		$arrOutput = Array(
			'javo-shortcode',
			'shortcode-' . get_class( $this ),
			'column-' . $this->numCols,
			'active',
		);
		$arrOutput = Array_Map( 'trim', $arrOutput );

		$strOutput = join( ' ', $arrOutput );
		printf( " class=\"%s\" ", $strOutput );
	}

	/*
	Keyword output
	 */

	public function keyword( $attr=Array() ) {
		$default = null;
		if( isset( $_GET[ 'keyword' ] ) && '' != $_GET[ 'keyword' ] ) {
			$default = $_GET[ 'keyword' ];
		}
		$this->is_keyword_active = true;
		printf(
			"<input type=\"text\" name=\"%s\" value=\"%s\" placeholder=\"%s\" class=\"form-control\">", 'keyword', $default, esc_html__( "Keyword", 'jvfrmtd' )
		);
	}

	public function tags_object( $attr=Array() ) {

		if( !$this->is_keyword_active )
			return false;

		// $arrTerms = $attr[ 'keyword_auto' ] != 'disable' ? get_terms( 'listing_keyword', Array( 'fields' => 'names' ) ) : Array();
		$arrTerms = true ? get_terms( 'listing_keyword', Array( 'fields' => 'names' ) ) : Array();

		printf(
			'<script type="text/javascript">var jvbpd_search_field_tags=%1$s;</script>',
			htmlspecialchars_decode( json_encode( $arrTerms ) )
		);
	}

	/*
	Google address search output
	 */
	public function google_search() {
		$default = null;
		if( isset( $_GET[ 'radius_key' ] ) && '' != $_GET[ 'radius_key' ] ) {
			$default = $_GET[ 'radius_key' ];
		}
		printf(
			'<div class="javo-search-form-geoloc">
				<input type="text" name="%1$s" class="form-control" value="%2$s" placeholder="%3$s">
				<i class="jvbpd-icon2-location1 javo-geoloc-trigger"></i>
			</div>',
			'radius_key', $default,
			esc_html__( "Address", 'jvfrmtd' )
		);
	}

	/*
	Listing category output
	 */
	public function listing_category( $attr=Array(), $columns=0 ) {
		$default = null;
		if( isset( $_GET[ 'category' ] ) && '' != $_GET[ 'category' ] ) {
			$default = $_GET[ 'category' ];
		}
		printf(
			'<select name="%1$s" class="form-control" data-max-items="1" data-selectize data-tax="%2$s" %5$s><option value="">%3$s</option>%4$s</select>',
			'category', 'listing_category',
			esc_html__( "All Categories", 'jvfrmtd' ),
			apply_filters( 'lava_get_selbox_child_term_lists', 'listing_category', null, 'select', $default, 0, 0, '-', Array(
				'hide_empty' => $attr['taxonomy_hide_empty'],
				'hide_child' => $attr['taxonomy_hide_child'],

			) ),
			( $attr[ 'remove_button' ] ? 'data-mode="multi"' : false )
		);
	}

	public function listing_category_with_keyword( $attr=Array(), $columns=0 ) {
		$queried = get_queried_object();
		$default = false;
		if( $queried instanceof WP_Term ) {
			if( $queried->taxonomy == 'listing_category' ) {
				$default = $queried->term_id;
			}
		}elseif( isset( $_GET[ 'category' ] ) && '' != $_GET[ 'category' ]  ) {
			$default = $_GET[ 'category' ];
		}

		printf(
			'<select name="%1$s" class="form-control" data-max-items="1" data-selectize data-category-tag="%2$s" data-tax="%2$s" data-keyword %5$s><option value="">%3$s</option>%4$s</select>',
			'category', 'listing_category',
			strtoupper( esc_html__( "What", 'jvfrmtd' ) ),
			apply_filters( 'lava_get_selbox_child_term_lists', 'listing_category', null, 'select', $default, 0, 0, '-', Array(
				'hide_empty' => $attr['taxonomy_hide_empty'],
				'hide_child' => $attr['taxonomy_hide_child'],
			) ),
			( $attr[ 'remove_button' ] ? 'data-mode="multi"' : false )
		);
		printf( '<img src="%1$s" class="selectize-loading">', jvbpdCore()->assets_url . 'images/selectize-loading.gif' );
	}

	public function location_with_google_search( $attr=Array(), $columns=0 ) {
		$queried = get_queried_object();
		$default = false;
		if( $queried instanceof WP_Term ) {
			if( $queried->taxonomy == 'listing_location' ) {
				$default = $queried->term_id;
			}
		}elseif( isset( $_GET[ 'location' ] ) && '' != $_GET[ 'location' ]  ) {
			$default = $_GET[ 'location' ];
		}
		printf(
			'<div class="field-location hidden"><select name="%1$s" data-max-items="1" class="form-control" data-selectize data-tax="%2$s" %5$s><option value="">%3$s</option>%4$s</select></div>',
			'location', 'listing_location',
			esc_html__( "All Locations", 'jvfrmtd' ),
			apply_filters( 'lava_get_selbox_child_term_lists', 'listing_location', null, 'select', $default, 0, 0, '-', Array(
				'hide_empty' => $attr['taxonomy_hide_empty'],
				'hide_child' => $attr['taxonomy_hide_child'],
			) ),
			( $attr[ 'remove_button' ] ? 'data-mode="multi"' : false )
		);
		echo '<div class="field-google">';
			$this->google_search();
		echo '</div>';
		printf(
			'<div class="jvbpd-switcher toggle admin-color-setting" data-toggle="tooltip" title="%3$s" data-google="%1$s" data-location="%2$s"><b class="b switch"></b></div>',
			esc_html__( "Search by Locations", 'jvfrmtd' ),
			esc_html__( "Search by Address", 'jvfrmtd' ),
			esc_html__( "Address or Location", 'jvfrmtd' )
		);
	}

	public function google_current_loadtion_search($attr=Array(), $columns=0) {
		$default = null;
		if( isset( $_GET[ 'radius_key' ] ) && '' != $_GET[ 'radius_key' ] ) {
			$default = $_GET[ 'radius_key' ];
		}
		printf(
			'<div class="javo-search-form-geoloc">
				<input type="text" name="%1$s" class="form-control" value="%2$s" placeholder="%3$s">
			</div>',
			'radius_key', $default,
			esc_html__( "Address", 'jvfrmtd' )
		);
	}

	public function ajax_search() {
		if( ! function_exists( 'lava_ajaxSearch' ) ) {
			return;
		}

		$queried = get_queried_object();
		$default = false;
		$default_label = NULL;
		if( $queried instanceof WP_Term ) {
			if( $queried->taxonomy == 'listing_category' ) {
				$default = $queried->term_id;
				$default_label = $queried->name;
			}
		}elseif( isset( $_GET[ 'category' ] ) && '' != $_GET[ 'category' ] ) {
			$queried_result = get_term_by( 'slug', $_GET[ 'category' ], 'listing_category' );
			if( $queried_result instanceof WP_Term ) {
				$default = $queried_result->term_id;
				$default_label = $queried_result->name;
			}
		}elseif( isset( $_GET[ 'keyword' ] ) && '' != $_GET[ 'keyword' ] ) {
			$default_label = $_GET[ 'keyword' ];
		}

		echo lava_ajaxSearch()->shortcode->search_form( Array(
			'default_value' => $default_label,
			'strip_form' => true,
			'submit_button' => false,
			'field_name' => 'keyword',
		) );
		printf( '<input type="hidden" name="%1$s" value="%2$s">', 'category', $default );
	}

	public function advanced_field( $params=Array(), $element='' ) {
		printf( '<div class="btn btn-primary" data-toggle="collapse" data-target="#%2$s"><i class="fa fa-cog"></i> %1$s</div>',
			esc_html__( "Advanced", 'jvfrmtd' ),
			$params[ 'advanced_section_id' ]
		);
	}

	/*
	Listing location output
	 */
	public function listing_location( $attr=Array(), $columns=0 ) {
		$default = null;
		if( isset( $_GET[ 'location' ] ) && '' != $_GET[ 'location' ] ) {
			$default = $_GET[ 'location' ];
		}
		printf(
			'<select name="%1$s" class="form-control" data-max-items="1" data-selectize data-tax="%2$s" %5$s><option value="">%3$s</option>%4$s</select>',
			'location', 'listing_location',
			esc_html__( "All Locations", 'jvfrmtd' ),
			apply_filters( 'lava_get_selbox_child_term_lists', 'listing_location', null, 'select', $default, 0, 0, '-', Array(
				'hide_empty' => $attr['taxonomy_hide_empty'],
				'hide_child' => $attr['taxonomy_hide_child'],
			) ),
			( $attr[ 'remove_button' ] ? 'data-mode="multi"' : false )
		);
	}

	/*
	Amenities output
	 */
	public static function amenities( $attr=Array(), $columns=3 ) {
		$strColumns = 'col amenities';
		$arrAmenities = get_terms( 'listing_amenities', Array( 'fields' => 'id=>name', 'hierarchical'=>false ) );

		echo "<div class=\"row search-box-block\">";
		if( $arrAmenities && ! is_wp_error( $arrAmenities ) ) {
			foreach( $arrAmenities as $id => $name ) {
				printf( "
					<div class=\"%1\$s\">
						<label class=\"chk-wrap\">
							<input type=\"checkbox\" name=\"%2\$s[]\" value=\"{$id}\" class=\"form-check-input\" data-tax=\"listing_amenities\">
							{$name}
							<span class=\"form-check-label checkmark\"></span>
						</label>
					</div>",
					$strColumns,
					'amenity'
				);
			}
		}else{
			printf( '<div class="text-center" style="color:#9C9F9F; letter-spacing:1px;">%1$s</div>', esc_html__( "Not Found Any Amenities", 'jvfrmtd' ) );
		}
		echo "</div>";
	}

	public function other_field( $params=Array(), $element='' ) {

		if( ! taxonomy_exists( $element ) ) {
			return;
		}

		$taxonomy = get_taxonomy( $element );
		$placeholder = sprintf( esc_html__( 'All %s', 'jvfrmtd' ), $taxonomy->label );

		printf(
			'<select name="%1$s" data-max-items="1" data-mode="single" data-selectize data-tax="%1$s"><option value="">%2$s</option>%3$s</select>',
			$taxonomy->name, $placeholder, apply_filters( 'lava_get_selbox_child_term_lists', $element, null, 'select', false, 0, 0, '-', Array(
				'hide_empty' => $params['taxonomy_hide_empty'],
				'hide_child' => $params['taxonomy_hide_child'],
			) )
		);
	}

	public function advanced() {
		?>
		<div class="search-box-inline field-advanced">
			<div class="dropdown field-advanced">
				<button class="btn dropdown-toggle btn-block" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="jvbpd-icon2-option"></i>
				</button>
				<div class="dropdown-menu">
					<div class='javo-geoloc-slider'></div>
				</div>
			</div>
		</div>
		<?php
	}

	public function las_custom_term_template( $template='', $term_id=0, $taxonomy='' ) {
		global $post;
		$strTemplatePermalink = '#';
		if( function_exists( 'jvbpd_tso' ) ) {
			$strTemplateID = jvbpd_tso()->get( 'search_sesult_page', 0 );
			$strTemplatePermalink = esc_url( add_query_arg(
				Array(
					'category' => $term_id,
				),
				get_permalink( $strTemplateID )
			) );
		}

		if( $post instanceof WP_Post ) {
			if( intVal( $strTemplateID ) == intVal( $post->ID ) ) {
				$strTemplatePermalink = '#';
			}
		}

		return sprintf( '<span><a href="%1$s" data-object-id="%2$s">%3$s</a></span>', $strTemplatePermalink, $term_id, get_term( $term_id, $taxonomy )->name );
	}

	public function las_load_custom_instances( $instances=Array(), $obj=null ) {
		if( class_exists( 'Lava_Ajax_Search_Terms' ) && function_exists( 'lava_directory' ) ) {
			$instances[ 'listing_category' ] = new Lava_Ajax_Search_Terms( 'listing_category' );
		}
		return $instances;
	}

	public function las_type_replaces($replaces=Array()) {
		$replaces['listing_category'] = esc_html__("Listing Category", 'jvfrmtd');
		return $replaces;
	}

	public function las_prepare_search( $args=Array(), $obj=null ) {
		$args[ 'searchable_items' ][] = 'listing_category';
		return $args;
	}

	public function las_types_order( $types=Array() ) {
		$firstOrder = Array();
		if( in_array( 'listings', $types ) ) {
			$firstOrder[] = 'listings';
		}
		if( in_array( 'listing_category', $types ) ) {
			$firstOrder[] = 'listing_category';
		}
		$types = array_diff( $types, Array( 'listings', 'listing_category') );
		return wp_parse_args( $types, $firstOrder  );
	}

	public static function getSearchHeader( $param ) {
		if( empty( $param[ 'title' ] ) )
			return;

		$strHeader	= $param[ 'title' ];
		?>

		<div class="row jv-search1-header-row">
			<div class="col-xs-12">
				<div class="static-label admin-color-setting">
					<?php echo $strHeader; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function before( $param ) {
		if( true !== $param[ 'strip_form' ] ) {
			$format = '<form action="%1$s" class="%2$s" method="get" data-form>';
		}else{
			$format = '<div data-request="%1$s" class="%2$s" data-form>';
		}
		printf(
			$format,
			esc_attr( apply_filters( 'jvbpd_wpml_link', intVal( $param[ 'query_requester' ] ) ) ),
			'search-type-a-form'
		);
	}

	public function after( $param ) {
		if( true !== $param[ 'strip_form' ] ) {
			$format = '</form>';
		}else{
			$format = '</div>';
		}
		printf( $format );
		do_action('jvbpd_search_field_after');
	}

	/*
	 *
	 * Shortcode output
	 */
	public function render( $param ) {
		global $jvbpd_filter_prices, $jvbpd_tso;

		$widths = !empty( $param[ 'widths' ] ) ? json_decode( $param[ 'widths' ], true ) : Array();

		if( !class_exists( 'Lava_Directory_Manager' ) ) {
			return sprintf(
				'<p align="center">%s</p>',
				esc_html__( "Please, active to the 'Lava Directory manager' plugin", 'jvfrmtd' )
			);
		}

		$arrButtonStyles = Array();
		$arrButtonClass = Array(
			'btn',
			'btn-block',
			'jv-submit-button'
		);

		$strButtonStyles = $strButtonClass = '';

		if( $param[ 'button_bg_color' ] != '' ) {
			$arrButtonStyles[ 'background-color' ] = $param[ 'button_bg_color' ];
		}

		if( $param[ 'button_text_color' ] != '' ) {
			$arrButtonStyles[ 'color' ] = $param[ 'button_text_color' ];
		}

		if( empty( $arrButtonStyles ) ) {
			$arrButtonClass[] = 'admin-color-setting';
		}

		$strButtonClass = join( ' ', $arrButtonClass );
		if( !empty( $arrButtonStyles ) ) : foreach( $arrButtonStyles as $strProperty => $strValue ){
			$strButtonStyles .= "{$strProperty}:{$strValue};";
		} endif; ?>


		<div id="<?php echo sanitize_html_class( $this->sID ); ?>" data-not-submit="<?php echo true == $param[ 'break_submit' ] ? 'true' : 'false'; ?>">
			<?php
			self::getSearchHeader( $param );
			$this->before( $param );

			$intColumnWidth = floor( 100 / intVal( $param[ 'columns' ] ) ) . '%';
			for( $intCount=1; $intCount <= intVal( $param[ 'columns' ] ); $intCount++ ) {
				if( isset( $param[ 'column' . $intCount ] ) ) {
					printf(
						'<div class="search-box-inline field-%1$s" style="width:%2$s;">',
						$param[ 'column' . $intCount ],
						( isset( $widths[ 'column' . $intCount ] ) ? intVal( $widths[ 'column' . $intCount ] ) . '%' : $intColumnWidth )
					);
					do_action( 'jvbpd_search_field_element_' . $param[ 'column' . $intCount ], $param, $param[ 'column' . $intCount ] );
					printf( '</div>' );
				}
			}

			$this->after( $param ); ?>
		</div>

		<?php
		$arrOutput		= Array();
		$arrOutput[]	= "<script type=\"text/javascript\">";
		$arrOutput[]	= sprintf( 'jQuery( function($){ if(typeof $.jvbpd_search_shortcode !="undefined"){ $.jvbpd_search_shortcode( "#%1$s", "%2$s" ); } });', $this->sID, admin_url( 'admin-ajax.php' ) );
		$arrOutput[]	= "</script>";
		echo join( '', $arrOutput );
	}

	public function getCustomIcons() {

		if( ! function_exists( 'lava_directory' ) ) {
			return json_encode( array() );
		}

		$result = Array();
		$taxonomies = Array_keys( (Array) Array_filter( apply_filters( 'lava_lv_listing_taxonomies', array() ) ) );
		foreach( $taxonomies as $taxonomy ) {
			$terms = get_terms( Array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'fields' => 'ids' ) );
			if( ! is_wp_error( $terms ) ) {
				foreach( $terms as $term_id ) {
					if( false !== ( $iconcode = lava_directory()->admin->getTermOption( $term_id, 'icon', $taxonomy, false ) ) ) {
						$result[ $term_id ] = $iconcode;
					}
				}
			}
		}
		return json_encode( $result );
	}

	/*
	Enqueue script
	 */
	public function scripts() {
		if( !$this->loaded )
			return;

		wp_enqueue_script( 'jquery-nouislider' );
		wp_enqueue_script( 'selectize' );
		// wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'jquery-type-header' ) );
		wp_localize_script(
			// jvbpdCore()->var_instance->getHandleName( 'jquery-jvbpd-search' ),
			jvbpdCore()->var_instance->getHandleName( 'frontend' ),
			'jvbpd_search1_param',
			Array(
				'icons' => $this->getCustomIcons(),
				'strings' => Array(
					'current_location' => esc_html__("Current Location", 'jvfrmtd'),
					'geolocation_fail' => esc_html__("failed find address", 'jvfrmtd'),
				),
			)
		);

		// wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'jquery-jvbpd-search' ) );
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}