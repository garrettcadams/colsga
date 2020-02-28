<?php
global $wiloke;
$sidebarPosition = $wiloke->aThemeOptions['woocommerce_sidebar'];
if ( empty($sidebarPosition) || $sidebarPosition == 'no' ){
    return '';
}

if ( is_cart() || is_checkout() || is_singular('product') ){
    return '';
}

?>
<div class="wilcity-shop-sidebar-position-<?php echo esc_attr($sidebarPosition); ?>">
	<aside class="sidebar-1_module__1x2S9">
		<?php if ( is_active_sidebar('wilcity-woocommerce-sidebar') ){
			dynamic_sidebar('wilcity-woocommerce-sidebar');
		} ?>
	</aside>
</div>
