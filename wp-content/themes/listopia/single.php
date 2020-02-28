<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 *
 */

get_header();
get_template_part( 'views/headers/header', 'single' ); ?>
<div class="container single-body-wrap header-<?php the_ID(); ?>">
	<div class="row">
		<div <?php Awps\Core\Layout::content_attributes();?>>
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
					<?php
					/* Start the Loop */
					while ( have_posts() ) : the_post();
						get_template_part( 'views/content', get_post_format() );
						the_post_navigation();
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					endwhile; ?>
				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- .col- -->
		<?php if(Awps\Core\Layout::is_active_sidebar()) { ?>
			<div class="col-sm-3">
				<?php get_sidebar(); ?>
			</div>
		<?php } ?>
	</div><!-- .row -->
</div><!-- .container -->
<?php
get_footer();