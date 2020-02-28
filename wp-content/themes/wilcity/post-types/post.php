<?php
get_header();
\WilokeListingTools\Framework\Helpers\General::$isBookingFormOnSidebar = true;
if ( have_posts() ) {
	while (have_posts()){
		the_post();
		global $wiloke, $wilcitySidebarWrapper, $wilcitySidebarID, $wp_query;
		$wilcitySidebarID = 'wilcity-single-post-sidebar';
		$blogSidebar = isset($wiloke->aThemeOptions['single_post_sidebar_layout']) ? $wiloke->aThemeOptions['single_post_sidebar_layout'] : 'right';

		switch ($blogSidebar){
			case 'left':
				$wilcitySidebarWrapper = 'col-md-4 col-md-pull-8';
				$contentWrapper = 'col-md-8 col-md-push-4';
				$imgSize = 'wilcity_750x420';
				break;
			case 'right':
				$wilcitySidebarWrapper = 'col-md-4';
				$contentWrapper = 'col-md-8';
				$imgSize = 'wilcity_750x420';
				break;
			default:
				$wilcitySidebarWrapper = '';
				$contentWrapper = 'col-md-12';
				$imgSize = 'large';
				break;
		}
		$featuredImg = \WilokeListingTools\Framework\Helpers\GetSettings::getBlogFeaturedImage($post->ID, $imgSize);
		?>

        <div class="wil-content">
            <section class="wil-section bg-color-gray-2">
                <div class="container">
                    <div class="row">
                        <div id="post-<?php the_ID(); ?>" <?php post_class($contentWrapper); ?>>
                            <article class="post_module__3uT9W">
                                <?php if ( !empty($featuredImg) ) : ?>
                                <header class="post_header__2pWQ0">
                                    <img src="<?php echo esc_url($featuredImg); ?>" alt="<?php echo esc_attr($post->post_title); ?>">
                                </header>
                                <?php endif; ?>
                                <div class="post_body__TYys6">
                                    <h1 class="post_title__2Jnhn"><?php the_title(); ?></h1>
                                    <div class="post_metaData__3b_38 color-primary-meta">
                                        <span><i class='la la-user'></i> <?php the_author(); ?></span>
                                        <span><i class='la la-calendar'></i> <?php the_date(); ?></span>
                                        <?php if ( has_category() ): ?>
                                            <span><i class="la la-list-alt"></i> <?php the_category(', '); ?></span>
                                        <?php endif ?>
                                        <span><i class='la la-comments'></i> <?php comments_number(esc_html__('No Comment', 'wilcity'), esc_html__('One Comment', 'wilcity'), esc_html__('%s Comments', 'wilcity')); ?></span>
                                    </div>
                                    <div class="post_singleDescription__2GviT">
										<?php
										the_content();
										wp_link_pages(array('link_before'=>'<span>', 'link_after'=>'</span>', 'before' => '<p class="wilcity-link-pages">' . esc_html__( 'Pages:', 'wilcity' ), 'after' => '</p>'));
										?>
                                    </div>
									<?php if ( has_tag() ) : ?>
                                        <div class="wilcity-tags">
											<?php the_tags( esc_html__('Tags: ', 'wilcity'),', ' ); ?>
                                        </div>
									<?php endif; ?>
                                </div>

								<?php if ( function_exists('wilcitySharingPosts') ) : ?>
                                    <footer class="post_footer__3hdew">
                                        <!-- social-icon_module__HOrwr -->
                                        <div class="social-icon_module__HOrwr social-icon_style-2__17BFy">
											<?php echo do_shortcode('[wilcity_sharing_post post_id="'.$post->ID.'" style="button"]'); ?>
                                        </div><!-- End /  social-icon_module__HOrwr -->
                                    </footer>
								<?php endif; ?>

                            </article><!-- End / post_module__3uT9W -->
							<?php comments_template(); ?>
                        </div>
						<?php if ( !empty($wilcitySidebarWrapper) ) {
							get_sidebar();
						} ?>
                    </div>
                </div>
            </section>
        </div>
		<?php
	}
}
wp_reset_postdata();
get_footer();