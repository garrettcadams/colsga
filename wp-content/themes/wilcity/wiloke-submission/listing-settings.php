<?php
global $post;
if ( !\WilokeListingTools\Frontend\User::isPostAuthor($post, true) ) {
    return '';
}
?>
<div v-show="isSingleNavActivating('listing-settings')" id="single-listing-settings" class="listing-settings listing-detail_body__287ZB <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-js-toggle-group')); ?>" data-tab-key="listing-settings">
    <div class="container">
        <div class="listing-detail_row__2UU6R clearfix">
			<?php do_action('wilcity/single-listing/before-listing-settings'); ?>
            <div class="wil-colSmall">
                <single-sidebar></single-sidebar>
            </div>
            <div class="wil-colLarge">
                <message v-show="msg.length" :status="msgStatus" :icon="msgIcon" :msg="msg"></message>
                <div class="content-box_module__333d9">
                    <single-general></single-general>
                    <single-edit-navigation></single-edit-navigation>
                    <single-edit-sidebar></single-edit-sidebar>
                </div>
            </div>
			<?php do_action('wilcity/single-listing/after-listing-settings'); ?>
        </div>
    </div>
</div>
