<?php
if( false === ( $arrLocationTerms = get_transient( 'jvbpd_map_filter_location' ) ) ) {
	$arrLocationTerms = apply_filters('lava_get_selbox_child_term_lists', 'listing_location', null, 'select', $post->req_listing_location, 0, 0, "-" );
	set_transient( 'jvbpd_map_filter_location', $arrLocationTerms );
} ?>
<div class="row text-left javo-map-box-contract-type">
	<div class="col-md-3 javo-map-box-title javo-map-box-title">
		<?php esc_html_e( "Location", 'jvfrmtd' ); ?>
	</div><!-- /.col-md-3 -->
	<div class="col-md-9 javo-map-box-field">
		<select name="map_filter[listing_location]" class="form-control javo-selectize-option" data-tax="listing_location" data-metakey="listing_location"  data-name="<?php esc_html_e( "Location", 'jvfrmtd' ); ?>">
			<option value=""><?php esc_html_e( "Any Location", 'jvfrmtd' ); ?></option>
			<?php echo $arrLocationTerms;?>
		</select>
	</div><!-- /.col-md-9 -->
</div><!-- /.row -->