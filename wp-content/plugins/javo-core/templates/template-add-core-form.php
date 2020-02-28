<?php
/**
 * Template Name: Submit listing form template
 * Author : javo theam
 */

global $wpdb, $edit;

$edit_id = intVal( get_query_var( 'edit' ) );
$is_edit = $wpdb->get_var( "select ID from {$wpdb->posts} where ID={$edit_id}" );
if( $is_edit ) {
	$edit = get_post( $edit_id );
}else{
	// Initialze for edit variable.
	$edit					= new stdClass();
	$edit->ID				=
	$edit->post_title		=
	$edit->post_content		=
	$edit->post_author		= false;
}

$lava_query = new lava_Array( $_POST );
$lava_get_this_tags = wp_get_post_tags( $edit->ID );
$lava_this_tags = '';
$lava_post_type = lava_directory()->core->slug;

add_action( 'wp_footer', Array( lava_directory()->shortcode, '_form_enqueues' ) );

$allow = true;

// If logged User ?
if('member' ==  lava_directory_manager_get_option( 'add_capability' ) ) {
	if( Lava_Directory_Manager_Shortcodes::ACCEPT !== ( $cReturn = Lava_Directory_Manager_Shortcodes::is_available_shortcode() ) ) {
		$allow = false;
	}
}

// If current user has modify permission ?
if( $is_edit && false !== get_post_status( $edit->ID ) ) {
	if( Lava_Directory_Manager_Shortcodes::ACCEPT !== ( $cReturn = lava_directory()->shortcode->is_can_modify( $edit_id ) ) ){
		$allow = false;
	}
}

if( function_exists( 'acf_form_head' ) ) {
	acf_form_head();
}
get_header();

if( $allow ) :
	?>
	<div class="lv-dashboard-additem lava-item-add-form">
		<div class="notice hidden"></div>
		<form method="post" enctype="multipart/form-data">
			<?php
			if(apply_filters('jvbpd_core/template/submit/display_content', true)) {
				the_content();
			}else{
				do_action('jvbpd_core/template/submit/custom_content');
			} ?>
			<!-- Submit Button -->
			<?php // lava_add_item_submit_button(); ?>
			<input type="hidden" name="action" value="<?php echo "lava_{$lava_post_type}_manager_submit_item";?>">
			<?php do_action('jvbpd_core/template/submit/form_after'); ?>
		</form>
	</div>
	<?php
else:
	echo $cReturn;
endif;
wp_enqueue_media();
get_footer();