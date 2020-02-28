<?php
// Get Item Tages
{
	$jvbpd_all_tags				= '';
	foreach( get_tags( Array( 'fields' => 'names' ) ) as $tags ) {
		$jvbpd_all_tags			.= "{$tags}|";
	}
	$jvbpd_all_tags				= substr( $jvbpd_all_tags, 0, -1 );
}

//
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
echo 'aaa
	<?php
	if( is_singular( 'page' ) ) {
		if( ! function_exists( 'jvbpd_elements_tools' ) || 'maps' == jvbpd_elements_tools()->getMapType() ) {
			the_content();
		}
	}else{
		if( function_exists( 'jvbpd_listing_achive_render' ) ){
			jvbpd_listing_achive_render();
		}
	} ?>
</div><!-- /.javo-maps-container -->

<?php

// Map Container After
do_action( 'jvbpd_'  . jvbpdCore()->getSlug() . '_map_container_after', get_the_ID() );