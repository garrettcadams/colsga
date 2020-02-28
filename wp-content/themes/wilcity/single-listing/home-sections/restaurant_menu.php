<?php
use \WilokeListingTools\Frontend\SingleListing;
use \WilokeListingTools\Framework\Helpers\GetSettings;

global $post, $wilcityArgs;
if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_restaurant_menu') ){
	return '';
}

$aMenus = SingleListing::getRestaurantMenu($post->ID);

if ( empty($aMenus) ){
	return '';
}
?>
<div class="content-box_module__333d9">
	<div class="content-box_body__3tSRB">
		<?php
		foreach ($aMenus as $key => $aMenu){
			wilcityRenderRestaurantListMenu($aMenu);
		}
		?>
	</div>
</div>
