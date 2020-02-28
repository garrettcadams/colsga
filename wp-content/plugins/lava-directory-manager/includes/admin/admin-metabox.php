<table class="form-table lava-item-add-form">

	<?php
	$objField = new Lava_Directory_Manager_Field( '_logo', Array(
		'label'		=> esc_html__( "Logo", 'Lavacode' ),
		'element'	=> 'wp_library',
		'type'		=> 'text',
		'class'		=> 'all-options',
		'dialog_title' => esc_html__( "Select logo", 'Lavacode' ),
		'button_upload_label' => esc_html__( "Select", 'Lavacode' ),
		'button_remove_label' => esc_html__( "Reset", 'Lavacode' ),
	) );
	$objField->value = get_post_meta( intVal( $post->ID ), '_logo', true );
	echo $objField->output();

	$objField = new Lava_Directory_Manager_Field( '_tagline', Array(
		'label'		=> esc_html__( "TagLine", 'Lavacode' ),
		'element'	=> 'input',
		'type'		=> 'text',
		'class'		=> 'all-options',
		'placeholder' => esc_html__( "Tagline", 'Lavacode' )
	) );
	$objField->value = get_post_meta( intVal( $post->ID ), '_tagline', true );
	echo $objField->output(); ?>

	<tr>
		<th><?php _e( "Featured Item", 'Lavacode' ); ?></th>
		<td><input type="checkbox" name="lava_pt[featured]" value="1" <?php checked( '1' === get_post_meta( $post->ID, '_featured_item', true ) ); ?>></td>
	</tr>

	<?php
	$lava_item_fields	= apply_filters( "lava_{$this->post_type}_more_meta", Array() );

	if( !empty( $lava_item_fields ) && is_Array( $lava_item_fields ) ) {
		foreach( $lava_item_fields as $fID => $meta ) {
			$objField = new Lava_Directory_Manager_Field( $fID, $meta );
			$objField->value = get_post_meta( $GLOBALS[ 'post' ]->ID, $fID, true );
			echo $objField->output();
		}
	} ?>

	<?php do_action( 'lava_admin_additem_other_field', $post ); ?>

	<tr>
		<th><?php _e('Address on map', 'Lavacode');?></th>
		<td>
			<input class="lava_txt_find_address" type="text"><a class="button lava_btn_find_address"><?php _e('Find', 'Lavacode');?></a>
			<div class="lava-item-map-container"></div>
			<?php
			echo "Latitude : <input name='lava_pt[map][lat]' value='{$post->lat}' type='text' class='only-number'>" . ', ';
			echo "Longitude : <input name='lava_pt[map][lng]' value='{$post->lng}' type='text' class='only-number'>";
			echo "<input name='lava_pt[map][country]' value='{$post->country}' type='hidden'>";
			echo "<input name='lava_pt[map][locality]' value='{$post->locality}' type='hidden'>";
			echo "<input name='lava_pt[map][political]' value='{$post->political}' type='hidden'>";
			echo "<input name='lava_pt[map][political2]' value='{$post->political2}' type='hidden'>";
			echo "<input name='lava_pt[map][zipcode]' value='{$post->zipcode}' type='hidden'>";
			echo "<input name='lava_pt[map][address]' value='{$post->address}' type='hidden'>";
			 ?>
		</td>
	</tr>

	<tr>
		<th><?php _e('StreetView', 'Lavacode');?></th>
		<td>
			<label>
				<input type="hidden" name="lava_pt[map][street_visible]" value="0">
				<input type="checkbox" name="lava_pt[map][street_visible]" value="1" <?php checked( 1 == $post->street_visible );?>>
				<?php _e("Use StreetView", 'Lavacode');?>
			</labeL>
			<div class="lava-item-streetview-container<?php echo $post->street_visible == 0? ' hidden': '';?>"></div>
			<fieldset class="hidden">
				<?php
				echo "Latitude : <input name='lava_pt[map][street_lat]' value='{$post->street_lat}' type='text'>";
				echo "Longitude : <input name='lava_pt[map][street_lng]' value='{$post->street_lng}' type='text'>";
				echo "Heading : <input name='lava_pt[map][street_heading]' value='{$post->street_heading}' type='text'>";
				echo "pitch: <input name='lava_pt[map][street_pitch]' value='{$post->street_pitch}' type='text'>";
				echo "zoom : <input name='lava_pt[map][street_zoom]' value='{$post->street_zoom}' type='text'>"; ?>
			</fieldset>
		</td>
	</tr>

	<tr>
		<th><?php _e('Description Images', 'Lavacode');?></th>
		<td>
			<div class="">
				<a href="javascript:" class="button button-primary lava_pt_detail_add"><?php _e('Add Images', 'Lavacode');?></a>
			</div>
			<div class="lava_pt_images">
				<?php
				$images = get_post_meta( $post->ID, "detail_images", true );
				if(is_Array($images)){
					foreach($images as $iamge=>$src){
						$url = wp_get_attachment_image_src($src, 'thumbnail');
						printf("
						<div class='lava_pt_field' style='float:left;'>
							<img src='%s'><input name='lava_attach[]' value='%s' type='hidden'>
							<div class='' align='center'>
								<input class='lava_pt_detail_del button' type='button' value=\"" . __( "Delete", 'Lavacode' ) . "\">
							</div>
						</div>
						", $url[0], $src);
					};
				};?>
			</div>
		</td>
	</tr>
</table>