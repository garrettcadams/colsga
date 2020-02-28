<?php
$arrAllowPostTypes		= apply_filters( 'jvbpd_single_post_types_array', Array( 'lv_listing' ) );
if( class_exists( 'lvDirectoryVideo_Render' ) ) {
	$objVideo = new lvDirectoryVideo_Render( get_post(), array(
		'width' => '100',
		'height' => '100',
		'unit' => '%',
	) );
	$is_has_video = $objVideo->hasVideo();
}else{
	$is_has_video = false;
}
if( class_exists( 'lvDirectory3DViewer_Render' ) ) {
	$obj3DViewer = new lvDirectory3DViewer_Render( get_post() );
	$is_has_3d = $obj3DViewer->viewer;
}else{
	$is_has_3d = false;
}
// Single page addon option
if( class_exists( 'Javo_Spot_Single_Addon' ) ){
	$single_addon_options = get_single_addon_options(get_the_ID());
	if($single_addon_options['background_transparent'] == 'disable'){
		$block_meta = 'extend-meta-block-wrap';
		if($single_addon_options['featured_height'] != '') $featured_height = 'style=height:'.$single_addon_options['featured_height'].'px;';
	}else{
		if($single_addon_options['featured_height'] != ''){
			$block_meta = '"style=height:auto;min-height:auto;';
			$featured_height = 'style=height:'.$single_addon_options['featured_height'].'px;';
		}
	}
}

$block_meta = 'extend-meta-block-wrap';
$featured_height = 'style=height:600px;';

$globalFirstDisplayDIV = jvbpd_tso()->get( 'lv_listing_single_first_header', 'featured' );
$firstDisplayDIV = get_post_meta( get_the_ID(), '_header_type', true );
if( $firstDisplayDIV ) {
	$globalFirstDisplayDIV = $firstDisplayDIV;
}

function jvbpd_custom_single_style() {
	if ( false === (boolean)( $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ) ) )
		$large_image_url	= '';
	else
		$large_image_url	=  $large_image_url[0];

	$output_style	= Array();
	$output_style[]	= sprintf( "%s:%s;", 'background-image'			, "url({$large_image_url})" );
	$output_style[]	= sprintf( "%s:%s;", 'background-attachment'	, 'fixed' );
	$output_style[]	= sprintf( "%s:%s;", 'background-repeat'		, 'no-repeat' );
	$output_style[]	= sprintf( "%s:%s;", 'background-position'		, 'center center' );
	$output_style[]	= sprintf( "%s:%s;", 'background-size'			, 'cover' );
	$output_style[]	= sprintf( "%s:%s;", '-webkit-background-size'	, 'cover' );
	$output_style[]	= sprintf( "%s:%s;", '-moz-background-size'		, 'cover' );
	$output_style[]	= sprintf( "%s:%s;", '-ms-background-size'		, 'cover' );
	$output_style[]	= sprintf( "%s:%s;", '-o-background-size'		, 'cover' );
	$output_style[]	= sprintf( "%s:%s;", 'height', '600px' );

	$output_style	= apply_filters( 'jvbpd_featured_detail_header'	, $output_style, $large_image_url );
	$output_style	= esc_attr( join( ' ', $output_style ) );

	echo "style=\"{$output_style}\"";
}
// Right Side Navigation
$jvbpd_rs_navigation = jvbpd_single_navigation();
 ?>
