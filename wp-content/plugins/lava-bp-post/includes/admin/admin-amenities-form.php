<?php
if( !isset( $lava_template_args ) ) {
	die();
}

$strLableText = isset( $is_edit_mode ) && $is_edit_mode ? false : esc_html__( "Amenities Icon", 'lvbp-bp-post' ); ?>

<?php if( $is_edit_mode ) : ?>
<tr>
	<th><?php esc_html_e( "Amenities Icon", 'lvbp-bp-post' ); ?></th>
	<td>
<?php endif; ?>

<div class="form-field">
	<label for="<?php echo $lava_template_args->fieldPrefix; ?>amenities_icon">
		<?php echo $strLableText; ?>
	</label>
	<input type="text" class="small-text" name="<?php echo $lava_template_args->fieldPrefix; ?>amenities_icon" value="<?php echo $lava_template_args->icon; ?>">
	<div class="lv-amenities-icon-description">
		<?php _e('<p>You can add icon class. <a href="http://fontawesome.io/icons/" target="_blank">Awesome Font Icons</a> <a href="http://wpjavo.com/a/jv-icons/demo.html" target="_blank">Javo Custom Icons</a><br>
							Before you use font icons, you need to enqueue icon code. <a href="http://fontawesome.io/get-started/" target="_blank">Here</a><br> 
							(If you are using javo themes, you do not need to enqueue)</p>','lvbp-bp-post'); ?>
	</div>
	<!-- p class="description"><?php _e( "Google", 'lvbp-bp-post');?></p -->
</div>
<?php if( $is_edit_mode ) : ?>
	</td>
</tr>
<?php endif; ?>