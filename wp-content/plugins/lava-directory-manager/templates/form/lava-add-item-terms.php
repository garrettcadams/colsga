<?php
$edit_id = isset( $edit ) ? $edit->ID : 0;
$lava_post_type = lava_directory()->core->getSlug();
$addition_terms = apply_filters( "lava_add_{$lava_post_type}_terms", Array() );
$lavaRequiredFields = lava_directory()->admin->get_settings( 'required_fields', Array() );

$limit_terms = Array(
	'listing_category' => lava_directory()->admin->get_settings( 'limit_category', 0 ),
	'listing_location' => lava_directory()->admin->get_settings( 'limit_location', 0 ),
);

if( !empty( $addition_terms ) ) : foreach( $addition_terms as $taxonomy ) {
	$this_value = wp_get_object_terms( $edit_id, $taxonomy, Array( 'fields' => 'ids' ) );
	$is_required = in_array( $taxonomy, $lavaRequiredFields );
	$starHTML = $is_required ? '<span class="field-required-star">*</span> ' : '';

	if( is_wp_error( $this_value ) || $taxonomy == 'listing_amenities' ) {
		continue;
	}

	$objTaxonomy = get_taxonomy( $taxonomy );

	printf( '
		<div class="lava-field-item form-inner field_%2$s">
			<label class="field-title">%7$s %1$s</label>
			<select name="lava_additem_terms[%2$s][]" multiple="multiple" class="lava-add-item-selectize" data-tax="%2$s" data-limit="%3$s" data-create="%4$s"%8$s>
				<option value="">%5$s</option>%6$s
			</select>
		</div>',
		$objTaxonomy->label, $taxonomy,
		( array_key_exists( $taxonomy, $limit_terms ) ? $limit_terms[ $taxonomy ] : 0 ),
		( $taxonomy === 'listing_keyword' ),
		esc_html__( "Select a one", 'Lavacode' ),
		apply_filters( 'lava_get_selbox_child_term_lists', $taxonomy, null, 'select', $this_value, 0, 0, '-' ),
		$starHTML, ( $is_required ? ' required="required"' : '' )
	);
} endif;