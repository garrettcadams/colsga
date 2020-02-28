<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="contact">
	<h2> <?php esc_html_e('Contact Information Settings', 'jvbpd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e('Contact Information', 'jvbpd' );?>
		<span class="description">
			<?php esc_html_e('Add Your Contact Information', 'jvbpd' );?>
		</span>
	</th><td>
		<h4><?php esc_html_e('Address', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[address]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("address") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e('Phone', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[phone]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("phone") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e('Mobile', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[mobile]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("mobile") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e('Fax', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[fax]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("fax") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e('Email', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[email]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("email") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e('Working Hours', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[working_hours]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("working_hours") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e('Additional Information', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[additional_info]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("additional_info") );?>" class="large-text">
		</fieldset>
	</td></tr><tr><th>
		<?php esc_html_e('Social Network Service IDs', 'jvbpd' );?>
		<span class="description">
			<?php esc_html_e('Add your SSN information.', 'jvbpd' );?>
		</span>
	</th><td>
		<h4><?php esc_html_e("Facebook  ex) https://facebook.com/your_name",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[facebook]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("facebook") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Twitter",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[twitter]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("twitter") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Instagram",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[instagram]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("instagram") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Google+",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[google]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("google" ) );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Dribbble",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[dribbble]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("dribbble") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Youtube",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[youtube]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("youtube") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Flickr",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[flickr]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("flickr") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Pinterest",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[pinterest]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("pinterest") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Linkedin",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[linkedin]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("linkedin") );?>" class="large-text">
		</fieldset>

		<h4><?php esc_html_e("Website",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[website]" type="text" value="<?php echo sanitize_text_field( jvbpd_tso()->get("website") );?>" class="large-text">
		</fieldset>

	</td></tr>
	</table>
</div>