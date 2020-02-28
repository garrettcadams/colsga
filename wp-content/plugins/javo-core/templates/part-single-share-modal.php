<div class="row">
	<div class="col-md-12">
		<header class="modal-header">
			<?php esc_html_e( "Share", 'jvfrmtd' ); ?>
			<button type="button" class="close">
				<span aria-hidden="true">&times;</span>
			</button>
		</header>

		<div class="row">
			<div class="col-md-9">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-link"></i>
					</span><!-- /.input-group-addon -->
					<input type="text" value="<?php the_permalink(); ?>" class="form-control" readonly>
				</div>
			</div><!-- /.col-md-9 -->
			<div class="col-md-3">
				<button class="btn btn-primary btn-block" id="jvbpd-single-share-link" data-clipboard-text="<?php the_permalink(); ?>">
					<i class="fa fa-copy"></i>
					<?php esc_html_e( "Copy URL", 'jvfrmtd' );?>
				</button>
			</div><!-- /.col-md-3 -->
		</div><!-- /,row -->
		<p>
			<div class="row">
				<div class="col-md-4 col-xs-4">
					<button class="btn btn-info btn-block javo-share sns-facebook" data-title="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>">
						<?php esc_html_e( "Facebook", 'jvfrmtd' );?>
					</button>
				</div><!-- /.col-md-4 -->
				<div class="col-md-4 col-xs-4">
					<button class="btn btn-info btn-block javo-share sns-twitter" data-title="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>">
						<?php esc_html_e( "Twitter", 'jvfrmtd' );?>
					</button>
				</div><!-- /.col-md-4 -->
				<div class="col-md-4 col-xs-4">
					<button class="btn btn-info btn-block javo-share sns-google" data-title="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>">
						<?php esc_html_e( "Google +", 'jvfrmtd' );?>
					</button>
				</div><!-- /.col-md-4 -->
			</div><!-- /,row -->
		</p>
	</div>
</div>