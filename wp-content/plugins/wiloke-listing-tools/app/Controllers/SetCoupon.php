<?php

namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Upload\Upload;

trait SetCoupon
{
    private function setCoupon()
    {
        if (empty($this->aCoupon)) {
            SetSettings::deletePostMeta($this->listingID, 'coupon');
            SetSettings::deletePostMeta($this->listingID, 'coupon_expiry');
        } else {
            if (!empty($this->aCoupon['popup_image'])) {
                if (isset($this->aCoupon['popup_image'][0]['imgID']) && !empty($this->aCoupon['popup_image'][0]['imgID'])) {
                    $imgID = $this->aCoupon['popup_image'][0]['imgID'];
                    unset($this->aCoupon['popup_image']);
                    $this->aCoupon['popup_image'] = $imgID;
                } else if (isset($this->aCoupon['popup_image'][0]['id']) && !empty($this->aCoupon['popup_image'][0]['id'])) {
                    $imgID                        = $this->aCoupon['popup_image'][0]['id'];
                    $this->aCoupon['popup_image'] = $imgID;
                } else {
                    $instUploadImg                     = new Upload();
                    $instUploadImg->userID             = get_current_user_id();
                    $instUploadImg->aData['imageData'] = $this->aCoupon['popup_image'][0]['src'];
                    $instUploadImg->aData['fileName']  = $this->aCoupon['popup_image'][0]['fileName'];
                    $instUploadImg->aData['fileType']  = $this->aCoupon['popup_image'][0]['fileType'];
                    $instUploadImg->aData['uploadTo']  = $instUploadImg::getUserUploadFolder();
                    $id                                = $instUploadImg->image();
                    unset($this->aCoupon['popup_image']);
                    $this->aCoupon['popup_image'] = $id;
                }
            }

            if (isset($this->aCoupon['expiry_date']) && !empty($this->aCoupon['expiry_date'])) {
                $currentTimezone = date_default_timezone_get();
                date_default_timezone_set('UTC');
                $this->aCoupon['expiry_date'] =  strtotime($this->aCoupon['expiry_date']);
                date_default_timezone_set($currentTimezone);
            } else {
                SetSettings::deletePostMeta($this->listingID, 'coupon_expiry_date');
            }

            SetSettings::setPostMeta($this->listingID, 'coupon', $this->aCoupon);
        }
    }
}
