<?php
// Single page addon option
if( class_exists( 'Javo_Spot_Single_Addon' ) ){
	$single_addon_options = get_single_addon_options(get_the_ID());
} ?>
<?php
$arrAllowPostTypes		= apply_filters( 'jvbpd_single_post_types_array', Array( 'lv_listing' ) );
if( class_exists( 'lvDirectoryVideo_Render' ) ) {
	$objVideo = new lvDirectoryVideo_Render( get_post(), array(
		'width' => '100',
		'height' => '100',
		'unit' => '%',
	) );
	$is_has_video = $objVideo->hasVideo();
}else{
	$is_has_video = false;
}
if( class_exists( 'lvDirectory3DViewer_Render' ) ) {
	$obj3DViewer = new lvDirectory3DViewer_Render( get_post() );
	$is_has_3d = $obj3DViewer->viewer;
}else{
	$is_has_3d = false;
}
// Single page addon option
if( class_exists( 'Javo_Spot_Single_Addon' ) ){
	$single_addon_options = get_single_addon_options(get_the_ID());
	if($single_addon_options['background_transparent'] == 'disable'){
		$block_meta = 'extend-meta-block-wrap';
		if($single_addon_options['featured_height'] != '') $featured_height = 'style=height:'.$single_addon_options['featured_height'].'px;';
	}else{
		if($single_addon_options['featured_height'] != ''){
			$block_meta = '"style=height:auto;min-height:auto;';
			$featured_height = 'style=height:'.$single_addon_options['featured_height'].'px;';
		}
	}
}

$block_meta = 'extend-meta-block-wrap';
$featured_height = 'style=height:600px;';

