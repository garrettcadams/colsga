<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="woocommerce">
	<h2> <?php esc_html_e( "Woo Commerce Setting", 'jvfrmtd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e( "Layout and Sidebar", 'jvfrmtd' );?>
		<span class="description"></span>
	</th><td>
		<?php
		$arrBpPages = Array(
			'woo_archive_sidebar' => esc_html__( "Woocommerce Archive Page", 'jvfrmtd' ),
			'woo_single_sidebar' => esc_html__( "Woocommerce Single Page", 'jvfrmtd' ),
		);

		if( !empty( $arrBpPages ) ) {

			foreach( $arrBpPages as $strOption => $strOptionLabel ) {
				?>
				<h4><?php echo esc_html( $strOptionLabel ); ?></h4>
				<fieldset class="inner">
					<select name="jvbpd_ts[<?php echo esc_attr( $strOption ); ?>]">
						<option value=""><?php esc_html_e( "Select one", 'jvfrmtd' ); ?></option>
						<?php
						foreach(
							Array( 'left' => esc_html__( "Left", 'jvfrmtd' ), 'right' => esc_html__( "Right", 'jvfrmtd' ), 'full' => esc_html__( "Full", 'jvfrmtd' ) )
							as $strPositionOption => $strPositionLabel
						) {
							printf(
								'<option value="%1$s"%3$s>%2$s</option>',
								$strPositionOption, $strPositionLabel,
								selected( $strPositionOption == jvbpd_tso()->get( $strOption ), true, false )
							);
						} ?>
					</select>
				</fieldset>
				<?php
			}
		} ?>

	</td></tr>
	</table>
</div>