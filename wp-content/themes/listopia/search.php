<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 *
 */

get_header(); ?>

<div class="container">

	<div class="row">

		<div <?php Awps\Core\Layout::content_attributes();?>>

			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

				<?php
					if ( have_posts() ) : ?>

						<header>
							<h1 class="page-title"><?php printf(
							/* translators: %s: Search Term. */
							esc_html__( 'Search Results for: %s', 'jvbpd' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
						</header><!-- .page-header -->

					<?php
								/* Start the Loop */
								while ( have_posts() ) : the_post();

										get_template_part( 'views/content', 'search' );

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
