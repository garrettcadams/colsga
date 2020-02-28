<?php
$spy_nav_font_color = isset( $spy_nav_font_color ) ? $spy_nav_font_color : NULL;
$spy_nav_background = isset( $spy_nav_background ) ? $spy_nav_background : NULL;

$jvbpd_spyscroll_navigation = apply_filters(
	'jvbpd_detail_item_nav'
	, Array(
		'page-style'				=> Array(
			'label'					=> esc_html__( "Top", 'jvfrmtd' )
			, 'class'				=> 'glyphicon glyphicon-home'
			, 'type'				=> Array( get_post_type() )
		)
	)
);

?>
<div id="javo-detail-item-header-wrap" class="container-fluid javo-spyscroll lava-spyscroll" <?php echo $spy_nav_background; ?>>
	<div class="container">
		<div class="row" data-spy="scroll" data-target=".navbar">
				<?php
				if( !empty( $jvbpd_spyscroll_navigation ) )	{
					echo "<ul class=\"nav-tabs\">\n";
					foreach( $jvbpd_spyscroll_navigation as $id => $attr ) {
						if( ! in_Array( get_post_type(), $attr[ 'type' ] ) )
							continue;
						echo "\t\t\t\t\t\t <li class=\"javo-single-nav\" title=\"{$attr['label']}\">\n";
							//echo "\t\t\t\t\t\t\t <a href=\"#{$id}\" {$spy_nav_font_color}><i class=\"{$attr['class']}\"></i> {$attr['label']}</a>\n";
							echo "\t\t\t\t\t\t\t <a href=\"#{$id}\" {$spy_nav_font_color}>{$attr['label']}</a>\n"; // remove icon
						echo "\t\t\t\t\t\t </li>\n";
					}
					echo "\t\t\t\t\t</ul>\n";
				} ?>

			<div class="col-md-3 jv-scrollspy-right-wrap">
			
			</div> <!-- jv-scrollspy-right-wrap -->
		</div> <!-- row -->
	</div> <!--/.nav-collapse -->
</div> <!-- javo-detail-item-header-wrap -->