<?php
if( ! isset( $jvbpd_aricle_args ) ) {
	die;
} ?>
<li class="list-group-item">
	<div class="listing-thumb">
		<a href="<?php echo esc_url_raw( $jvbpd_aricle_args->url ); ?>">
			<span class="badge badge-info badge-status counter">
				<?php echo esc_html( $jvbpd_aricle_args->current ); ?>
			</span>
		</a>
	</div>
	<div class="listing-content">
		<h5 class="title">
			<a href="<?php echo esc_url_raw( $jvbpd_aricle_args->url ); ?>">
				<?php echo esc_html( $jvbpd_aricle_args->label ); ?>
			</a>
		</h5>
		<span class="author">
			<a href="<?php echo esc_attr( $jvbpd_aricle_args->url ); ?>">
				<i class=" jvbpd-icon3-note" aria-hidden="true"></i>
				<?php printf( esc_html__( '%s Listings', 'jvfrmtd' ), $jvbpd_aricle_args->current ); ?>
			</a>
		</span>
	</div>
</li>