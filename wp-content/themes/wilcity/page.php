<?php
get_header();
if ( have_posts() ) {
	while (have_posts()){
		the_post();
		global $wiloke, $wilcitySidebarWrapper, $wilcitySidebarID, $wp_query;

		$wilcitySidebarID = 'wilcity-single-page-sidebar';
		$blogSidebar = isset($wiloke->aThemeOptions['single_page_sidebar_layout']) ? $wiloke->aThemeOptions['single_page_sidebar_layout'] : 'right';
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
		?>

        <div class="wil-content">
            <section class="wil-section bg-color-gray-2">
                <div class="container">
                    <div class="row">
                        <div id="post-<?php the_ID(); ?>" <?php post_class($contentWrapper); ?>>
                            <article class="post_module__3uT9W">
                                <header class="post_header__2pWQ0">
									<?php the_post_thumbnail($imgSize); ?>
                                </header>
                                <div class="post_body__TYys6">
                                    <h1 class="post_title__2Jnhn"><?php the_title(); ?></h1>
                                    <div class="post_singleDescription__2GviT">
										<?php
										the_content();
										wp_link_pages(array('link_before'=>'<span>', 'link_after'=>'</span>', 'before' => '<p class="wilcity-link-pages">' . esc_html__( 'Pages:', 'wilcity' ), 'after' => '</p>'));
										?>
                                    </div>
                                </div>

								<?php if ( (!function_exists('is_woocommerce') || ( function_exists('is_woocommerce') && !is_woocommerce() )) && function_exists('wilcitySharingPosts') ) : ?>
                                    <footer class="post_footer__3hdew">
                                        <!-- social-icon_module__HOrwr -->
                                        <div class="social-icon_module__HOrwr social-icon_style-2__17BFy">
											<?php do_shortcode('[wilcity_sharing_post post_id="'.$post->ID.'"]'); ?>
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