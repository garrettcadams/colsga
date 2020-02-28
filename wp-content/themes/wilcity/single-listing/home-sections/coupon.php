<?php
global $post, $wilcityArgs;
use WilokeListingTools\Framework\Helpers\GetSettings;

$aCoupon = GetSettings::getPostMeta($post->ID, 'coupon');
if ( empty($aCoupon) || ( empty($aCoupon['code']) && empty($aCoupon['redirect_to']) ) ){
	return '';
}

if ( !isset($aCoupon['expiry_date']) ){
	$aCoupon['expiry_date'] = '';
}

if (isset($aCoupon['expiry_date']) && is_numeric($aCoupon['expiry_date'])) {
    $aCoupon['expiry_date'] = date(get_option('date_format') . ' ' . get_option('time_format'), $aCoupon['expiry_date']);
}

?>
<div class="content-box_module__333d9">
    <div class="content-box_body__3tSRB">
	    <?php echo do_shortcode('[wilcity_get_coupon highlight="'.esc_attr($aCoupon['highlight']).'" title="'.esc_attr($aCoupon['title']).'" description="'.esc_attr($aCoupon['description']).'" code="'.esc_attr($aCoupon['code']).'" redirect_to="'.esc_attr($aCoupon['redirect_to']).'" expiry_date="'.esc_attr($aCoupon['expiry_date']).'"]'); ?>
    </div>
</div>
