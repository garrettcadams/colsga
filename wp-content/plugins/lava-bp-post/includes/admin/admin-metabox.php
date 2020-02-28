<table class="form-table">

	<?php
	if( !empty( $lava_item_fields ) && is_Array( $lava_item_fields ) ) : foreach( $lava_item_fields as $fID => $meta ) {

		$this_value			= get_post_meta( $post->ID, $fID, true );

		echo "<tr>";
			echo "<td>{$meta['label']}</td>";
			echo "<td>";
				switch( $meta[ 'element'] ) {
					case 'select' :
						echo "<select name=\"lava_additem_meta[{$fID}]\">";
						if( !empty( $meta[ 'values' ] ) ) foreach( $meta[ 'values' ] as $value => $label )
							printf( "<option value=\"{$value}\"%s>{$label}</option>", selected( $this_value == $value, true, false ) );
						echo "</select>";
					break;
					case 'textarea' :
						echo join( ' ',
							Array(
								'<textarea name=lava_additem_meta['.$fID.'] style="resize:none; width:100%; height:150px;">'.esc_html( $this_value ).'</textarea>'
							)
						);
					break;
					case 'input' :
						echo "<input type=\"{$meta['type']}\" name=\"lava_additem_meta[{$fID}]\" value=\"{$this_value}\" class=\"{$meta[ 'class']}\">";
					break;

				}
			echo "</td>";
		echo "</tr>";

	} endif; ?>

	<?php do_action( 'lava_admin_additem_other_field', $post ); ?>

	<tr>
		<th><?php _e('Description Images', 'lvbp-bp-post');?></th>
		<td>
			<div class="">
				<a href="javascript:" class="button button-primary lava_pt_detail_add"><?php _e('Add Images', 'lvbp-bp-post');?></a>
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
								<input class='lava_pt_detail_del button' type='button' value=\"" . __( "Delete", 'lvbp-bp-post' ) . "\">
							</div>
						</div>
						", $url[0], $src);
					};
				};?>
			</div>
		</td>
	</tr>
</table>