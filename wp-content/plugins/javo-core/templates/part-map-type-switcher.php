<?php
/* Map Switcher */{
	$jvbpd_listing_switcher =
		Array(
			'maps'	=>
				Array(
					'label'		=> esc_html__( "Map", 'jvfrmtd' )
					, 'icon'	=> 'fa fa-globe'
				)
			, 'listings'	=>
				Array(
					'label'		=> esc_html__( "List", 'jvfrmtd' )
					, 'icon'	=> 'fa fa-bars'
				)
		);
} ?>
<div id="javo-maps-listings-switcher" <?php jvbpd_map_class( 'text-right'); ?>>
	<div class="col-sm-9 switcher-left">
		<?php do_action( 'jvbpd_'. jvbpdCore()->getSlug() . '_map_switcher_before' ); ?>
	</div><!-- /.col-xs-8 -->
	<div class="col-sm-3 switcher-right">
		<div class="btn-group" data-toggle="buttons">
			<?php
			foreach( $jvbpd_listing_switcher as $type => $attr )
			{
				$this_listing_type	= apply_filters(
					'jvbpd_' . jvbpdCore()->getSlug() . '_map_switcher_value',
					get_post_meta(get_the_ID(), '_page_listing', true )
				);
				$jvbpd_listing_type		= $this_listing_type != '1' ? 'maps' : 'listings';
				$is_active				= $this_listing_type == '1';
				$is_active				= $jvbpd_listing_type === $type ? ' active' : $is_active;
				echo "<label class=\"btn btn-default {$is_active}\">";
					echo "<input type=\"radio\" name=\"m\" value=\"{$type}\"" . checked( (boolean)$is_active, true, false ) . ">";
					echo "<i class=\"{$attr['icon']}\"></i>" . ' ';
					echo "<span>".esc_html( $attr['label'] )."</span>";
				echo "</label>";
			} ?>
		</div><!--/.btn-group-->
	</div>
</div>