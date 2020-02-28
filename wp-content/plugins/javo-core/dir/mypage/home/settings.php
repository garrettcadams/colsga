<?php
$objWPQuery = new WP_Query();
$arrAllCoreItems = $arrAllCoreEvents = Array();
$arrAllCoreItems = $objWPQuery->query(
	Array(
		'author' => bp_loggedin_user_id(),
		'post_type' => 'lv_listing',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	)
);

$isEventActivate = function_exists( 'Lava_EventConnector' ) && function_exists( 'tribe_get_events' );
if( $isEventActivate ) {
	$arrAllCoreEvents = tribe_get_events(
		Array(
			'author' => bp_loggedin_user_id(),
			'post_status' => 'publish',
			'posts_per_page' => 10,
			'paged' => max( 1, get_query_var( 'paged' ) ),
		)
	);
} ?>
	<!-- Content Start -->
	<div class="jv-user-content">
			<form method="post" class="chart-setting">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><?php esc_html_e( "Select chart items", 'jvfrmtd' ); ?></h4>
						<small><?php printf( esc_html__( "Limited amount of chart item: %s" ), 5 ); ?></small>
					</div>
					<div class="card-block">
						<input type="hidden" name="jvbpd_mypage_chart_items">

						<h3><?php esc_html_e( "Listing", 'jvfrmtd' ); ?></h3>
						<?php
						$objResult = apply_filters( 'jvbpd_mypage_rejvbpd_setting_result', false );
						if( is_wp_error( $objResult ) ) {
							printf( '<div class="alert alert-danger"><span class="jv-icon2-info2"></span> <strong>%s</div>', $objResult->get_error_message() );
						}
						$arrChartItems = array_filter( (array) get_user_meta( get_current_user_id(), '_mypage_chart_items', true ) );
						if( !empty( $arrAllCoreItems ) ) {
							foreach( $arrAllCoreItems as $objPost ) {
								printf(
									'<div>
										<label>
											<input type="checkbox" name="jvbpd_mypage_chart_items[]" value="%1$s"%3$s> %2$s
										</label>
									</div>',
									$objPost->ID,
									$objPost->post_title,
									checked( in_array( $objPost->ID, $arrChartItems ), true, false )
								);
							}
						} ?>

						<?php if( $isEventActivate ) { ?>

							<h3><?php esc_html_e( "Events", 'jvfrmtd' ); ?></h3>
							<?php
							$arrChartItems = array_filter( (array) get_user_meta( get_current_user_id(), '_mypage_chart_events', true ) );
							if( !empty( $arrAllCoreEvents ) ) {
								foreach( $arrAllCoreEvents as $objPost ) {
									printf(
										'<div>
											<label>
												<input type="checkbox" name="jvbpd_mypage_chart_events[]" value="%1$s"%3$s> %2$s
											</label>
										</div>',
										$objPost->ID,
										$objPost->post_title,
										checked( in_array( $objPost->ID, $arrChartItems ), true, false )
									);
								}
							}
						} ?>
						<button type="submit" class="btn btn-primary">
							<i class=" jv-icon2-check"></i>
							<?php esc_html_e( "Save", 'jvfrmtd' ); ?>
						</button>
					</div>
				</div><!-- /.card -->
			</form>
	</div>
	<!-- Content End -->