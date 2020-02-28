<?php
if( !isset( $lavaDashBoardArgs ) ) {
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


$lava_user_posts = new WP_Query(
	Array(
		'post_type' => $this->post_type,
		'author' => ( false !== $lavaDashBoardArgs[ 'author' ] ? $lavaDashBoardArgs[ 'author' ] : get_current_user_id() ),
		'post_status' => $strListingStatus,
		'posts_per_page' => ( false !== $lavaDashBoardArgs[ 'count' ] ? $lavaDashBoardArgs[ 'count' ] : 10 ),
		'paged' => max( 1, get_query_var( 'paged' ) ),
	)
);

$arrDashboardTabs = apply_filters(
	'lava_' . $this->post_type . '_dashboard_tabs',
	Array(
		'thumb' => Array(
			'label' => '',
		),
		'title' => Array(
			'label' => esc_html__( "Title", 'lvbp-bp-post' ),
		),
		'posted_date' => Array(
			'label' => esc_html__( "Posted Date", 'lvbp-bp-post' ),
		),
		'post_status' => Array(
			'label' => esc_html__( "Status", 'lvbp-bp-post' ),
		),
	)
); ?>

<div class="lava-dir-shortcode-dashboard-wrap lava-my-item-list">

	<?php
	if( !empty( $lavaDashBoardArgs[ 'title' ] ) ) {
		printf( '<h2>%s</h2>', $lavaDashBoardArgs[ 'title' ] );
	}

	if( !empty( $GLOBALS[ 'lava_dashboard_message' ] ) ) {
		printf( '<div style="background-color:#000000; color:#ffffff; padding: 5px 10px; margin:10px 0;">%s</div>', $GLOBALS[ 'lava_dashboard_message' ] );
	} ?>

	<table cellPadding="0" cellSpacing=="0" width="100%">
		<thead>
			<tr>
			<?php
			if( !empty( $arrDashboardTabs ) ) { foreach( $arrDashboardTabs as $arrMeta ) {
				printf( '<th class="text-center">%s</th>', $arrMeta[ 'label' ] );
			} }; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			if( $lava_user_posts->have_posts() ) {
				while( $lava_user_posts->have_posts() ) {
					$lava_user_posts->the_post(); ?>
					<tr class="item itemid-<?php the_ID(); ?> status-<?php echo get_post_status(); ?>">
						<?php if( !empty( $arrDashboardTabs ) ) { foreach( $arrDashboardTabs as $strKey => $arrMeta ){ ?>
						<td class="text-center">

							<?php
							switch( $strKey ) {
								case 'thumb' :
									?>
									<a href="<?php the_permalink(); ?>">

										<?php
										if( has_post_thumbnail() ) {
											the_post_thumbnail( Array( 48, 48 ), Array( 'class' => 'lava-thumbnail' ) );
										}else{
											printf(
												'<img src="%s" style="width:40px; height:40px;" class="lava-thumbnail">',
												lava_bpp()->admin->getNoImage()
											);
										} ?>
									</a>
									<?php
									break;

								case 'title' :
									?>
									<div class="lava-title text-left">
										<a href="<?php the_permalink(); ?>" class="title">
											<?php the_title(); ?>
										</a>
									</div>
									<?php if( get_current_user_id() == get_the_author_meta( 'ID' ) ) : ?>
										<div class="lava-action text-left">

											<?php
											do_action(
												"lava_{$this->post_type}_dashboard_actions_before"
												, get_the_ID()
												, lava_bpp_get_option( 'page_add_' . $this->post_type )
											) ; ?>

											<a href="<?php echo lava_bpp()->core->get_edit_link( get_the_ID() ); ?>" class="edit">
												<?php _e( "Edit", 'lvbp-bp-post' ); ?>
											</a>
											<a href="javascript:" data-action="delete" data-id="<?php the_ID();?>" class="remove">
												<?php _e( "Remove", 'lvbp-bp-post' ); ?>
											</a>

											<?php
											do_action(
												"lava_{$this->post_type}_dashboard_actions_after"
												, get_the_ID()
												, lava_bpp_get_option( 'page_add_' . $this->post_type )
											); ?>

										</div>
									<?php endif; ?>
								<?php break;
								case 'posted_date' :
									echo get_the_date();
									break;
								case 'post_status' :
									$jv_this_post_status = get_post_status();
									echo $jv_this_post_status == 'publish' ? _e( 'PUBLISH','lvbp-bp-post' ) : _e( 'PENDING','lvbp-bp-post' );
									break;
							}
							do_action( 'lava_' . $this->post_type . '_dashboard_tab_contents', $strKey, get_post() ); ?>
						</td>
						<?php } }; ?>
					</tr>
					<?php
				}
			}else{
				printf( '<tr><td align="center" colspan="4"><h3>%s</h3></td></tr>', esc_html__( "No Data", 'lvbp-bp-post' ) );
			}
			wp_reset_query();
			?>
		</tbody>
	</table>
	<p class="lava-pagination">
		<?php
		$big						= 999999999;
		echo paginate_links(
			Array(
				'base'			=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) )
				, 'format'		=> '?paged=%#%'
				, 'current'		=> max( 1, get_query_var('paged') )
				, 'total'			=> $lava_user_posts->max_num_pages
			)
		); ?>
	</p>
</div>