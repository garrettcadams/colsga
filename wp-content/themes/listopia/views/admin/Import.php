<?php
// Get Theme Settings Default Values.
ob_start();
require_once( get_template_directory() . '/views/admin/default.txt' );
$jvbpd_theme_setting_default_values = ob_get_clean(); ?>

<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="import">
	<h2> <?php esc_html_e("Theme Settings Default Values", 'jvbpd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e('Import', 'jvbpd' );?>
		<span class="description">
			<?php esc_html_e('Please paste your previously saved theme settings values into the adjacent box. This may help you restore any backed-up theme settings.', 'jvbpd' );?>
		</span>
	</th><td>
		<h4><?php esc_html_e('Please paste your saved source into the box below.', 'jvbpd' );?></h4>
		<fieldset>
			<textarea class="large-text code javo-ts-import-field" rows="15"></textarea>
		</fieldset>
		<a class="button button-primary javo-btn-ts-import"><?php esc_html_e('Import options', 'jvbpd' );?></a>
	</td></tr><tr><th>
		<?php esc_html_e('Export', 'jvbpd' );?>
		<span class="description">
			<?php esc_html_e('Please copy and save the text in the adjacent box as a restore point for your preferred theme settings.', 'jvbpd' );?>
		</span>
	</th><td>
		<h4><?php esc_html_e('Please select and copy the source from the box below.', 'jvbpd' );?></h4>
		<fieldset>
			<textarea class="large-text code jv-export-textarea" rows="5"><?php echo maybe_serialize( jvbpd_get_theme_settings::getAll() );?></textarea>
		</fieldset>

	</td></tr><tr><th>
		<?php esc_html_e('Reset options', 'jvbpd' );?>
		<span class="description">
			<?php
			printf('<strong class="alert">%s</strong> %s'
				, esc_html__('Warning:', 'jvbpd' )
				, esc_html__('All values will be removed.', 'jvbpd' )
			);?>
		</span>
	</th><td>
		<textarea data-javo-ts-default-value class="hidden"><?php echo esc_textarea( $jvbpd_theme_setting_default_values );?></textarea>
		<a class="button button-primary javo-btn-ts-reset default"><?php esc_html_e('RESET DEFAULT OPTIONS', 'jvbpd' );?></a>
		<a class="button button-primary javo-btn-ts-reset"><?php esc_html_e('RESET OPTIONS', 'jvbpd' );?></a>
	</td></tr>
	</table>
</div>