// Right Side Navigation
$jvbpd_rs_navigation = jvbpd_single_navigation();
 ?>

 <div class="single-item-tab-feature-bg-wrap <?php echo isset($block_meta) ? $block_meta : ''; ?>">
	<div class="single-item-tab-bg" <?php echo isset($single_addon_options['background_transparent']) && $single_addon_options['background_transparent'] == 'disable' ? 'style="bottom:0 !important;"' : ''; ?>>
		<div class="container captions">
			<div class="header-inner <?php if(class_exists( 'Lava_Directory_Review' )) echo 'jv-header-rating'; ?>">
				<div class="item-bg-left pull-left text-left">
					<h1 class="uppercase">
						<?php
						$imgCompanyLogo = get_post_meta( get_the_ID(), '_logo', true );
						$imgCompanyLogoSrc = wp_get_attachment_image_url( $imgCompanyLogo );
						if( $imgCompanyLogoSrc ) {
							printf( '<div class="logo inline-block"><img src="%1$s" class="rounded-circle" width="50" height="50"></div>', $imgCompanyLogoSrc );
						} ?>

						<span class="jv-listing-title"><?php  echo get_the_title(); ?></span>
						<?php
						$strTagLine = get_post_meta( get_the_ID(), '_tagline', true );
						if( $strTagLine ) {
							printf( '<div class="tagline jv-listing-sub-title">%1$s</div>', $strTagLine );
						} ?>
						<a href="<?php echo jvbpd_getUserPage( get_the_author_meta( 'ID' ) ); ?>" class="header-avatar" style="display:none;">
							<?php echo get_avatar( get_the_author_meta( 'ID' ) ); ?>
						</a>
					</h1>
					<div class="jv-addons-meta-wrap">
						<?php echo apply_filters( 'jvbpd_' . get_post_type() . '_single_listing_rating', '' );?>
					</div>
					<div class="listing-des" style="display:none;">
						<?php
						$jvbpd_facebook_link = esc_html(get_post_meta( get_the_ID(), '_facebook_link', true ));
						$jvbpd_twitter_link = esc_html(get_post_meta( get_the_ID(), '_twitter_link', true ));
						$jvbpd_instagram_link = esc_html(get_post_meta( get_the_ID(), '_instagram_link', true ));
						$jvbpd_google_link = esc_html(get_post_meta( get_the_ID(), '_google_link', true ));
						$jvbpd_website_link = esc_html(get_post_meta( get_the_ID(), '_website', true ));

						if(!($jvbpd_facebook_link =='' && $jvbpd_twitter_link=='' && $jvbpd_instagram_link=='' && $jvbpd_google_link=='' && $jvbpd_website_link =='')){
						?>
						<div id="javo-item-social-section" data-jv-detail-nav>
							<div class="jvbpd_single_listing_social-wrap">
								<?php if ($jvbpd_facebook_link!=''){ ?>
									<a href="<?php echo $jvbpd_facebook_link;?>" target="_blank" class="jvbpd_single_listing_facebook javo-tooltip" data-original-title="<?php _e('Facebook','listopia'); ?>"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
								<?php }
								if ($jvbpd_twitter_link!=''){ ?>
									<a href="<?php echo $jvbpd_twitter_link;?>" target="_blank" class="jvbpd_single_listing_twitter javo-tooltip" data-original-title="<?php _e('Twitter','listopia'); ?>"><i class="fab fa-twitter" aria-hidden="true"></i></a>
								<?php }
								if ($jvbpd_instagram_link!=''){ ?>
									<a href="<?php echo $jvbpd_instagram_link;?>" target="_blank" class="jvbpd_single_listing_instagram javo-tooltip" data-original-title="<?php _e('Instagram','listopia'); ?>"><i class="fab fa-instagram" aria-hidden="true"></i></a>
								<?php }
								if ($jvbpd_google_link!=''){ ?>
									<a href="<?php echo $jvbpd_google_link;?>" target="_blank" class="jvbpd_single_listing_google javo-tooltip" data-original-title="<?php _e('Google','listopia'); ?>"><i class="fab fa-google" aria-hidden="true"></i></a>
								<?php }
								if ($jvbpd_website_link!=''){?>
									<a href="<?php echo $jvbpd_website_link;?>" target="_blank" class="jvbpd_single_listing_website javo-tooltip" data-original-title="<?php _e('Website','listopia'); ?>"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>
								<?php } ?>
							</div>
						</div><!-- #javo-item-social-section -->
						<?php } ?>
					</div>
				</div>
				<div class="clearfix"></div>
			</div> <!-- header-inner -->
			<?php
			if( function_exists( 'lava_directory_get_edit_page' ) && ( get_post()->post_author == get_current_user_id() || current_user_can( 'manage_options' ) ) ) {
				?>
				<div class="edit-button">
					<a href="<?php echo lava_directory_get_edit_page( get_the_ID() ); ?>">
						<i class="fa fa-pencil"></i>
						<?php esc_html_e( "Edit", 'litopia' ); ?>
					</a>
				</div>
				<?php
			} ?>

			<ul class="javo-core-single-featured-switcher pull-right list-inline">
				<li class='switch-featured'>
					<a class="javo-tooltip" data-original-title="<?php _e('Featured','listopia'); ?>"><i class="jvbpd-icon2-image" aria-hidden="true"></i></a>
				</li>
				<li class='switch-grid'>
					<a class="javo-tooltip" data-original-title="<?php _e('Grid','jvfrmtd'); ?>"><i class="jvd-icon-layers" aria-hidden="true"></i></a>
				</li>
				<li class='switch-category'>
					<a class="javo-tooltip" data-original-title="<?php _e('Category','jvfrmtd'); ?>"><i class="jvd-icon-folder" aria-hidden="true"></i></a>
				</li>
				<li class='switch-map'>
					<a class="javo-tooltip" data-original-title="<?php _e('Map','listopia'); ?>"><i class="jvbpd-icon-geolocalizator" aria-hidden="true"></i></a>
				</li>
				<?php if( 0 != get_post_meta( get_the_ID(), "lv_listing_street_visible", true ) ) : ?>
					<li class='switch-streetview'>
						<a class="javo-tooltip" data-original-title="<?php _e('StreetView','listopia'); ?>"><i class="jvbpd-icon-user" aria-hidden="true"></i></a>
					</li>
				<?php endif; ?>
				<?php if( $is_has_3d ) : ?>
					<li class='switch-3dview'>
						<a class="javo-tooltip" data-original-title="<?php _e('3DView','listopia'); ?>"><i class="icon-viewer-icon-g"></i></a>
					</li>
				<?php endif; ?>
				<?php if( $is_has_video ) : ?>
					<li class='switch-video'>
						<a class="javo-tooltip" data-original-title="<?php _e('Video','listopia'); ?>"><i class="jvbpd-icon-camera-video" aria-hidden="true"></i></a>
					</li>
				<?php endif; ?>
				<?php if( function_exists( 'lava_directory_direction' ) ) : ?>
					<li class='switch-get-direction' data-toggle="modal" data-target="#jvlv-single-get-direction">
						<a class="javo-tooltip" data-original-title="<?php _e('Get Direction','listopia'); ?>"><i class="jvbpd-icon-train"></i></a>
					</li>
				<?php endif; ?>
			</ul>
		</div> <!-- container -->
		<div class="container">
			<div class="row jvbpd-meta-details-wrap">
				<div class="col-md-3 single-header-terms">
					<?php
						$jvbpd_category = wp_get_object_terms(get_the_ID(), 'listing_category', Array( 'fields' => 'names'));
						$jvbpd_location = wp_get_object_terms(get_the_ID(), 'listing_location', Array( 'fields' => 'names' ));
						$jvbpd_category_link = get_term_link($jvbpd_category[0], 'listing_category');
						$jvbpd_location_link = get_term_link($jvbpd_location[0], 'listing_location');
					?>
					<div class="tax-item-category"><i class="jvbpd-icon3-desktop"></i><a href="<?php echo esc_url($jvbpd_category_link); ?>" target="_blank"><?php echo $jvbpd_category[0]; ?></a></div>
					<div class="tax-item-location"><i class="jvbpd-icon3-map"></i><a href="<?php echo esc_url($jvbpd_location_link); ?>" target="_blank"><?php echo $jvbpd_location[0]; ?></a></div>
				</div>
				<div class="col-md-9 jvbpd-meta-details-right">
					<div class="btn-favorite">
							<?php if( class_exists( 'lvDirectoryFavorite_button' ) ) {
								$objFavorite = new lvDirectoryFavorite_button(
									Array(
										'post_id' => get_the_ID(),
										'show_count' => true,
										'show_add_text' => "<span>".__('Save','jvfrmtd')."</span>",
										'save' => "<i class='jvbpd-icon2-bookmark2'></i>",
										'unsave' => "<i class='fa fa-heart'></i>",
										'class' => Array( 'btn', 'lava-single-page-favorite', 'admin-color-setting-hover' ),
									)
								);
								$objFavorite->output();
							} ?>
					</div> <!-- btn-favorite -->
					<div class="btn-share">
						<button type="button" class="btn btn-block admin-color-setting-hover lava-Di-share-trigger">
							<i class="jvbpd-icon2-flag"></i> <?php esc_html_e( "Share", 'jvfrmtd' ); ?>
						</button>
					</div> <!-- btn-share -->
					<div class="btn-amount-review">
						<a href="#javo-item-review-section" class="admin-color-setting-hover"><?php esc_html_e( "2 Ratings", 'jvfrmtd' ); ?> </a>
					</div> <!-- btn-amount-review -->
					<div class="btn-submit-review">
						<a href="#javo-item-review-section" class="admin-color-setting-hover"><i class="jvbpd-icon1-comment-o"></i> <?php esc_html_e( "Submit Review", 'jvfrmtd' ); ?></a>
					</div> <!-- btn-submit-review -->
					<div class="btn-score-review">
						<a href="#javo-item-review-section" class="admin-color-setting-hover"><?php esc_html_e( "4.8 / 5", 'jvfrmtd' ); ?></a>
					</div> <!-- btn-score-review -->
					<?php
					if( function_exists( 'pvc_post_views' ) ){ ?>
						<div class="btn-view-count">
							<?php pvc_post_views( $post_id = 0, $echo = true ); ?>
						</div> <!-- btn-view-count -->
					<?php } ?>
				</div>
			</div>
		</div>
	</div> <!-- single-item-tab-bg -->
