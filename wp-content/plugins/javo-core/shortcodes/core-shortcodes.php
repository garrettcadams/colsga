<?php
/**
 *	Javo Shortcodes
 *
 *	@Since	1.0.0
 */


/** Core Classes */
require_once 'functions-shortcode.php';
require_once 'class-shortcode.php';
require_once dirname( __FILE__ ) . '/../modules/class-module.php';
require_once dirname( __FILE__ ) . '/../modules/class-base-meta.php';

/** Shortcodes */
require_once 'shortcode-category_box.php';
require_once 'shortcode-category_box2.php';
require_once 'shortcode-mailchimp.php';
require_once 'shortcode-slider_authors.php';

if( !function_exists( 'jvbpd_register_shortcodes' ) ) :

	function jvbpd_register_shortcodes( $prefix ){

	/**
	 *	Variables
	 */
		$dirShortcode		= trailingslashit( dirname( __FILE__ ) );
		$dirModule			= Jvbpd_Core::$instance->module_path . '/';

		$arrGroupStrings	= Array(
			'header'		=> __( "Header", 'jvfrmtd' ),
			'content'		=> __( "Content", 'jvfrmtd' ),
			'advanced'		=> __( "Advanced", 'jvfrmtd' ),
			'effect'		=> __( "Effect", 'jvfrmtd' ),
			'style'			=> __( "Style", 'jvfrmtd' ),
			'filter'		=> __( "Filter", 'jvfrmtd' ),
		);

		list(
			$groupHeader,
			$groupContent,
			$groupAdvanced,
			$groupEffect,
			$groupStyle,
			$groupFilter
		) = Array_values( $arrGroupStrings );

	/**
	 *	Shortcodes Part
	 */
		$arrShortcodeDEFAULTS		= Array(

			/* Shortcode 0 */
			'block'			=> Array(
				'name'			=> __( "Block", 'jvfrmtd'),
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-2',
			),

			/* Shortcode 1 */
			'block1'				=> Array(
				'name'				=> __( "Block 1", 'jvfrmtd'),
				'icon'				=> 'jv-vc-shortcode-icon shortcode-block-1',
				'column'			=> 3,
				'hide_avatar'		=> true,
				'params'			=> Array(
					Array(
						'type'		=> 'dropdown',
						'group'		=> $groupStyle,
						'heading'		=> __( "Display Thumbnail", 'jvfrmtd'),
						'holder'		=> 'div',
						'param_name'	=> '_display_thumbnail',
						'value'		=> Array(
							__( "Enable", 'jvfrmtd' ) => '',
							__( "Disable", 'jvfrmtd' ) => '1'
						)
					),
					Array(
						'type'					=> 'dropdown'
						, 'group'				=> $groupStyle
						, 'heading'				=> __( "Display Post Border", 'jvfrmtd')
						, 'holder'				=> 'div'
						, 'param_name'			=> 'display_post_border'
						, 'value'				=> Array(
							__( "Enable", 'jvfrmtd' ) => '',
							__( "Disable", 'jvfrmtd' ) => '1'
						)
					)
				),
				'hover_style'	=> true,
			),

			/* Shortcode 2 */
			'block2'			=> Array(
				'name'			=> __( "Block 2", 'jvfrmtd'),
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-2',
				'column'		=> 3,
				'more'			=> true,
			),


			/* Shortcode 3 */
			'block3'			=> Array(
				'name'			=> __( "Block 3", 'jvfrmtd'),
				'filter'		=> true,
				'column'		=> 3,
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-3',
				'hide_avatar'	=> true,
			),

			/* Shortcode 4 */
			'block4'			=> Array(
				'name'			=> __( "Block 4", 'jvfrmtd'),
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-4',
				'column'		=> 4,
				'hover_style'	=> true,
			),


			/* Shortcode 5 */
			/*'block5'			=> Array(
				'name'			=> __( "Block 5", 'jvfrmtd'),
				'more'			=> true,
				'column'		=> 3,
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-5',
			),
*/

			/* Shortcode 6 */
			/*'block6'			=> Array(
				'name'			=> __( "Block 6", 'jvfrmtd'),
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-6',
				'column'		=> 3,
				'more'			=> true,
				'hover_style'	=> true,
			),*/

			/* Shortcode 7 */
			'block7'			=> Array(
				'name'			=> __( "Block 7", 'jvfrmtd'),
				'more'			=> true,
				'column'		=> 2,
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-7',
				'hover_style'	=> true,
			),

			/* Shortcode 8 */
			'block8'			=> Array(
				'name'				=> __( "Block 8", 'jvfrmtd')
				, 'more'				=> true
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-block-8'
				, 'column'			=> 2
			),

			/* Shortcode 9 */
			/*'block9'			=> Array(
				'name'				=> __( "Block 9", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-block-9'
				, 'more'				=> true
			),
*/

			/* Shortcode 10 */
			'block10'		=> Array(
				'name'			=> __( "Block 10", 'jvfrmtd'),
				'more'			=> true,
				'column'		=> 3,
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-10',
				'hover_style'	=> true,
			),

			/* Shortcode 11 */
			'block11'		=> Array(
				'name'			=> __( "Block 11", 'jvfrmtd'),
				'more'			=> true,
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-11',
				'column'		=> 3,
				'hover_style'	=> true,
			),

			/* Shortcode 12 */
			'block12'			=> Array(
				'name'				=> __( "Block 12", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-block-12'
				, 'column'			=> 3
				, 'more'				=> true
			),

			/* Shortcode 13 */
			/*'block13'			=> Array(
				'name'				=> __( "Block 13", 'jvfrmtd')
				, 'filter'				=> true
				, 'column'			=> 3
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-block-13'
			),
*/

			/* Shortcode 14  */
			/*'block14'			=> Array(
				'name'				=> __( "Block 14", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-block-14'
				, 'more'				=> true
			),
*/

			/* Shortcode 15  */
			/*'block15'		=> Array(
				'name'			=> __( "Block 15", 'jvfrmtd')
				, 'icon'		=> 'jv-vc-shortcode-icon shortcode-block-15'
				, 'column'		=> 3
				, 'more'		=> true
				, 'hide_thumb'	=> true
			),
*/

			/* Shortcode 16  */
			'block16'		=> Array(
				'name'			=> __( "Block 16", 'jvfrmtd'),
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-15',
				'column'		=> 3,
				'more'			=> true,
				'hover_style'	=> true,
			),

			/* Shortcode 21 */
			'block21'			=> Array(
				'name'			=> __( "Block 21", 'jvfrmtd'),
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-21',
				'column'		=> 3,
				'more'			=> true,
			),

			/* Shortcode 22 */
			'block22'			=> Array(
				'name'			=> __( "Block 22", 'jvfrmtd'),
				'icon'			=> 'jv-vc-shortcode-icon shortcode-block-22',
				'column'		=> 3,
				'more'			=> true,
			),


			/* Shortcode Grid 1  */
			'big_grid1'		=> Array(
				'name'			=> __( "Grid 1", 'jvfrmtd'),
				'more'			=> true,
				'fixed_count'	=> true,
				'icon'			=> 'jv-vc-shortcode-icon shortcode-grid1',
				'hover_style'	=> true,
			),

			/* Shortcode Grid 2  */
			/*'big_grid2'		=> Array(
				'name'				=> __( "Grid 2", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-grid2'
				, 'fixed_count'	=> true
				, 'more'				=> true
			),
*/

			/* Shortcode Grid 3  */
			'big_grid3'			=> Array(
				'name'				=> __( "Grid 3", 'jvfrmtd'),
				'icon'				=> 'jv-vc-shortcode-icon shortcode-grid3',
				'more'				=> true,
				'hover_style'		=> true,
			),

			/* Shortcode Slide1 */
			/*'slider1'			=> Array(
				'name'				=> __( "Slider 1", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-slider1'
				, 'fixed_count'	=> true
				, 'hide_avatar'	=> true
			),
*/

			/* Shortcode Slide1 */
			'slider2'			=> Array(
				'name'				=> __( "Slider 2", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-slider2'
				, 'fixed_count'	=> true
				, 'hide_avatar'	=> true
			),

			/* Shortcode Slide3 */
			'slider3'		=> Array(
				'name'			=> __( "Slider 3", 'jvfrmtd'),
				'icon'			=> 'jv-vc-shortcode-icon shortcode-slider3',
				'fixed_count'	=> true,
				'column'		=> 3,
				'params'		=> Array(
					Array(
						'type'			=> 'checkbox',
						'group'			=> $groupStyle,
						'heading'		=> __( "Wide Slider?", 'jvfrmtd'),
						'holder'		=> 'div',
						'param_name'	=> 'slide_wide',
						'value'			=> Array( __( "Enable", 'jvfrmtd' ) => '1' )
					)
				),
				'hover_style'	=> true,
			),

			/* Shortcode Slide4 */
			/*'slider4'			=> Array(
				'name'				=> __( "Slider 4", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-slider4'
				, 'fixed_count'	=> true
			),
*/

			/* Shortcode Slide5 */
			/*'slider5'			=> Array(
				'name'				=> __( "Slider 5", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-slider4'
				, 'fixed_count'	=> true
			),
*/

			/* Shortcode Timeline1 */
			/*'timeline1'		=> Array(
				'name'				=> __( "Timeline 1", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-timeline1'
			),
*/

			/* Shortcode Login1 */
			'login1'				=> Array(
				'name'				=> __( "Login 1", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-login1'
				//, 'no_param'			=> true
			),

			'login2'				=> Array(
				'name'				=> __( "Login 2", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-login1'
				//, 'no_param'			=> true
			),

			/* Vertical Shortcode 1 */
			/*'vblock1'			=> Array(
				'name'				=> __( "Vertical Block 1", 'jvfrmtd')
				, 'icon'			=> 'jv-vc-shortcode-icon shortcode-block-1'
				, 'column'			=> 3
			),
*/

			/* Vertical Slider Shortcode 1 */
			/*'vslider1'			=> Array(
				'name'				=> __( "Vertical Slider 1", 'jvfrmtd')
				, 'icon'			=> 'jv-vc-shortcode-icon shortcode-block-1'
			),
*/


		);

	if( defined( 'Jvbpd_Core::DEBUG' ) && Jvbpd_Core::DEBUG ) {
		/* Shortcode 20  */
		$arrShortcodeDEFAULTS[ 'block20' ]	=
			Array(
				'name'				=> __( "Block 20 ( ALL MODULE )", 'jvfrmtd')
				, 'icon'				=> 'jv-vc-shortcode-icon shortcode-block-20'
				, 'fixed_count'	=> true
			);
	}


	/**
	 *	Modules Part
	 */
		$arrModuleDEFAULTS				= Array(
			'module1'					=> Array( 'file' => $dirModule . 'module1.php' )
			, 'module2'					=> Array( 'file' => $dirModule . 'module2.php' )
			, 'module3'					=> Array( 'file' => $dirModule . 'module3.php' )
			, 'module4'					=> Array( 'file' => $dirModule . 'module4.php' )
			, 'module5'					=> Array( 'file' => $dirModule . 'module5.php' )
			, 'module6'					=> Array( 'file' => $dirModule . 'module6.php' )
			, 'module8'					=> Array( 'file' => $dirModule . 'module8.php' )
			, 'module9'					=> Array( 'file' => $dirModule . 'module9.php' )
			, 'module12'				=> Array( 'file' => $dirModule . 'module12.php' )
			, 'module13'				=> Array( 'file' => $dirModule . 'module13.php' )
			, 'module14'				=> Array( 'file' => $dirModule . 'module14.php' )
			, 'module15'				=> Array( 'file' => $dirModule . 'module15.php' )
			, 'module16'				=> Array( 'file' => $dirModule . 'module16.php' )

			, 'module21'				=> Array( 'file' => $dirModule . 'module21.php' )
			, 'module22'				=> Array( 'file' => $dirModule . 'module22.php' )

			, 'moduleBpGrid'			=> Array( 'file' => $dirModule . 'module-Bp-Grid.php' )
			, 'moduleBpGridNoBG'		=> Array( 'file' => $dirModule . 'module-Bp-Grid-No-BG.php' )
			, 'moduleSmallGrid'			=> Array( 'file' => $dirModule . 'module-SmallGrid.php' )
			, 'moduleBigGrid'			=> Array( 'file' => $dirModule . 'module-BigGrid.php' )
			, 'moduleHorizontalGrid'	=> Array( 'file' => $dirModule . 'module-HorizontalGrid.php' )
			, 'moduleWC1'				=> Array( 'file' => $dirModule . 'module-wc1.php' )
			, 'module_r_2'					=> Array( 'file' => $dirModule . 'module_r_2.php' )
		);


		$arrShortcodes = apply_filters( 'jvbpd_core/blocks/args', $arrShortcodeDEFAULTS );
		$arrModules = apply_filters( 'jvbpd_core/modules/args', $arrModuleDEFAULTS );
		$arrCommonParam	= Array(

			/**
			 *	@group : header
			 */
			 Array(
				'type'						=> 'dropdown'
				, 'group'					=> $groupHeader
				, 'heading'				=> __( "Header Type", 'jvfrmtd')
				, 'holder'				=> 'div'
				, 'class'					=> ''
				, 'param_name'		=> 'filter_style'
				, 'value'					=> Array(
					__( "None", 'jvfrmtd' )					=> ''
					, __( "Style1", 'jvfrmtd' )				=> 'general'
					, __( "Style2", 'jvfrmtd' )				=> 'linear'
					, __( "Box Style 1", 'jvfrmtd' )				=> 'box'
				)
			)

			/** Shortcode Title */
			, Array(
				'type'						=> 'textfield'
				, 'group'					=> $groupHeader
				, 'heading'				=> __( "Title", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'filter_style'
					, 'not_empty'		=> true
				)
				, 'param_name'		=> 'title'
				, 'value'					=> ''
			)

			/** Shortcode Subtitle */
			, Array(
				'type'						=> 'textfield'
				, 'group'					=> $groupHeader
				, 'heading'				=> __( "Sub Title (Only Header Type Style3)", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'filter_style'
					, 'value'				=> 'paragraph'
				)
				, 'param_name'		=> 'subtitle'
				, 'value'					=> ''
			)


			/**
			 *	@group : Style
			 */

			 /** Custom Post Taxonomies */
			, Array(
				'type'						=> 'dropdown'
				, 'group'					=> $groupFilter
				, 'heading'					=> __( "Show / Hide Filter", 'jvfrmtd')
				, 'holder'					=> 'div'
				, 'param_name'			=> 'hide_filter'
				, 'value'						=> Array(
					__( "Show", 'jvfrmtd' ) => '',
					__( "Hide", 'jvfrmtd' ) => '1'
				)
			),

			Array(
				'type'			=> 'css_editor',
				'group'			=> $groupStyle,
				'heading'		=> __( 'Css', 'my-text-domain' ),
				'param_name'	=> 'css',
			),

			/** Primary Color */
			Array(
				'type'						=> 'colorpicker'
				, 'group'					=> $groupStyle
				, 'heading'				=> __( "Primary Color", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'primary_color'
				, 'value'					=> ''
			)

			/** Primary Font Color */
			, Array(
				'type'						=> 'colorpicker'
				, 'group'					=> $groupStyle
				, 'heading'				=> __( "Primary Font Color", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'primary_font_color'
				, 'value'					=> ''
			)

			/** Primary Border Color */
			, Array(
				'type'						=> 'colorpicker'
				, 'group'					=> $groupStyle
				, 'heading'				=> __( "Primary Border Color", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'primary_border_color'
				, 'value'					=> ''
			)

			/** Post TItle Font Color */
			, Array(
				'type'						=> 'colorpicker'
				, 'group'					=> $groupStyle
				, 'heading'				=> __( "Post Title Color", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'post_title_font_color'
				, 'value'					=> ''
			)

			/** Post Title Size  */
			, Array(
				'type'					=> 'textfield'
				, 'group'				=> $groupStyle
				, 'heading'				=> __( "Post Title Font Size", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'description'		=> __( "Pixcel", 'jvfrmtd' )
				, 'param_name'		=> 'post_title_font_size'
				, 'value'					=> ''
			)

			/** Post TItle Capitalize */
			, Array(
				'type'					=> 'dropdown'
				, 'group'				=> $groupStyle
				, 'heading'				=> __( "Post Title Transform", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'post_title_transform'
				, 'value'					=> Array(
					__( "Inheritance", 'jvfrmtd' )	=> 'inherit',
					__( "Uppercase", 'jvfrmtd' )	=> 'uppercase',
					__( "Lowercase", 'jvfrmtd' )	=> 'lowercase',
				)
			)

			/** Post Meta Font Color */
			, Array(
				'type'						=> 'colorpicker'
				, 'group'					=> $groupStyle
				, 'heading'				=> __( "Post Meta Font Color", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'post_meta_font_color'
				, 'value'					=> ''
			)

			/** Post Describe Font Color */
			, Array(
				'type'						=> 'colorpicker'
				, 'group'					=> $groupStyle
				, 'heading'				=> __( "Post Description Font Color", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'post_describe_font_color'
				, 'value'					=> ''
			)

			/** Category Tag Visibility */
			, Array(
				'type'			=> 'dropdown',
				'group'			=> $groupStyle,
				'heading'		=> __( "Display Category Tags", 'jvfrmtd' ),
				'holder'		=> 'div',
				'param_name'	=> 'display_category_tag',
				'value'			=> Array(
					__( "Visible", 'jvfrmtd' )	=> '',
					__( "HIdden", 'jvfrmtd' )	=> 'hide'
				)
			)

			/** Category Tag Color */
			, Array(
				'type'			=> 'colorpicker',
				'group'			=> $groupStyle,
				'heading'		=> __( "Category Tags Background Color", 'jvfrmtd' ),
				'holder'		=> 'div',
				'dependency'	=> Array(
					'element'	=> 'display_category_tag',
					'value'		=> Array( '' )
				),
				'param_name'	=> 'category_tag_color',
				'value'			=> '#666'
			)

			/** Category Tag Hover Color */
			, Array(
				'type'			=> 'colorpicker',
				'group'			=> $groupStyle,
				'heading'		=> __( "Category Tags Background Hover Color", 'jvfrmtd' ),
				'holder'		=> 'div',
				'dependency'	=> Array(
					'element'	=> 'display_category_tag',
					'value'		=> Array( '' )
				),
				'param_name'	=> 'category_tag_hover_color',
				'value'			=> ''
			)

			/** Pre-loader Style */
			, Array(
				'type'			=> 'dropdown',
				'group'			=> $groupStyle,
				'heading'		=> __( "Pre-loader Style Type", 'jvfrmtd'),
				'holder'		=> 'div',
				'class'			=> '',
				'param_name'	=> 'loading_style',
				'value'			=>
					Array(
						__( "None", 'jvfrmtd' )		=> '',
						__( "Rectangle", 'jvfrmtd' )	=> 'rect',
						__( "circle", 'jvfrmtd' )		=> 'circle'
					)
			)

			/**
			 *	@group : Filter
			 */

			/** Post Type */
			, Array(
				'type'					=> 'dropdown'
				, 'group'				=> $groupFilter
				, 'heading'				=> __( "Post Type", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'post_type'
				, 'value'					=> apply_filters( 'jvbpd_shortcodes_post_types', Array( 'post' ) )
			)

			/** Order Type */
			, Array(
				'type'					=> 'dropdown'
				, 'group'				=> $groupFilter
				, 'heading'				=> __( "Order By", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'order_by'
				, 'value'					=> apply_filters( 'jvbpd_shortcodes_order_by',
					Array(
						__( "None", 'jvfrmtd' )			=> '',
						__( "Post Title", 'jvfrmtd' )	=> 'title',
						__( "Date", 'jvfrmtd' )			=> 'date',
						__( "Random", 'jvfrmtd' )	=> 'rand',
					)
				)
			)

			/** Order Type */
			, Array(
				'type'					=> 'dropdown'
				, 'group'				=> $groupFilter
				, 'heading'				=> __( "Order Type", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'order_by'
					, 'not_empty'		=> true
				)
				, 'param_name'		=> 'order_'
				, 'value'					=> Array(
					__( "Descending ( default )", 'jvfrmtd' )	=> '',
					__( "Ascending", 'jvfrmtd' )		=> 'ASC'
				)
			)

			/** Filter Taxonomy */
			, Array(
				'type'				=> 'dropdown'
				, 'group'			=> $groupFilter
				, 'heading'			=> __( "Category Filter", 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'dependency'		=> Array(
					'element'		=> 'post_type', 'value' => 'post'
				)
				, 'param_name'		=> 'filter_by'
				, 'value'			=> jvbpd_shortcode_taxonomies( 'post', Array( __( "None", 'jvfrmtd' )	=> '' ) )
			)

			/** Custom Post Taxonomies */
			, Array(
				'type'					=> 'checkbox'
				, 'group'				=> $groupFilter
				, 'heading'				=> __( "Use Custom Terms", 'jvfrmtd')
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'filter_by'
					, 'not_empty'		=> true
				)
				,  'description'		=> __( "To display specific terms only, please enable. if you use custom terms.", 'jvfrmtd' )
				, 'param_name'		=> 'custom_filter_by_post'
				, 'value'					=> Array( __( "Enable", 'jvfrmtd' ) => '1' )
			)

			/** Header / Filter Custom Terms */
			, Array(
				'type'						=> 'textfield'
				, 'group'					=> $groupFilter
				, 'heading'				=> __( "Custom Terms IDs", 'jvfrmtd')
				, 'holder'				=> 'div'
				, 'dependency'		=> Array(
					'element'			=> 'custom_filter_by_post'
					, 'value'				=> '1'
				)
				, 'param_name'		=> 'custom_filter'
				, 'description'			=> __( "Enter category IDs separated by commas (ex: 13,23,18). To exclude categories please add '-' (ex: -9, -10)", 'jvfrmtd' )
				, 'value'					=> ''
			)

			/**
			 *	@group : Advanced
			 */
			 , Array(
				'type'						=> 'dropdown'
				, 'group'					=> $groupAdvanced
				, 'heading'				=> __( "Display description", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'param_name'		=> 'module_contents_hide'
				, 'value'					=> Array(
					__( "Enable", 'jvfrmtd' )		=> '',
					__( "Disable", 'jvfrmtd' )		=> 'hide'
				)
			)
			, Array(
				'type'						=> 'textfield'
				, 'group'					=> $groupAdvanced
				, 'heading'				=> __( "Limit length of description", 'jvfrmtd' )
				, 'holder'				=> 'div'
				, 'description'			=> __( "( 0 = Unlimited )", 'jvfrmtd' )
				, 'param_name'		=> 'module_contents_length'
				, 'value'					=> '0'
			)
		);

		$arrCommonParam		= apply_filters( $prefix . 'commonParam', $arrCommonParam, $arrGroupStrings );
		$arrPaginationParam		= Array(

			/**
			 *	@group : Style
			 */

			 /** Pagination Style */
			Array(
				'type'						=> 'dropdown'
				, 'group'					=> $groupStyle
				, 'heading'				=> __( "Load More Type", 'jvfrmtd')
				, 'holder'				=> 'div'
				, 'class'					=> ''
				, 'param_name'		=> 'pagination'
				, 'value'					=> Array(
					__( "None", 'jvfrmtd' )				=> ''
					, __( "Pagination", 'jvfrmtd' )	=> 'number'
					, __( "Load More", 'jvfrmtd' )	=> 'loadmore'
					, __( "Previous & Next Button", 'jvfrmtd' )	=> 'prevNext'
				)
			)
		);

		$arrAmountParam							=  Array(

			/**
			 *	@group : Advanced
			 */

			/** Posts Per Page */
			Array(
				'type'			=> 'textfield'
				, 'group'		=> $groupAdvanced
				, 'heading'		=> __( "Number of posts to load", 'jvfrmtd')
				, 'holder'		=> 'div'
				, 'class'		=> ''
				, 'param_name'	=> 'count'
				, 'value'		=> 5
			)
		);

		/** module hover style **/
		$arrHoverStyleParam = Array(
			/**
			 *	@group : Effect
			 */
			Array(
				'type'			=> 'dropdown'
				, 'group'		=> $groupEffect
				, 'heading'		=> __( "Module Hover Effect", 'jvfrmtd')
				, 'holder'		=> 'div'
				, 'class'		=> ''
				, 'param_name'	=> 'hover_style'
				, 'value'		=> Array(
					__( "None", 'jvfrmtd' )				=> ''
					, __( "Effect 1 (Zoom In)", 'jvfrmtd' )	=> 'zoom-in'
					, __( "Effect 2 (Dark Fade In)", 'jvfrmtd' )	=> 'dark-fade-in'
				)
			)
		);

		// Parse Shortcodes
		if( ! empty( $arrShortcodes ) ) : foreach( $arrShortcodes as $scdName => $scdAttr ) :

			// File Exists
			if( isset( $scdAttr[ 'file' ] ) )
				$fnShortcode		= $scdAttr[ 'file' ];
			else
				$fnShortcode		= $dirShortcode . 'shortcode-' .$scdName . '.php';

			// Other Shortcode
			if( ! file_exists( $fnShortcode ) )
				continue;
			else
				require_once $fnShortcode;

			// Shortcode icon
			$scdAttr[ 'icon' ]	= isset( $scdAttr[ 'icon' ] ) ? $scdAttr[ 'icon' ] : 'javo-vc-icon';

			// Default Setting
			$scdAttr				= wp_parse_args(
				Array(
					'base'			=> 'jvbpd_' . $scdName
					, 'category'	=> __( "Javo", 'jvfrmtd' )
				)
				, $scdAttr
			);

			// Merge Parametters
			if( empty( $scdAttr[ 'params' ] ) ){
				$scdAttr[ 'params']	= $arrCommonParam;
			}else{
				$scdAttr[ 'params']	= wp_parse_args( $scdAttr[ 'params'], $arrCommonParam );
			}

			if( isset( $scdAttr[ 'more' ] ) )
				$scdAttr[ 'params']	= wp_parse_args( $arrPaginationParam, $scdAttr[ 'params'] );

			if( isset( $scdAttr[ 'column' ] ) && 0 < intVal( $scdAttr[ 'column' ] ) )
			{
				$arrColumn					= Array();
				for( $intCount = 1; $intCount <= $scdAttr[ 'column' ]; $intCount ++ )
					$arrColumn[ $intCount . ' ' . _n( "Column", "Columns", $intCount, 'jvfrmtd' ) ] = $intCount;

				$scdAttr[ 'params']		= wp_parse_args(
					Array(
						Array(
							'type'				=> 'dropdown'
							, 'group'			=> $groupContent
							, 'heading'			=> __( "Columns", 'jvfrmtd')
							, 'holder'			=> 'div'
							, 'class'				=> ''
							, 'param_name'	=> 'columns'
							, 'value'				=> $arrColumn
						)
					)
					, $scdAttr[ 'params']
				);
			}

			if( isset( $scdAttr[ 'hide_thumb' ] ) )
			{
				$scdAttr[ 'params']		= wp_parse_args(
					Array(
						Array(
							'type'				=> 'dropdown'
							, 'group'			=> $groupContent
							, 'heading'			=> __( "Display Posts Thumbnails", 'jvfrmtd')
							, 'holder'			=> 'div'
							, 'class'				=> ''
							, 'param_name'	=> 'hide_thumbnail'
							, 'value'				=> Array(
								__( "Visible", 'jvfrmtd' )	=> '',
								__( "Hidden", 'jvfrmtd' )	=> 'hide'
							)
						)
					)
					, $scdAttr[ 'params']
				);
			}

			if( isset( $scdAttr[ 'hide_avatar' ] ) )
			{
				$scdAttr[ 'params']		= wp_parse_args(
					Array(
						Array(
							'type'				=> 'dropdown'
							, 'group'			=> $groupContent
							, 'heading'			=> __( "Display User Avatar", 'jvfrmtd')
							, 'holder'			=> 'div'
							, 'class'				=> ''
							, 'param_name'	=> 'hide_avatar'
							, 'value'				=> Array(
								__( "Visible", 'jvfrmtd' )	=> false,
								__( "Hidden", 'jvfrmtd' )	=> true
							)
						)
					)
					, $scdAttr[ 'params']
				);
			}

			if( !isset( $scdAttr[ 'fixed_count' ] ) )
				$scdAttr[ 'params' ]	= wp_parse_args( $arrAmountParam, $scdAttr[ 'params'] );

			/* module hover style */
			if(isset($scdAttr['hover_style']))
				$scdAttr[ 'params' ] = wp_parse_args($arrHoverStyleParam, $scdAttr['params']);

			if( isset( $scdAttr[ 'no_param' ] ) && $scdAttr[ 'no_param' ] )
				$scdAttr[ 'params' ] = Array();

			add_shortcode( 'jvbpd_' . $scdName, 'jvbpd_parse_shortcode' );

			// Register shortcode in visual composer
			if( function_exists( 'vc_map' ) )
				vc_map( $scdAttr );

		endforeach; endif;

		$arrOtherShortcode = apply_filters( 'jvbpd_other_shortcode_array', Array() );
		if( !empty( $arrOtherShortcode ) ) foreach( $arrOtherShortcode as $shortcode_name => $shortcode_callback )
			add_shortcode( 'jvbpd_' . $shortcode_name, $shortcode_callback );


		if( function_exists( 'vc_map' ) ) :

			// Category Block
			vc_map( array(
				'base' => 'jvbpd_category_box',
				'name' => __( "Category Block", 'jvfrmtd' ),
				'icon' => 'jv-vc-shortcode-icon shortcode-featured',
				'category' => __( "Javo", 'jvfrmtd' ),
				'params' => Array(
					Array(
						'type' =>'dropdown',
						'heading' => __('Row Column', 'jvfrmtd'),
						'holder' => 'div',
						'class' => '',
						'param_name'	=> 'column',
							'value' => Array(
								'1/3' => '1-3',
								'2/3' => '2-3',
								'full' => 'full',
							)
						),
						Array(
							'type' =>'textfield',
							'heading' => __('Item Name', 'jvfrmtd'),
							'holder' => 'div',
							'class' => '',
							'param_name' => 'block_title',
							'value' => ''
						),
						Array(
							'type' =>'textfield',
							'heading' => __('Item Description', 'jvfrmtd'),
							'holder' => 'div',
							'class' => '',
							'param_name' => 'block_description',
							'value' => ''
						),
						Array(
							'type' =>'colorpicker',
							'heading' => __('Name Color', 'jvfrmtd'),
							'holder' => 'div',
							'group' => $groupStyle,
							'param_name' => 'text_color',
							'value' => '#fff'
						),
						Array(
							'type' =>'colorpicker',
							'heading' => __('Name Sub Color', 'jvfrmtd'),
							'holder' => 'div',
							'group' => $groupStyle,
							'param_name' => 'text_sub_color',
							'value' => '#fff'
						),
					Array(
						'type' =>'colorpicker',
						'heading' => __('Overlay Color', 'jvfrmtd'),
						'holder' => 'div',
						'group' => $groupStyle,
						'param_name' => 'overlay_color',
						'value' => '#34495e'
					),
					Array(
						'type' =>'textfield',
						'heading' => __( 'Link Parameter', 'jvfrmtd'),
						'holder' => 'div',
						'class' => '',
						'group' => $groupAdvanced,
						'param_name' => 'jvbpd_featured_block_param',
						'value' => ''
					),
					Array(
						'type' =>'attach_image',
						'heading' => __('Image (If you want another image)', 'jvfrmtd'),
						'holder' => 'div',
						'class' => '',
						'group' => $groupAdvanced,
						'param_name' => 'attachment_other_image',
						'value' => ''
					),
					Array(
						'type' =>'dropdown',
						'heading' => __( "Map Template Page", 'jvfrmtd'),
						'holder' => 'div',
						'class' => '',
						'param_name' => 'map_template',
						'value' => apply_filters(
							'jvbpd_get_map_templates',
							Array( __( "No set", 'jvfrmtd') => '' )
						)
					)
				)
			));

			// Category Block 2
			/*vc_map(
				Array(
					'base'				=> 'jvbpd_category_box2'
					, 'name'				=> __( "Category Block 2", 'jvfrmtd' )
					, 'icon'				=> 'jv-vc-shortcode-icon shortcode-featured'
					, 'category'		=> __( "Javo", 'jvfrmtd' )
					, 'params'			=> Array()
				)
			);
*/

			// Author's Slider
			/*vc_map(array(
				'base'						=> 'jvbpd_slider_authors'
				, 'name'						=> __( "Authors Slider", 'jvfrmtd' )
				, 'category'				=> __( "Javo", 'jvfrmtd')
				, 'icon'						=> 'jv-vc-shortcode-icon shortcode-authors'
				, 'params'					=> Array(
					Array(
						'type'				=> 'textfield'
						, 'heading'			=> __( "User listing setting", 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'class'				=> ''
						, 'description'	=> __( "Default (10 New users) or Please add user id. comma (,) for a separator. ex) 11, 23, 44", 'jvfrmtd' )
						, 'param_name'	=> 'user_ids'
						, 'value'				=> ''
					)
					, Array(
						'type'				=> 'textfield'
						, 'heading'			=> __('Display amount of users.', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'			=> $groupAdvanced
						, 'class'				=> ''
						, 'param_name'	=> 'max_amount'
						, 'description'	=> __('(Only Number. recomend around 8)', 'jvfrmtd')
						, 'value'				=> intVal( 0 )
					)
					, Array(
						'type'				=> 'textfield'
						, 'heading'			=> __('Total amount of author to display.', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'			=> $groupAdvanced
						, 'class'				=> ''
						, 'param_name'	=> 'total_loading_items'
						, 'value'				=> ''
					)
					, Array(
						'type'				=> 'textfield'
						, 'heading'			=> __('Radius (0~50)', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'			=> $groupStyle
						, 'class'				=> ''
						, 'param_name'	=> 'radius'
						, 'description'	=> __('Category image radius', 'jvfrmtd')
						, 'value'				=> (int)0
					)
					, Array(
						'type'				=>'colorpicker'
						, 'heading'			=> __( "Author Name Color", 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'			=> $groupStyle
						, 'param_name'	=> 'inline_author_text_color'
						, 'value'				=> ''
					), Array(
						'type'				=>'colorpicker'
						, 'heading'			=> __('Name Hover Color', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'			=> $groupStyle
						, 'param_name'	=> 'inline_cat_text_hover_color'
						, 'value'				=> ''
					), Array(
						'type'				=>'colorpicker'
						, 'heading'			=> __('Arrow Color', 'jvfrmtd')
						, 'group'			=> $groupStyle
						, 'holder'			=> 'div'
						, 'param_name'	=> 'inline_author_arrow_color'
						, 'value'				=> ''
					)
				)
			));
*/

			// Mailchimp
			/*
			vc_map(array(
				'base'						=> 'jvbpd_mailchimp'
				, 'name'						=> __( "Mailchimp 1", 'jvfrmtd' )
				, 'icon'						=> 'jv-vc-shortcode-icon shortcode-mailchimp'
				, 'category'				=> __('Javo', 'jvfrmtd')
				, 'params'					=> Array(
					Array(
						'type'				=> 'dropdown'
						, 'heading'			=> __("LIST ID", 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'class'				=> ''
						, 'param_name'	=> 'list_id'
						, 'description'	=> __('You need to create a list id on mailchimp site, if you don`t have', 'jvfrmtd')
						, 'value'			=> apply_filters( 'jvbpd_mail_chimp_get_lists',  Array(
							__( "Theme Setting > General > Plugin > API KEY (Please add your API key)", 'jvfrmtd') => ''
						) )
					)
				)
			) );
			*/
		endif;


		do_action( 'jvbpd_shortcodes_loaded', $arrShortcodes );

		// Parse Modules
		if( ! empty( $arrModules ) ) : foreach( $arrModules as $artName => $artAttr ) {
			if( ! file_exists( $artAttr[ 'file'] ) )
				continue;
			require_once $artAttr[ 'file' ];
		} endif;

		do_action( 'jvbpd_modules_loaded', $arrModules );
	}

	// Shortcode contents output
	function jvbpd_parse_shortcode( $attr, $content=null, $tag )
	{
		if( !class_exists( $tag ) )
			return '';

		$obj	= new $tag();
		return $obj->output( $attr, $content );
	}
endif;

require_once( 'bp-shortcodes.php' );

if( function_exists( 'jvbpd_vc_manipulate_shortcodes' ) ) {
	add_action('vc_before_init', 'jvbpd_vc_manipulate_shortcodes');
}


require_once( 'class-other-shortcode.php' );

require_once( 'shortcode-bp_member_list.php' );
require_once( 'shortcode-bp_active_member_list.php' );
require_once( 'shortcode-bp_group_list.php' );
require_once( 'shortcode-login3.php' );
require_once( 'shortcode-register.php' );
require_once( 'shorcode-search1.php' );
require_once( 'shorcode-search-field.php' );
// require_once( 'shortcode-category-slider.php' );
jvbpd_bp_member_list::getInstance();
jvbpd_bp_active_member_list::getInstance();
jvbpd_bp_group_list::getInstance();
jvbpd_login3::getInstance();
jvbpd_register::getInstance();
jvbpd_search1::getInstance();
jvbpd_search_field::getInstance();
// jvbpd_category_slider::getInstance();