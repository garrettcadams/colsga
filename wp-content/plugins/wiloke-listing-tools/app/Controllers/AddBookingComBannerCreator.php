<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\MetaBoxes\BookingComBannerCreator;
use WilokeListingTools\Models\BookingCom;

trait AddBookingComBannerCreator {
	protected function addBookingComBannerCreator(){
		$bookingID = BookingCom::getCreatorIDByParentID($this->listingID);
		if ( !empty($this->aBookingComBannerCreator) ){
			if ( !isset($this->aBookingComBannerCreator['bannerLink']) || empty($this->aBookingComBannerCreator['bannerLink']) ){
				if (!empty($bookingID)){
					wp_delete_post($bookingID, true);
				}
				return false;
			}
			if ( isset($this->aBookingComBannerCreator['bannerImg'][0]) && isset($this->aBookingComBannerCreator['bannerImg'][0]['src']) ){

				if ( !filter_var($this->aBookingComBannerCreator['bannerImg'][0]['src'], FILTER_VALIDATE_URL) ){
					$instUploadImg = new Upload();

					$instUploadImg->userID = get_current_user_id();
					$instUploadImg->aData['imageData']  = $this->aBookingComBannerCreator['bannerImg'][0]['src'];
					$instUploadImg->aData['fileName']   = $this->aBookingComBannerCreator['bannerImg'][0]['fileName'];
					$instUploadImg->aData['fileType']   = $this->aBookingComBannerCreator['bannerImg'][0]['fileType'];
					$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder();
					$id = $instUploadImg->image();
					$url = wp_get_attachment_image_url($id, 'full');
					$this->aBookingComBannerCreator['bannerImg'] = $url;
				}else{
					$this->aBookingComBannerCreator['bannerImg'] = sanitize_text_field($this->aBookingComBannerCreator['bannerImg'][0]['src']);
				}
			}else{
				$this->aBookingComBannerCreator['bannerImg'] = '';
			}

			if ( !empty($bookingID) ){
				BookingCom::updateBannerCreator($this->listingID, $bookingID, $this->aBookingComBannerCreator);
			}else{
				BookingCom::insertBannerCreator($this->listingID, $this->aBookingComBannerCreator);
			}
		}else{
			if (!empty($bookingID)){
				wp_delete_post($bookingID, true);
			}
		}
	}
}