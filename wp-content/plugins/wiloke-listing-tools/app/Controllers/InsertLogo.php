<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Upload\Upload;

trait InsertLogo {
	protected function insertLogo(){
		if ( empty($this->aRawLogo) || (isset($this->aRawLogo[0]['imgID']) && !empty($this->aRawLogo[0]['imgID'])) ){
			return false;
		}

		if ( isset($this->aRawLogo[0]['id']) && !empty($this->aRawLogo[0]['id']) ){
			$this->aGeneralData['logo']  = array(
				'src' => wp_get_attachment_image_url($this->aRawLogo[0]['id'], 'large'),
				'id'  => $this->aRawLogo[0]['id']
			);
			return true;
		}

		$instUploadImg = new Upload();

		$instUploadImg->userID = get_current_user_id();
		$instUploadImg->aData['imageData']  = $this->aRawLogo[0]['src'];
		$instUploadImg->aData['fileName']   = $this->aRawLogo[0]['fileName'];
		$instUploadImg->aData['fileType']   = $this->aRawLogo[0]['fileType'];
		$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder();

		$oldImgID = GetSettings::getPostMeta($this->listingID, 'logo_id');
		if ( !empty($oldImgID) ){
			Upload::deleteImg($oldImgID);
		}

		$id = $instUploadImg->image();
		$this->aGeneralData['logo']  = array(
			'src' => wp_get_attachment_image_url($id, 'large'),
			'id'  => $id
		);
	}
}