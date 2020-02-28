<?php
global $wiloke, $wilokeSidebarPosition;

$sidebarClass = $wilokeSidebarPosition === 'left' ? 'col-md-3 col-md-pull-9' : 'col-md-3';
?>
<div class="<?php echo esc_attr($sidebarClass); ?>">
	<div class="sidebar-background--light">
		<?php
		if ( is_active_sidebar('wiloke-events-sidebar') ){
			dynamic_sidebar('wiloke-events-sidebar');
		}
		?>
	</div>
</div>
