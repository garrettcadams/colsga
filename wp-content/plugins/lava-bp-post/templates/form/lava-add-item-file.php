<?php
$arrDetailImages = get_post_meta( $edit->ID, 'detail_images', true );
$arrAttachments = get_post_meta( $edit->ID, '_attachments', true );
$intDetailImageLimit = lava_bpp()->submit->getLimitDetailImages();

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
			lava_bpp()->submit->getAttachmentInfo( $intAttachment )
		);
	}
} ?>

<div class="form-inner">
	<label class="field-title">
		<?php _e( "Featured Image", 'lvbp-bp-post' ); ?>
	</label>
	<?php if( ! is_user_logged_in() ) : ?>
		<?php if( has_post_thumbnail( $edit ) ) : ?>
			<?php echo get_the_post_thumbnail( $edit ); ?>
			<div>
				<label>
					<input type="checkbox" name="lava_remove_featured_file" value="1" style="width:auto;">
					<span><?php esc_html_e( "Remove featured image", 'lvbp-bp-post' ); ?></span>
				</label>
			</div>
		<?php endif; ?>
		<input type="file" name="lava_featured_file">
	<?php
	else:
		$intFeaturedID = get_post_thumbnail_id( $edit->ID );
		$strFeatured = wp_get_attachment_url( $intFeaturedID );
		?>
		<div class="lava-listing-wp-media" data-field="featured_id" data-multiple="false" data-modal-title="<?php esc_html_e( "Featured Image", 'lvbp-bp-post' ); ?>" data-button-select="<?php esc_html_e( "Select", 'lvbp-bp-post' ); ?>">
			<input type="hidden" name="featured_id" value="<?php echo $intFeaturedID; ?>">
			<div class="upload-preview" style="background-image:url(<?php echo $strFeatured; ?>);"></div>
			<div class="upload-action">
				<button type="button" class="action-add-item button">
					<i class="fa fa-plus"></i>
					<?php esc_html_e( "Select", 'lvbp-bp-post' ); ?>
				</button>
				<button type="button" class="item-clear button">
					<i class="fa fa-remove"></i>
					<?php esc_html_e( "Clear", 'lvbp-bp-post' ); ?>
				</button>
			</div>
		</div>
		<?php
	endif; ?>
</div>

<div class="form-inner">
	<label class="field-title"><?php _e("Detail Image", "lvbp-bp-post"); ?></label>
	<?php if( ! is_user_logged_in() ) : ?>
		<div class="lava-upload-wrap" data-field="lava_additem_meta[detail_images][]" data-limit="<?php echo $intDetailImageLimit; ?>" data-value='<?php echo json_encode( $arrDetailImageOutput ); ?>'>
			<div class="upload-item-group"></div>
		</div>
	<?php
	else:
		?>
		<div class="lava-listing-wp-media" data-field="lava_additem_meta[detail_images][]" data-multiple="true" data-value='<?php echo json_encode( $arrDetailImageOutput ); ?>' data-modal-title="<?php esc_html_e( "Detail Images", 'lvbp-bp-post' ); ?>" data-button-select="<?php esc_html_e( "Select", 'lvbp-bp-post' ); ?>" data-button-remove="<?php esc_html_e( "Remove", 'lvbp-bp-post' ); ?>" data-limit="<?php echo $intDetailImageLimit; ?>">
			<input type="hidden" name="lava_additem_meta[detail_images]">
			<div class="upload-item-group"></div>
			<div class="upload-action">
				<button type="button" class="action-add-item button">
					<i class="fa fa-plus"></i>
					<?php esc_html_e( "Select", 'lvbp-bp-post' ); ?>
				</button>
			</div>
		</div>
		<?php
	endif; ?>
</div>