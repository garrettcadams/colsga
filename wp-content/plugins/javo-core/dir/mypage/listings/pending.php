<div class="jv-user-content">
		<div class="card listing-card">
			<div class="card-header"><h4 class="card-title"><?php esc_html_e( "Pending Listings", 'jvfrmtd' ); ?></h4></div><!-- card-header -->
			<?php
			$lavaDashBoardArgs[ 'type' ] = 'pending';
			require_once( dirname( __FILE__ ) . '/contents.php'); ?>
		</div><!-- /.section-block -->
</div>