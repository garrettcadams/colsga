<?php
if( 0 < intVal( get_query_var( 'edit' ) ) ) {
	$edit = get_post( get_query_var( 'edit' ) );
}else{
	$edit = new stdClass;
	$edit->ID = 0;
	$edit->post_title = $edit->post_content = $edit->post_parent = null;
}
$objEvent = jvbpd_events();
?>
<!-- Content Start -->
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"><?php esc_html_e( "Add Event", 'jvfrmtd' ); ?></h4>
			</div><!-- card-header -->

			<div class="card-block"><div>

			<?php
			if( $objEvent->hasMessage() ) {
				echo $objEvent->outputMessage();
			} ?>

			<form class="floating-labels00" method="post">
				<div class="form-group row">
					<div class="col-xs-12 col-md-2"><label class="col-form-label box-title"><?php esc_html_e('Title', 'jvfrmtd') ?></label></div>
					<div class="col-10"><input type="text" class="form-control" id="event-title" name="txtTitle" value="<?php echo $edit->post_title; ?>" required></div>
				</div>

				<div class="form-group row">
					<div class="col-xs-12 col-md-2"><label class="col-form-label box-title"><?php esc_html_e('Original Listing', 'jvfrmtd') ?></label></div>
					<div class="col-10">
						<select class="form-control p-0" id="input6" name="selParent" required>
							<option value=""><?php esc_html_e( "Choose a item", 'jvfrmtd' ); ?></option>
							<?php
							$arrItems = $objEvent->getItems();
							if( !empty( $arrItems ) ) {
								foreach( $arrItems as $objItem ) {
									printf(
										'<option value="%1$s"%3$s>%2$s</option>',
										$objItem->ID, $objItem->post_title,
										selected( $objItem->ID == $edit->post_parent, true, false )
									);
								}
							} ?>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-xs-12 col-md-2"><label class="col-form-label box-title"><?php esc_html_e('Event Date', 'jvfrmtd') ?></label></div>
					<div class="col-10">
						<div class="row">
							<div class="col-md-2">
								<?php esc_html_e( "Start Day", 'jvfrmtd' ); ?>
							</div>
							<div class="col-md-3">
								<input type="text" name="EventStartDate" class="form-control" data-date-picker value="<?php echo $objEvent->getEventDate( $edit->ID, '_EventStartDate', '' ); ?>">
								<input type="hidden" name="EventStartTime" class="form-control" value="00:00:00">
							</div>
							<!-- div class="col-md-2">
								<input type="text" name="EventStartTime" class="form-control">
							</div -->

							<div class="col-md-2 text-center">
								<?php esc_html_e( "To", 'jvfrmtd' ); ?>
							</div>

							<div class="col-md-2">
								<?php esc_html_e( "End Day", 'jvfrmtd' ); ?>
							</div>
							<div class="col-md-3">
								<input type="text" name="EventEndDate" class="form-control" data-date-picker value="<?php echo $objEvent->getEventDate( $edit->ID, '_EventEndDate', '' ); ?>">
								<input type="hidden" name="EventEndTime" class="form-control" value="00:00:00">
							</div>
							<!-- div class="col-md-2">
								<input type="text" name="EventEndTime" class="form-control">
							</div -->
						</div>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-xs-12 col-md-2"><label class="col-form-label box-title"><?php esc_html_e('Choose a categories', 'jvfrmtd') ?></label></div>
					<div class="col-10">
						<select class="form-control p-0" id="input6" name="selCategory">
							<option value=""><?php esc_html_e( "Choose a categories", 'jvfrmtd' ); ?></option>
							<?php
							$arrTerms = $objEvent->getEventCategories();
							if( !empty( $arrTerms ) ) {
								foreach( $arrTerms as $intTermID => $strTermName ) {
									printf(
										'<option value="%1$s"%3$s>%2$s</option>',
										$intTermID, $strTermName,
										selected( in_array( $intTermID, wp_get_object_terms( $edit->ID,  $objEvent->getEventCategoryName(), array( 'fields' => 'ids' ) ) ), true, false )
									);
								}
							} ?>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-xs-12 col-md-2"><label class="col-form-label box-title"><?php esc_html_e('Description', 'jvfrmtd') ?></label></div>
					<div class="col-10"><textarea class="form-control" name="txtDescription" rows="8" id="input7" style="height:auto;" required><?php echo esc_textarea( $edit->post_content ); ?></textarea></div>
				</div>

				<div class="form-group row">
					<div class="col-xs-12 col-md-2"><label class="col-form-label box-title"><?php esc_html_e('Image', 'jvfrmtd') ?></label></div>
					<div class="col-10">
						<div class="form-option lava-multi-uploader-wrap">
							<div id="lava-multi-uploader" class="clearfix">
								<input type="hidden" name="gallery_image_ids" value="">
								<?php
								$arrEventDetails = array_filter( (array) get_post_meta( $edit->ID, 'detail_images', true ) );

								$intFeaturedID = get_post_thumbnail_id( $edit->ID );
								if( !empty( $arrEventDetails ) ){
									foreach( $arrEventDetails as $intDetailImageID ){
										$obj_prop_image = get_post( $intDetailImageID );
										if( ! ( $obj_prop_image instanceof WP_Post ) ) {
											continue;
										}
										$is_featured_image =  ( $intFeaturedID == $intDetailImageID );
										$featured_icon = ( $is_featured_image ) ? 'fa-star' : 'fa-star-o';
										echo '<div class="gallery-thumb">';
										echo '<img src="'.$obj_prop_image->guid.'" alt="'.$obj_prop_image->post_title.'" />';
										echo '<a class="remove-image" data-event-id="'.$edit->ID.'" data-attachment-id="' . $intDetailImageID . '" href="#remove-image" ><i class="fa fa-trash-o"></i></a>';
										echo '<a class="mark-featured" data-event-id="'.$edit->ID.'" data-attachment-id="' . $intDetailImageID . '" href="#mark-featured" ><i class="fa '. $featured_icon . '"></i></a>';
										echo '<span class="loader"><i class="fa fa-spinner fa-spin"></i></span>';
										echo '<input type="hidden" class="gallery-image-id" name="gallery_image_ids[]" value="' . $intDetailImageID . '"/>';
										if ( $is_featured_image ) {
											echo '<input type="hidden" class="featured-img-id" name="featured_image_id" value="' . $intDetailImageID . '"/>';
										}
										echo '</div>';
									}
								}
								?>
							</div>

							<div id="lava-multi-uploader-drag-drop">
								<div class="drag-drop-msg"><i class="fa fa-cloud-upload"></i>&nbsp;&nbsp;<?php _e('Drag and drop images here','jvfrmtd'); ?></div>
								<div class="drag-or"><?php _e('or','jvfrmtd'); ?></div>
								<div class="drag-btn">
									<button id="select-images"  class="real-btn">
										<?php _e('Select Images','jvfrmtd'); ?>
									</button>
								</div>
							</div>
							<div class="field-description">
								<?php _e( 'Minimum 770 x 386 (px).', 'jvfrmtd' ); ?><br/>
							</div>
							<div id="errors-log"></div>
						</div>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12 text-center">
						<button type="submit" class="btn btn-primary btn-block">
							<i class="fa fa-check"></i>
							<?php esc_html_e( "Save", 'jvfrmtd' ); ?>
						</button>
					</div>
				</div>

				<input type="hidden" name="action" value="<?php echo $objEvent->getEventAction();?>">
				<input type="hidden" name="post_id" value="<?php echo $edit->ID; ?>">

			</form>
		</div>


			</div><!-- /.card-block -->
		</div><!-- /.card -->
	</div> <!-- col-md-12 -->
</div><!--/row-->