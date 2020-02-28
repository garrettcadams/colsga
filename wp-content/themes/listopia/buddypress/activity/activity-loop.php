<?php
/**
 * BuddyPress - Activity Loop
 *
 * @version 3.1.0
 */

bp_nouveau_before_loop(); ?>

<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) : ?>

	<?php if ( empty( $_POST['page'] ) || 1 === (int) $_POST['page'] ) :
		$activityClasses = Array( 'activity-list', 'item-list bp-list');
		$activityClasses = apply_filters('bp_nouveau_get_loop_classes', $activityClasses, 'activity'); ?>
		<ul id="activity-list" class="<?php echo join( ' ', $activityClasses ); ?>">
	<?php endif; ?>

	<?php
	while ( bp_activities() ) :
		bp_the_activity();
	?>

		<?php bp_get_template_part( 'activity/entry' ); ?>

	<?php endwhile; ?>

	<?php if ( bp_activity_has_more_items() ) : ?>

		<li class="load-more">
			<a href="<?php bp_activity_load_more_link(); ?>"><?php echo esc_html_x( 'Load More', 'button', 'jvbpd' ); ?></a>
		</li>

	<?php endif; ?>

	<?php if ( empty( $_POST['page'] ) || 1 === (int) $_POST['page'] ) : ?>
		</ul>
	<?php endif; ?>

<?php else : ?>

		<?php bp_nouveau_user_feedback( 'activity-loop-none' ); ?>

<?php endif; ?>

<?php bp_nouveau_after_loop(); ?>
