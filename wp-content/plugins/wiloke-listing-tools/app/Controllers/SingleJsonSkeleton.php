<?php

namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Models\FavoriteStatistic;
use WilokeListingTools\Models\ListingModel;
use WilokeListingTools\Models\SharesStatistic;
use WilokeListingTools\Models\ViewStatistic;

trait SingleJsonSkeleton
{
    public function getPromotions()
    {
        if (empty($aRawResults)) {
            return false;
        }

        $aPromotions = array();
        foreach ($aRawResults as $aData) {
            if ( !empty($aData['meta_value'])) {
                $aPromotions[] = $aData['meta_value'];
            }
        }
        if (empty($aPromotions)) {
            return false;
        }

        return $aPromotions;
    }

    public function getGeneralFeaturedImg($imgSize = 'thumbnail')
    {
        $aThemeOptions = \Wiloke::getThemeOptions();
        if (isset($aThemeOptions['listing_featured_image']) && isset($aThemeOptions['listing_featured_image']['id'])) {
            $featuredImg = wp_get_attachment_image_url($aThemeOptions['listing_featured_image']['id'], $imgSize);

            return $featuredImg;
        }

        return '';
    }

    public function json($post)
    {
        $aListing['postID']        = $post->ID;
        $aListing['title']         = get_the_title($post->ID);
        $aListing['postStatus']    = $post->post_status;
        $aListing['link']          = get_permalink($post->ID);
        $aListing['featuredImage'] = get_the_post_thumbnail_url($post->ID, 'thumbnail');
        if (empty($aListing['featuredImage'])) {
            $aListing['featuredImage'] = $this->getGeneralFeaturedImg();
        }

        $aListing['tagLine']               = GetSettings::getPostMeta($post->ID, 'tagLine');
        $togglePromotion                   = GetSettings::getOptions('toggle_promotion');
        $aPromotion                        = $this->getPromotions();
        $aListing['views']                 = ViewStatistic::countViews($post->ID);
        $aListing['shares']                = SharesStatistic::countShared($post->ID);
        $aListing['favorites']             = FavoriteStatistic::countFavorites($post->ID);
        $aListing['isNonRecurringPayment'] = GetWilokeSubmission::isNonRecurringPayment() ? 'yes' : 'no';
        $aListing['isEnabledPromotion']    = $togglePromotion == 'enable' ? 'yes' : 'no';
        $aListing['hasPromotion']          = empty($aPromotion) ? 'no' : 'yes';
        $aListing['aPromotions']           = ListingModel::listingBelongsToPromotion($post->ID);
        $expiryDate = GetSettings::getPostMeta($post->ID, 'post_expiry');
        $aListing['listingExpiry']         = empty($expiryDate) ? $expiryDate : date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $expiryDate);
        $blongsToPlanID                    = GetSettings::getPostMeta($post->ID, 'belongs_to');
        $aListing['belongsToPlan']         = empty($blongsToPlanID) ? '' : get_the_title($blongsToPlanID);

        return $aListing;
    }
}
