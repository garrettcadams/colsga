<?php
/**
 *
 * Template Name: Header preview template ( for elementor )
 *
 *
 * Author: javo
 */

if( function_exists( 'jvbpd_layout' ) ) {
	remove_action( 'jvbpd_body_after', Array( jvbpd_layout(), 'footer' ) );
}
if( ! function_exists( 'jvbpd_header_preview_remove_post_header' ) ) {
	add_filter( 'jvbpd_single_post_header', 'jvbpd_header_preview_remove_post_header' );
	function jvbpd_header_preview_remove_post_header() { return 'notitle'; }
}

remove_all_actions( 'Javo/Header/Render' );
get_header();
?>
	<?php the_content(); ?>
<?php
get_footer();