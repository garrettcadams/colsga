<?php
/**
 *
 *	Single Listng
 *
 *	@package	Lavacode
 *	@subpackage Javo framework
 *	@author		JAVO
 */
get_header();
do_action( 'lava_' . get_post_type() . '_single_container_before' );
?>

<style type="text/css">
.elementor-panel-category {display:none;}
</style>

<div id="post-<?php the_ID();?>" <?php post_class( 'single-item-tab' ); ?>>
	<?php
	if( function_exists( 'jvbpd_single_core_render' ) ) {
		jvbpd_single_core_render();
	}
	// This Theme Hook
	//do_action( 'jvbpd_' . get_post_type() . '_single_body' );
	do_action( 'lava_' . get_post_type() . '_single_container_after' ); ?>
</div>
<?php
get_footer();