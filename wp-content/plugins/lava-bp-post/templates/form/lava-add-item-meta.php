<?php
$lava_post_type = lava_bpp()->core->slug;
$lava_item_fields = apply_filters( "lava_{$lava_post_type}_more_meta", Array() );

if( !empty( $lava_item_fields ) && is_Array( $lava_item_fields ) ) {
	foreach( $lava_item_fields as $fID => $meta ) {
		$objField = new Lava_Bp_Post_Field( $fID, $meta );
		$objField->value = get_post_meta( intVal( get_query_var( 'edit' ) ), $fID, true );
		echo $objField->output();
	}
}