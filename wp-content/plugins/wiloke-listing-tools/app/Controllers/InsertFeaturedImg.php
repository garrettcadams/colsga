<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Upload\Upload;

trait InsertFeaturedImg {
	protected function insertFeaturedImg(){
		if ( empty($this->aRawFeaturedImage) ){
			return false;
		}

		if ( (isset($this->aRawFeaturedImage[0]['imgID']) && !empty($this->aRawFeaturedImage[0]['imgID'])) ){
			set_post_thumbnail($this->listingID, $this->aRawFeaturedImage[0]['imgID']);
			return true;
		}

		if ( isset($this->aRawFeaturedImage[0]['id']) && !empty($this->aRawFeaturedImage[0]['id']) ){
			set_post_thumbnail($this->listingID, $this->aRawFeaturedImage[0]['id']);
			return true;
		}

		$instUploadImg = new Upload();
		$instUploadImg->userID = get_current_user_id();
		$instUploadImg->aData['imageData']  = $this->aRawFeaturedImage[0]['src'];
		$instUploadImg->aData['fileName']   = $this->aRawFeaturedImage[0]['fileName'];
		$instUploadImg->aData['fileType']   = $this->aRawFeaturedImage[0]['fileType'];
		$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder();

		if ( !empty($this->listingID) && !current_user_can('edit_posts') ){
			$featuredID = get_post_thumbnail_id( $this->listingID );
			Upload::deleteImg($featuredID);
		}

		$id = $instUploadImg->image();
		set_post_thumbnail($this->listingID, $id);
	}
}