<?php do_action( "lava_{$post_type}_listings_before" ); ?>
<form id="lava-bpp-listing">
	<!--
	<div class="search-type">
		<div class="search-type-keywords">
			<input type="text" name="keyword" placeholder="<?php _e( "Keywords", 'lvbp-bp-post' ); ?>" data-filter-keyword>
		</div>
		<div class="search-type-location">

			<?php if( $optAddressField ) : ?>
				<input type="text" name="location" placeholder="<?php _e( "Location", 'lvbp-bp-post' ); ?>" data-filter-location>
			<?php else: ?>
				<select name="lava_filter[listing_location]" data-filter="listing_location">
					<option value=""><?php _e( "Any Location", 'lvbp-bp-post' );?></option>
					<?php echo apply_filters('lava_get_selbox_child_term_lists', 'listing_location', null, 'select', false, 0, 0, "-");?>
				</select>
			<?php endif; ?>
		</div>
	</div>
	<div class="select-type">
		<ul>
			<?php
			$lava_filterMultiple	= 'listing_category';
			if( $arrType_terms = get_terms( $lava_filterMultiple, Array( 'hide_empty' => false, 'fields' => 'id=>name' ) ) ) {
				foreach( $arrType_terms as $term_id => $name ) {
					echo "
						<li>
							<label>
								<input type=\"checkbox\" name=\"lava_filter[{$lava_filterMultiple}][]\" data-filter=\"{$lava_filterMultiple}\" value=\"{$term_id}\" checked>{$name}
							</label>
						</li>
						";
				}
			} ?>
		</ul>
	</div>
	<button type="submit">
		<?php _e( "Search", 'lvbp-bp-post' ); ?>
	</button>
		-->

	<fieldset class="hidden">
		<input type="hidden" name="paged" value="1">
		<input type="hidden" name="action" value="lava_bpp_listing">
	</fieldset>

</form>

<div id="lava-bpp-output"></div>
<?php do_action( "lava_{$post_type}_listings_after" ); ?>