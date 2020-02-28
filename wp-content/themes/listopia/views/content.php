<?php
/**
 * Template part for displaying content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if( has_post_thumbnail() && !is_single() ) : ?>
		<section class="single-post-thumbnail text-center">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full', Array( 'class' => 'img-responsive' ) ); ?></a>
			<div class="image-overlay"></div>
		</section><!-- .entry-header -->
	<?php endif; ?>
	<header class="entry-header">
		<?php
		if ( !is_single() ) :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		if( is_search() || is_archive() || is_home() ){
			?>
			<div class="entry-summary">
				<a href="<?php the_permalink(); ?>"><?php the_excerpt(); ?></a>
			</div><!-- .entry-summary -->
			<?php
		}else{
			the_content( sprintf(
					/* translators: %s: Name of current post. */
					wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'jvbpd' ), array(
						'span' => array(
							'class' => array(),
						),
					) ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false ) ) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jvbpd' ),
				'after' => '</div>',
			) );
		} ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
		if( is_single() ) {
			Awps\Core\Tags::entry_footer();
		}else{
			?>
			<div class="post-tags-items-wrap">
				<ul class="post-tags-items list-inline">
					<?php
					printf("<li class='list-inline-item mr-2'>%s</li>", esc_html__('Tags:','jvbpd' ));
					the_tags( "<li class=\"list-inline-item\"><span class=\"post-tags-item\">",
						',</span></li><li class="list-inline-item"><span class="post-tags-item">',
						'</span></li>'
					); ?>
				</ul>
			</div>


			<?php
		}

		edit_post_link(
			sprintf( "%s", esc_html__( 'Edit', 'jvbpd' ) ),
			'<span class="edit-link">',
			'</span>'
		); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
