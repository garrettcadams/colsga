<?php
global $post;
get_header(); ?>

<?php do_action( "lava_{$post->lava_type}_map_container_before" ); ?>

<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div id="lava-map-container"></div>

	<div id="lava-map-filter">

		<div class="filter-group">
			<div class="filter-group-column">
				<label><?php _e( "Category", 'Lavacode'); ?></label>
				<select name="lava_filter[listing_category]" data-filter="listing_category">
					<option value=""><?php _e( "Categories", 'Lavacode' );?></option>
					<?php echo apply_filters('lava_get_selbox_child_term_lists', 'listing_category', null, 'select', false, 0, 0, "-");?>
				</select>
			</div>
		</div>

		<div class="filter-group">

			<div class="filter-group-column">
				<label><?php _e( "Location", 'Lavacode'); ?></label>
				<select name="lava_filter[listing_location]" data-filter="listing_location">
					<option value=""><?php _e( "Location (city)", 'Lavacode' );?></option>
					<?php echo apply_filters('lava_get_selbox_child_term_lists', 'listing_location', null, 'select', false, 0, 0, "-");?>
				</select>
			</div>
			<div class="filter-group-column">
				<label><?php _e( "Amenities", 'Lavacode'); ?></label>
				<select name="lava_filter[listing_amenities]" data-filter="listing_amenities">
					<option value=""><?php _e( "Amenities", 'Lavacode' );?></option>
					<?php echo apply_filters('lava_get_selbox_child_term_lists', 'listing_amenities', null, 'select', false, 0, 0, "-");?>
				</select>
			</div>
		</div>

		<div class="filter-group submit">
			<label><?php _e( "Keyword", 'Lavacode'); ?></label>
			<input type="text" name="keyword" data-filter="tags" placeholder="<?php _e( "Keyword", 'Lavacode' ); ?>">
			<button type="button" id="lava-map-search">
				<?php _e( "Search Now", 'Lavacode' ); ?>
			</button>
		</div>

	</div>

	<p>
		<strong><?php _e( "Listings", 'Lavacode' );?></strong>
		<div id="lava-map-output"><?php _e( "Loading", 'Lavacode' ); ?>....</div>
	</p>

<div>


<fieldset class="hidden" id="lava-map-parameter">
	<input type="hidden" key="ajaxurl" value="<?php echo admin_url( 'admin-ajax.php' ); ?>">
	<input type="hidden" key="crossdomain" value="<?php echo $post->crossdomain; ?>">
	<input type="hidden" key="security" value="<?php echo wp_create_nonce( 'lava_' . $post->lava_type . '_get_json' ); ?>">
	<input type="hidden" key="json_file" value="<?php echo $post->json_file; ?>">
	<input type="hidden" key="prefix" value="<?php echo $post->lava_type; ?>">
	<input type="hidden" key="filter" value="#lava-map-filter">
	<input type="hidden" key="output" value="#lava-map-output">
	<input type="hidden" key="output-template" value="#lava-map-output-template">
	<input type="hidden" key="output-not-found" value="#lava-map-not-found-template">
</fieldset>

<script type="text/javascript">
	jQuery( function( $ ) {
		$.lava_boxMap({
			map : '#lava-map-container',
			params	: '#lava-map-parameter'
		});
	} );
</script>


<?php
do_action( "lava_{$post->lava_type}_map_container_after" );
wp_footer();