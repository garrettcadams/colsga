<?php
if( ! isset( $jvbpd_aricle_args ) ) {
	die;
}

$wpdb = $GLOBALS[ 'wpdb' ]; ?>
<div class="card-header">
	<h4 class="card-title">
		<?php echo esc_html( $jvbpd_aricle_args->label ); ?>
		<a href="<?php echo jvbpd_getUserPage( jvbpd_getDashboardUser()->ID, 'home/settings' ); ?>" class="btn btn-primary btn-sm pull-right hidden-xs hidden-sm"><?php esc_html_e( "Setting", 'jvfrmtd' ); ?></a>
	</h4>
</div>
<div class="card-block">
		<?php
		$arrChartData = Array();
		$limit_month = $jvbpd_aricle_args->limit_month;
		$intCountType = $jvbpd_aricle_args->count_type;
		if( ! empty( $jvbpd_aricle_args->values ) ) {
			$intColorKey = 0;
			$strDateUnit = false;
			switch( intVal( $intCountType ) ) {
				case 0: $strDateUnit = 'day'; break;
				case 2: $strDateUnit = 'month'; break;
			}
			$intLimitDate = date( 'Ym', strtotime( "-{$limit_month} {$strDateUnit}" ) );
			$arrColors = Array(
				'rgba(0, 191, 199, 0.3)', 'rgba(255, 0, 0, 0.3)', 'rgba(150, 117, 206, 0.3)', 'rgba(237, 255, 0, 0.3)'
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
				if( ! $objPost ) {
					continue;
				}
				$arrResultChartData = $wpdb->get_results( $wpdb->prepare( "select period as date, count from {$wpdb->prefix}post_views where 1=1 and id=%s and type=%s order by date", $objPost->ID, $intCountType ) );

				$arrChartData[ $objPost->ID ][ 'color' ] = $arrColors[ ( $intColorKey % sizeof( $arrColors ) ) ];
				$arrChartData[ $objPost->ID ][ 'title' ] = $objPost->post_title;

				if( !empty( $arrResultChartData ) ) {
					foreach( $arrResultChartData as $objChartData ) {
						if( $intLimitDate <= intVal( $objChartData->date ) ) {
							$arrChartData[ $objPost->ID ][ 'values' ][] = Array(
								'period' => $objChartData->date,
								'count' => $objChartData->count,
							);
						}
					}
				}
				$intColorKey++;
			}


		} ?>
		<canvas class="bp-mydahsobard-report-chart" data-x="<?php esc_html_e( "Month", 'jvfrmtd' ); ?>" data-y="<?php esc_html_e( "Count", 'jvfrmtd' ); ?>" data-limit="<?php echo esc_attr( $limit_month ); ?>" data-values='<?php echo json_encode( $arrChartData, JSON_HEX_APOS|JSON_HEX_QUOT ); ?>' data-type="<?php echo esc_attr( $intCountType ); ?>" data-graph="<?php echo esc_attr( $jvbpd_aricle_args->graph_type );?>"></canvas>
</div> <!-- card-block -->
