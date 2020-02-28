<?php
$arrDetailImages = get_post_meta( $edit->ID, 'detail_images', true );
$arrAttachments = get_post_meta( $edit->ID, '_attachments', true );
$intDetailImageLimit = lava_directory()->submit->getLimitDetailImages();
$intDetailImageLimit = apply_filterS( 'Lava/Directory/Form/DetailImages/Limit', $intDetailImageLimit );
$required_fields = lava_directory()->admin->get_settings( 'required_fields', Array() );

$featuredImage_is_required = in_array( 'featured_image', $required_fields );
$detailImages_is_required = in_array( 'detail_images', $required_fields );

$arrDetailImageOutput = $arrAttachmentOutput = Array();
if( is_arraY( $arrDetailImages ) ) {
	foreach( $arrDetailImages as $intImage ) {
		$arrDetailImageOutput[] = Array(
			'val' => $intImage,
			'img' => wp_get_attachment_image( $intImage ),
		);
	}
}

if( is_arraY( $arrAttachments ) ) {
	foreach( $arrAttachments as $intAttachment ) {
		$strMinType = get_post_mime_type( $intAttachment );
		$arrAttachmentOutput[] = wp_parse_args(
			Array(
				'val' => $intAttachment,
				'img' => $strMinType,
			),
			lava_directory()->submit->getAttachmentInfo( $intAttachment )
		);
	}
} ?>

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
$objField->value = get_post_meta( intVal( get_query_var( 'edit' ) ), '_logo', true );
echo $objField->output(); ?>
<div class="lava-field-item form-inner field_featured_image">
	<label class="field-title">
		<?php
		if( $featuredImage_is_required ) {
			printf( '<span class="field-required-star">*</span>' );
		}
		_e( "Featured Image", 'Lavacode' ); ?>
	</label>
	<?php
	if( apply_filters( 'Lava/Directory/Form/FeaturedImage', true ) ) :
		if( ! is_user_logged_in() ) :
			if( has_post_thumbnail( $edit ) ) :
				echo get_the_post_thumbnail( $edit ); ?>
				<div>
					<label>
						<input type="checkbox" name="lava_remove_featured_file" value="1" style="width:auto;">
						<span><?php esc_html_e( "Remove featured image", 'Lavacode' ); ?></span>
					</label>
				</div>
			<?php endif; ?>
			<input type="file" name="lava_featured_file">
		<?php
		else:
			$intFeaturedID = get_post_thumbnail_id( $edit->ID );
			$strFeatured = wp_get_attachment_url( $intFeaturedID );
			?>
			<div class="lava-listing-wp-media" data-field="featured_id" data-multiple="false" data-modal-title="<?php esc_html_e( "Featured Image", 'Lavacode' ); ?>" data-button-select="<?php esc_html_e( "Select", 'Lavacode' ); ?>">
				<input type="hidden" name="featured_id" value="<?php echo $intFeaturedID; ?>">
				<div class="upload-preview" style="background-image:url(<?php echo $strFeatured; ?>);"></div>
				<div class="upload-action">
					<button type="button" class="action-add-item button">
						<i class="fa fa-plus"></i>
						<?php esc_html_e( "Select", 'Lavacode' ); ?>
					</button>
					<button type="button" class="item-clear button">
						<i class="fa fa-remove"></i>
						<?php esc_html_e( "Clear", 'Lavacode' ); ?>
					</button>
				</div>
			</div>
			<?php
		endif;
	endif;
	do_action( 'Lava/Directory/Form/FeaturedImage/After' );
	?>
</div>
<div class="form-inner">
	<label class="field-title">
		<?php
		if( $detailImages_is_required ) {
			printf( '<span class="field-required-star">*</span>' );
		}
		_e( "Detail Images", 'Lavacode' ); ?>
	</label>
	<?php if( ! is_user_logged_in() ) : ?>
		<div class="lava-upload-wrap" data-field="lava_additem_meta[detail_images][]" data-limit="<?php echo $intDetailImageLimit; ?>" data-value="<?php echo htmlspecialchars( json_encode( $arrDetailImageOutput ) ); ?>">
			<div class="upload-item-group"></div>
		</div>
	<?php
	else:
		?>
		<div class="lava-listing-wp-media" data-field="lava_additem_meta[detail_images][]" data-multiple="true" data-value="<?php echo htmlspecialchars( json_encode( $arrDetailImageOutput ) ); ?>" data-modal-title="<?php esc_html_e( "Detail Images", 'Lavacode' ); ?>" data-button-select="<?php esc_html_e( "Select", 'Lavacode' ); ?>" data-button-remove="<?php esc_html_e( "Remove", 'Lavacode' ); ?>" data-limit="<?php echo $intDetailImageLimit; ?>">
			<input type="hidden" name="lava_additem_meta[detail_images]">
			<div class="upload-item-group"></div>
			<div class="upload-action">
				<button type="button" class="action-add-item button">
					<i class="fa fa-plus"></i>
					<?php esc_html_e( "Select", 'Lavacode' ); ?>
				</button>
			</div>
		</div>
		<?php
	endif; ?>
</div>