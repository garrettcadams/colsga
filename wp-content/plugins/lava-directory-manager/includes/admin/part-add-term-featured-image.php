<?php
if( ! isset( $lavaArgs ) || ! isset( $lavaModal ) ) {
	return false;
} ?>
<div class="form-field">
	<label for="<?php echo $lavaArgs->fieldID; ?>"><?php echo $lavaArgs->subject; ?></label>
	<div class="lava-edit-term-wp-upload" data-args="<?php echo esc_attr( json_encode( $lavaModal ) ); ?>">
		<div class="preview-wrap"></div>
		<div class="action-wrap">
			<input type="hidden" name="<?php echo $lavaArgs->fieldName; ?>" value="">
			<button type="button" class="button button-primary upload">
				<?php esc_html_e( "Select", 'Lavacode' ); ?>
			</button>
			<button type="button" class="button button-default remove">
				<?php esc_html_e( "Remove", 'Lavacode' ); ?>
			</button>
		</div>
	</div>
</div>