<?php
$lava_marker = $lavaInstance->getTermOption( $tag, 'marker', $taxonomy, '' );
$lava_amenities = $lavaInstance->getTermOption( $tag, 'features', $taxonomy, array() );
?>

<div class="form-field term-icon">
	<label for="term_icon"><?php esc_html_e( "Icon", 'Lavacode' ); ?></label>
	<input type="text" name="term_icon" id="term_icon" value="<?php echo $lavaInstance->getTermOption( $tag, 'icon', $taxonomy, '' ); ?>" />
	<?php echo isset( $iconDescription ) ? $iconDescription : ''; ?>
</div>

<div class="form-field term-featured-image">
	<label for="lava_listing_category_marker"><?php _e('Map Marker', 'Lavacode');?></label>
	<div class="lava-edit-term-wp-upload" data-args="<?php echo esc_attr( json_encode( Array(
			'title' => esc_html( "Select map marker", 'Lavacode' ),
			'select' => esc_html( "Select", 'Lavacode' ),
		))); ?>">
		<div class="preview-wrap" data-image="<?php echo esc_url( $lava_marker ); ?>"></div>
		<div class="action-wrap">
			<input type="hidden" name="lava_listing_category_marker" value="<?php echo $lava_marker; ?>" data-type="url">
			<button type="button" class="button button-primary upload">
				<?php esc_html_e( "Select", 'Lavacode' ); ?>
			</button>
			<button type="button" class="button button-default remove">
				<?php esc_html_e( "Remove", 'Lavacode' ); ?>
			</button>
		</div>
	</div>
	<p class="description">
		<?php _e( "Category markers : you need to refresh map data after you upload or change map pins (markers). Listings > Settings > Jason Generator", 'Lavacode');?>
	</p>
</div>

<?php
if( taxonomy_exists( 'listing_amenities' ) ) {
	$arrAmenitieTerms = get_terms( Array( 'taxonomy' => 'listing_amenities', 'fields' => 'id=>name', 'hide_empty' => false, ) );
	?>
	<div class="form-field term-featured-image">
		<label><?php _e( "Features (Amenities)", 'Lavacode' );?></label>
		<div class="" style="max-height:200px; overflow-y:scroll; border:solid 1px #aaaaaa; padding:10px;">
			<?php
			if( !empty( $arrAmenitieTerms ) ) {
				foreach( $arrAmenitieTerms as $intTerm => $strTerm ) {
					printf( '
						<div><label>
							<input type="checkbox" name="lava_listing_category_features[]" value="%1$s"%3$s> %2$s
						</label></div>', $intTerm, $strTerm,
						checked( in_array( $intTerm, $lava_amenities ), true, false )
					);
				}
			} ?>
		</div>
	</div>
<?php }