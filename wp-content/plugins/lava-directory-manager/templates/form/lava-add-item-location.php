<div class="lava-field-item form-inner field_map_address">
	<label class="field-title"><?php _e( "Location", 'Lavacode' ); ?></label>
	<div class="address-group">
		<input class="lava-add-item-map-search" placeholder="<?php _e("Type an Address","Lavacode");?>">
		<input type="button" value="<?php _e('Find','Lavacode'); ?>" class="lava-add-item-map-search-find">
	</div>
</div>

<div class="lava-field-item map_area field_map"></div>

<input type="hidden" name="lava_location[country]" value="<?php echo $edit->country; ?>">
<input type="hidden" name="lava_location[locality]" value="<?php echo $edit->locality; ?>">
<input type="hidden" name="lava_location[political]" value="<?php echo $edit->political; ?>">
<input type="hidden" name="lava_location[political2]" value="<?php echo $edit->political2; ?>">
<input type="hidden" name="lava_location[zipcode]" value="<?php echo $edit->zipcode; ?>">
<input type="hidden" name="lava_location[lat]" class="only-number" value="<?php echo $edit->lat; ?>">
<input type="hidden" name="lava_location[lng]" class="only-number" value="<?php echo $edit->lng; ?>">
<input type="hidden" name="lava_location[address]" class="only-number" value="<?php echo $edit->lng; ?>">

<div class="lava-field-item form-inner field_use_streetview">
	<label class="field-title"><?php _e( "Streetview Setting", 'Lavacode' ); ?></label>
	<input type="hidden" name="lava_location[street_visible]" value="0">
	<label>
		<input type="checkbox" name="lava_location[street_visible]" class='lava-add-item-set-streetview' value="1" <?php checked( $edit->street_visible == 1 ); ?>>
		<?php _e( "Show Streeview", 'Lavacode' ); ?>
	</label>
</div>


<div class="lava_map_advanced hidden">
	<div class="lava-field-item map_area_streetview field_streeview"></div>
	<?php
	foreach( Array(
		'street_lat' => Array( 'label' => esc_html__( "Streetview Lat", 'Lavacode' ), ),
		'street_lng' => Array( 'label' => esc_html__( "Streetview Lng", 'Lavacode' ), ),
		'street_heading' => Array( 'label' => esc_html__( "POV: Heading", 'Lavacode' ), ),
		'street_pitch' => Array( 'label' => esc_html__( "POV: Pitch", 'Lavacode' ), ),
		'street_zoom' => Array( 'label' => esc_html__( "POV : Zoom", 'Lavacode' ), ),
	) as $fID => $meta ) {
		$meta = wp_parse_args( Array(
			'element' => 'input',
			'type' => 'text',
			'class' => 'all-options',
		), $meta );
		$objField = new Lava_Directory_Manager_Field( $fID, $meta );
		$objField->fieldGroup = 'lava_location';
		$objField->fieldClassPrefix = 'field_';
		$objField->value = floatVal( get_post_meta( intVal( get_query_var( 'edit' ) ), 'lv_listing_' . $fID, true ) );
		echo $objField->output();
	} ?>
</div>
