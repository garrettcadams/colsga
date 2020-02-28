<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="custom">
	<h2> <?php esc_html_e("Javo Customization Settings", 'jvbpd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e( "CSS Stylesheet", 'jvbpd' );?>
		<span class="description"><?php esc_html_e('Please Add Your Custom CSS Code Here.', 'jvbpd' );?></span>
	</th><td>
		<h4><?php esc_html_e('Code:', 'jvbpd' );?></h4>
		<?php esc_html_e( '<style type="text/css">', 'jvbpd' );?>
		<fieldset>
			<textarea name="jvbpd_ts[custom_css]" class='large-text code' rows='15'><?php echo stripslashes( jvbpd_tso()->get( 'custom_css', '' ) );?></textarea>
		</fieldset>
		<?php esc_html_e( '</style>', 'jvbpd' );?>
	</td></tr><tr><th>
		<?php esc_html_e('Custom Script', 'jvbpd' );?>
		<span class="description">
			<?php esc_html_e(' If you have additional script, please add here.', 'jvbpd' );?>
		</span>
	</th><td>
		<h4><?php esc_html_e('Code:', 'jvbpd' );?></h4>
		<?php esc_html_e( '<script type="text/javascript">', 'jvbpd' );?>
		<fieldset>
			<textarea name="jvbpd_ts[custom_js]" class="large-text code" rows="15"><?php echo stripslashes( jvbpd_tso()->get('custom_js', ''));?></textarea>
		</fieldset>
		<?php esc_html_e( '</script>', 'jvbpd' );?>
		<div><?php esc_html_e('(Note : Please make sure that your scripts are NOT conflict with our own script or ajax core)', 'jvbpd' );?></div>
	</td></tr>
	</table>
</div>