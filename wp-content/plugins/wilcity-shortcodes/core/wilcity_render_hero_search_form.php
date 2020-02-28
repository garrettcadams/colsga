<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;

function wilcity_sc_render_hero_search_form($aAtts){
    global $wiloke;
    
    foreach($aAtts['items'] as $index => $item) {
        $item = is_array($item) ? (object)$item : $item;
        $item->icon = WilokeListingTools\Framework\Helpers\GetSettings::getPostTypeField('icon', $item->post_type);
        $aAtts['items'][$index] = $item;
    }

	$oFirstItem = reset($aAtts['items']);
	$oFirstItem = is_array($oFirstItem) ? (object)$oFirstItem : $oFirstItem;

    if ( isset($_GET['lang']) ){
	    $language = $_GET['lang'];
    }else if ( defined('ICL_LANGUAGE_CODE') ){
        $language = ICL_LANGUAGE_CODE;
    }else{
	    $language = '';
    }

	?>
    <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-hero-search-form')); ?>" class="tab_module__3fEXT wil-tab">
        <hero-tabs-search-form default-type="<?php echo esc_attr($oFirstItem->post_type); ?>" search-action-url="<?php echo esc_url(get_permalink($wiloke->aThemeOptions['search_page'])) ?>" :o-items='<?php echo json_encode($aAtts['items']); ?>' language="<?php echo esc_attr($language); ?>"></hero-tabs-search-form>
    </div>
<?php
}
