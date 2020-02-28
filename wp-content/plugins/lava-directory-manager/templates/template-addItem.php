<?php
global $edit;
$lava_post_type = lava_directory()->core->slug;
?>

<div class="lv-dashboard-additem lava-item-add-form">

	<div class="notice hidden"></div>
	<form method="post" enctype="multipart/form-data">
		<?php
		do_action( "lava_add_{$lava_post_type}_form_before", $edit );
		foreach(
			Array(
				'txt_title' => Array(
					'label' => esc_html__( "Title", 'Lavacode' ),
					'element' => 'input',
					'type' => 'text',
					'class' => 'all-options',
					'value' => $edit instanceof WP_Post ? $edit->post_title : null,
					'no_group' => true,
				),
				'_tagline' => Array(
					'label' => esc_html__( "TagLine", 'Lavacode' ),
					'element' => 'input',
					'type' => 'text',
					'class' => 'all-options',
					'placeholder' => esc_html__( "Tagline", 'Lavacode' )
				),
				'txt_content' => Array(
					'label' => esc_html__( "Description", 'Lavacode' ),
					'element' => 'textarea',
					'class' => 'all-options',
					'value' => $edit instanceof WP_Post ? $edit->post_content : null,
					'no_group' => true,
				),
			) as $field => $fieldArgs
		) {
			$fieldInstance = new Lava_Directory_Manager_Field( $field, $fieldArgs );
			if( isset( $fieldArgs[ 'no_group' ] ) ) {
				$fieldInstance->fieldGroup = false;
			}
			if( isset( $fieldArgs[ 'value' ] ) ) {
				$fieldInstance->value = $fieldArgs[ 'value' ];
			}
			echo $fieldInstance->output();
		}

		do_action( "lava_add_{$lava_post_type}_form_after", $edit );

		// Submit button
		lava_add_item_submit_button(); ?>
		<input type="hidden" name="action" value="<?php echo "lava_{$lava_post_type}_manager_submit_item";?>">

	</form>

</div>
<?php
wp_enqueue_media();
do_action( "lava_add_{$lava_post_type}_edit_footer", get_query_var('edit') );