<?php
$arrListingFilters	= apply_filters( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_list_filters', Array() );
$strListOutputClass = sprintf(
	'class="%s"', join(
		' ',
		apply_filters(
			'jvbpd_map_list_output_class',
			Array( 'javo-shortcode' )
		)
	)
); ?>
<div id="map-list-style-wrap" <?php function_exists( 'jvbpd_map_class' ) && jvbpd_map_class(); ?>>
	<?php
	do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_list_wrap_before', $GLOBALS[ 'post' ] );
	if( function_exists('jvbpd_elements_tools') && 'listings' == jvbpd_elements_tools()->getMapType() ) {
		if( get_queried_object() instanceof \WP_Term ) {
			Jvbpd_Listing_Elementor::get_listing_archive_content( get_queried_object() );
		}else{
			the_content();
		}
	}
	do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_list_wrap_after', $GLOBALS[ 'post' ] ); ?>
</div><!-- /.container-->