<?php
$arrPages = jvbpd_tso()->getPages(); ?>
<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="boddypress">
	<h2> <?php esc_html_e( "BuddyPress Setting", 'jvfrmtd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e( "Common", 'jvfrmtd' );?>
		<span class="description"></span>
	</th><td>

		<h4><?php esc_html_e( "BuddyPress or User Page Skin", 'jvfrmtd');?>: </h4>
		<fieldset  class="inner">
			<select name="jvbpd_ts[bp_skin]">
				<option value=""><?php esc_html_e( "Skin 1 - Dashboard Style", 'jvfrmtd' ); ?></option>
				<?php
				foreach( Array(
					'skin2' => esc_html__("Skin 2 - Dashboard Big BG"),
					'skin3' => esc_html__("Skin 3 - Center Big BG"),
					'skin4' => esc_html__("Skin 4 - BP Classic"),
					'skin5' => esc_html__("Skin 5"),
					) as $skinType => $skinLabel ) {
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						$skinType, $skinLabel,
						selected( jvbpd_tso()->get( 'bp_skin' ) == $skinType, true, false )
					);
				} ?>
			</select>
		</fieldset>

		<h4><?php esc_html_e("Default Cover Image For Members, Groups",'jvfrmtd'); ?></h4>
		<fieldset class="inner">
			<input type="text" name="jvbpd_ts[bp_no_image]" value="<?php echo esc_attr( jvbpd_tso()->get('bp_no_image') );?>" tar="bp_no_image">
			<input type="button" class="button button-primary fileupload" value="<?php esc_attr_e('Select Image', 'jvfrmtd');?>" tar="bp_no_image">
			<input class="fileuploadcancel button" tar="bp_no_image" value="<?php esc_attr_e('Delete', 'jvfrmtd');?>" type="button">
			<div class="description"><?php esc_html_e("If there is no members, groups cover images, it will be replaced", 'jvfrmtd');?></div>
			<p>
				<?php esc_html_e("Preview",'jvfrmtd'); ?><br>
				<img src="<?php echo esc_attr( jvbpd_tso()->get('bp_no_image') );?>" tar="bp_no_image">
			</p>
		</fieldset>

		<h4><?php esc_html_e( "BBPress Topic Front Form Page Setting", 'jvfrmtd');?>: </h4>
		<fieldset  class="inner">
			<select name="jvbpd_ts[bp_permalink]">
				<option value=""><?php esc_html_e( "Select a page", 'jvfrmtd' ); ?></option>
				<?php
				foreach( $arrPages as $objPage ) {
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						$objPage->ID, $objPage->post_title,
						selected( jvbpd_tso()->get( 'bp_permalink' ) == $objPage->ID, true, false )
					);
				} ?>
			</select>
			<div class="description"><?php esc_html_e("If you haven't created a topic front form page, please create a page with bbpress topic form shortcode. And then choose the page", 'jvfrmtd');?></div>
		</fieldset>

	</tr></tr><tr><th>
		<?php esc_html_e( "Member Page Disable menu", 'jvfrmtd' );?>
	</th><td>
		<?php
		$allRoles = wp_roles()->roles;
		$allRolesSettings = jvbpd_tso()->get( 'bp_page' );
		foreach( $allRoles as $role => $roleMeta ) {
			printf( '<h4>%s</h4>', $roleMeta[ 'name' ] );
			printf( '<fieldset class="inner">' );
			$current_role_settings = isset( $allRolesSettings[$role] ) ? $allRolesSettings[$role] : Array();
				foreach( Array(
					'listings' => esc_html__( "Listings", 'jvfrmtd' ),
					'favorites' => esc_html__( "Favorites", 'jvfrmtd' ),
					'reviews' => esc_html__( "Reviews", 'jvfrmtd' ),
					'orders' => esc_html__( "Orders", 'jvfrmtd' ),
					'activity' => esc_html__( "Activity", 'jvfrmtd' ),
					'profile' => esc_html__( "Profile", 'jvfrmtd' ),
					'notifications' => esc_html__( "Notification", 'jvfrmtd' ),
					'forums' => esc_html__( "Forum", 'jvfrmtd' ),
					'settings' => esc_html__( "Settings", 'jvfrmtd' ),
				) as $bp_page => $bp_page_label ) {
					$current_page_allow = isset( $current_role_settings[$bp_page] ) && '' != $current_role_settings[$bp_page];
					printf( '<label><input type="checkbox" name="jvbpd_ts[bp_page][%1$s][%2$s]"%4$s> %3$s</label>&nbsp;&nbsp;', $role, $bp_page, $bp_page_label,
						checked( $current_page_allow, true, false )
					);
				}
			printf( '</fieldset>' );

		} ?>


	</td></tr>
	</table>
</div>