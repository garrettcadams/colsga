<?php
$jvbpd_single_core_nav = jvbpd_single_navigation();
$spy_nav_font_color = isset( $spy_nav_font_color ) ? $spy_nav_font_color : NULL;
$spy_nav_background = isset( $spy_nav_background ) ? $spy_nav_background : NULL;
?>
<div id="javo-detail-item-header-wrap" class="container-fluid javo-spyscroll lava-spyscroll" <?php echo $spy_nav_background; ?>>
	<div class="container">
		<div class="row" data-spy="scroll" data-target=".navbar">
			<div id="javo-detail-item-header" class="col-md-8 navbar">
				<?php
				if( !empty( $jvbpd_single_core_nav ) )	{
					echo "<ul class=\"nav-tabs\">\n";
					foreach( $jvbpd_single_core_nav as $id => $attr ) {
						if( ! in_Array( get_post_type(), $attr[ 'type' ] ) )
							continue;
						echo "\t\t\t\t\t\t <li class=\"javo-single-nav\" title=\"{$attr['label']}\">\n";
							//echo "\t\t\t\t\t\t\t <a href=\"#{$id}\" {$spy_nav_font_color}><i class=\"{$attr['class']}\"></i> {$attr['label']}</a>\n";
							echo "\t\t\t\t\t\t\t <a href=\"#{$id}\" {$spy_nav_font_color}>{$attr['label']}</a>\n";
						echo "\t\t\t\t\t\t </li>\n";
					}
					echo "\t\t\t\t\t</ul>\n";
				} ?>
			</div>

			<div class="col-md-4 jv-scrollspy-right-wrap">

				<div class="row">
					<div class="col-md-4 btn-submit-review">
						<a href="#javo-item-review-section"><i class="jvbpd-icon1-comment-o"></i> Rating</a>
					</div> <!-- btn-submit-review -->
					<div class="col-md-4 btn-share">
						<button type="button" class="btn btn-block admin-color-setting-hover lava-Di-share-trigger">
							<i class="jvbpd-icon2-flag"></i> <?php esc_html_e( "Share", 'jvfrmtd' ); ?>
						</button>
					</div> <!-- btn-submit -->
					<div class="col-md-4 btn-favorite">
							<?php if( class_exists( 'lvDirectoryFavorite_button' ) ) {
								$objFavorite = new lvDirectoryFavorite_button(
									Array(
										'post_id' => get_the_ID(),
										'show_count' => true,
										'show_add_text' => "<span>".__('Save','jvfrmtd')."</span>",
										'save' => "<i class='jvbpd-icon2-bookmark2'></i>",
										'unsave' => "<i class='fa fa-heart'></i>",
										'class' => Array( 'btn', 'lava-single-page-favorite' ),
									)
								);
								$objFavorite->output();
							} ?>
					</div> <!-- btn-submit -->
				</div>

			<?php if( $this->single_type == 'type-half' ) : ?>
				<div class="dropdown">
					<button class="dropbtn"><i class="jvd-icon-envelope" <?php echo $spy_nav_font_color; ?>></i></button>
					<div class="dropdown-content e3 dropdown-menu-right">
						<?php lava_directory_get_widget(); ?>
					</div>
				</div><!-- dropdown -->
			<?php endif; ?>
			</div> <!-- jv-scrollspy-right-wrap -->
		</div> <!-- row -->
	</div> <!--/.nav-collapse -->
</div> <!-- javo-detail-item-header-wrap -->