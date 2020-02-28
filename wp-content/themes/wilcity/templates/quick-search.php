<?php
if ( !defined('WILOKE_LISTING_TOOL_VERSION') ){
    return '';
}


$aQuickSearchForm = \WilokeListingTools\Framework\Helpers\GetSettings::getOptions('quick_search_form_settings');
if ( empty($aQuickSearchForm) || ( isset($aQuickSearchForm['toggle_quick_search_form']) && $aQuickSearchForm['toggle_quick_search_form'] == 'no' ) ){
	return '';
}

?>
<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-quick-search-wrapper')); ?>" class="header_search__3IFfo">
    <quick-search-form></quick-search-form>
</div>