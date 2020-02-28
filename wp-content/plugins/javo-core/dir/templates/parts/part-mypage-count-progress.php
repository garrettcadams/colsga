<?php
if( ! isset( $jvbpd_aricle_args ) ) {
	die;
} ?>
<div class="col-lg-3 col-sm-6 <?php echo esc_attr( $jvbpd_aricle_args->wrap_class ); ?>">
	<div class="col-in row">
		<div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon="<?php echo esc_attr( $jvbpd_aricle_args->icon ); ?>"></i>
			<h5 class="text-muted vb"><?php echo strtoupper( $jvbpd_aricle_args->label ); ?></h5> </div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<h3 class="counter text-right m-t-15 text-<?php echo esc_attr( $jvbpd_aricle_args->primary_class ); ?>"><?php echo esc_html( $jvbpd_aricle_args->current ); ?></h3> </div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="progress">
				<div class="progress-bar progress-bar-<?php echo esc_attr( $jvbpd_aricle_args->primary_class ); ?>" role="progressbar" aria-valuenow="<?php echo esc_attr( $jvbpd_aricle_args->current); ?>" aria-valuemin="<?php echo esc_attr( $jvbpd_aricle_args->min ); ?>" aria-valuemax="<?php echo esc_attr( $jvbpd_aricle_args->max ); ?>" style="width: <?php echo esc_attr( $jvbpd_aricle_args->percentage ); ?>"> <span class="sr-only"><?php echo esc_attr(  $jvbpd_aricle_args->percentage ); ?> <?php esc_html_e( "Complete (success)", 'jvfrmtd' ); ?></span> </div>
			</div>
		</div>
	</div>
</div>