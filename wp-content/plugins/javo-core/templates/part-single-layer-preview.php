<div class="jvbpd-single-preview-layer right">
	<h3 class="title"><?php esc_html_e( "Preview", 'jvfrmtd' ); ?></h3>
	<?php
	if( function_exists( 'lava_directory' ) && get_post_type() == jvbpdCore()->getSlug() ) {
		lava_directory()->template->single_control_buttons();
	} ?>
</div>