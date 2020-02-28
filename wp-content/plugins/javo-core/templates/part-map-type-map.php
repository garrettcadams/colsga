<?php
$strMapOutputClass = sprintf(
	'class="%s"', join(
		' ',
		apply_filters(
			'jvbpd_map_output_class',
			Array( 'list-group', 'javo-shortcode' )
		)
	)
); ?>
<div class="javo-maps-container">
	<div class="javo-maps-panel-wrap">
		<?php
		if( function_exists('jvbpd_elements_tools') && 'maps' == jvbpd_elements_tools()->getMapType() ) {
			if( get_queried_object() instanceof \WP_Term ) {
				Jvbpd_Listing_Elementor::get_listing_archive_content( get_queried_object() );
			}else{
				the_content();
			}
		} ?>
	</div>
	<?php
	/*
	// Disable Fixed Map area
	<div <?php jvbpd_map_class( 'javo-maps-area-wrap' ); ?>>
		<div class="javo-maps-area"></div>
	</div> */ ?>
</div><!-- /.javo-maps-container -->

<?php
do_action( 'jvbpd_'  . jvbpdCore()->getSlug() . '_map_container_after', get_the_ID() );