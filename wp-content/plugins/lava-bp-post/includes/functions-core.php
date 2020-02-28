<?php

if( ! defined( 'ABSPATH' ) )
	die();




/**
 * Get assets url in plugin
 *
 * @return	String
 */
function lava_get_directory_assets_url() {
	global $lava_bpp_manager;

	if( ! is_object( $lava_bpp_manager ) )
		return false;

	return $lava_bpp_manager->assets_url;
}





/**
 *
 *
 * @param	String	Taxonomy ID
 * @param	Integer	Target ID of post
 * @param	Boolean	if echo true print result
 * @return	String
 */
function lava_bpp_featured_terms( $taxonomy='', $post_id=0, $echo=true ) {
	global $lava_bpp_func;

	$post			= get_post( $post_id );
	$sep_string		= '|';
	$tmp_string		= $lava_bpp_func->getTermsNameInItems( $post->ID, $taxonomy, $sep_string );
	$output_string	= @explode( $sep_string, $tmp_string );
	$output_result	= isset( $output_string[0] ) ? $output_string[0] : false;

	if( $echo )
		echo $output_result;
	return $output_result;
}




/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_bpp_get_edit_page( $post_id=false ) {
	return lava_bpp()->core->get_edit_link( $post_id );
}





/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_bpp_get_add_form_page( $post_id=false ) {
	return lava_bpp()->core->getAddFormLink();
}




/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_bpp_edit_page() {
	$post	= get_post();
	echo !empty( $post ) ? lava_bpp_get_edit_page( $post->ID ) : false;
}




/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_bpp_setupdata( &$post=false ) {

	if( ! is_object( $post ) )
		$post = get_post();

	if( !class_exists( 'Lava_Bp_Post_Func' ) )
		return;

	Lava_Bp_Post_Func::setupdata( $post );
}




/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
if( ! function_exists( 'lava_add_item_submit_button' ) ) {
	function lava_add_item_submit_button( $args=Array() ) {
		$intID = isset( $GLOBALS[ 'edit' ] ) ? intVal( $GLOBALS[ 'edit' ]->ID ) : 0;
		$is_update = 0 < $intID;

		$params = shortcode_atts(
			Array(
				'add_new' => esc_html__( "Submit", 'lvbp-bp-post' ),
				'edit' => esc_html__( "Save", 'lvbp-bp-post' ),
			), $args
		);

		$post_type		= lava_bpp()->core->getSlug();
		wp_nonce_field( 'lava_bpp_submit_' . $post_type, 'security' );

		printf( '
			<input type="hidden" name="post_id" value="%1$s">
			<fieldset class="submit"><button type="submit">%2$s</button></fieldset>',
			$intID, ( ! $is_update ? $params[ 'add_new' ] : $params[ 'edit' ] )
		);
	}
}





/**
 *
 *
 * @param	String	Image output size
 * @return	String
 */
function lava_bpp_attach( $args=Array() ) {

	global $post;

	$option						= wp_parse_args(
		$args
		, Array(
			'size'				=> 'thumbnail'
			, 'type'			=> 'normal'
			, 'title'			=> false
			, 'wrap_class'		=> ''
			, 'container_class'	=> 'lava-attach'
			, 'featured_image'	=> false
		)
	);

	$arrSlideItems				= Array();

	if( $option[ 'featured_image' ] )
		$arrSlideItems[]		= get_post_thumbnail_id();

	$arrSlideItems				= Array_Merge( $arrSlideItems, (Array) $post->attach );

	$arrOutputHTML				= Array(
		'container_before'		=> "<div class=\"{$option[ 'container_class' ]}\">"
		, 'container_after'		=> '</div>'
		, 'wrap_before'			=> ''
		, 'wrap_after'			=> ''
		, 'item_before'			=> ''
		, 'item_after'			=> ''
	);

	switch( $option[ 'type' ] ) :
		case 'ul' :
		case 'slide' :
		case 'slider' :
			$classes		= @explode( ' ', trim( $option[ 'wrap_class' ] ) );
			$classes		= @implode( ' ', wp_parse_args( $classes, Array( 'lava-attach-item' ) ) );

			$arrOutputHTML[ 'wrap_before' ]		= sprintf( '<ul class="%s">', $classes );
			$arrOutputHTML[ 'item_before' ]		= "<li>";
			$arrOutputHTML[ 'item_after' ]		= "</li>";
			$arrOutputHTML[ 'wrap_after' ]		= "</ul>";
		break;
	endswitch;

	echo $arrOutputHTML[ 'container_before' ] . "\n";

		if( !empty( $option[ 'title' ] ) ) : echo $option[ 'title' ] . "\n"; endif;

		echo "\t" . $arrOutputHTML[ 'wrap_before' ] . "\n";

			if( !empty( $arrSlideItems ) ) : foreach( $arrSlideItems as $attachID ) {
				if( false !== (boolean)( $htmlAttachIMG = wp_get_attachment_image( $attachID, $option['size'] ) ) ) :
					echo "\t\t{$arrOutputHTML['item_before']}$htmlAttachIMG{$arrOutputHTML['item_after']}\n";
				endif;
			} endif;

		echo "\t" . $arrOutputHTML[ 'wrap_after' ] ."\n";

	echo $arrOutputHTML[ 'container_after' ] ."\n";
}