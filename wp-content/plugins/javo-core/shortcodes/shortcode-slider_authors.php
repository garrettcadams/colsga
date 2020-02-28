<?php
class jvbpd_slider_authors
{
	static $load_script = false;
	public function __construct()
	{
		add_shortcode( 'jvbpd_slider_authors', Array( __CLASS__, 'callback'));
		add_action( 'wp_footer', Array( __CLASS__, 'load_script_func' ), 10, 1);
	}

	public static function callback( $atts, $content='' )
	{
		self::$load_script = true;

		extract(
			shortcode_atts(
				Array(
					'user_ids'									=> false
					, 'max_amount'							=> 8
					, 'total_loading_items'				=> 10
					, 'radius'									=> 50
					, 'inline_author_text_color'		=> ''
					, 'inline_cat_text_hover_color'	=> ''
					, 'inline_author_arrow_color'	=> ''
				)
				, $atts
			)
		);

		if($max_amount<=0) $max_amount=8;
		if($radius>50 || $radius<0) $radius=50;
		$jvbpd_this_get_term_args				= Array();
		$jvbpd_this_get_term_args['hide_empty']	= false;

		$arrGetUserArgs			= Array(
			'blog_id'					=> get_current_blog_id()
			, 'number'					=> intVal( $total_loading_items )
			, 'orderby'					=> 'registered'
			, 'order'						=> 'DESC'
		);

		if( !empty( $user_ids ) )
			$arrGetUserArgs[ 'include' ] = @explode( ',', $user_ids );

		$jvbpd_getUserQuery	= new WP_User_Query( $arrGetUserArgs );
		$jvbpd_getUsers			= $jvbpd_getUserQuery->get_results();
		ob_start();

		?>

		<style>
		<?php if( $inline_cat_text_hover_color != '' ){ ?>
		#javo-inline-category-slider-wrap .javo-inline-category:hover .javo-inline-cat-title{color:<?php echo $inline_cat_text_hover_color ?> !important;}
		<?php } ?>
		.item.javo-inline-category img{width:110px; height:110px; border-radius: <?php echo $radius; ?>%;}
		</style>

		<!-- Slider Shortcode Wrap -->
		<div id="javo-inline-category-slider-wrap">
			<div id="javo-inline-category-slider-inner">
				<div id="javo-inline-category-slider" class="owl-carousel owl-theme" style="display:block;">

					<?php
					if( !empty( $jvbpd_getUsers ) ) : foreach( $jvbpd_getUsers as $user ) {
							$user_image = get_avatar( $user->ID, 150  );
						?>

						<div class="item javo-inline-category">
							<a href="<?php echo function_exists( 'lynk_getUserPage') ? lynk_getUserPage( $user->ID ) : '#';?>">
								<?php
								if( $user_image!='' ){
									echo $user_image;
								}else{
									printf('<img src="%s" class="img-responsive wp-post-image" style="margin: 0 auto; width:121px; height:121px; border-radius:%s%%;">', jvbpd_tso()->get('no_image', JVBPD_IMG_DIR.'/no-image.png')
									, $radius);
								};?>
								<div class="javo-inline-cat-title" style="	<?php if($inline_author_text_color!='') echo 'color:'.$inline_author_text_color.';' ?>">
									<?php echo mb_strtoupper($user->display_name); ?>
								</div>
							</a>
						</div>
					<?php } endif; ?>

				</div>
				<div class="customNavigation">
				  <a class="btn prev" <?php if($inline_author_arrow_color!='') echo 'style="color:'.$inline_author_arrow_color.';"'?>><i class="fa fa-angle-left"></i></a>
				  <a class="btn next" <?php if($inline_author_arrow_color!='') echo 'style="color:'.$inline_author_arrow_color.';"'?>><i class="fa fa-angle-right"></i></a>

				</div><!--javo-inline-category-slider-->
			</div><!--javo-inline-category-slider-inner-->
		</div><!--javo-inline-category-slider-wrap-->

		<script type="text/javascript">

			jQuery( function( $ ) {
				var el			= $( "#javo-inline-category-slider-wrap" );
				var el_slider	= el.find( "#javo-inline-category-slider" );

				el_slider.owlCarousel({
					items				: parseInt(<?php echo $max_amount; ?>), //10 items above 1000px browser width
					itemsDesktop		: [1000,5], //5 items between 1000px and 901px
					itemsDesktopSmall	: [900,3], // 3 items betweem 900px and 601px
					itemsTablet			: [600,2], //2 items between 600 and 0;
					itemsMobile			: false // itemsMobile disabled - inherit from itemsTablet option
				});

				$( el )
					.on( 'click', '.next', function(){ el_slider.trigger( 'owl.next' ); } )
					.on( 'click', '.prev', function(){ el_slider.trigger( 'owl.prev' ); } )
					.on( 'click', '.play', function(){ el_slider.trigger( 'owl.play', 1000 ); } )
					.on( 'click', '.stop', function(){ el_slider.trigger( 'owl.stop' ); } )
			} );
		</script>

		<?php
		wp_reset_query();
		$content = ob_get_clean();
		return $content;
	}

	public static function load_script_func() {
		if( ! self::$load_script )
			return;
		wp_enqueue_script('owl-carousel' );
	}
}
new jvbpd_slider_authors;