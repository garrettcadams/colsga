<?php
namespace WilokeListingTools\Framework\Helpers;

use WilokeListingTools\AlterTable\AlterTableBusinessHourMeta;
use WilokeListingTools\AlterTable\AlterTableBusinessHours;
use WilokeListingTools\AlterTable\AlterTableLatLng;
use WilokeListingTools\Controllers\DokanController;
use WilokeListingTools\Frontend\PriceRange;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\MetaBoxes\Listing;
use WilokeListingTools\Models\BusinessHourMeta;
use WilokeListingTools\Models\EventModel;

class GetSettings
{
    protected static $aOptions = [];
    protected static $aAllParentTerms;
    protected static $aPostTerms;
    public static $aUserPlans;
    public static $aUsersData;
    public static $aPostMeta = [];
    public static $aPostFeaturedImgsByTerm = [];
    public static $aPostTypesSettings = [];
    private static $aCacheListingPlans = [];
    protected static $aCacheTermsBelongsToPostType = [];
    private static $customLoginPage = null;
    private static $canRegister = null;
    private static $aRawPromotionSettings = null;
    private static $aPromotionSettings = null;
    private static $aPromotionPlans = null;
    private static $findPosition;

    /*
     * Get main theme color
     *
     * @since 1.2.0
     */
    public static function getThemeColor()
    {
        $color = \WilokeThemeOptions::getOptionDetail('advanced_main_color');
        switch ($color) {
            case 'cyan';
                $color = '#56e0f9';
                break;
            case 'blue';
                $color = '#4fc3f7';
                break;
            case 'pink';
                $color = '#ff71fa';
                break;
            case 'red';
                $color = '#ff7474';
                break;
            case 'custom';
                $aCustomColor = \WilokeThemeOptions::getOptionDetail('advanced_custom_main_color');
                $color        = $aCustomColor['rgba'];
                break;
            default:
                $color = '#f06292';
                break;
        }

        return $color;
    }

    /*
     * Generating key of each promotion position. We will use this key to save the duration of each plan on the Promotion Post
     *
     * @since 1.2.0
     */
    public static function generateSavingPromotionDurationKey($aPromotion)
    {
        if (isset($aPromotion['id']) && !empty($aPromotion['id'])) {
            return $aPromotion['position'].'_'.trim($aPromotion['id']);
        }

        return $aPromotion['position'];
    }

    /*
     * Get all Promotion Plans Settings under Wiloke Listing Tools -> Promotions
     *
     * @since 1.2.0
     */
    public static function getPromotionPlans()
    {
        if (!empty(self::$aPromotionPlans)) {
            return self::$aPromotionPlans;
        }

        $aRawPromotionPlans = GetSettings::getOptions('promotion_plans');
        if (!is_array($aRawPromotionPlans)) {
            self::$aPromotionPlans = [];
        } else {
            foreach ($aRawPromotionPlans as $aPromotion) {
                self::$aPromotionPlans[self::generateSavingPromotionDurationKey($aPromotion)] = $aPromotion;
            }
        }

        return self::$aPromotionPlans;
    }

    /*
     * Generating Timestamp of duration of each place (Saving to Listing ID)
     *
     * @since 1.2.0
     */
    public static function generateListingPromotionMetaKey($aPromotion, $isMetaQuery = false)
    {
        $key = 'promote_'.self::generateSavingPromotionDurationKey($aPromotion);

        return $isMetaQuery ? 'wilcity_'.$key : $key;
    }

    private static function getPromotionSettings()
    {
        if (self::$aRawPromotionSettings !== null) {
            return self::$aRawPromotionSettings;
        }

        self::$aRawPromotionSettings = GetSettings::getOptions('promotion_plans');

        return self::$aRawPromotionSettings;
    }

    public static function getPromotionSetting($promotionID)
    {
        self::getPromotionSettings();

        if (isset(self::$aPromotionSettings[$promotionID])) {
            return self::$aPromotionSettings[$promotionID];
        }

        if (!is_array(self::$aRawPromotionSettings)) {
            return false;
        }

        foreach (self::$aRawPromotionSettings as $aPromotion) {
            if (isset($aPromotion['id']) && $aPromotion['id'] == $promotionID) {
                self::$aPromotionSettings[$promotionID] = $aPromotion;

                return $aPromotion;
            } else {
                if ($aPromotion['position'] == 'listing_sidebar') {
                    self::$aPromotionSettings[$promotionID] = $aPromotion;
                }
            }
        }

        return isset(self::$aPromotionSettings[$promotionID]) ? self::$aPromotionSettings[$promotionID] : false;
    }

    public static function getPromotionKeyByPosition($position, $isMetaQuery = false)
    {
        self::getPromotionSettings();

        if (!is_array(self::$aRawPromotionSettings)) {
            return '';
        }

        $aKeys = [];
        foreach (self::$aRawPromotionSettings as $aPromotion) {
            if ($aPromotion['position'] == $position) {
                $aKeys[] = self::generateListingPromotionMetaKey($aPromotion, $isMetaQuery);
            }
        }

        return $aKeys;
    }

    public static function getSingleListingBelongsToPlanClass($postID)
    {
        $belongsToPlanID = GetSettings::getPostMeta($postID, 'belongs_to');
        if (!empty($belongsToPlanID)) {
            $planClass = get_post_field('post_name', $belongsToPlanID);

            return apply_filters('wilcity/filter/class-prefix', 'wilcity-belongs-to-plan-').$planClass;
        }

        return '';
    }

    public static function redirectToAfterLogin($isRegister = false)
    {
        $loginType = \WilokeThemeOptions::getOptionDetail('login_redirect_type');
        if ($loginType == 'self') {
            if ($isRegister) {
                return home_url('/');
            }
            global $wp;

            return home_url($wp->request);
        }

        return get_permalink(\WilokeThemeOptions::getOptionDetail('login_redirect_to'));
    }

    public static function redirectToAfterRegister()
    {
        $createdAccountID = \WilokeThemeOptions::getOptionDetail('created_account_redirect_to');
        if (empty($createdAccountID)) {
            return self::redirectToAfterLogin();
        }

        return get_permalink($createdAccountID);
    }

    public static function getShareReviewURL($postURL, $reviewID)
    {
        return $postURL.'#tab=reviews&reviewID='.$reviewID;
    }

