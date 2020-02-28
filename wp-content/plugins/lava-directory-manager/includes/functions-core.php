<?php

if( ! defined( 'ABSPATH' ) )
	die();




/**
 * Get assets url in plugin
 *
 * @return	String
 */
function lava_get_directory_manager_assets_url() {
	global $lava_directory_manager;

	if( ! is_object( $lava_directory_manager ) )
		return false;

	return $lava_directory_manager->assets_url;
}




/**
 * Get file load for template in plugin
 *
 * @param	String filename
 * @return	void
 */
function lava_directory_template( $filename )
{
	global $lava_directory_manager;

	$load_temlate_file		= "{$lava_directory_manager->template_path}/$filename";

	if( file_exists( $load_temlate_file ) )
		require_once $load_temlate_file;
}




/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_directory_terms( $post_id, $taxonomy='', $sep=', ' ) {
	global $lava_directory_manager_func;
	return $lava_directory_manager_func->getTermsNameInItems( $post_id, $taxonomy, $sep );
}




/**
 *
 *
 * @param	String	Taxonomy ID
 * @param	Integer	Target ID of post
 * @param	Boolean	if echo true print result
 * @return	String
 */
function lava_directory_featured_terms( $taxonomy='', $post_id=0, $echo=true ) {
	global $lava_directory_manager_func;

	$post			= get_post( $post_id );
	$sep_string		= '|';
	$tmp_string		= $lava_directory_manager_func->getTermsNameInItems( $post->ID, $taxonomy, $sep_string );
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
function lava_directory_get_edit_page( $post_id=false ) {
	return lava_directory()->core->get_edit_link( $post_id );
}





/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_directory_get_add_form_page( $post_id=false ) {
	return lava_directory()->core->getAddFormLink();
}




/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_directory_edit_page() {
	$post	= get_post();
	echo !empty( $post ) ? lava_directory_get_edit_page( $post->ID ) : false;
}




/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_directory_setupdata( &$post=false ) {

	if( ! is_object( $post ) )
		$post = get_post();

	if( !class_exists( 'Lava_Directory_Manager_Func' ) )
		return;

	Lava_Directory_Manager_Func::setupdata( $post );
}




/**
 *
 *
 * @param	Integer	Target ID of post
 * @param	String	Taxonomy ID
 * @param	String	Separator word
 * @return	String
 */
function lava_directory_mapdata( &$post=false ) {

	if( ! is_object( $post ) )
		$post = get_post();

	if( !class_exists( 'Lava_Directory_Manager_Func' ) )
		return;

	Lava_Directory_Manager_Func::setup_mapdata( $post );
}




/**
 *
 * @return	String
 */
function lava_directory_get_widget() {
	$post	= get_post();
	echo "<ul class=\"lava-single-sidebar\">";
	if( is_active_sidebar( 'lava-' . get_post_type() . '-single-sidebar' ) )
		dynamic_sidebar( 'lava-' . get_post_type() . '-single-sidebar' );
	echo "</ul>";
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
				'add_new' => esc_html__( "Submit", 'Lavacode' ),
				'edit' => esc_html__( "Save", 'Lavacode' ),
			), $args
		);

		$post_type		= lava_directory()->core->getSlug();
		wp_nonce_field( 'lava_directory_manager_submit_' . $post_type, 'security' );

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
function lava_directory_attach( $args=Array() ) {

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




/**
 *
 *
 * @return	String
 */
if( !function_exists( 'lava_get_author_avatar' ) ) : function lava_get_author_avatar() {
	global $post;
	$strAvatarImage	= !empty( $post->avatar ) ? $post->avatar : null;
	echo "<img src=\"{$strAvatarImage}\">";
} endif;





/**
 *
 *
 * @param	Integer	Target ID of post
 * @return	Void
 */
function lava_directory_amenities( $post, $args=Array() ) {

	if( is_numeric( $post ) ) {
		$post = get_post( $post );
	}

	if( ! $post instanceof WP_Post ) {
		$post = get_post();
	}

	$args = shortcode_atts(
		Array(
			'container_before' => '',
			'container_after' => '',
		), $args
	);

	$corePostType = lava_directory()->core->getSlug();
	$taxonomy = 'listing_amenities';

	if( ! apply_filters( 'lava_' . $corePostType . '_amenties_display', true, $post->ID ) ) {
		return false;
	}

	if( ! taxonomy_exists( $taxonomy ) ) {
		return false;
	}

	$is_show_all = lava_directory()->admin->get_settings( 'display_amenities', 'showall' ) == 'showall';
	$is_with_icon = lava_directory()->admin->get_settings( 'display_amenities_icon', 'showall' ) == 'with-own-icon';
	$queried_terms = get_terms( Array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'fields' => 'id=>name' ) );
	$terms_in_post = wp_get_object_terms( $post->ID, $taxonomy, Array( 'fields' => 'ids' ) );
	if( is_wp_error( $queried_terms ) ) {
		printf( "<div align=\"center\">%s</div>", $queried_terms->get_error_message() );
		return;
	}
	$output = Array();
	$output_format = '<div class="lava-amenity%1$s">%2$s</div>';
	if( $is_with_icon ) {
		$output_format = '<div class="lava-amenity%1$s"><span class="%3$s"></span> %2$s</div>';
	}
	echo $args[ 'container_before' ];
		echo '<div id="lava-directory-amenities">';
		foreach( $queried_terms as $term_id => $term_name ) {
			$hasTerm = in_array( $term_id, $terms_in_post );
			if( ! $is_show_all && ! $hasTerm ) {
				continue;
			}
			$output[] = sprintf(
				$output_format,
				( $is_show_all ? ( $is_with_icon ? ( $hasTerm ? ' with-own-icon active' : ' with-own-icon' ) : ( $hasTerm ? ' active' : ''  )  ) : ' showall' ),
				$term_name, lava_directory()->admin->getTermOption( get_term( $term_id ), 'icon' )
			);
		}

		if( !empty( $output ) ) {
			echo join( '', $output );
		}else{
			printf( '<div style="text-align:center;">%s</div>', esc_html__( "Amenities not found.", 'Lavacode' ) );
		}
		echo '</div>';
	echo $args[ 'container_after' ];
}