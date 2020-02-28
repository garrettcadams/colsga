<?php
$edit_id = isset( $edit ) ? $edit->ID : 0;
$lava_post_type = lava_bpp()->core->getSlug();
$addition_terms = apply_filters( "lava_add_{$lava_post_type}_terms", Array() );

$limit_terms = Array(
	'category' => lava_bpp()->admin->get_settings( 'limit_category' ),
);

if( !empty( $addition_terms ) ) : foreach( $addition_terms as $taxonomy ) {

	$this_value = wp_get_object_terms( $edit_id, $taxonomy, Array( 'fields' => 'ids' ) );
	$objTaxonomy = get_taxonomy( $taxonomy );

	printf( '
		<div class="form-inner">
			<label for="" class="field-title">%1$s</label>
			<select name="lava_additem_terms[%2$s][]" multiple="multiple" class="lava-add-item-selectize" data-tax="%2$s" data-limit="%3$s" data-create="%4$s">
				<option value="">%5$s</option>%6$s
			</select>
		</div>',
		$objTaxonomy->label, $taxonomy,
		( array_key_exists( $taxonomy, $limit_terms ) ? $limit_terms[ $taxonomy ] : 0 ),
		( $taxonomy === 'post_tag' ),
		esc_html__( "Select a one", 'lvbp-bp-post' ),
		apply_filters( 'lava_get_selbox_child_term_lists', $taxonomy, null, 'select', $this_value, 0, 0, '-' )
	);
} endif;

?>

<script type="text/javascript">
jQuery( function( $ ){

	var lava_Ai_update_extend = function() {
		if( ! window.__LAVA_AI__EXTEND__ )
			this._init();
	}

	lava_Ai_update_extend.prototype = {

		constrcutor : lava_Ai_update_extend,

		_init : function() {
			var obj						= this;
			window.__LAVA_AI__EXTEND__	= 1;
			obj.setCategory();
		},

		setCategory : function() {
			var obj						= this;

			$( '.lava-add-item-selectize' ).each( function() {

				var
					limit		= parseInt( $( this ).data( 'limit' ) || 0 )
					, isCreate	= parseInt( $( this ).data( 'create' ) || 0 )
					options		= { plugins : [ 'remove_button' ], create : isCreate };

				if( limit > 0 )
					options.maxItems	= limit;

				$( this ).selectize( options );

			} );
		}
	};
	new lava_Ai_update_extend;
} );
</script>