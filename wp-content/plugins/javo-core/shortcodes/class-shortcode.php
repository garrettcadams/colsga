<?php
if( !class_exists( 'Jvbpd_Shortcode_Parse' ) && class_exists( 'Jvbpd_Core' ) ) :

	class Jvbpd_Shortcode_Parse
	{

		protected $sID;

		protected $attr = Array();

		protected $query = NULL;

		protected $fixCount = false;

		private $prefix = false;

		public $enq_prefix = '';

		public function __construct( $attr=Array() )
		{
			$this->prefix	= Jvbpd_Core::get_instance()->prefix . '_';
			$attr[ 'filter_style' ] = isset( $attr[ 'filter_style' ] ) && '' != $attr[ 'filter_style' ] ? $attr[ 'filter_style' ] : '';
			$this->attr		= shortcode_atts(
				apply_filters( $this->prefix . 'shortcodes_atts' ,
					Array(
						// Default Parametter
						'title'							=> '',
						'subtitle'					=> '',
						'css'							=> '',
						'author'						=> null,
						'block_type'					=> 'post',
						'post_type'					=> 'post',
						'count'						=> '',
						'taxonomy'					=> '',
						'term_id'						=> '',
						'columns'						=> 1,
						'paged'						=> 1,
						'pagination'					=> '',
						'offset'						=> '',
						'thumbnail_size'				=> '',
						'order_'						=> false,
						'order_by'					=> '',
						'filter_by'					=> '',
						'filter_style'				=> 'general',
						'loading_style'				=> '',
						'custom_filter'				=> '',
						'hide_filter'					=> false,
						'primary_color'				=> '',
						'primary_font_color'			=> '',
						'primary_border_color'		=> '',
						'custom_filter_by_post'		=> '',
						'post_title_font_color'		=> '',
						'post_title_font_size'		=> false,
						'post_title_transform'		=> 'inherit',
						'post_meta_font_color'		=> '',
						'post_describe_font_color'	=> '',
						'display_category_tag'		=> '',
						'display_post_border'		=> '',
						'category_tag_color'		=> '#454545',
						'category_tag_hover_color'	=> '',
						'slide_wide'				=> 0,
						'module_contents_hide'		=> ''	,
						'module_contents_length'	=> '',
						'hide_thumbnail'			=> '',
						'hide_avatar'			=> false,
						'hover_style' => '',
						'is_dashboard' => false,

						'post__in' => '',
						'exclude' => '',

						'block_module' => '',

						'block_display_type' => '',
						'carousel' => '',

						'filter_terms' => '',
						'masonry' => '',

						'module_click_popup' => '',

						'layout_type' => 'type_a',
						'column_1' => '',
						'column_2' => '',
					)
				)
				, $attr
			);

			$this->attr[ 'shortcode_name' ]	= get_class( $this );
			$this->sID = 'jvbpd_scd' . md5( wp_rand( 0 , 500 ) .time() );

			if( ! empty( $this->attr ) ) foreach( $this->attr as $key => $value ) {
				$this->$key = $value;
			}

			if( !empty( $this->subtitle ) && $this->filter_style == 'paragraph' ) {
				$this->title = sprintf( "<div class='sc-subtitle'>%s</div><div class='sc-title'>%s</div>", $this->subtitle, $this->title );
			}

			$this->hide_filter = empty( $this->filter_style );

			add_filter( 'jvbpd_core_module_excerpt_length', Array( $this, 'trim_string' ) );
			add_filter( 'jvbpd_module_shortcode_args', Array( $this, 'sendArgs' ) );
			do_action( 'jvbpd_parsed_shortcode', $this );
		}

		public function getID(){
			return $this->sID;
		}

		public function get_post() {
			$arrPostsArgs = Array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'posts_per_page' => 10,
				'paged' => 1,
			);

			if( !empty( $this->post__in ) ) {
				$post__in = $this->post__in;
				if( ! is_array( $post__in ) ) {
					$post__in = explode( ',',  $post__in );
				}
				$arrPostsArgs[ 'post__in' ] = array_map( 'intVal', $post__in );
			}

			$post__not_in = get_option( 'sticky_posts' );
			if( !empty( $this->exclude ) ) {
				$post__not_in = $this->exclude;
				if( ! is_array( $post__not_in ) ) {
					$post__not_in = explode( ',',  $post__not_in );
				}
				$post__not_in = array_map( 'intVal', $post__not_in ) + get_option( 'sticky_posts' );
			}
			$arrPostsArgs[ 'post__not_in' ] = array_map( 'intVal', $post__not_in );

			if( $this->post_type )
				$arrPostsArgs[ 'post_type' ]			= $this->post_type;

			if( $count = intVal( $this->count ) )
				$arrPostsArgs[ 'posts_per_page' ]	= $count;

			if( $this->fixCount )
				$arrPostsArgs[ 'posts_per_page' ]	= intVal( $this->fixCount );

			if( $this->is_dashboard )
				$arrPostsArgs[ 'post_status' ] = Array( 'publish', 'pending' );

			if(
				!empty( $this->filter_by ) &&
				!empty( $this->custom_filter_by_post ) &&
				!empty( $this->custom_filter )
			) {
				if( (boolean) $this->custom_filter_by_post ) {
					$arrCustomFilter = $arrInclude = $arrExclude = Array();
					$arrCustomFilter = @explode( ',', $this->custom_filter );
					if( !empty( $arrCustomFilter ) ) : foreach( $arrCustomFilter as $terms  )
						if( intVal( $terms ) > 0 ) {
							$arrInclude[] = intVal( $terms );
						}elseif( intVal( $terms ) < 0 ){
							$arrExclude[] = intVal( abs( $terms ) );
						}
					endif;
				}

				$arrPostsArgs[ 'tax_query' ] = Array();

				if( !empty( $arrInclude ) ) $arrPostsArgs[ 'tax_query' ][] = Array(
					'taxonomy' => $this->filter_by,
					'field' => 'term_id',
					'terms' => $arrInclude,
				);
				if( !empty( $arrExclude ) ) $arrPostsArgs[ 'tax_query' ][] = Array(
					'taxonomy' => $this->filter_by
					, 'operator' => 'NOT IN'
					, 'field' => 'term_id'
					, 'terms' => $arrExclude
				);
			}

			if(
				$this->taxonomy != '' &&
				intVal( $this->term_id ) > 0 ) {
				$arrPostsArgs[ 'tax_query' ]		= Array(
					Array(
						'taxonomy' => $this->taxonomy
						, 'field' => 'term_id'
						, 'terms' => $this->term_id
					)
				);
			}

			if( $this->order_by ) {
				$arrPostsArgs[ 'orderby' ]		= $this->order_by;
				if( $this->order_ ) {
					$arrPostsArgs[ 'order' ]		= strtoupper( $this->order_ );
				}
			}

			if( intVal( $this->paged ) > 0 )
				$arrPostsArgs[ 'paged' ]				= $this->paged;

			if( intVal( $this->author ) > 0 )
				$arrPostsArgs[ 'author' ]				= $this->author;

			$arrPostsArgs = apply_filters( $this->prefix . 'shotcode_query', $arrPostsArgs, $this );
			$this->query = new WP_Query( $arrPostsArgs );
			return $this->query->posts;
		}

		public function get_terms() {
			$output = Array();
			$taxonomy = $this->filter_by;
			$args = Array(
				'taxonomy' => $taxonomy,
				'hide_empty' => false,
				'fields' => 'all',
				'number' => $this->count,
			);
			if( ! taxonomy_exists( $taxonomy ) ) {
				return $output;
			}
			$filter_terms  = array_filter( explode( ',', $this->filter_terms . ',' ) );
			if( !empty($filter_terms) ) {
				$args['include'] = $filter_terms;
			}
			return get_terms( $args );
		}

		public function classes( $classes='', $exclude_object=false ) {
			$arrClasses	= Array( 'javo-shortcode' );

			if( !empty( $classes ) )
				$arrClasses[]	= $classes;

			if( !$exclude_object )
				$arrClasses[]	= 'shortcode-' . get_class( $this );

			if(
				$this->title == '' &&
				( $this->filter_by == '' || $this->hide_filter )
			){
				$arrClasses[]	= 'header-hide';
			}elseif( $this->title == '' ){
				$arrClasses[]	= 'title-hide';
			}elseif( $this->filter_by == '' ){
				$arrClasses[]	= 'filter-hide';
			}

			if( !empty( $this->filter_style ) )
				$arrClasses[]	= 'filter-' . $this->filter_style;

			if( !empty( $this->loading_style ) )
				$arrClasses[]	= 'loader-' . $this->loading_style;

			if( !empty( $this->post_type ) )
				$arrClasses[]	= 'type-' . $this->post_type;

			if( !empty( $this->hover_style ) )
				$arrClasses[]	= 'module-hover-' . $this->hover_style;

			if( !empty( $this->slide_wide ) )
				$arrClasses[]	= 'slide-wide';

			if( !empty( $this->hide_thumbnail ) )
				$arrClasses[]	= 'thumbnail-hide';

			if( $this->block_display_type == 'carousel' )
				$arrClasses[]	= 'is-carousel';

			if( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) && function_exists( 'vc_shortcode_custom_css_class' ) ){
				$arrClasses[] = apply_filters(
					VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,
					vc_shortcode_custom_css_class( $this->css, ' ' ),
					get_class( $this ),
					$this->attr
				);
			}

			$strClasses	= join( ' ', (array) apply_filters( 'jvbpd_shortcode_class', $arrClasses, get_class( $this ), $this ) );

			$oppend = '';
			if( $this->carousel ) {
				$oppend = sprintf( " data-carousel='%s' ", $this->carousel );
			}

			return " class=\"{$strClasses}\" " . $oppend;
		}

		public function sHeader() {
			add_action( 'wp_footer', Array( $this, 'enqueue' ) );

			$arrCustomCSS				= Array();
			$general_prefix				= ".filter-general #{$this->sID}" . ' ';
			$linear_prefix				= ".filter-linear #{$this->sID}" . ' ';
			$paragraph_prefix			= ".filter-paragraph #{$this->sID}" . ' ';

			// Primary Color
			if( $css = ( !empty( $this->primary_color ) ? $this->primary_color : jvbpd_tso()->get( 'total_button_color', '#000' ) ) ) {

				$primaryHex				= apply_filters( 'jvbpd_rgb', substr( $css, 1 ) );
				$intColorR				= empty( $primaryHex[ 'r' ]  )	? 0 : $primaryHex[ 'r' ];
				$intColorG				= empty( $primaryHex[ 'g' ]  )	? 0 : $primaryHex[ 'g' ];
				$intColorB				= empty( $primaryHex[ 'b' ]  )	? 0 : $primaryHex[ 'b' ];

				/* Header */
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-header{ border-color:{$css}; }";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-header .shortcode-title{ background-color:{$css}; }";

				$arrCustomCSS[]			= "#{$this->sID} div.shortcode-output .primary-bg,";
				$arrCustomCSS[]			= "#{$this->sID} div.shortcode-output .primary-bg-a > a";
				/** ----------------------------  */
				$arrCustomCSS[]			= "{ background-color:{$css}; }";

				/* Primary color font */
				$arrCustomCSS[]			= "#{$this->sID} div.shortcode-output .primary-color-font{ color:{$css} !important; }";

				/* Featured Category */
				// $arrCustomCSS[]			= "#{$this->sID} .shortcode-output .thumb-wrap .meta-status";
				/** ----------------------------  */
				// $arrCustomCSS[]			= "{ background-color:{$css} !important; }";

				/* Login Form */
				$arrCustomCSS[]			= "form#{$this->sID}_login_form > p.login-submit > input[type='submit']";
				/** ----------------------------  */
				$arrCustomCSS[]			= "{ background-color:{$css} !important; }";

				/* Rating */
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output .meta-rating-nomeric{ background-color:{$css}; }";
				$arrCustomCSS[]			= ".display-rating-garde > #{$this->sID} .shortcode-output .module.media > .media-left > a:before,";
				$arrCustomCSS[]			= ".display-rating-garde > #{$this->sID} .shortcode-output .module > .thumb-wrap:after";
				/** ----------------------------  */
				$arrCustomCSS[]			= "{ background-color:rgba( {$intColorR}, {$intColorG}, {$intColorB}, .9); }";

				/* Price */
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output .module.javo-module1 .media-body .meta-price,";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output .meta-wrap .meta-price";
				/** ----------------------------  */
				$arrCustomCSS[]			= "{ background-color:{$css}; }";

				/* Fade */
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output .thumb-wrap:hover .javo-thb:after{ background-color:rgba( {$intColorR}, {$intColorG}, {$intColorB}, .92); }";

				/* Pagination */
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output .page-numbers.loadmore:hover,";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output ul.pagination > li > a:hover,";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output ul.pagination > li.active > a,";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output ul.pagination > li.active > a:hover,";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output ul.pagination > li.active > a:focus,";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output ul.pagination > li.active > span,";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output ul.pagination > li.active > span:hover,";
				$arrCustomCSS[]			= "#{$this->sID} .shortcode-output ul.pagination > li.active > a:focus";
				/** ----------------------------  */
				$arrCustomCSS[]			= "{ color:#fff; background-color:{$css} !important; border-color:{$css}!important;} ";

				switch( $this->filter_style )
				{
					// Filter General
					case 'general' :
						$arrCustomCSS[]	= $general_prefix . ".shortcode-header .shortcode-nav ul li.current{ color:{$css}; }";
						$arrCustomCSS[]	= $general_prefix . ".shortcode-header .shortcode-title{ background-color:{$css}; }";
						break;

					// Filter Linear
					case 'linear' :
						$arrCustomCSS[]	= $linear_prefix . ".shortcode-header .shortcode-title{ background-color:transparent; }";
						$arrCustomCSS[]	= $linear_prefix . ".shortcode-header .shortcode-nav ul li:hover{ border-color:{$css}; }";
						$arrCustomCSS[]	= $linear_prefix . ".shortcode-header .shortcode-nav ul li.current{ border-color:{$css}; }";
						break;

					// Filter Paragraph
					case 'paragraph' :
						$arrCustomCSS[]	= $paragraph_prefix . ".shortcode-header .shortcode-title{ background-color:transparent; }";
						$arrCustomCSS[]	= $paragraph_prefix . ".shortcode-header .shortcode-nav ul li.active{ border-color:{$css}; }";
						break;
				}
				if( get_class( $this ) == 'jvbpd_vblock1' ){
					$arrCustomCSS[] = ".shortcode-lynk_vblock1 > #{$this->sID}{ border-top-color:{$css}; }";
					$arrCustomCSS[] = ".shortcode-lynk_vblock1 > #{$this->sID} > .shortcode-content > .shortcode-nav > .shortcode-filter > li.current,";
					$arrCustomCSS[] = ".shortcode-lynk_vblock1 > #{$this->sID} > .shortcode-content > .shortcode-nav > .shortcode-filter > li:not(.current):hover";
					$arrCustomCSS[] = "{ background-color:{$css} !important; color:#fff !important; }";
					$arrCustomCSS[] = ".shortcode-lynk_vblock1 > #{$this->sID} > div.shortcode-output .javo-module4 .jv-hover-back-info";
					/** ----------------------------  */
					$arrCustomCSS[] = "{background-color:{$css};}";
				}

			}

			// Primary Border Color
			if($css =  ( !empty( $this->primary_border_color ) ? $this->primary_border_color :  jvbpd_tso()->get( 'total_button_border_color', false ) ) ){
				$arrCustomCSS[] = "html body #{$this->sID} .shortcode-output .jv-button-transition,";
				$arrCustomCSS[] = "html body #{$this->sID} .shortcode-output .jv-button-transition:hover,";
				$arrCustomCSS[] = "html body #{$this->sID} .shortcode-output .admin-color-setting,";
				$arrCustomCSS[] = "html body #{$this->sID} .shortcode-output .admin-color-setting:hover";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ border-color:{$css} !important; }";

				/* Login Form */
				$arrCustomCSS[] = "form#{$this->sID}_login_form > p.login-submit > input[type='submit']";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ border-color:{$css} !important; }";
			}

			// Primary Font Color
			if( $css = ( !empty( $this->primary_font_color ) ? $this->primary_font_color : false ) ) {
				/* Header */
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-header  .shortcode-title{ color:{$css}; }";

				/* Filters */
				switch( $this->filter_style ) {
					case 'linear' :
						$arrCustomCSS[]	= $linear_prefix . ".shortcode-header .shortcode-title{ color:{$css}; }";
						break;
				}

				/* Featured */
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module.javo-module1 .media-body .author-name > span,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module.javo-module8 .meta-price-prefix,";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ color:{$css}; }";

				/* Block 6 */
				$arrCustomCSS[] = "#{$this->sID} .shortcode-output .jv-thumb .meta-category{color:{$css};}";
			}

			// Post Title Font Color
			if( $css = ( !empty( $this->post_title_font_color ) ? $this->post_title_font_color : false ) ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output  .meta-title,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output  .meta-title a,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output  .media-heading,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output  .media-heading a";
				/** ----------------------------  */
				// $arrCustomCSS[] = "{ color:{$css} !important; }";
				$arrCustomCSS[] = "{ color:{$css}; }";
			}

			// Post Meta Font Color
			if( $css = ( !empty( $this->post_meta_font_color ) ? $this->post_meta_font_color : false ) ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module-meta,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module-meta li,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module-meta li i,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module-meta li a,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module-meta a";
				/** ----------------------------  */
				// $arrCustomCSS[] = "{ color:{$css} !important; }";
				$arrCustomCSS[] = "{ color:{$css}; }";
			}

			// Post Describe Font Color
			if( $css = ( !empty( $this->post_describe_font_color ) ? $this->post_describe_font_color : false ) ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .meta-excerpt,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .meta-excerpt a";
				/** ----------------------------  */
				//$arrCustomCSS[] = "{ color:{$css} !important; }";
				$arrCustomCSS[] = "{ color:{$css}; }";
			}

			// Category Tag Color
			if( $css = ( !empty( $this->category_tag_color ) ? $this->category_tag_color : false ) ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .meta-category:not(.no-background)";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ background-color:{$css} !important; color:#fff !important; }";
			}

			// Category Tag hover Color
			if( $css = ( !empty( $this->category_tag_hover_color ) ? $this->category_tag_hover_color : false ) ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module:hover .meta-category:not(.no-background)";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ background-color:{$css} !important; color:#fff !important; }";
			}

			// Visibile / hidden Category Tag
			if( 'hide' == ( !empty( $this->display_category_tag ) ? $this->display_category_tag : false ) ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .meta-category";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ display:none !important; visibility:hidden  !important; }";
			}

			// Post Title Font Size
			if( $css = ( !empty( $this->post_title_font_size ) ? $this->post_title_font_size : false ) ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output h4.meta-title,";
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output h4.meta-title a";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ font-size:{$css}px !important; }";
			}

			// Post Title Transform
			if( $css = ( !empty( $this->post_title_transform ) ? $this->post_title_transform : false ) ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output h4.meta-title";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ text-transform:{$css} !important; }";
			}

			// Display Post Border
			if( '1' == $this->display_post_border ) {
				$arrCustomCSS[] = "div#{$this->sID} .shortcode-output .module";
				/** ----------------------------  */
				$arrCustomCSS[] = "{ border-color: transparent !important; }";
			}

			$arrCustomCSS		= apply_filters( 'jvbpd_shortcode_css', $arrCustomCSS, $this );
			$strCustomCSS		= join( false, $arrCustomCSS );

			// printf( "\n<style type=\"text/css\">\n%s\n</style>\n<div %s>", $strCustomCSS, $this->classes() );
			printf( "\n<div %s>", $this->classes() );
		}

		public function getFilterItems( $args=Array() ) {
			$args = wp_parse_args( $args,
				Array(
					'taxonomy' => NULL,
					'parent' => 0,
					'parentsNames' => '',
					'depth' => 0,
				)
			);

			$term = get_term( $args[ 'parent' ], $args[ 'taxonomy' ] );
			$output = '';
			if( $term instanceof WP_Term ) {
				$output = sprintf( '<li data-term="%1$s" class="filter-nav-item term-id-%1$s">%2$s</li>', $term->term_id, $term->name );
			}
			return $output;
		}

		public function sFilter() {
			if( empty( $this->filter_by ) )
				return;

			if( $this->hide_filter ) {

			}else{
				$filter_args = Array();
				if( taxonomy_exists( $this->filter_by ) ) {
					$arrCustomFilter = $arrInclude = $arrExclude = Array();

					$filter_terms  = array_filter( explode( ',', $this->filter_terms . ',' ) );
					//if( (boolean) $this->custom_filter_by_post ) {
					if( !empty( $filter_terms  ) ) {
						// $arrCustomFilter = @explode( ',', $this->custom_filter );
						// if( !empty( $arrCustomFilter ) ) : foreach( $arrCustomFilter as $terms  )
						foreach( $filter_terms as $terms ) {
							if( intVal( $terms ) > 0 ) {
								$arrInclude[] = intVal( $terms );
							}else{
								$arrExclude[] = intVal( $terms );
							}
						}
						//endif;
					}

					if( 0 < sizeof( $arrInclude ) ) {
						$filter_args[ 'include' ] = $arrInclude;
					}

					if( 0 < sizeof( $arrExclude ) ) {
						$filter_args[ 'exclude' ] = $arrExclude;
					}

					$htmlFilter = Array();

					/*
					if( !empty( $this->filter_terms ) ) {
						$objTerms = get_terms( Array( 'taxonomy' => $this->filter_by, 'parent' => $this->filter_terms,'fields' => 'id=>name' ) );
					}else{ */
						$objTerms = get_terms( Array( 'taxonomy' => $this->filter_by, 'fields' => 'ids' ) + $filter_args );
						/*
					} */

					$htmlFilter[]	= "<ul data-tax=\"{$this->filter_by}\" class=\"shortcode-filter \"" . ' ';
					$htmlFilter[]	= sprintf( "data-more=\"%s\" data-mobile=\"%s\">", __( "More", 'jvfrmtd' ), __( "Filter", 'jvfrmtd' ) );
					$htmlFilter[]	= sprintf( "<li class='filter-nav-item term-all current'>%s</li>", __( "All", 'jvfrmtd' ) );
						if( !empty( $objTerms ) ) foreach( $objTerms as $term_id ) {
							$htmlFilter[] = $this->getFilterItems( Array( 'taxonomy' => $this->filter_by, 'parent' => $term_id ) );
						}
					$htmlFilter[] = "</ul>";
					echo @implode( "\n", $htmlFilter );
				}
			}
		}

		public function sParams(){
			$serArgs = wp_json_encode( $this->attr, JSON_NUMERIC_CHECK );
			$htmlScripts = Array();
			$htmlScripts[]	= "<script type=\"text/javascript\" id=\"js-{$this->sID}\">";
			$htmlScripts[]	= "jQuery( function($){";
			$htmlScripts[]	= sprintf( "document.ajaxurl =\"%s\";", admin_url( 'admin-ajax.php' ) );
			$htmlScripts[]	= "$.jvbpd_ajaxShortcode( '{$this->sID}', {$serArgs}); });";
			$htmlScripts[]	= "</script>";
			echo implode( "\n", $htmlScripts );
		}

		public function trim_string( $intLength )
		{
			if( !empty( $this->module_contents_hide ) )
				return 0;

			if( intVal( $this->module_contents_length ) > 0 || $this->module_contents_length === '0' )
				$intLength = intVal( $this->module_contents_length );
			return $intLength;
		}

		public function sendArgs( $args ) {
			return wp_parse_args( $this->attr, $args );
		}

		public function pagination() {

			if( $this->block_type != 'post' ) {
				return;
			}

			if( $this->block_display_type == 'carousel' ) {
				return;
			}

			$cntMax_PageNumber = intVal( $this->query->max_num_pages );
			$cntCurrent_Number = max( 1, intVal( $this->paged ) ) ;
			$strMoreButton = '';

			if( empty( $this->pagination ) )
				return;

			printf( '<div class="jv-pagination %1$s">', ( $this->pagination == 'prevNext' ? 'text-left' : 'text-center' ) );
			switch( $this->pagination ) :

				case 'loadmore' :

					if( $cntCurrent_Number < $cntMax_PageNumber )
					{
						$cntCurrent_NumberUP = $cntCurrent_Number+1;
						$strMoreButton = "
							<a href='javascript:' data-href=\"loadmore|{$cntCurrent_NumberUP}\" class=\"page-numbers loadmore btn jv-btn-bright outline\">
								%s
							</a>
							";
					}else{
						$strMoreButton = "";
					}
					if( !empty( $strMoreButton ) )
						printf( $strMoreButton, __( "Load More", 'jvfrmtd' ) );
				break;

				case 'number' :
				//default :
					$arrPagination		= paginate_links(
						Array(
							'base'			=> '|%#%'
							, 'format'		=> '%#%'
							, 'current'		=> $cntCurrent_Number
							, 'total'			=> $cntMax_PageNumber
							, 'type'			=> 'array'
							, 'prev_text'	=> '&lt;'
							, 'next_text'	=> '&gt;'
						)
					);
					echo "<ul class=\"pagination\">";
					if( is_Array( $arrPagination ) ) foreach( $arrPagination as $pLink ) {
						$strCurrent	= strpos( $pLink, 'current' ) !== false ? " class=\"active\" " : '';
						echo "<li{$strCurrent}>{$pLink}</li>";
					}
					echo "</ul>";
				break;

				case 'prevNext' :
				default:

					$pagination = Array();
					if( 1 < $cntCurrent_Number ) {
						$pagination[] = sprintf(
							'<li class="page-item"><a href="#" data-href="prevNext|%2$s" class="page-link page-numbers prevNext previous">%1$s</a></li>',
							'<i class="fa fa-angle-left"></i>',
							abs( $cntCurrent_Number - 1 )
						);
					}else{
						$pagination[] = sprintf(
							'<li class="page-item disabled"><a href="#" data-href="prevNext|%2$s" class="page-link page-numbers previous">%1$s</a></li>',
							'<i class="fa fa-angle-left"></i>',
							abs( $cntCurrent_Number - 1 )
						);
					}

					if( $cntCurrent_Number < $cntMax_PageNumber ) {
						$pagination[] = sprintf(
							'<li class="page-item"><a href="#" data-href="prevNext|%2$s" class="page-link page-numbers prevNext next">%1$s</a></li>',
							'<i class="fa fa-angle-right"></i>',
							abs( $cntCurrent_Number + 1 )
						);
					}else{
						$pagination[] = sprintf(
							'<li class="page-item disabled"><a href="#" data-href="prevNext|%2$s" class="page-link page-numbers">%1$s</a></li>',
							'<i class="fa fa-angle-right"></i>',
							abs( $cntCurrent_Number + 1 )
						);
					}

					printf( '<ul class="pagination">%s</ul>', join( false, $pagination ) );

			endswitch;
			echo "</div>";
			wp_reset_query();
		}

		public function sFooter() {
			// Shortcode Close
			/*
			echo "
				<div class=\"output-cover\"></div>
				<div class=\"output-loading\"></div>
			</div> <!-- /.Shortcode End {$this->sID} -->
			"; */
			echo "<div class=\"output-loading\"></div></div> <!-- /.Shortcode End {$this->sID} -->";
			do_action( 'jvbpd_shortcode_after', $this );
		}

		public function enqueue() {
			wp_enqueue_script( $this->enq_prefix . 'jquery-flexslider-min-js' );
			wp_enqueue_script( 'owl-carousel' );
			wp_enqueue_script( 'flexmenu' );
			wp_enqueue_script( 'jquery-javo-ajaxshortcode' );
		}

		public function loop( $queried_posts ) {
			return apply_filters( $this->prefix . 'shortcodes_loop', '', $queried_posts, $this );
		}
	}

endif;