<?php
/**
 *
 * Template Name: Single listing  preview template ( for elementor )
 *
 *
 * Author: javo
 */

if( ! function_exists( 'jvbpd_header_preview_remove_post_header' ) ) {
	add_filter( 'jvbpd_single_post_header', 'jvbpd_header_preview_remove_post_header' );
	function jvbpd_header_preview_remove_post_header() { return 'notitle'; }
}
get_header();
the_content();
get_footer();