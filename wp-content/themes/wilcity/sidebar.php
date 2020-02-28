<?php
global $wiloke, $wilcitySidebarWrapper, $wilcitySidebarID;
?>
<div class="<?php echo esc_attr($wilcitySidebarWrapper); ?>">
	<aside class="sidebar-1_module__1x2S9">
		<?php if ( is_active_sidebar($wilcitySidebarID) ){
			dynamic_sidebar($wilcitySidebarID);
		} ?>
	</aside>
</div>
