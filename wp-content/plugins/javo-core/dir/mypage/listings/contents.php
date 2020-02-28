<?php
wp_localize_script(
	lava_directory()->enqueue->getHandleName( 'lava-dashboard.js' ),
	'lava_dir_dashboard_args',
	Array(
		'nonce' => wp_create_nonce( 'item_delete' ),
		'strings' => array(
			'strDeleteConfirm' => esc_html__( "Do you want to delete this item?", 'Lavacode' ),
		),
	)
);
wp_enqueue_script( lava_directory()->enqueue->getHandleName( 'lava-dashboard.js' ) );
if( ! isset( $lavaDashBoardArgs ) ) {
	die;
}

switch( $lavaDashBoardArgs[ 'type' ] ) {
	case 'publish' :
		$strListingStatus = Array( 'publish' );
		break;

	case 'pending' :
		$strListingStatus = Array( 'pending' );
		break;

	case 'all' : default :
		$strListingStatus = Array( 'pending', 'publish' );

}

$lavaGetPostsQuery = Array(
	'post_type' => 'lv_listing',
	'author' => bp_displayed_user_id(),
	'post_status' => $strListingStatus,
	'posts_per_page' => 10,
	'paged' => max( 1, get_query_var( 'paged' ) ),
);

if( isset( $lavaDashBoardArgs[ 'payment' ] ) && $lavaDashBoardArgs[ 'payment' ]  == 'expire' ) {
	$lavaGetPostsQuery[ 'meta_query' ][] =  Array(
		'type' => 'NUMERIC',
		'key' => 'lv_expire_day',
		'value' => current_time( 'timestamp' ),
		'compare' => '<=',
	);
}

$lava_user_posts = new WP_Query( $lavaGetPostsQuery );

$arrDashboardTabs = apply_filters(
	'lava_lv_listing_dashboard_tabs',
	Array(
		'title' => Array(
			'label' => esc_html__( "Title", 'jvfrmtd' ),
		),
		'posted_date' => Array(
			'label' => esc_html__( "Posted Date", 'jvfrmtd' ),
		),
		'post_status' => Array(
			'label' => esc_html__( "Status", 'jvfrmtd' ),
		),
	)
);

if( !empty( $GLOBALS[ 'lava_dashboard_message' ] ) ) {
	printf( '<div style="background-color:#000000; color:#ffffff; padding: 5px 10px; margin:10px 0;">%s</div>', $GLOBALS[ 'lava_dashboard_message' ] );
} ?>

<ul class="list-group list-group-flush lava-dir-shortcode-dashboard-wrap">

<?php
if( $lava_user_posts->have_posts() ) {
	while( $lava_user_posts->have_posts() ) {
		$lava_user_posts->the_post();
		?>
		<li class="list-group-item lava-directory-<?php the_ID(); ?>">

		<?php
		if( !empty( $arrDashboardTabs ) ) { foreach( $arrDashboardTabs as $strKey => $arrMeta ) {
			switch( $strKey ) {
			case 'title' : ?>
			<div class="listing-thumb">
				<a href="<?php the_permalink(); ?>" target="_blank">
					<?php
					if( has_post_thumbnail() ) {
						the_post_thumbnail( Array( 50, 50 ), Array( 'class' => 'rounded-circle' ) );
					} ?>
				</a>
			</div>

			<div class="listing-content">
				<h5 class="title"><a href="<?php the_permalink(); ?>" class="title" target="_blank"><?php the_title(); ?></a></h5>
				<span class="meta-taxonomies"><i class="icon-folder"></i><?php echo join( ', ', wp_get_object_terms( get_the_ID(), 'listing_category', Array( 'fields' => 'names' ) ) ); ?></span>
					<?php break;
					case 'posted_date' :
						echo '<span class="time date"><i class="icon-calender"></i>'. get_the_date() .'</span>';
						break;
					case 'post_status' :
						$jv_this_post_status = get_post_status();
						echo '<span class="post-status label label-rounded label-info">'.  (  $jv_this_post_status == 'publish' ? esc_html__( 'PUBLISH','jvfrmtd' ) : esc_html__( 'PENDING','jvfrmtd' ) )  .'</span>';
						break;
				} // switch
				do_action( 'lava_lv_listing_dashboard_tab_contents', $strKey, get_post());
			} ?>
			</div><!-- listing-content -->

			<div class="listing-action btn-box">
				<?php if( get_current_user_id() == get_the_author_meta( 'ID' ) ) : ?>
					<div class="lava-action text-right">
						<?php
						do_action(
							"lava_lv_listing_dashboard_actions_before"
							, get_the_ID()
							, lava_directory_manager_get_option( 'page_add_lv_listing' )
						) ; ?>
						<a href="<?php lava_directory_edit_page(); ?>" class="edit action">
							<?php _e( "Edit", 'jvfrmtd' ); ?>
						</a>
						<a href="javascript:" data-action="delete" data-id="<?php the_ID();?>" class="remove action">
							<?php _e( "Remove", 'jvfrmtd' ); ?>
						</a>
						<?php
						do_action(
							"lava_lv_listing_dashboard_actions_after"
							, get_the_ID()
							, lava_directory_manager_get_option( 'page_add_lv_listing' )
						) ; ?>
					</div><!-- lava-action -->
				<?php endif; ?>
			</div><!-- listing-action -->
		</li><!-- listing-block-body -->
		<?php
		}// if empty check
	} //while
}else{
	$strAddNewURL = NULL;
	$strNoDataFormat = '<li class="list-group-item text-center">%s</li>';
	if(bp_displayed_user_id() == bp_loggedin_user_id() || current_user_can('manage_options')) {
		$strAddnewString = esc_html__( "Add new Listing?", 'jvfrmtd' );
		if( function_exists( 'lava_directory_get_add_form_page' ) ) {
			$strNoDataFormat = '<li class="list-group-item text-center">%s, <a href="%s">%s</a></li>';
			$strAddNewURL = lava_directory_get_add_form_page();
		}
	}else{
		$strAddnewString = NULL;
	}
	printf( $strNoDataFormat, esc_html__( "There is no data", 'jvfrmtd' ), $strAddNewURL, $strAddnewString );
}//if
wp_reset_query(); ?>
	</ul>
 <div class="lava-pagination">
	<?php
	$big						= 999999999;
	/*
	echo paginate_links(
		Array(
			'base'			=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) )
			, 'format'		=> '?paged=%#%'
			, 'current'		=> max( 1, get_query_var('paged') )
			, 'total'			=> $lava_user_posts->max_num_pages
		)
	); */
	echo paginate_links( array(
		'base' =>  @add_query_arg('page','%#%'),
		'format' => '',
		'current' => max( 1, get_query_var('page') ),
		'total' => $lava_user_posts->max_num_pages
	) ); ?>
</div>