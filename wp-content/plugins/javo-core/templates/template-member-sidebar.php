<?php
global $members_template;

if( apply_filters( 'jvbpd_member_sidebar_display', true  ) ) {
	wp_enqueue_script( 'bp-widget-members' );
	wp_enqueue_script( 'bp-widget-groups' ); ?>

	<div class="jvfrm-jvbpd-member-sidebar-overlay"></div>
	<div class="jvfrm-jvbpd-member-sidebar widget">

		<div class="opener">
			<i class="show-close jv-icon3-left"></i>
			<i class="show-open jv-icon2-none"></i>
		</div>

		<?php if( shortcode_exists( 'jvlynk_bp_member_list' ) && shortcode_exists( 'jvlynk_bp_group_list' ) ) : ?>
			<div class="sections-group">
				<div class="section bp-member">
					<?php echo do_shortcode( sprintf( '[jvlynk_bp_member_list max="5" title="%s" filter_type="dropdown"]', esc_html__( "Members", 'jvfrmtd' ) ) ); ?>
				</div>
				<div class="section bp-group">
					<?php echo do_shortcode( sprintf( '[jvlynk_bp_group_list max="5" title="%s" filter_type="dropdown"]', esc_html__( "Groups", 'jvfrmtd' ) ) ); ?>
				</div>
			</div><!-- section-group -->
		<?php
		else:
			printf( '<div class="alert show-open bg-faded">%s</a>', esc_html__( "Please activate 'Core Plugin'", 'jvfrmtd' ) );
		endif;?>
	</div>
<?php
}