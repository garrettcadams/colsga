<?php

$output = '';

extract(
	shortcode_atts( array(
		'type' => 'newest',
		'number' => 12,
		'class' => '',
		'rounded' => "rounded"
	), $atts )
);

$params = array(
	'type' => $type,
	'per_page' => $number
);
if ($rounded == 'rounded') {
	$rounded = 'jvbpd-rounded';
}

if ( function_exists('bp_is_active') && bp_is_active('groups') ) {

	if ( bp_has_groups( $params ) ){

		$objShortcode = new Jvbpd_Shortcode_Parse(
			array(
				'display_category_tag' => 'hide',
			)
		);

		ob_start();
		?>
		<div class="mm wpb_wrapper shortcode-container no-flex-menu" id="<?php echo $objShortcode->getID(); ?>">
		<div id="groups-dir-list" class="groups dir-list">
		<?php $objShortcode->sHeader(); ?>
		<!-- ul id="groups-list" class="item-list row jvbpd-isotope masonry <?php echo $class;?>" -->
		<ul id="group-loop-animation" class="grid effect-2 <?php echo $class;?>">

			<?php while ( bp_groups() ) : bp_the_group(); ?>

				<li <?php bp_group_class( Array( 'shortcode-output' ) ); ?>>

					<?php
					$strModule = 'moduleBpGrid';
					if( class_exists( $strModule ) ) {
						$objContent = new stdClass;
						$objContent->ID = $objContent->post_status = $objContent->post_content = $objContent->post_type = null;
						$objContent->post_author = bp_get_group_id();
						$objContent->post_title = bp_get_group_name();
						$objArticle = new $strModule( $objContent, Array( 'hide_meta' => true ) );

						$objArticle->current_component = 'groups';
						$objArticle->component_action = sprintf( 'bp_directory_%s_actions', $objArticle->current_component );
						$objArticle->permalink = bp_get_group_permalink();
						$objArticle->title = $objArticle->get_title();
						$objArticle->avatar = bp_get_group_avatar('type=thumb&width=50&height=50&class="avatar rounded-circle author-img card-profile-img rounded-circle"');

						add_filter( 'jvbpd_module_css', 'add_group_loop_class', 10, 2 );
						add_action( 'jvbpd_module_hover_content', 'add_group_loop_action', 10, 2 );
						add_filter( 'jvbpd_module_thumbnail_src', 'bp_group_thumbnail', 10, 2 );
						echo $objArticle->output();
						remove_filter( 'jvbpd_module_css', 'add_group_loop_class', 10, 2 );
						remove_action( 'jvbpd_module_hover_content', 'add_group_loop_action', 10, 2 );
						remove_filter( 'jvbpd_module_thumbnail_src', 'bp_group_thumbnail', 10, 2 );
					} ?>
				</li>
			<?php endwhile; ?>

			</ul>
		<?php $objShortcode->sFooter(); ?>
		<script type="text/javascript">
			jQuery( document ).ready(function($) {
				var element = $( '#group-loop-animation' );
				if( 0 < element.length ) {
					new AnimOnScroll( element.get(0), {
						minDuration : 0.4,
						maxDuration : 0.7,
						viewportFactor : 0.2
					} );
				}
			});
		</script>
		</div>
		</div>

	<?php
	$output = ob_get_clean();

	}
	else
	{
		$output .= '<div class="alert alert-info">' . __( 'There are no groups to display. Please try again soon.', 'jvfrmtd' ) . '</div>';
	}

}
else
{
	$output = __("This shortcode must have Buddypress installed to work.",'jvfrmtd');
}

