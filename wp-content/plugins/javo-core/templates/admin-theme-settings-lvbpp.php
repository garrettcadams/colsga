<?php
$arrPages = jvbpd_tso()->getPages(); ?>
<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="lava_bpp">
	<h2> <?php esc_html_e( "Lava Bp post Setting", 'jvfrmtd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e( "General", 'jvfrmtd' );?>
		<span class="description"></span>
	</th><td>
		<h4><?php esc_html_e( "Lava BP Post Front Form Page", 'jvfrmtd' );?>: </h4>
		<fieldset  class="inner">
			<select name="jvbpd_ts[lvbpp_permalink]">
				<option value=""><?php esc_html_e( "Select a page", 'jvfrmtd' ); ?></option>
				<?php
				foreach( $arrPages as $objPage ){
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						$objPage->ID, $objPage->post_title,
						selected( jvbpd_tso()->get( 'lvbpp_permalink' ) == $objPage->ID, true, false )
					);
				} ?>
			</select>
		</fieldset>
		<div class="description"><?php esc_html_e("If you haven't created a post front form page, please create a page with lava post form shortcode. And then choose the page", 'jvfrmtd');?></div>
	</td></tr>
	</table>
</div>