<?php
use WilokeListingTools\Frontend\User as WilokeUser;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Models\PostModel;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;
use \WilokeListingTools\Frontend\SingleListing;

General::$isBookingFormOnSidebar = true;
get_header();
global $post, $wiloke, $wilcitySingleSidebarPos;
$logo = GetSettings::getLogo($post->ID, 'thumbnail');
$url = get_permalink($post->ID);

$aGeneralListingSettings = GetSettings::getPostMeta($post->ID, wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('general'));

if ( !isset($aGeneralListingSettings['sidebarPosition']) ){
	$wilcitySingleSidebarPos = 'wil-sidebar'.ucfirst($wiloke->aThemeOptions['single_listing_sidebar_layout']);
}else{
	$wilcitySingleSidebarPos = 'wil-sidebar'.ucfirst($aGeneralListingSettings['sidebarPosition']);
}
if ( have_posts() ): while ( have_posts() ): the_post();
	?>
	<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-single-listing-content')); ?>">
		<div class="wil-content">
			<div class="listing-detail_module__2-bfH">
				<?php get_template_part('single-listing/header'); ?>
				<div class="listing-detail_first__1PClf">
					<div class="container">
						<div class="listing-detail_left__22FMI">
							<div class="listing-detail_goo__1A8J-">
								<div class="listing-detail_logo__3fI4O bg-cover" style="background-image: url(<?php echo esc_url($logo); ?>);">
									<a href="<?php the_permalink(); ?>"><img class="hidden" src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($post->post_title);  ?>"></a>
								</div>
							</div>
							<div class="listing-detail_titleWrap__2A2Mm js-titleWrap-detail">
								<h1 class="listing-detail_title__2cR-R"><span class="listing-detail_text__31u2P"><?php the_title(); ?><?php if ( PostModel::isClaimed($post->ID) ) : ?><span class="listing-detail_claim__10fsw color-primary"><i class="la la-check"></i><span><?php esc_html_e('Claimed', 'wilcity'); ?></span></span><?php endif; ?></span></h1>
								<?php
								$tagLine = WilokeHelpers::getPostMeta($post->ID, 'tagline');
								if ( !empty($tagLine) ) :
									?>
									<span class="listing-detail_tagline__3u_9y"><?php Wiloke::ksesHTML($tagLine); ?></span>
								<?php endif; ?>
							</div>
						</div>
						<div class="listing-detail_right__2KHL5">
							
							<div class="listing-detail_rightButton__30xaS clearfix">
								<?php
                                $aNavbarButtons = apply_filters('wilcity/single-listing/right-top-tools/buttons', array('favorite', 'share', 'inbox'));
                                foreach ($aNavbarButtons as $button){
	                                get_template_part('single-listing/right-top-tools/'.$button);
                                }
                                do_action('wilcity/action/single-listing/right-top-tools/after');
                                ?>
							</div>

							<div class="listing-detail_rightDropdown__3J1qK">
								<div class="dropdown_module__J_Zpj">
									<div class="dropdown_threeDots__3fa2o" data-toggle-button="dropdown" data-body-toggle="true"><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span></div>
									<div class="dropdown_itemsWrap__2fuze" data-toggle-content="dropdown">
										<ul class="list_module__1eis9 list-none list_small__3fRoS list_abs__OP7Og arrow--top-right">
											<?php if ( WilokeUser::isPostAuthor($post, array('postAuthor'=>$post->post_author)) ) : ?>
												<li class="list_item__3YghP <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-menu-edit-listing'));?>"><a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="<?php echo esc_url(apply_filters('wilcity/single-listing/edit-listing', null, $post)); ?>"><span class="list_icon__2YpTp"><i class="la la-edit"></i></span><span class="list_text__35R07"><?php esc_html_e('Edit', 'wilcity'); ?></span></a></li>
											<?php endif; ?>

											<?php if ( ReviewModel::isEnabledReview($post->ID, $post->post_type) && !ReviewModel::isUserReviewed($post->ID) ) : ?>
												<li class="list_item__3YghP <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-menu-add-review'));?>">
                                                    <review-popup-btn v-on:on-open-review-popup="onOpenReviewPopup" review-id="" btn-name="<?php echo esc_attr__('Write a review', 'wilcity'); ?>" icon="la la-star-o" wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover"></review-popup-btn>
                                                </li>
											<?php endif; ?>
											<?php if ( WilokeUser::isPostAuthor($post) ) : ?>
												<li class="list_item__3YghP <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-menu-listing-settings'));?>">
                                                    <switch-tab-btn tab-title="<?php echo esc_attr(SingleListing::renderTabTitle(__('Listing Settings', 'wilcity'))); ?>" tab-key="listing-settings" wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover" page-url="<?php echo esc_url($url); ?>">
                                                        <template slot="insideTab"><span class="list_icon__2YpTp"><i class="la la-cog"></i></span><span class="list_text__35R07"><?php esc_html_e('Settings', 'wilcity'); ?></span></template>
                                                    </switch-tab-btn>
                                                </li>
											<?php endif; ?>

                                            <?php if (\WilokeListingTools\Controllers\ReportController::isAllowReport() ) : ?>
                                            <li class="list_item__3YghP <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-menu-report'));?>">
                                                <report-popup-btn wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover" target-id="<?php echo esc_attr($post->ID); ?>"><template slot="insideBtn"><span class="list_icon__2YpTp"><i class="la la-flag-o"></i></span><span class="list_text__35R07"><?php esc_html_e('Report', 'wilcity'); ?></span></template></report-popup-btn>
                                            </li>
                                            <?php endif; ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				/*
				 * @hooked SingleListing:printNavigation
				 */
				get_template_part('single-listing/navigation');
				?>
				<?php get_template_part('wiloke-submission/listing-settings'); ?>
				<div class="listing-detail_body__287ZB">
					<div class="container">
						<?php get_template_part('single-listing/content'); ?>
					</div>
				</div>
			</div>
			<?php do_action('wilcity/single-listing/wil-content', $post, true); ?>
		</div>
		<?php
		do_action('wilcity/single-listing/footer-wil-content');
		?>
		<dynamic-popup></dynamic-popup>
		<promotion-popup></promotion-popup>
	</div>
	<?php
endwhile; endif; wp_reset_postdata();
get_footer();
