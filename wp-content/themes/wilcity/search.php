<?php
global $wiloke, $wilcitySidebarWrapper, $wilcitySidebarID, $wp_query, $post;
get_header();
$wilcitySidebarID = 'wilcity-blog-sidebar';
$blogSidebar = isset($wiloke->aThemeOptions['single_blog_sidebar_layout']) ? $wiloke->aThemeOptions['single_blog_sidebar_layout'] : 'right';

switch ( $blogSidebar ){
	case 'left':
		$wilcitySidebarWrapper = 'col-md-4 col-md-pull-8';
		$contentWrapper = 'col-md-8 col-md-push-4';
		$imgSize = 'wiloke_listgo_750x420';
		break;
	case 'right':
		$wilcitySidebarWrapper = 'col-md-4';
		$contentWrapper = 'col-md-8';
		$imgSize = 'wiloke_listgo_750x420';
		break;
	default:
		$wilcitySidebarWrapper = '';
		$contentWrapper = 'col-md-12';
		$imgSize = 'large';
		break;
}
?>
    <div class="wil-content wilcity-archive">
        <section class="wil-section bg-color-gray-2">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="heading_module__156eJ wil-text-center">
                            <h1 class="heading_title__1bzno"><?php Wiloke::ksesHTML( sprintf(__( 'Search Results for: %s', 'wilcity' ), '<span>' . get_search_query() . '</span>' )); ?></h1>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr($contentWrapper); ?>">
						<?php if ( have_posts() ) : ?>
							<?php while (have_posts()) : the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post_module__3uT9W'); ?>>
                                    <header class="post_header__2pWQ0">
                                        <a href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail($imgSize); ?>
                                        </a>
                                    </header>
                                    <div class="post_body__TYys6">
                                        <h2 class="post_title__2Jnhn"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                        <div class="post_metaData__3b_38 color-primary-meta">
                                            <span><i class='la la-user'></i> <a href="<?php echo esc_url(get_author_posts_url($post->post_author)); ?>"><?php the_author(); ?></a></span>
                                            <span>
                                                <a href='<?php the_permalink(); ?>'><i class='la la-calendar'></i> <?php echo get_the_date(get_option('date_format'), $post->ID); ?></a>
                                            </span>
                                            <?php if ( has_category($post->ID) ): ?>
                                                <span><i class="la la-list-alt"></i> <?php the_category(', ', 'single', $post->ID); ?></span>
                                            <?php endif; ?>
                                            <span>
                                                <a href='<?php the_permalink(); ?>'><i class='la la-comments'></i> <?php comments_number(esc_html__('No Comment', 'wilcity'), esc_html__('One Comment', 'wilcity'), esc_html__('%s Comments', 'wilcity')); ?></a>
                                            </span>
                                        </div>
                                        <div class="post_description__2Rum5">
                                            <p><?php Wiloke::contentLimit($wiloke->aThemeOptions['blog_excerpt_length'], $post, false, $post->post_content, false); ?></p>
                                        </div>
                                    </div>

                                    <div class="post_readMoreWrap__fwCKz">
                                        <a class="post_readMore__3P2AS" href="<?php the_permalink(); ?>"><?php esc_html_e('Read More', 'wilcity'); ?>
                                        </a>
                                    </div>

									<?php if ( function_exists('wilcitySharingPosts') ) : ?>
                                        <footer class="post_footer__3hdew">
                                            <!-- social-icon_module__HOrwr -->
                                            <div class="social-icon_module__HOrwr social-icon_style-2__17BFy">
												<?php echo do_shortcode('[wilcity_sharing_post post_id="'.$post->ID.'" style="button"]'); ?>
                                            </div><!-- End /  social-icon_module__HOrwr -->
                                        </footer>
									<?php endif; ?>

                                </article>
							<?php endwhile; wp_reset_postdata(); ?>
							<?php WilokeHelpers::pagination($wp_query); ?>
						<?php else: ?>
                            <div class="search-no-results-form">
                                <p class="wil-text-center"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wilcity' ); ?></p>
								<?php get_search_form(); ?>
                            </div>
							<?php
						endif; wp_reset_postdata();
						?>
                    </div>
					<?php
					if ( !empty($wilcitySidebarWrapper) ) {
						get_sidebar();
					}
					?>
                </div>
            </div>
        </section>
    </div>
<?php
get_footer();
