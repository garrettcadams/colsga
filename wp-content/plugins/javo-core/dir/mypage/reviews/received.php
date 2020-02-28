<div class="jv-user-content">

	<!-- Starting Content -->
		<div class="card listing-card">
			<div class="card-header">
				<h4 class="card-title"><?php esc_html_e( "Recent Reviews", 'jvfrmtd' ); ?></h4>
			</div>
			<ul class="list-group list-group-flush">
				<?php jvbpdCore()->template_instance->load_template( '../dir/mypage/reviews/received-content' ); ?>
			</ul>
		</div> <!-- .card -->
	<!-- Content End -->

</div><!--/row-->