</div>

<div class="container">
	<div class="row">
		<div id="javo-single-content" class="col-md-8 col-xs-12 item-single">
			<div class="row javo-detail-item-content">
			<?php if( '' != get_the_content() ){ ?>
			<div class="col-md-12 col-xs-12 item-description" id="javo-item-describe-section" data-jv-detail-nav>

					<h3 class="page-header"><?php esc_html_e( "Description", 'listopia' ); ?></h3>
					<div class="panel panel-default">
						<div class="panel-body">

							<!-- Post Content Container -->
							<div class="jv-custom-post-content loaded">
								<div class="jv-custom-post-content-inner">
									<?php the_content(); ?>
								</div><!-- /.jv-custom-post-content-inner -->
								<div class="jv-custom-post-content-trigger hidden">
									<i class="fa fa-plus"></i>
									<?php esc_html_e( "Read More", 'listopia' ); ?>
								</div><!-- /.jv-custom-post-content-trigger -->
							</div><!-- /.jv-custom-post-content -->

						</div><!--/.panel-body-->
					</div><!--/.panel-->
				</div><!-- /#javo-item-describe-section -->
				<?php }
				lava_directory_amenities(
					get_the_ID(),
					Array(
						'container_before' => sprintf( '
						<div class="col-md-12 col-xs-12 item-amenities" id="javo-item-amenities-section" data-jv-detail-nav>
							<h3 class="page-header">%1$s</h3>
							<div class="panel panel-default">
								<div class="panel-body">
									<div class="expandable-content" >',
									esc_html__( "Amenities", 'listopia' )
						),
						'container_after' => '
									</div>
								</div><!-- panel-body -->
							</div>
						</div><!-- /#javo-item-amenities-section -->'
					)
				); ?>

				<div class="col-md-12 col-xs-12 item-faq" id="javo-item-faq-section" data-jv-detail-nav>
					<h3 class="page-header">%1$s</h3>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="expandable-content" >

							<?php
								if( class_exists( 'lvjr_Faq' ) ) :
									$objFaq = new lvjr_Faq( get_the_ID() );
									if( !empty( $objFaq->values ) ) {
										?>
										<div class="detail-block faq">
											<!--<h3><?php esc_html_e( "FAQ", 'listopia' ); ?></h3>-->

											<div class="panel-group" id="lava_faq" role="tablist" aria-multiselectable="true">
												<?php
												foreach( (array) $objFaq->values as $intIndex => $arrFaQ ) {

													printf( '
														<div class="panel panel-default">
															<div class="panel-heading" role="tab" id="headingOne">
																<i class="jvbpd-icon2-arrow-right"></i>
																<h4 class="panel-title">
																<a role="button" data-toggle="collapse" data-parent="#lava_faq" href="#%4$s" aria-expanded="true" aria-controls="%4$s">%1$s</a>
																</h4>
															</div>
															<div id="%4$s" class="panel-collapse collapse%3$s" role="tabpanel" aria-labelledby="headingOne">
																<div class="panel-body"><div class="lava_faq_content">%2$s</div></div>
															</div>
														</div>',
														$arrFaQ[ 'frequently' ], $arrFaQ[ 'question' ],
														( $intIndex == 0 ? ' show' : '' ), 'lavaFaQ' . $intIndex
													);
												}?>
											</div>
										</div><!-- detail-block contact -->
										<?php
									}
								endif; ?>


							</div>
						</div><!-- panel-body -->
					</div>
				</div><!-- /#javo-item-faq-section -->

				<?php
				$imgViewerFileName = jvbpdCore()->template_path . '/part-single-grid-images.php';
				if( jvbpd_has_attach() && file_exists( $imgViewerFileName ) ) :
					?>
					<div class="col-md-12 col-xs-12 item-gallery">
						<?php require_once( $imgViewerFileName ); ?>
					</div><!-- /.col-md-12.item-gallery -->
				<?php endif; ?><!-- Detail Image-->

				<?php do_action( 'jvbpd_' . get_post_type() . '_single_author_after' );

				if( function_exists( 'lava_directory_booking' ) && get_post_meta( get_the_ID(), '_booking', true ) ) {
				?>
					<div class="col-md-12 col-xs-12" id="javo-item-booking-section">
							<?php do_action( 'jvbpd_' . get_post_type() . '_single_booking' ); ?>
					</div>
				<?php
				}

				if( function_exists( 'get_lava_directory_review' ) ): ?>
					<div class="col-md-12 col-xs-12 item-description" id="javo-item-review-section" data-jv-detail-nav>

						<h3 class="page-header"><?php esc_html_e( "Review", 'listopia' ); ?></h3>
						<div class="panel panel-default">
							<div class="panel-body">
								<?php get_lava_directory_review(); ?>
							</div><!--/.panel-body-->
						</div><!--/.panel-->
					</div><!-- /#javo-item-describe-section -->
				<?php endif; ?>


			</div><!-- /.javo-detail-item-content -->

		</div> <!-- /#javo-single-content -->

		<div id="javo-single-sidebar" class="col-md-4 sidebar-right">

		<?php if( empty($single_addon_options['disable_detail_section'])) { ?>
			<div class="col-md-12 col-xs-12" id="javo-item-workinghours-section" data-jv-detail-nav="">
				<h3 class="page-header"><?php esc_html_e( "Working Hours", 'listopia' ); ?></h3>
				<div class="panel panel-default">
					<div class="panel-body">
						<?php echo do_shortcode( '[lava_working_hours]' ); ?>
					</div><!--/.panel-body -->
				</div><!--/.panel panel-default -->
			</div>

			<!-- Listing meta section -->
			<div class="col-md-12 col-xs-12" id="javo-listings-contact-section" data-jv-detail-nav>
				<h3 class="page-header"><?php esc_html_e( "Detail", 'listopia' ); ?></h3>
			<!--<h3 class="page-header"><?php esc_html_e( "Item detail", 'listopia' ); ?></h3>-->
				<div class="panel panel-default">
					<div class="panel-body">
							<div class="meta-small-map">
								<div class="small-map-container single-lv-map-style" id="lava-single-map-area"></div>
								<div class="lava-single-map-param">
									<input type="hidden" data-map-height="120">
								</div>
								<?php
								printf(
									'<a href="%1$s%2$s,%3$s" target="_blank" class="btn btn-block btn-default" title="%4$s">%4$s</a>',
									esc_url_raw( 'google.com/maps/dir/Current+Location/' ),
									get_post_meta( get_the_ID(), 'lv_listing_lat', true ),
									get_post_meta( get_the_ID(), 'lv_listing_lng', true ),
									esc_html__( "Get a direction", 'listopia' )
								); ?>
							</div>
						<?php if(''!=(get_post_meta( get_the_ID(), '_website', true ))){ ?>
							<div class="contact-info-meta meta-website">
									<span class="contact-icons"><i class=" jvbpd-icon3-globe"></i></span>
									<span><a href="<?php echo esc_url(esc_attr(get_post_meta( get_the_ID(), '_website', true )));?>" target="_blank"><?php echo esc_html(get_post_meta( get_the_ID(), '_website', true ));?></a></span>
							</div><!-- /.row *website -->
						<?php }
						if(''!=(get_post_meta( get_the_ID(), '_email', true ))){?>
							<!--<div class="row meta-email">
									<span class="contact-icons"><i class=" jvbpd-icon3-envelop"></i></span>
									<span>-->
										<?php
										//$listing_email = esc_html(get_post_meta( get_the_ID(), '_email', true ));
										//printf('<a href="mailto:%s">%s</a>', $listing_email, $listing_email);
										?>
									<!--</span>
							</div>--><!-- /.row *email -->
						<?php }
						if(''!=(get_post_meta( get_the_ID(), '_address', true ))){?>
							<div class="contact-info-meta meta-address">
								<span class="contact-icons"><i class="jvbpd-icon2-location3"></i></span>
								<span><?php echo esc_html(get_post_meta( get_the_ID(), '_address', true ));?></span>
							</div><!-- /.contact-info-meta *address -->
						<?php }
						if(''!=(get_post_meta( get_the_ID(), '_phone1', true ))){?>
							<div class="contact-info-meta meta-phone1">
								<span class="contact-icons"><i class="jvbpd-icon2-tell"></i></span>
								<span><a href="tel://<?php echo esc_html(get_post_meta( get_the_ID(), '_phone1', true ));?>"><?php echo esc_html(get_post_meta( get_the_ID(), '_phone1', true ));?></a></span>
							</div><!-- /.contact-info-meta *phone1-->
						<?php }
						if(''!=(get_post_meta( get_the_ID(), '_phone2', true ))){?>
							<div class="contact-info-meta meta-phone2">
								<span class="contact-icons"><i class="jvbpd-icon3-print"></i></span>
								<span><a href="tel://<?php echo esc_html(get_post_meta( get_the_ID(), '_phone2', true ));?>"><?php echo esc_html(get_post_meta( get_the_ID(), '_phone2', true ));?></a></span>
							</div><!-- /.contact-info-meta *phone2-->
						<?php }
						if($listing_keyword = esc_html(lava_directory_terms( get_the_ID(), 'listing_keyword' ))){?>
							<div class="contact-info-meta meta-keyword">
									<span class="contact-icons"><i class="jvbpd-icon2-bookmark2"></i></span>
									<span><i><?php echo $listing_keyword; ?></i></span>
							</div><!-- /.contact-info-meta *phone2-->
					<?php } ?>

					<?php
					$arrSoicalIcons = Array();
					foreach(
						Array(
							'facebook' => 'fab fa-facebook-f',
							'twitter' => 'fab fa-twitter',
							'instagram' => 'fab fa-instagram',
							'google' => 'fab fa-google',
							'linkedin' => 'fab fa-linkedin-in',
							'youtube' => 'fab fa-youtube',
						) as $strSocialName => $strSocialIcon
					) {
						$strSocialLink = get_post_meta( get_the_ID(), '_' . $strSocialName . '_link', true );
						if( $strSocialLink ) {
							$arrSoicalIcons[] = sprintf( '<a href="%1$s" target="_blank" class="jvbpd_single_listing_%2$s" title="%2$s"><i class="%3$s" aria-hidden="true"></i></a>', esc_url_raw( $strSocialLink ), $strSocialName, $strSocialIcon );
						}
					}

					if( !empty( $arrSoicalIcons ) ) {
						printf( '<div class="jvbpd_single_listing_social-wrap text-center">%s</div>', join( '', $arrSoicalIcons ) );
					}

					/*
			$jvbpd_facebook_link = esc_html(get_post_meta( get_the_ID(), '_facebook_link', true ));
			$jvbpd_twitter_link = esc_html(get_post_meta( get_the_ID(), '_twitter_link', true ));
			$jvbpd_instagram_link = esc_html(get_post_meta( get_the_ID(), '_instagram_link', true ));
			$jvbpd_google_link = esc_html(get_post_meta( get_the_ID(), '_google_link', true ));

			if(!($jvbpd_facebook_link =='' && $jvbpd_twitter_link=='' && $jvbpd_instagram_link=='' && $jvbpd_google_link=='')){
			?>
				<div class="jvbpd_single_listing_social-wrap">
					<?php if ($jvbpd_facebook_link!=''){ ?>
						<a href="<?php echo $jvbpd_facebook_link;?>" target="_blank" class="jvbpd_single_listing_facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
					<?php }
					if ($jvbpd_twitter_link!=''){ ?>
						<a href="<?php echo $jvbpd_twitter_link;?>" target="_blank" class="jvbpd_single_listing_twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
					<?php }
					if ($jvbpd_instagram_link!=''){ ?>
						<a href="<?php echo $jvbpd_instagram_link;?>" target="_blank" class="jvbpd_single_listing_instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
					<?php }
					if ($jvbpd_google_link!=''){ ?>
						<a href="<?php echo $jvbpd_google_link;?>" target="_blank" class="jvbpd_single_listing_google"><i class="fab fa-google" aria-hidden="true"></i></a>
					<?php } ?>
				</div>
			<?php }  */ ?>
					</div><!--/.panel-body -->
				</div><!--/.panel panel-default -->
				<?php
					if( function_exists( 'lava_directory_claim_button' ) ){
				?>
					<div class="jvbpd_single_claim_wrap">
					<?php
						lava_directory_claim_button(
							Array(
							'class'	=> 'btn btn-block admin-color-setting-hover',
							'label'		=> esc_html__( "Claim", 'listopia' ),
							'icon'		=> false
							)
						);
					?>
					</div>
					<?php
					}
				?>
			</div><!-- /#javo-item-location-section --><!-- Detail-->

			<div class="col-md-12 col-xs-12" id="javo-item-profile-section" data-jv-detail-nav>
				<div class="text-center">
					<img src="http://demo1.wpjavo.com/listopia/wp-content/uploads/sites/3/avatars/1/59ddeb8c3c54e-bpfull.jpg" class="avatar user-1-avatar avatar-150 photo" width="100" height="100" alt="Profile picture of javo">
					<div class="jvbpd-single-author-name">Posted By Javo</div>
					<button type="button" class="btn btn-block admin-color-setting lava-Di-share-trigger"> 	Follow </button>
					<button type="button" class="btn btn-block admin-color-setting lava-Di-share-trigger"> Profile </button>
				</div>
			</div>

			<?php	} ?>

			<?php do_action( 'jvbpd_' . get_post_type() . '_single_map_after' ); ?> <!--  Get Direction-->

			<?php do_action( 'jvbpd_' . get_post_type() . '_single_description_before' ); ?> <!--Custom Field -->

			<?php
				global $jvbpd_tso;
				if(	 (int) $jvbpd_tso->get( 'single_listing_contact_form_id' , 0 ) > 0
					&& false != $jvbpd_tso->get( 'single_listing_contact_type', false) ){
					$jv_listing_contact_form_id = $jvbpd_tso->get( 'single_listing_contact_form_id' );
					$jv_listing_contact_form_type = $jvbpd_tso->get( 'single_listing_contact_type' );
					if(class_exists('Lava_Advanced_Contact_Form')){
						$intContactFormID = get_post_meta( get_the_ID(), 'advanced_contact_form_id', true );
						$strContactForm = get_post_meta( get_the_ID(), 'advanced_contact_form', true );
						if($intContactFormID!='' && (int)$intContactFormID >0)
							$jv_listing_contact_form_id = $intContactFormID;
						if($strContactForm != '' & $strContactForm != false)
							$jv_listing_contact_form_type = $strContactForm.'form';
					}
			?>
				<div class="col-md-12 col-xs-12" id="javo-item-contact-section" data-jv-detail-nav>
					<h3 class="page-header"><?php esc_html_e( "CONTACT", 'listopia' ); ?></h3>
					<?php
						switch( $jv_listing_contact_form_type ) {
							case 'contactform'	: $jvbpd_quick_contact_shortcode = '[contact-form-7 id=%s]'; break;
							case 'ninjaform'	: $jvbpd_quick_contact_shortcode = '[ninja_forms id=%s]'; break;
						}
						$jvbpd_contact_form_shortcode = sprintf($jvbpd_quick_contact_shortcode, $jv_listing_contact_form_id );
						echo do_shortcode( $jvbpd_contact_form_shortcode );
					?>
				</div><!-- #javo-item-social-section -->
			<?php } ?>
		</div><!-- /.col-md-3 -->
	</div><!--/.contact-info-meta-->
