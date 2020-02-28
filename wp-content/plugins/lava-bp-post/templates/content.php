<?php do_action( 'lava_' . get_post_type() . '_single_container_before' ); ?>

<div class="item-single">

	<div id="post-<?php the_ID(); ?>" <?php post_class( Array( 'lava-single-content' ) ); ?>>

		<ul class="meta-summary">

			<li class="meta-author">
				<?php // lava_get_author_avatar(); ?>
				<strong><?php the_author_meta( 'display_name' ); ?></strong>
			</li>
			<li class="meta-type"><strong><?php _e( "Category", 'lvbp-bp-post' ); ?></strong> <span><?php echo join( ', ', wp_get_object_terms( get_the_ID(), 'category', array( 'fields' => 'names' ) ) ); ?></span></li>
			<?php do_action( "lava_single_meta_append_contents", get_post() ); ?>
		</ul><!--/.meta-summary-->

		<?php
		lava_bpp_attach(
			Array(
				'type'				=> 'ul',
				'title'				=> '',
				'size'				=> 'medium_large',
				'wrap_class'		=> 'slides ',
				'container_class'	=> 'lava-detail-images flexslider hidden',
				'featured_image'	=> true
			)
		); ?>

		<?php do_action( 'lava_' . get_post_type() . '_single_description_before' ); ?>

		<div class="description">
			<?php the_content(); ?>
		</div><!--/.description-->
		<?php do_action( 'lava_' . get_post_type() . '_single_description_after' ); ?>
	</div> <!-- content -->
</div><!--/.item-single -->

<?php do_action( 'lava_' . get_post_type() . '_single_container_after' ); ?>