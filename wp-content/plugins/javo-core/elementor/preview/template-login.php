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
?>
<div class="jvbpd-page-build preview-login-template">
	<?php
	the_content(); ?>
</div>
<?php
get_footer();