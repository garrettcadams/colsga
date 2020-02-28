<?php
/**
 * Type C - Dashboard
 * My Dashboard > Report
 *
 */
global $jvbpd_curUser;
require_once JVBPD_DSB_DIR . '/mypage-common-header.php';

$arrCharItems = array_filter( (array) get_user_meta( get_current_user_id(), '_mypage_chart_items', true ) );
get_header( 'mypage' ); ?>
	<!-- Content Start -->

	<div class="row">
		<div class="col-md-12">
			<div class="card chartjs">
				<?php
				if( class_exists( 'Post_Views_Counter' ) ) {
					jvbpd_layout()->load_template(
						'chart-template', Array(
							'jvbpd_aricle_args' => (object) Array(
								'label' => esc_html__( "Listing Views ( 6 Months )", 'jvfrmtd' ),
								'values' => $arrCharItems,
								'limit_month' => 6,
								'count_type' => 2,
								'graph_type' => 'line',
							),
						)
					);
				} ?>
			</div><!-- /.card -->
		</div> <!-- col-md-12 -->
	</div><!--/row-->

	<div class="row">
		<div class="col-md-12">
			<div class="card chartjs">
				<?php
				if( class_exists( 'Post_Views_Counter' ) ) {
					jvbpd_layout()->load_template(
						'chart-template', Array(
							'jvbpd_aricle_args' => (object) Array(
								'label' => esc_html__( "Listing Views ( 30 days )", 'jvfrmtd' ),
								'values' => $arrCharItems,
								'limit_month' => 30,
								'count_type' => 0,
								'graph_type' => 'bar',
							),
						)
					);
				} ?>
			</div><!-- /.card -->
		</div> <!-- col-md-12 -->
	</div><!--/row-->

	<!-- Content End -->
<?php get_footer( 'mypage' );