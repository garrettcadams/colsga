<?php
if( false === ( $arrCategoryTerms = get_transient( 'jvbpd_map_filter_category' ) ) ) {
	$arrCategoryTerms =	apply_filters('lava_get_selbox_child_term_lists', 'listing_category', null, 'select', $post->req_listing_category, 0, 0, "-" );
	set_transient( 'jvbpd_map_filter_category', $arrCategoryTerms );
} ?>
<div class="row text-left javo-map-box-category">

	<div class="col-md-3 javo-map-box-title">
		<?php esc_html_e( "Category", 'jvfrmtd' ); ?>
	</div><!-- /.col-md-3 -->
	<div class="col-md-9 javo-map-box-field">
		<select name="map_filter[listing_category]" class="form-control javo-selectize-option" data-tax="listing_category" data-metakey="listing_category" data-name="<?php esc_html_e( "Category", 'jvfrmtd' ); ?>">
			<option value=""><?php esc_html_e( "Any Categories", 'jvfrmtd' ); ?></option>
			<?php echo $arrCategoryTerms;?>
		</select>
	</div><!-- /.col-md-9 -->

</div><!-- /.row -->