</div><!-- /.container -->








<?php wp_enqueue_script( 'lava-directory-manager-jquery-lava-msg-js' ); ?>
<script type="text/html" id="lava-Di-share">
	<div class="row">
		<div class="col-md-12">
			<header class="modal-header">
				<?php esc_html_e( "Share", 'listopia' ); ?>
				<button type="button" class="close">
					<span aria-hidden="true">&times;</span>
				</button>
			</header>
			<div class="row">
				<div class="col-md-9">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-link"></i>
						</span><!-- /.input-group-addon -->
						<input type="text" value="<?php the_permalink(); ?>" class="form-control" readonly>
					</div>
				</div><!-- /.col-md-9 -->
				<div class="col-md-3">
					<button class="btn btn-primary btn-block" id="lava-wg-url-link-copy" data-clipboard-text="<?php the_permalink(); ?>">
						<i class="fa fa-copy"></i>
						<?php esc_html_e( "Copy URL", 'listopia' );?>
					</button>
				</div><!-- /.col-md-3 -->
			</div><!-- /,row -->
			<p>
				<div class="row">
					<div class="col-md-4">
						<button class="btn btn-info btn-block javo-share sns-facebook" data-title="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>">
							<?php esc_html_e( "Facebook", 'listopia' );?>
						</button>
					</div><!-- /.col-md-4 -->
					<div class="col-md-4">
						<button class="btn btn-info btn-block javo-share sns-twitter" data-title="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>">
							<?php esc_html_e( "Twitter", 'listopia' );?>
						</button>
					</div><!-- /.col-md-4 -->
					<div class="col-md-4">
						<button class="btn btn-info btn-block javo-share sns-google" data-title="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>">
							<?php esc_html_e( "Google +", 'listopia' );?>
						</button>
					</div><!-- /.col-md-4 -->
				</div><!-- /,row -->
			</p>
		</div>
	</div>
</script>
