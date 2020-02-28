<?php
$jvbpd_multi_filters = Array(
	'listing_amenities' => (Array) $post->req_listing_amenities,
);

if( !empty( $jvbpd_multi_filters ) ) : foreach( $jvbpd_multi_filters as $filter => $currentvalue ) {
	?>
	<div class="text-left javo-map-box-advance-term row">
		<div class="row jv-advanced-fields amenities-filter-area">
			<span><?php esc_html_e( "There is no amenities/features in this category", 'jvfrmtd' ); ?></span>
		</div><!-- /.col-md-9 -->
		<div class="opener" data-toggle="collapse" data-target=".javo-map-box-advance-term">
			<div class="opener-inner">
				<?php esc_html_e( "More amenities", 'jvfrmtd' ); ?>
				<i class="fa fa-caret-down"></i>
			</div>
		</div>
	</div>
	<?php
} endif;