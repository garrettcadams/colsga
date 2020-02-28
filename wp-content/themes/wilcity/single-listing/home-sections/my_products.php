<?php
use WilokeListingTools\Framework\Helpers\GetSettings;

global $post, $wilcityArgs;
$aProducts = GetSettings::getMyProducts($post->ID);

if ( empty($aProducts)  ){
    return '';
}

if ( isset($wilcityArgs['maximumItemsOnHome']) && !empty($wilcityArgs['maximumItemsOnHome']) ){
	$aProducts = array_slice($aProducts, 0, $wilcityArgs['maximumItemsOnHome']);
}

$columns = apply_filters('wilcity/event/my_tickets/columns', 2);

$productsContent = do_shortcode('[products columns="'.$columns.'" ids="'.implode(',', $aProducts).'"]');

if ( empty($productsContent) ){
	return '';
}
?>
<div class="content-box_module__333d9">
	<header class="content-box_header__xPnGx clearfix">
		<div class="wil-float-left">
			<h4 class="content-box_title__1gBHS"><i class="<?php echo esc_attr($wilcityArgs['icon']); ?>"></i><span><?php echo esc_html($wilcityArgs['name']); ?></span></h4>
		</div>
	</header>
	<div class="content-box_body__3tSRB">
		<div><?php echo $productsContent; ?></div>
	</div>
</div>

