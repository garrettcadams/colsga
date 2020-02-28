<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Framework\Helpers\Time;

add_shortcode('wilcity_sidebar_coupon', 'wilcityRenderSidebarCoupon');
function wilcityRenderSidebarCoupon($aArgs, $postID = '')
{
    global $post, $wiloke;
    $postID = empty($postID) ? $post->ID : $postID;

    if (!GetSettings::isPlanAvailableInListing($postID, 'toggle_coupon')) {
        return '';
    }

    $aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
    $aAtts = wp_parse_args(
        $aAtts,
        [
            'name' => esc_html__('Coupon', WILOKE_LISTING_DOMAIN),
            'icon' => 'la la-bar-chart'
        ]
    );

    $aCoupon = GetSettings::getPostMeta($postID, 'coupon');

    if (empty($aCoupon) || (empty($aCoupon['code']) && empty($aCoupon['redirect_to']))) {
        return '';
    }
    if (isset($aAtts['isMobile'])) {
        $aCoupon['popup_image'] = wp_get_attachment_image_url($aCoupon['popup_image'], 'large');
        if (isset($aCoupon['expiry_date']) && !empty($aCoupon['expiry_date'])) {
            if (!is_numeric($aCoupon['expiry_date'])) {
                $aCoupon['expiry_date'] = strtotime($aCoupon['expiry_date']);
            } else {
                $aCoupon['expiry_date'] = abs($aCoupon['expiry_date']);
            }

            $expiryDateIn = $aCoupon['expiry_date'] - current_time('timestamp');

            if ($expiryDateIn <= 0) {
                $aCoupon = [];
            } else if ($expiryDateIn > 86400) {
                $aCoupon['expiry_date'] = date_i18n(get_option('date_format').' '.get_option('time_format'),
                    $aCoupon['expiry_date']);
            }
        }



        $aCoupon['popup_image'] = GetSettings::getCouponFeatureImg($aCoupon);
        return apply_filters('wilcity/mobile/sidebar/coupon', json_encode($aCoupon), $post);
    }

    $img = '';
    if (isset($aCoupon['popup_image']) && !empty($aCoupon['popup_image'])) {
        $img = wp_get_attachment_image_url($aCoupon['popup_image'], 'large');
    } else {
        if (isset($wiloke->aThemeOptions['listing_coupon_popup_img']) && !empty($wiloke->aThemeOptions['listing_coupon_popup_img'])) {
            $img = isset($wiloke->aThemeOptions['listing_coupon_popup_img']['url']) && !empty($wiloke->aThemeOptions['listing_coupon_popup_img']['url']) ? esc_url($wiloke->aThemeOptions['listing_coupon_popup_img']['url']) : wp_get_attachment_image_url($wiloke->aThemeOptions['listing_coupon_popup_img']['id'],
                'large');
        }
    }
    $twitterHash = apply_filters('wilcity/wilcity-shortcodes/coupon/twitter-hash',
        esc_html__('coupon', 'wilcity-shortcodes').','.esc_attr($aCoupon['code']));

    $fbQuote      = sprintf(esc_html__('Please use this coupon code', 'wilcity-shortcodes'), $aCoupon['code']);
    $wrapperClass = apply_filters('wilcity/filter/class-prefix',
        'wilcity-sidebar-item-coupon content-box_module__333d9');
    if (isset($aCoupon['expiry_date']) && is_numeric($aCoupon['expiry_date'])) {
        $aCoupon['expiry_date'] = date(get_option('date_format') . ' ' . get_option('time_format'), $aCoupon['expiry_date']);
    }
    ob_start();
    ?>
    <div class="<?php echo esc_attr($wrapperClass); ?>">
        <?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
        <div class="content-box_body__3tSRB">
            <div class="content-box_body__3tSRB">
                <?php if (!empty($img)) : ?>
                    <div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                        'wilcity-coupon-image wil-text-center')); ?>">
                        <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($aCoupon['code']); ?>">
                    </div>
                <?php endif; ?>
                <?php if (!empty($aCoupon['highlight'])): ?>
                    <div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                        'wilcity-coupon-code color-secondary')); ?>">
                        <span><?php echo esc_html($aCoupon['highlight']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($aCoupon['description'])): ?>
                    <div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                        'wilcity-coupon-description')); ?>">
                        <p><?php echo esc_html($aCoupon['description']); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (isset($aCoupon['expiry_date']) && !empty($aCoupon['expiry_date'])): ?>
                    <div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                        'wilcity-coupon-expiry-date wil-text-center')); ?>" style="margin-bottom: 30px;">
                        <p><?php echo esc_html__('Expiry date: ',
                                    'wilcity-shortcodes').esc_html($aCoupon['expiry_date']); ?></p>
                    </div>
                <?php endif; ?>
                <a class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                    'wil-btn mt-10 wil-btn--facebook wil-btn--md wil-btn--block wil-btn--round wilcity-share-on-facebook')); ?>"
                   data-url="<?php echo esc_url(get_permalink($post->ID)); ?>"
                   data-content="<?php echo esc_attr(json_encode($aCoupon)); ?>"
                   data-quote="<?php echo esc_attr($fbQuote); ?>" href="#">
                    <i class="la la-facebook"></i> <?php esc_html_e('Share on Facebook', 'wilcity-shortcodes'); ?>
                </a>
                <a target="_blank" class="wil-btn mt-10 wil-btn--twitter wil-btn--md wil-btn--block wil-btn--round"
                   href="//twitter.com/share?text=<?php echo urlencode($aCoupon['title']); ?>&url=<?php echo esc_url(get_permalink($post->ID)); ?>&hashtags=<?php echo esc_attr($twitterHash); ?>">
                    <i class="la la-twitter"></i> <?php esc_html_e('Share on Twitter', 'wilcity-shortcodes'); ?>
                </a>
                <a href="mailto:?Subject=<?php echo esc_attr(str_replace(' ', '%20',
                    $aCoupon['title'])); ?>&Body=<?php echo esc_attr(sprintf(esc_html__('Please use this coupon code %s',
                    'wilcity-shortcodes'), $aCoupon['code'])); ?>"
                   class="wil-btn mt-10 wil-btn--success wil-btn--md wil-btn--block wil-btn--round"><i
                            class="la la-envelope"></i> <?php esc_html_e('Email Coupon', 'wilcity-shortcodes'); ?></a>
                <a data-target="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                    'wilcity-copy-coupon-code-on-sidebar')); ?>"
                   class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                       'wil-btn mt-10 wil-btn--primary wil-btn--md wil-btn--block wil-btn--round wilcity-copy')); ?>"
                   data-content="<?php echo esc_attr($aCoupon['code']); ?>" href="#"><i
                            class="la la-copy"></i> <?php esc_html_e('Copy Code', 'wilcity-shortcodes'); ?></a>
                <input id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix',
                    'wilcity-copy-coupon-code-on-sidebar')); ?>" type="hidden"
                       value="<?php echo esc_attr($aCoupon['code']); ?>">
            </div>
        </div>
    </div>
    <?php
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
