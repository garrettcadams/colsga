<?php
global $edit;

$lava_query					= new lava_Array( $_POST );
$lava_get_this_tags			= wp_get_post_tags( $edit->ID );
$lava_this_tags				= '';
$lava_post_type				= lava_bpp()->core->slug;

foreach( $lava_get_this_tags as $tags ) {
	$lava_this_tags .= $tags->name. ', ';
}
?>

<div class="lv-dashboard-additem lava-item-add-form">

	<div class="notice hidden"></div>
	<form method="post" enctype="multipart/form-data">

		<?php do_action( "lava_add_{$lava_post_type}_form_before", $edit ); ?>

		<div class="form-inner">
			<label class="field-title"><?php _e( "Title", "lvbp-bp-post" ); ?></label>
			<input name="txt_title" type="text" value="<?php echo esc_attr( isset($edit) ? $edit->post_title : NULL ); ?>" placeholder="<?php _e('Write a title','lvbp-bp-post'); ?>">
		</div>

		<div class="form-inner">
			<label class="field-title description"><?php _e( "Description", "lvbp-bp-post" ); ?></label>
			<textarea name="txt_content" rows="10" placeholder="<?php _e( "Write a description", 'lvbp-bp-post' );?>"><?php echo !empty($edit)?$edit->post_content:'';?></textarea>
		</div>

		<?php do_action( "lava_add_{$lava_post_type}_form_after", $edit ); ?>

		<!-- Submit Button -->
		<?php lava_add_item_submit_button(); ?>
		<input type="hidden" name="action" value="<?php echo "lava_{$lava_post_type}_submit_item";?>">

	</form>

</div>
<?php
wp_enqueue_media();
do_action( "lava_add_{$lava_post_type}_edit_footer", get_query_var('edit') );