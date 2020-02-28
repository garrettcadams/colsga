<?php
global $post, $wilcityArgs;

if ( !function_exists('wilcity_render_grid_post') ){
    return '';
}

$aPostIDs = WilokeListingTools\Framework\Helpers\GetSettings::getPostMeta($post->ID, 'my_posts');

if ( empty($aPostIDs) ){
    return '';
}

$aArgs = array(
	'post_type' => 'post',
	'posts_per_page' => $wilcityArgs['maximumItemsOnHome'],
	'post_status' => 'publish',
	'post__in'		=> is_array($aPostIDs) ? array_map('intval', $aPostIDs) : array_map('intval', explode(',', $aPostIDs))
);

$oPosts = new WP_Query($aArgs);

if ( $oPosts->have_posts() ) :
	?>
	<div class="content-box_module__333d9 wilcity-single-listing-post-box">
		<header class="content-box_header__xPnGx clearfix">
			<div class="wil-float-left">
				<h4 class="content-box_title__1gBHS"><i class="<?php echo esc_attr($wilcityArgs['icon']); ?>"></i><span><?php echo esc_html($wilcityArgs['name']); ?></span></h4>
			</div>
		</header>
		<div class="content-box_body__3tSRB">
			<div class="row" data-col-xs-gap="10">
				<?php
				while ($oPosts->have_posts()){
					$oPosts->the_post();
					?>
                    <div class="col-sm-6">
					    <?php wilcity_render_grid_post($oPosts->post); ?>
                    </div>
                    <?php
				}
				?>
			</div>
		</div>
        <footer class="content-box_footer__kswf3">
            <switch-tab-btn tab-key="posts" wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover wil-text-center" page-url="<?php echo esc_url($url); ?>" tab-title="<?php echo esc_attr(\WilokeListingTools\Frontend\SingleListing::renderTabTitle(__('Posts', 'wilcity'))); ?>">
                <template slot="insideTab">
					<?php esc_html_e('See All', 'wilcity'); ?>
                </template>
            </switch-tab-btn>
        </footer>
	</div>
	<?php
endif; wp_reset_postdata();