    public static function userCanRegister()
    {
        if (self::$canRegister !== null) {
            return self::$canRegister;
        }

        self::$canRegister = get_option('users_can_register') == 1 || (is_multisite() && get_site_option('registration') == 'user');

        return self::$canRegister;
    }

    public static function isEnableCustomLoginPage()
    {
        return \WilokeThemeOptions::isEnable('toggle_custom_login_page', false);
    }

    public static function getCustomLoginPage()
    {
        if (self::$customLoginPage !== null) {
            return self::$customLoginPage;
        }

        $customLoginPageID = \WilokeThemeOptions::getOptionDetail('custom_login_page');

        self::$customLoginPage = self::isEnableCustomLoginPage() && !empty($customLoginPageID) ? get_permalink($customLoginPageID) : '';

        return self::$customLoginPage;
    }

    public static function countChildren($oTerm)
    {
        $aChildren = get_terms($oTerm->taxonomy, [
            'parent'     => $oTerm->term_id,
            'hide_empty' => false
        ]);

        return empty($aChildren) || is_wp_error($aChildren) ? 0 : count($aChildren);
    }

    public static function isTermParent($termID, $taxonomy)
    {
        global $wpdb;
        $total = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT($wpdb->term_taxonomy.term_id) FROM {$wpdb->term_taxonomy} WHERE parent=%d and taxonomy=%s",
                $termID, $taxonomy
            )
        );

        return !empty($total);
    }

    public static function isTaxonomyUsingCustomPage()
    {
        if (!is_tax()) {
            return false;
        }

        $taxType = \WilokeThemeOptions::getOptionDetail('listing_taxonomy_page_type');
        if ($taxType != 'custom') {
            return false;
        }

        $taxonomy = get_queried_object()->taxonomy;
        $pageID   = \WilokeThemeOptions::getOptionDetail($taxonomy.'_page');
        if (empty($pageID)) {
            return false;
        }

        return $pageID;
    }

    public static function getListingPhone($postID, $isCheckPlan = true)
    {
        return (!GetSettings::isPlanAvailableInListing($postID,
                'toggle_phone') && $isCheckPlan) || !SingleListing::isClaimedListing($postID) ? '' : GetSettings::getPostMeta($postID,
            'phone');
    }

    public static function getListingEmail($postID, $isCheckPlan = true)
    {
        return (!GetSettings::isPlanAvailableInListing($postID,
                'toggle_email') && $isCheckPlan) || !SingleListing::isClaimedListing($postID) ? '' : GetSettings::getPostMeta($postID,
            'email');
    }

    public static function getListingTotalViews($postID)
    {
        return abs(GetSettings::getPostMeta($postID, 'count_viewed'));
    }

    public static function getTimeFormat($postID)
    {
        $timeFormat = GetSettings::getPostMeta($postID, 'timeFormat');
        if (empty($timeFormat) || $timeFormat == 'inherit') {
            $aThemeOptions = \Wiloke::getThemeOptions(true);

            return $aThemeOptions['timeformat'];
        }

        return $timeFormat;
    }

    public static function getMyProducts($postID)
    {
        $aProducts = GetSettings::getPostMeta($postID, 'my_products');

        if (empty($aProducts)) {
            return '';
        }

        foreach ($aProducts as $order => $productID) {
            $postStatus = get_post_status($productID);

            if ($postStatus != 'publish') {
                unset($aProducts[$order]);
                continue;
            }
            $aProducts[$order] = trim(abs($productID));
        }

        if (empty($aProducts)) {
            return '';
        }

        return $aProducts;
    }

    public static function getTransient($key, $focusDeleting = false)
    {
        $val = maybe_unserialize(get_transient($key));
        if ($focusDeleting) {
            SetSettings::deleteTransient($key);
        }

        return $val;
    }

    public static function adminEmail()
    {
        $aThemeOptions = \Wiloke::getThemeOptions(true);
        if (isset($aThemeOptions['email_from']) && !empty($aThemeOptions['email_from'])) {
            return $aThemeOptions['email_from'];
        }

        return get_option('admin_email');
    }

    public static function countNumberOfChildrenReviews($parentID)
    {
        global $wpdb;
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT($wpdb->posts.ID) FROM $wpdb->posts WHERE $wpdb->posts.post_parent=%d AND $wpdb->posts.post_status='publish'",
                $parentID
            )
        );

        return abs($count);
    }

    public static function getProductThumbnail($productID, $size = 'thumbnail')
    {
        return get_the_post_thumbnail_url($productID, $size);
    }

    public static function getPostFeaturedImgsByTerm($termID, $taxonomy)
    {
        if (isset(self::$aPostFeaturedImgsByTerm[$termID])) {
            return self::$aPostFeaturedImgsByTerm[$termID];
        }

        $query = new \WP_Query(
            [
                'post_type'      => General::getPostTypeKeys(false, false),
                'post_status'    => 'publish',
                'posts_per_page' => 4,
                'tax_query'      => [
                    [
                        'taxonomy' => $taxonomy,
                        'field'    => 'term_id',
                        'terms'    => $termID
                    ]
                ]
            ]
        );

        if (!$query->have_posts()) {
            wp_reset_postdata();

            return false;
        }

        self::$aPostFeaturedImgsByTerm[$termID] = [];

        while ($query->have_posts()) {
            $query->the_post();
            $logo = GetSettings::getLogo($query->post->ID, 'wilcity_40x40');
            if (empty($logo)) {
                $logo = GetSettings::getFeaturedImg($query->post->ID, 'wilcity_40x40');
            }

            if (!empty($logo)) {
                self::$aPostFeaturedImgsByTerm[$termID][$query->post->ID] = $logo;
            } else {
                self::$aPostFeaturedImgsByTerm[$termID][$query->post->ID] = '';
            }
        }
        wp_reset_postdata();

        return self::$aPostFeaturedImgsByTerm[$termID];
    }

    public static function getAddress($postID, $isReturnURL = false)
    {
        $aAddress = Listing::getListingAddress($postID);
        if (empty($aAddress) || !isset($aAddress['address'])) {
            return '';
        }

        if (!$isReturnURL) {
            return stripslashes($aAddress['address']);
        }

        return esc_url('https://www.google.com/maps/search/'.urldecode($aAddress['address']));
    }

    public static function getLatLng($postID)
    {
        $aAddress = Listing::getListingAddress($postID);
        if (empty($aAddress) || !isset($aAddress['address'])) {
            return '';
        }
        if (!isset($aAddress['lat']) || empty($aAddress['lat']) || !isset($aAddress['lng']) || empty($aAddress['lng'])) {
            return false;
        }

        return [
            'lat' => $aAddress['lat'],
            'lng' => $aAddress['lng']
        ];
    }

    public static function getCoverImage($postID, $size = 'large')
    {
        $coverImgID = GetSettings::getPostMeta($postID, 'cover_image_id');
        $img        = '';
        if (!empty($coverImgID)) {
            $img = wp_get_attachment_image_url($coverImgID, $size);
        }

        if (!empty($img) && strpos($img, 'kingcomposer') === false) {
            return $img;
        } else {
            $img = GetSettings::getPostMeta($postID, 'cover_image');
            if (!empty($img && strpos($img, 'kingcomposer') === false)) {
                return $img;
            }
            $img = get_the_post_thumbnail_url($postID, $size);
        }

        if (empty($img) || strpos($img, 'kingcomposer') !== false) {
            if (\WilokeThemeOptions::getOptionDetail('listing_featured_image_type') == 'category') {
                $categoryID = self::getPrimaryTermIDOfPost($postID, 'listing_cat');
                if (!empty($categoryID)) {
                    $termFeaturedImg = self::getTermFeaturedImg($categoryID, $size, 'listing_cat');
                    if (!empty($termFeaturedImg)) {
                        return $termFeaturedImg;
                    }
                }
            }

            $aThemeOption = \Wiloke::getThemeOptions(true);
            if (isset($aThemeOption['listing_featured_image']) && isset($aThemeOption['listing_featured_image']['id'])) {
                $img = wp_get_attachment_image_url($aThemeOption['listing_featured_image']['id'], 'large');
            }
        }

        return $img;
    }

    public static function getBlogFeaturedImage($postID, $size = 'large')
    {
        $img = get_the_post_thumbnail_url($postID, $size);
        if (!empty($img)) {
            return $img;
        }

        if (empty($img)) {
            $aThemeOption = \Wiloke::getThemeOptions(true);
            if (isset($aThemeOption['blog_featured_image']) && isset($aThemeOption['blog_featured_image']['id'])) {
                $img = wp_get_attachment_image_url($aThemeOption['blog_featured_image']['id'], $img);
            }
        }

        return $img;
    }

    public static function getFeaturedImg($postID, $size = 'large')
    {
        $imgID = get_post_thumbnail_id($postID);

        if (!empty($imgID)) {
            $url = get_the_post_thumbnail_url($postID, $size);
            if (!empty($url) && strpos($url, 'kingcomposer') === false) {
                return $url;
            }
        }

        return self::getCoverImage($postID, $size);
    }

    public static function getCouponFeatureImg($aCoupon)
    {
        $img = '';
        if (isset($aCoupon['popup_image']) && !empty($aCoupon['popup_image'])) {
            $img = wp_get_attachment_image_url($aCoupon['popup_image'], 'large');
        }
        if (empty($img)) {
            $aCouponImg = \WilokeThemeOptions::getOptionDetail('listing_coupon_popup_img');
            if (!empty($aCouponImg)) {
                $img = isset($aCouponImg['url']) && !empty($aCouponImg['url']) ? esc_url($aCouponImg['url']) :
                    wp_get_attachment_image_url($aCouponImg['id'], 'large');
            }
        }

        return $img;
    }

    public static function getBusinessHours($postID)
    {
        return Listing::getBusinessHoursOfListing($postID);
    }

    public static function getListingMapInfo($postID)
    {
        if (isset(self::$aPostMeta['listing_map_'.$postID])) {
            return self::$aPostMeta['listing_map_'.$postID];
        } else {
            global $wpdb;
            $tbl                                     = $wpdb->prefix.AlterTableLatLng::$tblName;
            self::$aPostMeta['listing_map_'.$postID] = $wpdb->get_row(
                $wpdb->prepare(
                    'SELECT * FROM '.$tbl.' WHERE objectID=%d',
                    $postID
                ),
                ARRAY_A
            );

            return self::$aPostMeta['listing_map_'.$postID];
        }
    }

    public static function getCartUrl($planID)
    {
        global $woocommerce;
        $productID = GetSettings::getPostMeta($planID, 'woocommerce_association');

        $cartUrl = function_exists('wc_get_cart_url') ? wc_get_cart_url() : $woocommerce->cart->get_cart_url();

        return add_query_arg(
            [
                'add-to-cart' => $productID,
                'quantity'    => 1
            ],
            $cartUrl
        );
    }

    public static function getTimeZoneByGeocode($latLng)
    {
        global $wiloke;
        if (empty($latLng) || $latLng == ',') {
            return '';
        }

        $url       = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$latLng.'&timestamp='.time().'&key='.$wiloke->aThemeOptions['general_google_api'];
        $aTimeZone = wp_remote_get(esc_url_raw($url));

        if (empty($aTimeZone) || is_wp_error($aTimeZone)) {
            return '';
        } else {
            $oTimeZone = json_decode($aTimeZone['body']);

            return $oTimeZone->timeZoneId;
        }
    }

    /*
     * @param int $postID
     * @param int $authorID
     */
    public static function getCommentIDByAuthorID($userID, $postID)
    {
        global $wpdb;
        $commentTbl = $wpdb->comments;

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT comment_ID FROM $commentTbl WHERE user_id=%d AND comment_post_ID=%d ORDER BY comment_ID DESC",
                $userID, $postID
            )
        );
    }

    public static function setPrefix($metaKey, $prefix)
    {
        $prefix = empty($prefix) ? wilokeListingToolsRepository()->get('general:metaboxPrefix') : $prefix;

        return $prefix.$metaKey;
    }

    public static function getPostTerms($postID, $taxonomy)
    {
        if (isset(self::$aPostTerms[$taxonomy.$postID])) {
            return self::$aPostTerms[$taxonomy.$postID];
        }

        $oPostTerms = wp_get_post_terms($postID, $taxonomy);

        if (empty($oPostTerms) || is_wp_error($oPostTerms)) {
            self::$aPostTerms[$taxonomy.$postID] = false;

            return false;
        }

        self::$aPostTerms[$taxonomy.$postID] = $oPostTerms;

        return self::$aPostTerms[$taxonomy.$postID];
    }

    public static function getLastPostTerm($postID, $taxonomy)
    {
        if (is_tax('listing_cat') && get_query_var('taxonomy') == $taxonomy) {
            return get_term_by('slug', get_query_var('term'), 'listing_cat');
        } else {
            $aTerms = self::getPostTerms($postID, $taxonomy);
            if (!empty($aTerms) && !is_wp_error($aTerms)) {
                foreach ($aTerms as $oTerm) {
                    if (!empty($oTerm->parent)) {
                        return $oTerm;
                    }
                }

                return end($aTerms);
            }

            return false;
        }
    }

    public static function getPlanSettings($planID)
    {
        $postType = get_post_type($planID);
        $metaKey  = 'add_'.$postType;

        return self::getPostMeta($planID, $metaKey);
    }

    public static function convertToRightKey($postID, $metaKey)
    {
        if (get_post_type($postID) == 'pricing') {
            $metaKey = 'pricing_settings';
        } else if (get_post_type($postID) == 'event-pricing') {
            $metaKey = 'event_pricing_settings';
        } else if (get_post_type($postID) == 'promotional-pricing') {
            $metaKey = 'wiloke_submission_promotion';
        }

        return $metaKey;
    }

    /**
     * We will use cache in the feature
     *
     * @param number $postID
     * @param bool   $isParseValue
     *
     * @return mixed
     */
    public static function getTerms($aOptions, $isParseValue = true)
    {
        if (!isset($aOptions['hide_empty'])) {
            $aOptions['hide_empty'] = false;
        }

        $aRawTerms = get_terms($aOptions);
        if (empty($aRawTerms) || is_wp_error($aRawTerms)) {
            return false;
        }

        return $aRawTerms;
    }

    public static function getTermBy($field, $term, $taxonomy)
    {
        $aRawTerms = get_term_by($field, $term, $taxonomy);
        if (empty($aRawTerms) || is_wp_error($aRawTerms)) {
            return false;
        }

        return $aRawTerms;
    }

    /**
     * We will use cache in the feature
     *
     * @param number $postID
     * @param string $metaKey
     *
     * @return mixed
     */
    public static function getPostMeta($postID, $metaKey = null, $prefix = null)
    {
        $prefix  = empty($prefix) ? wilokeListingToolsRepository()->get('general:metaboxPrefix') : $prefix;
        $metaKey = strpos($metaKey, $prefix) === false ? $prefix.$metaKey : $metaKey;

        if ($metaKey == 'hourMode' || $metaKey == 'wilcity_hourMode') {
            return BusinessHourMeta::get($postID, $metaKey);
        } else {
            return get_post_meta($postID, $metaKey, true);
        }
    }

    /**
     * We will use cache in the feature
     *
     * @param number $postID
     * @param string $metaKey
     *
     * @return mixed
     */
    public static function getCommentMeta($commentID, $metaKey = null, $prefix = null)
    {
        $prefix  = empty($prefix) ? wilokeListingToolsRepository()->get('general:metaboxPrefix') : $prefix;
        $metaKey = $prefix.$metaKey;

        return get_post_meta($commentID, $metaKey, true);
    }

    /**
     * We will use cache in the feature
     *
     * @param number $postID
     * @param string $metaKey
     *
     * @return mixed
     */
    public static function getTermMeta($termID, $metaKey = null, $prefix = null)
    {
        $prefix  = empty($prefix) ? wilokeListingToolsRepository()->get('general:metaboxPrefix') : $prefix;
        $metaKey = $prefix.$metaKey;

        return get_term_meta($termID, $metaKey, true);
    }

    public static function getCmb2Taxonomy($termID, $taxonomy)
    {
        return get_transient('_transient_cmb-cache-'.$taxonomy.'-'.$termID);
    }

    /**
     * We will use cache in the feature
     *
     * @param number $postID
     * @param string $metaKey
     *
     * @return mixed
     */
    public static function getAddListingSettings($postID)
    {
        return get_post_meta($postID, 'pricing_settings', true);
    }

    /**
     * Get User Meta
     *
     * @param number $userID
     * @param string $metaKey
     *
     * @return mixed
     */
    public static function getUserMeta($userID, $metaKey, $prefix = '')
    {
        $metaKey = self::setPrefix($metaKey, $prefix);

        return get_user_meta($userID, $metaKey, true);
    }

    public static function getUserPlans($userID, $isFocus = false)
    {
        if ($isFocus) {
            $aUserPlans                = self::getUserMeta($userID,
                wilokeListingToolsRepository()->get('user:userPlans'));
            self::$aUserPlans[$userID] = $aUserPlans;

            return $aUserPlans;
        }

        if (isset(self::$aUserPlans[$userID])) {
            return self::$aUserPlans[$userID];
        }

        self::$aUserPlans[$userID] = self::getUserMeta($userID, wilokeListingToolsRepository()->get('user:userPlans'));

        return self::$aUserPlans[$userID];
    }

    protected static function unSlashDeep($aVal)
    {
        if (!is_array($aVal)) {
            return stripslashes($aVal);
        }

        return array_map([__CLASS__, 'unSlashDeep'], $aVal);
    }

    /**
     * Get Options
     *
     * @param string $optionsKey
     *
     * @return mixed
     */
    public static function getOptions($optionsKey, $isFocus = false, $unSlashed = false, $checkWMPL = false)
    {
        if (isset(self::$aOptions[$optionsKey]) && !$isFocus) {
            return self::$aOptions[$optionsKey];
        }

        $val = get_option($optionsKey);

        if ($checkWMPL && defined('ICL_LANGUAGE_CODE') && empty($val)) {
            if (empty($val)) {
                $realOptionsKey = str_replace('_'.ICL_LANGUAGE_CODE, '', $optionsKey);
                $val            = get_option($realOptionsKey);
            }
        }
        self::$aOptions[$optionsKey] = maybe_unserialize($val);

        if (self::$aOptions[$optionsKey]) {
            self::$aOptions[$optionsKey] = self::unSlashDeep(self::$aOptions[$optionsKey]);
        }

        return self::$aOptions[$optionsKey];
    }

    public static function getOptionsWithWPML()
    {

    }

    public static function getNumberOfTermChildren($oTerm)
    {
        global $wpdb;

        $total = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT($wpdb->term_taxonomy.term_id) FROM $wpdb->term_taxonomy WHERE taxonomy=%s AND parent=%d",
                $oTerm->taxonomy, $oTerm->term_id
            )
        );

        return empty($total) ? 0 : abs($total);
    }

    /**
     * Get User Data
     *
     * @param int $userID
     *
     * @return object $oUseData
     */
    public static function getUserData($userID)
    {
        global $wiloke;
        if (isset(self::$aUsersData[$userID])) {
            $aUserData = self::$aUsersData[$userID];
        } else {
            $aUserData['avatar']   = self::getUserMeta($userID, 'avatar');
            $aUserData['position'] = self::getUserMeta($userID, 'position');
            if (empty($aUserData['avatar'])) {
                $aUserData['avatar'] = $wiloke->aThemeOptions['user_avatar']['url'];
            }

            $oUserInfo = get_userdata($userID);

            $aUserData['display_name'] = $oUserInfo->display_name;
            $aUserData['user_url']     = $oUserInfo->user_url;
            self::$aUsersData[$userID] = $aUserData;
        }

        return $aUserData;
    }

    public static function getTermGradients($oTerm, $toRgba = true, $taxonomy = '')
    {
        $aSettings = [];
        if (is_numeric($oTerm)) {
            $oTerm = GetSettings::getTermBy('term_id', $oTerm, $taxonomy);
        }
        $aSettings['left']  = GetSettings::getTermMeta($oTerm->term_id, 'left_gradient_bg');
        $aSettings['right'] = GetSettings::getTermMeta($oTerm->term_id, 'right_gradient_bg');
        if (empty($aSettings['left']) || empty($aSettings['right'])) {
            return false;
        }

        if ($toRgba) {
            $aSettings['left']  = \Wiloke::hexToRgba($aSettings['left'], 0.7);
            $aSettings['right'] = \Wiloke::hexToRgba($aSettings['right'], .08);
        }

        $aSettings['tiltedDegrees'] = GetSettings::getTermMeta($oTerm->term_id, 'gradient_tilted_degrees');

        return $aSettings;
    }

    public static function getTaxonomyHierarchy($aArgs, $postType = '', $isParentOnly = false, $isFocus = false)
    {
        // only 1 taxonomy
		$hideEmpty = 'yes';
		if ( !isset($aArgs['hide_empty']) || !$aArgs['hide_empty'] ){
			$aArgs['hide_empty'] = false;
			$hideEmpty = 'no';
		}
		if ( isset($aArgs['orderby']) && $aArgs['orderby'] == 'meta_value_num' ){
			$aArgs['meta_key'] = 'tax_position';
		}
		if ( !empty($postType) && !isset($aArgs['meta_query']) ){
			if ( !isset($aArgs['parent']) || $aArgs['parent'] == 0 ){
				
				$aArgs['meta_query'][] = array(
					'key'       => 'wilcity_belongs_to',
					'compare'   => 'LIKE',
					'value'     => $postType . '";'
				);
				$aTerms = get_terms($aArgs);
				if ( empty($aTerms) || is_wp_error($aTerms) ){
					unset($aTerms);
					unset($aArgs['meta_query']);
				}
			}
		}
		$aArgs = apply_filters('wilcity/wiloke-listing-tools/filter/term-args', $aArgs, $postType, $isParentOnly);
		// get all direct decendants of the $parent
		if ( !$isFocus ){
			if ( !isset(self::$aAllParentTerms[$aArgs['taxonomy'] . $hideEmpty]) || empty(self::$aAllParentTerms[$aArgs['taxonomy'].$hideEmpty]) ){
				if( isset($aTerms) ) {
					self::$aAllParentTerms[$aArgs['taxonomy'].$hideEmpty] = $aTerms;
				} else {
					self::$aAllParentTerms[$aArgs['taxonomy'].$hideEmpty] = get_terms($aArgs);
				}
				$aTerms = self::$aAllParentTerms[$aArgs['taxonomy'].$hideEmpty];
			}else{
				$aTerms = self::$aAllParentTerms[$aArgs['taxonomy'].$hideEmpty];
			}
		}elseif( !isset( $aTerms ) ){
			$aTerms = get_terms($aArgs);
		}
		$aResult = array();
		if ( empty($aTerms) || is_wp_error($aTerms) ){
			return $aResult;
		}

        foreach ($aTerms as $oTerm) {
            // recurse to get the direct decendants of "this" term
            $oTerm->value = $oTerm->name;
            $aResult[]    = $oTerm;
            if (!$isParentOnly) {
                $aArgs['parent'] = $oTerm->term_id;

                $aChildren = self::getTaxonomyHierarchy($aArgs, $postType, false, true);

                foreach ($aChildren as $oChild) {
                    $oChild->value = $oChild->name;
                    $aResult[]     = $oChild;
                    if (!empty($oChild->children)) {
                        foreach ($oChild->children as $oGrandChild) {
                            $oGrandChild->value = $oGrandChild->name;
                            $aResult[]          = $oGrandChild;
                        }
                    }
                }
            }
        }

        // send the results back to the caller
        return $aResult;
    }

    public static function getEventSettings($eventID)
    {
        if (isset(self::$aPostMeta[$eventID])) {
            return self::$aPostMeta[$eventID];
        } else {
            $aEventSettings            = EventModel::getEventData($eventID);
            self::$aPostMeta[$eventID] = $aEventSettings;
        }

        return $aEventSettings;
    }

    public static function isPlanAvailableInListing($postID, $key)
    {
        if (strpos($key, 'toggle_') !== 0) {
            $key = 'toggle_'.$key;
        }

        $planID = GetSettings::getPostMeta($postID, 'belongs_to');

        if (empty($planID)) {
            return true;
        }

        if (!isset(self::$aCacheListingPlans[$planID])) {
            self::$aCacheListingPlans[$planID] = GetSettings::getPlanSettings($planID);
        }

        return !isset(self::$aCacheListingPlans[$planID][$key]) || empty(self::$aCacheListingPlans[$planID][$key]) || self::$aCacheListingPlans[$planID][$key] == 'enable';
    }

    public static function getLogo($postID, $size = 'wilcity_40x40', $isFocusThemeOptions = false)
    {
        $logoID = self::getPostMeta($postID, 'logo_id');
        $logo   = '';
        if (!empty($logoID)) {
            $logo = wp_get_attachment_image_url($logoID, $size);
        }

        if (!empty($logo)) {
            return $logo;
        } else {
            $logo = self::getPostMeta($postID, 'logo');
        }
        $logo = apply_filters('wilcity/wiloke-listing-tools/getLogo', $logo, $postID);

        if (empty($logo)) {
            $aThemeOptions = \Wiloke::getThemeOptions(true);

            if (isset($aThemeOptions['general_listing_logo']) && isset($aThemeOptions['general_listing_logo']['id'])) {
                $logo = wp_get_attachment_image_url($aThemeOptions['general_listing_logo']['id'], $size);
            } else {
                $logo = get_the_post_thumbnail_url($postID, $size);
                if (empty($logo)) {
                    $aThemeOptions = \Wiloke::getThemeOptions($isFocusThemeOptions);
                    if (isset($aThemeOptions['listing_featured_image']) && isset($aThemeOptions['listing_featured_image']['id'])) {
                        $logo = wp_get_attachment_image_url($aThemeOptions['listing_featured_image']['id'], $size);
                    }
                }
            }
        }

        return $logo;
    }

    public static function getTagLine($post, $ifEmptyGetExcerpt = true, $isFocusThemeOptions = false)
    {
        if (is_numeric($post)) {
            $postID      = $post;
            $postExcerpt = '';
        } else {
            $postID      = $post->ID;
            $postExcerpt = $post->post_excerpt;
        }

        $aThemeOptions = \Wiloke::getThemeOptions($isFocusThemeOptions);

        $tagLine = self::getPostMeta($postID, 'tagline');
        if (empty($tagLine) && $ifEmptyGetExcerpt) {
            if (empty($postExcerpt)) {
                $postExcerpt = get_post_field('post_excerpt', $postID);
            }

            if (!empty($postExcerpt)) {
                return $postExcerpt;
            }

            if (is_numeric($post)) {
                $post = get_post($postID);
            }

            $tagLine = \Wiloke::contentLimit($aThemeOptions['listing_excerpt_length'], $post, true, $post->post_content,
                true);
        }

        return strip_shortcodes($tagLine);
    }

    public static function getAttachmentURL($attachmentID, $size = 'thumbnail', $ignoreKC = false)
    {
        $iconURL = wp_get_attachment_image_url($attachmentID, $size);
        if (!empty($iconURL) && strpos($iconURL, 'plugins/kingcomposer') === false) {
            return $iconURL;
        }

        if (!$ignoreKC) {
            return $iconURL;
        }

        return '';
    }

    public static function getPriceRange($postID, $isBuild = false)
    {
        $mode = GetSettings::getPostMeta($postID, 'price_range');
        if (empty($mode)) {
            return false;
        }

        $currencyCode   = GetWilokeSubmission::getField('currency_code');
        $currencySymbol = GetWilokeSubmission::getSymbol($currencyCode);
        $currency       = apply_filters('wilcity-shortcodes/currency-symbol', $currencySymbol);

        $aPriceRange = [
            'mode'         => $mode,
            'description'  => GetSettings::getPostMeta($postID, 'price_range_desc'),
            'minimumPrice' => GetSettings::getPostMeta($postID, 'minimum_price'),
            'maximumPrice' => GetSettings::getPostMeta($postID, 'maximum_price'),
            'currency'     => $currency
        ];;

        if (!$isBuild) {
            return $aPriceRange;
        } else {
            if (empty($aPriceRange['minimumPrice']) || empty($aPriceRange['maximumPrice']) || $aPriceRange['minimumPrice'] == $aPriceRange['maximumPrice']) {
                return false;
            } else {
                $aPriceRange['minimumPrice'] = GetWilokeSubmission::renderPrice($aPriceRange['minimumPrice'],
                    $aPriceRange['currency']);
                $aPriceRange['maximumPrice'] = GetWilokeSubmission::renderPrice($aPriceRange['maximumPrice'],
                    $aPriceRange['currency']);

                return $aPriceRange;
            }
        }

    }

    public static function getSocialNetworks($postID)
    {
        $aSocialNetworks = GetSettings::getPostMeta($postID, 'social_networks');
        if (empty($aSocialNetworks)) {
            return false;
        }

        $aResponse = [];
        foreach ($aSocialNetworks as $key => $val) {
            if (!empty($val)) {
                $aResponse[$key] = $val;
            }
        }

        if (empty($aResponse)) {
            return false;
        }

        return $aResponse;
    }

    public static function getAverageRating(
        $postID,
        $isReturnEventZero = false,
        $isGreatFormat = false,
        $convertToFiveScaleFormat = false
    ) {
        $averageRating = abs(GetSettings::getPostMeta($postID, 'average_reviews'));
        if (empty($averageRating)) {
            if ($isReturnEventZero) {
                return apply_filters('wilcity/wiloke-listing-tools/average-rating-zero-symbol', '-');
            }

            return '';
        }

        if ($convertToFiveScaleFormat) {
            $mode = GetSettings::getOptions(General::getReviewKey('mode', get_post_type($postID)));
            if ($mode == 10) {
                $averageRating = $averageRating / 2;
            }
        }

        return $isGreatFormat ? number_format($averageRating, 1) : $averageRating;
    }

    public static function getTimezone($postID)
    {
        return self::getPostMeta($postID, 'timezone');
    }

    public static function getListingBelongsToPlan($listingID)
    {
        return GetSettings::getPostMeta($listingID, 'belongs_to');
    }

    public static function getBestRating($postType)
    {
        $mode = GetSettings::getOptions(General::getReviewKey('mode', $postType));

        return abs($mode);
    }

    public static function getPostTypeField($field, $postType)
    {
        if (!isset(self::$aPostTypesSettings[$postType])) {
            $aPostTypes = self::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));
            foreach ($aPostTypes as $aData) {
                if ($aData['key'] == $postType) {
                    self::$aPostTypesSettings[$postType] = $aData;
                    break;
                }
            }
        }

        return isset(self::$aPostTypesSettings[$postType][$field]) ? self::$aPostTypesSettings[$postType][$field] : '';
    }

    public static function getTermCountInPostType($postTypes, $termID)
    {
        if (is_array($postTypes)) {
            $postTypes = implode("','", $postTypes);
        }

        global $wpdb;
        $termRelationshipTbl = $wpdb->term_relationships;
        $postsTbl            = $wpdb->posts;

        $total = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT($termRelationshipTbl.object_id) FROM $termRelationshipTbl LEFT JOIN $postsTbl ON ($postsTbl.ID = $termRelationshipTbl.object_id) WHERE $termRelationshipTbl.term_taxonomy_id=%d AND $postsTbl.post_type IN ('".esc_sql($postTypes)."')",
                $termID
            )
        );

        return empty($total) ? 0 : abs($total);
    }

    public static function getTermsBelongsToPostType(
        $postType,
        $taxonomy,
        $isFocus = false,
        $hideEmpty = false,
        $autoUpdateIfEmpty = true
    ) {
        $key = $taxonomy.'_belongs_to_'.$postType;
        if (!$isFocus) {
            if (isset(self::$aCacheTermsBelongsToPostType[$key])) {
                return self::$aCacheTermsBelongsToPostType[$key];
            }
        }

        $aCache = self::getOptions($key);
        if ($aCache) {
            self::$aCacheTermsBelongsToPostType[$key] = $aCache;

            return $aCache;
        }

        if ($autoUpdateIfEmpty) {
            $aTerms = self::getTerms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => $hideEmpty
            ]);

            if (empty($aTerms) || is_wp_error($aTerms)) {
                self::$aCacheTermsBelongsToPostType[$key] = false;
            }
            $aTermsChildren = [];
            foreach ($aTerms as $oTerm) {
                $aBelongsTo = self::getTermMeta($oTerm->term_id, 'belongs_to');
                if (empty($aBelongsTo)) {
                    $aTermsChildren[] = $oTerm->term_id;
                } else {
                    if (in_array($postType, $aBelongsTo)) {
                        $aTermsChildren[] = $oTerm->term_id;
                    }
                }
            }
            SetSettings::setTermsBelongsToPostType($postType, $taxonomy, $aTermsChildren);
            self::$aCacheTermsBelongsToPostType[$key] = $aTermsChildren;

            return $aTermsChildren;
        }

        return false;
    }

    public static function getSearchFormField($postType, $fieldKey)
    {
        $aSearchForm = GetSettings::getOptions(General::getSearchFieldsKey($postType));

        if (empty($aSearchForm)) {
            return '';
        }

        foreach ($aSearchForm as $aField) {
            if (isset($aField[$fieldKey])) {
                return $aField[$fieldKey];
            }
        }

        return '';
    }

    public static function getGroupSetting($groupKey, $postType)
    {
        $aSettings = self::getOptions(General::getUsedSectionKey($postType, true));

        if (empty($aSettings)) {
            return [];
        }

        foreach ($aSettings as $aField) {
            if ($aField['key'] == $groupKey) {
                return $aField;
            }
        }
    }

    public static function isUsingFirebase()
    {
        return \WilokeThemeOptions::isEnable('mobile_is_using_firebase_message');
    }

    public static function getSearchPage()
    {
        $pageID = \WilokeThemeOptions::getOptionDetail('search_page');

        return !empty($pageID) ? get_permalink($pageID) : home_url('/');
    }

    public static function getTermLink($oTerm, $withListingBelongTo = true)
    {
        $link = get_term_link($oTerm);
        if ($withListingBelongTo) {
            $aBelongsTo = GetSettings::getTermMeta($oTerm->term_id, 'belongs_to');
            $aArgs      = [];
            if (!empty($aBelongsTo)) {
                $aArgs['type'] = $aBelongsTo[0];
            }

            if (is_tax() && $oTerm->taxonomy != get_query_var('taxonomy')) {
                $aArgs[get_query_var('taxonomy')] = get_queried_object()->slug;
                $aArgs[$oTerm->taxonomy]          = $oTerm->slug;
                $link                             = add_query_arg(
                    $aArgs,
                    GetSettings::getSearchPage()
                );
            } else {
                $link = add_query_arg(
                    $aArgs,
                    $link
                );
            }
        }

        return $link;
    }

    public static function countTermDependsOnCurrentTerm($termID, $taxonomy = '')
    {
        global $wpdb;

        $total = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DISTINCT $wpdb->term_relationships.object_id FROM $wpdb->term_relationships WHERE $wpdb->term_relationships.object_id IN ( SELECT DISTINCT $wpdb->term_relationships.object_id FROM $wpdb->term_relationships LEFT JOIN $wpdb->posts ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) WHERE $wpdb->posts.post_status='publish' AND $wpdb->term_relationships.term_taxonomy_id = %d ) AND $wpdb->term_relationships.term_taxonomy_id=%d",
                get_queried_object_id(), $termID
            )
        );

        return !empty($total) ? count($total) : 0;
    }

    public static function getTermFeaturedImg($oTerm, $size = 'large', $taxonomy = '')
    {
        if (is_object($oTerm)) {
            $termID   = $oTerm->term_id;
            $taxonomy = $oTerm->taxonomy;
        } else {
            $termID = $oTerm;
        }

        $featuredImgID  = GetSettings::getTermMeta($termID, 'featured_image_id');
        $featuredImgUrl = '';
        if (!empty($featuredImgID)) {
            $featuredImgUrl = wp_get_attachment_image_url($featuredImgID, $size);
        }

        if (empty($featuredImgUrl)) {
            $featuredImgUrl = GetSettings::getTermMeta($termID, 'featured_image');
        } else {
            return $featuredImgUrl;
        }

        if (empty($featuredImgUrl)) {
            $termFeatureImgKey = $taxonomy.'_featured_image';
            $aThemeOption      = \Wiloke::getThemeOptions(true);
            if (isset($aThemeOption[$termFeatureImgKey]) && isset($aThemeOption[$termFeatureImgKey]['id'])) {
                $featuredImgUrl = wp_get_attachment_image_url($aThemeOption[$termFeatureImgKey]['id'], $size);
            }
        } else {
            return $featuredImgUrl;
        }

        return $featuredImgUrl;
    }

    public static function getEventHostedByUrl($post)
    {
        $hostedByUrl = GetSettings::getPostMeta($post->ID, 'hosted_by_profile_url');
        if (empty($hostedByUrl)) {
            $hostedByUrl = get_author_posts_url($post->post_authorID,
                get_the_author_meta('user_nicename', $post->post_author));
        }

        return $hostedByUrl;
    }

    public static function getEventHostedByName($post)
    {
        $hostedBy = GetSettings::getPostMeta($post->ID, 'hosted_by');
        if (empty($hostedBy)) {
            $hostedBy = get_the_author_meta('display_name', $post->post_author);
        }

        return $hostedBy;
    }

    public static function getEventHostedByTarget($hostedByURL)
    {
        $homeURL = get_option('home_url');

        return !empty($hostedByURL) && strpos($hostedByURL, $homeURL) === false ? '_target' : '_self';
    }

    public static function getAllDirectoryTypes($isGetKey = false)
    {
        $aCustomPostTypes = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));
        if (!$isGetKey) {
            return $aCustomPostTypes;
        }

        $aPostTypes = [];
        foreach ($aCustomPostTypes as $aPostType) {
            $aPostTypes[] = $aPostType['key'];
        }

        return $aPostTypes;
    }

    public static function getOrderDate($orderID)
    {
        $order = wc_get_order($orderID);

        return $order->get_date_completed();
    }

    public static function getDokanProductIDsByOrderID($orderID)
    {
        $order       = wc_get_order($orderID);
        $aItems      = $order->get_items();
        $aProductIDs = [];
        foreach ($aItems as $oItem) {
            $productID = $oItem->get_product_id();
            if (DokanController::isDokanProduct($productID)) {
                $aProductIDs[] = $productID;
            }
        }

        return $aProductIDs;
    }

    public static function getPrimaryTermIDOfPost($postID, $taxonomy)
    {
        $primaryCatID = get_post_meta($postID, '_yoast_wpseo_primary_'.$taxonomy, true);
        if (!empty($primaryCatID)) {
            return $primaryCatID;
        }

        $oLastTerm = self::getLastPostTerm($postID, $taxonomy);

        return empty($oLastTerm) ? false : $oLastTerm->term_id;
    }

    public static function getFirstDokanProductByOrder($orderID)
    {
        $order  = wc_get_order($orderID);
        $aItems = $order->get_items();

        foreach ($aItems as $oItem) {
            $productID = $oItem->get_product_id();
            if (DokanController::isDokanProduct($productID)) {
                return $productID;
            }
        }
    }

    public static function getDokanWithDrawField($fieldName, $withDrawID)
    {
        if (!class_exists('WeDevs_Dokan')) {
            return false;
        }

        global $wpdb;
        $dbName    = $wpdb->prefix.'dokan_withdraw';
        $fieldName = sanitize_text_field($fieldName);

        return $wpdb->get_var($wpdb->prepare(
            "SELECT {$fieldName} FROM {$dbName} WHERE id=%d",
            $withDrawID
        ));
    }

    public static function getDokanField($fieldName, $isReturnURL = false)
    {
        if (!class_exists('WeDevs_Dokan')) {
            return false;
        }

        $aPageSettings = get_option('dokan_pages');

        return !$isReturnURL ? $aPageSettings[$fieldName] : get_permalink($aPageSettings[$fieldName]);
    }

    public static function getListingIDByDokanProductID($productID)
    {
        if (GetSettings::getPostMeta($productID, 'is_dokan') != 'yes') {
            return false;
        }
        global $wpdb;

        $escapeProductID = '%'.abs($productID).'%';
        $aListingIDs     = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT $wpdb->postmeta.post_id From $wpdb->postmeta WHERE meta_key='wilcity_my_products' AND meta_value LIKE %s",
                $escapeProductID
            )
        );
        if (empty($aListingIDs)) {
            return false;
        }

        $aListListings = [];
        foreach ($aListingIDs as $oInfo) {
            $aMyProducts = GetSettings::getPostMeta($oInfo->post_id, 'my_products');

            if (!empty($aMyProducts) && in_array($productID, $aMyProducts)) {
                $aListListings[] = $oInfo->post_id;
            }
        }

        return empty($aListListings) ? false : $aListListings;
    }

    public static function getDokanPages($isCheckUserPermission = false)
    {
        if (!class_exists('WeDevs_Dokan')) {
            return false;
        }

        $aPageSettings = get_option('dokan_pages');
        if (!isset($aPageSettings['dashboard']) || empty($aPageSettings['dashboard'])) {
            return false;
        }

        if ($isCheckUserPermission) {
            if (!User::canAddProduct()) {
                return false;
            }
        }

        return [
            'id'        => $aPageSettings['dashboard'],
            'title'     => get_the_title($aPageSettings['dashboard']),
            'permalink' => get_permalink($aPageSettings['dashboard'])
        ];
    }

    public static function getDefaultPostType($isGetKey = false, $exceptEvent = false)
    {
        $aTypes = self::getFrontendPostTypes($exceptEvent);
        if ($isGetKey) {
            return $aTypes[0]['key'];
        }

        return $aTypes[0];
    }

    public static function getFrontendPostTypes($isGetKey = false, $exceptEvent = false)
    {
        $aCustomPostTypes = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));

        if (!is_array($aCustomPostTypes)) {
            $aCustomPostTypes = wilokeListingToolsRepository()->get('posttypes:post_types');
        }

        foreach ($aCustomPostTypes as $key => $aPostType) {
            if ((isset($aPostType['isDisableOnFrontend']) && $aPostType['isDisableOnFrontend'] == 'yes') || ($exceptEvent && $aPostType['key'] == 'event')) {
                unset($aCustomPostTypes[$key]);
            }
        }
        $aCustomPostTypes = apply_filters('wilcity/filter/frontend-directory-types', $aCustomPostTypes);
        if (!$isGetKey) {
            return $aCustomPostTypes;
        }

        $aPostTypes = [];
        foreach ($aCustomPostTypes as $aPostType) {
            $aPostTypes[] = $aPostType['key'];
        }

        return $aPostTypes;
    }

    public static function getTranslation()
    {
        $child = get_stylesheet_directory().'/configs/config.translation.php';
        if (is_file(get_stylesheet_directory().'/configs/config.translation.php')) {
            $aChildConfig = include $child;
        }

        $aParentConfig = include get_template_directory().'/configs/config.translation.php';
        if (isset($aChildConfig) && is_array($aChildConfig)) {
            return $aChildConfig + $aParentConfig;
        }

        return $aParentConfig;
    }
}
