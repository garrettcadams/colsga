<?php
/**
 *
 *	Single canvase
 *
 *	@package	Javo
 *	@subpackage Javo framework
 *	@author		JAVO
 */
get_header();
do_action( 'lava_' . get_post_type() . '_single_container_before' );
?>

<div id="post-<?php the_ID();?>" <?php post_class( 'single-item-tab' ); ?>>
	<?php
	the_content();
	do_action( 'lava_' . get_post_type() . '_single_container_after' ); ?>
</div>
<?php
get_footer();