<?php
if( ! isset( $jvbpd_aricle_args ) ) {
	die;
} ?>
	<li>
		<a href="#">
			<div>
				<p> <strong><?php echo strtoupper( $jvbpd_aricle_args->label ); ?></strong> <span class="pull-right text-muted"><?php echo esc_attr(  $jvbpd_aricle_args->current . ' / ' . $jvbpd_aricle_args->max ); ?></span> </p>
				<div class="progress progress-striped active">
					<div class="progress-bar progress-bar-<?php echo esc_attr( $jvbpd_aricle_args->primary_class ); ?>" role="progressbar" aria-valuenow="<?php echo esc_attr( $jvbpd_aricle_args->current ); ?>" aria-valuemin="<?php echo esc_attr( $jvbpd_aricle_args->min ); ?>" aria-valuemax="<?php echo esc_attr(  $jvbpd_aricle_args->max ); ?>" style="width: <?php echo esc_attr( $jvbpd_aricle_args->percentage ); ?>"> <span class="sr-only"><?php echo esc_attr( $jvbpd_aricle_args->percentage ); ?> <?php esc_html_e( "Complete (success)", 'jvfrmtd' ); ?></span> </div>
				</div>
			</div>
		</a>
	</li>
	<li class="divider"></li>