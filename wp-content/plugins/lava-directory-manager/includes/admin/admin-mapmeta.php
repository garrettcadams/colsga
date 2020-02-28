<?php
$lava_mapOptionsArgs		= Array();
$lava_mapOptions			= apply_filters( "lava_{$this->post_type}_map_options_args", $lava_mapOptionsArgs );
if( !empty( $lava_mapOptions ) ) : foreach( $lava_mapOptions as $mID => $mMeta ) {
	$thisValue	= get_post_meta( $post->ID, $mID, true );
	echo "<p><strong>{$mMeta['label']}</strong></p>";
	switch( $mMeta[ 'type'] ) :
		case 'select' :
			echo "<select name=\"lava_map_param[{$mID}]\">";
			if( !empty( $mMeta[ 'values' ] ) ) foreach( $mMeta[ 'values' ] as $value => $label )
				printf( "<option value=\"{$value}\"%s>{$label}</option>", selected( $thisValue == $value, true, false ) );
			echo "</select>";
		break; default:
			$placeholder	= isset( $mMeta[ 'placeholder' ] ) ? $mMeta[ 'placeholder' ] : '';
			echo "<p><input type=\"{$mMeta['type']}\" name=\"lava_map_param[{$mID}]\" placeholder=\"{$placeholder}\" value=\"{$thisValue}\"></p>";
	endswitch;
} endif;