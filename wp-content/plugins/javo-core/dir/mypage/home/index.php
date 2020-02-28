<div class="block-status-wrap card-columns">
	<?php
	foreach(
		Array(
			(object) Array(
				'icon' => 'icon jvic-pin-2',
				'label' => esc_html__( "My Published Listings", 'jvfrmtd' ),
				'url' => trailingslashit(bp_displayed_user_domain() . 'listings'),
				'count' => intVal( jvbpdCore()->var_instance->getUserListingCount( jvbpd_getDashboardUser()->ID, Array( 'publish' ) ) ),
			),
			(object) Array(
				'icon' => 'icon jvic-pause-2',
				'label' => esc_html__( "My Pending Listings", 'jvfrmtd' ),
				'url' => trailingslashit(bp_displayed_user_domain() . 'listings/pending'),
				'count' => intVal( jvbpdCore()->var_instance->getUserListingCount( jvbpd_getDashboardUser()->ID, Array( 'pending' ) ) ),
			),
			(object) Array(
				'icon' => 'icon jvic-trash-4',
				'label' => esc_html__( "My Expired Listings", 'jvfrmtd' ),
				'url' => trailingslashit(bp_displayed_user_domain() . 'listings/expired'),
				'count' => intVal( jvbpdCore()->var_instance->getUserListingCount( jvbpd_getDashboardUser()->ID, Array( 'expire' ) ) ),
			),
			(object) Array(
				'icon' => 'icon jvic-calendar-4',
				'label' => esc_html__( "My Events", 'jvfrmtd' ),
				'url' => trailingslashit(bp_displayed_user_domain() . 'events'),
				'count' => intVal( jvbpdCore()->var_instance->getUserEventsCount( jvbpd_getDashboardUser()->ID ) ),
				'visible' => function_exists( 'Lava_EventConnector' ) && function_exists( 'tribe_get_events' )
			),
		) as $objItem
	) {
		if( isset( $objItem->visible ) && false === $objItem->visible ) {
			continue;
		}
		printf(
			'<div class="card block-status text-center"><div class="card-block"><a href="%4$s"><h4 class="card-title">%1$s</h4>
			<p><span class="counter">%2$s</span></p></a><div class="shadow-icon"><i class="%3$s"></i></div></div></div>',
			$objItem->label, $objItem->count, $objItem->icon, $objItem->url
		);

	} ?>
</div>

<div class="dashboard-home card-columns">
	<?php if( class_exists( 'Post_Views_Counter' ) ) { ?>
		<!-- /.row -->
		<div class="chartjs">
			<div class="card mb-12">
				<?php
				if( class_exists( 'Post_Views_Counter' ) ) {
					$arrCharItems = array_filter( (array) get_user_meta( get_current_user_id(), '_mypage_chart_items', true ) );
					jvbpdCore()->template_instance->load_template(
						'../dir/templates/parts/part-mypage-chart', '.php', Array(
							'jvbpd_aricle_args' => (object) Array(
								'label' => esc_html__( "Listing Views ( 6 Months )", 'jvfrmtd' ),
								'values' => $arrCharItems,
								'limit_month' => 6,
								'count_type' => 2,
								'graph_type' => 'line',
							),
						), false
					);
				} ?>
			</div>
			<div class="card mb-12">
				<?php
				if( function_exists( 'Lava_EventConnector' ) ) {
					$arrCharItems = array_filter( (array) get_user_meta( get_current_user_id(), '_mypage_chart_events', true ) );
					jvbpdCore()->template_instance->load_template(
						'../dir/templates/parts/part-mypage-chart', '.php', Array(
							'jvbpd_aricle_args' => (object) Array(
								'label' => esc_html__( "Event Views ( 6 Months )", 'javospot' ),
								'values' =>$arrCharItems,
								'limit_month' => 6,
								'count_type' => 2,
								'graph_type' => 'line',
							),
						), false
					);
				} ?>
			</div>
		</div>
	<?php } ?>
	<div class="review">
		<div class="card mb-12 user-favorites">
			<div class="card-header">
				<h4 class="card-title"><?php esc_html_e( "Recent Favorites", 'jvfrmtd' ); ?></h4>
			</div>
			<ul class="list-group list-group-flush">
				<?php
				jvbpdCore()->template_instance->load_template( '../dir/mypage/favorites/favorites-content' ); ?>
			</ul>
		</div> <!-- .card -->
		<div class="card mb-12 user-reviews">
			<div class="card-header">
				<h4 class="card-title"><?php esc_html_e( "Recent Reviews", 'jvfrmtd' ); ?></h4>
			</div>
			<ul class="list-group list-group-flush">
				<?php
				jvbpdCore()->template_instance->load_template( '../dir/mypage/reviews/received-content' ); ?>
			</ul>
		</div> <!-- .card -->
	</div>

</div> <!-- card-columns -->