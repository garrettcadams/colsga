<?php
/**
 * The template for BuddyPress
 * 
 * This is for buddypress default layout
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * Last BP update : 3.2.1 ( 2019. 8.26 by Jason )
 * 
 * Modified files ( 2019.08.26 by Jason )
 * 1. /buddypress.php ( layout - full width : jvbp-wrap )
 * 2. /buddypress/members/single/parts/item-nav.php ( added a filter : echo apply_filters('jv_bp_nav_classes', '', 'member');)
 * 3. /buddypress/groups/single/parts/item-nav.php ( added a filter : echo apply_filters('jv_bp_nav_classes', '', 'group'); )
 * 4. /bbpress/includes/admin/settings.php ( Remove function 'screen_ico*' )
 * 5. /bbpress/includes/admin/tools.php ( Remove function 'screen_ico*' )
 * 6. /bbpress/includes/extend/akismet.php ( Change function get_optio*( 'hom*' ) to home_url() )
 * 7. /bbpress/includes/admin/admin.php ( Remove function add_submenu_pag* )
 * 8. /bbpress/includes/extend/akismet.php ( Remove function fsockope* )
 * 9. /buddypress/activity/activity-loop.php ( Added apply_filters('bp_nouveau_get_loop_classes', $activityClasses, 'activity') )
 * 10. /buddypress/groups/groups-loop.php ( Added span <?php bp_group_class( $classese = array('bp-img-num badge badge-info label-notice-num counter')); ?>><?php echo bp_get_group_member_count_int(); ?>)
 * 11. buddypress/activity/entry.php ( Added AOS )
 * 12. buddypress/groups/single/members-loop.php ( Added AOS )
 * 13. buddypress/groups/groups-loop.php ( Added AOS )
 * 14. bp-templates/bp-nouveau/buddypress/activity/entry.php ( Added AOS )
 */

get_header(); ?>

<div class="jvbp-wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				get_template_part( 'views/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile;
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php //get_sidebar(); ?>
</div><!-- .container -->

<?php
get_footer();