<div class="single-item-tab-feature-bg-wrap <?php echo sanitize_html_class( jvbpd_tso()->get( 'lv_listing_single_header_cover', null ) ); ?> <?php echo isset($block_meta) ? $block_meta : ''; ?>">
	<link rel="stylesheet" href="<?php echo get_template_directory_uri().'/assets/css/swiper.min.css'; ?>">
	<style>
		.swiper-container{width: 100%;height: 100%;}
		.swiper-slide{text-align: center;font-size: 18px;background:#fff;overflow:hidden;display:-webkit-box;display: -ms-flexbox;display: -webkit-flex;display: flex;-webkit-box-pack: center;-ms-flex-pack: center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-ms-flex-align:center;-webkit-align-items:center;align-items: center;}
		.swiper-slide img{height:100%;}
		.swiper-button-prev,
		.swiper-button-next{background-image:none;color:#fff;font-size:40px;width:auto;height:auto;max-width:40px;max-height:40px;opacity:0.85;}
		.swiper-button-prev:hover,
		.swiper-button-next:hover{opacity:1;}
		.swiper-container-horizontal>.swiper-pagination-bullets{bottom:5px;}
		.swiper-pagination-bullet{background:#fff;opacity:0.3;}
		.swiper-pagination-bullet-active{background:#fff;opacity:1;}
    </style>

	<div class="sinlge-header-height"></div>
	<div class="javo-core-single-featured-container" data-first="<?php echo esc_attr( $globalFirstDisplayDIV ); ?>">
		<?php
		$strCurrentTermURl = '';
		if( function_exists( 'lava_directory' ) ) {
			$intCurrentItemTerms = wp_get_object_terms( get_the_ID(), 'listing_category', array( 'fields' => 'ids' ) );
			$intCurrentItemTermsID = isset( $intCurrentItemTerms[0] ) ? $intCurrentItemTerms[0] : 0;
			$intCurrentTermFeaturedImage = lava_directory()->admin->getTermOption( $intCurrentItemTermsID, 'featured', 'listing_category' );
			$strCurrentTermURl = wp_get_attachment_image_url( $intCurrentTermFeaturedImage, 'full' );
		} ?>
		<div class="container-grid" <?php echo isset($featured_height) ? $featured_height : ''; ?>>
			<div class="swiper-container hidden">
				<div class="swiper-wrapper">
					<?php
					$arrImages = Array();
					if( is_object( $GLOBALS[ 'post' ] ) && !empty( $GLOBALS[ 'post' ]->attach ) )
						$arrImages = $GLOBALS[ 'post' ]->attach;

					if( !empty( $arrImages ) ) : foreach( $arrImages as $intImageID ) {
						if( $strSRC = wp_get_attachment_image_src( $intImageID, 'jvfrm-medium' ) ) {
							$strALT = explode("/",$strSRC[0]);
							$strALT = array_pop($strALT);
							printf( '<div class="swiper-slide"><img src="%s" alt="%s"></div>', $strSRC[0], $strALT );
						}
					} endif; ?>
				</div> <!-- swiper-wrapper -->
				<!-- Add Pagination -->
				<div class="swiper-pagination"></div>
				<!-- Arrow -->
				<div class="swiper-button-next"><span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></div>
				<div class="swiper-button-prev"><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span></div>
				<div class="jvbpd-single-header-gradient shadow <?php echo sanitize_html_class( jvbpd_tso()->get( 'lv_listing_single_header_cover', null ) ); ?>"></div>
			</div><!-- swiper-container -->
		</div>
		<div class="container-featured" <?php echo jvbpd_custom_single_style();?> data-background="<?php echo esc_url_raw( wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' ) ); ?>"></div>
		<div class="container-category-featured" <?php echo isset($featured_height) ? $featured_height : ''; ?> data-background="<?php echo esc_url_raw( $strCurrentTermURl ); ?>"></div>
		<div class="container-map" <?php echo isset($featured_height) ? $featured_height : ''; ?>></div>
		<div class="container-streetview" <?php echo isset($featured_height) ? $featured_height : ''; ?>></div>
		<div class="container-3dview" <?php echo isset($featured_height) ? $featured_height : ''; ?>>
			<script type="text/html">
				<?php
				if( $is_has_3d ) {
					$obj3DViewer->output();
				} ?>
			</script>
		</div>
		<div class="container-video" <?php echo isset($featured_height) ? $featured_height : ''; ?>>
			<?php
			if( $is_has_video )
				$objVideo->output();
			?>
		</div>
	</div>

    <!-- Initialize Swiper -->
    <script type="text/javascript">
	( function( $ ) {

		$( document.body ).on( 'javo:single_container_loaded', function( e, param ) {
			if( param.type == 'grid_style' ) {
				$( '.swiper-container' ).removeClass( 'hidden' );
				var swiper = new Swiper('.swiper-container', {
					pagination: '.swiper-pagination',
					slidesPerView: 3,
					paginationClickable: true,
					spaceBetween: 10,
					nextButton: '.swiper-button-next',
					prevButton: '.swiper-button-prev',
					breakpoints: {
						1024: {
							slidesPerView: 2,
							spaceBetween: 10
						},
						768: {
							slidesPerView: 2,
							spaceBetween: 10
						},
						640: {
							slidesPerView: 2,
							spaceBetween: 5
						},
						380: {
							slidesPerView: 1,
							spaceBetween: 0
						}
					}
				});
			}
		} );
	})( jQuery );
    </script>
</div>