<?php
$objEvent = class_exists( 'jvbpd_core_events' ) ? new jvbpd_core_events : false;
$isParentSinglePage = isset( $jvbpd_events_list_args[ 'is_single' ] ) && true === $jvbpd_events_list_args[ 'is_single' ];
$lava_user_posts = Array();
if( function_exists( 'tribe_get_events' ) ) {
	$arrEventListQuery = Array(
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'paged' => max( 1, get_query_var( 'paged' ) ),
	);
	if( ! $isParentSinglePage ) {
		$arrEventListQuery[ 'author' ] = bp_displayed_user_id();
	}else{
		$arrEventListQuery[ 'start_date' ] = date( 'Y-m-d' );
		$arrEventListQuery[ 'meta_query' ] = Array(
			Array(
				'key' => '_EventHideFromUpcoming',
				'compare' => 'NOT EXISTS',
				'value' => '',
			)
		);
	}
	if( isset( $jvbpd_events_list_args[ 'post_parent' ] ) && 0 < intVal( $jvbpd_events_list_args[ 'post_parent' ] ) ) {
		$arrEventListQuery[ 'post_parent' ] = $jvbpd_events_list_args[ 'post_parent' ];
	}
	$lava_user_posts = tribe_get_events( $arrEventListQuery );
}

$arrDashboardTabs = Array(
	'title' => Array(
		'label' => esc_html__( "Title", 'Lavacode' ),
	),
	'posted_date' => Array(
		'label' => esc_html__( "Posted Date", 'Lavacode' ),
	),
	'parent' => Array(
		'label' => esc_html__( "Parent", 'Lavacode' ),
	),
	'post_status' => Array(
		'label' => esc_html__( "Status", 'Lavacode' ),
	),
);

if( $objEvent->hasMessage() ) {
	echo $objEvent->outputMessage();
}
/**
if( $lava_user_posts->have_posts() ) {
	while( $lava_user_posts->have_posts() ) {*/
if( !empty( $lava_user_posts ) ) {
	if( isset( $jvbpd_events_list_args[ 'before' ] ) ) {
		echo $jvbpd_events_list_args[ 'before' ];
	}
	printf( '<ul class="mypage-my-events-wrap list-group list-group-flush" data-delete-comment="%s">', esc_html__( "Do you want to delete this item?", 'jvfrmtd' ) );
	foreach( $lava_user_posts as $lava_user_event ){
		//$lava_user_posts->the_post(); ?>
		<li class="list-group-item event-<?php echo $lava_user_event->ID; ?>" data-id="<?php echo $lava_user_event->ID; ?>">

		<?php
		if( !empty( $arrDashboardTabs ) ) { foreach( $arrDashboardTabs as $strKey => $arrMeta ) {
			switch( $strKey ) {
			case 'title' : ?>
			<div class="listing-thumb">
				<?php
				printf(
					'<a href="%1$s">%2$s</a>',
					get_permalink( $lava_user_event->ID ),
					get_the_post_thumbnail( $lava_user_event->ID, Array( 50, 50 ), Array( 'class' => 'img-circle' ) )
				); ?>
			</div>

			<div class="listing-content">
				<h5 class="title"><a href="<?php echo get_permalink( $lava_user_event->ID ); ?>" class="title" target="_blank"><?php echo $lava_user_event->post_title; ?></a></h5>
				<span class="meta-taxonomies"><i class="icon-folder"></i><?php echo join( ', ', wp_get_object_terms( $lava_user_event->ID, $objEvent->getEventCategoryName(), Array( 'fields' => 'names' ) ) ); ?></span>
					<?php break;
					case 'posted_date' :
						echo '<span class="time date"><i class="icon-calender"></i>'. date_i18n( get_option( 'date_format' ), strtotime( $lava_user_event->EventStartDate ) ) ;
						echo ' ~ '. date_i18n( get_option( 'date_format' ), strtotime( $lava_user_event->EventEndDate ) ) .'</span>';
						break;
					case 'parent' :
						$objParent = get_post( $lava_user_event->post_parent );
						if( ( $objParent instanceof WP_Post ) && ! $isParentSinglePage ) {
							printf(
								'<a href="%1$s">%2$s</a> ',
								get_permalink( $lava_user_event->post_parent ),
								$objParent->post_title
							);
						}
						break;
					case 'post_status' :
						$jv_this_post_status = $lava_user_event->post_status;
						echo '<span class="post-status label label-rounded label-info">'.  (  $jv_this_post_status == 'publish' ? esc_html__( 'PUBLISH','Lavacode' ) : esc_html__( 'PENDING','Lavacode' ) )  .'</span>';
						break;
				} // switch
			} ?>
			</div><!-- listing-content -->

			<div class="listing-action btn-box">
				<?php if( get_current_user_id() == $lava_user_event->post_author && ! $isParentSinglePage ) : ?>
					<div class="lava-action text-right">
						<a href="<?php echo esc_url( add_query_arg( Array( 'edit' => $lava_user_event->ID ), bp_displayed_user_domain() . 'events/add/' ) ); ?> " class="edit action">
							<?php _e( "Edit", 'Lavacode' ); ?>
						</a>
						<a href="javascript:" class="remove action">
							<?php _e( "Remove", 'Lavacode' ); ?>
						</a>
						<?php
						do_action(
							"lava_" . jvbpdCore()->getSlug()  . "_dashboard_actions_after"
							, get_the_ID()
							, lava_directory_manager_get_option( 'page_add_' . jvbpdCore()->getSlug() )
						) ; ?>
					</div><!-- lava-action -->
				<?php endif; ?>
			</div><!-- listing-action -->
		</li><!-- listing-block-body -->
		<?php
		} // if empty check
	} //while
	echo '</ul>';
	if( isset( $jvbpd_events_list_args[ 'after' ] ) ) {
		echo $jvbpd_events_list_args[ 'after' ];
	}
} //if
else{

	if( ! $isParentSinglePage ) {
		printf(
			'<ul class="mypage-my-events-wrap list-group list-group-flush"><li class="list-group-item text-center">%s, <a href="%s">%s</a></li></ul>',
			esc_html__( "There is no data", 'jvfrmtd' ),
			bp_displayed_user_domain() . 'events/add/',
			esc_html__( "Add new Event?", 'jvfrmtd' )
		);
	}
} ?>

<form method="post" class="hidden delete-event-form">
	<input type="hidden" name="event_id" value="">
	<input type="hidden" name="action" value="<?php echo $objEvent->getEventAction( 'delete' ); ?>">
</form>
<div class="hidden delete-event-loading">
	<i class="fa fa-cog fa-spin fa-3x"></i>
</div>