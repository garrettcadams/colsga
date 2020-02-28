<?php
use WilokeListingTools\Controllers\EventController;

get_header();
global $wiloke, $wilcityReviewConfiguration;

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$sidebarPos = isset($wiloke->aThemeOptions['single_event_sidebar']) ? $wiloke->aThemeOptions['single_event_sidebar'] : 'right';
		switch ($sidebarPos){
            case 'no':
	            $wrapperClass = 'col-md-12';
	            $sidebarClass = '';
                break;
            case 'left':
	            $wrapperClass = 'col-md-8 col-md-push-4';
	            $sidebarClass = 'col-md-4 col-md-pull-8';
                break;
            default:
                $wrapperClass = 'col-md-8';
	            $sidebarClass = 'col-md-4';
                break;
        }

		?>
		<div id="wilcity-single-event-wrapper">
			<div class="wil-content">
				<section class="wil-section bg-color-gray-2 pt-30">
					<div class="container">
						<div class="row">
							<div class="<?php echo esc_attr($wrapperClass); ?>">
								<article class="event-detail-content_module__2KYXK">
                                    <?php get_template_part('single-event/header'); ?>
								</article>
                                <div class="event-detail-content_body__2GYgr">
                                    <?php
                                    $wilcityReviewConfiguration['enableReview'] = EventController::isEnableComment();
                                    ?>
	                                <?php get_template_part('single-event/content'); ?>
	                                <?php get_template_part('reviews/comment-form'); ?>
	                                <?php get_template_part('single-event/comments'); ?>
                                </div>
							</div>
                            <?php if ( !empty($sidebarClass) ) : ?>
							<div class="<?php echo esc_attr($sidebarClass); ?>">
                                <div class="sidebar-background--light">
                                <?php
                                if ( is_active_sidebar('wilcity-single-event-sidebar') ){
                                    dynamic_sidebar('wilcity-single-event-sidebar');
                                }
                                ?>
                                </div>
							</div>
                            <?php endif; ?>
						</div>
					</div>
				</section>
			</div>
            <?php do_action('wilcity/single-event/wil-content', $post, true); ?>
		</div>
		<?php
	}
}
wp_reset_postdata();
get_footer();
?>
