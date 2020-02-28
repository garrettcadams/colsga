<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="logo">

<!-- Themes setting > Logo -->
	<h2><?php esc_html_e("Logo", 'jvbpd' );?></h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e("Header Logo Settings",'jvbpd' ); ?>
		<span class='description'>
			<?php esc_html_e("Uploaded logos will be displayed on the header in their appropriate locations.", 'jvbpd' );?>
		</span>
	</th>
	<td>

		<h4><?php esc_html_e("Main Logo ( Dark / Default )",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input type="text" name="jvbpd_ts[logo_url]" value="<?php echo esc_attr( jvbpd_tso()->get('logo_url') );?>" tar="logo_dark">
			<input type="button" class="button button-primary fileupload" value="<?php esc_attr_e('Select Image', 'jvbpd' );?>" tar="logo_dark">
			<input class="fileuploadcancel button" tar="logo_dark" value="<?php esc_attr_e('Delete', 'jvbpd' );?>" type="button">
			<p>
				<?php esc_html_e("Preview",'jvbpd' ); ?><br>
				<img src="<?php echo esc_attr( jvbpd_tso()->get('logo_url') );?>" tar="logo_dark">
			</p>
		</fieldset>

		<h4><?php esc_html_e("Main Logo ( Light )",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input type="text" name="jvbpd_ts[logo_light_url]" value="<?php echo esc_attr( jvbpd_tso()->get('logo_light_url') );?>" tar="logo_light">
			<input type="button" class="button button-primary fileupload" value="<?php esc_attr_e('Select Image', 'jvbpd' );?>" tar="logo_light">
			<input class="fileuploadcancel button" tar="logo_light" value="<?php esc_attr_e('Delete', 'jvbpd' );?>" type="button">
			<p>
				<?php esc_html_e("Preview",'jvbpd' ); ?><br>
				<img src="<?php echo esc_attr( jvbpd_tso()->get('logo_light_url') );?>" tar="logo_light">
			</p>
		</fieldset>

		<h4><?php esc_html_e("Small Logo ( Simple )",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input type="text" name="jvbpd_ts[logo_small_url]" value="<?php echo esc_attr( jvbpd_tso()->get('logo_small_url') );?>" tar="logo_small">
			<input type="button" class="button button-primary fileupload" value="<?php esc_attr_e('Select Image', 'jvbpd' );?>" tar="logo_small">
			<input class="fileuploadcancel button" tar="logo_small" value="<?php esc_attr_e('Delete', 'jvbpd' );?>" type="button">
			<p>
				<?php esc_html_e("Preview",'jvbpd' ); ?><br>
				<img src="<?php echo esc_attr( jvbpd_tso()->get('logo_small_url') );?>" tar="logo_small">
			</p>
		</fieldset>

		<h4><?php esc_html_e("Mobile Logo",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input type="text" name="jvbpd_ts[mobile_logo_url]" value="<?php echo esc_attr( jvbpd_tso()->get('mobile_logo_url') );?>" tar="mobile_logo">
			<input type="button" class="button button-primary fileupload" value="<?php esc_attr_e('Select Image', 'jvbpd' );?>" tar="mobile_logo">
			<input class="fileuploadcancel button" tar="mobile_logo" value="<?php esc_attr_e('Delete', 'jvbpd' );?>" type="button">
			<p>
				<?php esc_html_e("Preview",'jvbpd' ); ?><br>
				<img src="<?php echo esc_attr( jvbpd_tso()->get( 'mobile_logo_url' ) );?>" tar="mobile_logo">
			</p>
		</fieldset>

		<h4><?php esc_html_e("Retina Logo",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<p>
				<input type="text" name="jvbpd_ts[retina_logo_url]" value="<?php echo esc_attr( jvbpd_tso()->get( 'retina_logo_url' ) );?>" tar="g02">
				<input type="button" class="button button-primary fileupload" value="<?php esc_html_e('Select Image', 'jvbpd' );?>" tar="g02">
				<input class="fileuploadcancel button" tar="g02" value="<?php esc_html_e('Delete', 'jvbpd' );?>" type="button">
			</p>
			<p>
				<?php esc_html_e("Preview",'jvbpd' ); ?><br>
				<img src="<?php echo esc_attr( jvbpd_tso()->get( 'retina_logo_url' ) );?>" tar="g02">
			</p>
		</fieldset>

	</td></tr>
	</table>
</div>