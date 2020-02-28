<?php do_action( 'lava_' . get_post_type() . '_single_container_before' ); ?>

<div class="item-single">

	<div id="post-<?php the_ID(); ?>" <?php post_class( Array( 'lava-single-content' ) ); ?>>

		<h2 class="item-subtitle"><?php _e( "Summary", 'Lavacode' ); ?></h2>

		<ul class="meta-summary">

			<li class="meta-author">
				<?php lava_get_author_avatar(); ?>
				<strong><?php the_author_meta( 'display_name' ); ?></strong>
			</li>

			<?php if( $strType = lava_directory_featured_terms( 'listing_category', get_the_ID(), false ) ) : ?>
				<li class="meta-type"><strong><?php _e( "Category", 'Lavacode' ); ?></strong><span><?php echo $strType; ?></span></li>
			<?php endif; ?>

			<?php if( $strCity = lava_directory_featured_terms( 'listing_location', get_the_ID(), false ) ) : ?>
			<li class="meta-category">
				<strong><?php _e("Location", 'Lavacode' ); ?></strong>
				<span><?php echo $strCity; ?></span>
			</li>
			<?php endif; ?>

			<?php do_action( "lava_single_meta_append_contents", get_post() ); ?>

		</ul><!--/.meta-summary-->

		<?php
		lava_directory_attach(
			Array(
				'type'					=> 'ul',
				'title'						=> '',
				'size'						=> 'large',
				'wrap_class'			=> 'slides ',
				'container_class'	=> 'lava-detail-images flexslider hidden',
				'featured_image'	=> true
			)
		); ?>

		<ul class="meta-condition">
			<h2 class="item-subtitle"><?php _e( "Details", 'Lavacode' ); ?></h2>
			<?php
			foreach(
				Array(
					'_website'		=> __(  "Website", 'Lavacode' ),
					'_email'			=> __(  "Email", 'Lavacode' ),
					'_address'		=> __(  "Address", 'Lavacode' ),
					'_phone1'		=> __(  "Contact Phone 1", 'Lavacode' ),
					'_phone2'		=> __(  "Contact Phone 2", 'Lavacode' ),
					'_facebook_link'		=> __(  "Facebook", 'Lavacode' ),
					'_twitter_link'		=> __(  "Twitter", 'Lavacode' ),
					'_instagram_link'		=> __(  "Instagram", 'Lavacode' ),
					'_google_link'		=> __(  "Google+", 'Lavacode' )
				) as $key => $label ) :
				printf( "<li class=\"{$key}\"><strong>{$label}</strong> &#58; <span>%s</span></li>", get_post_meta( get_the_ID(), $key, true ) );
			endforeach;
			?>
		</ul><!--/.meta-condition-->

		<?php lava_directory_amenities(
			get_the_ID(),
			Array(
				'container_before' => sprintf( '<div class="amenities"><h2 class="item-subtitle">%1$s</h2>', __( "Amenities", 'Lavacode' ) ),
				'container_after' => '</div>',
			)
		); ?>

		<?php do_action( 'lava_' . get_post_type() . '_single_description_before' ); ?>
		<div class="description">
			<h2 class="item-subtitle"><?php _e( "Description", 'Lavacode' ); ?></h2>
			<?php the_content(); ?>
		</div><!--/.description-->
		<?php do_action( 'lava_' . get_post_type() . '_single_description_after' ); ?>
		<?php do_action( 'lava_' . get_post_type() . '_single_map_before' ); ?>
		<div class="location">
			<h2 class="item-subtitle"><?php _e( "Location", 'Lavacode' ); ?></h2>
			<div id="lava-single-map-area"></div>
			<h2 class="item-subtitle"><?php _e( "StreetView", 'Lavacode' ); ?></h2>
			<div id="lava-single-streetview-area"></div>
		</div><!--/.description-->
		<?php do_action( 'lava_' . get_post_type() . '_single_map_after' ); ?>
	</div> <!-- content -->
</div><!--/.item-single -->

<?php do_action( 'lava_' . get_post_type() . '_single_container_after' ); ?>