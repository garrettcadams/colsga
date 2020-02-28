<div class="jvbpd_somw_info panel {featured}">
	<div class="row jvbpd_somw_info_title">
		<div class="col-md-12 col-xs-12">
				<div class="row featured-show">
					<div class="content">
						<div class="ribbon"><?php esc_html_e( "Featured", 'jvfrmtd');?></div>
					</div><!-- /.content -->
				</div>
				<div class="">
					<div class="">
						<a href="{permalink}">
							{thumbnail}
						</a>
					</div><!--/.col-md-4 col-xs-4 -->
					<div class="col-md-12 col-xs-12">
						<div class="row">
							<div class="col-md-12 col-xs-12 text-left map-info-title">
								<h3><a href="{permalink}">{post_title}</a></h3>
							</div><!--/.col-md-12 col-xs-12 -->
							<div class="col-md-12 col-xs-12 text-left map-info-author">
								<span>{author_name}</span>
							</div><!--/.col-md-12 col-xs-12 -->
						</div><!--/.row-->
					</div><!--/.col-md-8 col-xs-8-->
				</div><!--/.row-->
		</div><!--/.col-md-12 col-xs-12-->
	</div><!--/.row-->
	<div class="jvbpd_somw_meta">
		{meta}
	</div> <!--// jvbpd_somw_meta -->
	{rating}
	<div class="jvbpd_somw_btns">
		<div class="row">
			<div class="col jvbpd_social">
				<ul class="jvbpd_social_list">
					<li class="twitter">
						<a href="#" class="javo-share sns-twitter" data-title="{post_title}" data-url="{permalink}">
							<span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-twitter fa-stack-1x fa-inverse"></i></span>
						</a>
					</li>
					<li class="twitter">
						<a href="#" class="javo-share sns-facebook" data-title="{post_title}" data-url="{permalink}">
							<span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-facebook fa-stack-1x fa-inverse"></i></span>
						</a>
					</li>
				</ul>
			</div> <!-- //col-md-6 jvbpd_social -->
			<div class="col jvbpd_btns_detail text-right">
				<button type="button" class="btn btn-primary btn-sm javo-infow-brief admin-color-setting" data-id="{post_id}">
					<i class="fa fa-search-plus"></i> <span><?php _e('Brief', 'jvfrmtd'); ?></span>
				</button>
				<button type="button" class="btn admin-color-setting" onclick="location.href='{permalink}';">
					<i class="fa fa-link"></i> <span><?php esc_html_e('Detail', 'jvfrmtd'); ?></span>
				</button>
			</div>
		</div>
	</div> <!--// jvbpd_somw_btns -->
</div>