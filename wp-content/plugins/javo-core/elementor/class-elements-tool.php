<?php
/**
 * Javo addons helper class
 */

use Elementor\Plugin;

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'jvbpd_elements_tools' ) ) {
	class jvbpd_elements_tools {

		public $terms = Array();
		public $taxonomies = Array( '' );
		public $blocks = Array();

		public $cacheRelativeListingMarkers = Array();
		public $cacheRelativeListingMarkerUrls = Array();

		public $preview_post = null;

		private static $instance = null;

		public function col_classes( $columns = array() ) {
			$columns = wp_parse_args( $columns, array(
				'desk' => 1,
				'tab'  => 1,
				'mob'  => 1,
			) );

			$classes = array();

			foreach ( $columns as $device => $cols ) {
				if ( ! empty( $cols ) ) {
					$classes[] = sprintf( 'col-%1$s-%2$s', $device, $cols );
				}
			}

			return implode( ' ' , $classes );
		}

		public function gap_classes( $use_cols_gap = 'yes', $use_rows_gap = 'yes' ) {

			$result = array();

			foreach ( array( 'cols' => $use_cols_gap, 'rows' => $use_rows_gap ) as $element => $value ) {
				if ( 'yes' !== $value ) {
					$result[] = sprintf( 'disable-%s-gap', $element );
				}
			}

			return implode( ' ', $result );

		}

		public function get_image_sizes() {

			global $_wp_additional_image_sizes;

			$sizes  = get_intermediate_image_sizes();
			$result = array( 'wh-50px' => esc_html__("50 x 50 px") );

			foreach ( $sizes as $size ) {
				if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
					$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
				} else {
					$result[ $size ] = sprintf(
						'%1$s (%2$sx%3$s)',
						ucwords( trim( str_replace( array( '-', '_', 'jvbpd' ), array( ' ', ' ', 'jv' ), $size ) ) ),
						$_wp_additional_image_sizes[ $size ]['width'],
						$_wp_additional_image_sizes[ $size ]['height']
					);
				}
			}

			return array_merge( array( 'full' => esc_html__( 'Full', 'jvfrmtd' ), ), $result );
		}

		public function get_categories() {

			$categories = get_categories();

			if ( empty( $categories ) || ! is_array( $categories ) ) {
				return array();
			}

			return wp_list_pluck( $categories, 'name', 'term_id' );

		}

		public function get_theme_icons_data() {

			$default = array(
				'icons'  => false,
				'format' => 'fa %s',
				'file'   => false,
			);

			$icon_data = apply_filters( 'jvbpd-elements/controls/icon/data', $default );
			$icon_data = array_merge( $default, $icon_data );

			return $icon_data;
		}

		public function orderby_arr() {
			return array(
				'none'          => esc_html__( 'None', 'jvfrmtd' ),
				'ID'            => esc_html__( 'ID', 'jvfrmtd' ),
				'author'        => esc_html__( 'Author', 'jvfrmtd' ),
				'title'         => esc_html__( 'Title', 'jvfrmtd' ),
				'name'          => esc_html__( 'Name (slug)', 'jvfrmtd' ),
				'date'          => esc_html__( 'Date', 'jvfrmtd' ),
				'modified'      => esc_html__( 'Modified', 'jvfrmtd' ),
				'rand'          => esc_html__( 'Rand', 'jvfrmtd' ),
				'comment_count' => esc_html__( 'Comment Count', 'jvfrmtd' ),
				'menu_order'    => esc_html__( 'Menu Order', 'jvfrmtd' ),
			);
		}

		public function order_arr() {

			return array(
				'desc' => esc_html__( 'Descending', 'jvfrmtd' ),
				'asc'  => esc_html__( 'Ascending', 'jvfrmtd' ),
			);

		}

		public function verrtical_align_attr() {
			return array(
				'baseline'    => esc_html__( 'Baseline', 'jvfrmtd' ),
				'top'         => esc_html__( 'Top', 'jvfrmtd' ),
				'middle'      => esc_html__( 'Middle', 'jvfrmtd' ),
				'bottom'      => esc_html__( 'Bottom', 'jvfrmtd' ),
				'sub'         => esc_html__( 'Sub', 'jvfrmtd' ),
				'super'       => esc_html__( 'Super', 'jvfrmtd' ),
				'text-top'    => esc_html__( 'Text Top', 'jvfrmtd' ),
				'text-bottom' => esc_html__( 'Text Bottom', 'jvfrmtd' ),
			);
		}

		public function get_select_range( $to = 10 ) {
			$range = range( 1, $to );
			return array_combine( $range, $range );
		}

		public function get_badge_placeholder() {
			return jvbpd_elements()->plugin_url( 'assets/images/placeholder-badge.svg' );
		}

		public function get_image_by_url( $url = null, $attr = array() ) {

			$url = esc_url( $url );

			if ( empty( $url ) ) {
				return;
			}

			$ext  = pathinfo( $url, PATHINFO_EXTENSION );
			$attr = array_merge( array( 'alt' => '' ), $attr );

			if ( 'svg' !== $ext ) {
				return sprintf( '<img src="%1$s"%2$s>', $url, $this->get_attr_string( $attr ) );
			}

			$base_url = network_site_url( '/' );
			$svg_path = str_replace( $base_url, ABSPATH, $url );
			$key      = md5( $svg_path );
			$svg      = get_transient( $key );

			if ( ! $svg ) {
				$svg = file_get_contents( $svg_path );
			}

			if ( ! $svg ) {
				return sprintf( '<img src="%1$s"%2$s>', $url, $this->get_attr_string( $attr ) );
			}

			set_transient( $key, $svg, DAY_IN_SECONDS );

			unset( $attr['alt'] );

			return sprintf( '<div%2$s>%1$s</div>', $svg, $this->get_attr_string( $attr ) ); ;
		}

		public function get_attr_string( $attr = array() ) {

			if ( empty( $attr ) || ! is_array( $attr ) ) {
				return;
			}

			$result = '';

			foreach ( $attr as $key => $value ) {
				$result .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
			}

			return $result;
		}

		public function get_carousel_arrow( $classes ) {
			$format = apply_filters( 'jvbpd_elements/carousel/arrows_format', '<i class="%s jvbpd-arrow"></i>', $classes );
			return sprintf( $format, implode( ' ', $classes ) );
		}

		public function get_post_types() {
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			$deprecated = apply_filters(
				'jvbpd-elements/post-types-list/deprecated',
				array( 'attachment', 'elementor_library' )
			);

			$result = array();

			if ( empty( $post_types ) ) {
				return $result;
			}

			foreach ( $post_types as $slug => $post_type ) {

				if ( in_array( $slug, $deprecated ) ) {
					continue;
				}

				$result[ $slug ] = $post_type->label;

			}

			return $result;

		}

		public function get_available_prev_arrows_list() {

			return apply_filters(
				'jvbpd_elements/carousel/available_arrows/prev',
				array(
					'fa fa-angle-left'          => __( 'Angle', 'jvfrmtd' ),
					'fa fa-chevron-left'        => __( 'Chevron', 'jvfrmtd' ),
					'fa fa-angle-double-left'   => __( 'Angle Double', 'jvfrmtd' ),
					'fa fa-arrow-left'          => __( 'Arrow', 'jvfrmtd' ),
					'fa fa-caret-left'          => __( 'Caret', 'jvfrmtd' ),
					'fa fa-long-arrow-left'     => __( 'Long Arrow', 'jvfrmtd' ),
					'fa fa-arrow-circle-left'   => __( 'Arrow Circle', 'jvfrmtd' ),
					'fa fa-chevron-circle-left' => __( 'Chevron Circle', 'jvfrmtd' ),
					'fa fa-caret-square-o-left' => __( 'Caret Square', 'jvfrmtd' ),
				)
			);

		}

		public function get_available_next_arrows_list() {

			return apply_filters(
				'jvbpd_elements/carousel/available_arrows/next',
				array(
					'fa fa-angle-right'          => __( 'Angle', 'jvfrmtd' ),
					'fa fa-chevron-right'        => __( 'Chevron', 'jvfrmtd' ),
					'fa fa-angle-double-right'   => __( 'Angle Double', 'jvfrmtd' ),
					'fa fa-arrow-right'          => __( 'Arrow', 'jvfrmtd' ),
					'fa fa-caret-right'          => __( 'Caret', 'jvfrmtd' ),
					'fa fa-long-arrow-right'     => __( 'Long Arrow', 'jvfrmtd' ),
					'fa fa-arrow-circle-right'   => __( 'Arrow Circle', 'jvfrmtd' ),
					'fa fa-chevron-circle-right' => __( 'Chevron Circle', 'jvfrmtd' ),
					'fa fa-caret-square-o-right' => __( 'Caret Square', 'jvfrmtd' ),
				)
			);

		}

		public function get_available_post_fields( $post_type='' ) {

			$defaultField = Array(
				'title' => __( 'Title', 'jvfrmtd' ),
				'content' => __( 'Content', 'jvfrmtd' ),
				'format' => __( 'Format', 'jvfrmtd' ),
				'author'	=> __(	'Author', 'jvfrmtd'	),
				'date'	=> __(	'Posted Date', 'jvfrmtd' ),
				'button' => __( 'Button', 'jvfrmtd' ),
			);

			switch( $post_type ) {

				case 'post' :
					$defaultField = wp_parse_args( array(
						'category' => __( 'Category', 'jvfrmtd' ),
						'tags' => __( 'Tags', 'jvfrmtd' ),
					), $defaultField );

					break;

				case 'lv_listing' :
					$defaultField = wp_parse_args( array(
						'share_icons' => __( 'Share Icons', 'jvfrmtd' ),
						'listing_category' => __( 'Category', 'jvfrmtd' ),
						'listing_location' => __( 'Location', 'jvfrmtd' ),
						'address'	=> __( 'Address', 'jvfrmtd'	),
						'bedrooms' => __( 'Bedrooms', 'jvfrmtd' ),
						'bathrooms' => __( 'Bathrooms', 'jvfrmtd' ),
						'garages' => __( 'Garages', 'jvfrmtd' ),
						'price' => __( 'Price', 'jvfrmtd' ),
						'area' => __( 'Area', 'jvfrmtd' ),
						'property_id' => __( 'Property ID', 'jvfrmtd' ),
					), $defaultField );

					break;
			}
			return apply_filters( 'jvbpd_elements/carousel/available_arrows/next', $defaultField );
		}

		public function getListingMetaFields( array $args=Array() ) {
			$output = Array();
			$args = shortcode_atts(
				Array(
					'meta' => true,
					'taxonomy' => true,
				), $args
			);

			if( $args[ 'meta' ] && class_exists( 'Lava_Directory_Manager_Func' ) ) {
				$fields = apply_filters( 'lava_lv_listing_more_meta', Array() );
				foreach( $fields as $field => $fieldMeta ) {
					$output[ $field ] = $fieldMeta[ 'label' ];
				}
			}
			if( $args[ 'taxonomy' ] ) {
				$fields = apply_filters( 'lava_lv_listing_taxonomies', Array() );
				if( !empty( $fields ) ) {
					foreach( $fields as $field => $fieldMeta ) {
						$output[ $field ] = isset( $fieldMeta[ 'args' ][ 'label' ] ) ? $fieldMeta[ 'args' ][ 'label' ] : NULL;
					}
				}
			}

			return $output;
		}

		public function getColumnsOption( $min=1, $max=3 ) {
			$result = Array();
			for( $i = intVal( $min ); $i <= intVal( $max ); $i++ ) {
				$result[ $i ] = sprintf( _nx( '%s Column', '%s Columns', $i, 'Columns count', 'jvfrmtd' ), $i );
			}
			return $result;
		}

		public function getActivateBlocks() { return $this->blocks; }

		public function get_taxonomies( $object_type='' ) {
			if( ! array_key_exists( $object_type, $this->taxonomies ) ) {
				if( post_type_exists( $object_type ) ) {
					$qry_object_type = is_array( $object_type) ? $object_type : Array( $object_type );
					$taxonomies = get_taxonomies( Array( 'object_type' => $qry_object_type ), 'objects' );
					foreach( $taxonomies as $taxonomy ) {
						$this->taxonomies[ $object_type ][ $taxonomy->name ] = $taxonomy->label;
					}
				}else{
					$this->taxonomies[ $object_type ] = Array();
				}
			}
			return $this->taxonomies[ $object_type ];
		}

		public function get_taxonomy_terms( $taxonomy='', $key='slug', $value='name' ) {

			$allow_keys = Array( 'term_id', 'name', 'slug' );

			$key = in_array( $key, $allow_keys ) ? $key : 'slug';
			$value = in_array( $value, $allow_keys ) ? $value : 'name';

			if( ! taxonomy_exists( $taxonomy ) ) {
				return Array();
			}

			if( array_key_exists( $taxonomy, $this->terms ) ) {
				return $this->terms[ $taxonomy ];
			}

			$terms = get_terms( Array( 'taxonomy' => $taxonomy, 'fields' => 'all', 'hide_empty' => false ) );
			$render = Array();
			foreach( $terms as $term ) {
				$render[ $term->{$key} ] = $term->{$value};
			}
			$this->terms[ $taxonomy ] = $render;
			return $this->terms[ $taxonomy ];
		}

		public static function get_instance( $shortcodes = array() ) {
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}
			return self::$instance;
		}

		public function getPostType( $args=Array() ) {
			$options = shortcode_atts(
				Array(
					'public' => true,
				)
			);

			$objects = get_post_types(
				Array(
					'public' => $options[ 'public' ],
				), 'objects'
			);
			return $objects;
		}

		public function getTaxonomyElement( $args=Array() ) {

			$args = shortcode_atts( Array(
				'taxonomy' => '',
				'type' => 'select',
				'default_val' => Array( 0 ),
				'remove_button' => true,
				'limit_items' => '1',
				'placeholder' => false,
				'show_count' => false,
				'condition' => 'or'
			), $args );

			extract( $args );

			$dropdownMode = "";

			if( ! is_array( $default_val ) ) {
				$default_val = array( $default_val );
			}

			if( $remove_button ) {
				$dropdownMode = "data-mode=\"multi\"";
			}

			if( ! $placeholder ) {
				$placeholder = esc_html__( 'Select a %1$s', 'jvfrmtd' );
			}

			if( ! taxonomy_exists( $taxonomy ) ) {
				return false;
			}

			$output = Array();
			$objTaxonomy = get_taxonomy( $taxonomy );

			switch( $type ) {
				case 'select' :
					$output[]	= "
					<select
						name=\"list_filter[{$taxonomy}]\"
						data-tax=\"{$taxonomy}\"
						{$dropdownMode}
						data-max-items=\"{$args['limit_items']}\"
						data-metakey=\"{$taxonomy}\"
						data-condition=\"{$condition}\"
						class=\"form-control javo-selectize-option\">";
					$output[] = sprintf( "<option value=''>%s</option>", sprintf( $placeholder, $objTaxonomy->label ) );
					$terms = $this->get_taxonomy_terms_array( Array(
						'taxonomy' => $taxonomy,
						'format'=> ( $show_count ? '%1$s(%2$s)' : '%1$s' ),
					) );
					foreach( $terms as $term_id => $name ) {
						$output[] = sprintf( '<option value="%1$s"%3$s>%2$s</option>', $term_id, $name, selected( in_array( $term_id, $default_val ), true, false ) );
					}
					$output[]	= "</select>";
					break;

				case 'checkbox' :
					$terms = $this->get_taxonomy_terms_array( Array(
						'taxonomy' => $taxonomy,
						'format'=> ( $show_count ? '%1$s<span class="count-bedge pull-right">%2$s</span>' : '%1$s' ),
					) );
					foreach( $terms as $term_id => $name ) {
						$output[]			= "<div class=\"form-check\">";
							$output[]		= '<label class="chk-wrap">';
								$output[]	= $name;
								$output[]	= "<input type=\"checkbox\" name=\"jvbpd_list_multiple_filter\" value=\"{$term_id}\"" . ' ';
								$output[]	= "class=\"form-check-input\" data-tax=\"{$taxonomy}\" data-condition=\"{$condition}\"";
								$output[]	= checked( in_array( $term_id, $default_val ), true, false );
								$output[]	= "><span class=\"form-check-label checkmark\">";
							$output[]		= '</span></label>';
						$output[]			= "</div>";
					}
					break;
			}
			return join( false, $output );
		}

		public function get_taxonomy_terms_array( Array $args=Array() ) {

			$output = Array();
			$args = wp_parse_args( $args,
				Array(
					'taxonomy' => NULL,
					'parent' => 0,
					'parentsNames' => '',
					'depth' => 0,
					'value' => 'term_id',
					'format' => '%1$s',
					'separator' => '-',
				)
			);

			if( ! taxonomy_exists( $args[ 'taxonomy' ] ) ) {
				return $output;
			}

			$term_args = Array(
				'taxonomy' => $args[ 'taxonomy' ],
				'parent' => $args[ 'parent' ],
				'hide_empty' => false,
			);

			$terms = get_terms( $term_args );
			$parent_term = get_term( $args[ 'parent' ], $args[ 'taxonomy' ] );
			if( $parent_term instanceof WP_Term ) {
				$args[ 'parentsNames' ] .= $parent_term->name . '/';
			}

			if( 0 >= sizeof( $terms ) ) {
				return $output;
			}

			$args[ 'depth' ]++;

			foreach( $terms as $term ) {
				$term_value = $args[ 'value' ] == 'name' ? $term->name : $term->term_id;
				$term_label = sprintf( $args[ 'format' ], ucfirst( $term->name ), intVal( $term->count ), $args[ 'parentsNames' ] );
				if( $args[ 'separator' ] ) {
					$output[ $term_value ] = sprintf( '%1$s %2$s', str_repeat( $args[ 'separator' ], $args[ 'depth' ] -1 ), $term_label );
				}else{
					$output[ $term_value ] = $term_label;
				}
				$args[ 'parent' ] = $term->term_id;
				$childs = $this->get_taxonomy_terms_array( $args );
				if( !empty( $childs ) ) {
					$output += $childs;
				}
			}
			return $output;
		}

		public function getShortcode( $shortcode_name='', $args=Array() ) {
			$output = $bindHook = false;
			$isFeaturedOnly = isset( $args[ 'featured_only' ] ) && $args[ 'featured_only' ];
			if( class_exists( $shortcode_name ) ) {
				if( $isFeaturedOnly ) {
					add_filter( Jvbpd_Core::get_instance()->prefix . '_shotcode_query', array( $this, 'featured_filter' ), 10, 2 );
					$bindHook = true;
				}
				$instance = new $shortcode_name();
				$output = $instance->output( $args, null );
				if( $bindHook ) {
					remove_filter( Jvbpd_Core::get_instance()->prefix . '_shotcode_query', array( $this, 'featured_filter' ), 10, 2 );
				}
			}
			return $output;
		}

		public function featured_filter( $query=Array(), $obj=null ) {
			$query[ 'meta_query' ][] = Array(
				'key' => '_featured_item',
				'value' => '1',
			);
			return $query;
		}

		public function getPageSetting( $key='', $default=false, $post_id=0 ) {
			// return $default;
			$post = get_post();
			if( 0 < intVal( $post_id ) ) {
				$post = get_post( $post_id );
			}
			if( class_exists( '\Elementor\Plugin' ) ) {
                $document = \Elementor\Plugin::$instance->documents->get( $post->ID );
                if ( $document ) {
                    $default = $document->get_settings( $key );
                }
			}
			return $default;
		}

		public function getMapType( $template_id=0 ) {
			if( get_queried_object() instanceof WP_Term ) {
				$template_id = apply_filters( 'jvbpd_core/elementor/custom_archive_listing', get_jvbpd_listing_archive_id(), get_queried_object() );
			}
			$type = $this->getPageSetting( 'lava_lv_listing_map_type', false, $template_id );
			return in_array( $type, Array( 'maps', 'listings' ) ) ? $type : 'maps';
		}

		public function is_sticky_header( $template_id=0 ) {
			return $this->getPageSetting( 'header_sticky', false, $template_id );
		}

		public function getACFGroups() {
			$output = Array( '' => esc_html( "Select a group", 'jvfrmtd' ) );
			if( function_exists( 'acf_get_field_groups' ) ){
				foreach( acf_get_field_groups() as $group ) {
					$output[ strVal( $group[ 'ID' ] ) ] = $group[ 'title' ];
				}
			}
			return $output;
		}

		public function getACFGroupFields( $groupID ) {
			$output = Array();
			if( $groupID && function_exists( 'acf_get_fields' ) ){
				foreach( acf_get_fields( $groupID ) as $field ) {
					$output[ $field[ 'key' ] ] = $field[ 'label' ];
				}
			}
			return $output;
		}

		public function getACFOptions( $obj, $condition=false, $prepend=false ) {
			$acfGroups = $this->getACFGroups();
			if( !empty( $acfGroups ) ) {

				$mainParam = Array(
					'type' => Elementor\Controls_Manager::SELECT2,
					'label' => esc_html__( 'ACF Group', 'jvfrmtd' ),
					'options' => $acfGroups,
				);

				if( is_array( $condition ) ) {
					$mainParam[ 'condition' ] = $condition;
				}

				if( is_array( $prepend ) ) {
					$mainParam[ 'name' ] = 'acf_group';
					$prepend[] = $mainParam;
				}else{
					$obj->add_control( 'acf_group', $mainParam );
				}

				foreach( $acfGroups as $groupID => $groupTitle ) {
					$groupParam = Array(
						'type' => Elementor\Controls_Manager::SELECT2,
						'label' => esc_html__( 'ACF Field', 'jvfrmtd' ),
						'condition' => Array( 'acf_group' => strVal( $groupID ) ),
						'options' => $this->getACFGroupFields( $groupID ),
					);
					if( is_array( $condition ) ) {
						$groupParam[ 'condition' ] = wp_parse_args( $condition, $groupParam[ 'condition' ] );
					}
					if( is_array( $prepend ) ) {
						$groupParam[ 'name' ] = 'acf_field_' . $groupID;
						$prepend[] = $groupParam;
					}else{
						$obj->add_control( 'acf_field_' . $groupID, $groupParam );
					}
				}
			}
			if( is_array( $prepend ) ) {
				return $prepend;
			}
		}



		public Function getACFTable($tableName){
			//echo $tableName;

			$table = get_field( $tableName );

			if ( $table ) {

				$output = '<table border="0" class="jv-single-atable">';
					if ( $table['header'] ) {
						$output .=  '<thead>';
							$output .=  '<tr>';
								foreach ( $table['header'] as $th ) {
									$output .=  '<th>';
										$output .=  $th['c'];
									$output .=  '</th>';
								}
							$output .=  '</tr>';
						$output .=  '</thead>';
					}

					$output .=  '<tbody>';
						foreach ( $table['body'] as $tr ) {
							$output .=  '<tr>';
								foreach ( $tr as $td ) {
									$output .=  '<td>';
										$output .=  $td['c'];
									$output .=  '</td>';
								}
							$output .=  '</tr>';
						}
					$output .=  '</tbody>';
				$output .=  '</table>';

				return $output;

			}


		}

		public function add_tax_term_control( $obj, $option_format, $args=Array() ) {

			$options = shortcode_atts(
				Array(
					'parent' => 'taxonomy',
					'taxonomies' => false,
					'type' => false,
					'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
					'condition' => Array(),
					'repeat_items' => false,
					'is_group' => false,
					'multiple' => false,
				), $args
			);

			$output = $options[ 'repeat_items' ];

			$taxonomies = $options[ 'taxonomies' ] ? $options[ 'taxonomies' ] : array();

			if( ! is_array( $taxonomies ) ) {
				return $output;
			}

			foreach( $taxonomies as $taxonomy ) {
				$option_args = Array(
					'label' => sprintf( $options[ 'label' ], $taxonomy ),
					'type' => $options[ 'type' ],
					'multiple' => $options[ 'multiple' ],
					'condition' => wp_parse_args( $options[ 'condition' ], Array( $options[ 'parent' ] => $taxonomy, ) ),
					/// 'options' => $this->get_taxonomy_terms( $taxonomy, 'term_id' ),
					'options' => $this->get_taxonomy_terms_array( Array(
						'taxonomy' => $taxonomy,
						'separator' => false,
						'format' => '%3$s%1$s',
					) ),
					'default' => '',
					'separator' => 'none',
				);
				if( $output ) {
					$fieldName = sprintf( $option_format, $taxonomy );
					if( $options[ 'is_group' ] ) {
						$output[ $fieldName ] = $option_args;
					}else{
						$option_args[ 'name' ] = $fieldName;
						$output[] = $option_args;
					}
				}else{
					$obj->add_control( sprintf( $option_format, $taxonomy ), $option_args );
				}
			}

			if( $output ) {
				return $output;
			}
		}

		public function getMoreTaxonoiesOptions( $prepend=Array() ) {
			$output = Array();
			if( function_exists( 'javo_moreTax' ) ) {
				$fields = javo_moreTax()->admin->getMoreTaxonomies();
				if( is_array( $fields ) ) {
					foreach( $fields as $field ) {
						if( ! empty( $field[ 'name' ] ) && ! empty( $field[ 'label' ] ) ) {
							$output[ $field[ 'name' ] ] = $field[ 'label' ];
						}
					}
				}
			}
			return wp_parse_args( $output, $prepend );
		}

		public function getStaticACFieldMeta() {
			return Array(
				'lvac_property_id' => esc_html( 'Property ID', 'jvfrmtd' ),
				'lvac_default_price' => esc_html( 'Pirce', 'jvfrmtd' ),
				'lvac_bedrooms' => esc_html( 'Bedrooms', 'jvfrmtd' ),
				'lvac_bathrooms' => esc_html( 'Bathrooms', 'jvfrmtd' ),
				'lvac_garages' => esc_html( 'Garages', 'jvfrmtd' ),
				'lvac_garages_size' => esc_html( 'Garages Size', 'jvfrmtd' ),
				'lvac_area' => esc_html( 'Area', 'jvfrmtd' ),
				'lvac_land_area' => esc_html( 'Land Area', 'jvfrmtd' ),

			);
		}

		public function add_button_control( $elementor, $param=Array() ) {
			/*foreach( Array(
				'tab_txt_icon' => Array(
					'is_tab' => true,
					'contents' => Array(
						'normal' => Array(
							'name' => 'txt_icon',
							'label' => esc_html__( "Normal", 'jvfrmtd' ),
							'fields' => Array( 'button_txt', 'label_normal_style' ),
						),
						'hover' => Array(
							'name' => 'txt_icon',
							'label' => esc_html__( "hover", 'jvfrmtd' ),
							'fields' => Array( 'button_txt_hover', 'label_hover_style' ),
						),
					),
				),
				Array(
					'is_tab' => false,
					'contents' => Array(
						Array(
							'name' => 'btn_size',
							'label' => esc_html__( "Size & Arrange", 'jvfrmtd' ),
							'fields' => Array( 'btn_size' ),
						),
					),
				),
				'tab_button_style' => Array(
					'is_tab' => true,
					'contents' => Array(
						'normal' => Array(
							'name' => 'button_style',
							'label' => esc_html__( "Normal", 'jvfrmtd' ),
							'fields' => Array( 'normal_style' ),
						),
						'hover' => Array(
							'name' => 'button_style',
							'label' => esc_html__( "hover", 'jvfrmtd' ),
							'fields' => Array( 'hover_style' ),
						),
					),
				),
				'tab_btn_bg_border' => Array(
					'is_tab' => true,
					'contents' => Array(
						'normal' => Array(
							'name' => 'btn_bg_border',
							'label' => esc_html__( "Normal", 'jvfrmtd' ),
							'fields' => Array( 'btn_bg_normal', 'btn_border_color_normal', 'btn_border_width_normal' ),
						),
						'hover' => Array(
							'name' => 'btn_bg_border',
							'label' => esc_html__( "hover", 'jvfrmtd' ),
							'fields' => Array( 'btn_bg_hover', 'btn_border_color_hover', 'btn_border_width_hover' ),
						),
					),
				),
				'tab_icon_normal_style' => Array(
					'is_tab' => true,
					'contents' => Array(
						'normal' => Array(
							'name' => 'icon_normal_style',
							'label' => esc_html__( "Normal", 'jvfrmtd' ),
							'fields' => Array( 'icon_normal_style' ),
						),
						'hover' => Array(
							'name' => 'icon_normal_style',
							'label' => esc_html__( "hover", 'jvfrmtd' ),
							'fields' => Array( 'icon_hover_style' ),
						),
					),
				),
				Array(
					'is_tab' => false,
					'contents' => Array(
						Array(
							'name' => 'settings',
							'label' => esc_html__( "Other Settings", 'jvfrmtd' ),
							'fields' => Array( 'btn_size' ),
						),
					),
				),

			) as $tabs_id => $tabe_content ) {
				if( $tabe_content[ 'is_tab' ] ) {
					$elementor->start_controls_tabs( $tabs_id );
				}
				foreach( $tabe_content[ 'contents' ] as $tab_content_id => $group ) {
					if( !is_array( $param ) ) {
						$param = Array();
					}
					$param[ 'params' ] = Array();
					$param[ 'separator' ] = 'none';
					if( $tabe_content[ 'is_tab' ] ) {
						$elementor->start_controls_tab( $tabs_id . '_' . $tab_content_id, Array( 'label' => $group[ 'label' ] ) );
					}
					$elementor->add_group_control( \jvbpd_group_button_style::get_type(), wp_parse_args( $param, $group ) );
					if( $tabe_content[ 'is_tab' ] ) {
						$elementor->end_controls_tab( $tabs_id . '_' . $tab_content_id, $group[ 'label' ] );
					}
				}
				if( $tabe_content[ 'is_tab' ] ) {
					$elementor->end_controls_tabs();
				}
			}
			*/

			foreach( Array(
				Array(
					'name' => 'txt_icon',
					'label' => esc_html__( "Text & Icon", 'jvfrmtd' ),
					'fields' => Array( 'button_txt', 'button_txt_hover', 'label_normal_style', 'label_hover_style' ),
				),
				Array(
					'name' => 'btn_size',
					'label' => esc_html__( "Size & Arrange", 'jvfrmtd' ),
					'fields' => Array( 'btn_size' ),
				),
				Array(
					'name' => 'button_style',
					'label' => esc_html__( "Button Style", 'jvfrmtd' ),
					'fields' => Array( 'normal_style', 'hover_style'),
				),
				Array(
					'name' => 'btn_bg_border',
					'label' => esc_html__( " Background & Border", 'jvfrmtd' ),
					'fields' => Array( 'btn_bg_normal', 'btn_border_color_normal', 'btn_border_width_normal', 'btn_bg_hover', 'btn_border_color_hover', 'btn_border_width_hover' ),
				),
				Array(
					'name' => 'icon_normal_style',
					'label' => esc_html__( "Icon Style", 'jvfrmtd' ),
					'fields' => Array( 'icon_normal_style', 'icon_hover_style'),
				),
				Array(
					'name' => 'settings',
					'label' => esc_html__( "Other Settings", 'jvfrmtd' ),
					'fields' => Array( 'settings' ),
				),

			) as $group ) {

				if( !is_array( $param ) ) {
					$param = Array();
				}
				$param[ 'params' ] = Array();
				$elementor->add_group_control( \jvbpd_group_button_style::get_type(), wp_parse_args( $param, $group ) );
			}
		}

		public function getModuleIDs() {
			$output = Array( '' => esc_html__( "Select a module", 'jvfrmtd' ) );
			if( class_exists( 'Jvbpd_Listing_Elementor' ) ) {
				$moduleIDs = (Array) Jvbpd_Listing_Elementor::get_template_id( 'custom_module' );
				foreach( array_filter( $moduleIDs ) as $moduleID ) {
					$output[ $moduleID ] = get_the_title( $moduleID );
				}
			}
			return $output;
		}

		public function getCanvasIDs() {
			$output = Array( '' => esc_html__( "Select a canvas", 'jvfrmtd' ) );
			if( class_exists( 'Jvbpd_Listing_Elementor' ) ) {
				$canvasIDs = (Array) Jvbpd_Listing_Elementor::get_template_id( 'canvas' );
				foreach( array_filter( $canvasIDs ) as $canvasID ) {
					$output[ $canvasID ] = get_the_title( $canvasID );
				}
			}
			return $output;
		}

		public function getBPModuleIDs() {
			$output = Array( '' => esc_html__( "Select a canvas", 'jvfrmtd' ) );
			if( class_exists( 'Jvbpd_Listing_Elementor' ) ) {
				$canvasIDs = (Array) Jvbpd_Listing_Elementor::get_template_id( 'custom_bp_module' );
				foreach( array_filter( $canvasIDs ) as $canvasID ) {
					$output[ $canvasID ] = get_the_title( $canvasID );
				}
			}
			return $output;
		}

		public function add_single_listing_relative_markers( array $params=Array(), \WP_Post $post ) {
			if( ! array_key_exists( $post->ID, $this->cacheRelativeListingMarkers ) ) {
				$query = new \WP_Query( Array(
					'post_type' => 'lv_listing',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'tax_query' => Array(
						Array(
							'taxonomy' => 'listing_category',
							'field' => 'term_id',
							'terms' => wp_get_object_terms( $post->ID, 'listing_category', Array( 'fields' => 'ids' ) ),
						),
					),
				) );

				$listings = Array();
				$term_icon = Array( 0 => '' );
				foreach( $query->get_posts() as $qPost ) {
					$term = 0;
					$terms = wp_get_object_terms( $qPost->ID, 'listing_category', Array( 'fields' => 'ids' ) );
					if( isset( $terms[0] ) && function_exists( 'lava_directory' ) ) {
						$term = $terms[0];
						$term_icon[ $term ] = lava_directory()->admin->getTermOption( $term, 'marker', 'listing_category' );
					}
					$listings[ $qPost->ID ] = Array(
						'term_id' => $term,
						'lat' => get_post_meta( $qPost->ID, 'lv_listing_lat', true ),
						'lng' => get_post_meta( $qPost->ID, 'lv_listing_lng', true ),
					);
				}
				$this->cacheRelativeListingMarkers[ $post->ID ] = $listings;
				$this->cacheRelativeListingMarkerUrls = $term_icon;
			}

			$params[ 'relative_items' ] = $this->cacheRelativeListingMarkers[ $post->ID ];
			$params[ 'relative_marker_urls' ] = $this->cacheRelativeListingMarkerUrls;
			return $params;
		}

		public function switch_preview_post() {
			if( ! Plugin::$instance->editor->is_edit_mode() ) {
				return;
			}
			$this->preview_post = $GLOBALS['post'];
			$template_type = get_post_meta( $this->preview_post->ID, 'jvbpd_template_type', true );
			switch( $template_type ) {
				case 'single_listing_page': $post_type = 'lv_listing'; break;
				case 'single_product_page': $post_type = 'product'; break;
				case 'single_post_page': default: $post_type = 'post';
			}

			$latest_post = get_posts( Array( 'post_type' => $post_type, 'posts_per_page' => 1 ) );
			$GLOBALS['post'] = get_post( $latest_post[0]->ID );
		}

		public function restore_preview_post() {
			if( ! Plugin::$instance->editor->is_edit_mode() ) {
				return;
			}
			if( !is_null( $this->preview_post ) ) {
				$GLOBALS['post'] = $this->preview_post;
				$this->preview_post = null;
			}
		}

		public function getNavLocations() {
			$output = Array( '' => esc_html__( "Select a menu", 'jvfrmtd' ) );
			$menus = get_terms( Array( 'taxonomy' => 'nav_menu', 'hide_empty' => false ) );
			foreach( $menus as $menu ) {
				$output[ $menu->slug ] = $menu->name;
			}
			return $output;
		}

		public function getHeaderSwitcherOptions() {
			$headerSwitchOptions = Array(
				'' => esc_html__( 'Select a button', 'jvfrmtd' ),
				'featured' => esc_html__( 'Featured Image', 'jvfrmtd' ),
				'grid' => esc_html__( 'Grid Slider', 'jvfrmtd' ),
				'category' => esc_html__( 'Category Image', 'jvfrmtd' ),
				'map' => esc_html__( 'Google Map', 'jvfrmtd' ),
				'streetview' => esc_html__( 'Street View', 'jvfrmtd' ),
				'3dview' => esc_html__( '3D(360) Image', 'jvfrmtd' ),
				'video' => esc_html__( 'Video', 'jvfrmtd' ),
			);

			if(function_exists('lava_directory_direction')){
				$headerSwitchOptions['get-direction'] = esc_html__( 'Get Direction', 'jvfrmtd' );
			}

			return $headerSwitchOptions;
		}
	}
}

if( ! function_exists( 'jvbpd_elements_tools' ) ) {
	function jvbpd_elements_tools() {
		return jvbpd_elements_tools::get_instance();
	}
}

if( ! function_exists( 'jvbpd_getAllBlocksName' ) ) {
	add_filter( 'jvbpd_core/blocks/args', 'jvbpd_getAllBlocksName' );
	function jvbpd_getAllBlocksName( array $args=Array() ) {
		if( empty( jvbpd_elements_tools()->blocks ) ) {
			foreach( $args as $block => $meta ) {
				if( false !== strpos( strtolower( $block ), 'block' ) ) {
					jvbpd_elements_tools()->blocks[ $block ] = $meta[ 'name' ];
				}
			}
		}
		return $args;
	}
}