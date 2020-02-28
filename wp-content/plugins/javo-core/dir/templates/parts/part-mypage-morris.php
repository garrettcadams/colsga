<?php
if( ! isset( $jvbpd_aricle_args ) ) {
	die;
}

$wpdb = $GLOBALS[ 'wpdb' ]; ?>
<div class="card-header"><h4 class="card-title"><?php echo esc_attr( $jvbpd_aricle_args->label ); ?></h4></div>
<div class="card-block">
<ul class="list-inline text-right">
	<?php
	$arrChartData = Array();
	if( ! empty( $jvbpd_aricle_args->values ) ) {
		$intColorKey = 0;
		$intLimitDate = date( 'Ym', strtotime( '-6 month' ) );
		$arrColors = Array(
			'#00bfc7', '#fb9678', '#9675ce', '#f0f0ff'
		);
		foreach( $jvbpd_aricle_args->values as $intPostID ){
			/**
			 *
			 * Type ID / Description
			 * 0 // day like 20140324
			 * 1 // week like 201439
			 * 2 // month like 201405
			 * 3 // year like 2014
			 * 4 // total views  */
			$objPost = get_post( $intPostID );
			$arrResultChartData = $wpdb->get_results( $wpdb->prepare( "select period as date, count from {$wpdb->prefix}post_views where 1=1 and id=%s and type=%s order by date", $objPost->ID, '2' ) );

			$arrChartData[ $objPost->ID ][ 'color' ] = $arrColors[ $intColorKey ];
			$arrChartData[ $objPost->ID ][ 'title' ] = $objPost->post_title;

			if( !empty( $arrResultChartData ) ) {
				foreach( $arrResultChartData as $objChartData ) {
					if( $intLimitDate < intVal( $objChartData->date ) ) {
						$arrChartData[ $objPost->ID ][ 'values' ][] = Array(
							'period' => $objChartData->date,
							'count' => $objChartData->count,
						);
					}
				}
			}
			printf( '<li><h5><i class="fa fa-circle m-r-5" style="color:%1$s"></i> %2$s</h5></li>', $arrColors[ $intColorKey ], $objPost->post_title );
			$intColorKey++;
		}
	} ?>
</ul>
</div> <!-- card-block -->
<div id="morris-area-chart" data-values='<?php echo json_encode( $arrChartData ); ?>'></div>