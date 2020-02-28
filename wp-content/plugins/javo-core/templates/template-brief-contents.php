<?php
if( ! isset( $jvbpd_post ) )
	die;
$tempPost = $GLOBALS[ 'post' ];
$GLOBALS[ 'post' ] = $post = get_post( $jvbpd_post );
$strFeaturedImageSrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'jvbpd-large' );
$isActivateReview = function_exists( 'lv_directoryReview' );
$isActivateFavorite = function_exists( 'lv_directory_favorite' ); ?>

<div class="jv-brief-info-wrap" style="position:relative; left: auto;">
	<div class="lava-mhome-item-info-loading-cover hidden"></div>
	<div class="jv-brief-info-inner">
		<div class="row">
			<div class=" col-md-12 jv-brief-info-header-img-wrap" style="background-repeat:no-repeat;background-size:cover;background-image:url('<?php echo $strFeaturedImageSrc[0]; ?>');">
				<a href="javascript:" data-dismiss="modal"><i class="fa fa-times jv-brief-info-close-btn"></i></a>
				<div id="jv-brief-info-author-thumb"><div class="lava-thb" style="background-image:url();"></div></div>
				<div class="jv-brief-info-heading-des">
					<div class="jv-brief-info-heading-cates"><?php echo function_exists( 'lava_directory_featured_terms' ) ? lava_directory_featured_terms( 'listing_category', $post->ID, false ) . ', ' . lava_directory_featured_terms( 'listing_location', $post->ID, false )  : false;  ?></div>
					<div class="jv-brief-info-heading-title"><?php esc_html_e( $post->post_title, 'jvfrmtd' ); ?></div>
					<?php
					if( $isActivateReview ){
						$ratingScore			= floatVal( get_post_meta( $post->ID, 'rating_average', true ) );
						$ratingPercentage	= floatVal( ( $ratingScore / 5 ) * 100 ) . '%';
						printf(
							'<div class="jv-brief-info-heading-reviews javo-shortcode inline-block"><div class="module"><div class="meta-rating-wrap"><div class="meta-rating" style="width:%1$s"></div></div></div></div>',
							// lv_directoryReview()->core->fa_get()
							$ratingPercentage
						);
					}
					if( $isActivateFavorite ){
						echo '<div class="jv-brief-info-heading-favorites inline-block" style="margin:0 10px;">';
						lv_directory_favorite()->core->appned_favorite( $post );
						echo '</div>';
					} ?>
				</div>
				<div class="meta-rating-wrap"><div class="meta-rating" style="width:0%;"></div></div>
				<div class="jv-brief-info-header-bg-overlay"></div>
			</div><!--/jv-brief-info-header-img-wrap-->
		</div><!-- /.row -->

		<div class="jv-brief-content-wrap">
			<div class="row" id="jv-brief-info-tabs-navi affix"  data-spy="affix" data-offset-top="197">
				<div class="col-md-12 jv-brief-info-tabs-wrap">
					<div class="header-tabs">
						<ul class="nav tabs-container">
							<a class="detail-tab scroll-spy force-active col-md-3" data-href="#detail-section" href="#detail-section">
								<h4 class="text-center"><?php esc_html_e( 'Detail', 'jvfrmtd' ); ?></h4>
							</a>
							<a class="detail-tab scroll-spy col-md-3" data-href="#amenities-section" href="#amenities-section">
								<h4 class="text-center"><?php esc_html_e( 'Amenities', 'jvfrmtd' ); ?></h4>
							</a>
							<a class="detail-tab scroll-spy col-md-3" data-href="#des-section" href="#des-section">
								<h4 class="text-center"><?php esc_html_e( 'Description', 'jvfrmtd' ); ?></h4>
							</a>
							<a class="detail-tab scroll-spy col-md-3" data-href="#location-section" href="#location-section">
								<h4 class="text-center"><?php esc_html_e( 'Location', 'jvfrmtd' ); ?></h4>
							</a>
						</ul><!-- /.tabs-container -->
					</div><!-- /.header-tabs -->
				</div><!--/jv-brief-info-tabs-wrap-->
			</div><!-- /.row -->

			<div class="jv-brief-info-content-wrap">
				<div class="jv-brief-info-content-section" id="detail-section">
					<div class="row jv-brief-info-content-section-wrap">
						<div class="col-md-12 heading-wrap">
							<h2><?php esc_html_e( 'Detail', 'jvfrmtd' ); ?></h2>
						</div><!-- /.col-md-12 -->
						<div class="col-md-12 panel jv-brief-info-content-section-panel-wrap">
							<div class="panel-body">
								<div class="row summary_items">

									<?php
									$arrSummaryItems = apply_filters(
										'jvbpd_brief_info_summary_items',
										Array(
											'website' => Array(
												'label' => esc_html__( 'Website', 'jvfrmtd' ),
												'value' => get_post_meta( $post->ID, '_website', true ),
											),
											'email' => Array(
												'label' => esc_html__( 'Email', 'jvfrmtd' ),
												'value' => get_post_meta( $post->ID, '_email', true ),
											),
											'phone' => Array(
												'label' => esc_html__( 'Phone', 'jvfrmtd' ),
												'value' => get_post_meta( $post->ID, '_phone1', true ),
											),
											'address' => Array(
												'label' => esc_html__( 'Address', 'jvfrmtd' ),
												'value' => get_post_meta( $post->ID, '_address', true ),
											),
										), $post->ID
									); ?>

									<div class="col-md-12 col-xs-12">
										<?php
										foreach( $arrSummaryItems as $summary => $summeryMeta ) {
											printf(
												'<div class="row"><div class="col-md-5 col-xs-12"><span>%2$s</span></div><div class="col-md-7 col-xs-12"><span><span class="item-%1$s">%3$s</span></span></div></div>',
												$summary,
												$summeryMeta[ 'label' ],
												$summeryMeta[ 'value' ]
											);
										} ?>
									</div>
								</div><!--/.summary_items-->
							</div><!--/.panel-body-->
						</div> <!-- jv-brief-info-content-section-panel-wrap -->
					</div><!-- /.jv-brief-info-content-section-wrap -->
				</div> <!-- /. jv-brief-info-content-section #detail-section -->


				<div class="jv-brief-info-content-section" id="amenities-section">
					<div class="row jv-brief-info-content-section-wrap">
						<div class="col-md-12 heading-wrap">
							<h2><?php esc_html_e( 'Amenities', 'jvfrmtd' ); ?></h2>
						</div><!-- /.col-md-12 -->
						<div class="col-md-12 panel jv-brief-info-content-section-panel-wrap">
							<div class="panel-body">
								<div class="row summary_items">
									<div class="col-md-12 col-xs-12">
										<?php lava_directory_amenities( $post->ID, Array( 'showall' => false ) ); ?>
									</div>
								</div><!--/.summary_items-->
							</div><!--/.panel-body-->
						</div> <!-- jv-brief-info-content-section-panel-wrap -->
					</div><!-- /.jv-brief-info-content-section-wrap -->
				</div> <!-- /. jv-brief-info-content-section #detail-section -->

				<div class="jv-brief-info-content-section" id="des-section">
					<div class="row jv-brief-info-content-section-wrap">
						<div class="col-md-12 heading-wrap">
							<h2><?php esc_html_e( 'Description', 'jvfrmtd' ); ?></h2>
						</div><!-- /.col-md-12 -->
						<div class="col-md-12 panel jv-brief-info-content-section-panel-wrap">
							<div class="panel-body">
								<div class="row summary_items">
									<div class="col-md-12 col-xs-12">
										<?php echo wp_trim_words( $post->post_content, 20, '...' ); ?>
									</div>
								</div><!--/.summary_items-->
							</div><!--/.panel-body-->
						</div> <!-- jv-brief-info-content-section-panel-wrap -->
					</div><!-- /.jv-brief-info-content-section-wrap -->
				</div> <!-- /. jv-brief-info-content-section #detail-section -->

				<div class="jv-brief-info-content-section" id="location-section">
					<div class="row jv-brief-info-content-section-wrap">
						<div class="col-md-12 heading-wrap">
							<h2><?php esc_html_e( 'Location', 'jvfrmtd' ); ?></h2>
						</div><!-- /.col-md-12 -->
						<div class="col-md-12 panel jv-brief-info-content-section-panel-wrap">
							<div class="panel-body">
								<div class="preview-map-container" data-lat="<?php echo get_post_meta( $post->ID, 'lv_listing_lat', true ); ?>" data-lng="<?php echo get_post_meta( $post->ID, 'lv_listing_lng', true ); ?>"></div>
							</div><!--/.panel-body-->
						</div> <!-- jv-brief-info-content-section-panel-wrap -->
					</div><!-- /.jv-brief-info-content-section-wrap -->
				</div> <!-- /. jv-brief-info-content-section #detail-section -->

			</div><!-- /.jv-brief-info-content-wrap -->

		</div> <!-- .aaa -->
	</div><!-- /.jv-brief-info-inner -->
</div><!--/ .jv-brief-info-wrap -->
<?php
// Restore post object
$GLOBLAS[ 'post' ] = $tempPost;