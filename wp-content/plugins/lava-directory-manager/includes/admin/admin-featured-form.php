<div class="form-field">
	<p class="description">
		<?php _e( "Category markers : you need to refresh map data after you upload or change map pins (markers). Listings > Settings > Json Generator", 'Lavacode');?>
	</p>
	<div class="lava-edit-term-wp-upload">
		<div class="preview-wrap">
			<img src="" class="preview-upload">
		</div>
		<div class="action-wrap">
			<input type="hidden" name="lava_listing_category_marker" value="" data-type="url">
			<button type="button" class="button button-primary upload">
				<?php esc_html_e( "Select", 'Lavacode' ); ?>
			</button>
			<button type="button" class="button button-default remove">
				<?php esc_html_e( "Remove", 'Lavacode' ); ?>
			</button>
		</div>
	</div>
</div>
<?php
do_action( 'lava_file_script' );