<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="footer">
	<h2> <?php esc_html_e("Footer Settings", 'jvbpd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e( "Select a Footer", 'jvbpd' );?>
		<span class="description"></span>
	</th><td>

		<?php
		if( function_exists( 'jvbpdCore' ) ) :
			foreach( Array(
				'elementor_footer' => Array(
					'label' => esc_html__( "Default(Global) Footer", 'jvbpd' ),
					'description' => sprintf( __(
						'To create more footers, Core >> page builder ( <a href="%1$s" target="_blank">Go page builder</a> )<br>
						 If you do not select, the recent footer ( you created ) will be displayed.', 'jvbpd' ),
						esc_url( add_query_arg( Array( 'post_type' => 'jvbpd-listing-elmt', ), admin_url( 'edit.php' ) ) )
					),
				),
				'elementor_footer_post' => Array(
					'label' => esc_html__( "Single post detail pages", 'jvbpd' ),
					'description' => esc_html__( 'If you do not select "Footer", "Default(Global) Footer" will be displayed.', 'jvbpd' ),
				),
				'elementor_footer_lv_listing' => Array(
					'label' => esc_html__( "Single listing detail pages", 'jvbpd' ),
					'description' => esc_html__( 'If you do not select "Footer", "Default(Global) Footer" will be displayed.', 'jvbpd' ),
				),

			) as $elementor_header_key => $elementor_header_meta ) {
				?>
				<h4><?php echo esc_html( $elementor_header_meta[ 'label' ] ); ?></h4>
				<fieldset class="inner">
					<select name="jvbpd_ts[<?php echo esc_attr($elementor_header_key); ?>]">
						<option value=''><?php esc_html_e( "Select a footer", 'jvbpd' ); ?></option>
						<?php
						foreach( jvbpdCore()->admin->getElementorFooterID() as $template_id  ) {
							if( false === get_post_status( $template_id ) ) {
								continue;
							}
							printf(
								'<option value="%1$s"%3$s>%2$s</option>', $template_id, get_the_title( $template_id ),
								selected( $template_id == jvbpd_tso()->get( $elementor_header_key, '' ), true, false )
							);
						} ?>
					</select>
					<?php
					if( isset( $elementor_header_meta[ 'description' ] ) ) { ?>
						<div>
							<span class="description"><?php echo esc_html($elementor_header_meta[ 'description' ]); ?></span>
						</div>
						<?php
					} ?>
				</fieldset>
				<?php
			}
		endif; ?>
	</td></tr>
	</table>
</div>