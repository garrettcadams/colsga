<?php

$output = '';

extract(
	shortcode_atts( array(
		'type' => 'newest',
		'member_type' => 'all',
		'number' => 12,
		'class' => '',
		'rounded' => "rounded",
		'online' => 'show'
	), $atts )
);

$params = array(
	'type' => $type,
	'scope' => $member_type,
	'per_page' => $number
);
if ($rounded == 'rounded') {
	$rounded = 'rounded';
}
if ( function_exists('bp_is_active') ) {
	if ( bp_has_members( $params ) ){

		$objShortcode = new Jvbpd_Shortcode_Parse(
			array(
				'display_category_tag' => 'hide',
			)
		);

		ob_start();
		?>
		<div class="mm wpb_wrapper shortcode-container no-flex-menu" id="<?php echo $objShortcode->getID(); ?>">
		<div id="members-dir-list" class="members dir-list">
		<?php $objShortcode->sHeader(); ?>
		<ul id="member-loop-animation" class="grid effect-2 <?php echo $class;?>">

		<?php

		$avatar_args = array(
			'type'   => 'thumb',
			'width'  => false,
			'height' => false,
			'class'  => 'avatar rounded-circle author-img card-profile-img',
			'id'     => false
		);
		while( bp_members() ) : bp_the_member();
			?>
			<li <?php bp_member_class( Array( 'shortcode-output' ) ); ?>>
				<?php
				$strModule = 'moduleBpGrid';
				if( class_exists( $strModule ) ) {
					$objContent = new stdClass;
					$objContent->ID = $objContent->post_status = $objContent->post_content = $objContent->post_type = null;
					$objContent->post_author = bp_get_member_user_id();
					$objContent->post_title = bp_get_member_name();
					$objArticle = new $strModule( $objContent, Array( 'hide_meta' => true ) );

					$objArticle->current_component = 'members';
					$objArticle->component_action = sprintf( 'bp_directory_%s_actions', $objArticle->current_component );
					$objArticle->permalink = bp_get_member_permalink();
					$objArticle->title = $objArticle->get_title();
					$objArticle->avatar = bp_get_member_avatar( $avatar_args );

					add_filter( 'jvbpd_module_css', 'add_member_loop_class', 10, 2 );
					add_action( 'jvbpd_module_hover_content', 'add_member_loop_action', 10, 2 );
					add_filter( 'jvbpd_module_thumbnail_src', 'bp_member_thumbnail', 10, 2 );
					echo $objArticle->output();
					remove_filter( 'jvbpd_module_css', 'add_member_loop_class', 10, 2 );
					remove_action( 'jvbpd_module_hover_content', 'add_member_loop_action', 10, 2 );
					remove_filter( 'jvbpd_module_thumbnail_src', 'bp_member_thumbnail', 10, 2 );
				} ?>
			</li>
			<?php
		endwhile;

		echo '</ul>';
		$objShortcode->sFooter();
		echo '</div>';
		echo '</div>';
		?>
		<script type="text/javascript">
			jQuery( document ).ready(function($) {
				var element = $( '#member-loop-animation' );
				if( 0 < element.length ) {
					new AnimOnScroll( element.get(0), {
						minDuration : 0.4,
						maxDuration : 0.7,
						viewportFactor : 0.2
					} );
				}
			});
		</script>
		<?php
		$output = ob_get_clean();
	}

}
else
{
	$output = __("This shortcode must have Buddypress installed to work.",'jvfrmtd');
}
