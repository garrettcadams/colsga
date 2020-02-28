<?php
if( ! isset( $lavaArgs ) || ! isset( $lavaModal ) ) {
	return false;
} ?>
<tr class="form-field">
	<th scope="row" valign="top">
		<label for="<?php echo $lavaArgs->fieldID; ?>"><?php echo $lavaArgs->subject; ?></label>
	</th>
	<td>
		<div class="lava-edit-term-wp-upload" data-args="<?php echo esc_attr( json_encode( $lavaModal ) ); ?>">
			<div class="action-wrap">
				<input type="hidden" name="<?php echo $lavaArgs->fieldName; ?>" value="<?php echo $lavaArgs->featured_id;?>">
				<button type="button" class="button button-primary upload">
					<?php esc_html_e( "Select", 'Lavacode' ); ?>
				</button>
				<button type="button" class="button button-default remove">
					<?php esc_html_e( "Remove", 'Lavacode' ); ?>
				</button>
			</div>
			<div class="preview-wrap" data-image="<?php echo esc_url( $lavaArgs->featured_src ); ?>"></div>
		</div>
	</td>
</tr>