<div class="jv-bbp-header">
	<div class="jv-bbp-header-inner container">

		<h1 class="header-title"><?php esc_html_e( "Community forum", 'jvfrmtd' ); ?></h1>

		<?php
		//get_search_form();
		$intBuddypressPage = jvbpd_tso()->get( 'bp_permalink', 0 );
		$strSearchShortcodeName = 'lava_ajax_search_form';
		if( shortcode_exists( $strSearchShortcodeName ) ) {
			printf( '<div class="header-content">%s</div>', do_shortcode( '[' . $strSearchShortcodeName . ']' ) );
		} ?>

		<div class="header-caption">
			<div class="pull-left">
				<?php bbp_breadcrumb(); ?>
				<?php bbp_forum_subscription_link(); ?>
				<?php bbp_topic_favorite_link(); ?>
			</div>
			<?php if( 0 < intVal( $intBuddypressPage ) ) { ?>
				<div class="pull-right"><a href="<?php echo get_permalink( $intBuddypressPage ); ?>" class="btn btn-primary add-new-forum-form-link"><i class="jvd-icon-basic_sheet_pencil"></i> <?php _e('Add New Forum','jvfrmtd'); ?></a></div>
			<?php } ?>

		</div>

		<?php
		/*
		if ( bbp_allow_search() ) : ?>
			<div class="bbp-search-form">
			<?php bbp_get_template_part( 'form', 'search' ); ?>
			</div>
		<?php endif; */
		?>


	</div>
</div>