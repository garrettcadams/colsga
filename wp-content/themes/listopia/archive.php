<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package jvbpd
 */

get_header(); ?>

<?php get_template_part( 'views/headers/header', 'archive' ); ?>
<div class="container">
	<div class="row">
		<div <?php Awps\Core\Layout::content_attributes();?>>
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) : the_post();
						get_template_part( 'views/content', get_post_format() );
					endwhile;
					the_posts_navigation();
				else :
					get_template_part( 'views/content', 'none' );
				endif; ?>
				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- .col- -->
		<?php if(Awps\Core\Layout::is_active_sidebar()) { ?>
			<div class="col-sm-3">
				<?php get_sidebar(); ?>
			</div><!-- .col- -->
		<?php } ?>
	</div><!-- .row -->
</div><!-- .container -->
<?php
get_footer();
