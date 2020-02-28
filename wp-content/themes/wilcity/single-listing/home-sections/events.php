<?php
global $post, $wilcityArgs;
$url = get_permalink($post->ID);
$oEventQuery = new WP_Query(
	array(
		'post_type'         => 'event',
		'posts_per_page'    => $wilcityArgs['maximumItemsOnHome'],
		'post_status'       => 'publish',
		'isFocusExcludeEventExpired'       => true,
        'post_parent'       => $post->ID,
        'order'             => 'ASC',
        'orderby'           => 'starts_from_ongoing_event',
	)
);


if ( $oEventQuery->have_posts() ) :
	?>
    <div class="content-box_module__333d9 wilcity-single-listing-events-box">
        <header class="content-box_header__xPnGx clearfix">
            <div class="wil-float-left">
                <h4 class="content-box_title__1gBHS"><i class="<?php echo esc_attr($wilcityArgs['icon']); ?>"></i><span><?php echo esc_html($wilcityArgs['name']); ?></span></h4>
            </div>
            <div class="wil-float-right"><a class="fs-13 color-primary" href="<?php echo esc_url(apply_filters('wilcity/add-new-event-url','#', $post)); ?>"><?php esc_html_e('+ Create an event', 'wilcity'); ?></a></div>
        </header>
        <div class="content-box_body__3tSRB">
            <div class="row" data-col-xs-gap="10">
				<?php
				while ($oEventQuery->have_posts()){
					$oEventQuery->the_post();
					get_template_part('single-listing/partials/event');
				}
				?>
            </div>
        </div>
        <footer class="content-box_footer__kswf3">
            <switch-tab-btn tab-key="events" wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover wil-text-center" page-url="<?php echo esc_url($url); ?>" tab-title="<?php echo esc_attr(\WilokeListingTools\Frontend\SingleListing::renderTabTitle(__('Events', 'wilcity'))); ?>">
                <template slot="insideTab">
					<?php esc_html_e('See All', 'wilcity'); ?>
                </template>
            </switch-tab-btn>
        </footer>
    </div>
	<?php
endif; wp_reset_postdata();
