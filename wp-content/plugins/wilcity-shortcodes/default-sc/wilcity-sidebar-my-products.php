<?php
use \WilokeListingTools\Framework\Helpers\GetSettings;


add_shortcode('wilcity_sidebar_my_products', 'wilcitySidebarMyProducts');
function wilcitySidebarMyProducts($aArgs){
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'icon'          => 'la la-clock-o',
			'desc'          => '',
			'name'          => 'My Products',
			'content'       => '',
			'key'           => ''
		)
	);

	global $post;
	$aProducts = GetSettings::getMyProducts($post->ID);

	if ( empty($aProducts) ){
		return '';
	}

    if ( isset($aAtts['isMobile']) ){
        return apply_filters('wilcity/mobile/sidebar/my_products', '', $aProducts, $aAtts);
    }

	$total = count($aProducts);
	if ( $total <= 3 ){
		$columns = $total;
	}else{
		$columns = 2;
	}

	$productsContent = do_shortcode('[products columns="'.$columns.'" ids="'.implode(',', $aProducts).'"]');

	if ( empty($productsContent) ){
		return '';
	}
	ob_start();
	?>
    <div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix','wilcity-sidebar-item-my-products content-box_module__333d9')); ?>">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
        <div class="content-box_body__3tSRB">
			<?php echo $productsContent; ?>
        </div>
    </div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
