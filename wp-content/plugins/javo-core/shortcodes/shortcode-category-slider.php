<?php

class jvbpd_category_slider extends Jvbpd_OtherShortcode {

	private static $instance = null;
	public $loaded		= false;
	public $attr			= Array();
	private $sID			= false;

	public function __construct(){
		parent::__construct( Array(
			'name' => 'category_slider',
			'label' => esc_html__( "Category Slider", 'jvfrmtd' ),
			'params' => $this->getParams(),
		) );



		// add_action( 'vc_before_init', Array( $this, 'register_shortcode_with_vc' ), 11 );

		// Shortcode Resgistered
		//add_filter( 'JVBPD_other_shortcode_array', Array( $this, 'register_shortcode' ) );

		// Shortcode Enqueues & Scripts
		add_action( 'wp_footer', Array( $this, 'scripts' ) );
	}

	public function parse( $args ) {
		$arrDefault = Array();
		foreach( $this->getParams() as $params ) {
			$arrDefault[ $params[ 'param_name' ] ] = $params[ 'default_var' ];
		}
		return shortcode_atts( $arrDefault, $args );
	}

	public function getParams() {
		$strGroupFilter = esc_html__( 'Filter', 'jvfrmtd' );
		$strGroupStyle = esc_html__( 'Style', 'jvfrmtd' );
		$strGroupAdvanced = esc_html__( 'Advanced', 'jvfrmtd' );

		return Array(
			 Array(
				'type' => 'textfield'
				, 'heading' => esc_html__( "Title", 'jvfrmtd' )
				, 'holder' => 'div'
				, 'class' => ''
				, 'param_name'	=> 'title'
				, 'value' => ''
				, 'default_var' => ''
			),

			Array(
				'type'					=> 'dropdown'
				, 'heading'			=> esc_html__('Landing map ( Result page)', 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'class'				=> ''
				, 'param_name'	=> 'query_requester'
				, 'value'				=> apply_filters(
					'JVBPD_get_map_templates'
					, Array( esc_html__("Default Search Page", 'jvfrmtd') => '' )
				)
				, 'default_var' => ''
			),


		 // @group : filter

			Array(
				'type'					=> 'dropdown'
				, 'heading'			=> esc_html__( "Display Category Type", 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'group'				=> $strGroupFilter
				, 'class'				=> ''
				, 'param_name'	=> 'display_type'
				, 'value'				=> Array(
					esc_html__('Parent Only', 'jvfrmtd')		=> 'parent'
					, esc_html__('Parent + Child', 'jvfrmtd')	=> 'child'
				)
				, 'default_var' => ''
			),

			Array(
				'type'					=>'checkbox'
				, 'heading'			=> esc_html__( "Parents", 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'group'				=> $strGroupFilter
				, 'class'				=> ''
				, 'param_name'	=> 'have_terms'
				, 'description'		=> esc_html__('Default : All Parents', 'jvfrmtd')
				, 'value'				=> $this->get_term_ids( 'listing_category' )
				, 'default_var' => ''
			),

			Array(
				'type'					=> 'checkbox'
				, 'heading'			=> esc_html__('Random Ordering', 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'group'				=> $strGroupFilter
				, 'class'				=> ''
				, 'param_name'	=> 'rand_order'
				, 'value'				=> Array(esc_html__('Enabled', 'jvfrmtd') =>'use')
				, 'default_var' => ''
			),

		 //@group : Style

			 Array(
				'type'					=> 'textfield'
				, 'heading'			=> esc_html__('Radius', 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'group'				=> $strGroupStyle
				, 'class'				=> ''
				, 'param_name'	=> 'radius'
				, 'description'		=> esc_html__('Category image radius', 'jvfrmtd')
				, 'value'				=> intVal( 0 )
				, 'default_var' => ''
			),

			Array(
				'type'					=>'colorpicker'
				, 'heading'			=> esc_html__('Category Name Color', 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'group'				=> $strGroupStyle
				, 'param_name'	=> 'inline_cat_text_color'
				, 'value'				=> ''
				, 'default_var' => ''
			),

			Array(
				'type'					=>'colorpicker'
				, 'heading'			=> esc_html__('Name Hover Color', 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'group'				=> $strGroupStyle
				, 'param_name'	=> 'inline_cat_text_hover_color'
				, 'value'				=> ''
				, 'default_var' => ''
			),

			Array(
				'type'					=>'colorpicker'
				, 'heading'			=> esc_html__('Arrow Color', 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'group'				=> $strGroupStyle
				, 'param_name'	=> 'inline_cat_arrow_color'
				, 'value'				=> ''
				, 'default_var' => ''
			),


		 // @group : Advanced

			Array(
				'type'					=> 'textfield'
				, 'heading'			=> esc_html__('Display amount of items.', 'jvfrmtd')
				, 'holder'			=> 'div'
				, 'group'				=> $strGroupAdvanced
				, 'class'				=> ''
				, 'param_name'	=> 'max_amount'
				, 'description'		=> esc_html__('(Only Number. recomend around 8)', 'jvfrmtd')
				, 'value'				=> intVal( 0 )
				, 'default_var' => ''
			),
		);
	}

	public function scripts() {
		if( !$this->loaded ) {
			return;
		}
		$attr = $this->attr;
		wp_enqueue_script( 'owl-carousel-script' );
		wp_enqueue_script( 'javo-shortcode-script' );

	}

	public function register_shortcode( $shortcode ) {
		return wp_parse_args(
			Array(
				'jvbpd_category_slider' => Array( $this, 'config' )
			), $shortcode
		);
	}

	public function register_shortcode_with_vc() {

		$strGroupFilter = esc_html__( "Filter", 'jvfrmtd' );
		$strGroupAdvanced = esc_html__( "Advanced", 'jvfrmtd' );
		$strGroupStyle = esc_html__( "Style", 'jvfrmtd' );

		if( !function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map(
			Array(
				'base'						=> 'jvbpd_category_slider'
				, 'name'						=> esc_html__( "Category Slider 1", 'jvfrmtd')
				, 'icon'						=> 'jv-vc-shortcode-icon shortcode-slide1'
				, 'category'				=> esc_html__('Javo', 'jvfrmtd')
				, 'params'					=> Array(


				 //@group : general

					 Array(
						'type'					=> 'textfield'
						, 'heading'			=> esc_html__( "Title", 'jvfrmtd' )
						, 'holder'			=> 'div'
						, 'class'				=> ''
						, 'param_name'	=> 'title'
						, 'value'				=> ''
					),

					Array(
						'type'					=> 'dropdown'
						, 'heading'			=> esc_html__('Landing map ( Result page)', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'class'				=> ''
						, 'param_name'	=> 'query_requester'
						, 'value'				=> apply_filters(
							'JVBPD_get_map_templates'
							, Array( esc_html__("Default Search Page", 'jvfrmtd') => '' )
						)
					),


				 // @group : filter

					Array(
						'type'					=> 'dropdown'
						, 'heading'			=> esc_html__( "Display Category Type", 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'				=> $strGroupFilter
						, 'class'				=> ''
						, 'param_name'	=> 'display_type'
						, 'value'				=> Array(
							esc_html__('Parent Only', 'jvfrmtd')		=> 'parent'
							, esc_html__('Parent + Child', 'jvfrmtd')	=> 'child'
						)
					),

					Array(
						'type'					=>'checkbox'
						, 'heading'			=> esc_html__( "Parents", 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'				=> $strGroupFilter
						, 'class'				=> ''
						, 'param_name'	=> 'have_terms'
						, 'description'		=> esc_html__('Default : All Parents', 'jvfrmtd')
						, 'value'				=> $this->get_term_ids( 'listing_category' )
					),

					Array(
						'type'					=> 'checkbox'
						, 'heading'			=> esc_html__('Random Ordering', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'				=> $strGroupFilter
						, 'class'				=> ''
						, 'param_name'	=> 'rand_order'
						, 'value'				=> Array(esc_html__('Enabled', 'jvfrmtd') =>'use')
					),

				 //@group : Style

					 Array(
						'type'					=> 'textfield'
						, 'heading'			=> esc_html__('Radius', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'				=> $strGroupStyle
						, 'class'				=> ''
						, 'param_name'	=> 'radius'
						, 'description'		=> esc_html__('Category image radius', 'jvfrmtd')
						, 'value'				=> intVal( 0 )
					),

					Array(
						'type'					=>'colorpicker'
						, 'heading'			=> esc_html__('Category Name Color', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'				=> $strGroupStyle
						, 'param_name'	=> 'inline_cat_text_color'
						, 'value'				=> ''
					),

					Array(
						'type'					=>'colorpicker'
						, 'heading'			=> esc_html__('Name Hover Color', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'				=> $strGroupStyle
						, 'param_name'	=> 'inline_cat_text_hover_color'
						, 'value'				=> ''
					),

					Array(
						'type'					=>'colorpicker'
						, 'heading'			=> esc_html__('Arrow Color', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'				=> $strGroupStyle
						, 'param_name'	=> 'inline_cat_arrow_color'
						, 'value'				=> ''
					),


				 // @group : Advanced

					Array(
						'type'					=> 'textfield'
						, 'heading'			=> esc_html__('Display amount of items.', 'jvfrmtd')
						, 'holder'			=> 'div'
						, 'group'				=> $strGroupAdvanced
						, 'class'				=> ''
						, 'param_name'	=> 'max_amount'
						, 'description'		=> esc_html__('(Only Number. recomend around 8)', 'jvfrmtd')
						, 'value'				=> intVal( 0 )
					)
				)
			)
		);
	}

	public function config( $request_attr, $content="")
	{
		$this->attr = shortcode_atts(
			Array(
				'title'										=> false,
				'query_requester'					=> '',
				'display_type'							=> 'parent',
				'have_terms'							=> '',
				'max_amount'						=> 8,
				'rand_order'							=> null,
				'radius'									=> 50,
				'inline_cat_text_color'				=> '',
				'inline_cat_text_hover_color'	=> '',
				'inline_cat_arrow_color'			=> '',
			), $request_attr
		);

		$this->loaded	= true;
		$this->sID		= 'JVBPD_scd' . md5( wp_rand( 0 , 500 ) .time() );
		return $this->render( $this->attr );
	}

	public static function _getHeader( $param ) {

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

	public function get_term_ids( $taxonomy ){
		$jvbpd_this_return		= Array();
		$jvbpd_this_terms		= get_terms( $taxonomy, Array('hide_empty'=>false, 'parent'=>0) );
		if( !is_wp_error( $jvbpd_this_terms ) && !empty( $jvbpd_this_terms ) ){
			foreach( $jvbpd_this_terms as $term ) {
				$jvbpd_this_return[$term->name]		= $term->term_id;
			};
		};
		return $jvbpd_this_return;
	}


	public function render( $attr )
	{
		global $jvbpd_filter_prices, $jvbpd_tso;
		$strRequester	= apply_filters( 'JVBPD_wpml_link', intVal( $attr[ 'query_requester' ] ) );

		if( !class_exists( 'Lava_Directory_Manager' ) )
			return sprintf(
				'<p align="center">%s</p>',
				esc_html__( "Please, active to the 'Lava Directory manager' plugin", 'jvfrmtd' )
			);

		ob_start();?>

		<?php self::getHeader( $attr ); ?>
		<?php
		$have_terms = @explode(',', $attr[ 'have_terms' ] );
		$jvbpd_have_terms = Array();
		if( !empty($have_terms) ){
			foreach( $have_terms as $term){
				if( (int)$term <= 0 ){
					continue;
				};
				$jvbpd_have_terms[] = get_term( $term, 'listing_category' );

				// IF NOT ONLY PARENTS
				if( $attr[ 'display_type' ] != 'parent' )
				{
					$jvbpd_sub_cat = get_terms( 'listing_category', Array( 'parent' => $term , 'hide_empty'=> false ) );
					foreach( $jvbpd_sub_cat as $cat )
					{
						$jvbpd_have_terms[] = $cat;
					}
				}
			};
		};
		if( $attr[ 'max_amount' ]<=0 || $attr[ 'max_amount' ]>8) $attr[ 'max_amount' ]=8;
		if( $attr[ 'radius' ] >50 || $attr[ 'radius' ]<0) $attr[ 'radius' ]=50;
		$jvbpd_this_get_term_args				= Array();
		$jvbpd_this_get_term_args['hide_empty']	= false;

		if( $attr[ 'display_type' ] == 'parent' || $attr[ 'display_type' ] == '' )
		{
			$jvbpd_this_get_term_args['parent']	= 0;
		}

		$jvbpd_inline_category_terms = !empty( $jvbpd_have_terms )? $jvbpd_have_terms : get_terms("listing_category", $jvbpd_this_get_term_args);
		$jvbpd_get_terms_ids = Array();

		if($attr[ 'rand_order' ] != null) shuffle($jvbpd_inline_category_terms); //random ordering

		ob_start();?>
		<?php if($attr[ 'inline_cat_text_hover_color' ] !=''){
			?>
			<style>
			#javo-inline-category-slider-wrap .javo-inline-category:hover .javo-inline-cat-title{color:<?php echo $attr[ 'inline_cat_text_hover_color' ]; ?> !important;}
			</style>
			<?php
		}

		?>
		<div class="javo-shortcode shortcode-jvbpd_category_slider active" id="<?php echo sanitize_html_class( $this->sID ); ?>">
			<div id="javo-inline-category-slider-wrap" class="jv-inline-category-slider">
				<div id="javo-inline-category-slider-inner">
					<div id="javo-inline-category-slider" class="owl-carousel owl-theme" style="display:block;" data-max="<?php echo intVal(  $attr[ 'max_amount' ] ); ?>">
						<?php
						if( !empty( $jvbpd_inline_category_terms ) )
						{
							foreach( $jvbpd_inline_category_terms as $terms )
							{
								$featured = get_option( 'lava_listing_category_'.$terms->term_id.'_featured', '' );
								$featured = wp_get_attachment_image_src( $featured, 'thumbnail' );
								$featured = $featured[0];
								$featured = $featured != ''? $featured : jvbpd_tso()->get('no_image', JVBPD_IMG_DIR.'/no-image.png');
								?>
								<div class="item javo-inline-category">
									<a href="<?php echo esc_url( add_query_arg( Array( 'category' => $terms->term_id ), $strRequester ) ); ?>">
										<img src="<?php echo $featured; ?>"style="width:111px; height:111px; border-radius:<?php echo $attr[ 'radius'];?>%;">
										<div class="javo-inline-cat-title" style="	<?php if($attr[ 'inline_cat_text_color' ] !='') echo 'color:'.$attr [' inline_cat_text_color' ] . ';' ?>">
											<?php echo $terms->name; ?>
										</div>
									</a>
								</div>
							<?php
							}
						} ?>
					</div>
					<div class="customNavigation">
					  <a class="btn prev" <?php if($attr[ 'inline_cat_arrow_color' ] !='') echo 'style="color:'.$attr[ 'inline_cat_arrow_color' ].';"'?>><i class="fa fa-angle-left"></i></a>
					  <a class="btn next" <?php if($attr[ 'inline_cat_arrow_color' ] !='') echo 'style="color:'.$attr[ 'inline_cat_arrow_color' ].';"'?>><i class="fa fa-angle-right"></i></a>
					</div><!--javo-inline-category-slider-->
				</div><!--javo-inline-category-slider-inner-->
			</div><!--javo-inline-category-slider-wrap-->
		</div><!-- /.shortcode-jvbpd_category_slider -->
	<?php
		$arrOutput		= Array();
		$arrOutput[]	= "<script type=\"text/javascript\">";
		$arrOutput[]	= "jQuery( function($){ $.JVBPD_search_shortcode( '#{$this->sID}' ); });";
		$arrOutput[]	= "</script>";
		//echo join( '', $arrOutput );
		echo ob_get_clean();
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}