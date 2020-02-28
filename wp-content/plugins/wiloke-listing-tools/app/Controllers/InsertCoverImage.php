<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Upload\Upload;

trait InsertCoverImage {
	protected function insertCoverImg(){
		if ( empty($this->aRawCoverImg) || (isset($this->aRawCoverImg[0]['imgID']) && !empty($this->aRawCoverImg[0]['imgID'])) ){
			return false;
		}

		if ( isset($this->aRawCoverImg[0]['id']) && !empty($this->aRawCoverImg[0]['id']) ){
			$this->aGeneralData['cover_image']  = array(
				'src' => wp_get_attachment_image_url($this->aRawCoverImg[0]['id'], 'large'),
				'id'  => $this->aRawCoverImg[0]['id']
			);

			return true;
		}

		$instUploadImg = new Upload();
		$instUploadImg->userID = get_current_user_id();
		$instUploadImg->aData['imageData']  = $this->aRawCoverImg[0]['src'];
		$instUploadImg->aData['fileName']   = $this->aRawCoverImg[0]['fileName'];
		$instUploadImg->aData['fileType']   = $this->aRawCoverImg[0]['fileType'];
		$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder();

		$oldImgID = GetSettings::getPostMeta($this->listingID, 'cover_image_id');
		if ( !empty($oldImgID) ){
			Upload::deleteImg($oldImgID);
		}

		$id = $instUploadImg->image();
		$this->aGeneralData['cover_image']  = array(
			'src' => wp_get_attachment_image_url($id, 'large'),
			'id'  => $id
		);
	}
}